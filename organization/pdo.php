<?php
class pos extends dbconn {
	public function __construct()
	{
        $this->initDBO();        
    }
    
    public function createOrganization($token, $organization_name){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($token, $organization_name)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
        if($tokenStatus['status'])
        {
                try
                {
                    $sql = 'SELECT organization_id FROM organization WHERE organization_name = :organization_name and business_id = :business_id';
                    $s = $db->prepare($sql);
                    $s->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $s->bindParam('organization_name', $organization_name);
                    if ($s->execute())
                    {
                        if($s->rowCount() > 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "Organization already exists.";
                            $stat['data'] = '';
                            return $stat;
                        }else{

                                $stmt = $db->prepare("insert into organization (organization_name, organization_status, business_id) values(:organization_name, 1, :business_id)");
                                $stmt->bindParam("organization_name", $organization_name);
                                $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['refresh_token'] = '';
                                $stat['auth'] = true;
                                $stat['message'] = "Organization created successfully.";
                                $stat['data'] = array("organization_id"=>$db->lastInsertId());                            
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

    public function deleteOrganization($token, $organization_id){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($token, $organization_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);        
            if($tokenStatus['status'])
            {
                try
                {

                    $sql = 'SELECT organization_id FROM organization WHERE organization_id = :organization_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('organization_id', $organization_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "Organization not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            $deleted_at = date("Y-m-d h:i:s");
                            $stmt = $db->prepare("update organization set organization_status = 2, organization_deleted_at = :deleted_at where organization_id = :organization_id and business_id = :business_id");
                            $stmt->bindParam("organization_id", $organization_id);
                            $stmt->bindParam("deleted_at", $deleted_at);
                            $stmt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                            $stmt->execute();                             
                            $stat['status'] = true;
                            $stat['auth'] = true;
                            $stat['refresh_token'] = $tokenStatus['refresh_token'];
                            $stat['message'] = "Organization deleted successfully.";
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

    public function getOrganizationList($token, $from, $no_of_records){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($token, $from, $no_of_records)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;        
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {                
                try
                {
                    $cnt = $db->prepare("select COUNT(*) from organization where (organization_status = 0 OR organization_status = 1) and business_id = :business_id");
                    $cnt->bindParam("business_id", $tokenStatus['business_id'], PDO::PARAM_INT);
                    $cnt->execute();
                    $numberofrows = $cnt->fetchColumn();

                    $stmt = $db->prepare("select * from organization where (organization_status = 0 OR organization_status = 1) and business_id = :business_id order By organization_id Desc LIMIT ".$from.", ".$no_of_records);
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

    public function updateOrganization($token, $organization_id, $organization_name, $organization_status){
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*////*/$mandatory = $this->validateMandatory(array($token, $organization_id)); if(!$mandatory){ $stat['status'] = false; $stat['auth'] = true; $stat['message'] = "mandatory fields missing."; $stat['data'] = ''; return $stat;}/*////*////////
            ///////checking mandatory fields/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $db = $this->dblocal;
        $tokenStatus = $this->validateToken($token);
            if($tokenStatus['status'])
            {
                try
                {
                    $sql = 'SELECT organization_id FROM organization WHERE organization_id = :organization_id';
                    $s = $db->prepare($sql);
                    $s->bindParam('organization_id', $organization_id);
                    if ($s->execute())
                    {
                        if($s->rowCount() == 0)
                        {
                            $stat['status'] = false;
                            $stat['auth'] = true;
                            $stat['message'] = "Organization not found.";
                            $stat['data'] = '';
                            return $stat;
                        }else{
                            if(strlen($organization_name)>0 || strlen($organization_status)>0){
                                $updatestring = "";                            
                                if(strlen($organization_name)>0){ $updatestring .= " organization_name = :organization_name,"; }
                                if(strlen($organization_status)>0){ 
                                    
                                    if($organization_status != 0 && $organization_status != 1){
                                        $stat['status'] = false;
                                        $stat['auth'] = true;
                                        $stat['message'] = "Organization status '".$organization_status."' not allowed.";
                                        $stat['data'] = '';
                                        return $stat;
                                    }else{
                                        $updatestring .= " organization_status = :organization_status,"; 
                                    }
                                
                                }
                                $updatestring = substr($updatestring, 0, -1);
                                $organization_updated_at = date("Y-m-d h:i:s");
                                $stmt = $db->prepare("update organization set ".$updatestring.", organization_updated_at = :organization_updated_at where organization_id = :organization_id");
                                $stmt->bindParam("organization_id", $organization_id, PDO::PARAM_INT);
                                $stmt->bindParam("organization_updated_at", $organization_updated_at);
                                if(strlen($organization_name)>0){ $stmt->bindParam("organization_name", $organization_name); }
                                if(strlen($organization_status)>0){ $stmt->bindParam("organization_status", $organization_status); }
                                $stmt->execute();
                                $stat['status'] = true;
                                $stat['auth'] = true;
                                $stat['refresh_token'] = $tokenStatus['refresh_token'];
                                $stat['message'] = "Organization updated successfully.";
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