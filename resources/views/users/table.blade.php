<?php
$columns = \App\Models\user_profile_status::where('status','enabled')->orderby('position')->get();
$users = \App\Models\user_profile::orderby('id')->get();
$week = array("Saturday","Sunday","Monday", "Tuesday", "Wednesday","Thursday","Friday");
$z=0;
?>
<table class="table table-responsive" id="users-table">
    <thead>
    <tr>
        <th></th>
        @foreach($columns as $column)
            <?php
            $column1 = str_replace(' ','_',$column->column_name_en);
            $column_name_en = str_replace('-','_',$column1);
            ?>
            <th>{{$column_name_en}}</th>
        @endforeach
        <th>Phone Number</th>
        <th>Timings</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>

    @foreach($users as $user)
        <?php
                $driver_timings = \Illuminate\Support\Facades\DB::table('driver_available')->where('user_id',$user->user_id)->first();
        ?>
        <tr>
            <td>
                @if($user->login_status == 'true')
                    <i class="fa fa-circle" style="color: darkgreen" aria-hidden="true"></i>
                @else
                    <i class="fa fa-circle" style="color: grey" aria-hidden="true"></i>
                @endif
            </td>
            @foreach($columns as $column)
                <?php
                $column1 = str_replace(' ','_',$column->column_name_en);
                $column_name_en = str_replace('-','_',$column1);
                ?>
                    @if($column->type_en == 'file')
                            @if($user->$column_name_en != '' || !empty($user->$column_name_en))
                                <td><img src="{{$user->$column_name_en}}" alt="{{$column_name_en}}" style="width: 75px;height: 75px;"> </td>
                            @else
                                <td><img src="{{asset('public/avatars/default.jpg')}}" alt="{{$column_name_en}}" style="width: 75px;height: 75px;"> </td>
                            @endif
                    @elseif($column->type_en == 'num')
                        <td>{{$user->$column_name_en}}</td>
                @else
                        <td>{{$user->$column_name_en}}</td>
                @endif
            @endforeach
            <td>{{$user->user_id}}</td>
                <td>
                    @if(isset($driver_timings))
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#driverTime{{$user->id}}">Timings</button>
                    @else
                        <p class="driver-timing-table">Not Available</p>
                    @endif
                </td>
            @if($user->status == 'pending')
                <td><button type="button" class="btn btn-default">Pending</button></td>
            @elseif($user->status == 'accepted')
                <td><button type="button" class="btn btn-success">Accepted</button></td>
            @elseif($user->status == 'rejected')
                <td><button type="button" class="btn btn-danger">Rejected</button></td>
                @endif


            @if($user->status == 'pending')
                <td><button type="button" class="btn btn-success" onclick="acceptUser('{{$user->id}}','accepted')" style="width: 100%;">Accept</button>
                <button type="button" class="btn btn-danger" onclick="acceptUser('{{$user->id}}','rejected')" style="width: 100%;">Reject</button></td>
            @elseif(($user->status == 'accepted') || ($user->status == 'rejected'))
                <td><a href="{{url('edit/drivers').'/'.$user->id.'/'.'Driver'}}"><button type="button" class="btn btn-default" style="width:100%;">Edit</button></a></td>
            @endif
            @if(isset($driver_timings))
            <div class="modal fade" id="driverTime{{$user->id}}" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <center>
                                <h4 class="modal-title">{{$user->First_Name}}&ensp;{{$user->Last_Name}}</h4>
                            </center>
                        </div>
                        <div class="modal-body">
                            <div style="padding: 10px;border: 1px solid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <center>
                                                <h4>Week Name</h4>
                                            </center>
                                        </div>
                                        <div class="col-md-4">
                                            <center>
                                                <h4>Start Time</h4>
                                            </center>
                                        </div>
                                        <div class="col-md-4">
                                            <center>
                                                <h4>End Time</h4>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            @for($z=0;$z<count($week);$z++)
                                <?php
                                    $short_week = strtolower(mb_substr($week[$z],0,3));
                                    $driver_timings = (array)($driver_timings);
                                ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <center>
                                                <h6>{{$week[$z]}}</h6>
                                            </center>
                                        </div>
                                        <div class="col-md-4">
                                            <center>
                                                @if(isset($driver_timings[$short_week.'_from']) && ($driver_timings[$short_week.'_from'] != '' || !empty($driver_timings[$short_week.'_from'])) && ($driver_timings[$short_week.'_to'] != '' || !empty($driver_timings[$short_week.'_to'])))
                                                    <h6>{{$driver_timings[$short_week.'_from']}}</h6>
                                                @else
                                                    <h6>-</h6>
                                                @endif
                                            </center>
                                        </div>
                                        <div class="col-md-4">
                                            <center>
                                                @if(isset($driver_timings[$short_week.'_from']) && ($driver_timings[$short_week.'_from'] != '' || !empty($driver_timings[$short_week.'_from'])) && ($driver_timings[$short_week.'_to'] != '' || !empty($driver_timings[$short_week.'_to'])))
                                                    <h6>{{$driver_timings[$short_week.'_to']}}</h6>
                                                @else
                                                    <h6>-</h6>
                                                @endif
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>
            @endif

        </tr>
    @endforeach


    </tbody>

</table>