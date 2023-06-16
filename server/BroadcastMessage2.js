const { startCon } = require("./WaConnection");
const { processing } = require("./Processing");
const fs = require("fs");
const {
  formatReceipt,
  toSqlDateTime,
  randomNumber,
} = require("./Helper");
const { v4 } = require("uuid");
const { dbQuery, setStatus } = require("./Querydb");
const { validationResult } = require("express-validator");
const { delay, isJidGroup } = require("@adiwajshing/baileys");
const { db } = require("./Database");

const getCampaign = async (_campaignId) => {
  const _campaign = await dbQuery(
    "SELECT a.id, a.user_id user, b.body sender, a.point, b.delay, b.start_time start, b.end_time end, b.is_active active, a.is_manual, a.schedule FROM campaigns a, numbers b WHERE a.id = ? AND a.number_id = b.id",
    [_campaignId]
  );

  return _campaign.length > 0
    ? {
        status: !![],
        data: _campaign[0],
        msg: "success",
      }
    : {
        status: ![],
        msg: "Campaign not found!",
        data: null,
      };
};

const checkConnection = async (_sender) => {
  const syncedFile = fs.existsSync("./sessions/"  + _sender + "/creds.json");
  if (!syncedFile) {
    await setStatus(_sender, "Disconnect");
    return {
      status: ![],
      msg: "The sender is not registered or not scan yet!",
    };
  }
  const conn = await startCon(_sender).catch((err) => {
    console.log(err);
    return {
      status: ![],
      msg: "The sender is not registered or not scan yet!",
    };
  });
  return { status: !![], data: conn, msg: "" };
};

const reqValidation = async (_req) => {
  const _validatedReq = validationResult(_req);

  return !_validatedReq.isEmpty()
    ? { status: ![], data: null, msg: _validatedReq.array()[0]["msg"] }
    : { status: !![], data: null, msg: _req };
};

