<div class="app align-content-stretch d-flex flex-wrap">
    <div class="app-sidebar">
        <div class="logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png', true) }}" height="50" width="auto">
            </a>
        </div>
        <div class="app-menu">
            <ul class="accordion-menu">
                <li class="sidebar-title">
                    Menu
                </li>
                <li class="{{ request()->is('dashboard') ? 'active-page' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line fa-lg"></i> {{ __('Beranda') }}</a>
                </li>
                {{-- <li class="{{ request()->is('file-manager') ? 'active-page' : '' }}">
                    <a href="{{ route('file-manager') }}" class="">
                        <i class="fas fa-folder fa-lg"></i> {{ __('File Manager') }}</a>
                </li>--}}
                <li class="{{ request()->is('autoreply') ? 'active-page' : '' }}">
                    <a href="{{ route('autoreply.index') }}" class="">
                        <i class="fas fa-comment-alt fa-lg"></i> {{ __('Balas Otomatis') }}</a>
                </li>
                <li class="{{ request()->is('contact') ? 'active-page' : '' }}">
                    <a href="">
                        <i class="fas fa-address-card fa-lg"></i>{{ __('Kontak') }}<i class="fas fa-angle-right has-sub-menu"></i></a>
                    <ul class="sub-menu active">
                        <li>
                            <a href="{{ route('contact.index') }}"><i class="fas fa-address-book"></i> {{ __('Daftar Kontak') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('tag') }}"><i class="fas fa-tag pr-2"></i> {{ __('Penanda') }}</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ request()->is('campaign') ? 'active-page' : '' }}">		
                    <a href="{{ route('campaign.index') }}" class="">		
                        <i class="fas fa-bullhorn"></i>{{ __('Kampanye') }}</a>		
                </li>
                {{-- <li class="{{ request()->is('blast') ? 'active-page' : '' }}">
                    <a href="{{ route('blast.index') }}" class="">
                        <i class="fas fa-mail-bulk fa-lg"></i>{{ __('system.blast') }}</a>
                </li> --}}
                <li class="{{ request()->is('message/test') ? 'active-page' : '' }}">
                    <a href="{{ route('messagetest') }}" class="">
                        <i class="fas fa-paper-plane fa-lg"></i>{{ __('Pengujian Pesan') }}</a>
                </li>
                <li class="{{ \Route::currentRouteName() == 'inbox.index' ? 'active-page' : '' }}">
                    <a href="{{ route('inbox.index') }}" class=""><i class="fas fa-mail-bulk"></i>Pesan Masuk</a>
                </li>
                <li class="sidebar-title">
                    Transaksi
                </li>
                <li class="{{ \Route::currentRouteName() == 'transaction.index' ? 'active-page' : '' }}">
                    <a href="{{ route('transaction.index') }}" class="">
                        <i class="fas fa-cash-register fa-lg"></i> {{ __('Pembelian') }}</a>
                </li>
                <li class="sidebar-title">
                    Managemen
                </li>
                @hasanyrole('super_admin')
                    <li class="{{ (request()->is('management/users/*') || request()->is('management/users')) ? 'active-page' : '' }}">
                        <a href="{{ route('users.index') }}">
                            <i class="fas fa-users"></i>{{ __('Pengguna') }}</a>
                    </li>
                    <li class="{{ request()->is('cost') ? 'active-page' : '' }}">		
                        <a href="{{ route('cost.index') }}" class="">		
                            <i class="fas fa-coins"></i>{{ __('Tarif/Biaya') }}</a>		
                    </li>
                    <li class="{{ \Route::currentRouteName() == 'management.users' ? 'active-page' : '' }}">
                        <a href="{{ route('management.users') }}">
                            <i class="fas fa-key"></i>{{ __('Hak Akses') }}</a>
                    </li>
                    {{-- <li class="{{ \Route::currentRouteName() == 'cost.index' ? 'active-page' : '' }}">
                        <a href="{{ route('cost.index') }}"><i
                                class="material-icons-two-tone">manage_accounts</i>{{ __('Point Cost') }}</a>
                    </li> --}}
                @endhasanyrole
                @can('product-crud')
                    <li class="{{ \Route::currentRouteName() == 'product.index' ? 'active-page' : '' }}">
                        <a href="{{ route('product.index') }}" class="">
                            <i class="fas fa-archive fa-lg"></i>{{ __('Produk') }}</a>
                    </li>
                @endcan
                @hasanyrole('customer')
                    <li class="{{ \Route::currentRouteName() == 'buypoint.index' ? 'active-page' : '' }}">
                        <a href="{{ route('buypoint.index') }}" class="">
                            <i class="fas fa-archive fa-lg"></i>{{ __('Beli Poin') }}</a>
                    </li>
                @endcan
                <li class="{{request()->is('bank') ? 'active-page' : '' }}">
                    <a href="{{ route('bank.index') }}" class="">
                        <i class="fas fa-credit-card fa-lg"></i>{{ __('Bank') }}</a>
                </li>
                <li class="{{request()->is('term-and-condition') ? 'active-page' : '' }}">
                    <a href="{{ route('term.index') }}" class="">
                        <i class="fas fa-user-shield"></i>{{ __('Syarat & Ketentuan') }}</a>
                </li>
                {{-- @hasanyrole('super_admin')
                <li class="{{request()->is('tickets') ? 'active-page' : '' }}">
                    <a href="{{ route('tickets.index') }}" class="">
                        <i class="fas fa-headset"></i>{{ __('Tiket') }}</a>
                </li>
                @endhasanyrole
                @hasanyrole('customer')
                    <li class="{{request()->is('tickets') ? 'active-page' : '' }}">
                        <a href="{{ route('ticket.index') }}" class="">
                            <i class="fas fa-headset"></i>{{ __('Tiket') }}</a>
                    </li>
                @endhasanyrole --}}

                <li class="sidebar-title">
                    Other
                </li>
                <li class="{{request()->is('point') ? 'active-page' : '' }}">
                    <a href="{{ route('point.index') }}" class="">
                        <i class="fas fa-coins fa-lg"></i>{{ __('Poin') }}</a>
                </li>
                <li class="{{ request()->is('change-password') ? 'active-page' : '' }}">
                    <a href="{{ route('change-password') }}">
                        <i class="fas fa-key fa-lg"></i>{{ __('Sandi') }}</a>
                </li>
                {{-- @hasanyrole('super_admin')
                    <li class="{{ request()->is('rest-api') ? 'active-page' : '' }}">
                        <a href="{{ route('rest-api') }}">
                            <i class="fas fa-link fa-lg"></i>{{ __('API Whatsapp') }}</a>
                    </li>
                    <li class="{{ request()->is('settings') ? 'active-page' : '' }}">
                        <a href="{{ route('settings') }}">
                            <i class="fas fa-cogs fa-lg"></i>{{ __('Pengaturan') }}</a>
                    </li>
                @endhasanyrole --}}
            </ul>
        </div>
    </div>
    <div class="app-container">
        {{-- <div class="search">
            <form>
                <input class="form-control" type="text" placeholder="Type here..." aria-label="Search">
            </form>
            <a href="#" class="toggle-search"><i class="material-icons">close</i></a>
        </div> --}}
        <div class="app-header">
            <nav class="navbar navbar-light navbar-expand-lg">
                <div class="container-fluid p-0">
                    <div class="navbar-nav flex-grow-1 " id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link nav-notifications-toggle arrow-left" href="#">
                                    <i class="fas fa-angle-left" id="arrow-toggle"></i></a>
                            </li>
                            {{-- <li class="nav-item dropdown hidden-on-mobile">
                                <a class="nav-link dropdown-toggle" href="#" id="exploreDropdownLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="material-icons-outlined">explore</i>
                                </a>

                            </li> --}}
                        </ul>
                    </div>
                    @if (is_null(Auth::user()->email_verified_at))
                        <div class="d-flex flex-column border border-1 border-danger rounded p-1">
                            @if (session('status') == 'verification-link-sent')
                                <div class="text-sm text-success">
                                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                </div>
                            @else
                            <p class="mb-1 hidden-on-mobile">{{ __('If you didn\'t receive the verification email, we will gladly send you another.') }}</p>
                            <form method="POST" action="{{ route('verification.send') }}" class="mx-auto my-auto">
                                @csrf
                                <button class="btn btn-sm btn-warning">{{ __('Resend Verification Email') }}</button>
                            </form>
                            @endif
                        </div>
                    @endif
                    <div class="d-flex">
                        <ul class="navbar-nav">
                                <li class="nav-item hidden-on-mobile">
                                    <a class="nav-link nav-notifications-toggle p-1" id="notificationsDropDown" href="#"
                                        data-bs-toggle="dropdown">
                                    <img src="{{ Auth::user()->avatar }}" class="rounded-circle" width="30"
                                        height="30" alt="Profile Picture">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end notifications-dropdown"
                                    aria-labelledby="notificationsDropDown">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-header h6"
                                            style="border: 0; background-color :white;">Logout</button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
