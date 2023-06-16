const { startCon } = require('./server/WaConnection')
const http = require('http');
// const https = require('https');
const fs = require('fs');
const express = require('express');
const app = express();
const server = http.createServer(app);
// const server = https.createServer({
//     'key': fs.readFileSync('/etc/ssl/private/cert.key'),
//     'cert': fs.readFileSync('/etc/ssl/certs/cert.pem')
// }, app);
const router = express.Router();
const { Server } = require('socket.io');
const { dbQuery } = require('./server/Querydb');
const io = new Server(server);
app.use(express.json());
app.use(express.urlencoded({ extended: true, limit: '150mb' }))
app.use(router);
require('./server/Routes')(router)

io.on('connection', (socket) => {
    socket.on('StartConnection', async (device) => {
        startCon(device, socket)
        return;
    })
    socket.on('LogoutDevice', (device) => {
        startCon(device, socket, true)
        return;
    })
})
server.listen(process.env.PORT_NODE, () => {
    console.log(`Server running on port ${process.env.PORT_NODE}`);
})


async function initiate() {
    const query = await dbQuery(
    "SELECT body FROM numbers WHERE status = ?", ["Connected"]
  );
  query.forEach(async (data) => {
    fs.existsSync("./sessions/" + data.body) &&
      (startCon(data.body),
      console.log("Success initialize " + data.body + " Device"));
  });
    console.log('initiated')
}

initiate();
