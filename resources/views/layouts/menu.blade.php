@if(Auth::user()->status == 'admin')
    <br/>
    <li class="{{ Request::is('broadcast/message*') ? 'active' : '' }}">
        <a href="{!! url('broadcast/message') !!}"><i class="fa fa-comment-o"></i><span>Broadcast Message</span></a>
    </li>
    <li class="{{ Request::is('broadcast/push*') ? 'active' : '' }}">
        <a href="{!! url('broadcast/push') !!}"><i class="fa fa-comment-o"></i><span>Broadcast Notification</span></a>
    </li>

    <li class="{{ Request::is('kmPrice*') ? 'active' : '' }}">
        <a href="{!! url('kmPrice') !!}"><i class="fa fa-money"></i><span>Kilometer Price</span></a>
    </li>
    <li class="{{ Request::is('parkingFees*') ? 'active' : '' }}">
        <a href="{!! url('parkingFees') !!}"><i class="fa fa-money"></i><span>Daily Parking Fees</span></a>
    </li>
    <li class="{{ Request::is('allDrivers*') || Request::is('edit/drivers*')? 'active' : '' }}">
        <a href="{!! url('allDrivers') !!}"><i class="fa fa-user"></i><span>Drivers Confirmation</span></a>
    </li>
    <li class="{{ Request::is('allCustomers*') || Request::is('edit/customers*')? 'active' : '' }}">
        <a href="{!! url('allCustomers') !!}"><i class="fa fa-user"></i><span>Customers</span></a>
    </li>
    <li class="{{ Request::is('show/column*') || Request::is('add/column*')? 'active' : '' }}">
        <a href="{!! url('show/columns') !!}"><i class="fa fa-columns"></i><span>Driver Profile Fields</span></a>
    </li>
    <li class="{{ Request::is('show/customer*') || Request::is('add/customer/column*')? 'active' : '' }}">
        <a href="{!! url('show/customer/columns') !!}"><i class="fa fa-columns"></i><span>Customer Profile Fields</span></a>
    </li>
    <li class="{{ Request::is('allBookings*') ||Request::is('edit/booking*')|| Request::is('customer/booking*') ? 'active' : '' }}">
        <a href="{!! url('allBookings') !!}"><i class="fa fa-book"></i><span>All Bookings</span></a>
    </li>
    <li class="{{ Request::is('booking*') ? 'active' : '' }}">
        <a href="{!! url('booking') !!}"><i class="fa fa-book"></i><span>Booking Fields</span></a>
    </li>

    {{--<li class="{{ Request::is('statusOfBooking*') ? 'active' : '' }}">--}}
        {{--<a href="{!! url('statusOfBooking') !!}"><i class="fa fa-book"></i><span>Booking Statuses</span></a>--}}
    {{--</li>--}}
    <li class="{{ Request::is('dynamic*') ? 'active' : '' }}">
        <a href="{!! url('dynamic/page') !!}"><i class="fa fa-file"></i><span>Terms and Conditions</span></a>
    </li>
    <li class="{{ Request::is('fare*') ? 'active' : '' }}">
        <a href="{!! url('fare/charts') !!}"><i class="fa fa-file"></i><span>Fare Charts</span></a>
    </li>
    <li class="{{ Request::is('how*') ? 'active' : '' }}">
        <a href="{!! url('how/works') !!}"><i class="fa fa-file"></i><span>How it Works</span></a>
    </li>
    <li class="{{ Request::is('other*') ? 'active' : '' }}">
        <a href="{!! url('other/services') !!}"><i class="fa fa-file"></i><span>Other Services</span></a>
    </li>
    <li class="{{ Request::is('fixed*') ? 'active' : '' }}">
        <a href="{!! url('fixed/route') !!}"><i class="fa fa-money"></i><span>Fixed Route Price</span></a>
    </li>
    <li class="{{ Request::is('minimum*') ? 'active' : '' }}">
        <a href="{!! url('minimum/price') !!}"><i class="fa fa-money"></i><span>Minimum Trip Price</span></a>
    </li>
    <li class="{{ Request::is('feedback*') ? 'active' : '' }}">
        <a href="{!! url('feedbacks') !!}"><i class="fa fa-comments-o"></i><span>Feedbacks</span></a>
    </li>
@endif