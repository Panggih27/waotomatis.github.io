----------------------------------
#WaOtomatis.Com
----------------------------------
#Requirement 
- Minimum Reqirement Server VPS RAM 2GB, vCPU 2 Core 
- Ubuntu 18.04
- node v16.16.0
- npm 8.11.0
- php7.4
- php7.4-fpm
- mysql 5.7
- nginx
- pm2
- composer
  
##Install PHP
sudo add-apt-repository ppa:ondrej/php
sudo apt update -y && apt upgrade -y
sudo apt-get install -y nginx php7.4 php7.4-fpm php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmth

##Install Composer
```
php -r "copy('https://getcomposer.org/installer', 'composer-set.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce337678c5611085589f1f3ddf8b3c52d662cd01d4ba75c0ap updatee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHfig_0.8.12-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.12-1_all.deb
sudo dpkg-reconfigure mysql-apt-config 
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 467B942D3A79BD29
```

##Install Nodejs
```
wget https://nodejs.org/dist/v16.16.0/node-v16.16.0-linux-x64.tar.xz
tar -C /usr/local --strip-components 1 -xJf node-v16.16.0-linux-x64.tar.xz
npm install -g npm@8.13.2
```

##Install MySql 5.7
```
wget https://dev.mysql.com/get/mysql-apt-config_0.8.12-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.12-1_all.deb
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 467B942D3A79BD29
sudo apt update && upgrade -y
sudo apt install mysql-server-5.7
sudo mysql_secure_installation
```

##Clone Project
```
cd /var/www/html/
git clone https://gitlab.com/sholihin/wa-blasting.git
```
##Konfigurasi Project
- Sesuikan konfigurasi database dan nama domain pada .env, jika belum memiliki .env maka silahkan copy dari root directory project dengan nama .env.example
- Lakukan instalasi laravel dengan perintah composer install
- Kemudian lakukan instalasi nodejs dengan perintah npm install
  
##Install PM2

Masuk ke root directory project terlebih dulu kemudian lakukan install berikut :
```
npm install pm2 -g
pm2 startup
pm2 start server.js --name waotomatis -- --port 3200
pm2 save
```
note : pastikan .env.example sudah di copy menjadi .env pada root directory project, langkah ini untuk menjalankan aplikasi server whatsapp di port 3200 pada http://localhost:3200, lakukan open port 3200 pada firewall server.

##Optional Instalasi
###Install Certbot (Untuk Keperluan SSL Gratis Di VPS)
```
sudo apt install certbot
sudo certbot --nginx -d waotomatis.com -d www.waotomatis.com
/etc/letsencrypt/live/waotomatis.com/fullchain.pem
/etc/letsencrypt/live/waotomatis.com/privkey.pem
```
note: daftarkan lokasi sertifikat ssl pada file server.js seperti berikut :
```
const { startCon } = require('./server/WaConnection')
//const http = require('http');
const https = require('https');
const fs = require('fs');
const express = require('express');
const app = express();
//const server = http.createServer(app);
const server = https.createServer({
    'key': fs.readFileSync('/etc/letsencrypt/live/waotomatis.com/privkey.pem'),
    'cert': fs.readFileSync('/etc/letsencrypt/live/waotomatis.com/fullchain.pem')
}, app);
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
```

###Setting Tanggal WIB Di Server
```
timedatectl set-timezone Asia/Jakarta
```

###Contoh Konfigurasi nginx Pada Server
Silahkan cek pada root direcotry ```/nginx/prod-waotomatis.conf```