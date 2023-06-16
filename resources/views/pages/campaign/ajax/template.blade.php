<div class="mb-3">
    <div class="mb-3">
        <label for="caption_template">Caption Title Template<b class="text-danger">*</b></label>
        <textarea name="caption_template" id="caption_template" style="height: 100px" class="form-control" placeholder="Caption Title Template" required>{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->text : old('caption_template') }}</textarea>
        <div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
            1. You can use spintax with the following format {word|word|word}. Ex:.. {Hello|Hi|Hi there,|Assalamualaikum} <br>
            2. You can use the dynamc variable from contacts var1 till var5. Ex:.. @{{var1}} @{{var2}} @{{var3}} @{{var4}} @{{var5}}
        </div>
    </div>
    <div class="mb-4">
        <label for="footer">Footer Template<b class="text-danger">*</b></label>
        <input type="text" name="footer_template" class="form-control" id="footer" value="{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->footer : old('footer') }}" placeholder="your footer message" required>
    </div>
    <h6 class="mb-3">Template 1 <b class="text-danger">*</b></h6>
    <div class="mb-3">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="button-type" id="url" value="url" required {{ isset($campaign) && ! is_null($campaign->template->template) ? ($campaign->template->template->templateButtons[0]->type == 'url' ? 'checked' : '') : 'checked' }}>
            <label class="form-check-label" for="url">URL Button</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="button-type" id="call" value="call" {{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'call' ? 'checked' : '' }}>
            <label class="form-check-label" for="call">Call Button</label>
        </div>
        <div class="form-check form-check-inline float-end">
            <a href="javascript:void(0)" class="btn btn-sm btn-primary" id="add-button">
                Add Template
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="text">Display Text</label>
            <input type="text" class="form-control" id="text" name="text" placeholder="Display Text" value="{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->templateButtons[0]->displayText : old('text') }}" required>
        </div>
        <div class="col">
            <label for="text" id="label-button-type">{{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'call' ? 'Phone Number' : 'URL' }}</label>
            <input type="text" class="form-control" id="action" name="action" value="{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->templateButtons[0]->action : old('action') }}" placeholder="{{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'call' ? '628123456789' : 'https://example.com' }}" required>
        </div>
    </div>                       
</div>
<div id="second-button" class="mt-3"></div>

@if (isset($campaign) && ! is_null($campaign->template->template))
    @if (count($campaign->template->template->templateButtons) > 1)
        <script>
            $(document).ready(function(){
                $('#add-button').addClass('d-none');
                $('#second-button').html(`<div class="mb-3">
                    <h6 class="mb-3">Template 2</h6>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="url2" value="url" required {{ isset($campaign) ? (! is_null($campaign->template->template) && $campaign->template->template->templateButtons[1]->type == 'url' ? 'checked' : '') : 'checked' }}>
                            <label class="form-check-label" for="url2">URL Button</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="call2" value="call" {{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[1]->type == 'call' ? 'checked' : '' }}>
                            <label class="form-check-label" for="call2">Call Button</label>
                        </div>
                        <div class="form-check form-check-inline float-end">
                            <a href="javascript:void(0)" class="btn btn-sm btn-danger" id="remove-button">
                                Remove Template
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="text2">Display Text</label>
                            <input type="text" class="form-control" id="text2" name="text2" placeholder="Display Text" value="{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->templateButtons[1]->displayText : old('text2') }}" required>
                        </div>
                        <div class="col">
                            <label for="action2" id="label-button-type2">{{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[1]->type == 'call' ? 'Phone Number' : 'URL' }}</label>
                            <input type="text" class="form-control" id="action2" name="action2" value="{{ isset($campaign) && ! is_null($campaign->template->template) ? $campaign->template->template->templateButtons[1]->action : old('action') }}" placeholder="{{ isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[1]->type == 'call' ? '628123456789' : 'https://example.com' }}" required>
                        </div>
                    </div>                       
                </div>`)
            })
        </script>
    @endif
@endif

