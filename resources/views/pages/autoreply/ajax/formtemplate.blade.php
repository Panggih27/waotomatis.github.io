<div class="mb-3">
    <div class="mb-3">
        <label for="caption">Caption<b class="text-danger">*</b></label>
        <textarea name="caption" id="caption" style="height: 100px" class="form-control" placeholder="Caption Template" required>{{ isset($autoreply) && $autoreply->reply_type == 'template' ? json_decode($autoreply->reply)->caption : old('caption') }}</textarea>
    </div>
    <div class="mb-4">
        <label for="footer">Footer Template<b class="text-danger">*</b></label>
        <input type="text" name="footer" class="form-control" id="footer" value="{{ isset($autoreply) && $autoreply->reply_type == 'template' ? json_decode($autoreply->reply)->footer : old('footer') }}" placeholder="your footer message" required>
    </div>
    <h6 class="mb-3">Template 1 <b class="text-danger">*</b></h6>
    <div class="mb-3">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="button-type" id="url" value="url" required {{ isset($autoreply) && $autoreply->reply_type == 'template' ? 
            (json_decode($autoreply->reply)->data[0]->type == 'urlButton' ? 'checked' : '') : 'checked' }}>
            <label class="form-check-label" for="url">URL Button</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="button-type" id="call" value="call" {{ isset($autoreply) && $autoreply->reply_type == 'template' ? 
            (json_decode($autoreply->reply)->data[0]->type == 'callButton' ? 'checked' : '') : '' }}>
            <label class="form-check-label" for="call">Call Button</label>
        </div>
        <div class="form-check form-check-inline float-end">
            <a href="javascript:void(0)" class="btn btn-sm btn-primary {{ isset($autoreply) && $autoreply->reply_type == 'template' && (count(json_decode($autoreply->reply)->data) > 1) ? 'd-none' : null }} " id="add-button">
                Add Template
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="text">Display Text</label>
            <input type="text" class="form-control" id="text" name="text" placeholder="Display Text" value="{{ isset($autoreply) && $autoreply->reply_type == 'template' ? json_decode($autoreply->reply)->data[0]->text : old('text') }}" required>
        </div>
        <div class="col">
            <label for="text" id="label-button-type">URL</label>
            <input type="text" class="form-control" id="action" name="action" value="{{ isset($autoreply) && $autoreply->reply_type == 'template' ? (json_decode($autoreply->reply)->data[0]->type == 'callButton' ? str_replace('+', '', json_decode($autoreply->reply)->data[0]->action) : json_decode($autoreply->reply)->data[0]->action) : old('action') }}" placeholder="https://example.com" required>
        </div>
    </div>                       
</div>
<div id="second-button" class="mt-3"></div>

