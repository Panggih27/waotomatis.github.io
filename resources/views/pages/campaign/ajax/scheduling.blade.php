<div class="mb-3">
    <input type="datetime-local" name="schedule" class="form-control w-50" id="schedule" value="{{ isset($campaign) && ! is_null($campaign->schedule) ? date('Y-m-d\TH:i', strtotime($campaign->schedule)) : old('schedule') }}" required>
</div>