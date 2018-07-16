
<?php
    header('Access-Control-Allow-Origin: *');
    require_once("Rest.inc.php");
    require 'sendsms.php';
    require 'phpmailer/PHPMailerAutoload.php';
    
    class API extends REST {
    
        public $data = "";
        
        /*const DB_SERVER = "162.215.241.244";
        const DB_USER = "buvviqf1_mmv";
        const DB_PASSWORD = "buvvi1991";
        const DB = "buvviqf1_mmv_db";
        */
        const DB_SERVER = "162.215.241.244";
        const DB_USER = "buvviqf1_test1";
        const DB_PASSWORD = "harini@123";
        const DB = "buvviqf1_test";
       
       eee

        private $db = NULL;
        private $mysqli = NULL;
        public function __construct(){
            parent::__construct();              // Init parent contructor
            $this->dbConnect();                 // Initiate Database connection
        }
        
        /*
         *  Connect to Database
        */
        private function dbConnect(){
            $this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
        }
        
        /*
         * Dynmically call the method based on the query string
         */
        public function processApi(){
            $func =
             strtolower(trim(str_replace("/","",$_REQUEST['x'])));
            if((int)method_exists($this,$func) > 0)
                $this->$func();
            else
                $this->response('file not found',404); // If the method not exist with in this class "Page not found".
        }

        private function mmv_checkappversion(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $mmvdevid=$this->_request['devid'];  
            //$password = md5($password);
            if(!empty($mmvdevid)){
                $query="SELECT mmv_app_version FROM mmv_userdet_master where mmv_deviceid='$mmvdevid'";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>0){
                    $result = array();
                    while($row = $r->fetch_assoc()){
                        $result[] = $row;
                    }
                    $this->response($this->json($result), 200); 
                    exit;
                } else{
                    echo "";
                    exit;
                }
            }
            else{
                echo "failed";
                exit;
            }
        }

        /*****************Disclaimer Page start ***************************************/
        private function mmv_checkagree(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $mmvdevid=$this->_request['devid'];  
            //$password = md5($password);
            if(!empty($mmvdevid)){
                $query="SELECT * FROM mmv_userdet_master where mmv_deviceid='$mmvdevid'";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>0){
                    $result = array();
                    while($row = $r->fetch_assoc()){
                        $result[] = $row;
                    }
                    $this->response($this->json($result), 200); 
                    exit;
                } else{
                    echo "";
                    exit;
                }
            }
            else{
                echo "failed";
                exit;
            }
        }
        
        private function mmv_user_agree(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $devid=$this->_request['devid'];
            $ifagreed=$this->_request['agree'];
            $qval=$this->_request['qval'];
            $ver=$this->_request['apiver'];
            //$pwd = md5($pwd);
            if($qval=="insert"){
                $query = "INSERT INTO `mmv_userdet_master` (`mmv_ud_id`, `mmv_email`, `mmv_password`, `mmv_phone`,`mmv_deviceid`,`mmv_agree`,`mmv_api_version`) VALUES ('', '', '', '','$devid','$ifagreed','$ver')";
            }else{
                $query="update mmv_userdet_master set mmv_agree='$ifagreed',mmv_api_version='$ver' where mmv_deviceid='$devid'";
            }
            
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            /* Sending the Email with Username and password begins*/     
            $success = array('status' => "Success", "msg" => "Inserted Successfully.");
            $this->response($this->json($success),200);
        }
        /******************************************************************************/