@if (isset($autoreply) && $autoreply->reply_type == 'template')
    <script>
        $(document).ready(function() {

            @if (count(json_decode($autoreply->reply)->data) > 1)
                $('#second-button').html(`<div class="mb-3">
                    <h6 class="mb-3">Template 2</h6>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="url2" value="url" required  {{ isset($autoreply) && $autoreply->reply_type == 'template' ? 
                                (json_decode($autoreply->reply)->data[1]->type == 'urlButton' ? 'checked' : '') : 'checked' }}>
                            <label class="form-check-label" for="url2">URL Button</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="call2" value="call" {{ isset($autoreply) && $autoreply->reply_type == 'template' ? 
                                (json_decode($autoreply->reply)->data[1]->type == 'callButton' ? 'checked' : '') : '' }}>
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
                            <input type="text" class="form-control" id="text2" name="text2" placeholder="Display Text" value="{{ isset($autoreply) && $autoreply->reply_type == 'template' ? json_decode($autoreply->reply)->data[1]->text : old('text2') }}" required>
                        </div>
                        <div class="col">
                            <label for="action2" id="label-button-type2">URL</label>
                            <input type="text" class="form-control" id="action2" name="action2" value="{{ isset($autoreply) && $autoreply->reply_type == 'template' ? (json_decode($autoreply->reply)->data[1]->type == 'callButton' ? str_replace('+', '', json_decode($autoreply->reply)->data[1]->action) : json_decode($autoreply->reply)->data[1]->action) : old('action2') }}" placeholder="https://example.com" required>
                        </div>
                    </div>                       
                </div>`)
            @endif

            $('#add-button').click(function(){
                $(this).addClass('d-none');
                @if (count(json_decode($autoreply->reply)->data) > 1)
                    $('#second-button').html(`<div class="mb-3">
                    <h6 class="mb-3">Template 2</h6>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="url2" value="url" required  {{ isset($autoreply) && $autoreply->reply_type == 'template' ? 
                                (json_decode($autoreply->reply)->data[1]->type == 'urlButton' ? 'checked' : '') : 'checked' }}>
                            <label class="form-check-label" for="url2">URL Button</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="call2" value="call" {{ isset($autoreply) && $autoreply->reply_type == 'template' ? 
                                (json_decode($autoreply->reply)->data[1]->type == 'callButton' ? 'checked' : '') : '' }}>
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
                            <input type="text" class="form-control" id="text2" name="text2" placeholder="Display Text" value="{{ isset($autoreply) && $autoreply->reply_type == 'template' ? json_decode($autoreply->reply)->data[1]->text : old('text2') }}" required>
                        </div>
                        <div class="col">
                            <label for="action2" id="label-button-type2">URL</label>
                            <input type="text" class="form-control" id="action2" name="action2" value="{{ isset($autoreply) && $autoreply->reply_type == 'template' ? (json_decode($autoreply->reply)->data[1]->type == 'callButton' ? str_replace('+', '', json_decode($autoreply->reply)->data[1]->action) : json_decode($autoreply->reply)->data[1]->action) : old('action2') }}" placeholder="https://example.com" required>
                        </div>
                    </div>                       
                </div>`)
                @else
                    $('#second-button').html(`<div class="mb-3">
                        <h6 class="mb-3">Template 2</h6>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="button-type2" id="url2" value="url" required checked>
                                <label class="form-check-label" for="url2">URL Button</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="button-type2" id="call2" value="call">
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
                                <input type="text" class="form-control" id="text2" name="text2" placeholder="Display Text" value="{{ old('text2') }}" required>
                            </div>
                            <div class="col">
                                <label for="action2" id="label-button-type2">URL</label>
                                <input type="text" class="form-control" id="action2" name="action2" value="{{ old('action') }}" placeholder="https://example.com" required>
                            </div>
                        </div>                       
                    </div>`)
                @endif
            });
        });
    </script>
@else
    <script>
        $(document).ready(function() {
            $('#add-button').click(function(){
                $(this).addClass('d-none');
                $('#second-button').html(`<div class="mb-3">
                    <h6 class="mb-3">Template 2</h6>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="url2" value="url" required checked>
                            <label class="form-check-label" for="url2">URL Button</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="button-type2" id="call2" value="call">
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
                            <input type="text" class="form-control" id="text2" name="text2" placeholder="Display Text" value="{{ old('text2') }}" required>
                        </div>
                        <div class="col">
                            <label for="action2" id="label-button-type2">URL</label>
                            <input type="text" class="form-control" id="action2" name="action2" value="{{ old('action') }}" placeholder="https://example.com" required>
                        </div>
                    </div>                       
                </div>`)
            });
        });
    </script>
@endif

<script>

    $(document).on('change', 'input[name="button-type2"]', function() {
        $('#action2').val("");
        if ($(this).val() == 'url') {
            $('#label-button-type2').text('URL');
            $('#action2').attr('placeholder', 'https://example.com');
        } else {
            $('#label-button-type2').text('Phone Number');
            $('#action2').attr('placeholder', '628123456789');
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
            } else {
                $('#label-button-type').text('Phone Number');
                $('#action').attr('placeholder', '628123456789');
            }
        })
    })
</script>