const send = async (_data, _sock) => {
  const _waId = isJidGroup(_data.receiver)
    ? _data.receiver
    : formatReceipt(_data.receiver);
  let _status = "failed",
    _message = "success",
    _point = 0;
  const _result = isJidGroup(_data.receiver)
    ? [_data.receiver]
    : await _sock.onWhatsApp(_waId).catch((err) => {
        _status = "failed";
        _message = err.message;
        console.log(err);
      });
  _result.length === 0
    ? (_status = "failed", _message = "The Destination Number Is Not Registered On Whatsapp")
    : await processing(_sock, _waId, _data)
        .then(() => {
          _status = "success";
        })
      .catch((err) => {
          console.log("error sending");
          (_status = "failed"), (_message = err.message);
          console.log(err);
        });

  if (_status == "success") {
    console.log("type : " + _data.type);
    const _getPoint = await dbQuery(
      "SELECT point FROM costs WHERE slug = ?",
      [_data.type]
    );

    _point = parseInt(_getPoint.length > 0 ? _getPoint[0].point : process.env.DEFAULT_POINT) + _data.broadcast;
    console.log("point message : " + _point);
  }

  await dbQuery(
    "UPDATE messages SET status = ?, point = ?, status_description = ?, executed_at = ? WHERE id = ?",
    [
      _status,
      _point,
      JSON.stringify(_message).replace(/["'/\\$]/g, ""),
      toSqlDateTime(new Date()),
      _data.id,
    ]
  );
};

const sendMessageBC2 = async (_req, _res) => {
  db.beginTransaction();
  try {
    const _campaign = await getCampaign(_req.body.campaign);
    if (!_campaign.status) {
      return _res.status(500).json({
        status: _campaign.status,
        msg: _campaign["msg"],
        data: null,
      });
    }

    const _reqValidation = await reqValidation(_req);
    if (!_reqValidation.status) {
      return _res.status(500).json({
        status: _reqValidation.status,
        msg: _reqValidation["msg"],
        data: null,
      });
    }

    const _checkCon = await checkConnection(_campaign.data.sender);
    if (!_checkCon.status) {
      return _res.status(500).json({
        status: _checkCon.status,
        msg: _checkCon["msg"],
        data: null,
      });
    }

    const _sock = _checkCon.data;
    // if (state.creds.me === undefined) {
    //   return _res.status(500).json({
    //     status: ![],
    //     msg: "Please Connect your whatsapp before use the Api!",
    //   });
    // }

    const _getAllMessages = await dbQuery(
      "SELECT id, receiver, body, type FROM messages WHERE messageable_id = ? AND messageable_type = 'App\\\\Models\\\\Campaign' AND (status = ? OR status = ?) AND user_id = ? ORDER BY FIELD(type, 'template', 'image', 'button', 'audio', 'document', 'video', 'location', 'contact', 'text')",
      [_campaign.data.id, "pending", "failed", _campaign.data.user]
    );

    if (_getAllMessages.length < 1) {
      return _res.status(500).json({
        status: ![],
        msg: "something went wrong!",
        data: null,
      });
    }

    let _broadcast = _campaign.data.is_manual
      ? "manual"
      : _campaign.data.schedule != null
      ? "schedule"
      : "now";

    const _getBroadPoint = await dbQuery(
      "SELECT point FROM costs WHERE slug = ?",
      [_broadcast]
    );

    let _broadcast_point = parseInt(
      _getBroadPoint.length > 0
        ? _getBroadPoint[0x0]["point"]
        : process.env.DEFAULT_POINT
    );

    _sock.ev.on("connection.update", async (_update) => {
      if (_update.connection == "open") {

        for (let _i = 0; _i < _getAllMessages.length; _i++) {
          if (_campaign.data.delay.includes("-")) {
            let _range = _campaign.data.delay.trim().split("-");
            let _delay = randomNumber(parseInt(_range[0]), parseInt(_range[1]));

            console.log("delay: " + _delay * 1000);
            await delay(_delay * 1000);
          } else {
            console.log(_campaign.data.delay * 1000);
            await delay(_campaign.data.delay * 1000);
          }

          let _param = {
            receiver: _getAllMessages[_i].receiver,
            id: _getAllMessages[_i].id,
            body: _getAllMessages[_i].body,
            point: _campaign.data.point,
            type: _getAllMessages[_i].type,
            broadcast: _broadcast_point,
          };

          await send(_param, _sock);
        }

        const _getCostPoint = await dbQuery(
          "SELECT SUM(point) as TOTAL, COUNT(*) AS BROADCAST FROM messages WHERE user_id = ? AND messageable_id = ? AND messageable_type = 'App\\\\Models\\\\Campaign' AND status = ?",
          [_campaign.data.user, _campaign.data.id, "success"]
        );
        
        _broadcast_point = parseInt(
          _getCostPoint.length > 0
            ? (_getCostPoint[0x0]["BROADCAST"] * _broadcast_point)
            : _broadcast_point
        );

        let _total =
          parseInt(
            _getCostPoint.length > 0
              ? _getCostPoint[0x0]["TOTAL"]
              : process.env.DEFAULT_POINT
          ) + _broadcast_point;
        
        _total = Number.isInteger(_total) ? _total : process.env.DEFAULT_POINT;

        console.log("broadcast point: " + _broadcast_point);
        console.log("total: " + _total);

        await dbQuery("UPDATE points SET point = point - ? WHERE user_id = ?", [
          _total,
          _campaign.data.user,
        ]);

        await dbQuery(
          "UPDATE campaigns SET description = ?, point = ?, is_processing = ?, broadcast_point = ? WHERE id = ?",
          ["successful", _total, 0, _broadcast_point, _campaign.data.id]
        );

        let uuidH = v4();
        await dbQuery(
          "INSERT INTO histories (id,user_id,historyable_type,historyable_id,point,type,created_at,updated_at)  VALUES(?, ?, 'App\\\\Models\\\\Campaign', ?, ?, ?, ?, ?)",
          [
            uuidH,
            _campaign.data.user,
            _campaign.data.id,
            _total,
            "-",
            toSqlDateTime(new Date()),
            toSqlDateTime(new Date()),
          ]
        );

        await delay(5000);
        console.log("Done");

        db.commit();
      } else {
        db.rollback();
        db.beginTransaction();
        try {
          const _checkProcess = await dbQuery(
            "SELECT point FROM campaigns WHERE id = ? AND description = ?",
            [_campaign.data.id, "successful"]
          );

          if (_checkProcess[0] == null) {
            await dbQuery(
              "UPDATE campaigns SET description = ?, is_processing = ? WHERE id = ?",
              ["something went wrong", 0, _campaign.data.id]
            );
          }

          db.commit();
        } catch (error) {
          console.log(error);
          db.rollback();
        }
      }
    });

    return _res.status(200).json({
      status: !![],
      msg: "broadcast message is on progress!, you can see the results in detail.",
    });
  } catch (error) {
    console.log(error);
    db.rollback();
  }
};

module.exports = {
  sendMessageBC2: sendMessageBC2,
};
