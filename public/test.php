

<?php
// API access key from Google API's Console
define( 'API_ACCESS_KEY', 'AIzaSyD3opS84x3ixtEB48Q6AKWJ_Rzt9MCvDgU' );
$registrationIds = array( "eG5yDfvIH2o:APA91bHZ4y6E7keR4065djsJfKeUkrKFrlgUx6DJB1jBGJDfrQr_kqtmO5pIe8W8MwQK47A06rWrQGPDJ7PxAPtNfyGk6F2oxQjxWNMrLPnl34SkLXbYiA776ukgIgMP6XG1le3VsAu3" );
// prep the bundle
$msg = array
(
    'body'  => utf8_encode('mensagem via api'),
    'title'     => utf8_encode('Hello from Api')
    //'vibrate'   => 1,
    //'sound'     => 1,
);

$fields = array
(
    'data'          => $msg,
    'to'  => '/topics/general'
);

$headers = array
(
    'Authorization: key=' . "AAAA9jBBOzQ:APA91bEn4siDOD9nVFq5gpIQ6gh-ZVi8aLfYpbiYXab2povo1fDLutEuynAMdtfuBfUKZbiA3pXjoTwFONlyTyilISGIETYk8e1y7w_F-pKlTr952nQxwM_aNVbg9KLGlycQptQg10YO",
    'Content-Type: application/json;charset=UTF-8'
);

$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
$result = curl_exec($ch );
curl_close( $ch );
echo $result;
?>