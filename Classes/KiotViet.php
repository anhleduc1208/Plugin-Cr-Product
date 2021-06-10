<?php 

    if(!class_exists('KiotViet')){
        class KiotViet {
            protected $client_id;
            protected $client_secret;
            protected $retailer;
            protected $token;
            public function __construct($kiotInfo){
                $this->client_id = $kiotInfo["id"];
                $this->client_secret = $kiotInfo['secret'];
                $this->retailer = $kiotInfo['retailer'];
                $this->token = $this->get_token($this->client_id, $this->client_secret);
            }
            public function get_token($p_id, $p_secret){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"https://id.kiotviet.vn/connect/token");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "scopes=PublicApi.Access&grant_type=client_credentials&client_id=".$p_id."&client_secret=".$p_secret);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec ($ch);
                curl_close ($ch);
                return json_decode($server_output)->access_token;
            }
            public function get_data($p_url, $p_params){
                $query = "";
                foreach($p_params as $key=>$value){
                    $query .= $key."=".$value."&";
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $p_url."?".$query);
                // curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Retailer:".$this->retailer,
                    "Authorization:"."Bearer ".$this->token
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec ($ch);
                curl_close ($ch);
                $server_output1 = json_decode($server_output,true);
                return $server_output1;
            }
        }    
    }
    if(!class_exists('KiotProduct')){
        class KiotProduct extends KiotViet {
            private $url = "https://public.kiotapi.com/products";
            public $data;

            function get_all_kiot_products($p_params){
                $this->data = $this->get_data($this->url,$p_params);
                return $this->data;
            }   
            function get_kiot_product_by_code($p_code){
                $this->data = $this->get_data($this->url."//code/".$p_code,array());
                return $this->data;
            }   
            function get_kiot_products_recent_modified($seconds){
                date_default_timezone_set("Asia/Ho_Chi_Minh");
                $real_time = idate("U");
                $the_time = $real_time-$seconds;
                $date = date('c',$the_time);
                //$date1 = json_encode($date,true);
                $p_params = array(
                    'pageSize' => '100',
                    'lastModifiedFrom' => $date
                );
                //$this->data = $p_params;
                $this->data = $this->get_data($this->url,$p_params);
                return $this->data;
            }
        }
    }
    
?>