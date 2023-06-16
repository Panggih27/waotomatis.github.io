const { validationResult } = require("express-validator");
const { startCon } = require("./WaConnection");
const fs = require("fs");
const { formatReceipt } = require("./Helper");
const { processing } = require("./Processing");
const { setStatus } = require("./Querydb");

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

const TestMessage = async (_req, _res) => {
    
    try {
      const _reqValidation = await reqValidation(_req);
      if (!_reqValidation.status) {
        return _res.status(500).json({
          status: _reqValidation.status,
          msg: _reqValidation["msg"],
          data: null,
        });
      }

      const _checkCon = await checkConnection(_req.body.sender);
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
      //     data: null,
      //   });
      // }

      _sock.ev.on("connection.update", async (_update) => {
        const {
          connection: connection,
          lastDisconnect: lastDisconnect,
        } = _update;
        if (connection == "open") {
          const _waId = formatReceipt(_req.body.receiver);
          const _result = await _sock.onWhatsApp(_waId).catch((err) => {
            console.log(err);
            return _res.status(500).json({
              status: ![],
              msg: err.message,
              data: null,
            });
          });
          if (_result.length < 1) {
            return _res.status(500).json({
              status: ![],
              msg: "The Destination Number Is Not Registered On Whatsapp!",
              data: null,
            });
          }

          let _param = {
            type: _req.body.type,
            body: _req.body.body,
          };

          await processing(_sock, _waId, _param).then(() => {
            console.log('success');
              return _res.status(200).json({
                status: !![],
                msg: "success",
              });
          }).catch((err) => {
            console.log(err);
            // throw new Error(err);
          });
        }
      });
    } catch (error) {
      console.log(error);
      return _res.status(500).json({
        status: !![],
        msg: error.message,
      });
    }
};

module.exports = {
  TestMessage: TestMessage,
};
