<?php
$count = \Illuminate\Support\Facades\DB::table('customer_profile_status')->get()->count();
?>
@extends('layouts.app')
@section('css')
    <style>
    .scroll
    {
    overflow: scroll !important;
    }
    </style>
@endsection
@section('content')
    <section class="content-header">
        @if( Session::has( 'error' ))
        <center><p style="background-color: red;color: white;width: 50%">{{ Session::get( 'error' ) }}</p></center>
        @endif
        <h1 class="pull-left">Customer Profile Fields</h1>
        @if($count < 15)
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! url('add/customer/column') !!}">Add New</a>
        </h1>
        @endif
    </section>
    <div class="content" id="tableContent">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body scroll">
                @include('user_profiles.table2')
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
            $('#userProfiles2-table').DataTable( {
//                scrollX: true,
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
                url: "{{url('changeCustomerStatus')}}"+'/'+columnId,
                success: function(result){
                    if(result == 'Success') {
                        window.location.reload();
                    }
                }
            });
        }
        function delteDcol(val) {
            if (confirm("Are You Sure?"))
            {
                $.ajax({
                    url: "{{url('deleteCColumn')}}"+'/'+val,
                    complete: function(result){
//                        console.log(result);
                            window.location.reload();
                    }
                });
            }
        }
    </script>
@endsection
