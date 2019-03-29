@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Minimum Trip Price
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <form method="post" action="{{url('minimum/price/save')}}">
                        {{csrf_field()}}
                    <div class="col-sm-12">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4">
                            <center>
                                <label>Price:</label>
                            </center>
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group col-sm-4">
                        </div>
                        <div class="col-sm-4 form-group">
                            <input type="text" name="price" class="form-control" value="{{$price}}">
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4 form-group">
                        <center>
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a class="btn btn-default" href="{{url('minimum/price')}}">Cancel</a>
                        </center>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection