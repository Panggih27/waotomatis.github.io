<div class="mb-3">
  <label class="form-label">Media</label>
  <div class="input-group">
    <span class="input-group-btn">
      <a id="media" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
        <i class="far fa-image-polaroid"></i> Choose
      </a>
    </span>
    <input id="thumbnail" class="form-control form-control-sm" name="media" type="text" value="{{ isset($autoreply) && $autoreply->reply_type == 'media' ? json_decode($autoreply->reply)->url : old('media') }}" readonly>
  </div>
</div>
<div class="mb-3">
  <label for="caption">Caption Media<b class="text-danger">*</b></label>
  <textarea name="caption" id="caption" style="height: 100px" class="form-control" placeholder="Caption Media" required>{{ isset($autoreply) && $autoreply->reply_type == 'media' ? json_decode($autoreply->reply)->caption : old('caption') }}</textarea>
  <div class="border rounded bg-light p-3 mt-2 text-break lh-lg" style="font-size: 13px !important;">
      1. You can use spintax with the following format {word|word|word}. Ex:.. {Hello|Hi|Hi there,|Assalamualaikum} <br>
      2. You can use the dynamc variable from contacts var1 till var5. Ex:.. @{{var1}} @{{var2}} @{{var3}} @{{var4}} @{{var5}} <br>
      3. Caption media only works on Image and Video. (its still mandatory)
  </div>
</div>

<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
  $('#media').filemanager('file')
</script>
                  