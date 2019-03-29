<?php
    $feedbacks=App\Models\feedback::get();
?>
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
        @if( Session::has( 'error' ))
            <center><p style="background-color: red;color: white;width: 50%">{{ Session::get( 'error' ) }}</p></center>
        @endif
        <h1 class="pull-left">Feedbacks</h1>

    </section>
    <div class="content" id="tableContent">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body scroll">
                <table class="table table-responsive" id="feedback-table">
                    <thead>
                    <tr>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Mobile Number</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($feedbacks as $feedback)
                            <tr>
                                <td>{{$feedback->message}}</td>
                                <td>{{$feedback->date}}</td>
                                <td>{{$feedback->user_id}}</td>
                                <td><button type="button" class="btn btn-danger" onclick="delteDcol('{{$feedback->id}}')">Delete</button></td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection
@section('scripts')

    <script>
        $(document).ready(function() {
            $('#feedback-table').DataTable( {
//            scrollX: true,
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
        function delteDcol(val) {
            if (confirm("Are You Sure?"))
            {
                $.ajax({
                    url: "{{url('deleteFeedback')}}"+'/'+val,
                    complete: function(result){
                            window.location.reload();
                    }
                });
            }
        }
    </script>
@endsection
