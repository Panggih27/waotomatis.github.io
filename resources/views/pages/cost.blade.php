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
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title">Tarif/Biaya   ||  <span class="fs-6">Point Default : {{ env('DEFAULT_POINT') }}</span></h5>
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal" id="addModal"
                                    data-bs-target="#addPointCost">
                                    <i class="material-icons-outlined">add</i>Tambah
                                </button>
                            </div>
                            <div class="card-body">
                                <table id="tableCost" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Poin</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($costs as $cost)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $cost->name }}
                                                </td>
                                                <td class="text-center fw-bold text-primary">
                                                    {{ $cost->point }}
                                                </td>
                                                <td class="text-center nowrap">
                                                    {!! $cost->description !!}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <button class="btn btn-sm btn-primary btn-edit me-1" data-id="{{ $cost->id }}" data-bs-toggle="modal" data-bs-target="#addPointCost">
                                                            Rubah
                                                        </button>
                                                        {{-- <button class="btn btn-sm btn-info mx-1 btn-detail" data-id="{{ $cost->id }}" data-bs-toggle="modal" data-bs-target="#detailCost">
                                                            Detail
                                                            <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span>
                                                        </button> --}}
                                                        <form action='{{ route("cost.destroy", $cost->id) }}' method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger delete-cost" type="submit">
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
    <div class="modal fade" id="addPointCost" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <form action="{{ route('cost.store') }}" method="POST" id="costForm">
                        @csrf
                        <div id="patchUpdate"></div>
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
                        <label for="point" class="form-label">Poin</label>
                        <input type="number" min="0" name="point" class="form-control" id="point" value="{{ old('point') }}" required>
                        <label for="summernote" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" placeholder="the description" id="summernote" style="height: 100px">{{ old('description') ?? null }}</textarea>
                        <div class="modal-footer">
                            <button type="button" id="batal" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="add" name="create" class="btn btn-primary">Tambah</button>
                            <button type="submit" id="update" name="update" class="btn btn-primary">Rubah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="{{ asset('assets/summernote/summernote-lite.min.css') }}">
    <script src="{{ asset('js/pages/datatables.js') }}"></script>
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/summernote/summernote-lite.min.js') }}"></script>
    <script>
        

        function moneyFormat(number) {
            let reverse = number.toString().split('').reverse().join(''),
            thousands   = reverse.match(/\d{1,3}/g)
            thousands   = thousands.join('.').split('').reverse().join('')
            return thousands
        }

        $(document).ready(function () {

            $('#summernote').summernote({
                toolbar : [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ],
                height: 150
            });

            $("#tableCost").DataTable({
                columnDefs: [
                    { width: "40%", targets: 2 }
                ],
                responsive: true
            });
            
            $('#update').hide()
            
            $('#batal').on('click', function () {
                $('#name').val(null);
                $('#point').val(null);
                $('#summernote').summernote('code', '');
                $('#add').show();
                $('#update').hide();
                $('#costForm').attr("action", "{{ route('cost.store') }}");
                $('#UpdatePatch').remove()
                $('#name').attr('readonly', false);
                $('#update').attr("data-id", "");
            });

            $('.btn-edit').on('click', function() {
                var id = $(this).attr('data-id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("cost.show", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#name').attr('readonly', true);
                        $('#name').val(data.name);
                        $('#point').val(data.point);
                        $('#summernote').summernote('code', data.description);
                        $('#add').hide();
                        $('#update').show();
                        $('#costForm').attr("action", "{{ route('cost.update', ":id") }}".replace(':id', id));
                        $('#update').attr("data-id", data.id);
                        $('#patchUpdate').html('<input type="hidden" name="_method" value="PATCH" id="UpdatePatch">');

                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            })

            $('#update').on('click', function (e) {
                var form = $('#costForm');
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin akan memperbarui ini ?",
                    text: "Kemungkinan akan berefek pada kampanya yang sudah dibuat",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Lanjutkan!",
                    cancelButtonText: "Tidak, Batalkan!"
                }).then((willProcess) => {
                    if (willProcess.isConfirmed) {
                        console.log(form.submit());
                        form.submit();
                    }
                });
            });

            $('.delete-cost').on('click', function (e) {
                var form = $(this).closest('form');
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin akan menghapus ini ?",
                    text: "jika terdapat kampanye yang menggunakan fitur ini, maka point yang dikenakan akan menggunakan point default",
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

        });
    </script>
</x-app-layout>