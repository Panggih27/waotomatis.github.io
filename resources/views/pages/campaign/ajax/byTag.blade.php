<hr>
<link href="{{asset('plugins/select2/css/select2.css')}}" rel="stylesheet">
<style>
    .select2-container {
        z-index: 100 !important;
    }
</style>
<div class="mb-3">
    <label for="number">Tag <b class="text-danger">*</b></label>
    <select name="tag" id="lists" class="form-select" required>
        @foreach ($tags as $tag)
            <option value="{{ $tag->id }}" {{ isset($campaign) && $campaign->receivers->type == 'tag' &&  $campaign->receivers->id == $tag->id ?  'selected' : '' }}>{{ $tag->name }}</option>
        @endforeach
    </select>
</div>
<script src="{{asset('js/pages/select2.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>