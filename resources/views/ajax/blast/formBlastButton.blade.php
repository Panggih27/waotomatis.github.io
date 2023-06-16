<link href="{{asset('plugins/select2/css/select2.css')}}" rel="stylesheet">

<div class="card-body">
    <form class="flex flex-row" method="POST" enctype="multipart/form-data" id="formblast">
        @csrf
        <input type="hidden" name="type" value="button">
        {{-- <div class="col-md-12"> --}}
            <label for="textmessage" class="form-label">Sender</label>
            <select name="sender" id="sender" class="form-control" style="width: 100%;" required>
               @foreach ($numbers as $number)
               <option value="{{$number->body}}">{{$number->body}}</option>
               @endforeach
               
            </select>
        {{-- </div> --}}
        <div class="d-flex justify-content-between">

            <div class="col-md-7">
                <div class="thisselect">
                    <label for="inputEmail4" class="form-label">Numbers</label>
    
                    <select name="listnumber[]" id="lists" class="form-control" style="width: 100%; height:200px;" multiple="multiple" required>
                      @foreach ($contacts as $contact)
                          
                      <option value="{{$contact->number}}">{{$contact->number}} ( {{$contact->name}} )</option>
                      @endforeach
                       
                    </select>
                </div>
                <div class="tagsOption d-none">
                    <label for="inputEmail4" class="form-label">Tag Lists</label>
                    <select name="tag" id="tags" class="form-control" style="width: 100%; height:200px;">
                      @foreach ($tags as $tag)
                          
                      <option value="{{$tag->id}}">{{$tag->name}}</option>
                      @endforeach
                       
                    </select>
                </div>
                <div class="form-check mt-3 checkboxAll">
                    <input class="form-check-input" id="all" type="checkbox" name="all" id="gridCheck">
                    <label class="form-check-label" for="gridCheck">
                        Send to All numbers ( in contacts page )
                    </label>
                </div>
                <div class="form-check mt-3 checkboxTag">
                    <input class="form-check-input" id="byTag" type="checkbox" name="byTag" id="gridCheck">
                    <label class="form-check-label" for="gridCheck">
                        Send by tag
                    </label>
                </div>
                <label for="button1" class="form-label">Button 1</label>
                <input type="text" name="button1" id="button1" class="form-control">
               
                <label for="button2" class="form-label">Button 1</label>
                <input type="text" name="button2" id="button2" class="form-control">

                <label for="footer" class="form-label">Footer Message</label>
                <input type="text" name="footer" id="footer" class="form-control">
    
            </div>
            <div class="col-md-5">
                <label for="inputPassword4" class="form-label">Message</label>
                <textarea name="message" id="message" cols="30" rows="10" class="form-control">This is your message,, use {name} to get a name.</textarea>
            </div>
         
        </div>

        <div class="mt-0" id="buttonblast">
            <button type="submit" id="buttonStartBlast" name="submit" class="btn btn-primary">Start Blast</button>
        </div>
    </form>
</div>

<script src="{{asset('js/pages/select2.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script>
    
      $("#all").change(function() {
        if (this.checked) {
            $('#lists').val([])
            $('.thisselect').hide()
            $('#lists').attr('disabled', true)
            $('#lists').attr('required', false)
            $('.checkboxTag').hide();
            checkboxAll = 1;
        } else {
            $('#lists').val([])
            $('.thisselect').fadeIn()
            $('#lists').attr('disabled', false)
            $('#lists').attr('required', true)
            $('.checkboxTag').fadeIn();
            checkboxAll = 0;
        }
      })
      $("#byTag").change(function() {
        if (this.checked) {
            checkboxTag =1;
            $('#lists').val([])
            $('.thisselect').hide()
            $('#lists').attr('disabled', true)
            $('#lists').attr('required', false)
            $('#tags').attr('disabled', false)
            $('#tags').attr('required', true)
            $('.checkboxAll').hide();
            $('.tagsOption').removeClass('d-none')

            ;
        } else {
            $('#lists').val([])
            $('.tagsOption').addClass('d-none')
            $('.checkboxAll').fadeIn();
            $('.thisselect').fadeIn()
            $('#lists').attr('disabled', false)
            $('#lists').attr('required', true)
            $('#tags').attr('disabled', true)
            $('#tags').attr('required', false)
            checkboxTag = 0;
        }
      })

      $("#lists").on('change',()=> {
          let s = [];
          $('#lists option:selected').each(function() {
            s.push($(this).val())
        });
      
         if(s.length != 0){
            $('.checkboxTag').hide();
            $('.checkboxAll').hide();
         } else {
            $('.checkboxTag').fadeIn();
            $('.checkboxAll').fadeIn();
         }

      })

     
      $('#buttonStartBlast').click((e)=> {
        e.preventDefault();
          let selected = []
        $('#lists option:selected').each(function() {
            selected.push($(this).val())
        });

        if(!$('#sender').val()    || !$('#message').val() || !$('#button1').val() || !$('#button2').val() || !$('#footer').val()  ){
            return alert('Please fill all field needed!');
        }
     
         if(checkboxAll === 0 && checkboxTag === 0 && selected.length === 0){
            return alert('Please fill number or select all, or select the tag!');
        } 

        let data;
        let typeReceipt
        if(checkboxAll){
            data = {
                type : 'button',
                typeReceipt : 'all',
                sender : $('#sender').val(),
                message : $('#message').val(),
                button1 : $('#button1').val(),
                button2 : $('#button2').val(),
                footer : $('#footer').val(),
                delay : $('#delay').val()
            }
        } else if(checkboxTag) {
            data = {
                type : 'button',
                typeReceipt : 'tag',
                tag : $('#tags').val(),
                sender : $('#sender').val(),
                message : $('#message').val(),
                button1 : $('#button1').val(),
                button2 : $('#button2').val(),
                footer : $('#footer').val(),
                delay : $('#delay').val()
            }

        } else {
            data = {
                type : 'button',
                typeReceipt : 'numbers',
                numbers : selected,
                sender : $('#sender').val(),
                message : $('#message').val(),
                button1 : $('#button1').val(),
                button2 : $('#button2').val(),
                footer : $('#footer').val(),
                delay : $('#delay').val(),
            }

        }
        $('#buttonStartBlast').html(`<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                                Prossess Blasting...`)
       $.ajax({
           method : 'POST',
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           url : '{{route('blast')}}',
           data : data,
           dataType : 'json',
           success : (result) => {
             //  return console.log(result)
           window.location = ''
           },
           error : (err) => {
                console.log(err);
           }
       })
      })
    
    
</script>