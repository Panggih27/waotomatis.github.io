const { startCon } = require("./WaConnection");
const fs = require("fs");
const { formatReceipt, toSqlDateTime } = require("./Helper");
const { v4 } = require("uuid");
const { dbQuery, setStatus } = require("./Querydb");
const { validationResult } = require("express-validator");
const { delay } = require("@adiwajshing/baileys");

const checkConnection = async (_req) => {
  const syncedFile = fs.existsSync("./sessions/"  + _req.body.sender + "/creds.json");
  if (!syncedFile) {
    await setStatus(_req.body.sender, "Disconnect");
    return {
      status: ![],
      msg: "The sender is not registered or not scan yet!",
    };
  }
  const conn = await startCon(_req.body.sender).catch((err) => {
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

async function broadcastMessageFormat(_request) {
  let _res = [];

  switch (_request.type) {
    case "text":
      _request.receivers.forEach(({ number: _receiver, message: _msg }) => {
        _res.push({
          number: _receiver,
          data: {
            text: _msg,
          },
        });
      });
      break;
    case "button":
      var _buttonData = [
        {
          buttonId: "id1",
          buttonText: {
            displayText: _request.body.data[0].buttonText.displayText,
          },
          type: 1,
        },
      ];

      if (_request.body.data.length > 1) {
        _buttonData.push({
          buttonId: "id2",
          buttonText: {
            displayText: _request.body.data[1].buttonText.displayText,
          },
          type: 1,
        });

        if (_request.body.data.length > 2) {
          _buttonData.push({
            buttonId: "id3",
            buttonText: {
              displayText: _request.body.data[2].buttonText.displayText,
            },
            type: 1,
          });
        }
      }
      _request.receivers.forEach(({ number: _receiver, message: _msg }) => {
        _res.push({
          number: _receiver,
          data: {
            text: _msg,
            footer: _request.body.footer,
            buttons: _buttonData,
            headerType: 1,
          },
        });
      });
      break;
    case "image":
      _request.receivers.forEach(({ number: _receiver, message: _msg }) => {
        _res.push({
          number: _receiver,
          data: {
            image: _request.body.data.image,
            caption: _msg,
          },
        });
      });
      break;
    case "template":
      _request.receivers.forEach(({ number: _receiver, message: _msg }) => {
        _res.push({
          number: _receiver,
          data: {
            text: _msg,
            footer: _request.body.footer,
            templateButtons: _request.body.data,
          },
        });
      });
      break;
    default:
      break;
  }

  return _res;
}

const send = async (_data, _sock) => {
  const _waId = formatReceipt(_data.receiver);
  const _group = await isJidUser(_waId);
  console.log(_waId);
  let _status = "failed",
    _message = "";
  const _result = _sock.onWhatsApp(_waId);
  _result.length === 0
    ? (_status = "failed")
    : await _sock
        .sendMessage(_waId, _data.message)
        .then(() => {
          _status = "success";
        })
        .catch((err) => {
          (_status = "failed"), (_message = err.message);
        });

  let uuid = v4();
  await dbQuery(
    `INSERT INTO messages (id,user_id,messageable_type,messageable_id,sender,receiver,body,type,point,status,status_description,executed_at,created_at,updated_at)  
    VALUES(?, ?, 'App\\\\Models\\\\Campaign', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
    [
      uuid,
      _data.user,
      _data.campaign,
      _data.sender,
      _data.receiver,
      JSON.stringify(_data.message),
      _data.type,
      _status == "success" ? _data.point : 0,
      _status,
      JSON.stringify(_message).replace(/["'/\\$]/g, ""),
      toSqlDateTime(new Date()),
      toSqlDateTime(new Date()),
      toSqlDateTime(new Date()),
    ]
  );
};

const sendMessageBC = async (_req, _res) => {
  const _reqValidation = await reqValidation(_req);
  const _checkCon = await checkConnection(_req);

  if (!_checkCon.status || !_reqValidation.status) {
    return _res.status(410).json({
      status: _checkCon.status,
      msg: _checkCon["msg"],
      data: null,
    });
  }

  const _sock = _checkCon.data;
  // if (state.creds.me === undefined) {
  //   return _res.status(410).json({
  //     status: ![],
  //     msg: "Please Connect your whatsapp before use the Api!",
  //   });
  // }

  const _formatMsg = await broadcastMessageFormat(_req.body);
  const _getUserId = await dbQuery(
    "SELECT user_id FROM numbers WHERE body = ?",
    [_req.body.sender]
  );
  const _getPointCost = await dbQuery(
    "SELECT point FROM campaigns WHERE id = ?",
    [_req.body.campaign]
  );

  _sock.ev.on("connection.update", async (_update) => {
    if (_update.connection == "open") {
      console.log("STart");

      for (let _i = 0; _i < _formatMsg.length; _i++) {
        if (
          Number.isInteger((_i + 1) / process.env.MESSAGE_MAX_QUEUE) &&
          _i > 0
        ) {
          await delay(process.env.MESSAGE_QUEUE_TIME * 1000);
          console.log(process.env.MESSAGE_QUEUE_TIME * 1000);
        }

        let _param = {
          receiver: _formatMsg[_i].number,
          sender: _req.body.sender,
          type: _req.body.type,
          message: _formatMsg[_i].data,
          point: _getPointCost[0x0]["point"],
          campaign: _req.body.campaign,
          user: _getUserId[0x0]["user_id"],
        };

        await send(_param, _sock);
      }

      let uuidH = v4();

      const _getCostPoint = await dbQuery(
        "SELECT SUM (point) as TOTAL FROM messages WHERE user_id = ? AND messageable_id = ? AND messageable_type = 'App\\\\Models\\\\Campaign' AND status = ?",
        [_getUserId[0x0]["user_id"], _campaign.data.id, "success"]
      );

      if (_getCostPoint[0x0]["TOTAL"] != null) {
        await dbQuery(
          "UPDATE points SET point = point - ? WHERE user_id = ?",
          [_getCostPoint[0]["TOTAL"], _getUserId[0x0]["user_id"]]
        );
      }

      await dbQuery(
        "UPDATE campaigns SET description = ?, is_processing = ? WHERE id = ?",
        ["successful", 0, _req.body.campaign]
      );

      await dbQuery(
        "INSERT INTO histories (id,user_id,historyable_type,historyable_id,point,type,created_at,updated_at)  VALUES(?, ?, 'App\\\\Models\\\\Campaign', ?, ?, ?, ?, ?)",
        [
          uuidH,
          _getUserId[0x0]["user_id"],
          _req.body.campaign,
          _getCostPoint[0]["TOTAL"] ?? 0,
          "-",
          toSqlDateTime(new Date()),
          toSqlDateTime(new Date()),
        ]
      );

      console.log("Done");
    } else {
      const _checkProcess = await dbQuery(
        "SELECT point FROM campaigns WHERE id = ? AND description = ?",
        [_req.body.campaign, "successful"]
      );

      if (_checkProcess[0] == null) {
        await dbQuery(
          "UPDATE campaigns SET description = ?, is_processing = ? WHERE id = ?",
          ["something went wrong", 0, _req.body.campaign]
        );
      }
    }
  });

  return _res.status(200).json({
    status: !![],
    msg: "broadcast message is on progress!, you can see the results in detail.",
  });
};

module.exports = {
  sendMessageBC: sendMessageBC,
};
