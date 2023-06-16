<table id="tableHistory" class="display" style="width:100%">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Receiver</th>
            <th class="text-center">Status</th>
            <th class="text-center">Point</th>
            <th class="text-center">Executed At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($histories as $key => $history)
            <tr>
                <td class="text-center">
                    {{ $key + 1 }}
                </td>
                <td class="text-center">
                    {{ $history->receiver }}
                </td>
                <td class="text-center text-uppercase fw-bold {{ $history->status == 'success' ? 'text-success' : ($history->status == 'pending' ? 'text-warning' : 'text-danger') }}">
                    {{ $history->status }}
                </td>
                <td class="text-center">
                    {{ $history->point }}
                </td>
                <td class="text-center fw-bold text-nowrap">
                    {{ ! is_null($history->executed_at) ? Carbon\Carbon::parse($history->executed_at)->isoFormat('MMMM Do YYYY, hh:mm a') : 'WAITING' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot></tfoot>
</table>

<script src="{{ asset('js/pages/datatables.js') }}"></script>
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#tableHistory').DataTable({
            columnDefs: [
                { width: "20%", targets: 0 },
                { width: "20%", targets: 2 }
            ],
            responsive: true
        });
    })
</script>
