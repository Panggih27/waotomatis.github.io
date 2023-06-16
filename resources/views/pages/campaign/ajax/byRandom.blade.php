<hr>
<div class="mb-3">
    <label for="number">Random <b class="text-danger">*</b></label>
    <input type="number" id="number" class="form-control" min="1" max="{{ $contacts }}" name="random" value="{{ isset($campaign) && $campaign->receivers->type == 'random' ? $campaign->receivers->id : old('random') }}" placeholder="0">
</div>

<script>
    $(document).ready(function () {
        $('#number').on('change', function () {
            if ($(this).val() > {{ $contacts }}) {
                $(this).val({{ $contacts }});
            }
        });
    });
</script>