/*******************************Login Page Start***************************************************/	
        /*************Admin Login**********************/
        private function admin_log_in(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $log_id = $this->_request['log_id'];  
            $password = $this->_request['password'];
            if(!empty($log_id) and !empty($password)){
                $query="SELECT * FROM admin_user WHERE username = '$log_id' AND password ='$password'";
                //$query="SELECT * FROM `mmv_userdet_master` where email='raoramprasath8@gmail.com' and password='123'";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>0){
                    $result = $r->fetch_assoc();
                    echo "success";
                    exit;
                } else{
                    echo "login failed";
                    exit;
                }
            }
            else{

            }
        }

        private function mmv_tot_userdet_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_ud_id) as mmv_id FROM mmv_userdet_master";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_tot_vv_profile_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_id) as mmv_id FROM mmv_vv_master";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_vv_vadhu_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_id) as mmv_id FROM mmv_vv_master where mmv_gender='male'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_vv_vara_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_id) as mmv_id FROM mmv_vv_master where mmv_gender='female'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_vv_vadhu_paid_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_id) as mmv_id FROM mmv_vv_master where mmv_gender='male' and mmv_vvd_view='1'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_vv_vara_paid_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_id) as mmv_id FROM mmv_vv_master where mmv_gender='female' and mmv_vvd_view='1'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_vv_vadhu_unpaid_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_id) as mmv_id FROM mmv_vv_master where mmv_gender='male' and mmv_vvd_view='0'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_vv_vara_unpaid_count(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            $query="SELECT count(mmv_id) as mmv_id FROM mmv_vv_master where mmv_gender='female' and mmv_vvd_view='0'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 1){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_profile_approve(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $query="SELECT mmv_id,mmv_gender,mmv_name,mmv_dob,mmv_religion,mmv_caste,mmv_subcaste,mmv_rasi,mmv_nakshatra,mmv_gothra FROM mmv_vv_master where mmv_vvd_view='0' order by mmv_createdon desc";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 0){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_profile_disapprove(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $query="SELECT mmv_id,mmv_gender,mmv_name,mmv_dob,mmv_religion,mmv_caste,mmv_subcaste,mmv_rasi,mmv_nakshatra,mmv_gothra FROM mmv_vv_master where mmv_vvd_view='1' order by mmv_createdon desc";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 0){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_adm_profile_list(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $query="SELECT mmv_id,mmv_gender,mmv_name,mmv_dob,mmv_religion,mmv_caste,mmv_subcaste,mmv_rasi,mmv_nakshatra,mmv_gothra FROM mmv_vv_master order by mmv_createdon desc";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows >= 0){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
            }
            $this->response('',204);
        }

        private function mmv_approve_user(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }

            $mmv_approveid=$this->_request['id'];
            $mmv_apptype=$this->_request['type'];
            $mmv_enable_upd=date('Y-m-d');
            
            //$query = "INSERT INTO `vva_registration_master` (`VVA_id`, `Name`, `Gender`, `DOB`, `Place_of_Birth`, `Time_of_birth`, `Father_Name`, `Mother_Name`, `Education`, `Company_Name`, `Occupation`, `Cast`, `Sub_Cast`, `Gothra`, `Rashi`, `Nakshatra`, `Family_Origin`, `Mobile_Number`, `Mobile_Number2`, `Email_id`) VALUES ('', '$$Name', '$Gender', '$DOB', '$Place_of_Birth', '$Time_of_birth', '$Father_Name', '$Mother_Name', '$Education', '$Company_Name', '$Occupation', '$Cast', '$Sub_Cast', '$Gothra', '$Rashi', '$Nakshatra', '$Family_Origin', '$Mobile_Number', '$Mobile_Number2', '$Email_id')";
            if($mmv_apptype=="enable"){
                $query = "update mmv_vv_master set mmv_vvd_view='1',mmv_enable_date='$mmv_enable_upd' where mmv_id='$mmv_approveid'";
            }else{
                $query = "update mmv_vv_master set mmv_vvd_view='0',mmv_enable_date='' where mmv_id='$mmv_approveid'";
            }
            
            //$query = "update mmv_vv_master set mmv_name='$mmv_updname',mmv_gender='$mmv_gender', mmv_dob='$mmv_dob', mmv_pob='$mmv_pob', mmv_tob='$mmv_tob', mmv_religion='$mmv_religion', mmv_caste='$mmv_caste', mmv_subcaste='$mmv_subcaste', mmv_rasi='$mmv_rasi', mmv_nakshatra='$mmv_nakshatra', mmv_pada='$mmv_pada', mmv_gothra='$mmv_gothra', mmv_fname='$mmv_fname', mmv_mname='$mmv_mname',mmv_edu='$mmv_edu', mmv_occupation='$mmv_occupation', mmv_salary='$mmv_salary', mmv_ph1='$mmv_ph1', mmv_ph2='$mmv_ph2', mmv_email='$mmv_email', mmv_address='$mmv_address', mmv_country='$mmv_country', mmv_state='$mmv_state', mmv_city='$mmv_city',mmv_updatedon='$mmv_updatedon' where mmv_devid='$mmv_uuid'"
            //$query = "INSERT INTO `mmv_vv_master` (`mmv_id`,`mmv_name`, `mmv_gender`, `mmv_dob`, `mmv_pob`, `mmv_tob`, `mmv_religion`, `mmv_caste`, `mmv_subcaste`, `mmv_rasi`, `mmv_nakshatra`, `mmv_pada`, `mmv_gothra`, `mmv_fname`, `mmv_mname`, `mmv_edu`, `mmv_occupation`, `mmv_salary`, `mmv_ph1`, `mmv_ph2`, `mmv_email`, `mmv_address`, `mmv_country`, `mmv_state`, `mmv_city`, `mmv_status`, `mmv_createdon`, `mmv_updatedon`, `mmv_vvd_view`) VALUES ('', '$mmv_name', '$mmv_gender', '$mmv_dob', '$mmv_pob', '$mmv_tob', '$mmv_religion', '$mmv_caste', '$mmv_subcaste', '$mmv_rasi', '$mmv_nakshatra', '$mmv_pada', '$mmv_gothra', '$mmv_fname', '$mmv_mname', '$mmv_edu', '$mmv_occupation', '$mmv_salary', '$mmv_ph1', '$mmv_ph2', '$mmv_email', '$mmv_add', '$mmv_country', '$mmv_state', '$mmv_city', '1', '$mmv_createdon', '$mmv_updatedon', '0')";

            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            /* Sending the Email with Username and password begins*/     
                $success = array('status' => "Success", "msg" => "Updated successfully.");
                $this->response($this->json($success),200);
        }

        /*******************Allow to login or not************************/
        private function mmv_allow_log_in(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $log_id = $this->_request['log_id'];  
            $password = $this->_request['password'];
            //$password = md5($password);
            if(!empty($log_id) and !empty($password)){
                $query="SELECT mmv_deviceid FROM mmv_userdet_master WHERE (mmv_phone = '$log_id' or mmv_email = '$log_id') AND mmv_password ='$password'";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>0){
                    $result = array();
                    while($row = $r->fetch_assoc()){
                        $result[] = $row;
                    }
                    $this->response($this->json($result), 200); 
                    exit;
                } else{
                    echo "login failed";
                    exit;
                }
            }
            else{
                echo "failed";
                exit;
            }
        }
        /******************************************************************/
        /*************************Login check******************************/
		private function log_in(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $log_id = $this->_request['log_id'];  
            $password = $this->_request['password'];
            //$devid=$this->_request['devid'];
            //$password = md5($password);
            if(!empty($log_id) and !empty($password)){
                $query="SELECT * FROM mmv_userdet_master WHERE (mmv_phone = '$log_id' or mmv_email = '$log_id') AND mmv_password ='$password'";
                //$query="SELECT * FROM `mmv_userdet_master` where email='raoramprasath8@gmail.com' and password='123'";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>0){
                    $result = $r->fetch_assoc();
                    echo "success";
                    exit;
                } else{
                    echo "login failed";
                    exit;
                }
            }
            else{

            }
        }
        /******************************************************************/
        /******************User Profile already exist or not***************/
        private function mmv_profile_exist(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $email = $this->_request['email'];  
            $ph = $this->_request['phone'];
            //$password = md5($password);
            if(!empty($email) and !empty($ph)){ 
                $query="SELECT * FROM mmv_userdet_master WHERE (mmv_phone = '$ph' or mmv_email = '$email')";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>0){
                    $result = $r->fetch_assoc();
                    echo "exists";
                    exit;
                } else{
                    echo "not available";
                    exit;
                }
            }
            else{
                echo "failed";
                exit;
            }
        }
        /***********************************************************************/
        /********************User Registration**********************************/
        private function mmv_userdet_getMaxrecord(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $query="SELECT max(vvaid) as maxrec from mmv_userdet_master";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

            if($r->num_rows>0){
                $result = array();
                while($row = $r->fetch_assoc()){
                    $result[] = $row;
                }
                $this->response($this->json($result), 200); 
                exit;
            } else{
                echo "Invalid Id";
                exit;
            }
        }
        private function mmv_user_master(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $email=$this->_request['email'];
            $pwd=$this->_request['pwd'];
            $phone=$this->_request['phone'];
            $location=$this->_request['loc'];
            $devid=$this->_request['devid'];
            //$pwd = md5($pwd);
            //$query = "INSERT INTO `mmv_userdet_master` (`mmv_ud_id`, `mmv_email`, `mmv_password`, `mmv_phone`,`mmv_deviceid`) VALUES ('', '$email', '$pwd', '$phone','$devid')";
            $query ="update mmv_userdet_master set mmv_email='$email',mmv_password='$pwd',mmv_phone='$phone',mmv_location='$location' where mmv_deviceid='$devid'";
            $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
            /* Sending the Email with Username and password begins*/     
            $success = array('status' => "Success", "msg" => "Updated Successfully.");
            $this->response($this->json($success),200);
        }
        /************************************************************************/
