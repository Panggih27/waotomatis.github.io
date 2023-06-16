const { v4 } = require("uuid");
const { db } = require("./Database");
const { toSqlDateTime, timeNow } = require("./Helper");
const { dbQuery } = require("./Querydb");
const { isJidUser } = require("@adiwajshing/baileys");

const inboxes = async (_messages, _sock) => {
  db.beginTransaction();
  try {
    if (!_messages.messages) return;
    _messages = _messages.messages[0];
    if (_messages.key.fromMe === !![]) return;
    if (_messages.message == null || _messages.message == undefined) return;
    const _keyMessage = Object.keys(_messages.message)[0];
    const _theMessage =
      _keyMessage === "conversation" && _messages["message"]["conversation"]
        ? _messages["message"]["conversation"]
        : _keyMessage == "imageMessage" &&
          _messages["message"]["imageMessage"]["caption"]
        ? _messages["message"]["imageMessage"]["caption"]
        : _keyMessage == "videoMessage" &&
          _messages["message"]["videoMessage"]["caption"]
        ? _messages["message"]["videoMessage"]["caption"]
        : _keyMessage == "extendedTextMessage" &&
          _messages["message"]["extendedTextMessage"]["text"]
        ? _messages["message"]["extendedTextMessage"]["text"]
        : _keyMessage == "listResponseMessage" &&
          _messages["message"]["listResponseMessage"]["selectedDisplayText"]
        ? _messages["message"]["listResponseMessage"]["selectedDisplayText"]
        : _keyMessage == "messageContextInfo"
        ? _messages["message"]["buttonsResponseMessage"]["selectedDisplayText"]
        : null;
    if (!_theMessage) return;
    let uuid = v4();

    if (isJidUser(_messages.key.remoteJid)) {
      await dbQuery(
        `INSERT INTO inboxes (id,number,message_id,sender,body,created_at,updated_at)
         VALUES(?, ?, ?, ?, ?, ?, ?)`,
        [
          uuid,
          _sock.user.id.split(':')[0],
          _messages.key.id,
          _messages.key.remoteJid.split('@')[0],
          _theMessage,
          toSqlDateTime(new Date()),
          toSqlDateTime(new Date()),
        ]
      ).catch((err) => {
        console.log(err);
      });
      db.commit();
    }
  } catch (err) {
    db.rollback();
    console.log(err);
  }
};

module.exports = {inboxes: inboxes}