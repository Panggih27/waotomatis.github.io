<x-app-layout title="Auto Replies">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <div class="app-content">
        <div class="content-wrapper">
            <div class="w-100">
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
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <a href="{{ route('autoreply.index') }}">
                                    <i class="fas fa-arrow-alt-left"></i>
                                </a>
                                <div class="d-flex">
                                    <h5 class="card-title">Balas Otomatis</h5>
                                    {{-- <form action="{{ route('deleteAllAutoreply') }}" method="POST">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm"><i
                                                class="material-icons">delete_outline</i>Delete All</button>
                                    </form>
                                    <button type="button" class="btn btn-primary btn-sm mx-4" data-bs-toggle="modal"
                                        data-bs-target="#addAutoRespond"><i
                                            class="material-icons-outlined">add</i>Add</button> --}}
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('autoreply.store') }}" method="POST"
                                    enctype="multipart/form-data" id="formautoreply">
                                    @isset($autoreply)
                                        @method('PATCH')
                                    @endisset
                                    @csrf
                                    <div class="mb-3">
                                        <link href="{{ asset('plugins/select2/css/select2.css') }}" rel="stylesheet">
                                        <style>
                                            .select2-container {
                                                z-index: 100 !important;
                                            }
                                        </style>
                                        <div class="mb-3">
                                            <label for="sender" class="form-label">Pengirim <b class="text-danger">*</b></label>
                                            <select name="sender" id="sender" class="form-control form-control-fix" required>
                                                @foreach ($numbers as $number)
                                                    <option value="{{ $number->id }}"
                                                        {{ isset($autoreply) ? ($number->id == $autoreply->number_id ? 'selected' : '') : '' }}>
                                                        {{ $number->name }} - {{ $number->body }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <script src="{{ asset('js/pages/select2.js') }}"></script>
                                        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col">
                                            <label for="keyword" class="form-label">Kata Kunci <b
                                                    class="text-danger">*</b></label>
                                            <input type="text" name="keyword"
                                                value="{{ $autoreply->keyword ?? old('keyword') }}"
                                                class="form-control form-control-sm" id="keyword"
                                                aria-describedby="keywordFeedback" required>
                                            <div id="keywordFeedback" class="invalid-feedback">Hanya Alphabet, Angka dan Karakter Spesial tertentu saja yang diperbolehkan</div>
                                        </div>
                                        <div class="mb-3 col">
                                            <label for="search" class="form-label">Tipe Pencarian Kata Kunci <b
                                                    class="text-danger">*</b></label>
                                            <select name="search" id="search" class="form-control" required>
                                                <option value="first"
                                                    {{ isset($autoreply) ? ($autoreply->search_type == 'first' ? 'selected' : '') : '' }}>
                                                    Awal Kata</option>
                                                <option value="last"
                                                    {{ isset($autoreply) ? ($autoreply->search_type == 'last' ? 'selected' : '') : '' }}>
                                                    Akhir Kata</option>
                                                <option value="contains"
                                                    {{ isset($autoreply) ? ($autoreply->search_type == 'contains' ? 'selected' : '') : '' }}>
                                                    Mengandung Kata</option>
                                                <option value="exact"
                                                    {{ isset($autoreply) ? ($autoreply->search_type == 'exact' ? 'selected' : '') : '' }}>
                                                    Sama Persis</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Tipe Balas <b
                                                class="text-danger">*</b></label>
                                        <style>
                                            .select2-container {
                                                z-index: 100 !important;
                                            }
                                        </style>
                                        <select name="type" id="type" class="js-states form-control"
                                            tabindex="-1" style="display: none; width: 100%" required>
                                            <option selected disabled>Pilihan</option>
                                            <option value="text"
                                                {{ isset($autoreply) ? ($autoreply->reply_type == 'text' ? 'selected' : '') : '' }}>
                                                Text Message</option>
                                            <option value="media"
                                                {{ isset($autoreply) ? ($autoreply->reply_type == 'media' ? 'selected' : '') : '' }}>
                                                Media Message</option>
                                            <option value="button"
                                                {{ isset($autoreply) ? ($autoreply->reply_type == 'button' ? 'selected' : '') : '' }}>
                                                Button Message</option>
                                            <option value="template"
                                                {{ isset($autoreply) ? ($autoreply->reply_type == 'template' ? 'selected' : '') : '' }}>
                                                Template Message</option>
                                            <option value="location"
                                                {{ isset($autoreply) ? ($autoreply->reply_type == 'location' ? 'selected' : '') : '' }}>
                                                Location Message</option>
                                        </select>
                                    </div>
                                    <div class="ajaxplace mb-3"></div>
                                    <button type="submit" name="submit"
                                        class="btn btn-primary float-end">{{ isset($autoreply) ? 'Rubah' : 'Simpan' }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    {{-- <script src="{{ asset('js/autoreply.js') }}"></script> --}}
    <script>
        $(document).ready(function() {

            $('#keyword').keyup(function() {
                var _set = new RegExp('^[a-zA-Z0-9@;:-]*$');
                if (!_set.test($(this).val())) {
                    $(this).addClass('is-invalid')
                } else {
                    if ($(this).hasClass('is-invalid')) {
                        $(this).removeClass('is-invalid')
                    }
                }
            })

            $('#type').select2();

            $('#device').select2();

            $(".js-example-basic-multiple-limit").select2({
                maximumSelectionLength: 2
            });

            $(".js-example-tokenizer").select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });

            @if (isset($autoreply))

                $('#formautoreply').attr('action', "{{ route('autoreply.update', $autoreply->id) }}")
                $.ajax({
                    url: "{{ route('autoreply.getFormByType', $autoreply->reply_type) }}" +
                        '?edit={{ $autoreply->id }}',
                    type: "GET",
                    dataType: "html",
                    success: (result) => {
                        $(".ajaxplace").html(result);
                    },
                    error: (error) => {
                        console.log(error.responseText);
                    },
                });

                $('#type').on('change', () => {
                    const type = $('#type').val();
                    $.ajax({
                        url: "{{ route('autoreply.getFormByType', ':type') }}".replace(':type',
                            type + '?edit={{ $autoreply->id }}'),
                        type: "GET",
                        dataType: "html",
                        success: (result) => {
                            $(".ajaxplace").html(result);
                        },
                        error: (error) => {
                            console.log(error.responseText);
                        },
                    });
                })
            @else
                $('#type').on('change', () => {
                    const type = $('#type').val();
                    $.ajax({
                        url: "{{ route('autoreply.getFormByType', ':type') }}".replace(':type',
                            type),
                        type: "GET",
                        dataType: "html",
                        success: (result) => {
                            $(".ajaxplace").html(result);
                        },
                        error: (error) => {
                            console.log(error.responseText);
                        },
                    });
                })
            @endif
        })
    </script>
</x-app-layout>
