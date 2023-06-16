<div class="example-content">
    @foreach ($transactions as $item)
        <div class="card border mb-3">
            <div class="card-header bg-white d-block d-md-flex justify-content-between align-items-center">
                <div>
                    <img src="{{ asset('assets/icons/bag.svg', true) }}"
                        alt="{{ $item->product->slug }}">
                    <span class="font-bold ml-2 text-wrap"
                        style="font-size: .8rem !important;">{{ $item->invoice }} -
                        {{ Carbon\Carbon::parse($item->created_at)->isoFormat('MMMM Do YYYY, h:mm a') }}</span>
                </div>
                <a href="#" class="text-blue float-md-end fw-bold text-decoration-none">
                    Invoice
                </a>
            </div>
            <div class="card-body d-block d-md-flex justify-content-between align-items-center py-4">
                <div class="d-flex justify-content-start align-items-center">
                    <img src="{{ asset('storage/' . $item->product->image, true) }}"
                        class="img-fluid"
                        style="width: 80px;border: 1px solid #ebebeb;border-radius: 5px;"
                        alt="{{ $item->product->slug }}">
                    <div>
                        <h5 class="ms-md-4 ms-1 mt-1 text-nowrap overflow-hidden">
                            {{ $item->product->title }}
                        </h5>
                        <div class="ms-md-4 ms-1 mt-1 d-flex flex-row">
                            <span style="font-size: .8rem !important;"
                                class="border p-1 border-success rounded-start">
                                {{ $item->product->duration . ' Bulan' }}
                            </span>
                            <span style="font-size: .8rem !important;"
                                class="border p-1 border-success rounded-end">
                                {{ $item->product->point . ' Point' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-end ms-auto mt-3 mt-md-0">
                    <div class="d-flex d-md-block align-items-center">
                        <span class="total-payment">Total</span>
                        <h4 class="price ms-auto mb-0">IDR. {{ number_format($item->grand_total) }}</h4>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white p-2">
                <div class="d-flex">
                    <button class="btn btn-sm btn-outline-secondary btnDetailOrder" data-id={{ $item->invoice }} data-bs-toggle="modal" data-bs-target="#detailOrder">
                        Detail Pesanan
                    </button>
                    <div class="ms-auto">
                        @if ($item->status == 'pending')
                            @if (is_null($item->confirmation))
                                <button class="btn btn-sm btn-outline-warning me-2 modalReject" data-invoice="{{ $item->invoice }}" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#transactionReject">
                                    Batal
                                </button>
                                @if ($item->user->is(Auth::user()))
                                    <button class="btn btn-sm btn-outline-primary mt-3 mt-sm-0 upload-payment" data-invoice="{{ $item->invoice }}" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#paymentOrder">
                                        Bukti Transfer
                                    </button>
                                @endif
                            @else
                                @can('transaction-update')
                                    <div class="d-flex justify-content-evently">
                                        <button class="btn btn-sm btn-outline-danger me-2 modalReject" data-invoice="{{ $item->invoice }}" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#transactionReject">
                                            Tolak Pembayaran
                                        </button>
                                        <form action="{{ route('transaction.status', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="btn btn-sm btn-outline-success acceptPayment" data-invoice="{{ $item->invoice }}" data-id="{{ $item->id }}">
                                                Terima Pembayaran
                                            </button>
                                        </form>
                                    </div>
                                @endcan
                            @endif
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

    <div class="modal fade" id="detailOrder" tabindex="-1" aria-labelledby="detailOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailOrderLabel">Detail Pesanan</h5>
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
                                    <h5 class="mt-2">Detail Pembayaran </h5>
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
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <div id="payButton"></div>
                    {{-- @if ($item->status == 'pending' && is_null($item->confirmation))
                            <button type="button" class="btn btn-sm btn-primary">Pay</button>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentOrder" tabindex="-1" aria-labelledby="paymentOrderLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentOrderLabel">Upload Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="formConfirmation" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" value="PATCH">
                        <div id="forInvoice"></div>
                        <div class="mt-2">
                            <label for="formFileSm" class="form-label">File Payment</label>
                            <input type="file" class="form-control form-control-sm" name="confirmation" id="formFileSm" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="formConfirmation" class="btn btn-sm btn-primary">Submit</button>
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

    <div class="modal fade" id="transactionReject" tabindex="-1" aria-labelledby="transactionRejectLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionRejectLabel">Batalkan Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" id="formReject" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="status" value="cancelled">
                        <div class="mt-0">
                            <label for="reject" class="form-label">Alasan</label>
                            <textarea class="form-control" id="reject" rows="3" name="reason" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" id="submitFormReject" form="formReject" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    @can('transaction-update')
        <script>
            $(document).ready(function(){

                // reject payment
                $('.modalReject').on('click', function(){
                    let id = $(this).data('id');
                    $('#formReject').attr("action", "{{ route('transaction.status', ':id') }}".replace(':id', id));
                })

                // accept payment
                $('.acceptPayment').click(function(e){
                    var form =  $(this).closest("form");
                    e.preventDefault();
                    swal.fire({
                        title: "Are you sure you want to process this invoice ?",
                        text: "this will change the status to 'PAID'",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Process It!",
                    }).then((willProcess) => {
                        if (willProcess.isConfirmed) {
                            form.submit();
                        }
                    });
                })
            })
        </script>
    @else
        <script>
            $(document).ready(function(){
                $('.modalReject').on('click', function(){
                    let id = $(this).data('id');
                    $('#formReject').attr("action", "{{ route('transaction.cancel', ':id') }}".replace(':id', id));
                })
            })
        </script>
    @endcan

<script>

    function moneyFormat(number) {
        let reverse = number.toString().split('').reverse().join(''),
        thousands   = reverse.match(/\d{1,3}/g)
        thousands   = thousands.join('.').split('').reverse().join('')
        return thousands
    }

    $(document).on('click', '.upload-payment', function(){
        let id = $(this).data('id');
        let invoice = $(this).data('invoice');
        $('#forInvoice').html('INVOICE : <span class="fw-bold text-primary">' + invoice + '</span>');
        $('#formConfirmation').attr('action', "{{ route('transaction.confirmation', ':id') }}".replace(':id', id));
    })

    $(document).ready(function(){

        // form reject
        $('#submitFormReject').click(function(e){
            e.preventDefault();
            let form = $('#formReject');
            swal.fire({
                title: "Are you sure you want to process this invoice ?",
                text: "this will CANCEL the transaction",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Process It!",
            }).then((willProcess) => {
                if (willProcess.isConfirmed) {
                    form.submit();
                }
            });
        })
        
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
    })
</script>
