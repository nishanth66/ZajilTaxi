@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Broadcast Notification
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">

            <div class="box-body">
                @include('adminlte-templates::common.errors')
                <div class="row">
                    <form method="post" action="{{url('broadcast/sendPush')}}">
                        @if( Session::has( 'error' ))
                            <p class="alert alert-error">{{ Session::get( 'error' ) }}</p>
                        @endif
                        @if(Session::has('success'))
                            <p class="alert alert-success">{{ Session::get('success') }}</p>
                        @endif
                        {{csrf_field()}}
                        <div class="form-group col-sm-12">
                            <textarea name="message" rows="20" cols="100"></textarea>
                        </div>
                        <div class="form-group col-sm-12">
                            <button type="submit" name="customer" value="customer" class="btn btn-primary">Send to Customers</button>
                            <button type="submit" name="driver" value="driver" class="btn btn-primary">Send to Drivers</button>
                            <a href="{{url('/home')}}"><button type="button" class="btn btn-default">Cancel</button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
