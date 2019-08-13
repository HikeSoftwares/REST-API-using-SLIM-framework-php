<?php
class pos extends dbconn {
	public function __construct()
	{
        $this->initDBO();
    }

    public function createCompetitors($token, $competitors_name, $facebook_url, $twitter_url, $twitter_handle, $competitors_logo){
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /*////*/$mandatory = $this->validateMandatory(array($token, $competitors_name, $competitors_logo)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
        if($tokenStatus['status'])
        {
                try
                {
                    $sql = 'SELECT competitors_id FROM competitors WHERE competitors_name = :competitors_name and business_id = :business_id';
                    $s = $db->prepare($sql);
                    $s->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $s->bindParam('competitors_name', $competitors_name);
                    if ($s->execute())
                    {
                        if($s->rowCount() > 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "Competition profile already exists.";
                            $stat['data'] = '';
                            return $stat;
                        }else{

                            $cnt = $db->prepare("select COUNT(*) from competitors where (competitors_status = 0 OR competitors_status = 1) and business_id = :business_id");
                            $cnt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                            $cnt->execute();
                            $numberofrows = $cnt->fetchColumn();
                            if($numberofrows>=3){
                                $stat['status'] = false;
                                $stat['auth'] = true;
                                $stat['message'] = "Competition profile reaches maximum limit.";
                                $stat['data'] = '';
                                return $stat;
                            }else{

                                //$otp_expiry = date('Y-m-d H:i:s', strtotime("+30 min"));
                                $stmt = $db->prepare("insert into competitors (business_id, competitors_name, competitors_logo, facebook_url, twitter_url, twitter_handle, competitors_status) values(:business_id, :competitors_name, :competitors_logo, :facebook_url, :twitter_url, :twitter_handle, 1)");
                                $stmt->bindParam("competitors_name", $competitors_name);
                                $stmt->bindParam("competitors_logo", $competitors_logo);
                                $stmt->bindParam("facebook_url", $facebook_url);
                                $stmt->bindParam("twitter_url", $twitter_url);
                                $stmt->bindParam("twitter_handle", $twitter_handle);
                                $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['refresh_token'] = '';
                                $stat['auth'] = true;
                                $stat['message'] = "Competition profile created successfully.";
                                $stat['data'] = array("competition_id"=>$db->lastInsertId());
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


    public function getCompetitorsList($token, $from, $no_of_records){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $from, $no_of_records)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;        
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $cnt = $db->prepare("select COUNT(*) from competitors where (competitors_status = 0 OR competitors_status = 1) and business_id = :business_id");
                    $cnt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $cnt->execute();
                    $numberofrows = $cnt->fetchColumn();

                    $stmt = $db->prepare("select * from competitors where (competitors_status = 0 OR competitors_status = 1) and business_id = :business_id order By competitors_id Desc LIMIT ".$from.", ".$no_of_records);
                    $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
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

    public function getCompetitorsDetails($token, $competitors_id){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       /*////*/$mandatory = $this->validateMandatory(array($token, $competitors_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
       ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

       $db = $this->dblocal;
       $tokenStatus = $this->validateToken($token);
           if($tokenStatus['status'])
           {
               try
               {
                   $sql = 'select * from competitors where (competitors_status = 0 OR competitors_status = 1) and business_id = :business_id and competitors_id = :competitors_id';
                   $s = $db->prepare($sql);
                   $s->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                   $s->bindParam("competitors_id", $competitors_id, PDO::PARAM_INT);
                   if ($s->execute())
                   {
                       if($s->rowCount() == 0)
                       {
                           $stat['status'] = false;
                           $stat['auth'] = true;
                           $stat['message'] = "Competitor prifile not found.";
                           $stat['data'] = '';
                           return $stat;
                       }else{

                        $data = $s->fetchAll(PDO::FETCH_ASSOC);
    
                        $stat['status'] = true;
                        $stat['auth'] = true;
                        $stat['refresh_token'] = $tokenStatus['refresh_token'];
                        $stat['message'] = "success.";
                        $stat['data'] = $data;
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


    public function updateCompetitor($token, $competitors_id, $competitors_name, $facebook_url, $twitter_url, $twitter_handle, $competitors_status, $competitors_logo){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $competitors_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $sql = 'SELECT competitors_id FROM competitors WHERE competitors_id = :competitors_id and (competitors_status != 2)';
                    $s = $db->prepare($sql);
                    $s->bindParam('competitors_id', $competitors_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "Competitor profile not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            if(strlen($competitors_name)>0 || strlen($facebook_url)>0 || strlen($twitter_url)>0 || strlen($twitter_handle)>0 || strlen($competitors_status)>0 || strlen($competitors_logo)>0){
                                $updatestring = "";                            
                                if(strlen($competitors_name)>0){ $updatestring .= " competitors_name = :competitors_name,"; }
                                if(strlen($competitors_status)>0){ 
                                    
                                    if($competitors_status != 0 && $competitors_status != 1){
                                        $stat['status'] = false;
                                        $stat['auth'] = true;
                                        $stat['message'] = "Competitor status '".$competitors_status."' not allowed.";
                                        $stat['data'] = '';
                                        return $stat;
                                    }else{
                                        $updatestring .= " competitors_status = :competitors_status,"; 
                                    }
                                
                                }
                                if(strlen($facebook_url)>0){ $updatestring .= " facebook_url = :facebook_url,"; }
                                if(strlen($twitter_url)>0){ $updatestring .= " twitter_url = :twitter_url,"; }
                                if(strlen($twitter_handle)>0){ $updatestring .= " twitter_handle = :twitter_handle,"; }
                                if(strlen($competitors_logo)>0){ $updatestring .= " competitors_logo = :competitors_logo,"; }

                                $updatestring = substr($updatestring, 0, -1);
                                $updated_at = date("Y-m-d h:i:s");
                                $stmt = $db->prepare("update competitors set ".$updatestring.", updated_at = :updated_at where competitors_id = :competitors_id");
                                $stmt->bindParam("competitors_id", $competitors_id, PDO::PARAM_INT);
                                $stmt->bindParam("updated_at", $updated_at);
                                if(strlen($competitors_name)>0){ $stmt->bindParam("competitors_name", $competitors_name); }
                                if(strlen($competitors_status)>0){ $stmt->bindParam("competitors_status", $competitors_status); }
                                if(strlen($facebook_url)>0){ $stmt->bindParam("facebook_url", $facebook_url); }
                                if(strlen($twitter_url)>0){ $stmt->bindParam("twitter_url", $twitter_url); }
                                if(strlen($twitter_handle)>0){ $stmt->bindParam("twitter_handle", $twitter_handle); }
                                if(strlen($competitors_logo)>0){ $stmt->bindParam("competitors_logo", $competitors_logo); }

                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['auth'] = true;
                                $stat['refresh_token'] = $tokenStatus['refresh_token'];
                                $stat['message'] = "location updated successfully.";
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


    public function deleteCompetitor($token, $competitors_id){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $competitors_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);        
            if($tokenStatus['status'])
            {
                try
                {

                    $sql = 'SELECT competitors_id FROM competitors WHERE competitors_id = :competitors_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('competitors_id', $competitors_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "Competitor profile not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            $deleted_at = date("Y-m-d h:i:s");
                            $stmt = $db->prepare("update competitors set competitors_status = 2, deleted_at = :deleted_at where competitors_id = :competitors_id and business_id = :business_id");
                            $stmt->bindParam("competitors_id", $competitors_id);
                            $stmt->bindParam("deleted_at", $deleted_at);
                            $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                            $stmt->execute();
                            $stat['status'] = true;
                            $stat['auth'] = true;
                            $stat['refresh_token'] = $tokenStatus['refresh_token'];
                            $stat['message'] = "Competitor deleted successfully.";
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


}

  ?>