<script>

    $(document).on('change', 'input[name="button-type2"]', function() {
        $('#action2').val("");
        if ($(this).val() == 'url') {
            $('#label-button-type2').text('URL');
            $('#action2').attr('placeholder', 'https://example.com');

            @php
                if (isset($campaign) && ! is_null($campaign->template->template) && (count($campaign->template->template->templateButtons) > 1) && $campaign->template->template->templateButtons[1]->type == 'url') {
                    echo '$("#action2").val("'.$campaign->template->template->templateButtons[1]->action.'");';
                }
            @endphp
        } else {
            $('#label-button-type2').text('Phone Number');
            $('#action2').attr('placeholder', '628123456789');

            @php
                if (isset($campaign) && ! is_null($campaign->template->template) && (count($campaign->template->template->templateButtons) > 1) && $campaign->template->template->templateButtons[1]->type == 'call') {
                    echo '$("#action2").val("'.$campaign->template->template->templateButtons[1]->action.'");';
                }
            @endphp
        }
    });

    $(document).on('click', '#remove-button', function() {
        $('#second-button').html('');
        $('#add-button').removeClass('d-none');
    });

    $(document).ready(function() {

        $('input[name="button-type"]').change(function(){
            $('#action').val("");
            if ($(this).val() == 'url') {
                $('#label-button-type').text('URL');
                $('#action').attr('placeholder', 'https://example.com');

                @php
                    if (isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'url') {
                        echo '$("#action").val("'.$campaign->template->template->templateButtons[0]->action.'");';
                    }
                @endphp
            } else {
                $('#label-button-type').text('Phone Number');
                $('#action').attr('placeholder', '628123456789');

                @php
                    if (isset($campaign) && ! is_null($campaign->template->template) && $campaign->template->template->templateButtons[0]->type == 'call') {
                        echo '$("#action").val("'.$campaign->template->template->templateButtons[0]->action.'");';
                    }
                @endphp
            }
        })

        $('#add-button').click(function(){
            $(this).addClass('d-none');
            $('#second-button').html(`<div class="mb-3">
                <h6 class="mb-3">Template 2</h6>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="button-type2" id="url2" value="url" required {{ isset($campaign) ?  ((! is_null($campaign->template->template) && count($campaign->template->template->templateButtons) > 1) ? ($campaign->template->template->templateButtons[1]->type == 'url' ? 'checked' : '') : 'checked' ) : 'checked' }}>
                        <label class="form-check-label" for="url2">URL Button</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="button-type2" id="call2" value="call" {{ isset($campaign) && ! is_null($campaign->template->template) && (count($campaign->template->template->templateButtons) > 1) && $campaign->template->template->templateButtons[1]->type == 'call' ? 'checked' : '' }}>
                        <label class="form-check-label" for="call2">Call Button</label>
                    </div>
                    <div class="form-check form-check-inline float-end">
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" id="remove-button">
                            Remove Template
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="text2">Display Text</label>
                        <input type="text" class="form-control" id="text2" name="text2" placeholder="Display Text" value="{{ isset($campaign) && ! is_null($campaign->template->template) && (count($campaign->template->template->templateButtons) > 1) ? $campaign->template->template->templateButtons[1]->displayText : old('text2') }}" required>
                    </div>
                    <div class="col">
                        <label for="action2" id="label-button-type2">{{ isset($campaign) && ! is_null($campaign->template->template) && (count($campaign->template->template->templateButtons) > 1) && $campaign->template->template->templateButtons[1]->type == 'call' ? 'Phone Number' : 'URL' }}</label>
                        <input type="text" class="form-control" id="action2" name="action2" value="{{ isset($campaign) && ! is_null($campaign->template->template) && (count($campaign->template->template->templateButtons) > 1) ? $campaign->template->template->templateButtons[1]->action : old('action') }}" placeholder="{{ isset($campaign) && ! is_null($campaign->template->template) && (count($campaign->template->template->templateButtons) > 1) && $campaign->template->template->templateButtons[1]->type == 'call' ? '628123456789' : 'https://example.com' }}" required>
                    </div>
                </div>                       
            </div>`)
        });
    });
</script>