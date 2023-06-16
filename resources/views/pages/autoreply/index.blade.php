<x-app-layout title="Auto Replies">
<link rel="stylesheet" href="{{ asset("css/custom.css") }}">
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
                                <h5 class="card-title">Balas Otomatis</h5>
                                <div class="d-flex">

                                    {{-- <form action="{{ route('deleteAllAutoreply') }}" method="POST">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Hapus Semua</button>
                                    </form> --}}
                                    <a href="{{ route('autoreply.create') }}" class="btn btn-primary btn-sm m-l-xs mr-0">Tambah</a>
                                    {{-- <button type="button" class="btn btn-primary btn-sm mx-4" data-bs-toggle="modal"
                                        data-bs-target="#addAutoRespond"><i
                                            class="material-icons-outlined">add</i>Add</button> --}}
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="datatable1" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Pengirim</th>
                                            <th class="text-center">Kata Kunci</th>
                                            <th class="text-center">Tipe Balas</th>
                                            <th class="text-center">Pencarian Kata</th>
                                            <th class="text-center">Tampil</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($autoreplies as $autoreply)
                                            <tr>
                                                <td class="text-center">{{ implode('-', str_split('+' .  $autoreply->number->body, 4)) }} </td>
                                                <td class="text-center">{{ $autoreply->keyword }} </td>
                                                <td class="text-center">{{ $autoreply->reply_type }} </td>
                                                <td class="text-center">{{ ucwords($autoreply->search_type) . ' Word' }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-primary btn-sm viewReply" data-id="{{ $autoreply->id }}">Lihat</button>
                                                </td>
                                                <td class="d-flex justify-content0-center">
                                                    <a href="{{ route('autoreply.show', $autoreply->id) }}" class="btn btn-sm btn-info">Rician</a>
                                                    <a href="{{ route('autoreply.edit', $autoreply->id) }}" class="btn btn-sm btn-warning mx-1">Rubah</a>
                                                    <form action="{{ route('autoreply.destroy', $autoreply->id) }}" method="POST" class="">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" name="delete"
                                                            class="btn btn-danger btn-sm del-autoreply">Hapus</button>
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
    <!-- Modal -->
    {{-- <div class="modal fade" id="addAutoRespond" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data" id="formautoreply">
                        @csrf
                        <label for="device" class="form-label">Device/Sender</label>
                        <select id="device" name="device" class="js-states form-control" tabindex="-1"
                            style="display: none; width: 100%" required>
                            @foreach ($numbers as $number)
                                <option value="{{ $number['body'] }}">{{ $number['body'] }}</option>
                            @endforeach
                        </select>
                        <label for="keyword" class="form-label">Keyword</label>
                        <input type="text" name="keyword" class="form-control" id="keyword" required>
                        <label for="type" class="form-label">Type Reply</label>
                        <select name="type" id="type" class="js-states form-control" tabindex="-1"
                            style="display: none; width: 100%" required>
                            <option selected disabled>Select One</option>
                            <option value="text">Text Message</option>
                            <option value="image">Image Message</option>
                            <option value="button">Button Message</option>
                            <option value="template">Template Message</option>

                        </select>
                        <div class="ajaxplace"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Responf Auto Reply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body showReply">
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    <script src="{{ asset('js/pages/datatables.js') }}"></script>
    <link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    <link href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#type').select2({
                dropdownParent: $('#addAutoRespond')
            });

            $('#device').select2({
                dropdownParent: $('#addAutoRespond')
            });

            $('select[name="datatable1_length"]').select2();

            $(".js-example-basic-multiple-limit").select2({
                maximumSelectionLength: 2
            });

            $(".js-example-tokenizer").select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });

            $('.viewReply').on('click', function() {
                var id = $(this).data('id');
                console.log(id);
                $.ajax({
                    url: "{{ route('autoreply.showRespond', ':id') }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'html',
                    success: (result) => {

                        $('.showReply').html(result);
                        $('#modalView').modal('show')
                    },
                    error: (error) => {
                        console.log(error.responseText);
                    }
                })
            })

            $('#datatable1 tbody').on('click', '.del-autoreply', function(e) {
                var form =  $(this).closest("form");
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin ingin menghapus template balas otomastis ini ?",
                    text: "aksi ini bersifat permanent, data terkait kemungkinan akan ikut terhapus",
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
        })
    </script>
</x-app-layout>
