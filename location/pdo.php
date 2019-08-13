<?php
class pos extends dbconn {
	public function __construct()
	{
        $this->initDBO();
    }

    public function createlocation($token, $location_name){
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      /*////*/$mandatory = $this->validateMandatory(array($token, $location_name)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
      ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
        if($tokenStatus['status'])
        {
                try
                {
                    $sql = 'SELECT location_id FROM location WHERE location_name = :location_name and business_id = :business_id';
                    $s = $db->prepare($sql);
                    $s->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $s->bindParam('location_name', $location_name);
                    if ($s->execute())
                    {
                        if($s->rowCount() > 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "location already exists.";
                            $stat['data'] = '';
                            return $stat;
                        }else{

                                //$otp_expiry = date('Y-m-d H:i:s', strtotime("+30 min"));
                                $stmt = $db->prepare("insert into location (location_name, location_status, business_id) values(:location_name, 1, :business_id)");
                                $stmt->bindParam("location_name", $location_name);
                                $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['refresh_token'] = '';
                                $stat['auth'] = true;
                                $stat['message'] = "location created successfully.";
                                $stat['data'] = array("location_id"=>$db->lastInsertId());
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

    public function deletelocation($token, $location_id){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $location_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);        
            if($tokenStatus['status'])
            {
                try
                {

                    $sql = 'SELECT location_id FROM location WHERE location_id = :location_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('location_id', $location_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "location not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            $deleted_at = date("Y-m-d h:i:s");
                            $stmt = $db->prepare("update location set location_status = 2, location_deleted_at = :deleted_at where location_id = :location_id and business_id = :business_id");
                            $stmt->bindParam("location_id", $location_id);
                            $stmt->bindParam("deleted_at", $deleted_at);
                            $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                            $stmt->execute();
                            $stat['status'] = true;
                            $stat['auth'] = true;
                            $stat['refresh_token'] = $tokenStatus['refresh_token'];
                            $stat['message'] = "location deleted successfully.";
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

    public function getlocationList($token, $from, $no_of_records){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $from, $no_of_records)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;        
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {                
                try
                {
                    $cnt = $db->prepare("select COUNT(*) from location where (location_status = 0 OR location_status = 1) and business_id = :business_id");
                    $cnt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $cnt->execute();
                    $numberofrows = $cnt->fetchColumn();

                    $stmt = $db->prepare("select * from location where (location_status = 0 OR location_status = 1) and business_id = :business_id order By location_id Desc LIMIT ".$from.", ".$no_of_records);
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

    public function updatelocation($token, $location_id, $location_name, $location_status){
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /*////*/$mandatory = $this->validateMandatory(array($token, $location_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
        ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $sql = 'SELECT location_id FROM location WHERE location_id = :location_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('location_id', $location_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "location not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            if(strlen($location_name)>0 || strlen($location_status)>0){
                                $updatestring = "";                            
                                if(strlen($location_name)>0){ $updatestring .= " location_name = :location_name,"; }
                                if(strlen($location_status)>0){ 
                                    
                                    if($location_status != 0 && $location_status != 1){
                                        $stat['status'] = false;
                                        $stat['auth'] = true;
                                        $stat['message'] = "location status '".$location_status."' not allowed.";
                                        $stat['data'] = '';
                                        return $stat;
                                    }else{
                                        $updatestring .= " location_status = :location_status,"; 
                                    }
                                
                                }
                                $updatestring = substr($updatestring, 0, -1);
                                $location_updated_at = date("Y-m-d h:i:s");
                                $stmt = $db->prepare("update location set ".$updatestring.", location_updated_at = :location_updated_at where location_id = :location_id");
                                $stmt->bindParam("location_id", $location_id, PDO::PARAM_INT);
                                $stmt->bindParam("location_updated_at", $location_updated_at);
                                if(strlen($location_name)>0){ $stmt->bindParam("location_name", $location_name); }
                                if(strlen($location_status)>0){ $stmt->bindParam("location_status", $location_status); }
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


}

  ?>