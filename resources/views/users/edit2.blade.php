<?php
$columns = \Illuminate\Support\Facades\DB::table('customer_profile_status')->where('status','enabled')->orderby('id')->get();
$users = \Illuminate\Support\Facades\DB::table('customer_profile')->whereId($id)->first();
?>
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Edit Customer Information
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <form method="post" action="{{url('save/customer/changes')}}" enctype="multipart/form-data">
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
                                  <div class="form-group col-sm-6">
                                      <label>{{$column_name_en}}:</label>
                                      <input type="text" name="{{$column_name_en}}" class="form-control" value="{{$users->$column_name_en}}" required>
                                  </div>
                                @else
                                  <div class="form-group col-sm-6">
                                      <label>{{$column_name_en}}:</label>
                                      <input type="text" name="{{$column_name_en}}" class="form-control" value="{{$users->$column_name_en}}">
                                  </div>
                                @endif

                            @elseif($column->type_en == 'file')
                                @if($column->validation == 'required')
                                    @if(isset($users->$column_name_en))
                                        <div class="form-group col-sm-6">
                                            <label>{{$column_name_en}}:</label>
                                            <input type="file" name="{{$column_name_en}}" class="form-control"> <br/>
                                            <img src="{{$users->$column_name_en}}" alt="User Image" style="width: 200px">
                                        </div>
                                    @else
                                        <div class="form-group col-sm-6">
                                            <label>{{$column_name_en}}:</label>
                                            <input type="file" name="{{$column_name_en}}" class="form-control" required> <br/>
                                            <img src="{{$users->$column_name_en}}" alt="User Image" style="width: 200px">
                                        </div>
                                    @endif
                                @else
                                  <div class="form-group col-sm-6">
                                      <label>{{$column_name_en}}:</label>
                                      <input type="file" name="{{$column_name_en}}" class="form-control"> <br/>
                                      <img src="{{$users->$column_name_en}}" alt="User Image" style="width: 200px">
                                  </div>
                                @endif


                                @else
                                    @if($column->validation == 'required')
                                        <div class="form-group col-sm-6">
                                            <label>{{$column_name_en}}:</label>
                                            <input type="text" name="{{$column_name_en}}" value="{{$users->$column_name_en}}" class="form-control" required> <br/>
                                        </div>

                                    @else
                                        <div class="form-group col-sm-6">
                                            <label>{{$column_name_en}}:</label>
                                            <input type="text" name="{{$column_name_en}}" value="{{$users->$column_name_en}}" class="form-control"> <br/>
                                        </div>

                                    @endif

                            @endif

                        @endforeach
                        <div class="form-group col-sm-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{url('allCustomers')}}"><button type="button" class="btn btn-default">Cancel</button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection