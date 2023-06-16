<x-app-layout title="Campaign">
    <div class="app-content">
        <link href="{{ asset('plugins/select2/css/select2.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
            integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
            crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
            integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
            crossorigin=""></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
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
                {{-- <div class="card-header d-flex justify-content-between">
                    <div class="d-flex justify-content-right">
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" id="addModal"
                            data-bs-target="#addProduct">
                            <i class="material-icons-outlined">add</i>Add
                        </button>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title">{{ isset($campaign) ? 'Rubah' : 'Tambah' }} Kampanye</h5>
                            </div>
                            <div class="card-body">
                                <form
                                    action="{{ isset($campaign) ? route('campaign.update', $campaign->id) : route('campaign.store') }}"
                                    method="POST">
                                    @csrf
                                    @isset($campaign)
                                        @method('PATCH')
                                    @endisset
                                    <style>
                                        .select2-container {
                                            z-index: 100 !important;
                                        }
                                    </style>
                                    <div class="mb-3">
                                        <label for="senders" class="fw-bold">Pengirim <b
                                                class="text-danger">*</b></label>
                                        <select name="sender" id="senders" class="form-control" required>
                                            @foreach ($senders as $sender)
                                                <option value="{{ $sender->id }}"
                                                    {{ $sender->id == ($campaign->number_id ?? '') ? 'selected' : '' }}>
                                                    {{ $sender->name }} - {{ $sender->body }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="title" class="fw-bold">Judul <b class="text-danger">*</b></label>
                                        <input type="text" name="title" class="form-control" id="title"
                                            value="{{ $campaign->title ?? old('title') }}"
                                            placeholder="Judul kampanye" required>
                                    </div>
                                    <label for="receiver" class="fw-bold">Penerima <b class="text-danger">*</b></label>
                                    <div class="mb-3 ms-2">
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver"
                                                id="contact" value="contact"
                                                {{ isset($campaign) ? ($campaign->receivers->type == 'contact' ? 'checked' : '') : 'checked' }}>
                                            <label class="form-check-label" for="contact">Manual</label>
                                        </div>
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver"
                                                id="group" value="group"
                                                {{ isset($campaign) && $campaign->receivers->type == 'group' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="group">Kontak Grup</label>
                                        </div>
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver"
                                                id="tag" value="tag"
                                                {{ isset($campaign) && $campaign->receivers->type == 'tag' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tag">Tag</label>
                                        </div>
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver"
                                                id="random" value="random"
                                                {{ isset($campaign) && $campaign->receivers->type == 'random' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="random">Acak</label>
                                        </div>
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver"
                                                id="all" value="all"
                                                {{ isset($campaign) && $campaign->receivers->type == 'all' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="all">Semua</label>
                                        </div>
                                        {{-- <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver" id="contact" value="contact" {{ isset($campaign) ? ($campaign->receivers->type == 'contact' ? 'checked' : '') : 'checked' }}>
                                            <label class="form-check-label" for="contact">Contact</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver" id="tag" value="tag" {{ isset($campaign) && $campaign->receivers->type == 'tag' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tag">Tag</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver" id="all" value="all" {{ isset($campaign) && $campaign->receivers->type == 'all' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="all">All</label>
                                        </div> --}}
                                        {{-- <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="receiver" id="group" value="group">
                                            <label class="form-check-label" for="group">Group</label>
                                        </div> --}}
                                    </div>
                                    <div class="list-receivers">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    {{-- <div class="mb-3 mt-2 ms-2">
                                        <div class="form-check form-switch form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="message-switch" id="message-switch" checked>
                                            <label class="form-check-label" for="message-switch">Pesan Teks</label>
                                        </div>
                                    </div> --}}
                                    <div class="form-check form-switch mt-4 ms-2">
                                        <label class="form-check-label fw-bold" for="message">Pesan Teks</label>
                                        <input class="form-check-input" name="text-campaign" type="checkbox"
                                            role="switch" id="text" value="text" disabled
                                            {{ isset($campaign) ? (!is_null($campaign->template->text) ? 'checked' : '') : 'checked' }}>
                                    </div>
                                    <div id="show-text" class="mt-3">
                                        <textarea name="message" id="message" style="height: 100px" class="form-control" placeholder="Pesan teks"
                                            required>{{ isset($campaign) && !is_null($campaign->template->text) ? $campaign->template->text : old('message') }}</textarea>
                                        <div class="border rounded bg-light p-3 mt-2 text-break lh-lg"
                                            style="font-size: 13px !important;">
                                            1. Anda dapat menggunakan spintax dengan format berikut {kata|kata|kata}. Cth:..
                                            {Hello|Hi|Hai kawan,|Assalamualaikum} <br>
                                            2. Anda dapat menggunakan variabel dynamc dari kontak var1 hingga var5 Cth:..
                                            @{{ var1 }} @{{ var2 }} @{{ var3 }}
                                            @{{ var4 }} @{{ var5 }}
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mt-4 ms-2">
                                        <label for="template" class="fw-bold">Template</label>
                                        <input class="form-check-input campaign-type" name="template-campaign"
                                            type="checkbox" role="switch" id="template" value="template">
                                    </div>
                                    <div id="show-template" class="mt-4"></div>
                                    <div class="form-check form-switch mt-4 ms-2">
                                        <label for="media" class="fw-bold">Media</label>
                                        <input class="form-check-input campaign-type" name="media-campaign"
                                            type="checkbox" role="switch" id="media" value="media">
                                    </div>
                                    <div id="show-media" class="mt-4"></div>
                                    <div class="form-check form-switch mt-4 ms-2">
                                        <label for="button" class="fw-bold">Button</label>
                                        <input class="form-check-input campaign-type" name="button-campaign"
                                            type="checkbox" role="switch" id="button" value="button">
                                    </div>
                                    <div id="show-button" class="mt-4"></div>
                                    <div class="form-check form-switch mt-4 ms-2">
                                        <label for="contacts" class="fw-bold">Contact</label>
                                        <input class="form-check-input campaign-type" name="contact-campaign"
                                            type="checkbox" role="switch" id="contacts" value="contact">
                                    </div>
                                    <div id="show-contact" class="mt-4"></div>
                                    <div class="form-check form-switch mt-4 ms-2">
                                        <label for="location" class="fw-bold">Location</label>
                                        <input class="form-check-input campaign-type" name="location-campaign"
                                            type="checkbox" role="switch" id="location" value="location">
                                    </div>
                                    <div id="show-location" class="mt-4"></div>
                                    {{-- <label for="type" class="mb-2">Type <b class="text-danger">*</b></label>
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="template" value="template" {{ isset($campaign) && $type == 'template' ? 'checked' : '' }} >
                                            <label class="form-check-label" for="template">Template</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="media" value="media" {{ isset($campaign) && $type == 'media' ? 'checked' : '' }} >
                                            <label class="form-check-label" for="media">Media</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="button" value="button" {{ isset($campaign) && $type == 'button' ? 'checked' : '' }} >
                                            <label class="form-check-label" for="button">Button</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="none" value="none" required {{ isset($campaign) ? ($type == 'none' ? 'checked' : '') : 'checked' }} >
                                            <label class="form-check-label" for="none">None</label>
                                        </div>
                                    </div> --}}
                                    <div id="show-type" class="mb-3"></div>
                                    <label for="schedule" class="fw-bold">Jadwal Kampanye <b
                                            class="text-danger">*</b></label>
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="scheduling"
                                                id="scheduling-true" value="schedule"
                                                {{ isset($campaign) ? (!is_null($campaign->schedule) ? 'checked' : '') : '' }}>
                                            <label class="form-check-label" for="scheduling-true">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="scheduling"
                                                id="manual-broadcast" value="0"
                                                {{ isset($campaign) ? ($campaign->is_manual && !is_null($campaign->is_manual) && is_null($campaign->schedule) ? 'checked' : '') : '' }}>
                                            <label class="form-check-label" for="manual-broadcast">Manual
                                                Broadcast</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="scheduling"
                                                id="broadcast-now" value="now"
                                                {{ isset($campaign) ? (!$campaign->is_manual && !is_null($campaign->is_manual) && is_null($campaign->schedule) ? 'checked' : '') : 'checked' }}>
                                            <label class="form-check-label" for="broadcast-now">Broadcast Sekarang</label>
                                        </div>
                                    </div>
                                    <div class="mb-3" id="scheduling-set"></div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary float-end" type="submit">
                                            {{ isset($campaign) ? 'Rubah' : 'Simpan' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/pages/select2.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    @if (isset($campaign))
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                    }
                });

                if ('{{ !is_null($campaign->template->text) }}') {
                    $('#text').prop('checked', true);
                } else {
                    $('#text').prop('checked', false);
                    $('#show-text').html('');
                }

                if ('{{ $campaign->receivers->type }}' != 'all') {
                    $.ajax({
                        url: "{!! route('campaign.type') . '?type=' . $campaign->receivers->type . '&update=' . $campaign->id !!}",
                        type: "GET",
                        success: function(data) {
                            $('.list-receivers').html(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                } else {
                    $('.list-receivers').html(
                        '<h3 class="font-bold p-2 text-center rounded border border-info">All Contacts</h3>');
                }

                if ('{{ $campaign->schedule }}' != '') {
                    $.ajax({
                        url: "{!! route('campaign.type') . '?campaign=schedule' . '&update=' . $campaign->id !!}",
                        type: "GET",
                        success: function(data) {
                            $('#scheduling-set').html(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }

                if ('{{ !is_null($campaign->template->template) }}') {
                    $.ajax({
                        url: "{!! route('campaign.type') . '?campaign=template&update=' . $campaign->id !!}",
                        type: "GET",
                        success: function(data) {
                            $('#show-template').html(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });

                    $('#text').prop('disabled', false);
                    $('#template').prop('checked', true);
                }
                if ('{{ !is_null($campaign->template->media) }}') {
                    $.ajax({
                        url: "{!! route('campaign.type') . '?campaign=media&update=' . $campaign->id !!}",
                        type: "GET",
                        success: function(data) {
                            $('#show-media').html(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });

                    $('#text').prop('disabled', false);
                    $('#media').prop('checked', true);
                }
                if ('{{ !is_null($campaign->template->button) }}') {
                    $.ajax({
                        url: "{!! route('campaign.type') . '?campaign=button&update=' . $campaign->id !!}",
                        type: "GET",
                        success: function(data) {
                            $('#show-button').html(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });

                    $('#text').prop('disabled', false);
                    $('#button').prop('checked', true);
                }
                if ('{{ !is_null($campaign->template->contact) }}') {
                    $.ajax({
                        url: "{!! route('campaign.type') . '?campaign=contact&update=' . $campaign->id !!}",
                        type: "GET",
                        success: function(data) {
                            $('#show-contact').html(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });

                    $('#text').prop('disabled', false);
                    $('#contacts').prop('checked', true);
                }

                if ('{{ !is_null($campaign->template->location) }}') {
                    $.ajax({
                        url: "{!! route('campaign.type') . '?campaign=location&update=' . $campaign->id !!}",
                        type: "GET",
                        success: function(data) {
                            $('#show-location').html(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });

                    $('#text').prop('disabled', false);
                    $('#location').prop('checked', true);
                }

                $('input[name="receiver"]').change(function() {
                    $('.list-receivers').html(
                        '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                        );
                    if ($(this).val() != 'all') {
                        $.ajax({
                            url: "{{ route('campaign.type') . '?type=' }}" + $(this).val() +
                                '&update=' + '{{ $campaign->id }}',
                            type: "GET",
                            success: function(data) {
                                $('.list-receivers').html(data);
                            },
                            error: function(err) {
                                console.log(err);
                            }
                        });
                    } else {
                        $('.list-receivers').html(
                            '<h3 class="font-bold p-2 text-center rounded border border-info">All Contacts</h3>'
                            );
                    }
                });

                $('input[name="scheduling"]').change(function() {
                    $('#scheduling-set').html(
                        '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                        );
                    if ($(this).val() == 'schedule') {
                        $.ajax({
                            url: "{{ route('campaign.type') . '?campaign=' }}" + $(this).val() +
                                '&update=' + '{{ $campaign->id }}',
                            type: "GET",
                            success: function(data) {
                                $('#scheduling-set').html(data);
                            },
                            error: function(err) {
                                console.log(err);
                            }
                        });
                    } else {
                        $('#scheduling-set').html('');
                    }
                });

                $('.campaign-type').change(function() {
                    let type = $(this).val(),
                        id = null;
                    if ($(this).val() == 'template') {
                        if ($('#show-template').html() == '') {
                            $('#show-template').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-template';
                        } else {
                            $('#show-template').html('')
                        }
                    } else if ($(this).val() == 'media') {
                        if ($('#show-media').html() == '') {
                            $('#show-media').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-media';
                        } else {
                            $('#show-media').html('')
                        }
                    } else if ($(this).val() == 'contact') {
                        if ($('#show-contact').html() == '') {
                            $('#show-contact').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-contact';
                        } else {
                            $('#show-contact').html('')
                        }
                    } else if ($(this).val() == 'button') {
                        if ($('#show-button').html() == '') {
                            $('#show-button').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-button';
                        } else {
                            $('#show-button').html('')
                        }
                    }
                    if (id != null) {
                        $.ajax({
                            url: "{{ route('campaign.type') . '?campaign=' }}" + type + '&update=' +
                                '{{ $campaign->id }}',
                            type: "GET",
                            success: function(data) {
                                $(id).html(data);
                            },
                            error: function(err) {
                                console.log(err);
                            }
                        });
                    }

                    let checkeds = [];

                    $('.campaign-type').each(function() {
                        if ($(this).is(':checked')) {
                            checkeds.push($(this).val());
                        }
                    });

                    if (checkeds.length == 0) {
                        $('#text').prop('disabled', true);
                        $('#text').prop('checked', true);
                        if ($('#show-text').html() == '') {
                            $('#show-text').html(`<textarea name="message" id="message" style="height: 100px" class="form-control" placeholder="Pesan Teks" required>{{ $campaign->template->text ?? old('message') }}</textarea>
                            <div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
                                1. Anda dapat menggunakan spintax dengan format berikut {kata|kata|kata}. Cth:.. {Hello|Hi|Hai kawan,|Assalamualaikum} <br>
                                2. Anda dapat menggunakan variabel dynamc dari kontak var1 hingga var5 Cth:.. @{{ var1 }} @{{ var2 }} @{{ var3 }} @{{ var4 }} @{{ var5 }}
                            </div>`)
                        }
                    } else {
                        $('#text').prop('disabled', false);
                    }
                });

                $('#text').on('change', function() {
                    if ($('#show-text').html() == '') {
                        $('#show-text').html(`<textarea name="message" id="message" style="height: 100px" class="form-control" placeholder="Pesan Teks" required>{{ $campaign->template->text ?? old('message') }}</textarea><div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
                            1. Anda dapat menggunakan spintax dengan format berikut {kata|kata|kata}. Cth:.. {Hello|Hi|Hai kawan,|Assalamualaikum} <br>
                            2. Anda dapat menggunakan variabel dynamc dari kontak var1 hingga var5 Cth:.. @{{ var1 }} @{{ var2 }} @{{ var3 }} @{{ var4 }} @{{ var5 }}
                        </div>`)
                    } else {
                        $('#show-text').html('')
                    }
                })
            })
        </script>
    @else
        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                    }
                });

                $('#text').prop('checked', true);

                $.ajax({
                    url: "{{ route('campaign.type') . '?type=' . (isset($campaign) ? $campaign->receivers->type : 'contact') }}",
                    type: "GET",
                    success: function(data) {
                        $('.list-receivers').html(data);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });

                $('input[name="receiver"]').change(function() {
                    $('.list-receivers').html(
                        '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                        );
                    if ($(this).val() != 'all') {
                        $.ajax({
                            url: "{{ route('campaign.type') . '?type=' }}" + $(this).val(),
                            type: "GET",
                            success: function(data) {
                                $('.list-receivers').html(data);
                            },
                            error: function(err) {
                                console.log(err);
                            }
                        });
                    } else {
                        $('.list-receivers').html(
                            '<h3 class="font-bold p-2 text-center rounded border border-info">All Contacts</h3>'
                            );
                    }
                });

                $('input[name="scheduling"]').change(function() {
                    $('#scheduling-set').html(
                        '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                        );
                    if ($(this).val() == 'schedule') {
                        $.ajax({
                            url: "{{ route('campaign.type') . '?campaign=' }}" + $(this).val(),
                            type: "GET",
                            success: function(data) {
                                $('#scheduling-set').html(data);
                            },
                            error: function(err) {
                                console.log(err);
                            }
                        });
                    } else {
                        $('#scheduling-set').html('');
                    }
                });

                $('.campaign-type').change(function() {
                    let type = $(this).val(),
                        id = null;
                    if ($(this).val() == 'template') {
                        if ($('#show-template').html() == '') {
                            $('#show-template').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-template';
                        } else {
                            $('#show-template').html('')
                        }
                    } else if ($(this).val() == 'media') {
                        if ($('#show-media').html() == '') {
                            $('#show-media').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-media';
                        } else {
                            $('#show-media').html('')
                        }
                    } else if ($(this).val() == 'contact') {
                        if ($('#show-contact').html() == '') {
                            $('#show-contact').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-contact';
                        } else {
                            $('#show-contact').html('')
                        }
                    } else if ($(this).val() == 'button') {
                        if ($('#show-button').html() == '') {
                            $('#show-button').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-button';
                        } else {
                            $('#show-button').html('')
                        }
                    } else if ($(this).val() == 'location') {
                        if ($('#show-location').html() == '') {
                            $('#show-location').html(
                                '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>'
                                );
                            id = '#show-location';
                        } else {
                            $('#show-location').html('')
                        }
                    }

                    if (id != null) {
                        $.ajax({
                            url: "{{ route('campaign.type') . '?campaign=' }}" + type,
                            type: "GET",
                            success: function(data) {
                                $(id).html(data);
                            },
                            error: function(err) {
                                console.log(err);
                            }
                        });
                    }

                    let checkeds = [];

                    $('.campaign-type').each(function() {
                        if ($(this).is(':checked')) {
                            checkeds.push($(this).val());
                        }
                    });

                    if (checkeds.length == 0) {
                        $('#text').prop('disabled', true);
                        $('#text').prop('checked', true);
                        if ($('#show-text').html() == '') {
                            $('#show-text').html(`<textarea name="message" id="message" style="height: 100px" class="form-control" placeholder="Pesan Teks" required>{{ $campaign->template->text ?? old('message') }}</textarea><div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
                                1. Anda dapat menggunakan spintax dengan format berikut {kata|kata|kata}. Cth:.. {Hello|Hi|Hai kawan,|Assalamualaikum} <br>
                                2. Anda dapat menggunakan variabel dynamc dari kontak var1 hingga var5 Cth:.. @{{ var1 }} @{{ var2 }} @{{ var3 }} @{{ var4 }} @{{ var5 }}
                            </div>`)
                        }
                    } else {
                        $('#text').prop('disabled', false);
                    }
                });

                $('input[name="message-switch"]').change(function() {
                    $('#text-message').toggleClass('d-none');
                })

                $('#text').on('change', function() {
                    if ($('#show-text').html() == '') {
                        $('#show-text').html(`<textarea name="message" id="message" style="height: 100px" class="form-control" placeholder="Pesan Teks" required>{{ $campaign->template->text ?? old('message') }}</textarea><div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
                            1. Anda dapat menggunakan spintax dengan format berikut {kata|kata|kata}. Cth:.. {Hello|Hi|Hai kawan,|Assalamualaikum} <br>
                            2. Anda dapat menggunakan variabel dynamc dari kontak var1 hingga var5 Cth:.. @{{ var1 }} @{{ var2 }} @{{ var3 }} @{{ var4 }} @{{ var5 }}
                        </div>`)
                    } else {
                        $('#show-text').html('')
                    }
                })

            });
        </script>
    @endif
</x-app-layout>
