<table id="datatableList" class="display" style="width:100%">
    <thead>
        <tr>
            <th class="text-center">Nama</th>
            <th class="text-center">Nomor</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($contacts as $contact)
            <tr>
                <td class="text-center">{{$contact->name}}</td>
                <td class="text-center fw-bold">{{$contact->number}}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot></tfoot>
</table>
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#datatableList').DataTable();
    });
</script>