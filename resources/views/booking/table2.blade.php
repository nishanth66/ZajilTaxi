<?php
use \App\Models\booking_status;
$bookings = booking_status::where('status','enabled')->get();
$books = \App\Models\booking::orderby('id','desc')->get();
$statuses = DB::table('status')->get();
//date_default_timezone_set('Asia/Dubai');
//$today = strtolower(date('l'));
//$today_short = substr($today, 0, 3);
//$available_drivers = DB::table('driver_available')->get();
//$now = time();
$drivers = \Illuminate\Support\Facades\DB::table('driver_profile')->where('status','accepted')->get();
?>
<table class="table table-responsive" id="booking2-table">
    <thead>
    <tr>
        <th>Order ID</th>
        <th>Customer ID</th>
        <th>Order Timing</th>
        <th>Trip Timing</th>
        <th>Trip Source</th>
        <th>Trip Destination</th>
        <th>Trip Price</th>
        <th>Driver</th>
        <th>Status</th>
        <th>Order Details</th>
        <th>Last Status Changed At</th>
        <th>Last Status Changed By</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i=1;
    ?>
    @foreach($books as $book)
        <tr>
            <td>{{$book->id}}</td>
            <td data-order="{{$book->user_id}}"><a class="bookingUser" href="{{url('customer/booking').'/'.$book->user_id}}">{{$book->user_id}}</a></td>
            <td>{{$book->date}}<br>{{$book->booking_time}}</td>
            <td>@if($book->first_trip_time != ""){{date('d/m/Y',strtotime(str_replace("/","-",$book->first_trip_time)))}} <br>{{date('h:i A',strtotime(str_replace("/","-",$book->first_trip_time)))}} @endif</td>
            <td>
                <a href="https://www.google.com/maps?q={{$book->source}}" target="_blank">{{$book->source}}</a><br>
                {{$book->source_description}}
            </td>
            <td>
                <a href="https://www.google.com/maps?q={{$book->destination}}" target="_blank">{{$book->destination}}</a><br>
                {{$book->destination_description}}
            </td>
            <td>{{$book->trip_price}}&ensp; <i class="fa fa-edit btn btn-default" data-toggle="modal" data-target="#tripPrice{{$book->id}}"></i></td>
            <td>
                <select id="driver{{$book->id}}" class="form-control bookingTableSelect" onchange="assignDriver('{{$book->id}}')">
                    <option value="" selected disabled>Seelct a Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{$driver->user_id}}" <?php if ($book->driver_id == $driver->user_id) { echo "selected"; } ?> >{{$driver->First_Name}}&ensp;{{$driver->Last_Name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select id="status{{$book->id}}" class="form-control bookingTableSelect" onchange="changeBookStatus('{{$book->id}}')">
                    <option value="" selected disabled>Select a Status</option>
                    @foreach($statuses as $status)
                        <option value="{{$status->name}}" <?php if($status->name == $book->status){ echo "selected";} ?>>{{$status->name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                @if(isset($book->image) && ($book->image != '' || !empty($book->image)))
                    <img src="{{asset('public/avatars').'/'.$book->image}}" class="statusImage">
                @else
                    <img src="{{asset('public/avatars/default.jpg')}}" class="statusImage">
                @endif

            </td>

            <div class="modal fade tripPrice" id="tripPrice{{$book->id}}" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <center>
                                <h4 class="modal-title">Edit Trip Price</h4>
                            </center>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-sm-12">
                                <label>Trip Price : </label>
                                <input type="number" name="trip_price" value="{{$book->trip_price}}" class="form-control" id="trip_price{{$book->id}}">
                            </div>
                            <div class="form-group col-sm-12">
                                <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="saveTripPrice('{{$book->id}}')">Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div>

                </div>
            </div>
            <td><a class="bookingUser" href="{{url('allBookings/show').'/'.$book->id}}"><button class="btn btn-info">Order Details</button></a></td>
            <td>{{$book->last_status_changed_at}}</td>
            <td>{{$book->last_status_changed_by}}</td>
            <td><a href="{{url('edit/booking').'/'.$book->id}}"><button type="button" class="btn btn-warning" >Edit</button> </a>
                <button type="button" class="btn btn-danger" onclick="cancelBooking('{{$book->id}}')">Delete</button>
            </td>

        </tr>

        <?php $i++; ?>
    @endforeach
    </tbody>

</table>

