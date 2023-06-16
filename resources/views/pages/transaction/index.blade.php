<x-app-layout title="Transaksi">

    <div class="app-content">
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
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title">Transaksi</h5>
                                <hr/>
                                <div class="pt-0 mb-0 page-description page-description-tabbed tab-color">
                                    <ul class="mt-0 nav nav-tabs mb-3" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link nav-transaction active" data-ajax="point" id="products-tab" type="button" data-bs-target="#products">
                                                Daftar Poin
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link nav-transaction" data-ajax="new" type="button">
                                                Transaksi Baru
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link nav-transaction position-relative" id="nav-pending" data-ajax="pending" type="button">
                                                Menunggu Konfirmasi
                                                {!! $pending ? '<span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                                    <span class="visually-hidden">Peringatan baru</span>
                                                </span>' : '' !!}
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link nav-transaction" data-ajax="paid" type="button">
                                                Selesai
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link nav-transaction" data-ajax="cancelled" type="button">
                                                Batal
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                                        <div class="example-content mt-1">
                                            <div class="row g-2">
                                                @foreach ($products as $key => $item)
                                                    <div class="col-md-6">
                                                        <div class="card border mb-2 overflow-hidden h-100">
                                                            <div class="card-body d-flex justify-content-between align-items-center">
                                                                <div class="d-flex justify-content-start align-items-center">
                                                                    <div class="d-flex flex-column">
                                                                        <div class="flex-shrink-0">
                                                                            <img src="{{ asset('storage/' . $item->image, true) }}"
                                                                            class="img-fluid"
                                                                            style="width: 120px;border: 1px solid #ebebeb;border-radius: 5px;"
                                                                            alt="{{ $item->slug }}">
                                                                        </div>
                                                                        <button type="button" class="btn btn-sm btn-outline-info mt-2" data-bs-toggle="modal" data-bs-target="#exModal{{ $key }}">
                                                                            Deskripsi
                                                                        </button>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h5 class="ms-md-4 ms-1 text-nowrap overflow-hidden">
                                                                            {{ $item->title }}
                                                                        </h5>
                                                                        <div class="ms-md-4 ms-1 mt-1 d-flex flex-row">
                                                                            <span style="font-size: .9rem !important;"
                                                                                class="border p-1 border-success rounded-start">
                                                                                <span class="fw-bold">{{ $item->duration }}</span> Bulan
                                                                            </span>
                                                                            <span style="font-size: .9rem !important;"
                                                                                class="border p-1 border-success rounded-end">
                                                                                <span class="fw-bold">{{ number_format($item->point) }}</span> Point
                                                                            </span>
                                                                        </div>
                                                                        <div class="ms-md-4 ms-1 mt-1 d-flex">
                                                                            @if ($item->discount > 0)
                                                                                <div class="d-flex flex-column">
                                                                                    <div class="d-flex">
                                                                                        <del class="me-1">{{ $item->price }}</del>
                                                                                        @if ($item->discount_type == 'percentage')
                                                                                            <span class="text-success fw-bold">IDR. {{ number_format($item->price - ($item->price * $item->discount) / 100) }}</span>
                                                                                        @else
                                                                                            <span class="text-success fw-bold">IDR. {{ number_format($item->price - $item->discount) }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <button class="btn btn-outline-danger btn-sm disabled fw-bold">
                                                                                        {{ $item->discount_type == 'percentage' ? $item->discount . '%' : '-' . number_format($item->discount)}}
                                                                                    </button>
                                                                                </div>
                                                                            @else
                                                                                <span class="text-success fw-bold">IDR. {{ number_format($item->price) }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end ms-auto mt-3 mt-md-0">
                                                                    <div class="d-flex d-md-block align-items-center">
                                                                        <div class="d-flex flex-column" role="group">
                                                                            <button class="btn btn-primary btnBuyPoint" data-slug={{ $item->slug }} data-bs-toggle="modal" data-bs-target="#buyPoint">
                                                                                BELI
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal fade" id="exModal{{ $key }}" tabindex="-1" aria-labelledby="exModal{{ $key }}Label" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exModal{{ $key }}Label">{{ $item->title }}</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            {!! $item->description !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="newtransaction" role="tabpanel" aria-labelledby="account-tab">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="example-container">
                                                    <div class="text-center p-4">
                                                        <div class="spinner-border text-info" role="status"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="buyPoint" tabindex="-1" aria-labelledby="buyPointLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="buyPointLabel">Detail Produk Poin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="w-100">
                        <div class="d-flex flex-column">
                            <div class="d-flex flex-row justify-content-between">
                                <div class="d-flex flex-column">
                                    <div id="detailProdPic" class="text-center"></div>
                                    <div class="d-flex mt-3 justify-content-between" id="buttonAfterUpload">
                                        <div id="buttonUpdatePayment"></div>
                                        <div id="buttonShowPayment"></div>
                                    </div>
                                    <div class="mt-2">
                                        <div id="detailProdTitle"></div>
                                        <div class="d-flex flex-row">
                                            <div id="detailProdDiscount"></div>
                                            <div id="detailProdReal" class="mx-1"></div>
                                        </div>
                                        <div class="d-flex flex-row mt-1">
                                            <div id="detailProdDuration"></div>
                                            <div id="detailProdPoint" class="mx-2"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column mx-4 w-75">
                                    <h5 class="mt-2">Detail Pembayaran </h5>
                                    <hr>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Harga : </h6>
                                            <div id="productPrice"></div>
                                        </div>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Diskon : </h6>
                                            <div id="productDiscount"></div>
                                        </div>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Fee : </h6>
                                            <div id="productFee"></div>
                                        </div>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Kode Pembayaran : </h6>
                                            <div id="productCode"></div>
                                        </div>
                                        <hr>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Total : </h6>
                                            <div id="productTotal"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex flex-row justify-content-evently w-100">
                                        <form action="{{ route('transaction.store') }}" method="POST" id="transaction-form">
                                            @csrf
                                            <div class="row">
                                                @foreach($banks as $key => $bank)
                                                    <div class="col-4">
                                                        <div class="mb-3 p-3 rounded border">
                                                            <div class="form-check">
                                                                <input class="form-check-input radio-bank" type="radio" name="bank" id="{{ $bank->bank->name }}" value="{{ $bank->id }}" {{ $key == 0 ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="{{ $bank->bank->name }}">
                                                                    <img src="{{ asset($bank->bank->image, true) }}" width="100" alt="{{ $bank->bank->name }}">
                                                                </label>
                                                            </div>
                                                            <div class="detail-bank mt-1 {{ ($key > 0) ? "visually-hidden" : "" }}" data-bank="{{ $bank->id }}">
                                                                <p class="text-sm my-1">Nama : <span class="fw-bold">{{ $bank->account_name }}</span></p>
                                                                <p class="text-sm my-1">Nomor Rekening : <span class="fw-bold">{{ $bank->account_number }}</span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <input type="hidden" name="payment_code" value="">
                                                <input type="hidden" name="product" value="">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border">
                    <button class="btn btn-primary" type="submit" form="transaction-form">BELI</button>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
<script>
    $(document).ready(function(){
        function moneyFormat(number) {
            let reverse = number.toString().split('').reverse().join(''),
            thousands   = reverse.match(/\d{1,3}/g)
            thousands   = thousands.join('.').split('').reverse().join('')
            return thousands
        }

        $('.radio-bank').click(function() {
            $('.detail-bank').addClass('visually-hidden');
            $('.detail-bank[data-bank='+$(this).val()+']').removeClass('visually-hidden');
        });

        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
            }
        });

        $('.nav-transaction').on('click', function(){
            typeAjax = $(this).attr('data-ajax')

            $('.nav-transaction').removeClass('active')
            $(this).addClass('active')
            $('.example-container').html('<div class="text-center p-4"><div class="spinner-border text-info" role="status"></div></div>')

            if (typeAjax != 'point') {
                if ($('#products').hasClass('show active')) {
                    $('#products').removeClass('show active')
                }
                $('#newtransaction').addClass('show active')

                $.ajax({
                    url: '{{ route("transaction.index") }}' + '?type=' + typeAjax,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data.res.length > 0) {
                            $('.example-container').html(data.res)
                        } else {
                            $('.example-container').html('<div class="text-center py-3 font-bold fs-4">Transaksi Masih Kosong</div>')
                        }

                        // if (data.pending) {
                        //     $('#nav-pending').html(`Menunggu Konfirmasi
                        //         <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                        //             <span class="visually-hidden">Peringatan baru</span>
                        //         </span>`)
                        // } else {
                        //     $('#nav-pending').html('Menunggu Konfirmasi');
                        // }
                    },
                    error: function(err) {
                        $('.example-container').html('<div class="text-center py-3 font-bold fs-4 text-danger">Terjadi kesalahan, silahkan muat ulang halaman!</div>')
                    }
                });
            } else {
                $('#products').addClass('show active')
                if ($('#newtransaction').hasClass('show active')) {
                    $('#newtransaction').removeClass('show active')
                }
            }
        })


        // show detail order
        $('.btnBuyPoint').on('click', function(){

            $.ajax({
                url: '{{ route("buypoint.detail", ":slug") }}'.replace(':slug', $(this).attr('data-slug')),
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let product = data.product;
                    var image = '{{ asset("storage/" . ":image")}}'.replace(':image', product.image);
                    $('input[name="payment_code"]').val(data.code)
                    $('input[name="product"]').val(product.id)
                    $('#detailProdPic').html('<img src='+image+' class="img-thumbnail" height="300" width="300" alt="Product Picture" id="detailProdPic">');
                    $('#detailProdTitle').html('<h2 style="font-weight: 600 !important;">'+product.title+'</h2>');
                    $('#productPrice').html('<h5>'+moneyFormat(product.price)+'</h5>');
                    $('#productFee').html('<h5>'+moneyFormat(0)+'</h5>');
                    $('#productCode').html('<h5>'+moneyFormat(data.code)+'</h5>');
                    let discount = 0;
                    if (product.discount > 0) {
                        if (product.discount_type == 'percentage') {
                            discount = (product.price * (product.discount / 100));
                        } else {
                            discount = product.discount;
                        }
                    }
                    $('#productDiscount').html('<h5>'+ (discount > 0 ? "-" : "") + moneyFormat(discount)+'</h5>');
                    $('#detailProdDuration').html('Duration: <span class="badge badge-success fs-6">'+product.duration+' Month(s)</span>');
                    $('#detailProdPoint').html('Points: <span class="badge badge-primary fs-6">'+product.point+'</span>');
                    $('#productTotal').html('<h5>'+moneyFormat(data.code + product.price - discount)+'</h5>');
                },
                error: function(err) {
                    console.log(err);
                }
            });
        })
    })
</script>
