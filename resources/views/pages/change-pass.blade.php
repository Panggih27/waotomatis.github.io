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
                                <form action="{{ route('change-password.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="_method" value="PATCH">
                                    <div class="row m-t-xxl">
                                        <div class="col-md-6">
                                            <label for="settingsCurrentPassword" class="form-label">Current
                                                Password</label>
                                            <input type="password" name="current" class="form-control"
                                                aria-describedby="settingsCurrentPassword"
                                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
                                            <div id="settingsCurrentPassword" class="form-text">Never share
                                                your password with anyone.</div>
                                        </div>
                                    </div>
                                    <div class="row m-t-xxl">
                                        <div class="col-md-6">
                                            <label for="settingsNewPassword" class="form-label">New
                                                Password</label>
                                            <input type="password" name="password" class="form-control"
                                                aria-describedby="settingsNewPassword"
                                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
                                        </div>
                                    </div>
                                    <div class="row m-t-xxl">
                                        <div class="col-md-6">
                                            <label for="settingsConfirmPassword" class="form-label">Confirm
                                                Password</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                aria-describedby="settingsConfirmPassword"
                                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
                                        </div>
                                    </div>
                                    <div class="row m-t-lg">
                                        <div class="col">

                                            <button type="submit" class="btn btn-primary m-t-sm">Change
                                                Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
