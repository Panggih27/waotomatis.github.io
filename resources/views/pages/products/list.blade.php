<x-app-layout title="{{ __('Beli Poin') }}">

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
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title">Beli Poin</h5>
                    </div>
                    <div class="card-body">
                        <hr/>
                        <div class="row">
                            @foreach ($products as $product)
                            <div class="card col-md-4 pt-3 m-r-sm">
                                <a href="{{  route('buypoint.detail', $product->slug) }}">
                                <img class="card-img-top" src="{{ asset('storage/' . $product->image, true) }}" alt="{{ $product->title }}">
                                </a>
                                <div class="card-body">
                                    <a class="text-decoration-none" href="{{  route('buypoint.detail', $product->slug) }}">
                                        <h2 class="text-dark text-center">{{ $product->title }}</h2>
                                    </a>
                                    <p class="card-text">
                                    <h6 class="card-subtitle mb-2 text-muted text-center">{{ $product->point }} Poin / {{ $product->duration }}Bulan</h6>
                                    @if ($product->discount > 0)
                                        <h1 class="text-center text-red-600 line-through mt-1 md:text-md text-sm italic">IDR. {{ number_format($product->price) }}</h1>
                                        <div class="flex justify-evenly items-center">
                                            @if ($product->discount_type == 'percentage')
                                                <h1 class="text-center text-green-600 font-bold mt-1 text-sm md:text-lg">IDR. {{ number_format($product->price - ($product->price * $product->discount / 100)) }}</h1>
                                                <span class="text-red-700 md:tex-normal text-xs bg-red-200 p-0.5 rounded">{{ $product->discount }}%</span>
                                            @else
                                                <h1 class="text-center text-green-600 font-bold mt-1 text-sm md:text-lg">IDR. {{ number_format($product->price - $product->discount) }}</h1>
                                                <span class="text-red-700 md:tex-normal text-xs bg-red-200 p-0.5 rounded">-{{ number_format($product->discount) }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <h1 class="text-center text-black font-bold mt-1 text-sm md:text-lg">IDR. {{ number_format($product->price) }}</h1>
                                    @endif
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="{{ route('buypoint.detail', $product->slug) }}" data-product={{ $product->slug }} data-set="buy" class="btn btn-lg btn-primary">
                                            BUY
                                        </a>
                                    </div>
                                    </p>
                                </div>
                                </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="newtransaction" role="tabpanel" aria-labelledby="account-tab">
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


</x-app-layout>
<script>
    $(document).ready(function() {
        $('a[data-set="buy"]').on('click', function (e) {
            localStorage.setItem('buyProduct', $(this).attr('data-product'));
        });
    });
</script>
