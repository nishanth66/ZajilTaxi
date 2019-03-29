<?php
    $columns = \App\Models\booking_status::where('status','enabled')->get();
    $book = \App\Models\booking::whereId($id)->first();
    $driver = \Illuminate\Support\Facades\DB::table('driver_profile')->where('status','accepted')->get();
    $statuses = DB::table('status')->get();
?>
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Booking
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form method="post" action="{{url('booking/change')}}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$id}}">
                        <div class="form-group col-sm-6">
                            <label>Customer ID</label>
                            <input type="text" readonly name="user_id" class="form-control" value="{{$book->user_id}}">
                        </div>

                        <div class="form-group col-sm-6">
                            <label>Trip Source</label>
                            <input type="text" name="source" class="form-control" value="{{$book->source}}">
                        </div>

                        <div class="form-group col-sm-6">
                            <label>Trip Destination</label>
                            <input type="text" name="destination" class="form-control" value="{{$book->destination}}">
                        </div>
                        @foreach($columns as $column)
                            <?php
                            $column1 = str_replace(' ','_',$column->column_name_en);
                            //            $column2 = str_replace(' ','_',$column->column_name_ar);
                            $column_name_en = str_replace('-','_',$column1);
                            //            $column_name_ar = str_replace('-','_',$column2);
                            ?>
                        @if($column->type_en == 'file' || $column->type_ar == 'file')
                            <div class="form-group col-sm-6">
                                <label>{{$column_name_en}}</label>
                                <input type="file" name="{{$column_name_en}}" class="form-control">
                                @if($book[$column_name_en] != '' || !empty($book[$column_name_en]))
                                <img src="{{$book[$column_name_en]}}" style="width: 75px;height: 75px;">
                                @endif
                            </div>
                        @elseif($column->type_en == 'num' || $column->type_ar == 'num' )
                            <div class="form-group col-sm-6">
                                <label>{{$column_name_en}}</label>
                                <input type="text" name="{{$column_name_en}}" class="form-control" value="{{$book[$column_name_en]}}">
                            </div>
                        @elseif($column->type_en == 'did' || $column->type_ar == 'did' )
                            <div class="form-group col-sm-6">
                                <label>{{$column_name_en}}</label>
                                <select name="{{$column_name_en}}" class="form-control">
                                    <option value="" disabled>Select Driver</option>
                                    @foreach($driver as $drivers)
                                        <option value="{{$drivers->id}}"  <?php if ($book[$column_name_en] == $drivers->id){ echo "selected";} ?>> {{$drivers->id}} </option>
                                    @endforeach
                                </select>
                            </div>
                                @else
                            <div class="form-group col-sm-6">
                                <label>{{$column_name_en}}</label>
                                <input type="text" name="{{$column_name_en}}" class="form-control" value="{{$book[$column_name_en]}}">
                            </div>
                        @endif
                        @endforeach
                        <div class="form-group col-sm-6">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="" selected disabled>Select a Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{$status->name}}" <?php if ($status->name == $book->status) { echo 'selected';} ?>>{{$status->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{url('allBookings')}}"><button type="button" class="btn btn-default">Cancel</button> </a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection