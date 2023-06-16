<x-app-layout title="Message Test">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <div class="app-content">
        @if (session()->has('alert'))
            <x-alert>
                @slot('type', session('alert')['type'])
                @slot('msg', session('alert')['msg'])
            </x-alert>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="page-description page-description-tabbed">
                            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="account-tab" data-bs-toggle="tab"
                                        data-bs-target="#text" type="button" role="tab" aria-controls="hoaccountme"
                                        aria-selected="true">Teks</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="security-tab" data-bs-toggle="tab"
                                        data-bs-target="#image" type="button" role="tab" aria-controls="security"
                                        aria-selected="false">Media</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="integrations-tab" data-bs-toggle="tab"
                                        data-bs-target="#button" type="button" role="tab"
                                        aria-controls="integrations" aria-selected="false">Button</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="integrations-tab" data-bs-toggle="tab"
                                        data-bs-target="#template" type="button" role="tab"
                                        aria-controls="integrations" aria-selected="false">Template</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="integrations-tab" data-bs-toggle="tab"
                                        data-bs-target="#location" type="button" role="tab"
                                        aria-controls="integrations" aria-selected="false">Location</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="text" role="tabpanel"
                                aria-labelledby="account-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Pesan Teks</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <form action="{{ route('textMessageTest') }}" method="POST" id="formKirimMsg">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="sender" class="form-label">Pengirim</label>
                                                        <select name="sender" id="sender" class="form-control" style="width: 100%;" required>
                                                            @foreach ($numbers as $number)
                                                                <option value="{{ $number->id }}">{{ $number->name }} - {{ $number->body }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="receiver" class="form-label">Penerima</label>
                                                        <input type="number" name="receiver" class="form-control" min="1" placeholder="628xxxxxxxxx" value="{{ old('receiver') }}" id="receiver" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="message" class="form-label">Pesan Teks</label>
                                                        <textarea name="message" id="message" style="height: 100px" class="form-control" placeholder="Pesan teks" required>{{ old('message') }}</textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-success mt-3">
                                                        <i class="material-icons-outlined">send</i>Kirim
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="image" role="tabpanel" aria-labelledby="security-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Media Pesan</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <form action="{{ route('mediaMessageTest') }}" method="POST" id="formKirimMsg">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="sender" class="form-label">Pengirim</label>
                                                        <select name="sender" id="sender" class="form-control" style="width: 100%;" required>
                                                            @foreach ($numbers as $number)
                                                                <option value="{{ $number->id }}">{{ $number->name }} - {{ $number->body }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="receiver" class="form-label">Penerima</label>
                                                        <input type="number" name="receiver" class="form-control" min="1" placeholder="628xxxxxxxxx" id="receiver" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="caption">Keterangan<b class="text-danger">*</b></label>
                                                        <textarea name="caption" id="caption" style="height: 100px" class="form-control" placeholder="Keterangan judul media" required>{{ old('caption') }}</textarea>
                                                        <div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
                                                            1. Keterangan media hanya berfungsi pada Gambar dan Video. (masih wajib)
                                                        </div>
                                                    </div>
                                                    <label for="thumbnail">Media<b class="text-danger">*</b></label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-btn">
                                                            <a id="imagetest" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                                                                <i class="fa fa-picture-o"></i> Pilih
                                                            </a>
                                                        </span>
                                                        <input id="thumbnail" class="form-control form-control-sm" type="text" name="media" value="{{ old('media') }}" readonly required>
                                                    </div>
                                                    <button type="submit" class="btn btn-success mt-3">
                                                        <i class="material-icons-outlined">send</i>Kirim
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="button" role="tabpanel"
                                aria-labelledby="integrations-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Button Message</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <form action="{{ route('buttonMessageTest') }}" method="POST"
                                                    id="formKirimMsg">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="sender" class="form-label">Pengirim</label>
                                                        <select name="sender" id="sender" class="form-control" style="width: 100%;" required>
                                                            @foreach ($numbers as $number)
                                                                <option value="{{ $number->id }}">{{ $number->name }} - {{ $number->body }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="receiver" class="form-label">Penerima</label>
                                                        <input type="text" name="receiver" class="form-control" id="receiver" placeholder="628xxxxxxxxx" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="caption">Keterangan<b class="text-danger">*</b></label>
                                                        <textarea name="caption" id="caption" style="height: 100px" class="form-control" placeholder="Keterangan judul tombol" required>{{ old('caption') }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="button1">Button 1 <b class="text-danger">*</b></label>
                                                        <input type="text" name="button1" class="form-control" id="button1" value="{{ old('button1') }}" placeholder="Display Text Button 1">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="button2">Button 2</label>
                                                        <input type="text" name="button2" class="form-control" id="button2" value="{{ old('button2') }}" placeholder="Display Text Button 2">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="button2">Button 3</label>
                                                        <input type="text" name="button3" class="form-control" id="button3" value="{{ old('button3') }}" placeholder="Display Text Button 3">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="footer">Footer Message</label>
                                                        <input type="text" name="footer" class="form-control" id="footer" value="{{ old('footer') }}" placeholder="Footer message">
                                                    </div>
                                                    <button type="submit" class="btn btn-success mt-3">
                                                        <i class="material-icons-outlined">send</i>Kirim
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="template" role="tabpanel"
                                aria-labelledby="integrations-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Template Message</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <form action="{{ route('templateMessageTest') }}" method="POST"
                                                    id="formKirimMsg">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="sender" class="form-label">Pengirim</label>
                                                        <select name="sender" id="sender" class="form-control" style="width: 100%;" required>
                                                            @foreach ($numbers as $number)
                                                                <option value="{{ $number->id }}">{{ $number->name }} - {{ $number->body }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="receiver" class="form-label">Penerima</label>
                                                        <input type="number" name="receiver" class="form-control" min="1" placeholder="628xxxxxxxxx" id="receiver" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="caption">Keterangan<b class="text-danger">*</b></label>
                                                        <textarea name="caption" id="caption" style="height: 100px" class="form-control" placeholder="Keterangan Title Button" required>{{ old('caption') }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="footer">Footer Message</label>
                                                        <input type="text" name="footer" class="form-control" id="footer" value="{{ old('footer') }}" placeholder="Footer message">
                                                    </div>
                                                    <h6 class="mb-3">Template 1 <b class="text-danger">*</b></h6>
                                                    <div class="mb-3">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="button-type" id="url" value="url" required {{ isset($campaign) && ! is_null($campaign->template->template) ? ($campaign->template->template->templateButtons[0]->type == 'url' ? 'checked' : '') : 'checked' }}>
                                                            <label class="form-check-label" for="url">URL Button</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="button-type" id="call" value="call" {{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'call' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="call">Call Button</label>
                                                        </div>
                                                        <div class="form-check form-check-inline float-end">
                                                            <a href="javascript:void(0)" class="btn btn-sm btn-primary" id="add-button">
                                                                Tambah Template
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="text">Display Text</label>
                                                            <input type="text" class="form-control" id="text" name="text" placeholder="Display Text" value="{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->templateButtons[0]->displayText : old('text') }}" required>
                                                        </div>
                                                        <div class="col">
                                                            <label for="text" id="label-button-type">{{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'call' ? 'Phone Number' : 'URL' }}</label>
                                                            <input type="text" class="form-control" id="action" name="action" value="{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->templateButtons[0]->action : old('action') }}" placeholder="{{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'call' ? '628123456789' : 'https://example.com' }}" required>
                                                        </div>
                                                    </div>
                                                    <div id="second-button" class="mt-3"></div>
                                                    <button type="submit" class="btn btn-success mt-3">
                                                        <i class="material-icons-outlined">send</i>Kirim
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="location" role="tabpanel"
                                aria-labelledby="integrations-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Location Message</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <form action="{{ route('locationMessageTest') }}" method="POST"
                                                    id="formKirimMsg">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="sender" class="form-label">Pengirim</label>
                                                        <select name="sender" id="sender" class="form-control" style="width: 100%;" required>
                                                            @foreach ($numbers as $number)
                                                                <option value="{{ $number->id }}">{{ $number->name }} - {{ $number->body }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="receiver" class="form-label">Penerima</label>
                                                        <input type="text" name="receiver" class="form-control" id="receiver" placeholder="628xxxxxxxxx" required>
                                                    </div>
                                                    <style>
                                                        #map {
                                                            height: 350px;
                                                            widows: 75%;
                                                        }
                                                    </style>
                                                    <div class="mb-3">
                                                        <div id="map"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="mb-3 col-6">
                                                            <label for="latitude">Latitude</label>
                                                            <input type="text" name="latitude" class="form-control" id="latitude"
                                                                value="{{ isset($autoreply) && $autoreply->reply_type == 'location' ? json_decode($autoreply->reply)->lat : old('latitude') }}"
                                                                placeholder="Latitude" readonly>
                                                        </div>
                                                        <div class="mb-3 col-6">
                                                            <label for="longitude">Longitude</label>
                                                            <input type="text" name="longitude" class="form-control" id="longitude"
                                                                value="{{ isset($autoreply) && $autoreply->reply_type == 'location' ? json_decode($autoreply->reply)->long : old('longitude') }}"
                                                                placeholder="Longitude" readonly>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-success mt-3">
                                                        <i class="material-icons-outlined">send</i>Kirim
                                                    </button>
                                                </form>
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
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#imagetest').filemanager('file')

    $(document).on('click', '#remove-button', function() {
        $('#second-button').html('');
        $('#add-button').removeClass('d-none');
    });

    $(document).on('change', 'input[name="button-type2"]', function() {
        $('#action2').val("");
        if ($(this).val() == 'url') {
            $('#label-button-type2').text('URL');
            $('#action2').attr('placeholder', 'https://example.com');
        } else {
            $('#label-button-type2').text('Phone Number');
            $('#action2').attr('placeholder', '628123456789');
        }
    });

    $(document).ready(function(){
        $('input[name="button-type"]').change(function(){
            $('#action').val("");
            if ($(this).val() == 'url') {
                $('#label-button-type').text('URL');
                $('#action').attr('placeholder', 'https://example.com');
            } else {
                $('#label-button-type').text('Phone Number');
                $('#action').attr('placeholder', '628123456789');
            }
        })

        $('#add-button').click(function(){
            $(this).addClass('d-none');
            $('#second-button').html(`<div class="mb-3">
                <h6 class="mb-3">Template 2</h6>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="button-type2" id="url2" value="url" required checked>
                        <label class="form-check-label" for="url2">URL Button</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="button-type2" id="call2" value="call">
                        <label class="form-check-label" for="call2">Call Button</label>
                    </div>
                    <div class="form-check form-check-inline float-end">
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" id="remove-button">
                            Remove Template
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="text2">Display Text</label>
                        <input type="text" class="form-control" id="text2" name="text2" placeholder="Display Text" value="{{ old('text2') }}" required>
                    </div>
                    <div class="col">
                        <label for="action2" id="label-button-type2">URL</label>
                        <input type="text" class="form-control" id="action2" name="action2" value="{{ old('action') }}" placeholder="https://example.com" required>
                    </div>
                </div>                       
            </div>`)
        });

        var defLat = -6.175392;
        var defLong = 106.827153;
        function setToInput(lat, long) {
            $('#latitude').val(lat)
            $('#longitude').val(long)
        }
        setToInput(defLat, defLong);
        var map = L.map('map').setView([defLat, defLong], 14);
        var marker = L.marker([defLat, defLong], {
            draggable: true
        }).addTo(map);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
        marker.on('moveend', function(e) {
            setToInput(e.target._latlng.lat, e.target._latlng.lng);
        });
        map.on('click', function(e) {
            marker.setLatLng(e.latlng)
            setToInput(e.latlng.lat, e.latlng.lng);
        });
        var control = L.Control.geocoder({
            placeholder: 'Search here...',
            defaultMarkGeocode: false,
        }).on('markgeocode', function(e) {
            marker.setLatLng(e.geocode.center)
            map.setView(e.geocode.center, map.getZoom());
            setToInput(e.geocode.center.lat, e.geocode.center.lng);
        }).addTo(map);
    })
</script>
