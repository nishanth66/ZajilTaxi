@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Booking Statuses</h1>
        <h1 class="pull-right">
            <button class="btn btn-primary pull-right"  style="margin-top: -10px;margin-bottom: 5px" data-toggle="modal" data-target="#addStatus">Add New</button>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-responsive" id="booking3-table">
                    <thead>
                    <tr>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($statuses as $status)
                        <tr>
                            <td>{{$status->name}}</td>
                            <td>
                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editStatus{{$status->id}}">Edit</button>
                                <button type="button" class="btn btn-danger" onclick="deleteStatus('{{$status->id}}')">Delete</button>
                            </td>

                            <div class="modal fade" id="editStatus{{$status->id}}" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <center><h4 class="modal-title">{{$status->name}}</h4></center>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{url('bookingStatus/edit')}}">
                                                {{csrf_field()}}
                                                <div class="form-group col-sm-12">
                                                    <label>Status: </label>
                                                    <input type="text" name="name" value="{{$status->name}}" class="form-control">
                                                    <input type="hidden" name="id" value="{{$status->id}}" class="form-control">
                                                </div>
                                                <div class="form-group col-sm-12">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </tr>
                    @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    </div>
    <div class="modal fade" id="addStatus" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <center><h4 class="modal-title">Add a Status</h4></center>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{url('bookingStatus/add')}}">
                        {{csrf_field()}}
                        <div class="form-group col-sm-12">
                            <label>Status: </label>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group col-sm-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                </div>
            </div>

        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#booking3-table').DataTable( {
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
        function deleteStatus(a)
        {
            if (confirm("Are You Sure?")) {
                $.ajax({
                    url: "{{url('bookStatusDelete')}}" + '/' + a,
                    success: function (result) {
                            window.location.reload();
                    }
                });
            }
        }
    </script>
@endsection

