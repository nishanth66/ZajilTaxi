<?php
$columns = DB::table('booking_status')->where('status','enabled')->get();
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
                    <div class="form-group col-sm-12">
                        <label>Customer ID</label>
                        <p>{{$book->user_id}}</p>
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Order Date</label>
                        <p>{{$book->date}}</p>
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Order Time</label>
{{--                        <p>{{date("H:s a",strtotime($book->time))}}</p>--}}
                        <p>{{$book->booking_time}}</p>
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Trip Source</label>
                        <p>{{$book->source}}</p>
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Trip Destination</label>
                        <p>{{$book->destination}}</p>
                    </div>
                    @foreach($columns as $column)
                        <?php
                        $column1 = str_replace(' ','_',$column->column_name_en);
                        //            $column2 = str_replace(' ','_',$column->column_name_ar);
                        $column_name = str_replace('-','_',$column1);
                        ?>
                        @if($column->type_en == 'file')
                            <div class="form-group col-sm-12">
                                <label>{{$column->column_name_en}}</label>
                                @if(isset($book->$column_name) && ($book->$column_name != '' || !empty($book->$column_name)) && $book->$column_name != 'null')
                                <p><img src="{{$book->$column_name}}" class="statusImage1"></p>
                                @else
                                <p><img src="{{asset('public/avatars/default.jpg')}}" class="statusImage1"></p>
                                @endif
                            </div>
                        @else
                            <div class="form-group col-sm-12">
                                <label>{{$column->column_name_en}}</label>
                                <p>{{$book->$column_name}}</p>
                            </div>
                        @endif
                    @endforeach
                    @if(\Illuminate\Support\Facades\DB::table('booking_images')->where('booking_id',$book->id)->exists())
                        <?php
                            $images = \Illuminate\Support\Facades\DB::table('booking_images')->where('booking_id',$book->id)->get()
                         ?>
                    <div class="form-group col-sm-12">
                        <label>Status Images:</label>
                        <div class="row">
                            @foreach($images as $image)
                                <div class="col-md-3 ">
                                    <img src="{{asset('public/avatars').'/'.$image->image}}" class="statusImage">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="form-group col-sm-12">
                        <a href="{{url('allBookings')}}"><button type="button" class="btn btn-default">Back</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        Array.prototype.forEach.call(document.querySelector("img"), function (elem) {
            elem.addEventListener("click", function () {
                elem.classList.toggle("enlarged");
            });
        });
    </script>
@endsection