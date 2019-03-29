<?php
$last =\Illuminate\Support\Facades\DB::table('driver_profile_status')->orderby('position', 'desc')->first();
if (isset($last->position))
{
    $position = $last->position + 1;
}
else
{
    $position=1;
}
?>
@extends('layouts.app')
@section('content')
<section class="content-header">
    <h1>
        New Column
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">

        <div class="box-body">
            <div class="row">
                <form method="post" action="{{url('new/column')}}">
                    @if( Session::has( 'error' ))
                        <center><p style="background-color: red;color: white;width: 50%">{{ Session::get( 'error' ) }}</p></center>
                    @endif
                    {{csrf_field()}}
                    <div class="form-group col-sm-12">
                        <label>English Column Name</label>
                        <input type="text" name="column_en" id="column" class="form-control" placeholder="Please enter the Column Name in English">
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Arabic Column Name</label>
                        <input type="text" name="column_ar" id="column" class="form-control" placeholder="Please enter the Column Name in Arabic">
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Position</label>
                        <input type="text" name="pos" id="pos" class="form-control" value="{{$position}}" placeholder="Please Enter Position">
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="" selected disabled>Select Type</option>
                            <option value="num">Number</option>
                            <option value="file">File</option>
                            <option value="text">Text</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-12">
                        <label>Validation</label>
                        <select name="validation" class="form-control" required>
                            <option value="" selected disabled>Select Validation</option>
                            <option value="required">Required</option>
                            <option value="optional">Optional</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-12">
                        <input type="submit" name="submit" value="Save" class="btn btn-primary">
                        <a href="{!! route('userProfiles.index') !!}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection