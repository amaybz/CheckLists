<?
    class membersdb
    {

        public $MemberAuthToken  = 0;

        public $isLoggedIn  = 0;

        public $MemberID  = 0;

        public $fullName  = "";

        Public $Permission = "none";


        public function __construct()
        {
            if($this->ValidTokenCookie()){
                $this->getLoggedInMember();
            }
            

        }

        public function login($MemberID, $password)
        {
            //send member ID and password to API and get token and member details back
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://wol-api-ajshort.vercel.app/graphql",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            //CURLOPT_POSTFIELDS =>'{"query":"mutation {\\r\\n  login(memberNumber: ' . $MemberID . ', password: \\"' . $password . '\\") {\\r\\n    token\\r\\n    member {\\r\\n      number\\r\\n  fullName\\r\\n      permission\\r\\n      team\\r\\n      unit\\r\\n    }\\r\\n  }\\r\\n}","variables":{}}',
            CURLOPT_POSTFIELDS =>'{"query":"mutation($memberNumber: Int!, $password: String! ) {\\r\\n  login(memberNumber: $memberNumber, password: $password) {\\r\\n    token\\r\\n    member {\\r\\n      number\\r\\n fullName\\r\\n      units {\\r\\n          code\\r\\n          name\\r\\n          permission\\r\\n          team\\r\\n      }\\r\\n    }\\r\\n  }\\r\\n}","variables":{"memberNumber":' . $MemberID . ',"password":"' . $password . '"}}',

            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;
            $data = json_decode($response, true);
            
            $this->MemberAuthToken = $data['data']['login']['token'];
            //echo $this->MemberAuthToken;
            $this->CreateCookie();
            $this->isLoggedIn = 1;
            return $data;
        }

        public function LoginViaToken()
        {
            //send auth token and get current logged in member details back
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://wol-api-ajshort.vercel.app/graphql',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"query":"{\\r\\n  loggedInMember{\\r\\n    number\\r\\n    fullName\\r\\n    mobile\\r\\n    callsign\\r\\n    rank\\r\\n    units {\\r\\n        code\\r\\n        team\\r\\n        permission\\r\\n    }\\r\\n  }\\r\\n}","variables":{}}',
                CURLOPT_HTTPHEADER => array(
                  'Authorization: ' . $this->MemberAuthToken,
                  'Content-Type: application/json'
                ),
              ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;
            //echo "t: " . $this->MemberAuthToken;
            $data = json_decode($response, true);
            $this->MemberID = $data['data']['loggedInMember']['number'];
            $this->fullName = $data['data']['loggedInMember']['fullName'];
		    $Membermobile = $data['data']['loggedInMember']['mobile'];
		    $Memberteam = $data['data']['loggedInMember']['units']['team'];
            $this->Permission = $data['data']['loggedInMember']['units'][0]['permission'];
            $MemberRank = $data['data']['loggedInMember']['rank'];
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


        public function getLoggedInMember()
        {
            //send auth token and get current logged in member details back
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://wol-api-ajshort.vercel.app/graphql',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"query":"{\\r\\n  loggedInMember{\\r\\n    number\\r\\n    fullName\\r\\n    mobile\\r\\n    callsign\\r\\n    rank\\r\\n    units {\\r\\n        code\\r\\n        team\\r\\n        permission\\r\\n    }\\r\\n  }\\r\\n}","variables":{}}',
                CURLOPT_HTTPHEADER => array(
                  'Authorization: Bearer ' . $this->MemberAuthToken,
                  'Content-Type: application/json'
                ),
              ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;
            //echo "t: " . $this->MemberAuthToken;
            $data = json_decode($response, true);
            $this->MemberID = $data['data']['loggedInMember']['number'];
            $this->fullName = $data['data']['loggedInMember']['fullName'];
		    $Membermobile = $data['data']['loggedInMember']['mobile'];
		    $Memberteam = $data['data']['loggedInMember']['units']['team'];
            $this->Permission = $data['data']['loggedInMember']['units'][0]['permission'];
            $MemberRank = $data['data']['loggedInMember']['rank'];
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
      
    
        Private function CreateCookie() {
            $this->clearAuthCookie();
            $token = $this->MemberAuthToken;
            $cookie_expiration_time = time()+60*60*24*90;
            $expiry_date = date("Y-m-d H:i:s", $cookie_expiration_time);
            $cookie = $token;
            $mac = hash_hmac('sha256', $cookie, "DPTSES");
            $cookie .= ':' . $mac;
            setcookie('token', $cookie, $cookie_expiration_time);
        }
    
        public function clearAuthCookie() {
            if (isset($_COOKIE["token"])) {
                setcookie("token", "",time()-3600);
            }
        }

        public function Logout() {
            $this->clearAuthCookie;
            $this->isLoggedIn = 0;
            $this->MemberAuthToken = "";
            $this->fullName = "";

        }

        Public function ValidTokenCookie() {
            $cookie = isset($_COOKIE['token']) ? $_COOKIE['token'] : '';
            if ($cookie) {
                list ($token, $mac) = explode(':', $cookie);
                //echo $token;
                if (hash_equals(hash_hmac('sha256', $token, "DPTSES"), $mac)) {
                    //echo "login in via cookie";
                    $this->MemberAuthToken = $token;
                    //echo "<br>test: " . $this->MemberAuthToken;
                    return true;
                }
                else{
                    return false;
                }

            }
        }
}

?>