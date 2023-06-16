<x-index-layout>
    <div class="flex flex-col sm:flex-row justify-between sm:justify-center items-center px-3 pt-6 sm:pt-0 bg-gray-100 overflow-auto">
        <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-2 gap-0 lg:gap-x-6">
            @foreach ($products as $product)
                <div class="my-6 sm:mx-5 mx-1 px-3 bg-white shadow-md border border-green-500 hover:shadow-lg hover:shadow-green-500/50 rounded-lg relative">
                    <div class="p-1 sm:p-4 w-full">
                        <div class="flex flex-col">
                            <a href="{{  route('buypoint.detail', $product->slug) }}" class="">
                                <h2 class="text-ellipsis text-center mb-3 font-semibold">{{ $product->title }}</h2>
                                <div class="flex-shrink-0 content-center">
                                    <img class="rounded lg:h-48 lg:w-48" src="{{ asset('storage/' . $product->image, true) }}" alt="Product Image">
                                </div>
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
                                <h6 class="bg-green-400 w-full sm:text-md text-xs mt-1 text-white p-0.5 rounded text-center">{{ $product->point }} Points <span class="font-extrabold text-white">/</span> {{ $product->duration }} Month(s)</h6>
                            </a>
                            <div class="mt-16">
                                <div class="absolute inset-x-0 bottom-0 p-4">
                                    <hr class="my-3">
                                    @auth
                                        <a href="{{ route('buypoint.detail', $product->slug) }}" data-product={{ $product->slug }} data-set="buy" class="mt-1 w-full border border-green-500 hover:border-green-300 text-green-500 hover:bg-green-200 hover:text-black rounded shadow-md py-2 px-4 text-sm leading-5 font-semibold uppercase text-center block">
                                            BUY
                                        </a>
                                    @endauth
                                    @guest
                                        <a href="{{ route('login') }}" class="mt-1 w-full border border-green-500 hover:border-green-300 text-green-500 hover:bg-green-200 hover:text-black rounded shadow-md py-2 px-4 text-sm leading-5 font-semibold uppercase text-center block">
                                            BUY
                                        </a>
                                    @endguest
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('a[data-set="buy"]').on('click', function (e) {
                localStorage.setItem('buyProduct', $(this).attr('data-product'));
            });
        });
    </script>
</x-index-layout>