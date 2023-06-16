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
                            <div class="card-header d-flex justify-content-between">
                                <a href="{{ route('users.index') }}">
                                    <i class="material-icons-outlined">arrow_back</i>
                                </a>
                                <h5 class="card-title">History Point</h5>
                                <h5 class="card-title text-primary">{{ $user->name }} - {{ $user->email }}</h5>
                            </div>
                            <div class="card-body">
                                <table id="tablePoint" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Point</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->histories as $key => $history)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $key+1 }}
                                                </td>
                                                <td class="text-center {{ $history->type == '+' ? 'text-success fw-bold' : 'text-danger' }}">
                                                    {{ $history->type }} {{ $history->point }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        @if ($history->historyable_type == 'App\Models\Transaction')
                                                            <button class="btn btn-sm btn-info btnDetailOrder" data-id={{ $history->historyable->invoice }} data-bs-toggle="modal" data-bs-target="#detailOrder">
                                                                Detail
                                                            </button>
                                                        @else
                                                            <button disabled class="btn btn-sm btn-info btnDetailOrder" data-id={{ $history->id }} data-bs-toggle="modal" data-bs-target="#detailOrder">
                                                                Detail
                                                            </button>
                                                        @endif
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

    <div class="modal fade" id="detailOrder" tabindex="-1" aria-labelledby="detailOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailOrderLabel">Detail Order</h5>
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
                                    <div id="detailProdTime" class="mt-3 text-primary"></div>
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
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <div id="payButton"></div>
                    {{-- @if ($item->status == 'pending' && is_null($item->confirmation))
                            <button type="button" class="btn btn-sm btn-primary">Pay</button>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filePayment" tabindex="-1" aria-labelledby="filePaymentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filePaymentLabel">File Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="imagePayment"></div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/pages/datatables.js') }}"></script>
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    <script>
        function moneyFormat(number) {
            let reverse = number.toString().split('').reverse().join(''),
            thousands   = reverse.match(/\d{1,3}/g)
            thousands   = thousands.join('.').split('').reverse().join('')
            return thousands
        }

        $(document).ready(function () {

            $("#tablePoint").DataTable({
                responsive: true
            });

            // show detail order
        $('.btnDetailOrder').on('click', function(){
            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                }
            });

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
                    $('#detailProdTime').html('<h6>'+new Date(data.created_at).toISOString().slice(0, 19).replace('T', ' ')+'</h6>');
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
                    $('#detailProdDescription').html('<h6 class="fw-bold">Descriptions</h6><p>'+history.description+'</p>');

                    if (data.confirmation != null) {
                        if (data.status == 'pending' && data.user_id == '{{ Auth::user()->id }}') {
                            $('#buttonUpdatePayment').html('<button class="btn btn-sm me-1 btn-outline-warning upload-payment" data-invoice='+data.invoice+' data-id='+data.id+' data-bs-toggle="modal" data-bs-target="#paymentOrder"><span class="d-block d-sm-none">Update</span><span class="d-none d-sm-block">Update Payment</span></button>')
                        } else {
                            $('#buttonUpdatePayment').html('');
                        }
                        $('#buttonShowPayment').html('<button class="btn btn-sm ms-1 btn-primary" data-bs-toggle="modal" data-bs-target="#filePayment"><span class="d-block d-sm-none">Show</span><span class="d-none d-sm-block">Show File Payment</span></button>');
                        let confirmation = '{{ asset("storage/" . ":image", true) }}'.replace(':image', data.confirmation);
                        $('#imagePayment').html('<img src='+confirmation+' class="img-thumbnail" height="375" width="375" alt="Payment Image">');
                    } else {
                        $('#buttonUpdatePayment').html('');
                        $('#buttonShowPayment').html('');
                        $('#imagePayment').html('');
                    }

                    if (data.status == 'cancelled' && data.cancelled_reason != null) {
                        $('#rejectReason').html(`
                            <div class="card-body border border-danger rounded">
                                <h6 class="fw-bold text-danger mb-3">Cancelled Reason</h6>
                                <p>${data.cancelled_reason}</p>
                            </div>
                        `);
                    } else {
                        $('#rejectReason').html('');
                    }
                },
                error: function(err) {
                    console.log(err);
                }
                });
            })
        });
    </script>
</x-app-layout>
