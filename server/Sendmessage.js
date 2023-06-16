const _0x2dd658 = _0x1059;
(function (_0x316c6e, _0x52bf45) {
  const _0xee1c70 = _0x1059,
    _0x133765 = _0x316c6e();
  while (!![]) {
    try {
      const _0x1c884f =
        (-parseInt(_0xee1c70(0x1d9)) / 0x1) *
          (-parseInt(_0xee1c70(0x1c7)) / 0x2) +
        -parseInt(_0xee1c70(0x1ee)) / 0x3 +
        (-parseInt(_0xee1c70(0x1f1)) / 0x4) *
          (parseInt(_0xee1c70(0x1d6)) / 0x5) +
        -parseInt(_0xee1c70(0x1e7)) / 0x6 +
        parseInt(_0xee1c70(0x1db)) / 0x7 +
        parseInt(_0xee1c70(0x1dc)) / 0x8 +
        (-parseInt(_0xee1c70(0x1e5)) / 0x9) *
          (-parseInt(_0xee1c70(0x1cb)) / 0xa);
      if (_0x1c884f === _0x52bf45) break;
      else _0x133765["push"](_0x133765["shift"]());
    } catch (_0x47938c) {
      _0x133765["push"](_0x133765["shift"]());
    }
  }
})(_0x4621, 0xc6d84);

function _0x4621() {
  const _0x48f6d4 = [
    "split",
    "SELECT\x20user_id\x20FROM\x20numbers\x20WHERE\x20body\x20=\x20\x27",
    "caption",
    "button",
    "message",
    "status",
    "call",
    "Button",
    "type",
    "open",
    "512170qkjLTO",
    "button1",
    "onWhatsApp",
    "31XMRBoF",
    "The\x20sender\x20is\x20not\x20registered\x20or\x20not\x20scan\x20yet!",
    "3636444wDTMzU",
    "11007176PkpKNv",
    "express-validator",
    "footer",
    "Blasts\x20message\x20on\x20progress!,you\x20can\x20see\x20the\x20results\x20in\x20history\x20Blast\x20page.",
    "failed",
    "Please\x20Connect\x20your\x20whatsapp\x20before\x20use\x20the\x20Api!",
    "array",
    "connection",
    "text",
    "36HUFZuj",
    "button2",
    "1411122JXWLSB",
    "url",
    "phoneNumber",
    "The\x20Destination\x20Number\x20Is\x20Not\x20Registered\x20On\x20Whatsapp",
    "end",
    "./sessions/session-",
    "Id2",
    "3023697izSzCF",
    "socket.io",
    "data",
    "4Helfpz",
    "length",
    "template",
    "json",
    "readFileSync",
    "image",
    "connection.update",
    "user_id",
    "sendMessage",
    "success",
    "then",
    "Id1",
    "./Helper",
    "exports",
    "sender",
    "creds",
    "INSERT\x20INTO\x20blasts\x20(user_id,receiver,message,type,status)\x20\x20VALUES\x20(\x22",
    "/creds.json",
    "log",
    "catch",
    "body",
    "\x22,\x22",
    "application/",
    "./Querydb",
    "10866WPLIZp",
    "template1",
    "./WaConnection",
    "msg",
    "240440kmuOBR",
  ];
  _0x4621 = function () {
    return _0x48f6d4;
  };
  return _0x4621();
}
function _0x1059(_0xecdef3, _0x1853a1) {
  const _0x462160 = _0x4621();
  return (
    (_0x1059 = function (_0x105921, _0x4038fc) {
      _0x105921 = _0x105921 - 0x1c6;
      let _0x104907 = _0x462160[_0x105921];
      return _0x104907;
    }),
    _0x1059(_0xecdef3, _0x1853a1)
  );
}

const { validationResult } = require("express-validator");
const { v4 } = require("uuid");
const { startCon } = require("./WaConnection");
const fs = require("fs");
const {
  formatSendBlast,
  getFileTypeFromUrl,
  formatReceipt,
  toSqlDateTime,
} = require("./Helper");
const { dbQuery, setStatus } = require("./Querydb");

