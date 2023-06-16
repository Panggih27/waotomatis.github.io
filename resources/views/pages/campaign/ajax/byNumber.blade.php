<hr>
<link href="{{asset('plugins/select2/css/select2.css')}}" rel="stylesheet">
<style>
    .select2-container {
        z-index: 100 !important;
    }
</style>
<div class="mb-3">
    <label for="number">Numbers <b class="text-danger">*</b></label>
    <select name="numbers[]" id="number" class="form-control" multiple="multiple" required>
        @foreach ($contacts as $contact)
            <option value="{{ $contact->number }}" {{ isset($campaign) && $campaign->receivers->type == 'contact' && in_array($contact->number, collect($campaign->receivers->data)->pluck('number')->toArray()) ?  'selected' : '' }}>{{ $contact->number }} ( {{ $contact->name }} )</option>
        @endforeach
    </select>
</div>
<script src="{{asset('js/pages/select2.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>