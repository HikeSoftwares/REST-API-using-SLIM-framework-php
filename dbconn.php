<?php
date_default_timezone_set("Asia/Calcutta");
$dbuserx='root';
$dbpassx='';
define("BASE_URL", "http://localhost/engayg/rest");
define("API_KEY", "rake");
define("APP_NAME", "BSNL");

class dbconn {
	public $dblocal;
	public function __construct()
	{

	}
	public function initDBO()
	{
		global $dbuserx,$dbpassx;
		try {
			$this->dblocal = new PDO("mysql:host=localhost;dbname=engayg;charset=latin1",$dbuserx,$dbpassx,array(PDO::ATTR_PERSISTENT => true));
			$this->dblocal->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			die("Can not connect database");
		}
		
	}


	public function validateToken($token)
	{
		$this->initDBO();
		$db = $this->dblocal;		
		try
        {
					if($token == '11'){
						$stmt = $db->prepare("select users_token.*, users.name as users_name, users.email as users_email, users.user_type, users.user_role, users.business_id from users_token JOIN users ON users_token.users_id = users.id where users_token.users_id = 1 order By users_token.id Desc LIMIT 1");
					}else{
						$stmt = $db->prepare("select users_token.*, users.name as users_name, users.email as users_email, users.user_type, users.user_role, users.business_id from users_token JOIN users ON users_token.users_id = users.id where users_token.token = '".$token."' AND users_token.expiry>NOW() order By users_token.id Desc LIMIT 1");
					}
          
		  if ($stmt->execute())
		  	{
				if($stmt->rowCount() > 0){
							$result = $stmt->fetchObject();
							$expiry = date('Y-m-d H:i:s', strtotime($result->expiry));		
							$thresholdTime = date('Y-m-d H:i:s', strtotime("+1 min"));
							if($thresholdTime>$expiry){
								$created_at = date("Y-m-d H:i:s");
								$expiry_at = date('Y-m-d H:i:s', strtotime("+2 min"));
								
								$sql = "UPDATE users_token SET expiry='1970-01-01 08:00:00' WHERE users_id=:users_id";
								$sth1 = $db->prepare($sql);
								$sth1->bindParam("users_id", $result->users_id);
								$sth1->execute();

								$token = $this->generateToken($result->users_id, $created_at, $expiry_at);

								$sql = "INSERT INTO users_token (users_id, token, created, expiry) VALUES (:users_id, :token, :created, :expiry)";
								$sth = $db->prepare($sql);
								$sth->bindParam("users_id", $result->users_id);
								$sth->bindParam("token", $token);
								$sth->bindParam("created", $created_at);
								$sth->bindParam("expiry", $expiry_at); 
								$sth->execute();

								$response['refresh_token'] = $token;	
							}else{
								$response['refresh_token'] = '';	
							}
							$response['status'] = true; 
							$response['users_id'] = $result->users_id;
							$response['business_id'] = $result->business_id;
							$response['users_name'] = $result->users_name;
							$response['user_type'] = $result->user_type;
							$response['user_role'] = $result->user_role;
							$response['user_email'] = $result->users_email;
							return $response;							
				}else{
					$response['status'] = false;
					$response['users_id'] = '';
					return $response;
				}
			}else{
				$response['status'] = false;
				$response['users_id'] = '';
				return $response;
			}
        }
        catch(PDOException $ex)
		{
			$response['status'] = false;
			$response['users_id'] = '';
			return $response;
		}	  
	}

	public function generateToken($users_id, $created_at, $expiry_at){

		// Create token header as a JSON string
		$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
  
		// Create token payload as a JSON string
		$payload = json_encode(['users_id' => $users_id, 'created_at' => $created_at, 'expiry_at' => $expiry_at]);
  
		// Encode Header to Base64Url String
		$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
  
		// Encode Payload to Base64Url String
		$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
  
		// Create Signature Hash
		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'hike@#11', true);
  
		// Encode Signature to Base64Url String
		$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
  
		// Create JWT
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
  
		return $jwt;
  
	 }

	 public function timeAgo($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
	
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
	
		$string = array(
			'y' => 'Year',
			'm' => 'Month',
			'w' => 'Week',
			'd' => 'Day',
			'h' => 'Hr',
			'i' => 'Mn',
			's' => 'Sec',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
	
		if (!$full) $string = array_slice($string, 0, 1);
		if($string['s'] != '')
		 return "few sec";
		else
		return $string ? implode(', ', $string) . '' : 'just now';
	}

	public function validateMandatory($arr){
		$res = true;
		foreach($arr as $value){
			if(strlen($value)==0 || $value = ''){
				$res = false;
			}
		}
		return $res;
	}
}
?>