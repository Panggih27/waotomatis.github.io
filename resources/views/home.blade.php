<x-app-layout title="Home">

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
                    <div class="col-xl-6">
                        <div class="card widget widget-stats">
                            <div class="card-body">
                                <div class="widget-stats-container d-flex">
                                    <div class="widget-stats-icon widget-stats-icon-primary">
                                        <i class="far fa-address-book"></i>
                                    </div>
                                    <div class="widget-stats-content flex-fill">
                                        <span class="widget-stats-title">Kontak</span>
                                        <span class="widget-stats-amount">{{ Auth::user()->contacts()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card widget widget-stats">
                            <div class="card-body">
                                <div class="widget-stats-container d-flex">
                                    <div class="widget-stats-icon widget-stats-icon-warning">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </div>
                                    <div class="widget-stats-content flex-fill">
                                        <span class="widget-stats-title">Pesan</span>

                                        <span
                                            class="widget-stats-info">{{ Auth::user()->messages()->where(['status' => 'success'])->count() }}
                                            Success</span>
                                        <span
                                            class="widget-stats-info">{{ Auth::user()->messages()->where(['status' => 'failed'])->count() }}
                                            Failed</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-xl-4">
                        <div class="card widget widget-stats">
                            <div class="card-body">
                                <div class="widget-stats-container d-flex">
                                    <div class="widget-stats-icon widget-stats-icon-danger">
                                        <i class="material-icons-outlined">schedule</i>
                                    </div>
                                    <div class="widget-stats-content flex-fill">
                                        <span class="widget-stats-title">Pesan jadwal</span>
    
                                        <span class="widget-stats-info">0 Sukses</span>
                                        <span class="widget-stats-info">0 Gagal</span>
                                        <span class="widget-stats-info">0 Pending</span>
                                    </div>
    
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">Server Whatsapp</h5>
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                    data-bs-target="#addDevice"><i class="fas fa-plus"></i>Tambah
                                </button>
                                <table class="table table-striped">
                                    <thead>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">HP</th>
                                        {{-- <th class="text-center">Webhook Link</th> --}}
                                        <th class="text-center">Jadwal Aktif</th>
                                        <th class="text-center">Jeda</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aktifasi</th>
                                        <th class="text-center">Aksi</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($numbers as $key => $number)
                                            <tr>

                                                <td class="text-center">
                                                    {{ $key + 1 }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $number->name }}
                                                </td>
                                                <td class="text-center">
                                                    {{ implode('-', str_split('+' .  $number->body, 4)) }}
                                                </td>
                                                {{-- <td>{{ $number['webhook'] }}</td> --}}
                                                <td class="text-center fw-bold">
                                                    {{ Carbon\Carbon::parse($number->start_time)->format('H:i') . ' - ' . Carbon\Carbon::parse($number->end_time)->format('H:i') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $number->delay ?? 0 }} (s)
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge fs-6 badge-{{ $number->status == 'Connected' ? 'success' : 'danger' }}">{{ $number->status == 'Connected' ? 'Terhubung' : 'Terputus' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input activating-device" type="checkbox" role="switch" data-id="{{ $number->id }}" value="{{ $number->id }}" {{ $number->is_active ? 'checked' : false}}>
                                                    </div>
                                                </td>
                                                <td class="d-flex justify-content-center">
                                                    <a href="{{ route('scan', $number->body) }}" class="btn-trans px-61 text-dark text-center">
                                                        <i class="p-1 fas fa-qrcode"></i>
                                                    </a>
                                                    <button class="btn-trans text-center text-warning ml-1 editNumber" data-bs-toggle="modal"
                                                        data-bs-target="#addDevice" data-id="{{ $number->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('device.destroy', $number->id) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" name="delete" class="btn-trans text-danger text-center delete-device">
                                                            <i class="p-1 fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addDevice" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Nomor Handphone</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ route('device.store') }}" method="POST" id="deviceForm">
                        @csrf
                        <div id="patchUpdate"></div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Nama">
                        </div>
                        <div class="mb-3">
                            <label for="number" class="form-label">Nomor Handphone</label>
                            <input type="text" name="sender" class="form-control" id="number" placeholder="62xxx">
                            <p class="text-small text-danger">*gunakan kode negara ( tanpa + )</p>
                        </div>
                        <div class="input-group row">
                            <div class="mb-3 col-6">
                                <label for="delay_type" class="form-label">Jeda</label>
                                <select class="form-select form-select-sm" name="delay_type" id="delay_type">
                                    <option value="time" id="time">Time</option>
                                    <option value="random" id="random">Random</option>
                                </select>
                            </div>
                            <div class="mb-3 col-6" id="time_delay">
                                <label for="delay" class="form-label">Waktu</label>
                                <input type="number" min="0" name="delay" class="form-control" id="delay" placeholder="1">
                            </div>
                        </div>
                        <div class="input-group row d-none" id="random_delay">
                            <div class="mb-3 col-6">
                                <label for="delay_from" class="form-label">Dari</label>
                                <input type="number" min="0" name="delay_from" class="form-control" id="delay_from" placeholder="1">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="delay_to" class="form-label">Ke</label>
                                <input type="number" min="0" name="delay_to" class="form-control" id="delay_to" placeholder="5">
                            </div>
                        </div>
                        <div class="input-group row">
                            <div class="mb-3 col-6">
                                <label for="start" class="form-label">Mulai Jam Aktif</label>
                                <input type="time" name="start" class="form-control" id="start" placeholder="1">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="end" class="form-label">Akhir Jam Aktif</label>
                                <input type="time" name="end" class="form-control" id="end" placeholder="5">
                            </div>
                        </div>
                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" name="active" value="1" role="switch" id="active" checked>
                            <label class="form-check-label" for="active">Aktifkan ?</label>
                        </div>
                        {{-- <label for="urlwebhook" class="form-label">Link webhook</label>
                        <input type="text" name="urlwebhook" class="form-control" id="urlwebhook">
                        <p class="text-small text-danger">*Optional</p> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" id="closeForm" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.delete-device').on('click', function (e) {
                var form =  $(this).closest("form");
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin ingin menghapus device ini ?",
                    text: "aksi ini bersifat permanent, data yang terkait dengan device ini akan ikut terhapus",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Lanjutkan!",
                    cancelButtonText: "Tidak, Batalkan!"
                }).then((willProcess) => {
                    if (willProcess.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $('#delay_type').on('change', function() {
                if (this.value == 'time') {
                    $('#time_delay').removeClass('d-none');
                    $('#random_delay').addClass('d-none');
                } else {
                    $('#time_delay').addClass('d-none');
                    $('#random_delay').removeClass('d-none');
                }
            })

            $('.editNumber').click(function() {

                let id = $(this).attr('data-id');

                $.ajax({
                    url: '{{ route("device.show", ":id") }}'.replace(':id', id),
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#modalTitle').html('Rubah Nomor Handphone');
                        $('#name').val(data.name);
                        $('#number').val(data.body);
                        if (data.delay != null && data.delay.includes('-')) {
                            $('#delay_type').val('random');
                            $('#random_delay').removeClass('d-none');
                            $('#time_delay').addClass('d-none');
                            let delay = data.delay.trim().split('-');
                            $('#delay_from').val(parseInt(delay[0]));
                            $('#delay_to').val(parseInt(delay[1]));
                        } else {
                            $('#time').attr('checked', true);
                            $('#time_delay').val(data.delay);
                        }
                        $('#start').val(data.start_time.substr(0,5));
                        $('#end').val(data.end_time.substr(0,5));
                        if (data.is_active) {
                            $('#active').attr('checked', true);
                        } else {
                            $('#active').attr('checked', false);
                        }
                        $('#deviceForm').attr("action", "{{ route('device.update', ":id") }}".replace(':id', id));
                        $('#patchUpdate').html('<input type="hidden" name="_method" value="PATCH" id="UpdatePatch">');

                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            })

            $('#closeForm').click(function(){
                $('#modalTitle').html('ADD DEVICE');
                $('#number').val('');
                $('#delay_type').val('time');
                $('#time_delay').removeClass('d-none');
                $('#random_delay').addClass('d-none');
                $('#start').val('');
                $('#end').val('');
                $('#active').attr('checked', true);
                $('#deviceForm').attr("action", "{{ route('device.store') }}");
                $('#patchUpdate').html('');
            })

            $('.activating-device').on('change', function() {
                var id = $(this).val();

                var url = '{{ route("device.activating", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "PATCH",
                    data: {
                        'status': status
                    },
                    success: function (data) {
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            })
        })
    </script>

</x-app-layout>
