
<div class="mb-3">
    <label for="caption" class="form-label">Message</label>
    <input type="text" name="caption" value="{{ isset($autoreply) && $autoreply->reply_type == 'button' ? json_decode($autoreply->reply)->caption : old('caption') }}" class="form-control" id="caption" placeholder="Text Message" required>
</div>

<div class="mb-3">
    <label for="footer" class="form-label">Footer message</label>
    <input type="text" name="footer" value="{{ isset($autoreply) && $autoreply->reply_type == 'button' ? json_decode($autoreply->reply)->footer : old('footer') }}" class="form-control" id="footer" placeholder="Footer Message" required>
</div>

<div class="row">
    <div class="col">
        <label for="button1" class="form-label">Button 1 <b class="text-danger">*</b></label>
        <input type="text" name="button1" value="{{ isset($autoreply) && $autoreply->reply_type == 'button' ? json_decode($autoreply->reply)->data[0] : old('button1') }}" class="form-control" id="button1" placeholder="Button Text" required>
    </div>
    <div class="col">
        <label for="button2" class="form-label">Button 2</label>
        <input type="text" name="button2" value="{{ isset($autoreply) && $autoreply->reply_type == 'button' ? json_decode($autoreply->reply)->data[1] : old('button2') }}" class="form-control" id="button2" placeholder="Button Text">
    </div>
    <div class="col">
        <label for="button3" class="form-label">Button 3</label>
        <input type="text" name="button3" value="{{ isset($autoreply) && $autoreply->reply_type == 'button' ? json_decode($autoreply->reply)->data[2] : old('button3') }}" class="form-control" id="button3" placeholder="Button Text">
    </div>
</div>