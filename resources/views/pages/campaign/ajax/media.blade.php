<div class="mb-3">
    <label for="caption_media">Caption Title Media<b class="text-danger">*</b></label>
    <textarea name="caption_media" id="caption_media" style="height: 100px" class="form-control" placeholder="Caption Title Media" required>{{ isset($campaign) && ! is_null($campaign->template->media) ? $campaign->template->media->caption : old('caption_media') }}</textarea>
    <div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
        1. You can use spintax with the following format {word|word|word}. Ex:.. {Hello|Hi|Hi there,|Assalamualaikum} <br>
        2. You can use the dynamc variable from contacts var1 till var5. Ex:.. @{{var1}} @{{var2}} @{{var3}} @{{var4}} @{{var5}} <br>
        3. Caption media only works on Image and Video. (its still mandatory)
    </div>
</div>
<div class="input-group">
  <span class="input-group-btn">
    <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
      <i class="fa fa-picture-o"></i> Choose
    </a>
  </span>
  <input id="thumbnail" class="form-control form-control-sm" type="text" name="media" value="{{ isset($campaign) && ! is_null($campaign->template->media) ? $campaign->template->media->url : old('media') }}" readonly required>
</div>

<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
  $('#image').filemanager('file')
</script>