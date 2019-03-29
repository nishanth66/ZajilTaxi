<?php
    use \App\Models\booking_status;
    $bookings = booking_status::orderby('position')->get();
    $i=1;
?>
<table class="table table-responsive" id="booking-table">
    <thead>
        <tr>
            <th>Position</th>
            <th>English Column Name</th>
            <th>Arabic Column Name</th>
            <th>Type</th>
            <th>Validation</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookings as $booking)

            <tr>
                <td>{{$booking->position}}</td>
                <td>{{$booking->column_name_en}}</td>
                <td>{{$booking->column_name_ar}}</td>
                <td>{{$booking->type_en}}</td>
                <td>{{$booking->validation}}</td>
                <td>
                    @if($booking->status == 'enabled')
                        <button type="button" class="btn btn-success">Enabled</button>
                    @else
                        <button type="button" class="btn btn-danger">Disabled</button>
                    @endif
                </td>
                <td>
                    @if($booking->status == 'enabled')
                        <button type="button" class="btn btn-danger" onclick="actionBtn({{$booking->id}})">Disable</button>
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#colEdit{{$i}}">Edit</button>
                    <a href="{{url("deleteColumn").'/'.$booking->id}}" style="color:red"> <i class="fa fa-trash" style="color: red;cursor: pointer;" aria-hidden="true"></i></a>
                    @else
                        <button type="button" class="btn btn-success" onclick="actionBtn({{$booking->id}})" style="width: 72px">Enable </button>
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#colEdit{{$i}}">Edit</button>
                    <a href="{{url("deleteColumn").'/'.$booking->id}}" style="color:red"> <i class="fa fa-trash" style="color: red;cursor: pointer;" aria-hidden="true"></i></a>
                        @endif
                </td>
            </tr>
            <div class="modal fade" id="colEdit{{$i}}" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Change the Column Name</h4>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{url('edit/Bookingcolumn')}}">
                                {{csrf_field()}}
                                <div class="form-group col-sm-12">
                                    <label>English Column Name : </label>
                                    <input type="text" class="form-control" value="{{$booking->column_name_en}}" name="column_name_en">
                                </div>
                                <div class="form-group col-sm-12">
                                    <label>Arabic Column Name : </label>
                                    <input type="text" class="form-control" value="{{$booking->column_name_ar}}" name="column_name_ar">
                                </div>
                                <div class="form-group col-sm-12">
                                    <label>Position : </label>
                                    <input type="text" class="form-control" value="{{$booking->position}}" name="pos">
                                </div>
                                <div class="form-group col-sm-12">
                                    <label>Type : </label>
                                    <Select name="type" class="form-control" required>
                                        <option value="" disabled>Select Type</option>
                                        <option value="text" <?php if ($booking->type_en== 'text') { echo "selected";} ?>>Text</option>
                                        <option value="file" <?php if ($booking->type_en== 'file') { echo "selected";} ?>>File</option>
                                        <option value="did" <?php if ($booking->type_en== 'did') { echo "selected";} ?>>Driver</option>
                                        <option value="num" <?php if ($booking->type_en== 'num') { echo "selected";} ?>>Number</option>
                                    </Select>
                                </div>

                                <div class="form-group col-sm-12">
                                    <label>Validation :</label>
                                    <select name="validation" class="form-control" required>
                                        <option  value="" selected disabled>Select Validation</option>
                                        <option  <?php if ($booking->validation== 'required') { echo "selected";} ?> value="required">Required</option>
                                        <option  <?php if ($booking->validation== 'optional') { echo "selected";} ?> value="optional">Optional</option>
                                    </select>
                                </div>
                                <input type="hidden" class="form-control" value="{{$booking->id}}" name="id">
                                <input type="hidden" class="form-control" value="{{$booking->position}}" name="prev_pos">
                                <input type="hidden" class="form-control" value="{{str_replace(" ","_",$booking->column_name_en)}}" name="prev">
                                <div class="modal-footer">
                                    <div class="form-group col-sm-12">
                                        <input type="submit" class="btn btn-primary" name="submit" value="Save">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>

                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
            <?php
            $i++;
            ?>
        @endforeach
    </tbody>

</table>