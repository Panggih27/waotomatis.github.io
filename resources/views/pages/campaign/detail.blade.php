<x-app-layout title="{{ $campaign->title }}">
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
                                <h5 class="card-title">Detail Campaigns - {{ $campaign->title }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 col-sm-3">
                                        <div class="card" style="background: rgb(2,0,36);background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(34,105,244,1) 0%, rgba(231,236,248,1) 95%);">
                                            <div class="p-2 px-4">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <div class="d-flex flex-column">
                                                        <h3 class="text-white">{{ $recipient }}</h3>
                                                        <h5 class="mt-2 fs-5 fs-sm-1 text-white">Total Recipient</h5>
                                                    </div>
                                                    <i class="align-self-center text-primary fas fa-2x fa-envelope-open"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="card" style="background: rgb(2,0,36);background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(255,149,0,1) 0%, rgba(231,236,248,1) 95%);">
                                            <div class="p-2 px-4">
                                                <div class="d-flex flex-row justify-content-between text-warning">
                                                    <div class="d-flex flex-column">
                                                        <h3 class="text-white">{{ $pending }}</h3>
                                                        <h5 class="mt-2 text-white">Pendings</h5>
                                                    </div>
                                                    <i class="align-self-center text-warning fas fa-2x fa-spinner"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="card" style="background: rgb(2,0,36);background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(75,173,72,1) 0%, rgba(231,236,248,1) 95%);">
                                            <div class="p-2 px-4">
                                                <div class="d-flex flex-row justify-content-between text-warning">
                                                    <div class="d-flex flex-column">
                                                        <h3 class="text-white">{{ $success }}</h3>
                                                        <h5 class="mt-2 text-white">Success</h5>
                                                    </div>
                                                    <i class="align-self-center text-success fas fa-2x fa-check"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="card" style="background: rgb(2,0,36);background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(255,72,87,1) 0%, rgba(231,236,248,1) 95%);">
                                            <div class="p-2 px-4">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <div class="d-flex flex-column">
                                                        <h3 class="text-white">{{ $failed }}</h3>
                                                        <h5 class="mt-2 text-white">Failed</h5>
                                                    </div>
                                                    <i class="align-self-center text-danger fas fa-2x fa-times"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h6>Broadcast Point : {{ $broadcast_point }}</h6>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-info" id="showHistory">
                                    Show History
                                </button>
                            </div>
                        </div>
                        <div class="card mt-3" id="card-history">
                            <div id="listHistory" class="p-3"></div>
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

            $('#card-history').addClass('d-none');

            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                }
            });

            $('#showHistory').click('click', function() {
                // $('#listHistory').toggleClass('d-none');
                $('#card-history').toggleClass('d-none');

                if ($('#listHistory').html() == '') {
                    $('#listHistory').html('<div class="p-5 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>')
                    $.ajax({
                        url: '{{ route("campaign.history", ":id") }}'.replace(':id', '{{ $campaign->id }}'),
                        type: "GET",
                        dataType: 'html',
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