const validationSend = async (_req) => {
  const _validationResult = validationResult(_req);
  if (!_validationResult.isEmpty())
    return {
      status: ![],
      msg: _validationResult.array()[0]["msg"],
    };
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

const sendMessage = async (_req, _res) => {
  const _result = await validationSend(_req),
    _id = formatReceipt(_req["body"]["number"]);
  if (_result.status === ![])
    return _res.status(410).json({
      status: ![],
      msg: _result["msg"],
    });
  const { conn: _sock, state: _state } = _result["data"],
    _type = _req["body"]["type"],
    _messages = await getMessage(_type, _req["body"]);
  console.log(_messages),
    _sock.ev.on("connection.update", async (_update) => {
      if (_update.connection === "open") {
        const _waId = await _sock.onWhatsApp(_id);
        if (_waId.length === 0)
          return _res.status(410).json({
            status: ![],
            msg: "The Destination Number Is Not Registered On Whatsapp",
          });
        await _sock
          .sendMessage(_id, _messages)
          .then(() => {
            _res.status(200).json({
              status: !![],
              msg: "Message\x20Sent!",
            });
          })
          .catch((_err) => {
            _res.status(410).json({
              status: ![],
              msg: _err["message"],
            });
          }),
          _sock.end(),
          setTimeout(async () => {
            await startCon(_req["body"]["sender"]);
            return;
          }, 5000);
      }
    });
};

async function asyncForEach(_formats, blastAsync) {
  for (let _i = 0; _i < _formats.length; _i++) {
    await blastAsync(_formats[_i], _i, _formats);
  }
}

const blastMessage = async (_req, _res) => {
  const _reqValidated = await validationSend(_req),
    _getUserId = await dbQuery("SELECT user_id FROM numbers WHERE body = ?", [
      _req["body"]["sender"],
    ]),
    _userId = _getUserId[0x0]["user_id"],
    _formatBlast = await formatSendBlast(
      _req["body"]["type"],
      _req["body"]["data"]
    );

  if (_reqValidated.status === ![])
    return _res.status(410).json({
      status: ![],
      msg: _reqValidated["msg"],
    });

  const _sock = _reqValidated["data"];
  // if (state.creds.me === undefined)
  //   return _res.status(410).json({
  //     status: ![],
  //     msg: "Please Connect your whatsapp before use the Api!",
  //   });
  return (
    _sock.ev.on("connection.update", async (_update) => {
      _update.connection === "open" &&
        (await asyncForEach(
          _formatBlast,
          async ({ number: _receiver, msg: _theMessage }) => {
            let _status = "failed";
            const _waId = formatReceipt(_receiver),
              _result = await _sock.onWhatsApp(_waId);
            _result.length === 0
              ? (_status = "failed")
              : (console.log(_theMessage),
                await _sock
                  .sendMessage(_waId, _theMessage)
                  .then(() => {
                    _status = "success";
                  })
                  .catch((_err) => {
                    console.log(_err), (_status = "failed");
                  }));
            let uuid = v4();
            const _message =
              _req["body"]["type"] === "image"
                ? _theMessage["caption"]
                : _theMessage["text"];
            await dbQuery(
              "INSERT INTO blasts (id,user_id,receiver,message,type,status,created_at,updated_at)  VALUES(?, ?, ?, ?, ?, ?, ?)",
              [
                uuid,
                _userId,
                _receiver,
                _message,
                _req["body"]["type"],
                _status,
                toSqlDateTime(new Date()),
                toSqlDateTime(new Date()),
              ]
            );
          }
        ));
    }),
    _res.status(200).json({
      status: !![],
      msg: "Blasts message on progress!,you can see the results in history Blast page.",
    })
  );
};

async function getMessage(_req, _res) {
  try {
    let _result;
    switch (_req) {
      case "text":
        _result = { text: _res["message"] };
        break;
      case "image":
        _result = {
          image: { url: _res["url"] },
          caption: _res["message"],
        };
        break;
      case "document":
        const { explode: _fileName, fileType: _fileType } = getFileTypeFromUrl(
          _res["url"]
        );
        _result = {
          document: { url: _res["url"] },
          fileName: _fileName,
          mimetype: "application/" + _fileType,
        };
        break;
      case "button":
        const _buttons = [
          {
            buttonId: "Id1",
            buttonText: { displayText: _res["button1"] },
            type: 0x1,
          },
          {
            buttonId: "Id1",
            buttonText: { displayText: _res["button2"] },
            type: 0x1,
          },
        ];
        _result = {
          text: _res["message"],
          footer: _res["footer"],
          buttons: _buttons,
          headerType: 0x1,
        };
        break;
      case "template":
        const _template1 = _res["template1"]["split"]("|"),
          _template2 = _res["template2"]["split"]("|"),
          _type1 = _template1[0x0] === "call" ? "phoneNumber" : "url",
          _type2 = _template2[0x0] === "call" ? "phoneNumber" : "url",
          _button1 = _template1[0x0] + "Button",
          _button2 = _template2[0x0] + "Button",
          _templateButtons = [
            {
              index: 0x1,
              [_button1]: {
                [_type1]: _template1[0x2],
                displayText: _template1[0x1],
              },
            },
            {
              index: 0x2,
              [_button2]: {
                [_type2]: _template2[0x2],
                displayText: _template2[0x1],
              },
            },
          ],
          _templates = {
            text: _res["message"],
            footer: _res["footer"],
            templateButtons: _templateButtons,
          };
        _result = _templates;
        break;
      default:
        break;
    }
    return _result;
  } catch (_err) {
    console.log(_err);
  }
}

module.exports = {
  sendMessage: sendMessage,
  blastMessage: blastMessage,
};
