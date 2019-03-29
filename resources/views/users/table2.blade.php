<?php
$columns1 = \Illuminate\Support\Facades\DB::table('customer_profile_status')->where('status','enabled')->orderby('position')->get();
$users1 = \Illuminate\Support\Facades\DB::table('customer_profile')->orderby('id')->get();
?>
<table class="table table-responsive" id="users2-table">
    <thead>
    <tr>
        @foreach($columns1 as $column)
        <?php
        $column1 = str_replace(' ','_',$column->column_name_en);
        $column_name_en = str_replace('-','_',$column1);
        ?>
        <th>{{$column_name_en}}</th>
        @endforeach
        <th>Phone Number</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>

    @foreach($users1 as $user)
    <tr>
        @foreach($columns1 as $column)
        <?php
        $column1 = str_replace(' ','_',$column->column_name_en);
        $column_name_en = str_replace('-','_',$column1);
        ?>
        @if($column->type_en == 'file' )
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
        <td><a href="{{url('edit/customers').'/'.$user->id.'/'.'Customer'}}"><button type="button" class="btn btn-default">Edit</button></a></td>
    </tr>
    @endforeach


    </tbody>

</table>