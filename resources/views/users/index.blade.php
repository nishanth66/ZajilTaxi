
@extends('layouts.app')
@section('css')
<style>
    .scroll
    {
        overflow: auto !important;
    }
</style>
@endsection
@section('content')
    <section class="content-header">
        <h1 class="pull-left">Drivers Confirmation</h1>
        <h1 class="pull-right">
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body scroll">
                    @include('users.table')
            </div>
        </div>
        <div class="text-center">
        
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/plug-ins/1.10.19/i18n/Arabic.json"></script>
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable( {
//                 scrollX: true,
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
        function acceptUser(a,b)
        {
            $.ajax({
                url: "{{url('driverStatus')}}"+'/'+a+'/'+b,
                success: function(result){
                        window.location.reload();
                }
            });
        }
    </script>
@endsection

