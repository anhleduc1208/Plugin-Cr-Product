<?php
    /*-------add library------- */
    include_once __DIR__.'../../Classes/KiotViet.php';
    include_once __DIR__.'../../Classes/SyncKiotToWoo.php';
    include_once __DIR__.'../../Classes/Woocommerce.php';
    require __DIR__ . '../../woo_api_lib/vendor/autoload.php';       
    use Automattic\WooCommerce\Client;

    /*-----Declaration----------*/
    $woo_api_url = 'http://a.creta.vn';
    $woo_api_ck =  'ck_1e7d3516fa1371f44ac97a31fdb0629d5ebf6d82';
    $woo_api_cs = 'cs_b80311e7aef91c81aee92931ab791903767d95bf' ;
    $woo_info = array (
        'url' => $woo_api_url,
        'ck' => $woo_api_ck,
        'cs' => $woo_api_cs
    );

    $kiot_client_id = 'bae3bcbe-c860-4bac-9e4a-0651dcf4bad0';
    $kiot_client_secret = '0D92F5E0DF1973CC5385348F42C665D8775E7468';
    $kiot_client_retailer = 'cretasolu';
    $kiot_info = array(
        'id' => $kiot_client_id,
        'secret' => $kiot_client_secret,
        'retailer' => $kiot_client_retailer
    );

    $sync_obj = new SyncKiotToWoo($woo_info,$kiot_info);
    
    
    /*------Đăng kí api cho các thao tác đồng bộ sản phẩm của trang web---- */
    add_action('rest_api_init','creta_sync_product_register_route');

    function creta_sync_product_register_route() {
        register_rest_route( 'cr/v1', 'cr-sync-product/kiot-to-woo/recent-modified/seconds=(?P<seconds>[0-9-]+)', array(
            'methods' => 'GET',
            'callback' => 'sync_prod_recent_modified',
            // 'permission_callback' => function() {
            //     return current_user_can( 'edit_posts' );
            // },
            ) 
        );

        register_rest_route( 'cr/v1', 'cr-sync-product/kiot-to-woo/single/code=(?P<code>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => 'sync_single_prod_by_code',
            // 'permission_callback' => function() {
            //     return current_user_can( 'edit_posts' );
            // },
            ) 
        );

        register_rest_route( 'cr/v1', 'cr-sync-product/kiot-to-woo/(?P<quantity>[0-9-]+)/startIndex=(?P<startIndex>[0-9-]+)', array(
            'methods' => 'GET',
            'callback' => 'sync_num_of_prods_start_from',
            // 'permission_callback' => function() {
            //     return current_user_can( 'edit_posts' );
            // },
            ) 
        );

        register_rest_route( 'cr/v1', 'cr-sync-product/kiot-to-woo/single-stock/code=(?P<code>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => 'sync_stock_of_single_prod',
            // 'permission_callback' => function() {
            //     return current_user_can( 'edit_posts' );
            // },
            ) 
        );

        register_rest_route( 'cr/v1', 'cr-sync-product/kiot-to-woo/sku-list', array(
            'methods' => 'GET',
            'callback' => 'get_sku_list',
            // 'permission_callback' => function() {
            //     return current_user_can( 'edit_posts' );
            // },
            ) 
        );

        register_rest_route( 'cr/v1', 'cr-sync-product/kiot-to-woo/update-stock-all', array(
            'methods' => 'GET',
            'callback' => 'update_stock_all',
            // 'permission_callback' => function() {
            //     return current_user_can( 'edit_posts' );
            // },
            ) 
        );


    }


    /*------Các function call-back xử lý khi có request gửi đến các endpoint----- */

    function sync_prod_recent_modified($raw_data) {
        global $sync_obj;
        $url_data = $raw_data->get_url_params();  
        $seconds = $url_data['seconds'];   
        //$result = $kiotObj->get_all_kiot_products($get_data);
        return rest_ensure_response( 'developing: '.$seconds);
    }

    function sync_single_prod_by_code($raw_data) {
        global $sync_obj;
        $url_data = $raw_data->get_url_params();  
        $code = $url_data['code'];   
        //$result = $kiotObj->get_all_kiot_products($get_data);
        return rest_ensure_response( 'developing: '.$code);
    }

    function sync_num_of_prods_start_from($raw_data) {
        global $sync_obj;
        $url_data = $raw_data->get_url_params();  
        $quantity = $url_data['quantity'];   
        $startIndex = $url_data['startIndex']; 
        //$result = $kiotObj->get_all_kiot_products($get_data);
        return rest_ensure_response( 'developing: '.$quantity.' '.$startIndex);
    }

    function sync_stock_of_single_prod($raw_data) {
        global $sync_obj;
        $url_data = $raw_data->get_url_params();  
        $code = $url_data['code'];  
        
        $result = $sync_obj->sync_stock_by_code($code);
        return rest_ensure_response($result);
    }

    function get_sku_list() {
        global $sync_obj;        
        $result = $sync_obj->woo->get_sku_list_internal();
        return rest_ensure_response($result);
    }

    function update_stock_all() {
        global $sync_obj;        
        $sku_list = $sync_obj->woo->get_sku_list_internal();
        $sku_cnt = count($sku_list);
        $result= array();
        for ( $x=0; $x<$sku_cnt; $x++ ) {
            $rs = $sync_obj->sync_stock_by_code($sku_list[$x]);
            $rs['stt'] = $x + 1;
            array_push($result,$rs);
        }
        return rest_ensure_response($result);
    }


