<?php 

class resetPassword{   
    function __Construct(){
        date_default_timezone_set("Asia/Kolkata");
        require_once('../../vendor/phpmailer/class.phpmailer.php');
        require_once("../../vendor/phpmailer/class.smtp.php");  
        require_once("../dbconn.php");              
    }
    public function resetPassword($email, $password, $name, $print = 0){	
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,BASE_URL."/v1/auth/mail.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"email=$email&password=$password&name=$name&BASE_URL=".BASE_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return true;
        }
}
?>    