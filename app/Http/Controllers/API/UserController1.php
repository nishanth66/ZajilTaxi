<?php

namespace App\Http\Controllers\API;

use App\Models\app_users;
use App\Models\user_profile;
use App\Models\booking;
use App\Models\user_profile_status;
use App\Models\booking_status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController1 extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function verify(Request $request)
	{
		$length = strlen($request->mobile_number);
		if ($request->mobile_number!='' && !empty($request->mobile_number) && ctype_digit($request->mobile_number) && $length == 11) {
			if (app_users::where('mobile_number', $request->mobile_number)->exists()) {
				$count = app_users::where('mobile_number', $request->mobile_number)->first();
				$otp = $count->otp;

				$counter = $count->otp_counter;
				if ($otp != '' || !empty($otp)) {
					if ($counter < 3) {
						if (app_users::where('mobile_number', $request->mobile_number)->where('otp', $request->otp)->exists()) {
							$user = app_users::where('mobile_number', $request->mobile_number)->where('otp', $request->otp)->first();
							$input['otp_counter'] = 1;
							app_users::where('mobile_number', $request->mobile_number)->where('otp', $request->otp)->update($input);
							$data['statusCode'] = 200;
							$data['status'] = 'success';
							$data['message'] = 'OTP Verified. Logged In Successfully.';
							$data['data'] = $user;
						} else {
							$last = 3 - $counter;
							$counter = $counter + 1;
							$input['otp_counter'] = $counter;
							app_users::where('mobile_number', $request->mobile_number)->update($input);
							$data['statusCode'] = 500;
							$data['status'] = 'failed';
							$data['message'] = 'Enter Valid OTP You have last ' . $last . ' chance left';
							$data['data'] = $counter;
						}
					} elseif ($counter == 3 && $request->otp == $otp) {
						$user = app_users::where('mobile_number', $request->mobile_number)->where('otp', $request->otp)->first();
						$input['otp_counter'] = 1;
						app_users::where('mobile_number', $request->mobile_number)->where('otp', $request->otp)->update($input);
						$data['statusCode'] = 200;
						$data['status'] = 'success';
						$data['message'] = 'OTP Verified. Logged In Successfully.';
						$data['data'] = $user;
					} else {
						$input['otp_counter'] = 1;
						$input['otp'] = '';
						$input['lastError'] = time();
						app_users::where('mobile_number', $request->mobile_number)->update($input);
						$data['statusCode'] = 500;
						$data['status'] = 'failed';
						$data['message'] = 'OTP is Invalid!! You have exceeded the maximum attempt. Please wait for 2 minutes and try Again By resending the OTP.';
						$data['data'] = [];
					}
				}
				else {
					$data['statusCode'] = 500;
					$data['status'] = 'failed';
					$data['message'] = 'Please request for OTP and try again.';
					$data['data'] = [];
				}
			}
			else
			{
				$data['statusCode'] = 500;
				$data['status'] = 'failed';
				$data['message'] = 'Please Check Your Mobile Number and try again.';
				$data['data'] = [];
			}
		}
		else
		{
			$data['statusCode'] = 500;
			$data['status'] = 'failed';
			$data['message'] = 'Please Check Your Mobile Number and try again.';
			$data['data'] = [];
		}
		return json_encode($data);
	}
	public function login(Request $request)
	{
		if(isset($request->mobile_number) && $request->mobile_number != "") {
			$mobileNum = $request->mobile_number;
			if (app_users::where('mobile_number', $request->mobile_number)->exists()) {
				$user = app_users::where('mobile_number', $request->mobile_number)->first();
				$lastRequest = (int)$user->lastRequested;
				$now = time();
				if ($user->lastError!='' || !empty($user->lastError)) {
					$lastError = (int)$user->lastError;
					$valid = ($now - $lastError) / 60;
//                    return $valid;
				}
				else
				{
					$valid = 999;
				}
				$total = ($now - $lastRequest) / 60;
				$counter = $user->counter;
				$id = $user->id;
			} else {
				$input = ['mobile_number' => $request->mobile_number];
				$input['lastRequested'] = time();
				$input['counter'] = 0;
				$id = app_users::create($input);
				$total = 0;
				$valid = 999;
				$counter =1;
			}
			if ($valid >= 2) {
				if ($total <= 1 && $counter < 3) {
					$headers = array(
						'Host: ismartsms.net',
						'Content-Type: application/json',
						'Cache-Control: no-cache'
					);
					$otp = substr(str_shuffle("0123456789"), 0, 4);
					$requestData = [
						"UserID" => "zajil_ws",
						"Password" => "J#cjsw19",
						"Message" => $otp,
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
					$data = json_decode(curl_exec($ch), true);
					curl_close($ch);
					if ($data['Code'] == "1") {
						$user = app_users::where('mobile_number', $request->mobile_number)->first();
						$input['otp'] = $otp;
						$counter = $counter + 1;
						$input['counter'] = $counter;
						app_users::whereId($id)->update($input);
						$user['otp'] = "";
						$data1['statusCode'] = 200;
						$data1['status'] = 'success';
						$data1['message'] = 'OTP sent successfully.';
						$data1['data'] = $user;
					} else {
						$data1['statusCode'] = 500;
						$data1['status'] = 'failed';
						$data1['message'] = 'Something went wrong.';
						$data1['data'] = [];
					}
				} elseif ($total <= 1 && $counter >= 3) {
					$data1['statusCode'] = 500;
					$data1['status'] = 'failed';
					$data1['message'] = 'too many requests please try after 2 minutes.';
					$data1['data'] = [];
				} elseif ($total > 2) {
//                return "oops went";
					$headers = array(
						'Host: ismartsms.net',
						'Content-Type: application/json',
						'Cache-Control: no-cache'
					);
					$otp = substr(str_shuffle("0123456789"), 0, 4);
					$requestData = [
						"UserID" => "zajil_ws",
						"Password" => "J#cjsw19",
						"Message" => $otp,
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
					$data = json_decode(curl_exec($ch), true);
					curl_close($ch);
					if ($data['Code'] == "1") {
						$user = app_users::where('mobile_number', $request->mobile_number)->first();
						$input['otp'] = $otp;
						$input['lastRequested'] = time();
						$counter = 1;
						$input['counter'] = $counter;

						app_users::whereId($id)->update($input);
						$user['otp'] = "";
						$data1['statusCode'] = 200;
						$data1['status'] = 'success';
						$data1['message'] = 'OTP sent successfully.';
						$data1['data'] = $user;
					} else {
						$data1['statusCode'] = 500;
						$data1['status'] = 'failed';
						$data1['message'] = 'Something went wrong.';
						$data1['data'] = [];
					}
				} elseif ($total >= 1 && $counter < 3) {
					$headers = array(
						'Host: ismartsms.net',
						'Content-Type: application/json',
						'Cache-Control: no-cache'
					);
					$otp = substr(str_shuffle("0123456789"), 0, 4);
					$requestData = [
						"UserID" => "zajil_ws",
						"Password" => "J#cjsw19",
						"Message" => $otp,
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
					$data = json_decode(curl_exec($ch), true);
					curl_close($ch);
					if ($data['Code'] == "1") {
						$user = app_users::where('mobile_number', $request->mobile_number)->first();
						$input['otp'] = $otp;
						$input['lastRequested'] = time();
						$counter = 1;
						$input['counter'] = $counter;

						app_users::whereId($id)->update($input);
						$user['otp'] = "";
						$data1['statusCode'] = 200;
						$data1['status'] = 'success';
						$data1['message'] = 'OTP sent successfully.';
						$data1['data'] = $user;
					} else {
						$data1['statusCode'] = 500;
						$data1['status'] = 'failed';
						$data1['message'] = 'Something went wrong.';
						$data1['data'] = [];
					}
				}
			}
			else
			{
				$data1['statusCode'] = 500;
				$data1['status'] = 'failed';
				$data1['message'] = 'You need to wait for 2 minutes before trying again.';
				$data1['data'] = [];
			}
		}
		else {
			$data1['statusCode'] = 500;
			$data1['status'] = 'failed';
			$data1['message'] = 'Mobile number can not be empty.';
			$data1['data'] = [];
		}
		return json_encode($data1);
	}

	function verifyOtp($mobile_number,$otp) {
		$length = strlen($mobile_number);
		if ( $mobile_number != '' && ! empty( $mobile_number ) && ctype_digit( $mobile_number ) && $length == 11 ) {
			if ( app_users::where( 'mobile_number', $mobile_number )->exists() ) {
				if ($otp != '' || !empty($otp)) {
					if (app_users::where('mobile_number', $mobile_number)->where('otp', $otp)->exists()) {
						return ['status'=>true];
					}
					else{
						return ['status'=>false,'message'=>"Enter Valid OTP."];
					}
				}
				else{
					return ['status'=>false,'message'=>"Enter Valid OTP."];
				}
			}
			else{
				return ['status'=>false,'message'=>"Enter Valid mobile number."];
			}
		}

		else{
			return ['status'=>false,'message'=>"Enter Valid mobile number."];
		}
	}
	public function customerProfileFields()
	{
		$userProfile=DB::table('customer_profile_status')->where('status','enabled')->orderby('position')->get();
		$data2['statusCode']=200;
		$data2['status']='success';
		$data2['message']='User Profile fields are Retrieved Successfully.';
		$data2['data']=$userProfile;
		return $data2;
	}
	public function customerProfile(Request $request)
	{
//	    return $request->all();
		foreach($request->all() as $key=>$value)
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

		DB::table('customer_profile')->insert($input);
		$data7['statusCode']=200;
		$data7['status']='success';
		$data7['message']='Driver profile is edited Successfully.';
		$data7['data']=$input;

		return $data7;
	}
	public function customerProfileEI(Request $request)
	{
//	    return $request->all();
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']){
			foreach($request->except('mobile_number','otp') as $key=>$value)
			{
				$key1 = str_replace(' ','_',$key);
				$input[$key1] = $value;
			}
			$input['user_id']=$mNumber;
			$fields=DB::table('customer_profile_status')->where('status','enabled')->get();
			foreach ($fields as $field){
				if($field->type_en == "file"){
					$colName=str_replace(" ","_",$field->column_name_en);
					$data = $input[$colName];

					if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
						$data = substr($data, strpos($data, ',') + 1);
						$type = strtolower($type[1]); // jpg, png, gif

						if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
							$response['statusCode'] = 500;
							$response['status'] = 'failed';
							$response['message'] = 'Invalid image type.';
							return $response;
						}

						$data = base64_decode($data);

						if ($data === false) {
							$response['statusCode'] = 500;
							$response['status'] = 'failed';
							$response['message'] = 'base64_decode failed.';
							return $response;
						}
					} else {
						$response['statusCode'] = 500;
						$response['status'] = 'failed';
						$response['message'] = 'did not match data URI with image data.';
						return $response;
					}
					$imgName=rand(1, 9999999) . time() . '.'.$type;
					file_put_contents(public_path().'/avatars/'.$imgName, $data);
					$input[$colName]=asset('public').'/avatars/'.$imgName;
				}
			}
			$customer=DB::table('customer_profile')->whereUserId($mNumber)->get();
			if(count($customer) > 0){
				DB::table('customer_profile')->where('user_id',$mNumber)->update($input);
				$customerProfile=DB::table('customer_profile')->where('user_id',$mNumber)->first();
				$response['statusCode'] = 200;
				$response['status'] = 'success';
				$response['message'] = 'User Profile is Updated Successfully.';
				$response['data'] = $customerProfile;
			}
			else{
				if (DB::table('customer_profile')->insert($input)) {
					$lastId=DB::getPdo()->lastInsertId();
					$customerProfile=DB::table('customer_profile')->whereId($lastId)->first();
					$response['statusCode'] = 200;
					$response['status'] = 'success';
					$response['message'] = 'User Profile is Saved Successfully.';
					$response['data'] = $customerProfile;
				}
				else
				{
					$response['statusCode'] = 500;
					$response['status'] = 'failed';
					$response['message'] = 'Something went wrong. Please try again.';
				}
			}
		}
		else{
			$response['statusCode'] = 500;
			$response['status'] = 'failed';
			$response['message'] = $status['message'];
		}
		return $response;
	}
	public function RegisteredCustomers()
	{
		$users = DB::table('customer_profile')->get();
		$data7['statusCode']=200;
		$data7['status']='success';
		$data7['message']='Registerd customer profiles are Retrieved Successfully.';
		$data7['data']=$users;
		return $data7;
	}
	public function EditcustomerProfile(Request $request)
	{
//        return $request->all();
		foreach($request->all() as $key=>$value)
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

		DB::table('customer_profile')->whereId($request->id)->update($input);
		$data7['statusCode']=200;
		$data7['status']='success';
		$data7['message']='Customer profile is edited Successfully.';
		$data7['data']=$input;
		return $data7;
	}
	public function customerProfileData(Request $request){
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']){
			if(DB::table('customer_profile')->where('user_id',$mNumber)->count() > 0) {
				$customerProfile        = DB::table( 'customer_profile' )->where( 'user_id', $mNumber )->first();
				$customerProfile1=[];
				foreach($customerProfile as $key => $value){
					if(empty($value)){
						$customerProfile1[$key]="";
					}
					else{
						$customerProfile1[$key]=$value;
					}
				}
				$response['statusCode'] = 200;
				$response['status']     = 'success';
				$response['message']    = 'User Profile is retrieved Successfully.';
				$response['data']       = $customerProfile1;
			}
			else{
				$response['statusCode'] = 500;
				$response['status'] = 'failed';
				$response['message'] = "User not exists";
			}
		}
		else{
			$response['statusCode'] = 500;
			$response['status'] = 'failed';
			$response['message'] = $status['message'];
		}
		return $response;
	}

	public function driverProfileFields()
	{
		$userProfile=user_profile_status::where('status','enabled')->orderby('position')->get();
		$data2['statusCode']=200;
		$data2['status']='success';
		$data2['message']='User Profile fields are Retrieved Successfully.';
		$data2['data']=$userProfile;
		return $data2;
	}
	public function driverProfile(Request $request)
	{
		foreach($request->all() as $key=>$value)
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
		DB::table('driver_profile')->insert($input);
		$data1['statusCode']=200;
		$data1['status']='success';
		$data1['message']='Driver profile is edited Successfully.';
		$data1['data']=$input;
		return json_encode($data1);
	}
	public function registeredDrivers()
	{
		$users = DB::table('driver_profile')->where('status','!=','pending')->get();
		$data6['statusCode']=200;
		$data6['status']='success';
		$data6['message']='Registerd driver profiles are Retrieved Successfully.';
		$data6['data']=$users;
		return $data6;
	}
	public function EditdriverProfile(Request $request)
	{
//        return $request->id;
		foreach($request->all() as $key=>$value)
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

		DB::table('driver_profile')->where('id',$request->id)->update($input);
		$data7['statusCode']=200;
		$data7['status']='success';
		$data7['message']='Driver profile is edited Successfully.';
		$data7['data']=$input;
		return $data7;
	}
	public function viewDriverProfile(Request $request){
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']){
			if(DB::table('driver_profile')->where('user_id',$mNumber)->count() > 0) {
				$Profile        = DB::table( 'driver_profile' )->where( 'user_id', $mNumber )->first();
				$response['statusCode'] = 200;
				$response['status']     = 'success';
				$response['message']    = 'User Profile is retrieved Successfully.';
				$response['data']       = $Profile;
			}
			else{
				$response['statusCode'] = 500;
				$response['status'] = 'failed';
				$response['message'] = "User not exists";
			}
		}
		else{
			$response['statusCode'] = 500;
			$response['status'] = 'failed';
			$response['message'] = $status['message'];
		}
		return $response;
	}

	public function bookingFields()
	{
		$userProfile=booking_status::where('status','enabled')->orderby('position')->get();
		$data4['statusCode']=200;
		$data4['status']='success';
		$data4['message']='Booking fields are Retrieved Successfully.';
		$data4['data']=$userProfile;
		return json_encode($data4);
	}
	public function bookingProfile(Request $request)
	{
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']) {
			foreach ( $request->except('mobile_number','otp') as $key => $value ) {
				$key1           = str_replace( ' ', '_', $key );
				$input[ $key1 ] = $value;
			}
			if ( isset( $_FILES ) ) {
				foreach ( $_FILES as $fName => $fArray ) {
					if ( isset( $input[ $fName ] ) ) {
						$photoName = asset( 'public/avatars' ) . '/' . rand( 1, 9999999 ) . time() . '.' . $input[ $fName ]->getClientOriginalExtension();
						$input[ $fName ]->move( public_path( 'avatars' ), $photoName );
						$input[ $fName ] = $photoName;
					}
				}
			}
			$id = DB::table( 'booking' )->insert( $input );
			$data6['statusCode'] = 200;
			$data6['status']     = 'success';
			$data6['message']    = 'Booking is Saved Successfully.';
			$data6['data']       = $input;
		}
		else{
			$data6['statusCode'] = 500;
			$data6['status'] = 'failed';
			$data6['message'] = $status['message'];
		}
		return json_encode($data6);
	}
	public function tripPrice(Request $request){
		$source=$request->source;
		$destination=$request->destination;
		$src=explode(",",$source);
		if(isset($src[0]) && isset($src[1])){
			$latSrc=$src[0];
			$lonSrc=$src[1];
		}
		else{
			$response['statusCode']=500;
			$response['status']='failed';
			$response['message']='Enter Valid Source Location.';
			return $response;
		}
		$dest=explode(",",$destination);
		if(isset($dest[0]) && isset($dest[1])){
			$latDest=$dest[0];
			$lonDest=$dest[1];
		}
		else{
			$response['statusCode']=500;
			$response['status']='failed';
			$response['message']='Enter Valid Destination Location.';
			return $response;
		}

		$distance=round($this->distance($latSrc, $lonSrc, $latDest, $lonDest, "K"),2);
		$km_price1=DB::table('km_price')->get();
		$km_price=$km_price1[0]->km_price;
		$price=$km_price*$distance;

		$response['statusCode']=200;
		$response['status']='success';
		$response['message']='Trip Price Calculated Successfully.';
		$response['data']=$price;
		return $response;
	}
	function distance($lat1, $lon1, $lat2, $lon2, $unit) {

		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
}
