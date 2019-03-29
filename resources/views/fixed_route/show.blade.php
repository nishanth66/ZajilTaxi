@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Route
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Id</label>
                            <p>{{$fixed->id}}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Source Logitude</label>
                            <p>{{$fixed->source_long}}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Destination Lattitude</label>
                            <p>{{$fixed->destination_lat}}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Destination Logitude</label>
                            <p>{{$fixed->destination_long}}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Fixed Price</label>
                            <p>{{$fixed->fixed_price}}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            <a href="{{url('fixed/route')}}"><button type="button" class="btn btn-default">Back</button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
