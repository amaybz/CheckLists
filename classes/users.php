<?
    class users
    {

        public $MemberAuthToken  = 0;

        public $isLoggedIn  = 0;

        public $MemberID  = 0;

        public $fullName  = "";

        Public $Permission = "none";


        public function __construct()
        {
            if($this->ValidTokenCookie()){
                $this->LoginViaToken();
            }
            

        }

        public function login($MemberID, $password)
        {
            //send member ID and password to API and get token and member details back
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ajcomputers.com.au/dptses/check_list/api/login/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>'{
             "username" : "' . $MemberID . '",
             "password" : "' . $password . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;
            $data = json_decode($response, true);
            
            if($data['message'] == "Successful login.")
            {
                $this->MemberAuthToken = $data['jwt'];
                //echo "Token: " . $this->MemberAuthToken;
                $this->CreateCookie();
                $this->isLoggedIn = 1;
            }
            return $data;

        }

        public function LoginViaToken()
        {
            //echo $this->MemberAuthToken;
            //send auth token and get current logged in member details back
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://ajcomputers.com.au/dptses/check_list/api/login/validate/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                  'Authorization: ' . $this->MemberAuthToken
                ),
              ));


            $response = curl_exec($curl);

            curl_close($curl);


            //echo $response;
            //echo "<br>t: " . $this->MemberAuthToken;
            $data = json_decode($response, true);
            $this->MemberID = $data['data']['username'];
            $this->fullName = $data['data']['name'];
		    //$Membermobile = $data['data']['loggedInMember']['mobile'];
		    //$Memberteam = $data['data']['loggedInMember']['units']['team'];
            $this->Permission = $data['data']['access'];
            //$MemberRank = $data['data']['loggedInMember']['rank'];
            //echo $this->Permission;
            if($this->MemberID != 0)
            {
               // echo "num: " . $this->MemberID;
                $this->isLoggedIn = 1;
            }
            else
            {
                //echo "num: " . $this->MemberID;
                $this->isLoggedIn = 0;
            }
            
            
            return $this->isLoggedIn;
        }


 
      
    
        private function CreateCookie() {
            $this->clearAuthCookie();
            $token = $this->MemberAuthToken;
            $cookie_expiration_time = time()+60*60*24*90;
            $expiry_date = date("Y-m-d H:i:s", $cookie_expiration_time);
            $cookie = $token;
            $mac = hash_hmac('sha256', $cookie, "DPTSESL");
            $cookie .= ':' . $mac;
            setcookie('tokenuser', $cookie, $cookie_expiration_time);
        }
    
        public function clearAuthCookie() {
            if (isset($_COOKIE["tokenuser"])) {
                setcookie("tokenuser", "",time()-3600);
            }
        }

        public function Logout() {
            $this->clearAuthCookie();
            $this->isLoggedIn = 0;
            $this->MemberAuthToken = "";
            $this->fullName = "";

        }

        public function ValidTokenCookie() {
            $cookie = isset($_COOKIE['tokenuser']) ? $_COOKIE['tokenuser'] : '';
            if ($cookie) {
                list ($token, $mac) = explode(':', $cookie);
                //echo $token;
                if (hash_equals(hash_hmac('sha256', $token, "DPTSESL"), $mac)) {
                    //echo "login in via cookie";
                    $this->MemberAuthToken = $token;
                    //echo "<br>Cookie Token: " . $this->MemberAuthToken . '<br>';
                    return true;
                }
                else{
                    return false;
                }

            }
        }
}

?>