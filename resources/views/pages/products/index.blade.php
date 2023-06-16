<x-app-layout title="products">
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
                <div class="card-header d-flex justify-content-between px-0">
                    <div class="d-flex justify-content-right">
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" id="addModal"
                            data-bs-target="#addProduct">
                            <i class="fas fa-plus-circle"></i>Tambah
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title">Produk</h5>
                            </div>
                            <div class="card-body">
                                <table id="tableProduct" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Judul</th>
                                            <th class="text-center">Gambar</th>
                                            <th class="text-center">Poin</th>
                                            <th class="text-center">Durasi (Bulan)</th>
                                            <th class="text-center">Harga</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $product->title }}
                                                </td>
                                                <td class="text-center">
                                                    <img src="{{ asset('storage/' . $product->image) }}" height="150" width="150" class="img-thumbnail" alt="{{ $product->slug }}">
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">{{ $product->point }}</span>
                                                </td>
                                                <td class="text-center">
                                                    {{ $product->duration }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($product->discount > 0)
                                                        <del>{{ $product->price }}</del>
                                                        @if ($product->discount_type == 'percentage')
                                                            <span class="text-success fw-bold">IDR. {{ number_format($product->price - ($product->price * $product->discount) / 100) }}</span>
                                                            <span class="text-danger bg-discount p-1 rounded">
                                                                {{ $product->discount }}%</span>
                                                        @else
                                                            <span class="text-success fw-bold">IDR. {{ number_format($product->price - $product->discount) }}</span>
                                                            <span
                                                                class="text-danger bg-discount p-1 rounded">-{{ $product->discount }}</span>
                                                        @endif
                                                    @else
                                                        <span class="fw-bold">IDR. {{ number_format($product->price) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input activating-product" type="checkbox" role="switch" data-id="{{ $product->id }}" value="{{ $product->id }}" {{ $product->is_active ? 'checked' : false}}>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <button class="btn btn-sm btn-primary btn-edit" data-id="{{ $product->id }}" data-bs-toggle="modal" data-bs-target="#addProduct">
                                                            Rubah
                                                        </button>
                                                        <button class="btn btn-sm btn-info mx-1 btn-detail" data-id="{{ $product->id }}" data-bs-toggle="modal" data-bs-target="#DetailProduct">
                                                            Detail
                                                        </button>
                                                        <form action='{{ route("product.destroy", $product->id) }}' method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger del-product" type="submit">
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
    <div class="modal fade" id="addProduct" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Produk</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        <div id="patchUpdate"></div>
                        <label for="title" class="form-label">Nama Produk</label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" required>
                        <label for="image" class="form-label">Gambar</label>
                        <input type="file" name="image" class="form-control" id="image">
                        <label for="point" class="form-label">Poin</label>
                        <input type="number" min="0" name="point" class="form-control" id="point" value="{{ old('point') }}" required>
                        <label for="duration" class="form-label">Durasi</label>
                        <input type="number" min="0" name="duration" class="form-control" id="duration" value="{{ old('duration') }}" placeholder="Bulan" required>
                        <label for="price" class="form-label">Harga</label>
                        <input type="number" min="0" name="price" class="form-control" id="price" value="{{ old('price') }}" required>
                        <label for="discount_type" class="form-label">Tipe Diskon</label>
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="discount_type" id="percentage" value="percentage">
                                <label class="form-check-label" for="percentage">Persentase</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="discount_type" id="amount" value="amount">
                                <label class="form-check-label" for="amount">Nominal</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="discount_type" id="none" value="" checked>
                                <label class="form-check-label" for="none">Tidak Ada</label>
                            </div>
                        </div>
                        <input type="number" min="0" name="discount" class="form-control" id="discount" value="{{ old('discount') }}">
                        <label for="summernote" class="form-label">Deskripsi Diskon</label>
                        <textarea class="form-control" name="description" placeholder="the description" id="summernote" style="height: 100px">{{ old('description') ?? null }}</textarea>
                        <div class="modal-footer">
                            <button type="button" id="batal" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="add" name="submit" class="btn btn-primary">Tambah</button>
                            <button type="submit" id="update" name="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DetailProduct" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="detailProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailProductLabel">Detail Product</h5>
                </div>
                <div class="modal-body">
                    <div class="w-100">
                        <div class="d-flex flex-column">
                            <div class="d-flex flex-row">
                                <div id="detailProdPic"></div>
                                <div class="d-flex flex-column mx-4">
                                    <div id="detailProdTitle"></div>
                                    <div id="detailProdPrice"></div>
                                    <div class="d-flex flex-row">
                                        <div id="detailProdDiscount"></div>
                                        <div id="detailProdReal" class="mx-1"></div>
                                    </div>
                                    <div class="d-flex flex-row mt-3">
                                        <div id="detailProdDuration"></div>
                                        <div id="detailProdPoint" class="mx-2"></div>
                                    </div>
                                    <div class="d-flex flex-row mt-2">
                                        <div id="detailProdCreated"></div>
                                        <div id="detailProdUpdated" class="mx-2"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <div id="detailProdDescription"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="batal" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
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
                height: 300
            });

            $("#tableProduct").DataTable({
                responsive: true
            });

            $('input[name="discount"]').hide()
            $('#update').hide()
            
            $('#batal').on('click', function () {
                $('#title').val(null);
                $('#point').val(null);
                $('#duration').val(null);
                $('#price').val(null);
                $('input:radio[name=discount_type]').each(function () {
                    if ($(this).val() != "") {
                        $(this).prop('checked', false);
                    } else {
                        $(this).prop('checked', true);
                    }
                });
                $('#discount').val(null);
                $('#summernote').summernote('code', '');
                $('#add').show();
                $('#update').hide();
                $('#productForm').attr("action", "{{ route('product.store') }}");
                $('#UpdatePatch').remove()
                $('input[name="discount"]').hide()
            });

            $('input[name="discount_type"]').change(function(){
                $('input[name="discount"]').show()
                var discount = $('input[name="discount"]')
                var val = $('input[name=discount_type]:checked').val();
                discount.val(0);
                if (val == 'percentage') {
                    discount.attr("step", ".01");
                    discount.attr("max", "95");
                } else if (val == 'amount') {
                    discount.attr("step", "1");
                    discount.removeAttr("max");
                } else {
                    discount.val(0);
                    $('input[name="discount"]').hide()
                }
            });

            $('.btn-edit').on('click', function() {
                var id = $(this).attr('data-id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("product.show", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('input[name="discount"]').show()
                        $('#title').val(data.title);
                        $('#point').val(data.point);
                        $('#duration').val(data.duration);
                        $('#price').val(data.price);
                        $("input[name=discount_type][value=" + data.discount_type + "]").prop('checked', true);
                        if (data.discount_type == 'percentage') {
                            $('input[name="discount"]').attr("step", ".01");
                            $('input[name="discount"]').attr("max", "95");
                        } else if (data.discount_type == 'amount') {
                            $('input[name="discount"]').attr("step", "1");
                            $('input[name="discount"]').attr("max", data.price);
                        } else {
                            $("input[name=discount_type][id=none]").prop('checked', true);
                            $('input[name="discount"]').val(0);
                            $('input[name="discount"]').hide()
                        }
                        $('#discount').val(data.discount);
                        $('#summernote').summernote('code', data.description);
                        $('#add').hide();
                        $('#update').show();
                        $('#productForm').attr("action", "{{ route('product.update', ":id") }}".replace(':id', id));
                        $('#patchUpdate').html('<input type="hidden" name="_method" value="PATCH" id="UpdatePatch">');

                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            })

            $('.btn-detail').on('click', function() {
                var id = $(this).attr('data-id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("product.show", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var image = '{{ asset("storage/" . ":image")}}'.replace(':image', data.image);
                        $('#detailProdPic').html('<img src='+image+' class="img-thumbnail" height="300" width="300" alt="Product Picture" id="detailProdPic">');
                        $('#detailProdTitle').html('<h2 style="font-weight: 600 !important;">'+data.title+'</h2>');
                        if (data.discount > 0) {
                            if (data.discount_type == 'percentage') {
                                var price = data.price - (data.price * (data.discount / 100))
                                $('#detailProdPrice').html('<h3>Rp. '+moneyFormat(price)+'</h3>');
                                $('#detailProdDiscount').html('<span class="text-danger bg-discount p-1">'+data.discount+'%</span>');
                                $('#detailProdReal').html('<del>Rp. '+moneyFormat(data.price)+'</del>');
                            } else {
                                var price = data.price - data.discount
                                $('#detailProdPrice').html('<h3>Rp. '+moneyFormat(price)+'</h3>');
                                $('#detailProdDiscount').html('<span class="text-danger bg-discount p-1">Rp. '+moneyFormat(data.discount)+'</span>');
                                $('#detailProdReal').html('<del>Rp. '+moneyFormat(data.price)+'</del>');
                            }
                        } else {
                            $('#detailProdPrice').html('<span>Rp. '+moneyFormat(data.price)+'</span>');
                        }
                        $('#detailProdDuration').html('Duration: <span class="badge badge-success fs-6">'+data.duration+' Month(s)</span>');
                        $('#detailProdPoint').html('Points: <span class="badge badge-primary fs-6">'+data.point+'</span>');
                        $('#detailProdCreated').html('Created By: <span style="font-weight: 600 !important;">'+data.created_by.name+'</span>');
                        if (data.updated_by) {
                            $('#detailProdUpdated').html('Updated By: <span style="font-weight: 600 !important;">'+data.updated_by.name+'</span>');
                        }
                        $('#detailProdDescription').html('<p>'+data.description+'</p>');

                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            })

            $('.activating-product').on('change', function() {
                var id = $(this).val();
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("product.activating", ":id") }}'.replace(':id', id);
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

            $('#tableProduct tbody').on('click', '.del-product', function(e) {
                var form =  $(this).closest("form");
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin ingin menghapus produk ini ?",
                    text: "aksi ini bersifat permanent, data yang terkait dengan product ini akan terhapus",
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
