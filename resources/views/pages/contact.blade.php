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
                    <div class="d-flex justify-content-right">
                        <button type="button" class="btn btn-primary btn-sm " data-bs-toggle="modal"
                            data-bs-target="#addNumber"><i class="fas fa-plus"></i>Tambah</button>
                        <button type="button" class="btn btn-primary btn-sm mx-2" data-bs-toggle="modal"
                        data-bs-target="#importContacts"><i class="fas fa-upload"></i>Import
                        (xlsx)</button>
                        <form action="{{ route('exportContact') }}" method="POST">
                            @csrf
                            <button type="submit" name="" class="btn btn-sm  btn-warning "><i class="fas fa-download"></i>Export (xlsx)</button>
                        </form>
                    </div>
                    <div class="d-flex justify-content-start">
                        <button id="deleteChoice" class="btn btn-sm btn-danger">Hapus<i class="ms-2 fas fa-trash-alt"></i></button>
                        <div class="d-flex justify-content-between visually-hidden" id="deletePurpose">
                            <button id="cancelChoice" class="btn btn-sm btn-primary">Cancel<i class="ms-2 fas fa-times"></i></button>
                            <div class="border rounded p-2 mx-2 bg-light">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" value="semua" id="selectAll">
                                    <label class="form-check-label ms-0 me-1" for="selectAll">
                                        Pilih Semua
                                    </label>
                                </div>
                            </div>
                            <button id="processChoice" class="btn btn-sm btn-danger">Ya, Hapus<i class="ms-2 fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title">Kontak</h5>
                            </div>
                            <div class="card-body">
                                <style>
                                    .select2-container {
                                        z-index: 100 !important;
                                    }
                                </style>
                                <table id="datatable1" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Nomor</th>
                                            <th class="text-center">Tag</th>
                                            <th class="text-center">Var1</th>
                                            <th class="text-center">Var2</th>
                                            <th class="text-center">Var3</th>
                                            <th class="text-center">Var4</th>
                                            <th class="text-center">Var5</th>
                                            <th class="d-flex justify-content-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contacts as $contact)
                                            <tr>
                                                <td class="text-center">{{ $contact->name }}</td>
                                                <td class="text-center">{{ $contact->number }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">{{ collect($contact->tags)->pluck('name')->first() . (count($contact->tags) > 1 ? ', ...' : '') }}</span>
                                                </td>
                                                <td class="text-center">
                                                    {{ $contact->var1 ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $contact->var2 ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $contact->var3 ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $contact->var4 ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $contact->var5 ?? '-' }}
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center action-body">
                                                        <button type="button" id="editTagsContact" class="btn btn-sm btn-info me-1 editTagsContact" data-bs-toggle="modal"
                                                            data-bs-target="#editContactTag" data-id="{{ $contact->id }}">Rubah</button>
                                                        <form action="{{ route('contact.destroy') }}"
                                                            method="POST">
                                                            @method('DELETE')
                                                            @csrf
                                                            <input type="hidden" name="contacts[]" value="{{ $contact->id }}">
                                                            <button type="submit" name="delete"
                                                                class="btn btn-danger btn-sm del-cont">Hapus</button>
                                                        </form>
                                                    </div>
                                                    <div class="d-flex action-delete justify-content-center visually-hidden">
                                                        <div class="form-check">
                                                            <input type="checkbox" value="{{ $contact->id }}" name="deleteContact[]" class="form-check-input delete-checkbox">
                                                        </div>
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
    <div class="modal fade" id="editContactTag" tabindex="-1" aria-labelledby="editContactTagLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContactTagLabel">Edit Tags Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="editContact">
                        @method('PATCH')
                        @csrf
                        <div class="mb-2">
                            <label for="nameEdit" class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" id="nameEdit" required>
                        </div>
                        <div class="mb-2">
                            <label for="numberEdit" class="form-label">Nomor</label>
                            <input type="text" name="number" class="form-control contact-format" aria-describedby="keywordFeedback" id="numberEdit" required>
                            <div id="keywordFeedback" class="invalid-feedback">Hanya Angka yang diperbolehkan</div>
                        </div>
                        <div class="mb-2">
                            <label for="tagEdit" class="form-label">Tag</label>
                            <select id="tagEdit" name="tag[]" class="tag-select-edit form-control" style="width: 100% !important" multiple="multiple" required>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="editContact" name="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addNumber" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="formCreateContact" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="mb-2">
                            <label for="number" class="form-label">Nomor</label>
                            <input type="text" name="number" class="form-control contact-format" aria-describedby="keywordFeedback" id="number" required>
                            <div id="keywordFeedback" class="invalid-feedback">Hanya Angka yang diperbolehkan</div>
                        </div>
                        <div class="mb-2">
                            <label for="tag" class="form-label">Tag</label>
                            <select id="tag" name="tag[]" class="tag-select form-control" style="width: 100% !important" multiple required>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formCreateContact" name="submit" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importContacts" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-2">
                        @if (Storage::exists('excels/failures/'. Auth::id() . '.xlsx' ))
                            <a href="{{ route('contact.download', 'error') }}" class="btn btn-sm btn-danger text-center">error <i class="ms-1 fas fa-download"></i></a>
                        @endif
                        <a href="{{ asset('assets/ex-import-contact.xlsx', true) }}" class="btn btn-sm btn-info text-center">examlple <i class="ms-1 fas fa-download"></i></a>
                    </div>
                    <form action="{{ route('importContacts') }}" id="formImportContact" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <label for="fileContacts" class="form-label">File</label>
                            <input type="file" name="fileContacts" class="form-control" id="fileContacts" required>
                        </div>
                        <div class="mb-2">
                            <label for="tag1" class="form-label">Tag</label>
                            <select id="tag1" name="tag" class="tag-select-import form-control" style="width: 100% !important" required>
                            @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formImportContact" name="submit" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $('.contact-format').keyup(function() {
                var _set = new RegExp('^[0-9]*$');
                if (!_set.test($(this).val())) {
                    $(this).addClass('is-invalid')
                } else {
                    if ($(this).hasClass('is-invalid')) {
                        $(this).removeClass('is-invalid')
                    }
                }
            })

            $('#selectAll').on('change', function () {
                if ($(this).prop('checked')) {
                    $('.delete-checkbox').prop('checked', true);
                } else {
                    $('.delete-checkbox').prop('checked', false);
                }
            })


            $('#deleteChoice').on('click', function(){
                $(this).addClass('visually-hidden');
                $('.action-body').addClass('visually-hidden');
                $('#deletePurpose').removeClass('visually-hidden');
                $('.action-delete').removeClass('visually-hidden');
            })
            
            $('#cancelChoice').on('click', function() {
                $('.delete-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false);
                $('#deleteChoice').removeClass('visually-hidden');
                $('#deletePurpose').addClass('visually-hidden');
                $('.action-delete').addClass('visually-hidden');
                $('.action-body').removeClass('visually-hidden');
            })

            $('.delete-checkbox').on('change', function () {
                if ($('.delete-checkbox:checked').length != $('.delete-checkbox').length) {
                    $('#selectAll').prop('checked', false);
                } else {
                    $('#selectAll').prop('checked', true);
                }
            })

            $('#processChoice').on('click', function () {
                let data = [];
                $('.delete-checkbox').map(function () {
                    if ($(this).prop('checked')) {
                        return data.push($(this).val());
                    }
                })

                if (data.length < 1) {
                    swal.fire({
                        title: "WARNING",
                        text: "Pilih Kontak Yang Ingin Dihapus Terlebih Dahulu",
                        icon: "warning",
                        confirmButtonText: "OK!",
                    })
                } else {
                    swal.fire({
                        title: "Apakah anda yakin ingin menghapus kontak ini ?",
                        text: "aksi ini bersifat permanent",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Lanjutkan!",
                        cancelButtonText: "Tidak, Batalkan!"
                    }).then((willProcess) => {
                        if (willProcess.isConfirmed) {
                            $.ajax({
                                url: "{{ route('contact.destroy') }}",
                                type: 'POST',
                                data: {
                                    'contacts[]': data,
                                    '_method': 'DELETE',
                                },
                                success: function (data) {
                                    swal.fire({
                                        title: "SUKSES",
                                        text: "Kontak Berhasil Dihapus",
                                        icon: "success",
                                        confirmButtonText: "OK!",
                                    }).then(() => {
                                        location.reload();
                                    })
                                },
                                error: function (data) {
                                    console.log(data);
                                    swal.fire({
                                        title: "GAGAL",
                                        text: "Terjadi Kesalahan Di Server",
                                        icon: "error",
                                        confirmButtonText: "OK!",
                                    }).then(() => {
                                        location.reload();
                                    })
                                }
                            })
                        }
                    });
                }
            })

            $("#datatable1 tbody").on('click', '.del-cont', function(e){
                var form =  $(this).closest("form");
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin ingin menghapus kontak ini ?",
                    text: "aksi ini bersifat permanent",
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

            $('.tag-select').select2({
                dropdownParent: $('#addNumber')
            });
            $('.tag-select-import').select2({
                dropdownParent: $('#importContacts')
            });
            $('.tag-select-edit').select2({
                dropdownParent: $('#editContactTag')
            });

            $('.editTagsContact').on('click', function () {

                $.ajax({
                    url: "{{ route('contact.show', ':id') }}".replace(':id', $(this).data('id')),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#nameEdit').val(data.name);
                        $('#numberEdit').val(data.number);
                        $('#editContact').attr('action', "{{ route('contact.update', ':id') }}".replace(':id', data.id));
                        data.tags.length > 0 && data.tags.forEach(function (value, index) {
                            $('.tag-select-edit').find('option[value="' + value.id + '"]').attr('selected', true).trigger('change');
                        });
                    },
                    error: function (data) {
                        console.log(data);
                    }
                })
            })
        });
    </script>
    <script src="{{asset('js/pages/datatables.js')}}"></script>
    <script src="{{asset('plugins/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
</x-app-layout>
