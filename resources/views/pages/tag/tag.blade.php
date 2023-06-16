<x-app-layout title="Contacts">
    <div class="app-content">
        <link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
        <link href="{{ asset('plugins/select2/css/select2.css') }}" rel="stylesheet">
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


                <div class="card-header d-flex justify-content-between px-0">

                    {{-- <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#selectNomor"><i class="material-icons-outlined">contacts</i>Generate Kontak</button> --}}
                    <div class="d-flex justify-content-right">

                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addTag"><i
                                class="material-icons-outlined">add</i>Tambah</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title">Tags</h5>
                                <!-- <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#selectNomor"><i class="material-icons-outlined">contacts</i>Hapus semua</button>
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#selectNomor"><i class="material-icons-outlined">contacts</i>Generate Kontak</button>
                                <div class="d-flex justify-content-right">
                                    <form action="" method="POST">
                                        <button type="submit" name="export" class="btn btn-warning "><i class="material-icons">download</i>Export (xlsx)</button>
                                    </form>
                                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#importExcel"><i class="material-icons-outlined">upload</i>Import (xlsx)</button>
                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addNumber"><i class="material-icons-outlined">add</i>Tambah</button>
                                </div> -->
                            </div>
                            <div class="card-body">
                                <table id="datatable1" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Kontak</th>
                                            <th class="d-flex justify-content-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tags as $tag)
                                            <tr>
                                                <td class="text-center">{{ $tag->name }}</td>
                                                <td class="text-center">{{ $tag->contacts_count }} Kontak</td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <button class="btn btn-success btn-sm mx-3"
                                                            onclick="viewTag('{{ $tag->id }}', '{{ $tag->name }}')">Daftar Kontak</button>
                                                        <form action="{{ route('tag.delete') }}" method="POST">
                                                            @method('delete')
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $tag->id }}">
                                                            <button type="submit" name="delete"
                                                                class="btn btn-danger btn-sm del-tag">Hapus</button>
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
    <div class="modal fade" id="addTag" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tag.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewTag" tabindex="-1" aria-labelledby="exampleModalXlLabel" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4 titleTag" id="exampleModalXlLabel "></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body listContactsInTag">
                    ...
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/pages/datatables.js') }}"></script>
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    <script>
        function viewTag(id, name) {
            $('.titleTag').html('Tag ID #' + name);
            $.ajax({
                url: "{{ route('tag.view', ':id') }}".replace(':id', id),
                method: 'GET',
                dataType: 'html',
                success: (result) => {
                    $('.listContactsInTag').html(result)
                    $('#viewTag').modal('show')
                },
                error: (err) => {
                    console.log(err)
                }
            })
        }

        $(document).ready(function() {
            $('#datatable1 tbody').on('click', '.del-tag', function(e) {
                var form =  $(this).closest("form");
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin ingin menghapus penanda ini ?",
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
