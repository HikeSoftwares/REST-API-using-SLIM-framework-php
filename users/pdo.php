<?php
class users extends dbconn {
	public function __construct()
	{
        $this->initDBO();
    }
    
    public function createUser($token, $name, $email, $password, $phone, $image, $user_type, $user_role, $organization, $location, $internal_business_id = '0'){
        $password = md5($password);

        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $name, $email, $password, $user_type, $user_role)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        if($internal_business_id!='0'){$tokenStatus['business_id'] = $internal_business_id; $tokenStatus['status'] = true;}else{
            $tokenStatus = $this->validateToken($token);
        }

        if($tokenStatus['status'])
        {
                try
                {
                    $sql = 'SELECT id FROM users WHERE email = :email and business_id = :business_id and status!=3';
                    $s = $db->prepare($sql);
                    $s->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $s->bindParam('email', $email);
                    if ($s->execute())
                    {
                        if($s->rowCount() > 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "User already registered with same details.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            require 'sendotp.php';
                            $otp = rand(1000,9999);
                            $obOTP = new sendOTP();
                            $otpStatus = $obOTP->sendOTP($email, $otp, $name);
                            $status = 2;
                            if($otpStatus){
                                $otp_expiry = date('Y-m-d H:i:s', strtotime("+30 min"));
                                $stmt = $db->prepare("insert into users (business_id, name, email, password, phone, image, user_type, user_role, status, otp, otp_expiry, organization, location) values(:business_id, :name, :email, :password, :phone, :image, :user_type, :user_role, :status, :otp, :otp_expiry, :organization, :location)");
                                $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                                $stmt->bindParam("name", $name);
                                $stmt->bindParam("email", $email);
                                $stmt->bindParam("password", $password);
                                $stmt->bindParam("phone", $phone);
                                $stmt->bindParam("image", $image);
                                $stmt->bindParam("user_type", $user_type);
                                $stmt->bindParam("user_role", $user_role);
                                $stmt->bindParam("status", $status);
                                $stmt->bindParam("otp", $otp);
                                $stmt->bindParam("otp_expiry", $otp_expiry);
                                $stmt->bindParam("organization", $organization);
                                $stmt->bindParam("location", $location);
                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['refresh_token'] = '';
                                $stat['auth'] = true;
                                $stat['message'] = "User created successfully.";
                                $stat['data'] = array("users_id"=>$db->lastInsertId());                            
                                return $stat;
                            }else{
                                $stat['status'] = false;
                                $stat['auth'] = '';
                                $stat['message'] = "Something went wrong, Please try again.";
                                $stat['data'] = '';
                                return $stat;
                            }
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
                    $stat['auth'] = true;
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

    function resendEmailOTP($API_KEY, $users_id)
    {        
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($API_KEY, $users_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            $db = $this->dblocal;       
            if($API_KEY == API_KEY)
            {
                try
                {                    
                    $sql = 'SELECT * FROM users WHERE id = :users_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('users_id', $users_id, PDO::PARAM_INT);
                    $queryResponse = $s->execute();
                    $rowsCount = $s->rowCount();
                    $userData = $s->fetchAll(PDO::FETCH_ASSOC);   
                    if ($queryResponse)
                    {                   
                        if($rowsCount == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "User does not exist.";
                            $stat['data'] = '';
                            return $stat;
                        }else if($userData[0]['status'] != 2)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "User disabled or already activated.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            require 'sendotp.php';
                            $otp = rand(1000,9999);
                            $obOTP = new sendOTP();
                            $otpStatus = $obOTP->sendOTP($userData[0]['email'], $otp, $userData[0]['name']);                                                    
                            if($otpStatus){
                                $otp_expiry = date('Y-m-d H:i:s', strtotime("+30 min"));
                                $stmt = $db->prepare("update users set otp = :otp, otp_expiry = :otp_expiry where id = :users_id");
                                $stmt->bindParam("otp", $otp);
                                $stmt->bindParam("otp_expiry", $otp_expiry);
                                $stmt->bindParam("users_id", $users_id);
                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['refresh_token'] = '';
                                $stat['auth'] = true;
                                $stat['message'] = "OTP sent successfully.";
                                $stat['data'] = $users_id;                            
                                return $stat;
                            }else{
                                $stat['status'] = false;
                                $stat['auth'] = '';
                                $stat['message'] = "Something went wrong, Please try again.";
                                $stat['data'] = '';
                                return $stat;
                            }
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
                    $stat['auth'] = true;
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
    public function deleteUser($token, $user_id){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $user_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $sql = 'SELECT id FROM users WHERE id = :id and status !=3';
                    $s = $db->prepare($sql);
                    $s->bindParam('id', $user_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "User not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            $deleted_at = date("Y-m-d h:i:s");
                            $stmt = $db->prepare("update users set status = 3, deleted_at = :deleted_at where id = :id and business_id = :business_id");
                            $stmt->bindParam("id", $user_id);
                            $stmt->bindParam("deleted_at", $deleted_at);
                            $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                            $stmt->execute();
                            $stat['status'] = true;
                            $stat['auth'] = true;
                            $stat['refresh_token'] = $tokenStatus['refresh_token'];
                            $stat['message'] = "User deleted successfully.";
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
                    $stat['auth'] = true;
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

    public function getUsersList($token, $from, $no_of_records){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $from, $no_of_records)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;        
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $cnt = $db->prepare("select COUNT(*) from users where business_id = :business_id and status!=3");
                    $cnt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $cnt->execute();
                    $numberofrows = $cnt->fetchColumn();

                    $stmt = $db->prepare("select * from users where business_id = :business_id and status!=3 order By id Desc LIMIT ".$from.", ".$no_of_records);
                    $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    //$stmt->debugDumpParams(); exit; 
                    $stmt->execute();
                    
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    for($i = 0; $i<sizeof($data);$i++){
                        $data[$i]['password'] = '';
                    }

                    $stat['status'] = true;
                    $stat['auth'] = true;
                    $stat['total_records'] = $numberofrows;
                    $stat['refresh_token'] = $tokenStatus['refresh_token'];
                    $stat['message'] = "success.";
                    $stat['data'] = $data;
                    return $stat;
                }
                catch(PDOException $ex)
                {
                    $stat['status'] = false;
                    $stat['auth'] = true;
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
     

     public function getUserDetails($token, $user_id){
         ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $user_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);        
        if($tokenStatus['status']){
            try
            {

              $stmt = $db->prepare("select * from users where id=:users_id order By id Desc");
              $stmt->bindParam("users_id", $user_id);
              $stmt->execute();       
              $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
              $data[0]['password'] = '';
               
              $stat['status'] = true;
              $stat['auth'] = true;
              $stat['refresh_token'] = $tokenStatus['refresh_token'];
              $stat['message'] = "success.";
              $stat['data'] = $data;
              return $stat;
            }
            catch(PDOException $ex)
            {
                $stat['status'] = false;
                $stat['auth'] = true;
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


     public function updateUserStatus($token, $users_id, $status){
         ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $users_id, $status)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);        
            if($tokenStatus['status'])
            {
                try
                {
                    $sql = 'SELECT id FROM users WHERE id = :users_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('users_id', $users_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "User not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                                $stmt = $db->prepare("update users set status = :status where id = :users_id");
                                $stmt->bindParam("users_id", $users_id, PDO::PARAM_INT);
                                $stmt->bindParam("status", $status, PDO::PARAM_INT);
                                $stmt->execute();                             
                                $stat['status'] = true;
                                $stat['auth'] = true;
                                $stat['refresh_token'] = $tokenStatus['refresh_token'];
                                $stat['message'] = "Status updated successfully.";
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
                    $stat['auth'] = true;
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
     
     

     public function updateUserDetails($token, $users_id, $name, $email, $phone, $image, $user_type, $user_role){
         ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $users_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $sql = 'SELECT id FROM users WHERE id = :users_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('users_id', $users_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "User not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{

                            if(strlen($name)>0 || strlen($email)>0 || strlen($phone)>0 || strlen($image)>0 || strlen($user_type)>0 || strlen($user_role)>0){
                                $updatestring = "";                            
                                if(strlen($name)>0){ $updatestring .= " name = :name,"; }
                                if(strlen($email)>0){ $updatestring .= " email = :email,"; }
                                if(strlen($phone)>0){ $updatestring .= " phone = :phone,"; }
                                if(strlen($image)>0){ $updatestring .= " image = :image,"; }
                                if(strlen($user_type)>0){ $updatestring .= " user_type = :user_type,"; }
                                if(strlen($user_role)>0){ $updatestring .= " user_role = :user_role,"; }
                                $updatestring = substr($updatestring, 0, -1);
                                $stmt = $db->prepare("update users set ".$updatestring." where id = :users_id");
                                $stmt->bindParam("users_id", $users_id, PDO::PARAM_INT);
                                if(strlen($name)>0){ $stmt->bindParam("name", $name); }
                                if(strlen($email)>0){ $stmt->bindParam("email", $email); }
                                if(strlen($phone)>0){ $stmt->bindParam("phone", $phone); }
                                if(strlen($image)>0){ $stmt->bindParam("image", $image); }
                                if(strlen($user_type)>0){ $stmt->bindParam("user_type", $user_type); }
                                if(strlen($user_role)>0){ $stmt->bindParam("user_role", $user_role); }
                                //$stmt->debugDumpParams(); exit; 
                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['auth'] = true;
                                $stat['refresh_token'] = $tokenStatus['refresh_token'];
                                $stat['message'] = "User updated successfully.";
                                $stat['data'] = '';
                                return $stat;
                            }
                            else{
                                $stat['status'] = false;
                                $stat['auth'] = '';
                                $stat['message'] = "Nothing to update.";
                                $stat['data'] = '';
                                return $stat;
                            }
                            
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
                    $stat['auth'] = true;
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