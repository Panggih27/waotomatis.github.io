<x-app-layout title="Scan {{ $number->body }}">
    <div class="app-content">
        <div class="content-wrapper">
            <div class="w-100">
                <h4 class="my-5">#Device-{{ $number->body }}</h4>
                @if ($number->status == 'Connected')	
                    <div class="alert alert-secondary">Jika Terjadi Gangguan, Bisa Mencoba Untuk Reset Connection dan Login Ulang</div>	
                @else	
                    <div class="alert alert-secondary">Jangan Matikan Scanner HP Sampai Loading Scan Selesai dan Reload Halaman Setelahnya</div>	
                @endif
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget widget-stats-large">
                            <div class="row">
                                <div class="col-xl-8">
                                    <div class="widget-stats-large-chart-container">
                                        <div class="card-header d-flex justify-content-between">	
                                            <div class="logoutbutton"></div>	
                                            <div class="resetbutton"></div>	
                                        </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="apex-earnings"></div>
                                            <div class="imageee text-center">
                                                <img src="{{ asset("images/other/waiting.jpg") }}" height="300px" alt="Waiting">
                                            </div>
                                            <div class="statusss text-center">
                                                <button class="btn btn-primary" type="button" disabled>
                                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                    Connecting to Node server...
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4">
                                        <div class="widget-stats-large-info-container">
                                            <div class="card-header">
                                                <h5 class="card-title">
                                                    Whatsapp Info <span class="badge badge-info badge-style-light">Updated 5 min ago</span>
                                                </h5>
                                            </div>
                                            <div class="card-body account">`
                                                <ul class="list-group account list-group-flush">
                                                    <li class="list-group-item name">Nama : </li>
                                                    <li class="list-group-item number">Nomor : </li>
                                                    <li class="list-group-item device">Device : </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.socket.io/4.4.1/socket.io.min.js"
integrity="sha384-fKnu0iswBIqkjxrhQCTZ7qlLHOFEgNkRmK2vaO/LbTZSXdJfAu6ewRBdwHPhBo/H" crossorigin="anonymous"></script>
<script>
    let socket;
    if ("{{ env('TYPE_SERVER') }}" === 'hosting') {
        socket = io();
    } else {
        socket = io("{{ env('WA_URL_SERVER') }}", { 
            transports: ['websocket', 'polling', 'flashsocket']
        });
    }
    socket.emit('StartConnection', '{{ $number->body }}')

    socket.on('QrGenerated', (url) => {
        $('.imageee').html(` <img src="${url}" height="300px" alt="">`)
        let count = 0;

        $('.statusss').html(`<button class="btn btn-warning" type="button" disabled>
                                                <span class="" role="status" aria-hidden="true"></span>
                                               QR Code didapatkan, silahkan scan
                                            </button>`)
        timeout = setTimeout(() => {
            $('.statusss').html(`  <button class="btn btn-danger" type="button" disabled>
                                                    <span class="" role="status" aria-hidden="true"></span>
                                             Timed Out, Reload halaman untuk Generate qr ulang
                                                </button>`)
            $('.imageee').html(' <img src="../images/other/waiting.jpg" height="300px" alt="Waiting">');

        }, 30000);

    })

    socket.on('Authenticated', data => {

        $('.name').html(`Nama : ${data.name}`)
        $('.number').html(`Number : ${data.id.split(':')[0]}`)
        $('.device').html(`Device : Tidak terdeteksi`)
        // $('.imageee').html(` <img src="${data.imgUrl}" height="300px" alt="">`)
        $('.statusss').html(`  <button class="btn btn-success" type="button" disabled>
                                                <span class="" role="status" aria-hidden="true"></span>
                                            Connected
                                            </button>`)
        $('.logoutbutton').html(`<button class="btn btn-danger" class="logout"  id="logout"  onclick="logout({{ $number->body }})">
                                                Logout
                                            </button>`)
        $('.resetbutton').html(`<button class="btn btn-warning" class="logout"  id="logout"  onclick="logout({{ $number->body }})">Reset Connection</button>`)
        
    })

    socket.on('Proccess', () => {
        $('.statusss').html(`<button class="btn btn-success" type="button" disabled>
                                                <span class="" role="status" aria-hidden="true"></span>
                                               Connection Progres, Will refresh in 5 seconds.
                                            </button>`)
        setTimeout(() => {
            location.reload()
        }, 5000);
    })

    socket.on('Unauthorized', () => {
        $('.statusss').html(`<button class="btn btn-danger" type="button" disabled>
                                                <span class="" role="status" aria-hidden="true"></span>
                                               Unauthorized,you have been logged out, will generate Qr again in 5 seconds
                                            </button>`)
        setTimeout(() => {
            location.reload()
        }, 5000);
    })

    socket.on('wrongDevice', () => {
        $('.statusss').html(`<button class="btn btn-danger" type="button" disabled>
                                                <span class="" role="status" aria-hidden="true"></span>
                                               Unauthorized, please use the number as you registered
                                            </button>`)
        // $('.imageee').html('<img src="../images/other/waiting.jpg" height="300px" alt="Waiting">');
        setTimeout(() => {
            location.reload()
        }, 3000);
    })

    function logout(device) {
        socket.emit('LogoutDevice', device)
    }
</script>
