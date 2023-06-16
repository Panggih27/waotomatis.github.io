const P = require("pino");
const {
  default: makeWASocket,
  useSingleFileAuthState,
  DisconnectReason,
  jidDecode,
  makeInMemoryStore,
  useMultiFileAuthState,
  fetchLatestBaileysVersion
} = require("@adiwajshing/baileys");
const fs = require("fs");
const qrCode = require("qrcode");
const { setStatus, dbQuery } = require("./Querydb");
const { autoReply } = require("./Autoreply");
const { toSqlDateTime, fetchUrl, fetchBuffer } = require("./Helper");
const { Boom } = require("@hapi/boom");
const phoneNumber = require("awesome-phonenumber");
const { v4 } = require("uuid");
const { db } = require("./Database");
const FileType = require("file-type");
const { inboxes } = require("./Inbox");

const startCon = async (
  _device,
  _socket = undefined,
  _isLogout = undefined
  ) => {
    const { state, saveCreds } = await useMultiFileAuthState("./sessions/" + _device);
    const {version, isLatest} = await fetchLatestBaileysVersion();
    
  const sock = makeWASocket({
    auth: state,
    version: version,
    logger: P({ level: "silent" }),
    printQRInTerminal: ![],
    browser: [process.env.APP_NAME, "Desktop", "10.0"],
  });

  sock.ev.on("connection.update", async (update) => {
    const {
      qr: qrString,
      connection: connection,
      lastDisconnect: lastDisconnect,
    } = update;
    console.log("connection is : " + connection);
    if (connection === "close") {
      let reason = new Boom(lastDisconnect?.error)?.output.statusCode;
      console.log("reason : " + reason);
      if (reason !== DisconnectReason.loggedOut) {
        reason == DisconnectReason.restartRequired &&
          startCon(_device),
          _socket !== undefined ? _socket.emit("Process") : "";
      } else {
        reason === DisconnectReason.loggedOut &&
          (_socket !== undefined ? _socket.emit("Unauthorized") : "",
          await setStatus(_device, "Disconnect"),
          fs.existsSync("./sessions/" + _device) &&
            fs.rmSync("./sessions/" + _device, { recursive: true }));
      }
    } else {
      if (connection === "open") {
        _socket !== undefined ? _socket.emit("Authenticated", sock.user) : console.log('Authenticated socket undefined');

        if (_device && _device !== sock.user.id.split(":")[0]) {
          sock.logout().then(async () => {
            console.log("Device is not match");
            _socket !== undefined ? _socket.emit("wrongDevice") : console.log('wrongDevice socket undefined');
            await setStatus(_device, "Disconnect")
            fs.existsSync("./sessions/" + _device) &&
            fs.rmSync("./sessions/" + _device, { recursive: true });
          });
          return;
        }

        if (_isLogout) {
          sock.logout().then(async () => {
            await setStatus(_device, "Disconnect"), _socket.emit("Process");
            fs.existsSync("./sessions/" + _device) &&
              fs.rmSync("./sessions/" + _device, { recursive: true });
          });
          return;
        }
        await setStatus(_device, "Connected");
      }
    }
    qrString &&
      qrCode.toDataURL(qrString, (err, url) => {
        if (err) console.log(err);
        _socket !== undefined ? _socket.emit("QrGenerated", url) : "";
      });
  });

  sock.ev.on("messages.upsert", (_message) => {
    if (_message !== undefined && _message !== null) {
      autoReply(_message, sock);
      inboxes(_message, sock);
    }
  });

  sock.ev.on("group-participants.update", async (group) => {
    db.beginTransaction();
    try {
      if (
        group.action === "remove" &&
        group.participants.includes(sock.user.id.split(":")[0] + '@s.whatsapp.net')
      ) {
        console.log('im being removed from group');
        const _queryNumber = await dbQuery(
          "SELECT id FROM numbers WHERE body = ?",
          [sock.user.id.split(":")[0]]
        );
        await dbQuery("DELETE FROM groups WHERE jid = ? and number_id = ?", [
          group.id.split("@")[0],
          _queryNumber[0].id,
        ]);
        return;
      }
      console.log(group);
      let metadata = await sock.groupMetadata(group.id);
      const _getGroup = await dbQuery(
        "SELECT B.number_id id, B.jid FROM numbers A, groups B WHERE A.id = B.number_id AND A.body = ?",
        [sock.user.id.split(":")[0]]
      );

      if (_getGroup.length > 0) {
        console.log("group exists");
        await dbQuery(
          "UPDATE groups SET participant_count = ?, title = ? WHERE jid = ? AND number_id = ?",
          [metadata.participants.length, metadata.subject, _getGroup[0].jid, _getGroup[0].id]
        );
      } else {
        console.log("group does not exist");
        const _queryNumber = await dbQuery(
          "SELECT id FROM numbers WHERE body = ?",
          [sock.user.id.split(":")[0]]
        );

        let uuid = v4();
        await dbQuery(
          "INSERT INTO groups (id, number_id, jid, title, participant_count, is_mine, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
          [
            uuid,
            _queryNumber[0].id,
            metadata.id.split("@")[0],
            metadata.subject,
            metadata.participants.length,
            metadata.owner.split("@")[0] === sock.user.id.split(":")[0] ? 1 : 0,
            toSqlDateTime(new Date()),
            toSqlDateTime(new Date()),
          ]
        );
      }
      
      db.commit();
    } catch (err) {
      db.rollback();
      console.log(err);
    }
  });

  sock.ev.on("creds.update", saveCreds);

  sock.decodeJid = (jid) => {
    if (!jid) return jid;
    if (/:\d+@/gi.test(jid)) {
      let decode = jidDecode(jid) || {};
      return (
        (decode.user && decode.server && decode.user + "@" + decode.server) ||
        jid
      );
    } else return jid;
  };

  sock.getName = async (jid, withoutContact = false) => {
    id = sock.decodeJid(jid);
    withoutContact = sock.withoutContact || withoutContact;
    let v;
    if (id.endsWith("@g.us"))
      return new Promise(async (resolve) => {
        v = store.contacts[id] || {};
        if (!(v.name || v.subject)) v = sock.groupMetadata(id) || {};
        resolve(
          v.name ||
            v.subject ||
            phoneNumber("+" + id.replace("@s.whatsapp.net", "")).getNumber(
              "international"
            )
        );
      });
    else
      v =
        id === "0@s.whatsapp.net"
          ? {
              id,
              name: "WhatsApp",
            }
          : id === sock.decodeJid(sock.user.id)
          ? sock.user
          : store.contacts[id] || {};
    return (
      (withoutContact ? "" : v.name) ||
      v.subject ||
      v.verifiedName ||
      phoneNumber("+" + jid.replace("@s.whatsapp.net", "")).getNumber(
        "international"
      )
    );
  };

  sock.sendContact = async (jid, contacts, quoted = {}, options = {}) => {
    let list = [];
    addon = quoted ? { quoted, ...options } : { ...options };
    for (let i of contacts) {
      list.push({
        displayName: await sock.getName(i.number + "@s.whatsapp.net"),
        vcard: `BEGIN:VCARD\nVERSION:3.0\n" +"FN:${await sock.getName(
          i.number + "@s.whatsapp.net"
        )}\nTEL;type=CELL;type=VOICE;waid=${i.number}:+${phoneNumber(
          "+" + i.number
        ).getNumber("international")}\nEND:VCARD`,
      });
    }
    console.log(list);
    await sock.sendMessage(jid,
      { contacts: { displayName: `${list.length} Contacts`, contacts: list }, ...options },
      { addon }
    ).catch((err) => {
      console.log(err);
    });
  };

  sock.fetchAllGroups = async () => {
    const getGroups = await sock.groupFetchAllParticipating();
    db.beginTransaction();
    try {
      let _groups = Object.entries(getGroups)
        .slice(0)
        .map((entry) => entry[1]);
      console.log(_groups);
      let _jids = _groups.map((v) => v.id.split("@")[0]);
      const _getNumber = await dbQuery(
        "SELECT B.number_id id, B.jid FROM numbers A, groups B WHERE A.id = B.number_id AND A.body = ?",
        [sock.user.id.split(":")[0]]
      );
      let _number_id = 0;
      if (_getNumber.length > 0) {
        _number_id = _getNumber[0].id;
      } else {
        const _queryNumber = await dbQuery(
          "SELECT id FROM numbers WHERE body = ?",
          [sock.user.id.split(":")[0]]
        );
        _number_id = _queryNumber[0].id;
      }
      let _numbers = _getNumber.map((v) => v.jid);
      let _notEx = _jids.filter((v) => {
        return !_numbers.includes(v);
      });
      let _notIn = _numbers.filter((v) => {
        return !_jids.includes(v);
      });
      let _insGroup = _groups.filter((v) => {
        return _notEx.includes(v.id.split("@")[0]);
      });

      for (let i of _insGroup) {
        let uuid = v4();
        await dbQuery(
          "INSERT INTO groups (id, number_id, jid, title, participant_count, is_mine, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
          [
            uuid,
            _number_id,
            i.id.split("@")[0],
            i.subject,
            i.participants.length,
            i.owner && i.owner.split("@")[0] === sock.user.id.split(":")[0] ? 1 : 0,
            toSqlDateTime(new Date()),
            toSqlDateTime(new Date()),
          ]
        );
      }

      for (let d of _notIn) {
        await dbQuery("DELETE FROM groups WHERE jid = ? and number_id = ?", [
          d,
          _number_id,
        ]);
      }

      db.commit();
      return getGroups;
    } catch (error) {
      db.rollback();
      console.log(error);
    }
  }

  sock.sendMedia = async (jid, media, quoted = {}, options = {}) => {
    
    addon = quoted ? { quoted, ...options } : { ...options };
    const res = await fetchBuffer(media.url).catch((err) => {
      console.log(err);
    });
    let { ext, mime } = await FileType.fromBuffer(res.data).catch((err) => {
      console.log(err);
    });
    let fileName = new Date * 1 + '.' + ext, type = '';
    if (/image/.test(mime)) type = "image";
    else if (/video/.test(mime)) type = "video";
    else if (/audio/.test(mime)) type = "audio";
    else type = "document";

    await sock.sendMessage(
      jid, { [type]: { url: media.url }, caption: media.caption, mimetype: mime, fileName: fileName, ...options },
      { addon }
    ).catch((err) => {
      console.log(err);
    });
  }

  sock.sendButton = async (jid, buttons, quoted = {}, options = {}) => { 
    addon = quoted ? { quoted, ...options } : { ...options };

    let _buttons = [];

    buttons.data.filter((item) => item !== null && item !== undefined).forEach((item, index) => {
      _buttons.push({
        buttonId: "id" + (index + 1),
        buttonText: { displayText: item },
        type: 1,
      });
    });

    const buttonMessage = {
      text: buttons.caption,
      footer: buttons.footer,
      buttons: _buttons,
      headerType: 1,
      ...options,
    };

    await sock.sendMessage(jid, buttonMessage, { addon }).catch((err) => {
      console.log(err);
    });
  }

  sock.sendTemplate = async (jid, template, quoted = {}, options = {}) => { 
    addon = quoted ? { quoted, ...options } : { ...options };

    let _templates = [];

    console.log(template);

    template.data.filter((item) => item.hasOwnProperty('text')).forEach((item, i) => {
      _templates.push({
        index: i + 1,
        [item.type]: {
          'displayText': item.text,
          [item.type == "urlButton" ? "url" : "phoneNumber"]: item.action
        },
      });
    });

    const templateMessage = {
      text: template.caption,
      footer: template.footer,
      templateButtons: _templates,
      ...options,
    };

    await sock.sendMessage(jid, templateMessage, { addon }).catch((err) => {
      console.log(err);
    });
  }

  sock.sendLocation = async (jid, location, quoted = {}, options = {}) => {
    addon = quoted ? { quoted, ...options } : { ...options };

    const locationMessage = {
      location: {
        degreesLatitude: location.lat,
        degreesLongitude: location.long,
      },
      ...options,
    };

    await sock.sendMessage(jid, locationMessage, { addon }).catch((err) => {
      console.log(err);
    });
  }

  return sock;
};

// setInterval(async () => {
//   const query = await dbQuery(
//     "SELECT * FROM numbers WHERE status = 'Connected' AND is_active = " +
//       1 +
//       " AND start_time <= '" +
//       timeNow() +
//       "' AND end_time >= '" +
//       timeNow() +
//       "'"
//   );
//   query.forEach(async (data) => {
//     fs.existsSync("./sessions/session-" + data.body + ".json") &&
//       (startCon(data.body),
//       console.log("Success initialize " + data.body + " Device"));
//   });
// }, 10_000),
module.exports = { startCon: startCon };
