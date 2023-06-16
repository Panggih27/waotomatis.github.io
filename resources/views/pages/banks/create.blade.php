<x-app-layout title="Banks">
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


                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex justify-content-right">
                        <a href="{{ route('bank.index') }}">
                            <i class="material-icons-outlined">arrow_back</i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title">{{ isset($bank) ? 'Rubah' : 'Tambah' }} Bank</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ isset($bank) ? route('bank.update', $bank) : route('bank.store') }}" method="POST">
                                    @csrf
                                    <input name="_method" type="hidden" value="{{ isset($bank) ? 'PATCH' : 'POST' }}">
                                    <div class="mb-3">
                                        <select class="form-select" name="bank" aria-label="Select Bank" required>
                                            <option selected>Bank</option>
                                            <option value="BNI" {{ (isset($bank) && $bank->bank->name == 'BNI') ? 'selected' : null }}>
                                                Bank Negara Indonesia (BNI)
                                            </option>
                                            <option value="BRI" {{ (isset($bank) && $bank->bank->name == 'BRI') ? 'selected' : null }}>
                                                Bank Rakyat Indonesia (BRI)
                                            </option>
                                            <option value="MANDIRI" {{ (isset($bank) && $bank->bank->name == 'MANDIRI') ? 'selected' : null }}>
                                                Bank Mandiri
                                            </option>
                                            <option value="BCA" {{ (isset($bank) && $bank->bank->name == 'BCA') ? 'selected' : null }}>
                                                Bank Central Asia (BCA)
                                            </option>
                                            <option value="PERMATA" {{ (isset($bank) && $bank->bank->name == 'PERMATA') ? 'selected' : null }}>
                                                Bank Permata
                                            </option>
                                            <option value="BSI" {{ (isset($bank) && $bank->bank->name == 'BSI') ? 'selected' : null }}>
                                                Bank Syariah Indonesia (BSI)
                                            </option>
                                            <option value="DANAMON" {{ (isset($bank) && $bank->bank->name == 'DANAMON') ? 'selected' : null }}>
                                                Bank Danamon
                                            </option>
                                            <option value="BTN" {{ (isset($bank) && $bank->bank->name == 'BTN') ? 'selected' : null }}>
                                                Bank Tabungan Negara (BTN)
                                            </option>
                                            <option value="CIMB" {{ (isset($bank) && $bank->bank->name == 'CIMB') ? 'selected' : null }}>
                                                Bank CIMB Niaga
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_name" class="form-label">Nama Pemilik</label>
                                        <input type="text" name="account_name" value="{{ old('account_name', isset($bank) ? $bank->account_name : '') }}" class="form-control" id="account_name" placeholder="Account Name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_number" class="form-label">Nomor Rekening</label>
                                        <input type="number" name="account_number" value="{{ old('account_number', (isset($bank) ? $bank->account_number : '')) }}" class="form-control" id="account_number" placeholder="Account Number" required>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary float-end" type="submit">
                                            {{ isset($bank) ? 'Rubah' : 'Simpan' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
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

            $("#tableBank").DataTable();
        });
    </script>
</x-app-layout>
