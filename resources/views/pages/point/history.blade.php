<div class="card-footer">
    <table id="tablePoint" class="display" style="width:100%">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Point</th>
                <th class="text-center">Type</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($histories as $key => $history)
                <tr>
                    <td class="text-center">
                        {{ $key + 1 }}
                    </td>
                    <td class="text-center fw-bold {{ $history->type == '+' ? 'text-success' : 'text-danger' }}">
                        {{ $history->type }} {{ $history->point }}
                    </td>
                    <td class="text-center fw-bold">
                        {{ str_replace('App\Models\\', '', $history->historyable_type) }}
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            @if ($history->historyable_type == 'App\Models\Transaction')
                                <button class="btn btn-sm btn-info mx-1 btn-detail"
                                    data-id="{{ $history->historyable->invoice }}" data-bs-toggle="modal"
                                    data-bs-target="#DetailHistory">
                                    Detail
                                    <span class="material-icons-outlined"
                                        style="font-size: 15px !important;">visibility</span>
                                </button>
                            @else
                                <a href="{{ route(strtolower(str_replace('App\Models\\', '', $history->historyable_type)).'.show', $history->historyable_id) }}" class="btn btn-sm btn-info mx-1">
                                    Detail
                                    <span class="material-icons-outlined"
                                        style="font-size: 15px !important;">visibility</span>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot></tfoot>
    </table>
</div>

<script src="{{ asset('js/pages/datatables.js') }}"></script>
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#tablePoint').DataTable({
            columnDefs: [
                { width: "20%", targets: 0 },
                { width: "20%", targets: 2 }
            ],
            responsive: true
        });
    })
</script>
