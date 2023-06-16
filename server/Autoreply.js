const { v4 } = require("uuid");
const { toSqlDateTime, timeNow } = require("./Helper");
const { dbQuery } = require("./Querydb");
const axios = require("axios");
const fs = require("fs");
const { db } = require("./Database");
const { processing } = require("./Processing");

async function removeForbiddenCharacters(_string) {
  let _theNode = ["/", "?", "&", "=", ".", "\x22"];
  for (let _clear of _theNode) {
    _string = _string.split(_clear).join("");
  }
  return _string;
}

const autoReply = async (_messages, _sock) => {
  db.beginTransaction();
  try {
    if (!_messages.messages) return;
    _messages = _messages.messages[0];
    if (_messages.key.remoteJid === "status@broadcast") return;
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
    const _filter = await removeForbiddenCharacters(_theMessage.toLowerCase());
    console.log("filter : " + _filter);
    const _getReply = await dbQuery(
      `SELECT A.ID id, A.REPLY reply, B.BODY sender, A.USER_ID user, A.REPLY_TYPE type FROM autoreplies A, numbers B WHERE B.BODY = ? AND A.NUMBER_ID = B.ID AND B.IS_ACTIVE = ? AND
        B.STATUS = ? AND B.START_TIME <= ? AND B.END_TIME >= ? AND (
          (A.SEARCH_TYPE = ? AND ? LIKE A.KEYWORD || '%') OR
          (A.SEARCH_TYPE = ? AND ? LIKE '%' || A.KEYWORD) OR
          (A.SEARCH_TYPE = ? AND A.KEYWORD = ?) OR
        (A.SEARCH_TYPE = ? AND (LOCATE(A.KEYWORD, ?) > ?))) LIMIT 1`,
      [
        _sock.user.id.split(":")[0],
        1,
        "Connected",
        timeNow(),
        timeNow(),
        "first",
        _filter,
        "last",
        _filter,
        "exact",
        _filter,
        "contains",
        _filter,
        0,
      ]
    );

    let _messageable = "",
      _user = "",
      _type = "",
      _point = 0,
      _status = "failed",
      _msg = null;
    const _from = _messages.key.remoteJid.split("@")[0];

    if (_getReply.length === 0) {
      const _getNumProp = await dbQuery(
        "SELECT user_id user, webhook FROM numbers where body = ? AND status = ? AND start_time <= ? AND end_time >= ?",
        [_sock.user.id.split(":")[0], "Connected", timeNow(), timeNow()]
      );
      if (_getNumProp.length < 1 || _getNumProp[0]["webhook"] === null) return;

      const _dataWebHook = await sendWebhook({
        command: _filter,
        from: _from,
        url: _getReply[0]["webhook"],
      });

    if (_dataWebHook === ![]) return;
      _texts = JSON.stringify(_dataWebHook);
      _messageable = "WEBHOOK";
      _type = "WEBHOOK";
      _user = _getNumProp[0]["user"];
    } else {
      _texts = JSON.stringify(_getReply[0]["reply"]);
      _messageable = "App\\Models\\Autoreply";
      _user = _getReply[0]["user"];
      _type = _getReply[0]["type"];
    }

    // fs.writeFileSync("chats/sent-" + _sock.user.id.split(":")[0] + ".txt",_texts),
    let _param = {
      type: _type,
      body: JSON.parse(_texts),
    };
    await processing(_sock, _messages.key.remoteJid, _param)
      .then(() => {
        _status = "success";
      })
      .catch((_err) => {
        _msg = _err.message;
      });

    if (_status == "success") {
      const _getPoint = await dbQuery(
        "SELECT point FROM costs WHERE slug = ?",
        [_getReply[0]["type"]]
      );

      if (_getPoint.length === 0) {
        _point = process.env.DEFAULT_POINT;
      } else {
        _point = _getPoint[0]["point"];
      }
    }
    console.log(JSON.parse(_texts));
    let uuid = v4();
    await dbQuery(
      `INSERT INTO messages (id,user_id,messageable_type,messageable_id,sender,receiver,body,type,point,status,status_description,executed_at,created_at,updated_at) 
      VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        uuid,
        _user,
        _messageable,
        (_messageable == "WEBHOOK" ? uuid : _getReply[0]["id"]),
        _sock.user.id.split(":")[0],
        _from,
        _texts,
        _type,
        _point,
        _status,
        JSON.stringify(_msg).replace(/["'/\\$]/g, ""),
        toSqlDateTime(new Date()),
        toSqlDateTime(new Date()),
        toSqlDateTime(new Date()),
      ]
    );

    if (_point > 0) {
      let uuidH = v4();
      await dbQuery(
        `INSERT INTO histories (id,user_id,historyable_type,historyable_id,point,type,created_at,updated_at)  
        VALUES(?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          uuidH,
          _user,
          _messageable,
          (_messageable == "WEBHOOK" ? uuid : _getReply[0]["id"]),
          _point,
          "-",
          toSqlDateTime(new Date()),
          toSqlDateTime(new Date()),
        ]
      );

      await dbQuery(
        "UPDATE points SET point = point - ? WHERE user_id = ?", [_point, _user]
      );
    }
    db.commit();
  } catch (error) {
    console.log(error);
    db.rollback();
  }
};

async function sendWebhook({ command: _command, from: _from, url: _url }) {
  try {
    const _data = { message: _command, from: _from },
      _res = await axios.post(_url, _data);
    return _res["data"];
  } catch (_err) {
    return console.log(_err), ![];
  }
}
module.exports = { autoReply: autoReply };
