@extends('layouts.app')

@section('content')
    <section class="content-header">
        @if( Session::has( 'error' ))
        <center><p style="background-color: red;color: white;width: 50%">{{ Session::get( 'error' ) }}</p></center>
        @endif
        <h1 class="pull-left">Booking Columns</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! url('booking/column') !!}">Add New</a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('booking.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#booking-table').DataTable( {

                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [ 0, ':visible' ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'colvis'
                ]
            } );
        } );
        // $(document).ready(function() {
        //     $('#paypalCredentials-table').DataTable();
        // } );
    </script>

    <script>
        function actionBtn(val)
        {
            var columnId= val;
            $.ajax({
                url: "{{url('changeBookingStatus')}}"+'/'+columnId,
                success: function(result){
                    if(result == 'Success') {
                        window.location.reload();
                    }
                }
            });
        }
    </script>
@endsection

