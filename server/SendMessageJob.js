const { validationResult } = require("express-validator");
const { startCon } = require("./WaConnection");
const fs = require("fs");
const { formatReceipt } = require("./Helper");
const { processing } = require("./Processing");
const { delay, isJidGroup } = require("@adiwajshing/baileys");
const { setStatus } = require("./Querydb");

const validationSend = async (_req) => {
  const _validationResult = validationResult(_req);
  if (!_validationResult.isEmpty()) {
    return {
      status: ![],
      msg: _validationResult.array()[0]["msg"],
    };
  }
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
  return { status: !![], data: conn };
};

const sendMessageJob = async (_req, _res) => {
  const _result = await validationSend(_req);
  const _id = isJidGroup(_req.body.receiver)
    ? _req.body.receiver
    : formatReceipt(_req.body.receiver);
  if (_result.status === ![]) {
    return _res.status(410).json({
      status: ![],
      msg: _result["msg"],
    });
  }

  const _sock = _result.data;

  _sock.ev.on("connection.update", async (_update) => {
    if (_update.connection === "open") {
      const _waId = isJidGroup(_id) ? [_id] : await _sock.onWhatsApp(_id);
      if (_waId.length === 0) {
        return _res.status(410).json({
          status: ![],
          msg: "The Destination Number Is Not Registered On Whatsapp",
        });
      }

      await processing(_sock, _id, _req.body)
        .then(() => {
          return _res.status(200).json({
            status: !![],
            msg: "Message Sent!",
          });
        })
        .catch((err) => {
          return _res.status(410).json({
            status: ![],
            msg: err["message"],
          });
        });
    }
  });
};

module.exports = {
  sendMessageJob: sendMessageJob,
};
