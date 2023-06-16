<div class="min-h-screen -mt-24 pt-28 px-5 flex flex-col sm:justify-center items-center bg-gray-100">
    <div>
        <img src="{{ asset('assets/images/logo.png', true) }}" class="logo">
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden rounded-lg">
        {{ $slot }}
    </div>
</div>
