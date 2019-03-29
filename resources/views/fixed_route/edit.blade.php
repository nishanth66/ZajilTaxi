@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Route Edit
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <form method="post" action="{{url('fixed/routeEdit').'/'.$fixed->id}}">
                        {{csrf_field()}}
                        <div class="form-group col-sm-12">
                            <label>Source Lattitude</label>
                            <input type="text" name="source_lat" class="form-control" value="{{$fixed->source_lat}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Source Logitude</label>
                            <input type="text" name="source_long" class="form-control" value="{{$fixed->source_long}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Destination Lattitude</label>
                            <input type="text" name="destination_lat" class="form-control" value="{{$fixed->destination_lat}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Destination Logitude</label>
                            <input type="text" name="destination_long" class="form-control" value="{{$fixed->destination_long}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Fixed Price</label>
                            <input type="text" name="fixed_price" class="form-control" value="{{$fixed->fixed_price}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{url('fixed/route')}}"><button type="button" class="btn btn-default">Cancel</button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
