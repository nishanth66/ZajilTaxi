<?php
    $columns = \App\Models\user_profile_status::where('status','enabled')->where('column_name_en','not like','ima%')->where('column_name_en','not like','Ima%')->where('column_name_ar','not like','ima%')->where('column_name_ar','not like','ima%')->orderby('id')->get();
    $users = \App\Models\user_profile::whereId($id)->first();
    $image_column = \App\Models\user_profile_status::where('column_name_en','like','ima%')->orWhere('column_name_en','like','Ima%')->where('column_name_ar','like','Ima%')->orWhere('column_name_ar','like','ima%')->first();
?>
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Edit Driver Information
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form method="post" action="{{url('save/changes')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$id}}">
                        <input type="hidden" name="status" value="{{$status}}">
                        @foreach($columns as $column)
                        <?php
                        $column1 = str_replace(' ','_',$column->column_name_en);
                        $column_name_en = str_replace('-','_',$column1);
                        ?>
                        @if($column->type_en == 'num')
                        @if($column->validation == 'required')
                        <div class="form-group col-sm-12">
                            <label>{{$column_name_en}}:</label>
                            <input type="text" name="{{$column_name_en}}" class="form-control" value="{{$users->$column_name_en}}" required>
                        </div>
                        @else
                        <div class="form-group col-sm-12">
                            <label>{{$column_name_en}}:</label>
                            <input type="text" name="{{$column_name_en}}" class="form-control" value="{{$users->$column_name_en}}">
                        </div>
                        @endif

                        @elseif($column->type_en == 'file')
                        @if($column->validation == 'required')
                        @if(isset($users->$column_name_en))
                        <div class="form-group col-sm-12">
                            <label>{{$column_name_en}}:</label>
                            <input type="file" name="{{$column_name_en}}" class="form-control"> <br/>
                            @if(isset($users->$column_name_en))
                            <img src="{{$users->$column_name_en}}" alt="User Image" style="width: 200px">
                            @else
                            <img src="{{asset('public/avatars/default.jpg')}}" alt="User Image" style="width: 200px">
                            @endif
                        </div>
                        @else
                        <div class="form-group col-sm-12">
                            <label>{{$column_name_en}}:</label>
                            <input type="file" name="{{$column_name_en}}" class="form-control" required> <br/>
                            @if(isset($users->$column_name_en))
                            <img src="{{$users->$column_name_en}}" alt="User Image" style="width: 200px">
                            @else
                            <img src="{{asset('public/avatars/default.jpg')}}" alt="User Image" style="width: 200px">
                            @endif
                        </div>
                        @endif
                        @else
                        <div class="form-group col-sm-12">
                            <label>{{$column_name_en}}:</label>
                            <input type="file" name="{{$column_name_en}}" class="form-control"> <br/>
                            @if(isset($users->$column_name_en))
                            <img src="{{$users->$column_name_en}}" alt="User Image" style="width: 200px">
                            @else
                            <img src="{{asset('public/avatars/default.jpg')}}" alt="User Image" style="width: 200px">
                            @endif
                        </div>
                        @endif


                        @else
                        @if($column->validation == 'required')
                        <div class="form-group col-sm-12">
                            <label>{{$column_name_en}}:</label>
                            <input type="text" name="{{$column_name_en}}" value="{{$users->$column_name_en}}" class="form-control" required> <br/>
                        </div>

                        @else
                        <div class="form-group col-sm-12">
                            <label>{{$column_name_en}}:</label>
                            <input type="text" name="{{$column_name_en}}" value="{{$users->$column_name_en}}" class="form-control"> <br/>
                        </div>

                        @endif

                        @endif

                        @endforeach
                        <div class="form-group col-sm-12">
                            <label>Driver Commission:</label>
                            <input type="text" name="Driver_Commission" value="{{$users->Driver_Commission}}" class="form-control"> <br/>
                        </div>
                    <div class="form-group col-sm-12">
                        <button type="submit" class="btn btn-primary">Save</button>
                        {{--<a href="{{url('allCustomers')}}"><button type="button" class="btn btn-default">Cancel</button></a>--}}
                            <a href="{{url('allDrivers')}}"><button type="button" class="btn btn-default">Cancel</button></a>

                    </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection