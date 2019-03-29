@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Kilometer Price
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    <form method="post" action="{{url('kmPrice')}}">
                        {{csrf_field()}}
                        <!-- Price Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('km_price', 'Price Per Kilometer:') !!}
                            {!! Form::number('km_price', $km_price, ['class' => 'form-control','step'=>"any"]) !!}
                        </div>

                        <!-- Submit Field -->
                        <div class="form-group col-sm-12">
                            {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
