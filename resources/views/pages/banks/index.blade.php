<x-app-layout title="Banks">
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


                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex justify-content-right">
                        <a href="{{ route('bank.create') }}" class="btn btn-primary">
                            <i class="material-icons-outlined">add</i>Tambah
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <table id="tableBank" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Bank</th>
                                            <th class="text-center">Nama Pemilik</th>
                                            <th class="text-center">Nomor Rekening</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($banks as $bank)
                                            <tr>
                                                <td class="text-center nowrap">
                                                    <img src="{{ asset($bank->bank->image, true) }}" height="125" width="125" class="img-thumbnail" alt="{{ $bank->bank->name }}">
                                                    <h3 class="d-sm-block d-md-none">{{ $bank->bank->name }}</h3>
                                                </td>
                                                <td class="text-center">
                                                    {{ $bank->account_name }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $bank->account_number }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input bank-status" id="{{ $bank->id }}" type="checkbox" role="switch" data-id="{{ $bank->id }}" value="{{ $bank->id }}" {{ $bank->status ? 'checked' : false }}>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('bank.show', $bank) }}" class="btn btn-sm btn-primary btn-edit me-2">
                                                            Edit
                                                        </a>
                                                        <form action='{{ route("bank.destroy", $bank->id) }}' method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger del-bank" type="submit">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/pages/datatables.js') }}"></script>
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    <script>

        $(document).ready(function () {

            $("#tableBank").DataTable({
                responsive: true
            });

            $('.bank-status').on('change', function() {
                var id = $(this).val();
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("bank.status", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "PATCH",
                    data: {
                        'status': status
                    },
                    success: function (data) {
                        console.log(data);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            })

            $('#tableBank tbody').on('click', '.del-bank', function(e) {
                var form =  $(this).closest("form");
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin ingin menghapus bank ini ?",
                    text: "aksi ini bersifat permanent, transaksi yang menggunakan bank ini kemungkinan akan tetap menggunakan bank ini atau transaksi tidak dapat diproses",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Lanjutkan!",
                    cancelButtonText: "Tidak, Batalkan!"
                }).then((willProcess) => {
                    if (willProcess.isConfirmed) {
                        form.submit();
                    }
                });
            })
        });
    </script>
</x-app-layout>
