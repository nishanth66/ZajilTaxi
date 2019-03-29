@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1 class="pull-left">All Bookings</h1>
    <h1 class="pull-right">
<!--        <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! url('booking/column') !!}">Add New</a>-->
    </h1>
</section>
<div class="content">
    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body scrollable" id="scrollable">
            @include('booking.table2')
        </div>
    </div>
    <div class="text-center">

    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('#booking2-table').DataTable( {
            pageLength: 15,
            sorting:[[ 0, "desc" ]],
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
    function cancelBooking(a)
    {
        var id= a;
        if (confirm("Are You Sure?")) {
            $.ajax({
                url: "{{url('cancelBooking')}}" + '/' + id,
                success: function (result) {
                    if (result == 'Success') {
                        window.location.reload();
                    }
                }
            });
        }
    }
    function changeBookStatus(a)
    {
        var status = $('#status'+a).val();
//        if (confirm("Are You Sure?")) {
            $.ajax({
                url: "{{url('changeBookStatus')}}" + '/' + a + '/' + status,
                success: function (result) {
                        window.location.reload();
                }
            });
//        }
    }
    function saveTripPrice(id) {
        var price = $('#trip_price'+id).val();
        $.ajax({
            url: "{{url('saveTripPrice')}}"+'/'+id+'/'+price,
            success: function(result){
                $('.tripPrice');
                $("#scrollable").load(location.href + " #scrollable");
        }});
    }
    function assignDriver(id) {
        var driver = $('#driver'+id).val();
        $.ajax({
            url: "{{url('assignDriver')}}"+'/'+id+'/'+driver,
            success: function(result){
                $('.tripPrice');
                $("#scrollable").load(location.href + " #scrollable");
            }});
    }
    Array.prototype.forEach.call(document.querySelector("img"), function (elem) {
        elem.addEventListener("click", function () {
            elem.classList.toggle("enlarged");
        });
    });
</script>
@endsection

