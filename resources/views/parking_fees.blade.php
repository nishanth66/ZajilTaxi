@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Daily Parking Fees
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
                    <form method="post" action="{{url('parkingFees')}}">
                    {{csrf_field()}}
                    <!-- Price Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('shaded', 'Shaded Daily Parking Fees:') !!}
                            {!! Form::number('shaded', $shaded, ['class' => 'form-control','step'=>"any"]) !!}
                        </div>

                        <!-- Price Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('unshaded', 'Unshaded Daily Parking Fees:') !!}
                            {!! Form::number('unshaded', $unshaded, ['class' => 'form-control','step'=>"any"]) !!}
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
