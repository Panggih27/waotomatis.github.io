<div id="large-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full" aria-hidden="true">
    <div class="relative p-4 w-full max-w-4xl h-full md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex justify-between items-center p-5 rounded-t border-b dark:border-gray-600">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                    Transaction
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="large-modal">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="flex flex-col md:flex-row justify-start">
                    <div class="p-5 self-start shadow grow mr-1">
                        <div class="flex flex-col md:flex-row justify-start">
                            <img src="{{ asset('storage/' . ($product->image ?? ''), true) }}" class="rounded hidden md:block w-40 mh-40" alt="Product Image">
                            <div class="flex flex-col w-full">
                                <h1 class="text-ellipsis text-2xl mb-2 md:ml-5 md:text-left text-center lg:mr-3 md:mt-0 mt-3">{{ $product->title }}</h1>
                                <img src="{{ asset('storage/' . ($product->image ?? ''), true) }}" class="rounded md:hidden block" alt="Product Image">
                                <div class="flex flex-row mt-3 md:ml-5 justify-center sm:justify-start">
                                    <span class="text-green-500 w-full text-center hover:text-black text-base sm:text-xs border border-green-500 whitespace-nowrap px-3 py-0.5 rounded-l-lg">{{ $product->point }} Point(s)</span>
                                    <span class="text-green-500 w-full text-center hover:text-black text-base sm:text-xs border border-green-500 whitespace-nowrap px-3 py-0.5 rounded-r-lg">{{ $product->duration }} Month(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="p-5 shadow ml-1">
                            <div class="flex flex-col justify-evently">
                                <div class="flex justify-between w-full">
                                    <h3>Price   : </h3>
                                    <h3 class="font-bold">{{ number_format($product->price) }}</h3>
                                </div>
                                <div class="flex justify-between w-full">
                                    <h3>Discount   : </h3>
                                    @if ($product->discount > 0)
                                        @if ($product->discount_type == 'percentage')
                                            <h3>- {{ number_format(($product->price * $product->discount / 100)) }} ({{ $product->discount }}%)</h3>
                                            @else
                                            <h3>- {{ number_format($product->discount) }}</h3>
                                        @endif
                                    @else
                                        <h3 class="font-bold">{{ number_format(0) }}</h3>
                                    @endif
                                </div>
                                <div class="flex justify-between w-full">
                                    <h3>Fee   : </h3>
                                    <h3 class="font-bold">{{ number_format(0) }}</h3>
                                </div>
                                @php
                                    $code = rand(100,999);
                                @endphp
                                <div class="flex justify-between w-full mb-2">
                                    <h3>Payment Code   : </h3>
                                    <h3 class="font-bold">{{ number_format($code) }}</h3>
                                </div>
                                <hr>
                                <div class="flex justify-between w-full self-end mt-3">
                                    <h3>Total   : </h3>
                                    @if ($product->discount > 0)
                                        @if ($product->discount_type == 'percentage')
                                            <h3 class="font-bold">{{ number_format(($product->price - ($product->price * $product->discount / 100)) + $code) }}</h3>
                                            @else
                                            <h3 class="font-bold">{{ number_format(($product->price - $product->discount) + $code) }}</h3>
                                        @endif
                                    @else
                                        <h3 class="font-bold">{{ number_format($product->price + $code) }}</h3>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="p-3 shadow ml-1 mt-2 max-h-60 overflow-y-scroll scroll-smooth">
                            <h5 class="text-sm">Payment Transfer</h5>
                            <div class="flex flex-col justify-evently">
                                <div class="grid grid-cols-1">
                                    <form action="{{ route('transaction.store') }}" method="POST" id="transaction-form">
                                        @csrf
                                        @foreach ($banks as $bank)
                                            <div class="border border-green-300 rounded my-1">
                                                <div class="p-2 rounded shadow items-center flex flex-row justify-between">
                                                    <input type="radio" name="bank" value="{{ $bank->id }}" class="radio-bank w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 dark:focus:bg-blue-600 dark:bg-gray-700 dark:border-gray-600" id="{{ $bank->id }}" required>
                                                    <label for="{{ $bank->id }}">
                                                        <img src="{{ asset($bank->bank->image, true) }}" class="mb-2" width="100" alt="{{ $bank->bank->name }}">
                                                    </label>
                                                </div>
                                                <div class="detail-bank p-2" data-bank={{ $bank->id }}>
                                                    <h5 class="text-sm my-1">Name : <span class="font-bold">{{ $bank->account_name }}</span></h5>
                                                    <h5 class="text-sm my-1">Account Number : <span class="font-bold">{{ $bank->account_number }}</span></h5>
                                                </div>
                                            </div>
                                        @endforeach
                                        <input type="hidden" name="payment_code" value="{{ $code }}">
                                        <input type="hidden" name="product" value="{{ $product->id }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-end items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600">
                <button type="button" data-modal-toggle="large-modal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
                <button type="submit" form="transaction-form" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Buy</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.radio-bank').click(function() {
            $('.detail-bank').addClass('hidden');
            $('.detail-bank[data-bank='+$(this).val()+']').removeClass('hidden');
        });
    });
</script>