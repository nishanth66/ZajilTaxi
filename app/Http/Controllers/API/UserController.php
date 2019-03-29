<?php

namespace App\Http\Controllers\API;

use App\Models\app_users;
use App\Models\user_profile;
use App\Models\booking;
use App\Models\user_profile_status;
use App\Models\booking_status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Models\fixed_routes;
use \App\Models\feedback;

class UserController extends Controller
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

	public function Driverlogin(Request $request)
	{
		if(isset($request->mobile_number) && $request->mobile_number != "") {
			$mobileNum = $request->mobile_number;
			if (app_users::where('mobile_number', $request->mobile_number)->exists()) {
			    if(DB::table('customer_profile')->where('user_id',$request->mobile_number)->exists() == 0)
                {
                    $insert_driver['user_id'] = $request->mobile_number;
                    DB::table('driver_profile')->insert($insert_driver);
                }
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
				if(isset($request->device_token) && $request->device_token != ""){
					app_users::where('mobile_number', $request->mobile_number)->update(['device_token'=>$request->device_token]);
				}
				$total = ($now - $lastRequest) / 60;
				$counter = $user->counter;
				$id = $user->id;
			} else {

				$input = ['mobile_number' => $request->mobile_number];
				$input['lastRequested'] = time();
				$input['counter'] = 0;
				if(isset($request->device_token) && $request->device_token != "")
				{
					$input['device_token']=$request->device_token;
				}
                $insert_driver['user_id'] = $request->mobile_number;
				$drivver_id = DB::table('driver_profile')->insert($insert_driver);
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
					$data1['message'] = 'Too many requests please try after 2 minutes.';
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
	public function Customerlogin(Request $request)
	{
		if(isset($request->mobile_number) && $request->mobile_number != "") {
			$mobileNum = $request->mobile_number;
			if (app_users::where('mobile_number', $request->mobile_number)->exists()) {
                if(DB::table('customer_profile')->where('user_id',$request->mobile_number)->exists() == 0)
                {
                    $insert_user['user_id'] = $request->mobile_number;
                    DB::table('customer_profile')->insert($insert_user);
                }
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
				if(isset($request->device_token) && $request->device_token != ""){
					app_users::where('mobile_number', $request->mobile_number)->update(['device_token'=>$request->device_token]);
				}
				$total = ($now - $lastRequest) / 60;
				$counter = $user->counter;
				$id = $user->id;
			} else {

				$input = ['mobile_number' => $request->mobile_number];
				$input['lastRequested'] = time();
				$input['counter'] = 0;
				if(isset($request->device_token) && $request->device_token != ""){
					$input['device_token']=$request->device_token;
				}
                $insert_user['user_id'] = $request->mobile_number;
                $customer_id= DB::table('customer_profile')->insert($insert_user);
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
					$data1['message'] = 'Too many requests please try after 2 minutes.';
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
        foreach ($userProfile as $user)
        {
            $user->column_name_en = str_replace(' ','_',$user->column_name_en);
            $user->column_name_ar = str_replace(' ','_',$user->column_name_ar);
        }
		$data2['statusCode']=200;
		$data2['status']='success';
		$data2['message']='User Profile fields are Retrieved Successfully.';
		$data2['data']=$userProfile;
		return $data2;
	}
	public function customerProfile(Request $request) {
//	    $column = DB::table('customer_profile_status')->where('validation','required')->get();
//	    $count = DB::table('customer_profile_status')->where('validation','required')->count();
		$mNumber = $request->mobile_number;
		$otp     = $request->otp;
		$status  = $this->verifyOtp( $mNumber, $otp );
		if ( $status['status'] ) {
			foreach ( $request->except( 'mobile_number', 'otp' ) as $key => $value ) {
//                    foreach ($column as $col) {
//                        $cols = str_replace(' ','_',$col->column_name_en);
//                    if (($cols == $key) && ($value != '' || !empty($value)))
//                    {
//                        $count = $count - 1;
//                    }

				$key1           = str_replace( ' ', '_', $key );
				$input[ $key1 ] = $value;
//                }
			}
			if ( isset( $_FILES ) ) {
				foreach ( $_FILES as $fName => $fArray ) {
					if ( isset( $input[ $fName ] ) ) {
						if ( $_FILES[ $fName ]["size"] > 2097152 ) {
							$response['statusCode'] = 500;
							$response['status']     = 'failed';
							$response['message']    = $fName . ' can not be greater then 2 MB.';

							return json_encode( $response );
						} else {
							$photoName = asset( 'public/avatars' ) . '/' . rand( 1, 9999999 ) . time() . '.' . $input[ $fName ]->getClientOriginalExtension();
							$input[ $fName ]->move( public_path( 'avatars' ), $photoName );
							$input[ $fName ] = $photoName;
						}
					}
				}
			}
//			if ($count == 0) {
			$input['user_id'] = $mNumber;
			$customer         = DB::table( 'customer_profile' )->whereUserId( $mNumber )->get();
			if ( count( $customer ) > 0 ) {
				DB::table( 'customer_profile' )->where( 'user_id', $mNumber )->update( $input );
				$customerProfile        = DB::table( 'customer_profile' )->where( 'user_id', $mNumber )->first();
				$response['statusCode'] = 200;
				$response['status']     = 'success';
				$response['message']    = 'User Profile is Updated Successfully.';
				$response['data']       = $customerProfile;
			} else {
				if ( DB::table( 'customer_profile' )->insert( $input ) ) {
					$lastId                 = DB::getPdo()->lastInsertId();
					$customerProfile        = DB::table( 'customer_profile' )->whereId( $lastId )->first();
					$response['statusCode'] = 200;
					$response['status']     = 'success';
					$response['message']    = 'User Profile is Saved Successfully.';
					$response['data']       = $customerProfile;
				} else {
					$response['statusCode'] = 500;
					$response['status']     = 'failed';
					$response['message']    = 'Something went wrong. Please try again.';
				}
			}
		}
//            else
//            {
//                $response['statusCode'] = 500;
//                $response['status'] = 'failed';
//                $response['message'] = 'Please Fill all the Required Columns.';
//            }

		else{
			$response['statusCode'] = 500;
			$response['status'] = 'failed';
			$response['message'] = $status['message'];
		}
		return $response;
	}
//	public function RegisteredCustomers()
//	{
//		$users = DB::table('customer_profile')->get();
//		$data7['statusCode']=200;
//		$data7['status']='success';
//		$data7['message']='Registerd customer profiles are Retrieved Successfully.';
//		$data7['data']=$users;
//		return $data7;
//	}
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
		return json_encode($response);
	}

	public function driverProfileFields()
	{
		$userProfile=user_profile_status::where('status','enabled')->orderby('position')->get();
		foreach ($userProfile as $user)
        {
            $user->column_name_en = str_replace(' ','_',$user->column_name_en);
            $user->column_name_ar = str_replace(' ','_',$user->column_name_ar);
        }
		$data2['statusCode']=200;
		$data2['status']='success';
		$data2['message']='User Profile fields are Retrieved Successfully.';
		$data2['data']=$userProfile;
		return $data2;
	}
	public function driverProfile(Request $request)
	{
        $column = DB::table('driver_profile_status')->where('validation','required')->get();
        $count = DB::table('driver_profile_status')->where('validation','required')->count();
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']) {
            foreach ($request->except('mobile_number', 'otp') as $key => $value) {
                foreach ($column as $col) {
                    $cols = str_replace(' ', '_', $col->column_name_en);
                    if (($cols == $key) && ($value != '' || !empty($value))) {
                        $count = $count - 1;
                    }

                    $key1 = str_replace(' ', '_', $key);
                    $input[$key1] = $value;
                }
            }
            if (isset($_FILES)) {
                foreach ($_FILES as $fName => $fArray) {
                    if (isset($input[$fName])) {
                        if ($_FILES[$fName]["size"] > 2097152) {
                            $response['statusCode'] = 500;
                            $response['status'] = 'failed';
                            $response['message'] = $fName . ' can not be greater then 2 MB.';
                            return json_encode($response);
                        } else {
                            $photoName = asset('public/avatars') . '/' . rand(1, 9999999) . time() . '.' . $input[$fName]->getClientOriginalExtension();
                            $input[$fName]->move(public_path('avatars'), $photoName);
                            $input[$fName] = $photoName;
                        }
                    }
                }
            }
            if ($count == 0) {
                $input['user_id'] = $mNumber;
                $customer = DB::table('driver_profile')->whereUserId($mNumber)->get();
                if (count($customer) > 0) {
                    DB::table('driver_profile')->where('user_id', $mNumber)->update($input);
                    $customerProfile = DB::table('driver_profile')->where('user_id', $mNumber)->first();
                    $response['statusCode'] = 200;
                    $response['status'] = 'success';
                    $response['message'] = 'Driver Profile is Updated Successfully.';
                    $response['data'] = $customerProfile;
                } else {
                    if (DB::table('driver_profile')->insert($input)) {
                        $lastId = DB::getPdo()->lastInsertId();
                        $customerProfile = DB::table('driver_profile')->whereId($lastId)->first();
                        $response['statusCode'] = 200;
                        $response['status'] = 'success';
                        $response['message'] = 'Driver Profile is Saved Successfully.';
                        $response['data'] = $customerProfile;
                    } else {
                        $response['statusCode'] = 500;
                        $response['status'] = 'failed';
                        $response['message'] = 'Something went wrong. Please try again.';
                    }
                }
//			DB::table( 'driver_profile' )->insert( $input );
//			$response['statusCode'] = 200;
//			$response['status']     = 'success';
//			$response['message']    = 'Driver profile is edited Successfully.';
//			$response['data']       = $input;
            }
            else
            {
                $response['statusCode'] = 500;
                $response['status'] = 'failed';
                $response['message'] = "Please fill all the required Columns";
            }
        }
		else{
            $response['statusCode'] = 500;
            $response['status'] = 'failed';
            $response['message'] = $status['message'];
			}
		return json_encode($response);
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
				$Profile = DB::table( 'driver_profile' )->where( 'user_id', $mNumber )->first();
				$customerProfile1=[];
				foreach($Profile as $key => $value){
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

	public function bookingFields()
	{
		$userProfile=booking_status::where('status','enabled')->orderby('position')->get();
		$data4['statusCode']=200;
		$data4['status']='success';
		$data4['message']='Booking fields are Retrieved Successfully.';
		$data4['data']=$userProfile;
		return json_encode($data4);
	}
	public function bookingFiles(Request $request){
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']) {
			if ( isset( $_FILES ) ) {
				$photoName="";
				foreach ( $request->except('mobile_number','otp','source','destination') as $key => $value ) {
					$key1           = str_replace( ' ', '_', $key );
					$input[ $key1 ] = $value;
				}
				foreach ( $_FILES as $fName => $fArray ) {
					if ( isset( $input[ $fName ] ) ) {
						if ( $_FILES[ $fName ]["size"] > 2097152 ) {
							$data6['statusCode'] = 500;
							$data6['status']     = 'failed';
							$data6['message']    = $fName . ' can not be greater then 2 MB.';

							return json_encode( $data6 );
						} else {
							$photoName = asset( 'public/avatars' ) . '/' . rand( 1, 9999999 ) . time() . '.' . $input[ $fName ]->getClientOriginalExtension();
							$input[ $fName ]->move( public_path( 'avatars' ), $photoName );
						}
					}
				}
				if($photoName == ""){
					$data['statusCode'] = 500;
					$data['status'] = 'failed';
					$data['message'] = 'There is no file to upload.';
				}else {
					$data['statusCode'] = 200;
					$data['status']     = 'success';
					$data['message']    = 'File uploaded successfully.';
					$data['file_link']  = $photoName;
				}
			}
			else{
				$data['statusCode'] = 500;
				$data['status'] = 'failed';
				$data['message'] = 'There is no file to upload.';
			}
		}
		else{
			$data['statusCode'] = 500;
			$data['status'] = 'failed';
			$data['message'] = $status['message'];
		}
		return json_encode($data);
	}
	public function bookingProfile(Request $request)
	{
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']) {
			foreach ( $request->except('mobile_number','otp','source','destination','trip_price','source_description','destination_description') as $key => $value ) {
				$key1           = str_replace( ' ', '_', $key );
				$input[ $key1 ] = $value;
			}
			if ( isset( $_FILES ) ) {
				foreach ( $_FILES as $fName => $fArray ) {
					if ( isset( $input[ $fName ] ) ) {
						if ($_FILES[$fName]["size"] > 2097152) {
							$data6['statusCode'] = 500;
							$data6['status'] = 'failed';
							$data6['message'] = $fName.' can not be greater then 2 MB.';
							return json_encode($data6);
						}
						else {
							$photoName = asset( 'public/avatars' ) . '/' . rand( 1, 9999999 ) . time() . '.' . $input[ $fName ]->getClientOriginalExtension();
							$input[ $fName ]->move( public_path( 'avatars' ), $photoName );
							$input[ $fName ] = $photoName;
						}
					}
				}
			}
            date_default_timezone_set('Asia/Muscat');
//			*********************************************************************
//
//            *********************************************************************
            $date_now = time();
            $input['user_id'] = $mNumber;
            $input['source'] = $request->source;
            $input['destination'] = $request->destination;
            $input['driver_trip_earning'] = $request->driver_trip_earning;
            $input['trip_price'] = (int)$request->trip_price;
            $input['source_description'] = $request->source_description;
            $input['destination_description'] = $request->destination_description;
            $input['booking_time'] = date('h:i A',$date_now);
            $input['date'] = date('d/m/Y',$date_now);
            $input['first_trip_time'] =  $request->first_trip_time;
            $input['second_trip_time'] = $request->second_trip_time;
            $input['driver_onboard'] = $request->driver_onboard;
            $input['wash_car'] = $request->wash_car;
            $input['parking_fees'] = $request->parking_fees;
            $input['payment'] = $request->payment;
			DB::table( 'booking' )->insert( $input );

			if($request->second_trip_time != '' || !empty($request->second_trip_time))
			{
				$input1=$input;
				$input1['source']= $request->destination;
				$input1['destination'] = $request->source;
				$input1['driver_trip_earning'] = $request->driver_trip_earning;
				$input1['source_description'] = $request->destination_description;
				$input1['destination_description']= $request->source_description;
				$input1['first_trip_time']= $request->second_trip_time;
				$input1['second_trip_time']= "";
				$input1['payment']= $request->payment;
				DB::table( 'booking' )->insert( $input1 );

			}
			$data = DB::table('booking')->orderby('id','desc')->first();
			$data6['statusCode'] = 200;
			$data6['status']     = 'success';
			$data6['message']    = 'Booking is Saved Successfully.';
			$data6['data']       = $data;
		}
		else{
			$data6['statusCode'] = 500;
			$data6['status'] = 'failed';
			$data6['message'] = $status['message'];
		}
		return json_encode($data6);
	}
	public function tripPrice(Request $request){
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']) {
			$source      = $request->source;
			$destination = $request->destination;
			$src         = explode( ",", $source );
			if ( isset( $src[0] ) && isset( $src[1] ) ) {
				$latSrc = $src[0];
				$lonSrc = $src[1];
			} else {
				$response['statusCode'] = 500;
				$response['status']     = 'failed';
				$response['message']    = 'Enter Valid Source Location.';

				return $response;
			}
			$dest = explode( ",", $destination );
			if ( isset( $dest[0] ) && isset( $dest[1] ) ) {
				$latDest = $dest[0];
				$lonDest = $dest[1];
			} else {
				$response['statusCode'] = 500;
				$response['status']     = 'failed';
				$response['message']    = 'Enter Valid Destination Location.';

				return $response;
			}
			if (fixed_routes::where('source_lat',$latSrc)->where('source_long',$lonSrc)->where('destination_lat',$latDest)->where('destination_long',$lonDest)->exists())
            {
                $fixed = fixed_routes::where('source_lat',$latSrc)->where('source_long',$lonSrc)->where('destination_lat',$latDest)->where('destination_long',$lonDest)->first();
                $distance = round($this->distance($latSrc, $lonSrc, $latDest, $lonDest, "K"), 2);
                $p=$this->round_up($fixed->fixed_price,1);
            }
            else {
                $distance = round($this->distance($latSrc, $lonSrc, $latDest, $lonDest, "K"), 2);
                if (DB::table('km_price')->exists())
                {
                    $km_price1 = DB::table('km_price')->get();
                    $km_price = $km_price1[0]->km_price;
                }
                else
                {
                    $km_price = 0;
                }

                $price = $km_price * $distance;

                $p = $this->round_up($price, 1);
            }
            if (DB::table('min_trip_price')->exists())
            {
                $price = DB::table('min_trip_price')->first();
                $p22 = $price->price;
            }
            else
            {
                $p22 = 0;
            }
			if ($p > $p22) {
                $p1 = number_format($p, 3);
            }
            else
            {
                $p1 = number_format($p22, 3);
            }
			$response['statusCode'] = 200;
			$response['status']     = 'success';
			$response['message']    = 'Trip Price Calculated Successfully.';
			$response['data']       = $p1;
		}
		else{
			$response['statusCode'] = 500;
			$response['status'] = 'failed';
			$response['message'] = $status['message'];
		}
		return $response;
	}
	function round_up ( $value, $precision ) {
		$pow = pow ( 10, $precision );
		return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow;
	}
	public function tripTime(Request $request){
		$source=$request->source;
		$destination=$request->destination;

		$waypoint0= urlencode($source);
		$waypoint1= urlencode($destination);
		$mode= urlencode("fastest;car;traffic:enabled");
		$app_id= urlencode("EUFykovZfO3bZH9E30Ih");
		$app_code= urlencode("_a_I4A7F07PqX-iB2jl1MA");
		$departure= urlencode("now");

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://route.api.here.com/routing/7.2/calculateroute.json?waypoint0=$waypoint0&waypoint1=$waypoint1&mode=$mode&app_id=$app_id&app_code=$app_code&departure=$departure",
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache"
			),
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
//		echo "<pre>";print_r($resp);exit;

		if( !$resp ){
			// log this Curl ERROR:
//			echo curl_error($curl) ;
			$response['statusCode']=500;
			$response['status']='failed';
			$response['message']='Try again.';
		}
		else{
			$r=json_decode($resp,true);
			$time=$r['response']['route'][0]['summary']['trafficTime'];
			$response['statusCode']=200;
			$response['status']='success';
			$response['message']='Trip Time Calculated Successfully.';
			$response['data']=(int)$time;
		}
		curl_close($curl);
//
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
	function feedback(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status']) {
	        @date_default_timezone_set('Asia/Muscat');
            $input = $request->all();
            $date = date('d-m-Y', time());
            $input['date'] = $date;
            $input['user_id'] = $mNumber;
            $id = feedback::create($input);
            $feedback['statusCode'] = 200;
            $feedback['status'] = 'success';
            $feedback['message'] = 'Feedback sent successfully.';
            $feedback['data'] = $input;
            $feedback['last_insert_id'] = $id->id;
        }
        else
        {
            $feedback['statusCode'] = 500;
            $feedback['status'] = 'failed';
            $feedback['message'] = $status['message'];
        }

        return $feedback;
    }
    function driverAvailable(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status']) {
            $input = $request->except('mobile_number','otp');
            $input['user_id'] = $mNumber;
            if (DB::table('driver_profile')->where('user_id',$mNumber)->where('status','accepted')->exists()) {
                if (DB::table('driver_available')->where('user_id',$mNumber)->exists())
                {
                    $driver_available = DB::table('driver_available')->where('user_id',$mNumber)->update($input);
                    $available['statusCode'] = 200;
                    $available['status'] = 'success';
                    $available['message'] = 'timings set successfully.';
                    $available['data'] = $driver_available;
                }
                else {
                    $driver_available = DB::table('driver_available')->insert($input);
                    $available['statusCode'] = 200;
                    $available['status'] = 'success';
                    $available['message'] = 'timings set successfully.';
                    $available['data'] = $driver_available;
                }
            } else {
                $available['statusCode'] = 500;
                $available['status'] = 'failed';
                $available['message'] = 'Driver is not exist. and Driver is not accepted by the Admin';
                $available['data'] = [];
            }
        }
        else
        {
            $available['statusCode'] = 500;
            $available['status'] = 'failed';
            $available['message'] = $status['message'];
        }
        return $available;
    }
    public function allBookings(Request $request){
	    $mNumber=$request->mobile_number;
	    $otp=$request->otp;
	    $status=$this->verifyOtp($mNumber,$otp);
	    if($status['status']) {
	    	$bookings=DB::table('booking')->where('user_id',$mNumber)->get();
		    $feedback['statusCode'] = 200;
		    $feedback['status'] = 'success';
		    $feedback['message'] = 'Bookings retrieved successfully.';
		    $feedback['data'] = $bookings;
	    }
		else
		{
			$feedback['statusCode'] = 500;
			$feedback['status'] = 'failed';
			$feedback['message'] = $status['message'];
		}

		return $feedback;
    }
	public function bookingStatus(Request $request){
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status']) {
			$booking_id=$request->booking_id;
			if(DB::table( 'booking' )->where('id',$booking_id)->where('user_id',$mNumber)->exists()) {
				$booking = DB::table( 'booking' )->where( 'id', $booking_id )->first();
				$response['statusCode']     = 200;
				$response['status']         = 'success';
				$response['booking_status'] = $booking->status;
			}
			else{
				$response['statusCode'] = 500;
				$response['status'] = 'failed';
				$response['message'] = "Booking not exists.";
			}
		}
		else {
			$response['statusCode'] = 500;
			$response['status']     = 'failed';
			$response['message']    = $status['message'];
		}
		return $response;
	}
	public function driverAvailableStatus(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status'])
        {
            $driver_login['isAvailable'] = $request->isAvailable;
            $driver_login_status = DB::table('driver_available')->where('user_id',$mNumber)->update($driver_login);
            $driver_login2['login_status'] = $request->isAvailable;
            DB::table('driver_profile')->where('user_id',$mNumber)->update($driver_login2);
            $loginStatus['statusCode']     = 200;
            $loginStatus['status']         = 'success';
            $loginStatus['message'] = "Login Status Updated Successfully";
            $loginStatus['data'] = $driver_login_status;
        }
        else
        {
            $loginStatus['statusCode'] = 500;
            $loginStatus['status']     = 'failed';
            $loginStatus['message']    = $status['message'];
        }
        return $loginStatus;

    }
    public function driverAvailableList()
    {
        date_default_timezone_set('Asia/Muscat');
        $today = strtolower(date('l'));
        $available_drivers = DB::table('driver_available')->get();
        $now = time();
        $driver_available = [];
        foreach($available_drivers as $driver)
        {
            $driver=(array)$driver;
            $driver_time = explode(',',$driver[$today]);
           foreach($driver_time as $time)
           {
               $start_end = explode('-',$time);
               $start = strtotime($start_end[0]);
               $end = strtotime($start_end[1]);
               if ($driver['isAvailable'] == 'true' && $now >= $start && $now <= $end)
               {
                   $driver_data = DB::table('driver_profile')->whereUserId($driver['user_id'])->first();

                   $driver_available[$driver['user_id']]= $driver_data;
                   break;
               }

           }
        }

            $driversAvailable['statusCode'] = 200;
            $driversAvailable['status'] = 'success';
            if (count($driver_available) > 0)
            {
                $driversAvailable['message'] = "Driver Available List Fetched Successfully";
            }
            else
            {
                $driversAvailable['message'] = "No Drivers are Available";
            }
            $driversAvailable['number_of_available_drivers'] = count($driver_available);
            $driversAvailable['available_drivers'] = $driver_available;

        return $driversAvailable;
    }
    public function assignedBookings(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status'])
        {
            $bookings = DB::table('booking')->where('driver_id',$mNumber)->get();
            $assigned['statusCode']     = 200;
            $assigned['status']         = 'success';
            $assigned['message'] = "Assigned Booking Fetched Successfully";
            $assigned['number_of_bookings'] = count($bookings);
            $assigned['data'] = $bookings;
        }
        else
        {
            $assigned['statusCode'] = 500;
            $assigned['status'] = 'failed';
            $assigned['message'] = $status['message'];
        }
        return $assigned;
    }
    public function changeBookingStatus(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status'])
        {
            if (isset($request->booking_status))
            {
                $update['status'] = $request->booking_status;
            }
            else
            {
                $booking_status['statusCode'] = 500;
                $booking_status['status'] = 'failed';
                $booking_status['message'] = "Booking Status is can not be null";
                return $booking_status;
            }
            if (isset($request->paid) && ($request->paid != '' || !empty($request->paid)) && $request->paid == 'true')
            {
                $update['paid'] = 'true';
            }
            if( $request->hasFile('image')) {
                foreach ($request->file('image') as $image)
                {
                    $imageInput = [];
                    $photoName = rand(1,777777777).time().'.'.$image->getClientOriginalExtension();
                    $image->move(public_path('avatars'), $photoName);
                    $imageInput['image'] = $photoName;
                    $imageInput['booking_id'] = $request->booking_id;
                    DB::table('booking_images')->insert($imageInput);
                }
            }
	        date_default_timezone_set('Asia/Muscat');
	        $update['last_status_changed_at'] = date("Y-m-d H:i:s");
	        $update['last_status_changed_by'] = $mNumber;
            DB::table('booking')->whereId($request->booking_id)->update($update);
            $booking = DB::table('booking')->whereId($request->booking_id)->first();
            if($booking->paid == 'true' && $booking->commisson_earned == 0)
            {
                $driver = DB::table('driver_profile')->where('user_id',$booking->driver_id)->first();
                $trip_price = (float)$booking->trip_price;
                $commission = $driver->Driver_Commission;
                $commission_price = (float)$booking->driver_trip_earning;
                $current_revenue = (float)$driver->total_revenue;
                if ($driver->total_revenue == '' || empty($driver->total_revenue))
                {
                    $current_revenue = 0;
                }
                $new_commission = $current_revenue+$commission_price;
                $update_driver['total_revenue'] = $new_commission;
                $update_booking['commisson_earned'] = 1;
                DB::table('booking')->whereId($booking->id)->update($update_booking);
                DB::table('driver_profile')->whereId($driver->id)->update($update_driver);
            }
	        $appUser=app_users::where('mobile_number',$booking->user_id)->first();
	        if($appUser->device_token != ""){
		        $this->pushNotification("customer_notif","Booking's status changed","Status Changed",$appUser->device_token);
	        }
	        if(app_users::where('mobile_number',$booking->driver_id)->exists()){
		        $driver=app_users::where('mobile_number',$booking->driver_id)->first();
		        if($driver->device_token != ""){
			        $this->pushNotification("driver_notif","Booking's status changed ","Status Changed",$driver->device_token);
		        }
	        }
            $booking_status['statusCode']     = 200;
            $booking_status['status']         = 'success';
            $booking_status['message'] = "Assigned Booking Fetched Successfully";
            $booking_status['data'] = $booking;
        }
        else
        {
            $booking_status['statusCode'] = 500;
            $booking_status['status'] = 'failed';
            $booking_status['message'] = $status['message'];
        }
        return $booking_status;
    }
    public function driverOnlineStatus(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status'])
        {
            if (DB::table('driver_available')->where('user_id', $mNumber)->exists())
            {
                $driver = DB::table('driver_available')->where('user_id',$mNumber)->first();
                if ($driver->isAvailable == "true")
                {
                    $online_status['statusCode']     = 200;
                    $online_status['status']         = 'success';
                    $online_status['message'] = "Online Status Fetched Successfully";
                    $online_status['online_status'] = "Yes";
                }
                elseif ($driver->isAvailable == "false")
                {
                    $online_status['statusCode']     = 200;
                    $online_status['status']         = 'success';
                    $online_status['message'] = "Online Status Fetched Successfully";
                    $online_status['online_status'] = "No";
                }
            }
            else
            {
                $online_status['statusCode']     = 500;
                $online_status['status']         = 'failed';
                $online_status['message'] = "Driver need to setup the available timing!";
                $online_status['online_status'] = "";
            }
        }
        else
        {
            $online_status['statusCode'] = 500;
            $online_status['status'] = 'failed';
            $online_status['message'] = $status['message'];
        }
        return $online_status;
    }
    public function getDriverAvailableTime(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status'])
        {
            if (DB::table('driver_available')->where('user_id',$mNumber)->exists())
            {
//                $driver = DB::table('driver_available')->where('user_id',$mNumber)->first();
                $table = 'driver_available';
                $driver = DB::select("select monday,tuesday,wednesday,thursday,friday,saturday,sunday from $table where user_id=$mNumber");
                $driverTimings['statusCode']     = 200;
                $driverTimings['status']         = 'success';
                $driverTimings['message'] = "Driver Timing Fetched Successfully";
                $driverTimings['data'] = $driver;
            }
            else
            {
                $driverTimings['statusCode']     = 500;
                $driverTimings['status']         = 'Failed';
                $driverTimings['message'] = "Driver Timing Details are Doesn't exist";
                $driverTimings['data'] = [];
            }
        }
        else
        {
            $driverTimings['statusCode'] = 500;
            $driverTimings['status'] = 'failed';
            $driverTimings['message'] = $status['message'];
        }
        return $driverTimings;
    }
	public function pushNotification($pushFor,$body,$title,$token,$bookingId=null){
		$fcmUrl = 'https://fcm.googleapis.com/fcm/send';

		if ($bookingId == null)
        {
            $notification = [
                'body' => utf8_encode($body),
                'title'     => utf8_encode($title),
	            'pushFor'   => $pushFor
            ];
        }
        else
        {
            $notification = [
                'body' => utf8_encode($body),
                'title'     => utf8_encode($title),
                'orderid'     => $bookingId
            ];
        }

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
		return $result;
	}
	public function token_changed(Request $request){
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status'])
		{
			$device_token=$request->device_token;
			app_users::where('mobile_number', $mNumber)->update(['device_token'=>$device_token]);
			$response['statusCode'] = 200;
			$response['status'] = 'success';
			$response['message'] = "Device token updated successfully.";
		}
		else
		{
			$response['statusCode'] = 500;
			$response['status'] = 'failed';
			$response['message'] = $status['message'];
		}
		return $response;
	}
	public function pendingBookings(Request $request){
		$mNumber=$request->mobile_number;
		$otp=$request->otp;
		$status=$this->verifyOtp($mNumber,$otp);
		if($status['status'])
		{
			$bookings = DB::table('booking')->where('driver_id',$mNumber)->where('status','!=','Car Delivered')->get();
			$assigned['statusCode']     = 200;
			$assigned['status']         = 'success';
			$assigned['message'] = "Pending Booking Fetched Successfully";
			$assigned['number_of_bookings'] = count($bookings);
			$assigned['data'] = $bookings;
		}
		else
		{
			$assigned['statusCode'] = 500;
			$assigned['status'] = 'failed';
			$assigned['message'] = $status['message'];
		}
		return $assigned;
	}
	public function bookingAccept(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status'])
        {
            $booking = DB::table('booking')->whereId($request->booking_id)->first();
            if (($booking->driver_id == '' || empty($booking->driver_id)))
            {
                $update['driver_id'] = $mNumber;
                $update['status'] = "Driver Assigned";
	            date_default_timezone_set('Asia/Muscat');
                $update['last_status_changed_at'] = date("Y-m-d H:i:s");
                $update['last_status_changed_by'] = $mNumber;
                DB::table('booking')->whereId($request->booking_id)->update($update);
                $assigned['statusCode'] = 200;
                $assigned['status'] = 'success';
                $assigned['message'] = "Booking is Successfully Assigned";
                $driver = DB::table('driver_profile')->where('user_id',$mNumber)->first();
                $array = array(
                    'name' => $driver->First_Name.' '.$driver->Last_Name,
                    'contact' => $mNumber,
                );
                $message = "Greeting from Zajil! Your Booking has been Confirmed. Driver Name : ".$array['name']." , Driver Contact : ".$array['contact'].". Thank You ";
//                echo $message;
//                exit;
                $headers = array(
                    'Host: ismartsms.net',
                    'Content-Type: application/json',
                    'Cache-Control: no-cache'
                );
                $requestData = [
                    "UserID" => "zajil_ws",
                    "Password" => "J#cjsw19",
                    "Message" => $message,
                    "Language" => "0",
                    "MobileNo" => $booking->user_id,
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
//                return $data;
                curl_close($ch);
            }
            else
            {
                $assigned['statusCode'] = 500;
                $assigned['status'] = 'failed';
                $assigned['message'] = "Sorry..Booking is Already Assigned!";
            }
        }
        else
        {
            $assigned['statusCode'] = 500;
            $assigned['status'] = 'failed';
            $assigned['message'] = $status['message'];
        }
        return $assigned;
    }
	public function daily_parking_fees(){
	    if (DB::table('daily_parking_fee')->exists())
        {
            $fees=DB::table('daily_parking_fee')->first();
            $assigned['statusCode'] = 200;
            $assigned['status'] = 'success';
            $assigned['fees'] = $fees;
            return $assigned;
        }
        else
        {
            $assigned['statusCode'] = 200;
            $assigned['status'] = 'success';
            $assigned['fees'] = 0;
            return $assigned;
        }
	}

	public function sendNotification()
    {
        date_default_timezone_set('Asia/Muscat');
//	    date_default_timezone_set('Asia/Kolkata');
        if(DB::table('booking')->where('notification_sent',0)->where('driver_id','')->orWhere('driver_id',null)->exists())
        {
            $bookings = DB::table('booking')->where('notification_sent',0)->where('driver_id','')->orWhere('driver_id',null)->get();
//            return $bookings;
            $drivers = DB::table('driver_available')->get();
            $now = strtotime(date('Y-m-d H:i:s'));
            $later = strtotime('+30 minutes', $now);
            $day = strtolower(date('l'));
            foreach($bookings as $booking)
            {
                $trip_time  = strtotime(str_replace('/','-',$booking->first_trip_time));
                $trip_week= strtolower(date('l',$trip_time));
                $date = date('H:i',$trip_time);
//                if($booking->id == 125){
////	                return $booking;
//                	return $trip_time."---".$now."---".$later;
//                }
                if ($trip_time >= $now && $trip_time <= $later && date('d-m-Y',$now) == date('d-m-Y',$trip_time) && $day == $trip_week)
                {
                  foreach ($drivers as $driver)
                  {
                      $driver = (array)$driver;
                      if ($driver[$trip_week] != '' || !empty($driver[$trip_time]))
                      {
                          $driver_time = explode(',',$driver[$trip_week]);
                      }
                      $date= strtotime($date);
                      if ($driver['isAvailable'] == 'true')
                      {
                          $appUser=app_users::where('mobile_number',$driver['user_id'])->first();
                          if($appUser->device_token != ""){
                              $this->pushNotification("driver_notif","New Booking is made, Do you want to Continue?","New Booking",$appUser->device_token,$booking->id);
                          }
                          $update['notification_sent'] = 1;
                          DB::table('booking')->whereId($booking->id)->update($update);
                      }
                      if (isset($driver_time))
                      {
                          foreach ($driver_time as $time)
                          {
                              $start_end = explode('-',$time);
                              $start = strtotime($start_end[0]);
                              $end = strtotime($start_end[1]);
                              if (($start != '' || !empty($start) || $end != '' || !empty($end)) && $date >= $start && $date <= $end && $driver['isAvailable'] != 'true')
                              {
                                  $appUser = app_users::where('mobile_number', $driver['user_id'])->first();
                                  if ($appUser->device_token != "") {
                                      $this->pushNotification("driver_notif","New Booking is made, Do you want to Continue?", "New Booking", $appUser->device_token,$booking->id);
                                  }
                                  $update['notification_sent'] = 1;
                                  DB::table('booking')->whereId($booking->id)->update($update);
//                                  return "done";
                              }
                          }
                      }



                  }
                }
            }
            return "done";
        }
    }
    public function bookingDetails(Request $request)
    {
        $mNumber=$request->mobile_number;
        $otp=$request->otp;
        $status=$this->verifyOtp($mNumber,$otp);
        if($status['status'])
        {
            if (DB::table('booking')->whereId($request->booking_id)->exists())
            {
                $booking = DB::table('booking')->whereId($request->booking_id)->first();
                if ($booking->driver_id == '' || empty($booking->driver_id))
                {
                    $data6['statusCode'] = 200;
                    $data6['status'] = 'success';
                    $data6['message'] = "Booking is Un-assigned";
                    $data6['data'] = $booking;
                }
                elseif ($booking->driver_id == $mNumber)
                {
                    $data6['statusCode'] = 200;
                    $data6['status'] = 'success';
                    $data6['message'] = "Booking is Assigned to current driver";
                    $data6['data'] = $booking;
                }
                else
                {
                    $data6['statusCode'] = 200;
                    $data6['status'] = 'success';
                    $data6['message'] = "Order already assigned to another account";
                }
            }
            else
            {
                $data6['statusCode'] = 500;
                $data6['status'] = 'failed';
                $data6['message'] = "Booking Not Found";
            }
        }
        else
        {
            $data6['statusCode'] = 500;
            $data6['status'] = 'failed';
            $data6['message'] = $status['message'];
        }
        return $data6;
    }
}
