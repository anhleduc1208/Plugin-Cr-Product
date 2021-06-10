<?php
    /*-------add library------- */
    include __DIR__.'../../Classes/KiotViet.php';

    /*-----Declaration----------*/
    $kiot_client_id = 'bae3bcbe-c860-4bac-9e4a-0651dcf4bad0';
    $kiot_client_secret = '0D92F5E0DF1973CC5385348F42C665D8775E7468';
    $kiot_client_retailer = 'cretasolu';
    $kiotInfo = array(
        'id' => $kiot_client_id,
        'secret' => $kiot_client_secret,
        'retailer' => $kiot_client_retailer
    );
    $kiotObj = new KiotProduct($kiotInfo);

    /*------Đăng kí api cho kiot của trang web---- */
    add_action('rest_api_init','creta_kiot_register_route');
    
    function creta_kiot_register_route() {
        register_rest_route( 'cr/v1', 'cr-kiot', array(
            'methods' => 'GET',
            'callback' => 'get_all_kiot_products',
            // 'permission_callback' => function() {
            //     return current_user_can( 'edit_posts' );
            // },
            ) 
        );

        register_rest_route( 'cr/v1', 'cr-kiot/code=(?P<code>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => 'get_kiot_product_by_code',            
            ) 
        );
        register_rest_route( 'cr/v1', 'cr-kiot/seconds=(?P<seconds>[0-9-]+)', array(
            'methods' => 'GET',
            'callback' => 'get_kiot_products_recent_modified',           
            ) 
        );
    }

    /*------Các function call-back xử lý khi có request gửi đến các endpoint----- */

    function get_all_kiot_products($raw_data) {
        global $kiotObj;
        $get_data = $raw_data->get_params();     
        $result = $kiotObj->get_all_kiot_products($get_data);
        return rest_ensure_response( $result);
    }
    function get_kiot_product_by_code($raw_data) {
        global $kiotObj;
        $params = $raw_data->get_url_params();        
        $code = $params['code'];      
        $result = $kiotObj->get_kiot_product_by_code($code);        
        return rest_ensure_response( $result);
    }
    function get_kiot_products_recent_modified($raw_data) {
        global $kiotObj;
        $params = $raw_data->get_url_params();        
        $seconds = $params['seconds'];      
        $result = $kiotObj->get_kiot_products_recent_modified($seconds);        
        return rest_ensure_response( $result['data']);
    }