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
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column justify-content-center">
                                    <h1 class="text-primary text-center fw-bold">
                                        {{ number_format($point->point) }}
                                    </h1>
                                    <h5 class="text-center fw-bold {{ Carbon\Carbon::parse($point->expired_at)->isPast() ? 'text-danger' : 'text-success' }} mt-3">
                                        {{ Carbon\Carbon::parse($point->expired_at)->isoFormat('MMMM Do YYYY, hh:mm a') }} ({{ Carbon\Carbon::parse($point->expired_at)->isPast() ? 'EXPIRED' : Carbon\Carbon::parse($point->expired_at)->diffForHumans() }})
                                    </h5>
                                </div>
                                <button class="btn btn-sm btn-primary d-flex float-start" id="showHistory">
                                    <span class="d-none d-md-block">Point History</span> <i class="material-icons-outlined" style="margin-left: .1px; margin-right: 0px;">arrow_downward</i>
                                </button>
                            </div>
                            <div class="d-none text-center" id="listHistory"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="DetailHistory" tabindex="-1" aria-labelledby="DetailHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DetailHistoryLabel">Detail Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="w-100">
                        <div class="d-flex flex-column">
                            <div class="d-flex flex-row">
                                <div class="d-flex flex-column">
                                    <div id="detailProdPic" class="text-center"></div>
                                    <div class="d-flex mt-3 justify-content-between" id="buttonAfterUpload">
                                        <div id="buttonUpdatePayment"></div>
                                        <div id="buttonShowPayment"></div>
                                    </div>
                                </div>
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
                                    <hr>
                                    <h5 class="mt-2">Payment Details </h5>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Price : </h6>
                                            <div id="productPrice"></div>
                                        </div>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Discount : </h6>
                                            <div id="productDiscount"></div>
                                        </div>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Fee : </h6>
                                            <div id="productFee"></div>
                                        </div>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Payment Code : </h6>
                                            <div id="productCode"></div>
                                        </div>
                                        <hr>
                                        <div class="d-flex flex-row justify-content-between">
                                            <h6>Total : </h6>
                                            <div id="productTotal"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex flex-row justify-content-evently">
                                        <div id="bankImage"></div>
                                        <div class="d-flex flex-column ms-2">
                                            <div id="bankName"></div>
                                            <div id="bankNumber"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2" id="rejectReason">
                            </div>
                            <div class="card mt-2">
                                <div class="card-body">
                                    <div id="detailProdDescription"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        function moneyFormat(number) {
            let reverse = number.toString().split('').reverse().join(''),
            thousands   = reverse.match(/\d{1,3}/g)
            thousands   = thousands.join('.').split('').reverse().join('')
            return thousands
        }
            // show detail order
            $(document).on('click', '.btn-detail', function(){

                $.ajax({
                    url: '{{ route("transaction.show", ":id") }}'.replace(':id', $(this).attr('data-id')),
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var data = data.transaction
                        var history = data.product
                        var image = '{{ asset("storage/" . ":image")}}'.replace(':image', history.image);
                        $('#detailProdPic').html('<img src='+image+' class="img-thumbnail" height="300" width="300" alt="Product Picture" id="detailProdPic">');
                        $('#detailProdTitle').html('<h2 style="font-weight: 600 !important;">'+history.title+'</h2>');
                        $('#productPrice').html('<h6>'+moneyFormat(history.price)+'</h6>');
                        if (history.discount > 0) {
                            if (history.discount_type == 'percentage') {
                                var price = history.price - (history.price * (history.discount / 100))
                                $('#detailProdPrice').html('<h3>Rp. '+moneyFormat(price)+'</h3>');
                                $('#detailProdDiscount').html('<span class="text-danger bg-discount p-1">'+history.discount+'%</span>');
                                $('#detailProdReal').html('<del>Rp. '+moneyFormat(history.price)+'</del>');

                                $('#productDiscount').html('<h6>- '+moneyFormat((history.price * (history.discount / 100)))+'</h6>');
                            } else {
                                var price = history.price - history.discount
                                $('#detailProdPrice').html('<h3>Rp. '+moneyFormat(price)+'</h3>');
                                $('#detailProdDiscount').html('<span class="text-danger bg-discount p-1">Rp. '+moneyFormat(history.discount)+'</span>');
                                $('#detailProdReal').html('<del>Rp. '+moneyFormat(history.price)+'</del>');

                                $('#productDiscount').html('<h6>- '+moneyFormat(history.discount)+'</h6>');
                            }
                        } else {
                            $('#detailProdPrice').html('<span>Rp. '+moneyFormat(history.price)+'</span>');

                            $('#productPrice').html('<h6>'+moneyFormat(0)+'</h6>');
                            $('#productDiscount').html('<h6>'+moneyFormat(0)+'</h6>');
                        }
                            $('#productFee').html('<h6>'+data.fee+'</h6>');
                            $('#productCode').html('<h6>'+data.payment_code+'</h6>');
                            $('#productTotal').html('<h5 class="fw-bold">'+moneyFormat(data.grand_total)+'</h5>');
                            if (data.status == 'pending' && data.confirmation == null) {
                                $('#payButton').html('<button class="btn btn-sm btn-primary upload-payment" data-invoice='+data.invoice+' data-id='+data.id+' data-bs-toggle="modal" data-bs-target="#paymentOrder">Upload Payment</button>');
                            } else {
                                $('#payButton').html('');
                            }
                            var bankImage = '{{ asset(":image")}}'.replace(':image', data.bank.bank.image);
                            $('#bankImage').html('<img src='+bankImage+' class="img-thumbnail" height="150" width="150" alt='+data.bank.bank.name+'>');
                            $('#bankName').html('<h6>'+data.bank.account_name+'</h6>');
                            $('#bankNumber').html('<h5 class="fw-bold">'+data.bank.account_number+'</h5>');
                            $('#detailProdDuration').html('Duration: <span class="badge badge-success fs-6">'+history.duration+' Month(s)</span>');
                            $('#detailProdPoint').html('Points: <span class="badge badge-primary fs-6">'+history.point+'</span>');
                            $('#detailProdDescription').html('<h6 class="fw-bold">Descriptions</h6><p>'+history.description+'</p>')
                        },
                        error: function(err) {
                            console.log(err);
                        }
                });
            })

        $(document).ready(function() {

            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                }
            });

            $('#showHistory').click('click', function() {
                $('#listHistory').toggleClass('d-none');

                if (!$('#listHistory').hasClass('d-none') && $('#listHistory').html() == '') {
                    $('#listHistory').html('<div class="p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>')
                    $.ajax({
                    url: '{{ route("point.index") }}',
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#listHistory').html(data)
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>
