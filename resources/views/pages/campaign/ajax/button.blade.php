<div class="mb-3">
    <label for="caption_button">Caption Title Button<b class="text-danger">*</b></label>
    <textarea name="caption_button" id="caption_button" style="height: 100px" class="form-control" placeholder="Caption Title Button" required>{{ isset($campaign) && ! is_null($campaign->template->button) ? $campaign->template->button->text : old('caption_button') }}</textarea>
    <div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
        1. You can use spintax with the following format {word|word|word}. Ex:.. {Hello|Hi|Hi there,|Assalamualaikum} <br>
        2. You can use the dynamc variable from contacts var1 till var5. Ex:.. @{{var1}} @{{var2}} @{{var3}} @{{var4}} @{{var5}}
    </div>
</div>
<div class="mb-3">
    <label for="button1">Button 1 <b class="text-danger">*</b></label>
    <input type="text" name="button1" class="form-control" id="button1" value="{{ isset($campaign) && ! is_null($campaign->template->button) ? $campaign->template->button->buttons[0]->displayText : old('button1') }}" placeholder="Display Text Button 1">
</div>
<div class="mb-3">
    <label for="button2">Button 2</label>
    <input type="text" name="button2" class="form-control" id="button2" value="{{ isset($campaign) && ! is_null($campaign->template->button) && count($campaign->template->button->buttons) > 1 ? $campaign->template->button->buttons[1]->displayText : old('button2') }}" placeholder="Display Text Button 2">
</div>
<div class="mb-3">
    <label for="button2">Button 3</label>
    <input type="text" name="button3" class="form-control" id="button3" value="{{ isset($campaign) && ! is_null($campaign->template->button) && count($campaign->template->button->buttons) > 2 ? $campaign->template->button->buttons[2]->displayText : old('button3') }}" placeholder="Display Text Button 3">
</div>
<div class="mb-3">
    <label for="footer">Footer Message</label>
    <input type="text" name="footer_button" class="form-control" id="footer" value="{{ isset($campaign) && ! is_null($campaign->template->button) ? $campaign->template->button->footer : old('footer') }}" placeholder="your footer message">
</div>