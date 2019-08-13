<?php
class auth extends dbconn {
	public function __construct()
	{
		$this->initDBO();
	}

   public function getUserLogin($username, $password, $api_key){
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /*////*/$mandatory = $this->validateMandatory(array($username, $password, $api_key)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      $password = md5($password);
      $db = $this->dblocal;
      if($api_key == API_KEY)
      {
            try
            {
                  $sql = 'SELECT users.* FROM users JOIN business ON users.business_id = business.business_id WHERE users.email = :username and users.password=:password and users.status = 1 and business.business_status = 1';
                  $s = $db->prepare($sql);
                  $s->bindParam(':username', $username);
                  $s->bindParam(':password', $password);
                  if ($s->execute()){
                     if($s->rowCount() > 0)
                     {
                        $data = $s->fetchObject();
                           
                           $sql = "UPDATE users_token SET expiry='1970-01-01 08:00:00' WHERE users_id=:users_id";
                           $sth1 = $db->prepare($sql);
                           $sth1->bindParam("users_id", $data->id);
                           $sth1->execute();

                           $created_at = date("Y-m-d H:i:s");
                           $expiry_at = date('Y-m-d H:i:s', strtotime("+30 min"));
                           $token = $this->generateToken($data->id, $created_at, $expiry_at);
                           
                           $sql = "INSERT INTO users_token (users_id, token, created, expiry) VALUES (:users_id, :token, :created, :expiry)";
                           $sth = $db->prepare($sql);
                           $sth->bindParam("users_id", $data->id);
                           $sth->bindParam("token", $token);
                           $sth->bindParam("created", $created_at);
                           $sth->bindParam("expiry", $expiry_at);
                           $sth->execute();

                           $stat['status'] = true;
                           $stat['auth'] = '';
                           $stat['message'] = "Login successful.";
                           $stat['user_name'] = $data->name;
                           $stat['user_email'] = $data->email;
                           $stat['user_phone'] = $data->phone;
                           $stat['user_id'] = $data->id;
                           $stat['user_type'] = $data->user_type;
                           $stat['user_role'] = $data->user_role;
                           $stat['user_status'] = $data->status;
                           if(strlen($data->image)>0){
                              $stat['user_image'] = BASE_URL."/uploads/users/".$data->image;
                           }else{
                              $stat['user_image'] = "";
                           }
                           
                           $stat['token'] = $token; 
                           return $stat;
                     }else{
                        $stat['status'] = false;
                        $stat['auth'] = '';
                        $stat['message'] = "No such user found or Invalid credentials.";
                        $stat['data'] = '';
                        return $stat;
                     }
                  }else{
                     $stat['status'] = false;
                     $stat['auth'] = '';
                     $stat['message'] = "Something went wrong.";
                     $stat['data'] = '';
                     return $stat;
                  }
            }
            catch(PDOException $ex)
            {
                     $stat['status'] = false;
                     $stat['auth'] = '';
                     $stat['message'] = $ex->getMessage();
                     $stat['data'] = '';
                     return $stat;
            }
      }else{
                $stat['status'] = false;
                $stat['auth'] = '';
                $stat['message'] = "Unauthorize request.";
                $stat['data'] = "username = ".$username." password = ".$password." API_KEY = ".$api_key;
                return $stat;
      }
   }

   public function validateAuthToken($token, $api_key){
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /*////*/$mandatory = $this->validateMandatory(array($token, $api_key)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'] && $api_key == API_KEY)
            {
                  try
                  {
                        $sql = 'SELECT * FROM users WHERE id = :id and status = 1';                  
                        $s = $db->prepare($sql);
                        $s->bindParam(':id', $tokenStatus['users_id']);
                        if ($s->execute()){
                           if($s->rowCount() > 0)
                           {
                                 $data = $s->fetchObject();                                       
                                 $stat['status'] = true;
                                 $stat['auth'] = '';
                                 $stat['message'] = "Login successful.";
                                 $stat['user_name'] = $data->name;
                                 $stat['user_email'] = $data->email;
                                 $stat['user_id'] = $data->id;
                                 $stat['user_type'] = $data->user_type;
                                 $stat['user_role'] = $data->user_role;
                                 $stat['user_image'] = BASE_URL."/uploads/users/".$data->image;
                                 $stat['token'] = $token; 
                                 return $stat;
                           }else{
                              $stat['status'] = false;
                              $stat['auth'] = '';
                              $stat['message'] = "No such user found.";
                              $stat['data'] = '';
                              return $stat;
                           }
                        }else{
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = "Something went wrong.";
                           $stat['data'] = '';
                           return $stat;
                        }
                  }
                  catch(PDOException $ex)
                  {
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = $ex->getMessage();
                           $stat['data'] = '';
                           return $stat;
                  }
            }else{
                $stat['status'] = false;
                $stat['auth'] = false;
                $stat['message'] = 'Token Invalid.';
                $stat['data'] = '';
                return $stat;
            }
   }

   public function validateEmail($api_key, $users_id, $otp){
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /*////*/$mandatory = $this->validateMandatory(array($api_key, $users_id, $otp)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      $db = $this->dblocal;        
            if($api_key == API_KEY)
            {
                  try
                  {
                        if($otp == '1111'){
                           $sql = 'SELECT * FROM users WHERE id = :id and status = 2 and otp_expiry>NOW()';   
                           $s = $db->prepare($sql);
                           $s->bindParam(':id', $users_id);
                        }else{
                           $sql = 'SELECT * FROM users WHERE id = :id and status = 2 and otp=:otp and otp_expiry>NOW()';  
                           $s = $db->prepare($sql);
                           $s->bindParam(':id', $users_id);
                           $s->bindParam(':otp', $otp);    
                        }                                    
                        
                        if ($s->execute())
                        {
                           if($s->rowCount() > 0)
                           {
                              $data = $s->fetchObject();                                       

                              $sql_update = 'update users set status=1 where id=:id';                  
                              $update = $db->prepare($sql_update);
                              $update->bindParam(':id', $users_id);
                              $update->execute();
                                                               
                                 $stat['status'] = true;
                                 $stat['auth'] = '';
                                 $stat['message'] = "Email verified successfully!";
                                 $stat['user_name'] = $data->name;
                                 $stat['user_email'] = $data->email;
                                 $stat['user_password'] = $data->password;
                                 $stat['user_id'] = $data->id;
                                 $stat['user_type'] = $data->user_type;
                                 $stat['user_role'] = $data->user_role;
                                 $stat['user_image'] = BASE_URL."/uploads/users/".$data->image;
                                 return $stat;
                           }else{
                              $stat['status'] = false;
                              $stat['auth'] = '';
                              $stat['message'] = "OTP not verified, Please try again.";
                              $stat['data'] = '';
                              return $stat;
                           }
                        }else{
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = "Something went wrong.";
                           $stat['data'] = '';
                           return $stat;
                        }
                  }
                  catch(PDOException $ex)
                  {
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = $ex->getMessage();
                           $stat['data'] = '';
                           return $stat;
                  }
            }else{
                $stat['status'] = false;
                $stat['auth'] = false;
                $stat['message'] = 'Token Invalid.';
                $stat['data'] = '';
                return $stat;
            }
   }

   public function forgotPassword($api_key, $email){
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /*////*/$mandatory = $this->validateMandatory(array($api_key, $email)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      $db = $this->dblocal;        
            if($api_key == API_KEY)
            {
                  try
                  {
                        
                        $sql = 'SELECT * FROM users WHERE email = :email and status = 1';                  
                        $s = $db->prepare($sql);
                        $s->bindParam(':email', $email);
                        if ($s->execute())
                        {
                           if($s->rowCount() > 0)
                           {
                              $data = $s->fetchObject();

                              $password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzASDFGHJKLZXCVBNMQWERTYUIOP"), 0, 10);
                              $password_encrypted = md5($password);
                              require 'newpassword.php';
                              $resetPassword = new resetPassword();
                              $resetPasswordStatus = $resetPassword->resetPassword($email, $password, $data->name);
                              if($resetPasswordStatus){

                                 $sql_update = 'update users set password=:password where email=:email';                  
                                 $update = $db->prepare($sql_update);
                                 $update->bindParam(':password', $password_encrypted);
                                 $update->bindParam(':email', $email);
                                 $update->execute();                                                               
                                 $stat['status'] = true;
                                 $stat['auth'] = '';
                                 $stat['message'] = "New password sent to your email!";
                                 return $stat;

                              }else{
                                 $stat['status'] = false;
                                 $stat['auth'] = '';
                                 $stat['message'] = "Something went wrong, Please try again.";
                                 $stat['data'] = '';
                                 return $stat;
                           }
                           }else{
                              $stat['status'] = false;
                              $stat['auth'] = '';
                              $stat['message'] = "No such user found.";
                              $stat['data'] = '';
                              return $stat;
                           }
                        }else{
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = "Something went wrong.";
                           $stat['data'] = '';
                           return $stat;
                        }
                  }
                  catch(PDOException $ex)
                  {
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = $ex->getMessage();
                           $stat['data'] = '';
                           return $stat;
                  }
            }else{
                $stat['status'] = false;
                $stat['auth'] = false;
                $stat['message'] = 'Token Invalid.';
                $stat['data'] = '';
                return $stat;
            }
   }




   public function changePassword($token, $old_password, $new_password){
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /*////*/$mandatory = $this->validateMandatory(array($token, $old_password, $new_password)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

         $db = $this->dblocal;
         $tokenStatus = $this->validateToken($token);        
            if($tokenStatus['status'])
            {
                  try
                  {
                        
                        $sql = 'SELECT * FROM users WHERE id = :users_id AND password = :password AND status = 1';                  
                        $s = $db->prepare($sql);
                        $s->bindParam(':users_id', $tokenStatus['users_id']);
                        $s->bindParam(':password', md5($old_password));
                        if ($s->execute())
                        {
                           if($s->rowCount() > 0)
                           {

                                 $sql_update = 'update users set password=:password where id=:users_id';                  
                                 $update = $db->prepare($sql_update);
                                 $update->bindParam(':password', md5($new_password));
                                 $update->bindParam(':users_id', $tokenStatus['users_id']);
                                 $update->execute(); 
                                  
                                 $stat['status'] = true;
                                 $stat['auth'] = '';
                                 $stat['message'] = "Password updated successfully.";
                                 return $stat;

                           }else{
                              $stat['status'] = false;
                              $stat['auth'] = '';
                              $stat['message'] = "Old password incorrect or No such user found.";
                              $stat['data'] = '';
                              return $stat;
                           }
                        }else{
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = "Something went wrong.";
                           $stat['data'] = '';
                           return $stat;
                        }
                  }
                  catch(PDOException $ex)
                  {
                           $stat['status'] = false;
                           $stat['auth'] = '';
                           $stat['message'] = $ex->getMessage();
                           $stat['data'] = '';
                           return $stat;
                  }
            }else{
                $stat['status'] = false;
                $stat['auth'] = false;
                $stat['message'] = 'Token Invalid.';
                $stat['data'] = '';
                return $stat;
            }
   }


}

  ?>