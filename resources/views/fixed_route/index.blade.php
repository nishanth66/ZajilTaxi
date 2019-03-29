@extends('layouts.app')

@section('content')
    <section class="content-header">
        @if( Session::has( 'error' ))
            <center><p style="background-color: red;color: white;width: 50%">{{ Session::get( 'error' ) }}</p></center>
        @endif
        <h1 class="pull-left">Fixed Routes</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! url('fixed/price') !!}">Add New</a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-responsive" id="fixed-table">
                    <thead>
                    <tr>
                        <th>Position</th>
                        <th>Source Point</th>
                        <th>Destination Point</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                      $i=1;
                    ?>
                        @foreach($fixed as $route)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$route->source_lat}},{{$route->source_long}}</td>
                                <td>{{$route->destination_lat}},{{$route->destination_long}}</td>
                                <td>{{$route->fixed_price}}</td>
                                <td>
                                    <div class='btn-group'>
                                        <a href="{!! url('fixed/route/show', [$route->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                                        <a href="{!! url('fixed/route/edit', [$route->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                       <i class="glyphicon glyphicon-trash btn btn-default btn-xs" style="color: red" onclick="deleteRoute('{{$route->id}}')"></i>
                                    </div>
                                </td>
                            </tr>
                        <?php
                         $i++;
                        ?>
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
            $('#fixed-table').DataTable( {
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
        function deleteRoute(id) {
            if(confirm("Are You Sure?"))
            {
                $.ajax({
                    url: "{{url('deleteFixed')}}"+'/'+id,
                    success: function(result){
                        window.location.reload();
                }});
            }
        }
    </script>
@endsection

