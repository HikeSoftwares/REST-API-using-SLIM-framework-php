<?php 
class sendOTP{   
    function __Construct(){
        date_default_timezone_set("Asia/Kolkata");
        require_once('../../vendor/phpmailer/class.phpmailer.php');
        require_once("../../vendor/phpmailer/class.smtp.php");  
        require_once("../dbconn.php");              
    }
    public function sendOTP($email, $otp, $name, $print = 0){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,BASE_URL."/v1/users/mail.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"email=$email&otp=$otp&name=$name&BASE_URL=".BASE_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return true;
        }
}
if(isset($_GET['msg'])){
   $ob = new sendOTP();
   $ob->sendOTP('jktech11@gmail.com', '11', 'Rajeev', $print = 1);
}
?>    