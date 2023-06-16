<x-app-layout title="Campaign">
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
                                <h5 class="card-title">Daftar Kampanye</h5>
                                <a href="{{ route('campaign.create') }}" class="btn btn-sm btn-primary text-decoration-none">
                                    <i class="material-icons-outlined">add</i>Tambah
                                </a>
                            </div>
                            <div class="card-body">
                                <table id="tableCampaign" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Judul</th>
                                            <th class="text-center">Pengirim</th>
                                            <th class="text-center">Penerima</th>
                                            <th class="text-center">Poin</th>
                                            <th class="text-center">Penjadwalan</th>
                                            <th class="text-center">Tereksekusi</th>
                                            {{-- <th class="text-center">Description</th> --}}
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($campaigns as $key => $campaign)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $key + 1 }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $campaign->title }}
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    {{ implode('-', str_split('+' .  $campaign->number->body, 4)) }}
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    @if ($campaign->receivers->type == 'all')
                                                        <b>ALL CONTACTS</b>
                                                    @elseif ($campaign->receivers->type == 'contact')
                                                        {{ implode('-', str_split('+' .  collect($campaign->receivers->data)->pluck('number')->first(), 4)) . (count($campaign->receivers->data) > 1 ? ', ....' : '') }}
                                                    @elseif ($campaign->receivers->type == 'group')
                                                        {!! 'Group : <b>' . $campaign->receivers->name .  '</b>' !!}
                                                    @elseif ($campaign->receivers->type == 'random')
                                                        {!! 'Random : <b>' . $campaign->receivers->id .  ' Contacts</b>' !!}
                                                    @else
                                                        {!! 'Tag : <b>' . $campaign->receivers->name .  '</b>' !!}
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold text-danger">
                                                    {!! ! is_null($campaign->executed_at) ? $campaign->point : $campaign->point . '<br><small>(estimasi point per nomor whatsapp)</small>' !!}
                                                </td>
                                                @if (! is_null($campaign->schedule))
                                                    <td class="text-center fw-bold text-nowrap {{ Carbon\Carbon::parse($campaign->schedule)->isPast() ? 'text-danger' : 'text-success' }} mt-3">
                                                        {{ ! is_null($campaign->schedule) ? Carbon\Carbon::parse($campaign->schedule)->isoFormat('Do MMMM YYYY HH:mm') : 'Non Scheduleable' }}
                                                    </td>
                                                @else
                                                    <td class="text-center fw-bold text-nowrap">
                                                        {{ $campaign->is_manual ? 'Manually' : 'Executed' }}
                                                    </td>
                                                @endif
                                                <td class="text-center fw-bold text-nowrap {{ Carbon\Carbon::parse($campaign->executed_at)->isPast() ? 'text-danger' : 'text-success' }} mt-3">
                                                    {{ ! is_null($campaign->executed_at) ? Carbon\Carbon::parse($campaign->executed_at)->isoFormat('Do MMMM YYYY HH:mm') : ($campaign->is_processing ? 'processing...' : '-') }}
                                                </td>
                                                {{-- <td class="text-center">
                                                    {{ $campaign->description}}
                                                </td> --}}
                                                <td class="text-center text-nowrap">
                                                    <div class="d-flex justify-content-center">
                                                        @if (is_null($campaign->executed_at) && $campaign->is_manual && ! $campaign->is_processing)
                                                            <form action='{{ route("campaign.send", $campaign->id) }}' method="POST">
                                                                @csrf
                                                                <button class="btn btn-sm btn-danger me-1" type="submit">
                                                                    Kirim
                                                                </button>
                                                            </form>
                                                            {{-- <form action='{{ route("campaign.sendByJob", $campaign->id) }}' method="POST">
                                                                @csrf
                                                                <button class="btn btn-sm btn-success" type="submit">
                                                                    SEND JOB
                                                                </button>
                                                            </form> --}}
                                                        @endif
                                                        <a href="{{ route('campaign.show', $campaign->id) }}" class="btn btn-sm btn-info btn-detail mx-1">
                                                            Detail
                                                        </a>
                                                        @if (is_null($campaign->executed_at) || $campaign->is_processing)
                                                            <a href="{{ route('campaign.edit', $campaign->id) }}" class="btn btn-sm btn-warning me-1">
                                                                Edit
                                                                {{-- <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span> --}}
                                                            </a>
                                                        @endif
                                                        @if(!$campaign->is_processing)
                                                            <form action='{{ route("campaign.destroy", $campaign->id) }}' method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-danger del-campaign" type="submit">
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        @endif
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

            $("#tableCampaign").DataTable({
                responsive: true
            });

            $('#tableCampaign tbody').on('click', '.del-campaign', function(e) {
                var form =  $(this).closest("form");
                e.preventDefault();
                swal.fire({
                    title: "Apakah anda yakin ingin menghapus Kampanye ini ?",
                    text: "aksi ini bersifat permanent, data yang terkait dengan kampanye ini seperti history akan terhapus",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Lanjutkan!",
                    cancelButtonText: "Tidak, Batalkan!"
                }).then((willProcess) => {
                    if (willProcess.isConfirmed) {
                        form.submit();
                    }
                });
            })

        });
    </script>
</x-app-layout>
