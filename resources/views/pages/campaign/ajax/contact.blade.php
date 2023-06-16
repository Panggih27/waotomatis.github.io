{{-- <div class="mb-3">
    <div class="row">
        <div class="col">
            <label for="contact-name">Name <b class="text-danger">*</b></label>
            <input type="text" class="form-control" id="contact-name" name="contact-name" placeholder="Contact Name" value="{{ isset($campaign) && ! is_null($campaign->template->contact) ? $campaign->template->contact->name : old('name') }}" required>
        </div>
        <div class="col">
            <label for="contact-fname">Full Name</label>
            <input type="text" class="form-control" id="contact-fname" name="contact-fname" placeholder="Contact Full Name" value="{{ isset($campaign) && ! is_null($campaign->template->contact) ? $campaign->template->contact->fullName : old('contact-fname') }}">
        </div>
        <div class="col">
            <label for="org">Organization</label>
            <input type="text" class="form-control" id="org" name="org" placeholder="Organization" value="{{ isset($campaign) && ! is_null($campaign->template->contact) ? $campaign->template->contact->org : old('org') }}">
        </div>
    </div>
</div> --}}
<link href="{{ asset('plugins/select2/css/select2.css') }}" rel="stylesheet">
<style>
    .select2-container {
        z-index: 100 !important;
    }
</style>
<div class="mb-3">
    <label for="vcard">Number <b class="text-danger">*</b></label>
    <select name="vcard[]" id="vcard" class="form-control" multiple="multiple" required>
        @foreach ($contacts as $key => $contact)
            <option value="{{ $contact->number }}" {{ isset($campaign) && ! is_null($campaign->template->contact) && in_array($contact->number, array_column($campaign->template->contact->vcard, 'number'))  ?  'selected' : '' }}>{{ $contact->number }} ( {{ $contact->name }} )</option>
        @endforeach
    </select>
</div>
<script src="{{asset('js/pages/select2.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>