<?php
use \App\Models\user_profile_status;
$columns = \Illuminate\Support\Facades\DB::table('customer_profile_status')->orderby('position')->get();
?>
<table class="table table-responsive" id="userProfiles2-table">
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
    <?php
    $i=1;
    ?>
    @foreach($columns as $column)

        <tr>
            <td>{!! $column->position !!}</td>
            <td>{!! $column->column_name_en !!}</td>
            <td>{!! $column->column_name_ar !!}</td>
            <td>{!! $column->type_en !!}</td>
            <td>{!! $column->validation !!}</td>
            @if($column->status == 'enabled')
                <td><button type="button" class="btn btn-success">Enabled </button> </td>
                <td><button type="button" class="btn btn-danger" onclick="actionBtn({{$column->id}})">Disable</button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#colEdit{{$i}}">Edit</button>
                    <a href="{{url("deleteCColumn").'/'.$column->id}}" style="color:red"> <i class="fa fa-trash" style="color: red;cursor: pointer;" aria-hidden="true"></i></a>

                </td>
            @elseif($column->status == 'disabled')
                <td><button type="button" class="btn btn-danger">Disabled</button> </td>
                <td><button type="button" class="btn btn-success" onclick="actionBtn({{$column->id}})" style="width: 72px">Enable </button>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#colEdit{{$i}}">Edit</button>
                    <a href="{{url("deleteCColumn").'/'.$column->id}}" style="color:red"> <i class="fa fa-trash" style="color: red;cursor: pointer;" aria-hidden="true"></i></a>
                </td>
            @endif

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
                        <form method="post" action="{{url('edit/customer/column')}}">
                            {{csrf_field()}}
                            <div class="form-group col-sm-12">
                                <label>English Column Name</label>
                                <input type="text" class="form-control" value="{{$column->column_name_en}}" name="column_name_en">
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Arabic Column Name</label>
                                <input type="text" class="form-control" value="{{$column->column_name_ar}}" name="column_name_ar">
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Position</label>
                                <input type="text" class="form-control" value="{{$column->position}}" name="pos">
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="num" <?php if ($column->type_en == 'num' || $column->type_ar == 'num'){ echo "selected";} ?>>Number</option>
                                    <option value="file" <?php if ($column->type_en == 'file' || $column->type_ar == 'file'){ echo "selected";} ?> >File</option>
                                    <option value="text" <?php if ($column->type_en == 'text' || $column->type_ar == 'text'){ echo "selected";} ?> >Text</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Validation</label>
                                <select name="validation" class="form-control" required>
                                    <option value="" selected disabled>Select Validation</option>
                                    <option value="required" <?php if ($column->validation == 'required'){ echo "selected";} ?>>Required</option>
                                    <option value="optional" <?php if ($column->validation == 'optional'){ echo "selected";} ?> >Optional</option>
                                </select>

                            </div>
                            <input type="hidden" class="form-control" value="{{$column->position}}" name="prev_pos">
                            <input type="hidden" class="form-control" value="{{$column->id}}" name="id">
                            <input type="hidden" class="form-control" value="{{$column->column_name_en}}" name="prev_en">
                            <input type="hidden" class="form-control" value="{{$column->column_name_ar}}" name="prev_ar">
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