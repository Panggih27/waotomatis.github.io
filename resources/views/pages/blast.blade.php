<style>
    .lds-ellipsis {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-ellipsis div {
  position: absolute;
  top: 33px;
  width: 13px;
  height: 13px;
  border-radius: 50%;
  background: black;
  animation-timing-function: cubic-bezier(0, 1, 1, 0);
}
.lds-ellipsis div:nth-child(1) {
  left: 8px;
  animation: lds-ellipsis1 0.6s infinite;
}
.lds-ellipsis div:nth-child(2) {
  left: 8px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(3) {
  left: 32px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(4) {
  left: 56px;
  animation: lds-ellipsis3 0.6s infinite;
}
@keyframes lds-ellipsis1 {
  0% {
    transform: scale(0);
  }
  100% {
    transform: scale(1);
  }
}
@keyframes lds-ellipsis3 {
  0% {
    transform: scale(1);
  }
  100% {
    transform: scale(0);
  }
}
@keyframes lds-ellipsis2 {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(24px, 0);
  }
}

</style>
<x-app-layout title="Blast">

   
  <link href="{{asset('plugins/datatables/datatables.min.css')}}" rel="stylesheet">

<script src="{{asset('js/pages/datatables.js')}}"></script>
<script src="{{asset('plugins/datatables/datatables.min.js')}}"></script>
    <div class="app-content">
        @if (session()->has('alert'))
        <x-alert>
            @slot('type',session('alert')['type'])
            @slot('msg',session('alert')['msg'])
        </x-alert>
     @endif
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card todo-container">
                            <div class="row">
                                <div class="col-xl-4 col-xxl-3">
                                    <div class="todo-menu">

                                        <h5 class="todo-menu-title">Type</h5>
                                        <ul class="list-unstyled todo-status-filter">
                                            <li><a onclick="textBlast()" class="optionTextBlast"><i class="material-icons-outlined">email</i>Blast Text</a></li>
                                            <li><a onclick="imageBlast()" class="optionImageBlast"><i class="material-icons-outlined">image</i>Blast Image</a></li>
                                            <li><a onclick="buttonBlast()" class="optionButtonBlast"><i class="material-icons-outlined">email</i>Blast Button</a></li>
                                            <li><a onclick="templateBlast()" class="optionTemplateBlast"><i class="material-icons-outlined">email</i>Blast Template</a></li>
                                        </ul>
                                       
                                    </div>
                                </div>
                                <div class="col-xl-8 col-xxl-9 formBlastWrapper ">
                                    
                                </div>
                           
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="mt-4">Histories</h2>
                <div class="row mt-4">
                  <div class="col">
                      <div class="card">
                          <div class="card-header d-flex justify-content-between">
                              <h5 class="card-title">Histories</h5>
                              <form action="" method="POST">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Delete All</button>
                              </form>
                          </div>
                          <div class="card-body">
                              <table id="datatable1" class="display" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th>Receiver</th>
                                          <th>Type</th>
                                          <th>Message</th>
                                          <th>Status</th>
                                          {{-- <th class="d-flex justify-content-center">Action</th> --}}
                                      </tr>
                                  </thead>
                                  <tbody>
                                     @foreach ($histories as $history)
                                         
                                     <tr>
                                         <td>{{$history->receiver}}</td>
                                         <td><span class="badge badge-secondary badge-sm text-warning">{{$history->type}}</span></td>
                                         <td> <textarea name="" id="" cols="30" rows="2" disabled>{{Str::limit($history->message,100)}}</textarea> </td>
                                         <td><span class="badge badge-{{$history->status === 'success' ? 'success' : 'danger'}}">{{$history->status}}</span></td>
                                         {{-- <td>
                                             <div class="d-flex justify-content-center">
                                                 <button class="btn btn-success btn-sm mx-3">Add to Tag</button>
                                                 <form action="{{route('contactDeleteOne',$contact->id)}}" method="POST">
                                                  @method('delete')
                                                  @csrf
                                                     <input type="hidden" name="id" value="{{$contact->id}}">
                                                     <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="material-icons">delete_outline</i>Delete</button>
                                                  </form>
                                             </div>
                                          </td> --}}
                                      </tr>
                                      @endforeach
                                    
  
                                  </tbody>
                                  <tfoot></tfoot>
                              </table>
                          </div>
                      </div>
                  </div>
  
              </div>
            </div>
        </div>
    </div>
    
    <script>
    let checkboxAll = 0;
    let checkboxTag = 0;
        function textBlast(){
            $('.optionTemplateBlast').removeClass('active')
            $('.optionButtonBlast').removeClass('active')
            $('.optionTextBlast').addClass('active')
            $('.optionImageBlast').removeClass('active')
           getForm('text-message');   
        }
        function imageBlast(){
            $('.optionTemplateBlast').removeClass('active')
            $('.optionButtonBlast').removeClass('active')
            $('.optionTextBlast').removeClass('active')
            $('.optionImageBlast').addClass('active')
           getForm('image-message');   
        }
        function buttonBlast(){
            $('.optionTemplateBlast').removeClass('active')
            $('.optionTextBlast').removeClass('active')
            $('.optionImageBlast').removeClass('active')
            $('.optionButtonBlast').addClass('active')

           getForm('button-message');   
        }
        function templateBlast(){
            
            $('.optionTextBlast').removeClass('active')
            $('.optionImageBlast').removeClass('active')
            $('.optionButtonBlast').removeClass('active')
            $('.optionTemplateBlast').addClass('active')

           getForm('template-message');   
        }

        function getForm(url){
            $.ajax({
                    url : `/blast/${url}`,
                    method : 'GET',
                    dataType : 'html',
                    success : (result) => {
                      
                        $('.formBlastWrapper').addClass('d-flex align-items-center justify-content-center')
                        $('.formBlastWrapper').html(`<div class="lds-ellipsis flex justify-items-center"><div></div><div></div><div></div><div></div></div>`)
                        setTimeout(() => {
                           
                            $('.formBlastWrapper').removeClass('d-flex')
                            $('.formBlastWrapper').html(result)
                        }, 500);
                        
                    },
                    error : (err) => {
                        console.log(err)
                    }
                })
                return; 
        }

      
   


    </script>
</x-app-layout>