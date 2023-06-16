const { db } = require('./Database')

const setStatus = async (device, status) => {
    db.beginTransaction();
    try {
        db.execute(
          "UPDATE numbers SET status = ?, is_active = ? WHERE body = ?",
          [status, (status == "Connected" ? 1 : 0), device]
        );
        db.commit();
        return true;
    } catch (error) {
        db.rollback();
        return false
    }
}

function dbQuery(query, param) {
    return new Promise(data => {
        db.execute(query, param, (err, res, field) => {
          if (err) throw err;
          try {
            data(res);
          } catch (error) {
            data({});
            //throw error;
          }
        });
    })
}



module.exports = { setStatus, dbQuery }