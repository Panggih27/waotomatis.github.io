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
                {{-- <div class="card-header d-flex justify-content-between">
                    <div class="d-flex justify-content-right">
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" id="addModal"
                            data-bs-target="#addProduct">
                            <i class="material-icons-outlined">add</i>Add
                        </button>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="card-title">Daftar Pengguna</h5>
                            </div>
                            <div class="card-body">
                                <table id="tableList" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Device</th>
                                            <th class="text-center">Poin</th>
                                            <th class="text-center">Kadaluarsa</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $user->name }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $user->email }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $user->numbers->pluck('body') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $user->point->point }}
                                                </td>
                                                <td class="text-center fw-bold {{ Carbon\Carbon::parse($user->point->expired_at)->isPast() ? 'text-danger' : 'text-success' }} mt-3">
                                                    {{ Carbon\Carbon::parse($user->point->expired_at)->isoFormat('MMMM Do YYYY, hh:mm a') }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('users.transaction', $user->id) }}" class="btn btn-sm btn-info btn-detail">
                                                            Transaction
                                                            {{-- <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span> --}}
                                                        </a>
                                                        <a href="{{ route('users.point', $user->id) }}" class="btn btn-sm btn-info mx-1 btn-detail">
                                                            Point
                                                            {{-- <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span> --}}
                                                        </a>
                                                        {{-- <form action='{{ route("product.destroy", $user->id) }}' method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger" type="submit">
                                                                Delete
                                                            </button>
                                                        </form> --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot></tfoot>
                                </table>
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

            $("#tableList").DataTable({
                responsive: true
            });

        });
    </script>
</x-app-layout>
