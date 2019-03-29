<?php

namespace App\Http\Controllers;

use App\Models\app_users;
use App\Models\user_profile;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use \App\Models\booking;
use \App\Models\booking_status;
use \App\Models\fixed_routes;
use \App\Models\feedback;
use \Flash;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function addColumn()
    {
        return view('booking/column');
    }
    public function newColumn(Request $request)
    {
        $last =DB::table('booking_status')->orderby('position', 'desc')->first();
        if (isset($last->position)) {
            $last_pos = $last->position;
        }
        else
        {
            $last_pos=1;
        }
        $current_pos = (int)$request->pos;
        if (DB::table('booking_status')->where('position',$request->pos)->exists())
        {
//            $pos = $request->pos;
            for($i=$current_pos;$i<=$last_pos;$i++)
            {
                $newPos=(int)$i+1;
                $input5['position']=$newPos;
                $value=DB::table('booking_status')->where('position',$i)->orderby('position','desc')->first();
                $value_id = $value->id;
                DB::table('booking_status')->whereId($value_id)->update($input5);
            }
        }

        if (Auth::user()->status == 'admin')
        {
            $name=str_replace(' ','_',$request->column_en);
            $name1=$request->column_ar;
            try
            {
                Schema::table('booking', function ($table) use ($name) {
                    $table->text('' . $name . '')->nullable();
                });
            }
            catch (QueryException $e){
                \Laracasts\Flash\Flash::error($e->getMessage());
                if (DB::table('booking_status')->where('column_name_en',$request->column_en)->where('column_name_ar',$name1)->exists() == 0)
                {
                    $input['column_name_en'] = $request->column_en;
                    $input['column_name_ar'] = $name1;
                    $input['type_en'] = $request->type;
                    $input['type_ar'] = $request->type;
                    $input['validation'] = $request->validation;
                    $input['position'] = $request->pos;
                    DB::table('booking_status')->insert($input);
                }
                return redirect()->back();
            }

            Artisan::call("migrate");
            if ((int)$request->pos > $last_pos)
            {
                $request->pos = (int)$last_pos + 1;
            }
            $input['column_name_en'] = $request->column_en;
            $input['column_name_ar'] = $name1;
            $input['type_en'] = $request->type;
            $input['type_ar'] = $request->type;
            $input['validation'] = $request->validation;
            $input['position'] = $request->pos;
            DB::table('booking_status')->insert($input);
        }
        return redirect('booking');
    }

    public function addProfile()
    {
        if (Auth::user()->status == 'admin') {
            return view('column/feild');
        }
        else
        {
            return redirect('/home');
        }
    }
    public function booking()
    {
        if (Auth::user()->status == 'admin') {
            return view('booking/index');
        }
        else
        {
            return redirect('/home');
        }
    }
    public function changeStatus($id)
    {
        $current=booking_status::whereId($id)->first();
        if ($current->status == 'enabled')
        {
            $input['status'] = 'disabled';
            if (Auth::user()->status == 'admin') {
                booking_status::whereId($id)->update($input);
            }
            return "Success";
        }
        else if ($current->status == 'disabled')
        {
            $input['status'] = 'enabled';
            if (Auth::user()->status == 'admin') {
                booking_status::whereId($id)->update($input);
            }
            return "Success";
        }
        else
            return "failed";

    }
    public function editColumn(Request $request)
    {
        $check = DB::table('booking_status')->where('position',$request->pos)->first();
        if (!isset($check->position))
        {
            return Redirect('booking')->withError( 'Please Specify the propper Position' );
        }
        $last =\Illuminate\Support\Facades\DB::table('booking_status')->orderby('position','desc')->first();
        $last_pos = $last->position;
        $current_pos = (int)$request->pos;
        $prev_pos = (int)$request->prev_pos;
        if (DB::table('booking_status')->where('position',$current_pos)->exists()) {
            if ($current_pos < $prev_pos) {
//                return "aaaaa";
                $prev_pos = (int)$request->prev_pos - 1;
                for ($i = $prev_pos; $i >= $current_pos; $i--) {
                    $newPos = (int)$i + 1;
                    $input5['position'] = $newPos;
                    $value = DB::table('booking_status')->where('position', $i)->first();
                    $value_id = $value->id;
                    DB::table('booking_status')->whereId($value_id)->update($input5);
                }
            } elseif ($current_pos > $prev_pos) {
//                return "bbbb";
                $prev_pos = (int)$request->prev_pos + 1;
                for ($i = $prev_pos; $i<= $current_pos ; $i++) {
                    $newPos = (int)$i - 1;
                    $input5['position'] = $newPos;
                    $value = DB::table('booking_status')->where('position', $i)->first();
                    $value_id = $value->id;
//                    return $value_id;
                    DB::table('booking_status')->whereId($value_id)->update($input5);
                }
            }
        }



        $prev = $request->prev;
        $column11 = $request->column_name_en;
        $column = str_replace(' ','_',$request->column_name_en);
        $column1 = $request->column_name_ar;
        $id = $request->id;
        $input['column_name_en']=$column11;
        $input['column_name_ar']=$column1;
        $input['validation']=$request->validation;
        $input['type_en']=$request->type;
        $input['type_ar']=$request->type;
        $input['position']=$request->pos;
        if (Auth::user()->status == 'admin') {
            try
            {
                DB::table('booking_status')->whereId($id)->update($input);
                Schema::table('booking', function ($table) use ($prev, $column) {
                    $table->renameColumn($prev, $column);
                });
            }
            catch (QueryException $ex){
                \Laracasts\Flash\Flash::error($ex->getMessage());
                if (DB::table('booking_status')->where('column_name_en',$request->column_name_en)->where('column_name_ar',$request->column_name_ar)->exists() == 0)
                {
                    $input['column_name_en'] = $request->column_name_en;
                    $input['column_name_ar'] = $request->column_name_ar;
                    $input['type_en'] = $request->type;
                    $input['type_ar'] = $request->type;
                    $input['validation'] = $request->validation;
                    $input['position'] = $request->pos;
                    DB::table('booking_status')->insert($input);
                }
                return redirect()->back();
            }

        }
        return redirect('booking');
    }

    public function deleteColumn($id)
    {
        $last =DB::table('booking_status')->orderby('position','desc')->first();
        $last_pos = (int)$last->position;
        $column = DB::table('booking_status')->whereId($id)->first();
        $e = $column->column_name_en;
        $p = (int)$column->position + 1;

        for ($i=$p;$i<=$last_pos;$i++)
        {
            $newPos=(int)$i-1;
            $input5['position']=$newPos;
            $value=DB::table('booking_status')->where('position',$i)->first();
            $value_id = $value->id;
            DB::table('booking_status')->whereId($value_id)->update($input5);
        }


        $english = str_replace(' ','_',$e);
        $col_en = str_replace('-','_',$english);
//        return $col_en;



        if (Schema::hasColumn('booking', $col_en)) {
            Schema::table('booking', function ($table) use ($col_en) {
                $table->dropColumn($col_en);
            });
        }
        else
        {
            return redirect('booking');
        }
        DB::table('booking_status')->whereId($id)->delete();
        return redirect('booking');
    }



    public function showUsers()
    {
        return view('users/index');
    }

    public function showCustomers()
    {
        return view('users/index2');
    }

    public function allBooking()
    {
        return view('booking/index2');
    }

    public function editBooking($id)
    {
        return view('booking/edit',compact('id'));
    }

    public function editUser($id,$status)
    {
        return view('users/edit',compact('id','status'));
    }

    public function editCustomer($id,$status)
    {
        return view('users/edit2',compact('id','status'));
    }

    public function broadcastMessage()
    {
        return view('message/field');
    }

    public function broadcastPush()
    {
        return view('message/push_field');
    }

    public function showPageDetails()
    {
        $f = base_path('public').'/termsText.html';
        $content = file_get_contents($f);
        return view('dynamicWebPage/page')->with('content',$content);
    }

    public function userStatus($id,$status)
    {
        $input['status'] = $status;
        user_profile::whereId($id)->update($input);
        return "Success";
    }

    public function editUsers(Request $request)
    {
        foreach($request->except('_token','status') as $key=>$value)
        {
            $key1 = str_replace(' ','_',$key);
            $input[$key1] = $value;
        }
        if(isset($_FILES)){
            foreach ($_FILES as $fName => $fArray){
                if(isset($input[$fName])) {
                    $photoName = asset('public/avatars') . '/' . rand(1, 9999999) . time() . '.' . $input[$fName]->getClientOriginalExtension();
                    $input[$fName]->move(public_path('avatars'), $photoName);
                    $input[$fName] = $photoName;
                }
            }
        }

        $id=DB::table('driver_profile')->whereId($request->id)->update($input);
        return redirect('allDrivers');
    }
    public function editCustomers(Request $request)
    {
        foreach($request->except('_token','status') as $key=>$value)
        {
            $key1 = str_replace(' ','_',$key);
            $input[$key1] = $value;
        }
        if(isset($_FILES)){
            foreach ($_FILES as $fName => $fArray){
                if(isset($input[$fName])) {
                    $photoName = asset('public/avatars') . '/' . rand(1, 9999999) . time() . '.' . $input[$fName]->getClientOriginalExtension();
                    $input[$fName]->move(public_path('avatars'), $photoName);
                    $input[$fName] = $photoName;
                }
            }
        }

        $id=DB::table('customer_profile')->whereId($request->id)->update($input);
            return redirect('allCustomers');


    }
    public function bookingChange(Request $request)
    {
        $req = $request->except('_token');
        foreach($req as $key=>$value)
        {
            $key1 = str_replace(' ','_',$key);
            $input[$key1] = $value;
        }
        if(isset($_FILES)){
        foreach ($_FILES as $fName => $fArray){
            if(isset($input[$fName])) {
                $photoName = asset('public/avatars') . '/' . rand(1, 9999999) . time() . '.' . $input[$fName]->getClientOriginalExtension();
                $input[$fName]->move(public_path('avatars'), $photoName);
                $input[$fName] = $photoName;
            }
        }
    }
        $id = DB::table('booking')->whereId($request->id)->update($input);
        return redirect('allBookings');
    }
    public function cancelBooking($id)
    {
        $id = DB::table('booking')->whereId($id)->delete();
        return "Success";
    }
    public function sendMessage(Request $request)
    {
        if (isset($request->customer))
        {
            $users = DB::table('customer_profile')->get();
        }
        elseif (isset($request->driver))
        {
            $users = DB::table('driver_profile')->where('status','accepted')->get();
        }
        $data = [];
        foreach($users as $user)
        {
            $headers = array(
                'Host: ismartsms.net',
                'Content-Type: application/json',
                'Cache-Control: no-cache'
            );
            $msg = $request->message;
            $mobileNum = $user->user_id;
            $requestData = [
                "UserID" => "zajil_ws",
                "Password" => "J#cjsw19",
                "Message" => $msg,
                "Language" => "0",
                "MobileNo" => array($mobileNum),
                "ScheddateTime" => "01/22/2017 00:00:00",
                "RecipientType" => "1"
            ];
            $ch = curl_init("https://ismartsms.net/RestApi/api/SMS/PostSMS");
            $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
                CURLOPT_AUTOREFERER => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
                CURLOPT_TIMEOUT => 20,          // timeout on response
                CURLOPT_POST => 1,            // i am sending post data
                CURLOPT_POSTFIELDS => json_encode($requestData),    // this are my post vars
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_VERBOSE => 1,
                CURLOPT_HTTPHEADER => $headers
            );
            curl_setopt_array($ch, $options);
            $data += json_decode(curl_exec($ch), true);
            curl_close($ch);
//            foreach($data as $da)

        }
        if (isset($data['Code'])) {
            if ($data['Code'] == "1") {
                Session::flash('success', 'Message Sent Succesfully');
                return redirect()->back();
            } else
                return Redirect()->back()->withError('' . $data['Message'] . '' . ' Please Try Again');
        }
        else
        {
            return Redirect()->back()->withError('Something Went wrong. Please try again');
        }
    }
    public function sendPush(Request $request)
    {
        if (isset($request->customer))
        {
            $users = DB::table('customer_profile')->get();
            $pushFor= "customer_notif";
        }
        elseif (isset($request->driver))
        {
            $users = DB::table('driver_profile')->where('status','accepted')->get();
	        $pushFor= "driver_notif";
        }
        foreach($users as $user)
        {
            $mobileNum = $user->user_id;
//            return $mobileNum;
	        if($appUser=app_users::where('mobile_number',$mobileNum)->exists()){
		        $appUser=app_users::where('mobile_number',$mobileNum)->first();
		        if($appUser->device_token != ""){
			        $this->pushNotification($request->message,"Notification from Zajil",$appUser->device_token,$pushFor);
		        }
	        }
        }
        Session::flash('success', 'Notifications Sent Successfully.');
        return redirect()->back();
    }
    public function kmPrice(){
        if (DB::table('km_price')->exists())
        {
            $km_price1=DB::table('km_price')->get();
            $km_price=$km_price1[0]->km_price;
        }
        else
        {
            $km_price = 0;
        }
		return view('kmPrice',compact('km_price'));
    }
    public function kmPriceSave(Request $request){
        if (DB::table('km_price')->exists())
        {
            DB::table('km_price')->update(['km_price'=>$request->km_price]);
            Flash::success('Kilimeter Price Updated Successfully.');
            return redirect('kmPrice');
        }
        else
        {
            DB::table('km_price')->insert(['km_price'=>$request->km_price]);
            Flash::success('Kilimeter Price Updated Successfully.');
            return redirect('kmPrice');
        }

    }
    public function parkingFees(){
	    $parkingFees1=DB::table('daily_parking_fee')->first();
		if(!empty($parkingFees1)){
			$shaded=$parkingFees1->shaded;
			$unshaded=$parkingFees1->unshaded;
		}
		else
		{
			$shaded=0;
			$unshaded=0;
		}
	    return view('parking_fees',compact('shaded','unshaded'));
    }
    public function parkingFeesSave(Request $request){
        if (DB::table('daily_parking_fee')->exists())
        {
            DB::table('daily_parking_fee')->update(['shaded'=>$request->shaded,'unshaded'=>$request->unshaded]);
            Flash::success('Daily Parking Fees Updated Successfully.');
            return redirect('parkingFees');
        }
        else
        {
            DB::table('daily_parking_fee')->insert(['shaded'=>$request->shaded,'unshaded'=>$request->unshaded]);
            Flash::success('Daily Parking Fees Updated Successfully.');
            return redirect('parkingFees');
        }

    }
    public function savePage(Request $request)
    {
        $file = base_path('public').'/terms.html';
        $file2 = base_path('public').'/termsText.html';
        // Open the file to get existing content
        // Append a new person to the file
        $current = '<!DOCTYPE html>
                                <html>
                                <head>
                                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                                    <meta charset="utf-8">
                                    <style>
                                    @media screen and (min-width: 480px) and (max-width: 768px) {
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 70px;
                                        }
                                        h2{
                                            font-size: 68px;
                                        }
                                        h3{
                                            font-size: 50px;
                                        }
                                        h4{
                                            font-size: 42px;
                                        }
                                        h5{
                                            font-size: 38px;
                                        }
                                        h6{
                                            font-size: 32px;
                                        }
                                        p{
                                            font-size: 35px;
                                        }
                                        div{
                                            display: block;
                                            font-size: 30px;
                                        }
                                        td{
                                            font-size: 40px;
                                        }
                                        th{
                                            font-size: 40px;
                                        }
                                        div
                                        {
                                            width: 80%;
                                            font-size: 30px !important;
                                            margin-left: 11%;
                                        }
                                       }
                                       @media (min-width: 769px) and (max-width: 1024px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       @media (min-width: 1025px) and (max-width: 1280px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        }
                                        @media (min-width: 1281px) {
                                            div
                                            {
                                            width: 80%;
                                            font-size: 18px !important;
                                            margin-left: 11%;
                                            }
                                            img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       
                                       
                                      
                                    </style>
                                </head>
                                <body>'.$request->terms.'
                                
                                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                                <script>
                                    var w=$( window ).width();
                                   	$( "img" ).each(function() {
                                       if($(this).width() > w-15){
                                           $(this).attr("width",w-15);
                                       }
                                    });
                                </script>
                                </body>
                               
                                </html>';
        // Write the contents back to the file
        file_put_contents($file, $current);
        file_put_contents($file2, $request->terms);
        return redirect('dynamic/page');
    }

    public function fareCharts()
    {
        $f = base_path('public').'/fareText.html';
        $content = file_get_contents($f);
        return view('dynamicWebPage/fare')->with('content',$content);
    }

    public function saveFare(Request $request)
    {
        $file = base_path('public').'/fare.html';
        $file2 = base_path('public').'/fareText.html';
        // Open the file to get existing content
        // Append a new person to the file
        $current = '<!DOCTYPE html>
                                <html>
                                <head>
                                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                                    <meta charset="utf-8">
                                    <style>
                                    @media screen and (min-width: 480px) and (max-width: 768px) {
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 70px;
                                        }
                                        h2{
                                            font-size: 68px;
                                        }
                                        h3{
                                            font-size: 50px;
                                        }
                                        h4{
                                            font-size: 42px;
                                        }
                                        h5{
                                            font-size: 38px;
                                        }
                                        h6{
                                            font-size: 32px;
                                        }
                                        p{
                                            font-size: 35px;
                                        }
                                        div{
                                            display: block;
                                            font-size: 30px;
                                        }
                                        td{
                                            font-size: 40px;
                                        }
                                        th{
                                            font-size: 40px;
                                        }
                                        div
                                        {
                                            width: 80%;
                                            font-size: 30px !important;
                                            margin-left: 11%;
                                        }
                                       }
                                       @media (min-width: 769px) and (max-width: 1024px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       @media (min-width: 1025px) and (max-width: 1280px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        }
                                        @media (min-width: 1281px) {
                                            div
                                            {
                                            width: 80%;
                                            font-size: 18px !important;
                                            margin-left: 11%;
                                            }
                                            img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       
                                       
                                      
                                    </style>
                                </head>
                                <body>'.$request->fare.'
                                
                                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                                <script>
                                    var w=$( window ).width();
                                   	$( "img" ).each(function() {
                                       if($(this).width() > w-15){
                                           $(this).attr("width",w-15);
                                       }
                                    });
                                </script>
                                </body>
                               
                                </html>';
        // Write the contents back to the file
        file_put_contents($file, $current);
        file_put_contents($file2, $request->fare);
        return redirect('fare/charts');
    }
    public function howWorks()
    {
        $f = base_path('public').'/workText.html';
        $content = file_get_contents($f);
        return view('dynamicWebPage/work')->with('content',$content);
    }


    public function saveWork(Request $request)
    {
        $file = base_path('public').'/work.html';
        $file2 = base_path('public').'/workText.html';
        // Open the file to get existing content
        // Append a new person to the file
        $current = '<!DOCTYPE html>
                                <html>
                                <head>
                                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                                    <meta charset="utf-8">
                                    <style>
                                    @media screen and (min-width: 480px) and (max-width: 768px) {
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 70px;
                                        }
                                        h2{
                                            font-size: 68px;
                                        }
                                        h3{
                                            font-size: 50px;
                                        }
                                        h4{
                                            font-size: 42px;
                                        }
                                        h5{
                                            font-size: 38px;
                                        }
                                        h6{
                                            font-size: 32px;
                                        }
                                        p{
                                            font-size: 35px;
                                        }
                                        div{
                                            display: block;
                                            font-size: 30px;
                                        }
                                        td{
                                            font-size: 40px;
                                        }
                                        th{
                                            font-size: 40px;
                                        }
                                        div
                                        {
                                            width: 80%;
                                            font-size: 30px !important;
                                            margin-left: 11%;
                                        }
                                       }
                                       @media (min-width: 769px) and (max-width: 1024px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       @media (min-width: 1025px) and (max-width: 1280px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        }
                                        @media (min-width: 1281px) {
                                            div
                                            {
                                            width: 80%;
                                            font-size: 18px !important;
                                            margin-left: 11%;
                                            }
                                            img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       
                                       
                                      
                                    </style>
                                </head>
                                <body>'.$request->work.'
                                
                                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                                <script>
                                    var w=$( window ).width();
                                   	$( "img" ).each(function() {
                                       if($(this).width() > w-15){
                                           $(this).attr("width",w-15);
                                       }
                                    });
                                </script>
                                </body>
                               
                                </html>';
        // Write the contents back to the file
        file_put_contents($file, $current);
        file_put_contents($file2, $request->work);
        return redirect('how/works');
    }
    public function otherService()
    {
        $f = base_path('public').'/otherText.html';
        $content = file_get_contents($f);
        return view('dynamicWebPage/other')->with('content',$content);
    }
    public function saveOther(Request $request)
    {
        $file = base_path('public').'/other.html';
        $file2 = base_path('public').'/otherText.html';
        // Open the file to get existing content
        // Append a new person to the file
        $current = '<!DOCTYPE html>
                                <html>
                                <head>
                                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                                    <meta charset="utf-8">
                                    <style>
                                    @media screen and (min-width: 480px) and (max-width: 768px) {
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 70px;
                                        }
                                        h2{
                                            font-size: 68px;
                                        }
                                        h3{
                                            font-size: 50px;
                                        }
                                        h4{
                                            font-size: 42px;
                                        }
                                        h5{
                                            font-size: 38px;
                                        }
                                        h6{
                                            font-size: 32px;
                                        }
                                        p{
                                            font-size: 35px;
                                        }
                                        div{
                                            display: block;
                                            font-size: 30px;
                                        }
                                        td{
                                            font-size: 40px;
                                        }
                                        th{
                                            font-size: 40px;
                                        }
                                        div
                                        {
                                            width: 80%;
                                            font-size: 30px !important;
                                            margin-left: 11%;
                                        }
                                       }
                                       @media (min-width: 769px) and (max-width: 1024px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       @media (min-width: 1025px) and (max-width: 1280px) {
                                        div
                                        {
                                        width: 80%;
                                        font-size: 18px !important;
                                        margin-left: 11%;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                        }
                                        @media (min-width: 1281px) {
                                            div
                                            {
                                            width: 80%;
                                            font-size: 18px !important;
                                            margin-left: 11%;
                                            }
                                            img{
                                            width:80% !important;
                                            margin-left: 2% !important;
                                            height: auto;
                                        }
                                         h1{
                                            font-size: 2.5rem;
                                        }
                                        h2{
                                            font-size: 2rem;
                                        }
                                        h3{
                                            font-size: 1.75rem;
                                        }
                                        h4{
                                            font-size: 1.5rem;
                                        }
                                        h5{
                                            font-size: 1.25rem;
                                        }
                                        h6{
                                            font-size: 1rem;
                                        }
                                        p{
                                            font-size: 25px;
                                        }
                                        td{
                                            font-size: 30px;
                                        }
                                        th{
                                            font-size: 30px;
                                        }
                                        }
                                       
                                       
                                      
                                    </style>
                                </head>
                                <body>'.$request->other.'
                                
                                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                                <script>
                                    var w=$( window ).width();
                                   	$( "img" ).each(function() {
                                       if($(this).width() > w-15){
                                           $(this).attr("width",w-15);
                                       }
                                    });
                                </script>
                                </body>
                               
                                </html>';
        // Write the contents back to the file
        file_put_contents($file, $current);
        file_put_contents($file2, $request->other);
        return redirect('other/services');
    }




    public function fixedRoute()
    {
        $fixed = fixed_routes::get();
        return view('fixed_route.index')->with('fixed',$fixed);
    }
    public function fixedPrice()
    {
        return view('fixed_route.create');
    }
    public function faxedRoutee(Request $request)
    {
        $input = $request->except('_token');
        fixed_routes::create($input);
        return redirect('fixed/route');
    }
    public function fixedEdit($id)
    {
        $fixed = fixed_routes::whereId($id)->first();
        return view('fixed_route.edit')->with('fixed',$fixed);
    }
    public function faxedRouteEdit($id,Request $request)
    {
        $update = $request->except('_token');
        fixed_routes::whereId($id)->update($update);
        return redirect('fixed/route');
    }
    public function fixedShow($id)
    {
        $fixed = fixed_routes::whereId($id)->first();
        return view('fixed_route.show')->with('fixed',$fixed);
    }
    public function deleteFixed($id)
    {
        fixed_routes::whereId($id)->delete();
        return "success";
    }
    public function feedbacks()
    {
        return view('feedback.feedback');
    }
    public function feedbacksDelete($id)
    {
        $feedbacks = feedback::whereId($id)->delete();
        return "success";
    }
    public function minPrice()
    {
        if (DB::table('min_trip_price')->exists())
        {
            $price = DB::table('min_trip_price')->first();
            $price = $price->price;
        }
        else
        {
            $price = 0;
        }
        return view('minimum_price.show')->with('price',$price);
    }
    public function minPriceEdit()
    {
        if (DB::table('min_trip_price')->exists())
        {
            $price = DB::table('min_trip_price')->first();
            $price = $price->price;
        }
        else
        {
            $price = 0;
        }
        return view('minimum_price.edit')->with('price',$price);
    }
    public function minPriceSave(Request $request)
    {
        $input = $request->except('_token');
        if (DB::table('min_trip_price')->exists())
        {
            DB::table('min_trip_price')->update($input);
        }
        else
        {
            DB::table('min_trip_price')->insert($input);
        }
        return redirect('minimum/price');
    }
    public function bookCust($id)
    {
        $user = DB::table('customer_profile')->where('user_id',$id)->first();
        return view('booking.customer')->with('user',$user);
    }
    public function bookShowAll($id)
    {
        $book = DB::table('booking')->where('id',$id)->first();
        return view('booking.show')->with('book',$book);
    }
    public function bookStatus()
    {
        $statuses = DB::table('status')->get();
        return view('booking.statusIndex')->with('statuses',$statuses);
    }
    public function bookStatusDelete($id)
    {
        DB::table('status')->whereId($id)->delete();
        return "success";
    }
    public function bookStatusEdit(Request $request)
    {
        $update = $request->except('id','_token');
        DB::table('status')->whereId($request->id)->update($update);
        return redirect('statusOfBooking');
    }
    public function bookStatusAdd(Request $request)
    {
        $input = $request->except('_token');
        DB::table('status')->insert($input);
        return redirect('statusOfBooking');
    }
    public function changeBookStatus($id,$status)
    {
        $booking1['status'] = $status;
	    date_default_timezone_set('Asia/Muscat');
	    $booking1['last_status_changed_at'] = date("Y-m-d H:i:s");
	    $booking1['last_status_changed_by'] = "Admin";
	    $booking = DB::table('booking')->whereId($id)->first();
	    $appUser=app_users::where('mobile_number',$booking->user_id)->first();
	    if($appUser->device_token != ""){
		    $this->pushNotification("Booking's status changed to ".$status,"Status Changed",$appUser->device_token,"customer_notif");
	    }
	    if(app_users::where('mobile_number',$booking->driver_id)->exists()){
		    $driver=app_users::where('mobile_number',$booking->driver_id)->first();
		    if($driver->device_token != ""){
			    $this->pushNotification("Booking's status changed to ".$status,"Status Changed",$driver->device_token,"driver_notif");
		    }
	    }

        DB::table('booking')->whereId($id)->update($booking1);
        return "Success";
    }
    public function saveTripPrice($id,$status)
    {
        $update['trip_price'] = $status;
        DB::table('booking')->whereId($id)->update($update);
        return "Success";
    }
    public function assignDriver($id,$driver)
    {
        $update['driver_id'] = $driver;
        DB::table('booking')->whereId($id)->update($update);
	    $appUser=app_users::where('mobile_number',$driver)->first();
	    if($appUser->device_token != ""){
		    $this->pushNotification("New Booking assigned to you","New Booking",$appUser->device_token,"driver_notif");
	    }
        return "Success";
    }

	public function pushNotification($body,$title,$token,$pushFor){
		$fcmUrl = 'https://fcm.googleapis.com/fcm/send';
		$notification = [
			'body' => utf8_encode($body),
			'title'     => utf8_encode($title),
			'pushFor'   => $pushFor
		];

		$fcmNotification = [
			'to'        => $token, //single token
			'data' => $notification,
		];

		$headers = [
			'Authorization: key=AIzaSyBMhqDNhwKKwkU51ETaDH_eKrye2QCc-JI',
			'Content-Type: application/json;charset=UTF-8;'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$fcmUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
		$result = curl_exec($ch);
		curl_close($ch);
	}
}
