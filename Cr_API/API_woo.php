<?php
/*-------add library------- */
    include __DIR__.'../../Classes/Woocommerce.php';
    require __DIR__ . '../../woo_api_lib/vendor/autoload.php';       
    use Automattic\WooCommerce\Client;

/*-----Declaration----------*/
    $woo_api_url = 'http://a.creta.vn';
    $woo_api_ck =  'ck_1e7d3516fa1371f44ac97a31fdb0629d5ebf6d82';
    $woo_api_cs = 'cs_b80311e7aef91c81aee92931ab791903767d95bf' ;
    $WooInfo = array (
        'url' => $woo_api_url,
        'ck' => $woo_api_ck,
        'cs' => $woo_api_cs
    );
    $wooObj = new WooProduct($WooInfo);

/*------Đăng kí api cho woo của trang web---- */
    add_action('rest_api_init','creta_product_register_route');

    function creta_product_register_route() {        
        /*--------route for external woocommerce */
            register_rest_route( 'cr/v1', 'cr-woo', array(
                    'methods' => 'GET',
                    'callback' => 'get_all_products_external',
                    // 'permission_callback' => function() {
                    //     return current_user_can( 'edit_posts' );
                    // },
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo/sku=(?P<sku>[a-zA-Z0-9-]+)', array(
                'methods' => 'GET',
                'callback' => 'get_product_by_sku_external',
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo/sku=(?P<sku>[a-zA-Z0-9-]+)', array(
                'methods' => 'PUT',
                'callback' => 'update_product_by_sku_external',
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo', array(
                'methods' => 'POST',
                'callback' => 'create_new_product_external',
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo/sku=(?P<sku>[a-zA-Z0-9-]+)', array(
                'methods' => 'DELETE',
                'callback' => 'del_product_by_sku_external',
                ) 
            );

        /*--------route internal woocommerce */
            register_rest_route( 'cr/v1', 'cr-woo-in', array(
                'methods' => 'GET',
                'callback' => 'get_all_products_internal',
                // 'permission_callback' => function() {
                //     return current_user_can( 'edit_posts' );
                // },
                )
            ); 

            
            register_rest_route( 'cr/v1', 'cr-woo-in/sku=(?P<sku>[a-zA-Z0-9-]+)', array(
                'methods' => 'GET',
                'callback' => 'get_product_by_sku_internal',
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo-in/sku=(?P<sku>[a-zA-Z0-9-]+)', array(
                'methods' => 'PUT',
                'callback' => 'update_product_by_sku_internal',
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo-in', array(
                'methods' => 'POST',
                'callback' => 'create_new_product_internal',
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo-in/sku=(?P<sku>[a-zA-Z0-9-]+)', array(
                'methods' => 'DELETE',
                'callback' => 'del_product_by_sku_internal',
                ) 
            );

            register_rest_route( 'cr/v1', 'cr-woo-in/sku-list', array(
                'methods' => 'GET',
                'callback' => 'get_sku_list_internal',
                ) 
            );
    }


/*------Các function call-back xử lý khi có request gửi đến các endpoint----- */
    /*-----Xử lý data với woo external----- */    
        function get_all_products_external() {       
            global $wooObj;      
            $arr = $wooObj->get_all_products_external();
            return rest_ensure_response( $arr);
        }

        function get_product_by_sku_external($raw_data) {
            global $wooObj;   
            $params = $raw_data->get_url_params();  
            $arr = $wooObj->get_product_by_sku_external($params);
        
            return rest_ensure_response( $arr);
        }

        function update_product_by_sku_external($raw_data) {
            $params = $raw_data->get_url_params();
            $put_data = $raw_data->get_params();        
            
            global $wooObj;
        
            $arr = $wooObj->update_product_by_sku_external($params,$put_data);
            return rest_ensure_response($arr);
        }

        function create_new_product_external($data) {        
            $post_data = $data->get_params();
            global $wooObj;
        
            $new_prod = $wooObj->create_new_product_external($post_data);       
        
            return rest_ensure_response($new_prod);
        }

        function del_product_by_sku_external($raw_data) {
            $params = $raw_data->get_url_params();          
            
            global $wooObj;
        
            $rs = $wooObj->del_product_by_sku_external($params);   
            return rest_ensure_response($rs);
        }
    /*-----Xử lý data với woo internal----- */
        function get_all_products_internal() {       
            global $wooObj;      
            $arr = $wooObj->get_all_products_internal();
            return rest_ensure_response( $arr);
        }

        function get_product_by_sku_internal($raw_data) {
            global $wooObj;   
            $params = $raw_data->get_url_params();  
            $arr = $wooObj->get_product_by_sku_internal($params);
        
            return rest_ensure_response( $arr);
        }

        function update_product_by_sku_internal($raw_data) {
            $params = $raw_data->get_url_params();
            $put_data = $raw_data->get_params();        
            
            global $wooObj;
        
            $arr = $wooObj->update_product_by_sku_internal($params,$put_data);
            return rest_ensure_response($arr);
        }

        function create_new_product_internal($data) {        
            $post_data = $data->get_params();
            global $wooObj;
        
            $new_prod = $wooObj->create_new_product_internal($post_data);       
        
            return rest_ensure_response($new_prod);
        }

        function del_product_by_sku_internal($raw_data) {
            $params = $raw_data->get_url_params();   
            
            global $wooObj;
        
            $rs = $wooObj->del_product_by_sku_internal($params);   
            return rest_ensure_response($rs);
        }

        function get_sku_list_internal() {
            global $sync_obj;        
            $result = $sync_obj->woo->get_sku_list_internal();
            return rest_ensure_response($result);
        }


?>