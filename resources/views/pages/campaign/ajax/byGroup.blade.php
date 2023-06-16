<hr>
<div class="mb-3 ms-2">
    <div class="form-check form-switch form-check-inline">
        <input class="form-check-input" type="radio" name="group_type" id="group" value="direct" {{ isset($campaign) ? ($campaign->receivers->type == 'group' && !$campaign->receivers->is_broadcast ? 'checked' : '') : 'checked' }}>
        <label class="form-check-label" for="contact">Send to Group</label>
    </div>
    <div class="form-check form-switch form-check-inline">
        <input class="form-check-input" type="radio" name="group_type" id="broadcast" value="broadcast" {{ isset($campaign) && $campaign->receivers->type == 'group' && $campaign->receivers->is_broadcast ? 'checked' : '' }}>
        <label class="form-check-label" for="group">Broadcast To Group's Member</label>
    </div>
    <div class="form-check form-check-inline" id="loading-refresh-group">
        <button class="btn btn-sm btn-info" id="refresh-group">Refresh Group</button>
    </div>
</div>
<link href="{{asset('plugins/select2/css/select2.css')}}" rel="stylesheet">
<style>
    .select2-container {
        z-index: 100 !important;
    }
</style>
<div class="mb-3">
    <div class="form-group">
        <label for="group_list">Group</label>
        <select class="form-control" id="group_list" name="group_list" size="3"></select>
    </div>
</div>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getGroup() {
            $('#group_list').html('')
            $('#refresh-group').html('<div class="spinner-border text-white" role="status"><span class="visually-hidden">Loading...</span></div>')
            $('#refresh-group').addClass('disabled');
            $.ajax({
                url: "{{ route('show-group', ':sender') }}".replace(':sender', $('#senders').val()),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    data.forEach(function (value, index) {
                        @if (isset($campaign) && $campaign->receivers->type == "group")
                            if ('{{ $campaign->receivers->id }}' == value.id) {
                                $('#group_list').append('<option data-count="'+value.participant_count+'" value="' + value.id + '" selected>' + value.title + '</option>')
                            } else {
                                $('#group_list').append('<option data-count="'+value.participant_count+'" value="' + value.id + '">' + value.title + '</option>')
                            }
                        @else
                         $('#group_list').append('<option data-count="'+value.participant_count+'" value="' + value.id + '">' + value.title + '</option>')
                        @endif
                    });
                    $('#refresh-group').html('Refresh Group');
                    $('#refresh-group').removeClass('disabled');
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }

        getGroup();

        $('#refresh-group').on('click', function (e) {
            e.preventDefault();
            $('#group_list').html('')
            $('#refresh-group').html('<div class="spinner-border text-white" role="status"><span class="visually-hidden">Loading...</span></div>')
            $('#refresh-group').addClass('disabled');
            $.ajax({
                url: "{{ route('fetch-group', ':sender') }}".replace(':sender', $('#senders').val()),
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    getGroup();
                },
                error: function (err) {
                    console.log(err);
                }
            });
        });

        $('#senders').on('change', function () {
            getGroup();
        });

        
        function formatState (state) {
            if (!state.id) {
                return state.text;
            }
            
            var $state = $(
                `<div class="d-flex flex-column p-2">
                    <h6 class="border-bottom pb-1 mb-0">${state.text}</h6>
                    <small>${state.element.dataset.count} participants</small>
                </div>`
            );
            return $state;
        };

        $("#group_list").select2({
            templateResult: formatState,
        });
    });
</script>