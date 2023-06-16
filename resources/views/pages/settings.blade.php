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
                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="{{ route('generateNewApiKey') }}" method="POST">
                                            @csrf
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">API Key</span>

                                                <input type="text" class="form-control"
                                                    value="{{ Auth::user()->api_key }}" aria-label="Username"
                                                    aria-describedby="basic-addon1" readonly>
                                                <button type="submit" name="api_key" class="btn btn-primary">Generate
                                                    New</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('changeChunk') }}" method="POST">
                                            @csrf
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Maximal
                                                    Blast</span>

                                                <input type="text" name="chunk" class="form-control"
                                                    value="{{ Auth::user()->chunk_blast }}" aria-label="Username"
                                                    aria-describedby="basic-addon1">
                                                <button type="submit" name="changechunk"
                                                    class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="row m-t-lg">
                                        <form action="{{ route('setServer') }}" method="POST">
                                            @csrf
                                            <div class="col-md-6">
                                                <label for="typeServer" class="form-label">Server
                                                    Type</label>
                                                <select name="typeServer" class="form-control" id="server" required>

                                                    @if (env('TYPE_SERVER') === 'localhost')
                                                        <option value="localhost" selected>Localhost</option>
                                                        <option value="hosting">Hosting Shared</option>
                                                        <option value="other">Other</option>
                                                    @elseif(env('TYPE_SERVER') === 'hosting')
                                                        <option value="localhost">Localhost</option>
                                                        <option value="hosting" selected>Hosting Shared</option>
                                                        <option value="other">Other</option>
                                                    @else
                                                        <option value="other" required>Other</option>
                                                        <option value="localhost">Localhost</option>
                                                        <option value="hosting">Hosting Shared</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="Port" class="form-label">Port Node JS</label>
                                                <input type="number" name="portnode" class="form-control" id="Port"
                                                    value="{{ env('PORT_NODE') }}" required>
                                            </div>
                                            <div
                                                class="row m-t-lg {{ env('TYPE_SERVER') === 'other' ? 'd-block' : 'd-none' }} formUrlNode">
                                                <div class="col-md-6">
                                                    <label for="settingsInputUserName " class="form-label">URL
                                                        Node</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"
                                                            id="settingsInputUserName-add">URL</span>
                                                        <input type="text" class="form-control"
                                                            value="{{ env('WA_URL_SERVER') }}" name="urlnode"
                                                            id="settingsInputUserName"
                                                            aria-describedby="settingsInputUserName-add">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-t-lg">
                                                <div class="col">
                                                    <button type="submit" class="btn btn-primary m-t-sm">Update</button>
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
        </div>
    </div>
    <script>
        $('#server').on('change', function() {
            let type = $('#server :selected').val();
            if (type === 'other') {
                $('.formUrlNode').removeClass('d-none')
            } else {
                $('.formUrlNode').addClass('d-none')
            }
        })
    </script>
</x-app-layout>