/************************************Login Page End****************************************************/
/************************************Registration Page Start**********************************************/

/********************Loading data into controls *************************/
private function mmv_get_religion(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT religion from vvm_religion";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
private function mmv_get_caste(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT  caste_name from vvm_caste";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}

private function mmv_get_subcaste(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT subcaste from vvm_subcaste";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
private function mmv_get_rasi(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT rashi from vvm_rashi";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
private function mmv_get_nakshatra(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT nakshatra from vvm_nakshatra";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
private function mmv_get_padha(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT pada from vvm_pada";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
private function mmv_get_gothra(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT gothra_name from vvm_gothra";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
private function mmv_get_country(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT country from vvm_country";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
private function mmv_get_state(){
    if($this->get_request_method() != "GET"){
        $this->response('',406);
    }
    $query="SELECT state from vvm_state";
    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

    if($r->num_rows>0){
        $result = array();
        while($row = $r->fetch_assoc()){
            $result[] = $row;
        }
        $this->response($this->json($result), 200); 
        exit;
    } else{
        echo "Invalid Id";
        exit;
    }
}
/*********************************************************************** */
        /***************************Vadu vara Profile already exist or not***************************************/
        private function mmv_profile_vvd_exist(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $email = $this->_request['email'];  
            $ph1 = $this->_request['ph'];
            //$password = md5($password);
            if(!empty($email) and !empty($ph1)){ 
                $query="SELECT * FROM mmv_vv_master WHERE (mmv_ph1 = '$ph1' or mmv_email = '$email')";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>=1){
                    $result = $r->fetch_assoc();
                    echo "exists";
                    exit;
                } else{
                    echo "not available";
                    exit;
                }
            }
            else{
                echo "failed";
                exit;
            }
        }
        /**********************************************************************************************/

        /***************************view enable or disable***************************************/
        private function mmv_profile_view_status(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $devid = $this->_request['devid'];  
            //$password = md5($password);
            if(!empty($devid)){ 
                $query="SELECT mmv_gender,mmv_vvd_view FROM mmv_vv_master WHERE mmv_devid = '$devid'";
                $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                if($r->num_rows>0){
                    $result = array();
                    while($row = $r->fetch_assoc()){
                        $result[] = $row;
                    }
                    $this->response($this->json($result), 200); 
                    exit;
                } else{
                    echo "not available";
                    exit;
                }
            }
            else{
                echo "failed";
                exit;
            }
        }
        /**********************************************************************************************/
        /*******************************Profile View***************************************************/
                private function mmv_profile_view(){
                    if($this->get_request_method() != "GET"){
                        $this->response('',406);
                    }
                    $gender = $this->_request['gender'];  
                    $query="SELECT mmv_id,mmv_name,mmv_dob,mmv_religion,mmv_caste,mmv_subcaste,mmv_rasi,mmv_nakshatra,mmv_gothra FROM mmv_vv_master where mmv_gender='$gender' order by mmv_createdon desc";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

                    if($r->num_rows >= 0){
                        $result = array();
                        while($row = $r->fetch_assoc()){
                            $result[] = $row;
                        }
                        $this->response($this->json($result), 200); 
                    }
                    $this->response('',204);
                }	

                private function mmv_profile_fullview(){
                    if($this->get_request_method() != "GET"){
                        $this->response('',406);
                    }
                    $id = $this->_request['mmvid'];  
                    $query="SELECT * FROM mmv_vv_master where mmv_id='$id'";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

                    if($r->num_rows >= 0){
                        $result = array();
                        while($row = $r->fetch_assoc()){
                            $result[] = $row;
                        }
                        $this->response($this->json($result), 200); 
                    }
                    $this->response('',204);
                }
                
                private function mmv_self_profile_fullview(){
                    if($this->get_request_method() != "GET"){
                        $this->response('',406);
                    }
                    $deviceid = $this->_request['devid'];  
        
                    $query="SELECT * FROM mmv_vv_master where mmv_devid='$deviceid'";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

                    if($r->num_rows >= 0){
                        $result = array();
                        while($row = $r->fetch_assoc()){
                            $result[] = $row;
                        }
                        $this->response($this->json($result), 200); 
                    }
                    $this->response('',204);
                }

                private function get_mmvid_lastinserted(){
                    if($this->get_request_method() != "GET"){
                        $this->response('',406);
                    }
                    
                    $query="SELECT max(mmv_id) from mmv_vv_master";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

                    if($r->num_rows>0){
                        $result = $r->fetch_assoc();
                        echo $result;
                        exit;
                    } else{
                        echo "Invalid Id";
                        exit;
                    }
                    //$this->response('',204);
                }
                
        /*******************************Profile Post***************************************************/
                
                private function profilepost(){
                    if($this->get_request_method() != "POST"){
                        $this->response('',406);
                    }

                    $mmv_name=$this->_request['name'];
                    $mmv_gender=$this->_request['gender'];
                    $mmv_dob=$this->_request['dob'];
                    $mmv_pob=$this->_request['pob'];
                    $mmv_tob=$this->_request['tob'];
                    $mmv_religion=$this->_request["religion"];
                    $mmv_caste=$this->_request['caste'];
                    $mmv_subcaste=$this->_request['subcaste'];
                    $mmv_rasi=$this->_request['rasi'];
                    $mmv_nakshatra=$this->_request['nakshatra'];
                    $mmv_pada=$this->_request['pada'];
                    $mmv_gothra=$this->_request['gothra'];
                    $mmv_fname=$this->_request['fname'];
                    $mmv_mname=$this->_request['mname'];
                    $mmv_edu=$this->_request['edu'];
                    $mmv_occupation=$this->_request['occupation'];
                    $mmv_salary=$this->_request["salary"];
                    $mmv_ph1=$this->_request['ph1'];
                    $mmv_ph2=$this->_request['ph2'];
                    $mmv_email=$this->_request['email'];
                    $mmv_add=$this->_request['add'];
                    $mmv_country=$this->_request['ctry'];
                    $mmv_state=$this->_request['state'];
                    $mmv_city=$this->_request['city'];
                    $mmv_comments=$this->_request['othcmnts'];
                    $mmv_finfo=$this->_request['finfo'];
                    $mmv_height=$this->_request['height'];
                    $mmv_updatedon=date('Y-m-d H:i:s');
                    $mmv_createdon = date('Y-m-d H:i:s');
                    $mmv_devid=$this->_request['devid'];
                
                    //$query = "INSERT INTO `vva_registration_master` (`VVA_id`, `Name`, `Gender`, `DOB`, `Place_of_Birth`, `Time_of_birth`, `Father_Name`, `Mother_Name`, `Education`, `Company_Name`, `Occupation`, `Cast`, `Sub_Cast`, `Gothra`, `Rashi`, `Nakshatra`, `Family_Origin`, `Mobile_Number`, `Mobile_Number2`, `Email_id`) VALUES ('', '$$Name', '$Gender', '$DOB', '$Place_of_Birth', '$Time_of_birth', '$Father_Name', '$Mother_Name', '$Education', '$Company_Name', '$Occupation', '$Cast', '$Sub_Cast', '$Gothra', '$Rashi', '$Nakshatra', '$Family_Origin', '$Mobile_Number', '$Mobile_Number2', '$Email_id')";
                    $query = "INSERT INTO `mmv_vv_master` (`mmv_id`,`mmv_name`, `mmv_gender`, `mmv_dob`, `mmv_pob`, `mmv_tob`, `mmv_religion`, `mmv_caste`, `mmv_subcaste`, `mmv_rasi`, `mmv_nakshatra`, `mmv_pada`, `mmv_gothra`, `mmv_fname`, `mmv_mname`, `mmv_edu`, `mmv_occupation`, `mmv_salary`, `mmv_ph1`, `mmv_ph2`, `mmv_email`, `mmv_address`, `mmv_country`, `mmv_state`, `mmv_city`, `mmv_status`, `mmv_createdon`, `mmv_updatedon`, `mmv_vvd_view`, `mmv_devid`,`mmv_other_comments`,`mmv_family_info`,`mmv_height`) VALUES ('', '$mmv_name', '$mmv_gender', '$mmv_dob', '$mmv_pob', '$mmv_tob', '$mmv_religion', '$mmv_caste', '$mmv_subcaste', '$mmv_rasi', '$mmv_nakshatra', '$mmv_pada', '$mmv_gothra', '$mmv_fname', '$mmv_mname', '$mmv_edu', '$mmv_occupation', '$mmv_salary', '$mmv_ph1', '$mmv_ph2', '$mmv_email', '$mmv_add', '$mmv_country', '$mmv_state', '$mmv_city', '1', '$mmv_createdon', '$mmv_updatedon', '0','$mmv_devid','$mmv_comments','$mmv_finfo','$mmv_height')";

                    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                    /* Sending the Email with Username and password begins*/     
                        $success = array('status' => "Success", "msg" => "Insert successfully.");
                        $this->response($this->json($success),200);
                }

                private function mmv_vv_profile_count(){
                    if($this->get_request_method() != "GET"){
                        $this->response('',406);
                    }
                    $deviceid = $this->_request['devid'];  
        
                    $query="SELECT mmv_devid FROM mmv_vv_master where mmv_devid='$deviceid'";
                    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

                    if($r->num_rows >= 1){
                        echo "available";
                        exit;
                    }else{
                        echo "not available";
                        exit;
                    }
                    $this->response('',204);
                }

                private function mmv_updprofile(){
                    if($this->get_request_method() != "POST"){
                        $this->response('',406);
                    }

                    $mmv_updname=$this->_request['name'];
                    $mmv_updgender=$this->_request['gender'];
                    $mmv_upddob=$this->_request['dob'];
                    $mmv_updpob=$this->_request['pob'];
                    $mmv_updtob=$this->_request['tob'];
                    $mmv_updreligion=$this->_request["religion"];
                    $mmv_updcaste=$this->_request['caste'];
                    $mmv_updsubcaste=$this->_request['subcaste'];
                    $mmv_updrasi=$this->_request['rasi'];
                    $mmv_updnakshatra=$this->_request['nakshatra'];
                    $mmv_updpada=$this->_request['pada'];
                    $mmv_updgothra=$this->_request['gothra'];
                    $mmv_updfname=$this->_request['fname'];
                    $mmv_updmname=$this->_request['mname'];
                    $mmv_updedu=$this->_request['edu'];
                    $mmv_updoccupation=$this->_request['occupation'];
                    $mmv_updsalary=$this->_request["salary"];
                    $mmv_updph1=$this->_request['ph1'];
                    $mmv_updph2=$this->_request['ph2'];
                    $mmv_updemail=$this->_request['email'];
                    $mmv_updadd=$this->_request['add'];
                    $mmv_updcountry=$this->_request['ctry'];
                    $mmv_updstate=$this->_request['state'];
                    $mmv_updcity=$this->_request['city'];
                    $mmv_updupdatedon=date('Y-m-d H:i:s');
                    $mmv_updcomments=$this->_request['othcmnts'];
                    $mmv_updfinfo=$this->_request['finfo'];
                    $mmv_updheight=$this->_request['height'];
                    $mmv_upduuid=$this->_request['devid'];
                
                    //$query = "INSERT INTO `vva_registration_master` (`VVA_id`, `Name`, `Gender`, `DOB`, `Place_of_Birth`, `Time_of_birth`, `Father_Name`, `Mother_Name`, `Education`, `Company_Name`, `Occupation`, `Cast`, `Sub_Cast`, `Gothra`, `Rashi`, `Nakshatra`, `Family_Origin`, `Mobile_Number`, `Mobile_Number2`, `Email_id`) VALUES ('', '$$Name', '$Gender', '$DOB', '$Place_of_Birth', '$Time_of_birth', '$Father_Name', '$Mother_Name', '$Education', '$Company_Name', '$Occupation', '$Cast', '$Sub_Cast', '$Gothra', '$Rashi', '$Nakshatra', '$Family_Origin', '$Mobile_Number', '$Mobile_Number2', '$Email_id')";
                    $query = "update mmv_vv_master set mmv_name='$mmv_updname',mmv_gender='$mmv_updgender', mmv_dob='$mmv_upddob', mmv_pob='$mmv_updpob', mmv_tob='$mmv_updtob', mmv_religion='$mmv_updreligion', mmv_caste='$mmv_updcaste', mmv_subcaste='$mmv_updsubcaste', mmv_rasi='$mmv_updrasi', mmv_nakshatra='$mmv_updnakshatra', mmv_pada='$mmv_updpada', mmv_gothra='$mmv_updgothra', mmv_fname='$mmv_updfname', mmv_mname='$mmv_updmname',mmv_edu='$mmv_updedu', mmv_occupation='$mmv_updoccupation', mmv_salary='$mmv_updsalary', mmv_ph1='$mmv_updph1', mmv_ph2='$mmv_updph2', mmv_email='$mmv_updemail', mmv_address='$mmv_updadd', mmv_country='$mmv_updcountry', mmv_state='$mmv_updstate', mmv_city='$mmv_updcity', mmv_updatedon='$mmv_updupdatedon', mmv_other_comments='$mmv_updcomments', mmv_family_info='$mmv_updfinfo', mmv_height='$mmv_updheight' where mmv_devid='$mmv_upduuid'";
                    //$query = "update mmv_vv_master set mmv_name='$mmv_updname',mmv_gender='$mmv_gender', mmv_dob='$mmv_dob', mmv_pob='$mmv_pob', mmv_tob='$mmv_tob', mmv_religion='$mmv_religion', mmv_caste='$mmv_caste', mmv_subcaste='$mmv_subcaste', mmv_rasi='$mmv_rasi', mmv_nakshatra='$mmv_nakshatra', mmv_pada='$mmv_pada', mmv_gothra='$mmv_gothra', mmv_fname='$mmv_fname', mmv_mname='$mmv_mname',mmv_edu='$mmv_edu', mmv_occupation='$mmv_occupation', mmv_salary='$mmv_salary', mmv_ph1='$mmv_ph1', mmv_ph2='$mmv_ph2', mmv_email='$mmv_email', mmv_address='$mmv_address', mmv_country='$mmv_country', mmv_state='$mmv_state', mmv_city='$mmv_city',mmv_updatedon='$mmv_updatedon' where mmv_devid='$mmv_uuid'"
                    //$query = "INSERT INTO `mmv_vv_master` (`mmv_id`,`mmv_name`, `mmv_gender`, `mmv_dob`, `mmv_pob`, `mmv_tob`, `mmv_religion`, `mmv_caste`, `mmv_subcaste`, `mmv_rasi`, `mmv_nakshatra`, `mmv_pada`, `mmv_gothra`, `mmv_fname`, `mmv_mname`, `mmv_edu`, `mmv_occupation`, `mmv_salary`, `mmv_ph1`, `mmv_ph2`, `mmv_email`, `mmv_address`, `mmv_country`, `mmv_state`, `mmv_city`, `mmv_status`, `mmv_createdon`, `mmv_updatedon`, `mmv_vvd_view`) VALUES ('', '$mmv_name', '$mmv_gender', '$mmv_dob', '$mmv_pob', '$mmv_tob', '$mmv_religion', '$mmv_caste', '$mmv_subcaste', '$mmv_rasi', '$mmv_nakshatra', '$mmv_pada', '$mmv_gothra', '$mmv_fname', '$mmv_mname', '$mmv_edu', '$mmv_occupation', '$mmv_salary', '$mmv_ph1', '$mmv_ph2', '$mmv_email', '$mmv_add', '$mmv_country', '$mmv_state', '$mmv_city', '1', '$mmv_createdon', '$mmv_updatedon', '0')";

                    $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
                    /* Sending the Email with Username and password begins*/     
                        //$success = array('status' => "Success", "msg" => "Updated successfully.");
                        //$this->response($this->json($success),200);
                }
/******************************************************************************************************* */

        private function json($data){
            if(is_array($data)){
                return json_encode($data);
            }
        }
    }
    
    // Initiiate Library
    
    $api = new API;
    $api->processApi();
?>