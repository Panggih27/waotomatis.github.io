<x-index-layout>
    <div class="bg-gray-100">
        <div class="md:container mx-auto">
            <div class="items-center px-3 pt-6 sm:pt-0 overflow-auto">
                <div class="my-5">
                    <div class="w-full bg-white rounded">
                        <div class="p-6">
                            <div class="flex justify-start flex-col md:flex-row">
                                <img src="{{ asset('storage/' . $product->image, true) }}" class="rounded md:w-72 md:h-72" alt="Product Image">
                                <div class="flex flex-col justify-between">
                                    <h1 class="text-ellipsis lg:text-3xl md:text-2xl text-base md:ml-5 lg:mr-3 md:mt-0 mt-3">{{ $product->title }}</h1>
                                    <div>
                                        @if ($product->discount > 0)
                                            @if ($product->discount_type == 'percentage')
                                                <div class="md:ml-5 mr-3 md:mt-5 mt-3">
                                                    <h1 class="md:text-2xl font-bold text-lg text-green-600">IDR. {{ number_format($product->price - ($product->price * $product->discount / 100)) }}</h1>
                                                    <div class="flex flex-row justify-start items-center">
                                                        <span class="text-red-700 md:text-base text-sm bg-red-200 p-1 rounded mr-2">{{ $product->discount }}%</span>
                                                        <h1 class="md:text-lg text-base line-through">IDR. {{ number_format($product->price) }}</h1>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="md:ml-5 mr-3 md:mt-5 mt-3">
                                                    <h1 class="md:text-2xl font-bold text-lg text-green-600">IDR. {{ number_format($product->price - $product->discount) }}</h1>
                                                    <div class="flex flex-row justify-start items-center">
                                                        <span class="text-red-700 text-sm bg-red-200 p-1 rounded mr-2">-{{ $product->discount }}</span>
                                                        <h1 class="md:text-lg text-base line-through">IDR. {{ number_format($product->price) }}</h1>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <h1 class="md:text-4xl text-xl md:ml-5 mr-3 md:mt-5 mt-3 overflow-hidden">IDR. {{ $product->price }}</h1>
                                        @endif
                                        <div class="flex-flex-col justify-content-between md:ml-5 mr-3 md:mt-10 mt-3">
                                            <div class="flex flex-row w-50">
                                                <span class="text-green-500 hover:text-black text-lg border border-green-500 px-5 py-1 rounded-l-lg">{{ $product->point }} Point(s)</span>
                                                <span class="text-green-500 hover:text-black text-lg border border-green-500 px-5 py-1 rounded-r-lg">{{ $product->duration }} Month(s)</span>
                                            </div>
                                            <div class="hidden product-slug" id="{{ $product->slug }}">
                                                {{ $product->slug }}
                                            </div>
                                            @auth
                                                <button id="button-buy" class="mt-5 w-full bg-green-400 hover:border-green-300 text-white hover:bg-green-200 hover:text-black rounded shadow-md py-2 px-4 text-sm leading-5 font-semibold uppercase text-center block" data-modal-toggle="large-modal">
                                                    BUY
                                                </button>
                                            @endauth
                                            @guest
                                                <a href="{{ route('login') }}" class="mt-5 w-full bg-green-400 hover:border-green-300 text-white hover:bg-green-200 hover:text-black rounded shadow-md py-2 px-4 text-sm leading-5 font-semibold uppercase text-center block">
                                                    BUY
                                                </a>
                                            @endguest
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full mt-5 bg-white rounded">
                        <div class="p-4">
                            <div class="flex justify-start flex-col">
                                <h1 class="lg:text-lg md:text-lg mb-1 text-base md:ml-5 lg:mr-3 md:mt-0 mt-3">Description</h1>
                                <hr class="shadow">
                                <div class="flex mt-3 flex-col justify-between">
                                    <p class="text-lg md:ml-5 mr-3 md:mt-0 mt-3">{!! $product->description !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @auth
        @include('main.buy')

        <script>
            $(document).ready(function(){
                var prod = localStorage.getItem('buyProduct');
                if (prod == '{{ $product->slug }}') {
                    $('#button-buy').click()
                }
                localStorage.removeItem('buyProduct');
            })
        </script>
    @endauth
</x-index-layout>