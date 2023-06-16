<x-app-layout title="Point Cost">
    <div class="app-content">
        <link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
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
                            data-bs-target="#addPointCost">
                            <i class="material-icons-outlined">add</i>Add
                        </button>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="d-block w-100">
                                            <label for="number">Pesan Masuk</label>
                                            <select name="number" id="number" class="form-select">
                                                @foreach ($device as $item)
                                                    <option value="{{ $item->body }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="d-flex justify-content-evenly align-items-center">
                                            <form action="" method="get" id="downloadInboxes" class="d-flex justify-content-evenly align-items-center w-100">
                                                <div class="d-block w-100 me-2">
                                                    <label for="min">Tanggal Mulai</label>
                                                    <input type="date" class="form-control" id="min" name="min">
                                                </div>
                                                <div class="d-block w-100 me-2">
                                                    <label for="max">Tanggal Akhir</label>
                                                    <input type="date" class="form-control" id="max" name="max">
                                                </div>
                                                <input type="hidden" class="form-control" id="download" name="download" value="1">
                                            </form>
                                            <div class="d-block text-center me-2">
                                                <button class="btn btn-sm btn-info mt-3" id="cari-byDate">
                                                    Cari
                                                </button>
                                            </div>
                                            <div class="d-block text-center">
                                                <button class="btn btn-sm btn-success mt-3" id="download-byDate">
                                                    Download
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="datatable1" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Pengirim</th>
                                            <th class="text-center">Isi Pesan</th>
                                            <th class="text-center">Waktu</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('js/pages/datatables.js')}}"></script>
    <script src="{{asset('plugins/datatables/datatables.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                }
            });

            function getInbox(number, addon = '') {
                var url = '{{ route("inbox.show", ":number") }}'.replace(':number', number) + addon;
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "html",
                    success: function (data) {
                        $('tbody').html(data);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }

            getInbox($('#number').val());

            $('#number').on('change', function() {
                getInbox($('#number').val());
            })

            $('#cari-byDate').on('click', function(e) {
                e.preventDefault();
                let addon = '?min=' + $('#min').val() + '&max=' + $('#max').val();
                getInbox($('#number').val(), addon);
            })

            $('#download-byDate').on('click', function(e) {
                e.preventDefault();
                let form = $("#downloadInboxes").closest('form');
                $('#downloadInboxes').attr('action', '{{ route("inbox.show", ":number") }}'.replace(':number', $('#number').val()))
                form.submit();
            })
        })
    </script>
</x-app-layout>