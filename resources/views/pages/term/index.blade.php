
<x-app-layout title="{{ __('Term a Condition') }}">

    <div class="app-content">
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="text" role="tabpanel" aria-labelledby="account-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Syarat & Ketentuan</h5>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                @role('customer')
                                                    {!! $data->content !!}
                                                @else
                                                <form id="formTerm" method="POST">
                                                    @csrf
                                                    <input type="text" name="contents" class="form-control" id="summernote" style="height: 100px">
                                                    <button type="button" name="sendMsg" class="btn btn-success mt-3"><i class="fas fa-save"></i>Update</button>
                                                </form>
                                                @endrole
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<link rel="stylesheet" href="{{ asset('assets/summernote/summernote-lite.min.css') }}">
<script src="{{ asset('assets/summernote/summernote-lite.min.js') }}"></script>

<script>
    $(document).ready(function () {
        let code = '{!! $data->content !!}';
        $('#summernote').summernote({
            toolbar : [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
            ],
            height: 300
        });
        $('#summernote').summernote('code', code);

        $('.btn-success').on('click', function() {
            let token = $("meta[name='csrf-token']").attr('content');
            let url = '{{ route("term.update", "term") }}';
            let content = $('#summernote').summernote('code');

            let data = {
                _token  : token,
                content : content,
            };
            
            $.ajax({
                url: url,
                type: "PUT",
                data: data,
                dataType:'json',
                success: function (data) {
                    if(data.success === true){
                        swal.fire({
                        position: 'top-center',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 1500
                        })
                    }else{
                        swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.error.message,
                        })
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });

    });

    
</script>
