@extends('layouts.app')
@section('css')
    <style>
        .hidden{display:none;}

    </style>
@endsection
@section('content')
    <section class="content-header">
        <h1>
            Fare Charts
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <form method="post" action="{{url('fare/Pages/Save')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group col-sm-12">
                            <textarea name="fare" class="form-control page">{{$content}}</textarea>
                        </div>
                        <input name="image" type="file" id="upload" class="hidden" onchange="">
                        <div class="form-group col-sm-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{url('home')}}"><button type="button" class="btn btn-default">Cancel</button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://tinymce.cachefly.net/4.2/tinymce.min.js"></script>
    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: "textarea.page",
                mode : "textareas",
                resize: 'both',
                height : 300,
//            width : 360,
                theme: "modern",
                mobile: { theme: 'mobile' },
                paste_data_images: true,
                plugins: [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor colorpicker textpattern"
                ],
                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                toolbar2: "print preview media | forecolor backcolor emoticons",
                image_advtab: true,
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype == 'image') {
                        $('#upload').trigger('click');
                        $('#upload').on('change', function() {
                            var file = this.files[0];
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                callback(e.target.result, {
                                    alt: ''
                                });
                            };
                            reader.readAsDataURL(file);
                        });
                    }
                },
                templates: [{
                    title: 'Test template 1',
                    content: 'Test 1'
                }, {
                    title: 'Test template 2',
                    content: 'Test 2'
                }]
            });
        });
    </script>
@endsection