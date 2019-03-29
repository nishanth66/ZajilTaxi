<?php
$columns = DB::table('customer_profile_status')->where('status','enabled')->get();
?>
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Profile
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    @foreach($columns as $column)
                        <?php
                        $column1 = str_replace(' ','_',$column->column_name_en);
                        //            $column2 = str_replace(' ','_',$column->column_name_ar);
                        $column_name = str_replace('-','_',$column1);
                        ?>
                    @if($column->type_en == 'file')
                        <div class="form-group col-sm-12">
                            <label>{{$column->column_name_en}}</label>
                            <p><img src="{{$user->$column_name}}" style="height: 150px;width: 150px;"></p>
                        </div>
                     @else
                        <div class="form-group col-sm-12">
                            <label>{{$column->column_name_en}}</label>
                            <p>{{$user->$column_name}}</p>
                        </div>
                     @endif
                    @endforeach
                    <div class="form-group col-sm-12">
                        <a href="{{url('allBookings')}}"><button type="button" class="btn btn-default">Back</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection