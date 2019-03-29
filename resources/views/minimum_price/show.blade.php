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
                                <input type="text" class="form-control" readonly value="{{$price}}">
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4 form-group">
                        <center>
                            <a href="{{url('minimum/price/edit')}}" class="btn btn-primary">Edit</a>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection