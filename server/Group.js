const { db } = require("./Database");
const { dbQuery, setStatus } = require("./Querydb");
const { startCon } = require("./WaConnection");
const fs = require("fs");

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

const fetchAllGroup = async (_req, _res) => {
  db.beginTransaction();
  try {
    const _checkCon = await checkConnection(_req.params.sender);
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

    _sock.ev.on("connection.update", async (_update) => {
      if (_update.connection == "open") {
        await _sock
          .fetchAllGroups()
          .then((data) => {
            db.commit();
            return _res.status(200).json({
              status: !![],
              msg: "groups fetched",
              data: data,
            });
          })
          .catch((err) => {
            db.rollback();
            console.log(err);
            return _res.status(500).json({
              status: ![],
              msg: "something went wrong",
              data: null,
            });
          });
      }
    });
  } catch (error) {
    db.rollback();
    console.log(error);
  }
};

const getGroupMetaData = async (_req, _res) => {
  try {
    const _jidValidation = await dbQuery(
      "SELECT * FROM groups A, numbers B WHERE A.number_id = B.id AND A.jid = ? AND B.BODY = ?",
      [_req.params.jid, _req.params.sender]
    );
    if (_jidValidation.length < 1) {
      return _res.status(500).json({
        status: ![],
        msg: "The group jid is not valid, if its your group, please re-fetch all of your groups!",
        data: null,
      });
    }

    const _checkCon = await checkConnection(_req.params.sender);
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

    _sock.ev.on("connection.update", async (_update) => {
      if (_update.connection == "open") {
        await _sock
          .groupMetadata(_req.params.jid + "@g.us")
          .then(async (metadata) => {
            return _res.status(200).json({
              status: !![],
              msg: "group metadata fetched",
              data: metadata,
            });
          })
          .catch(async (err) => {
            console.log(err);
            return _res.status(500).json({
              status: ![],
              msg: "something went wrong",
              data: null,
            });
          });
      }
    });
  } catch (error) {
    console.log(error);
  }
};


module.exports = {
  fetchAllGroup: fetchAllGroup,
  getGroupMetaData: getGroupMetaData,
};
