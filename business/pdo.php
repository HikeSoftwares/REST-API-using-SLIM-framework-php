<?php
class business extends dbconn {
	public function __construct()
	{
        $this->initDBO();        
    }
    
    public function createBusiness($API_KEY, $business_name, $name, $email, $password, $phone, $business_logo){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($API_KEY, $business_name, $name, $email, $password)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            $db = $this->dblocal;
            if($API_KEY == API_KEY)
            {
                try
                {
                    $sql = 'SELECT business_id FROM business WHERE business_name = :business_name and business_status!=2';
                    $s = $db->prepare($sql);
                    $s->bindParam('business_name', $business_name);
                    if ($s->execute())
                    {
                        if($s->rowCount() > 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "Business already exists.";
                            $stat['data'] = '';
                            return $stat;
                        }else{

                                $stmt = $db->prepare("insert into business (business_name, business_status, business_logo) values(:business_name, 1, :business_logo)");
                                $stmt->bindParam("business_name", $business_name);
                                $stmt->bindParam("business_logo", $business_logo);
                                $stmt->execute();
                                $business_id = $db->lastInsertId();

                                include("../users/pdo.php");
                                $usersOb = new users();
                                $usersOb->createUser($API_KEY, $name, $email, $password, $phone, '', 'admin', 'admin', '', '', $business_id);

                                $stat['status'] = true;
                                $stat['refresh_token'] = '';
                                $stat['auth'] = true;
                                $stat['message'] = "Business created successfully.";
                                $stat['data'] = array("business_id"=>$business_id);
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

    public function deleteBusiness($token, $business_id){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($token, $business_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);        
            if($tokenStatus['status'])
            {
                try
                {

                    $sql = 'SELECT business_id FROM business WHERE business_id = :business_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('business_id', $business_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "business not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            $deleted_at = date("Y-m-d h:i:s");
                            $stmt = $db->prepare("update business set business_status = 2, business_deleted_at = :deleted_at where business_id = :business_id");
                            $stmt->bindParam("deleted_at", $deleted_at);
                            $stmt->bindParam("business_id", $business_id, PDO::PARAM_INT);
                            $stmt->execute();                             
                            $stat['status'] = true;
                            $stat['auth'] = true;
                            $stat['refresh_token'] = $tokenStatus['refresh_token'];
                            $stat['message'] = "business deleted successfully.";
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

    public function getBusinessList($token, $from, $no_of_records){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($token, $from, $no_of_records)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;        
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {                
                try
                {
                    $cnt = $db->prepare("select COUNT(*) from business where (business_status = 0 OR business_status = 1)");
                    $cnt->execute();
                    $numberofrows = $cnt->fetchColumn();

                    $stmt = $db->prepare("select * from business where (business_status = 0 OR business_status = 1) order By business_id Desc LIMIT ".$from.", ".$no_of_records);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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


    public function getBusinessDetails($token, $business_id){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $business_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $db = $this->dblocal;        
    $tokenStatus = $this->validateToken($token);
        if($tokenStatus['status'])
        {                
            try
            {
                $cnt = $db->prepare("select COUNT(*) from business where business_id = :business_id and (business_status=0 OR business_status=1)");
                $cnt->bindParam("business_id", $business_id, PDO::PARAM_INT);
                $cnt->execute();
                $numberofrows = $cnt->fetchColumn();

                $stmt = $db->prepare("select * from business where (business_status = 0 OR business_status = 1) and business_id = :business_id order By business_id Desc");
                $stmt->bindParam("business_id", $business_id, PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    public function updateBusiness($token, $business_id, $business_name, $business_status, $business_logo, $fb_app_id, $fb_secret_key, $fb_auth_token, $twitter_consumer_key, $twitter_consumer_secret, $twitter_token, $twitter_token_secret, $twitter_username, $fb_msg_mention, $fb_msg_direct_message, $fb_msg_new_followers, $tw_msg_mention, $tw_msg_direct_message, $tw_msg_retweets, $tw_msg_retweets_comments, $tw_msg_new_followers){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($token, $business_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $sql = 'SELECT business_id FROM business WHERE business_id = :business_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('business_id', $business_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "business not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            if(strlen($business_name)>0 || strlen($business_status)>0 || strlen($business_logo)>0 || strlen($fb_app_id)>0 || strlen($fb_secret_key)>0 || strlen($fb_auth_token)>0 || strlen($twitter_consumer_key)>0 || strlen($twitter_consumer_secret)>0 || strlen($twitter_token)>0 || strlen($twitter_token_secret)>0 || strlen($twitter_username)>0){
                                
                                $updatestring = "";
                                if(strlen($business_name)>0){ $updatestring .= " business_name = :business_name,"; }
                                if(strlen($business_status)>0){ 
                                    if($business_status != 0 && $business_status != 1){
                                        $stat['status'] = false;
                                        $stat['auth'] = true;
                                        $stat['message'] = "business status '".$business_status."' not allowed.";
                                        $stat['data'] = '';
                                        return $stat;
                                    }else{
                                        $updatestring .= " business_status = :business_status,"; 
                                    }
                                }
                                if(strlen($business_logo)>0){ $updatestring .= " business_logo = :business_logo,"; }
                                if(strlen($fb_app_id)>0){ $updatestring .= " fb_app_id = :fb_app_id,"; }
                                if(strlen($fb_secret_key)>0){ $updatestring .= " fb_secret_key = :fb_secret_key,"; }
                                if(strlen($fb_auth_token)>0){ $updatestring .= " fb_auth_token = :fb_auth_token,"; }
                                if(strlen($twitter_consumer_key)>0){ $updatestring .= " twitter_consumer_key = :twitter_consumer_key,"; }
                                if(strlen($twitter_consumer_secret)>0){ $updatestring .= " twitter_consumer_secret = :twitter_consumer_secret,"; }
                                if(strlen($twitter_token)>0){ $updatestring .= " twitter_token = :twitter_token,"; }
                                if(strlen($twitter_token_secret)>0){ $updatestring .= " twitter_token_secret = :twitter_token_secret,"; }
                                if(strlen($twitter_username)>0){ $updatestring .= " twitter_username = :twitter_username,"; }

                                if(strlen($fb_msg_mention)>0){ $updatestring .= " fb_msg_mention = :fb_msg_mention,"; }
                                if(strlen($fb_msg_direct_message)>0){ $updatestring .= " fb_msg_direct_message = :fb_msg_direct_message,"; }
                                if(strlen($fb_msg_new_followers)>0){ $updatestring .= " fb_msg_new_followers = :fb_msg_new_followers,"; }
                                if(strlen($tw_msg_mention)>0){ $updatestring .= " tw_msg_mention = :tw_msg_mention,"; }
                                if(strlen($tw_msg_direct_message)>0){ $updatestring .= " tw_msg_direct_message = :tw_msg_direct_message,"; }
                                if(strlen($tw_msg_retweets)>0){ $updatestring .= " tw_msg_retweets = :tw_msg_retweets,"; }
                                if(strlen($tw_msg_retweets_comments)>0){ $updatestring .= " tw_msg_retweets_comments = :tw_msg_retweets_comments,"; }
                                if(strlen($tw_msg_new_followers)>0){ $updatestring .= " tw_msg_new_followers = :tw_msg_new_followers,"; }

                                $updatestring = substr($updatestring, 0, -1);
                                $business_updated_at = date("Y-m-d h:i:s");
                                $stmt = $db->prepare("update business set ".$updatestring.", business_updated_at = :business_updated_at where business_id = :business_id");
                                $stmt->bindParam("business_id", $business_id, PDO::PARAM_INT);
                                $stmt->bindParam("business_updated_at", $business_updated_at);
                                if(strlen($business_name)>0){ $stmt->bindParam("business_name", $business_name); }
                                if(strlen($business_status)>0){ $stmt->bindParam("business_status", $business_status); }
                                if(strlen($business_logo)>0){ $stmt->bindParam("business_logo", $business_logo); }
                                if(strlen($fb_app_id)>0){ $stmt->bindParam("fb_app_id", $fb_app_id); }
                                if(strlen($fb_secret_key)>0){ $stmt->bindParam("fb_secret_key", $fb_secret_key); }
                                if(strlen($fb_auth_token)>0){ $stmt->bindParam("fb_auth_token", $fb_auth_token); }
                                if(strlen($twitter_consumer_key)>0){ $stmt->bindParam("twitter_consumer_key", $twitter_consumer_key); }
                                if(strlen($twitter_consumer_secret)>0){ $stmt->bindParam("twitter_consumer_secret", $twitter_consumer_secret); }
                                if(strlen($twitter_token)>0){ $stmt->bindParam("twitter_token", $twitter_token); }
                                if(strlen($twitter_token_secret)>0){ $stmt->bindParam("twitter_token_secret", $twitter_token_secret); }
                                if(strlen($twitter_username)>0){ $stmt->bindParam("twitter_username", $twitter_username); }
                                if(strlen($fb_msg_mention)>0){ $stmt->bindParam("fb_msg_mention", $fb_msg_mention); }
                                if(strlen($fb_msg_direct_message)>0){ $stmt->bindParam("fb_msg_direct_message", $fb_msg_direct_message); }
                                if(strlen($fb_msg_new_followers)>0){ $stmt->bindParam("fb_msg_new_followers", $fb_msg_new_followers); }
                                if(strlen($tw_msg_mention)>0){ $stmt->bindParam("tw_msg_mention", $tw_msg_mention); }
                                if(strlen($tw_msg_direct_message)>0){ $stmt->bindParam("tw_msg_direct_message", $tw_msg_direct_message); }
                                if(strlen($tw_msg_retweets)>0){ $stmt->bindParam("tw_msg_retweets", $tw_msg_retweets); }
                                if(strlen($tw_msg_retweets_comments)>0){ $stmt->bindParam("tw_msg_retweets_comments", $tw_msg_retweets_comments); }
                                if(strlen($tw_msg_new_followers)>0){ $stmt->bindParam("tw_msg_new_followers", $tw_msg_new_followers); }

                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['auth'] = true;
                                $stat['refresh_token'] = $tokenStatus['refresh_token'];
                                $stat['message'] = "business updated successfully.";
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