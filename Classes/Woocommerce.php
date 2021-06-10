<?php
    //require __DIR__ . '../../../woocommerce/includes/class-wc-product-query.php';
    require __DIR__ . '../../woo_api_lib/vendor/autoload.php';       
    use Automattic\WooCommerce\Client;
    
    class WooAPI {
        protected $ck;
        protected $cs;
        protected $url;
        public $woo;
        public function __construct($WooInfo) {
            $this->ck = $WooInfo['ck'];
            $this->cs = $WooInfo['cs'];
            $this->url = $WooInfo['url'];
            $this->woo = new Client (
                $this->url,
                $this->ck,
                $this->cs,
                [            
                    'version' => 'wc/v3',
                ]
            );
        } 
        public function set_connection($WooInfo){
            $this->ck = $WooInfo['ck'];
            $this->cs = $WooInfo['cs'];
            $this->url = $WooInfo['url'];
            $this->woo = new Client(
                $this->url,
                $this->ck,
                $this->cs,
                [            
                    'version' => 'wc/v3',
                ]
            );
            return true;
        }       
    }

    class WooProduct extends WooAPI {  
        /*----------------External Functions----------------------- */
            public function get_all_products_external() {            
                $result = $this->woo->get('products');
                return $result;
            }
            public function get_product_by_sku_external($params) {
                $result = $this->woo->get('products',$params);
                return $result;
            }
            public function update_product_by_sku_external($params,$put_data) {
                $prod = $this->woo->get('products',$params);
                $count = count($prod);
                if ($count == 1){
                    $id = $prod[0]->id;
                    $end_point = 'products/'.$id;
                    $update_data = $put_data;                
                    $result = $this->woo->put($end_point,$update_data);
                    
                } else if ($count == 0 ) {
                    $result = 'ko co ma sp nay';
                } else {
                    $result = 'Loi.ton tai nhiu ma sp qua';
                } 
                return $result;
            }

            public function create_new_product_external($post_data) {
                $result = $this->woo->post('products',$post_data);
                return $result;
            }

            public function del_product_by_sku_external($params) {
                $arr = $this->woo->get('products',$params);
                $count = count($arr);
                if ($count == 1){
                    $id = $arr[0]->id;
                    $end_point = 'products/'.$id; 
                
                    //echo 'xoa thanh cong';
                    $result = $this->woo->delete($end_point);
                    
                } else if ($count == 0 ) {
                    $result = 'ko co ma sp nay';
                } else {
                    $result = 'ton tai nhiu ma sp qua';
                }   
                return $result;
            }
        /*----------------Internal Functions----------------------- */
            public function get_sku_list_internal() {
                $result = array();
                $args = array(
                    'post_type' => 'product', 
                    'posts_per_page' => -1
                );
                
                $wcProductsArray = get_posts($args);
                
                if (count($wcProductsArray)) {
                    foreach ($wcProductsArray as $productPost) {
                        $productSKU = get_post_meta($productPost->ID, '_sku', true);
                        
                        array_push($result,$productSKU);                    
                    }
                }
                return $result;
            }

            public function get_all_products_internal() {  
                $result=array();          
                
                $args = array(
                    'post_type' => 'product', 
                    'posts_per_page' => -1
                );
                $wcProductsArray = get_posts($args);
                
                if (count($wcProductsArray)) {
                    foreach ($wcProductsArray as $productPost) {
                        $product = new WC_Product($productPost->ID);
                        
                        array_push($result,$product->get_data());                    
                    }
                }
               
                return $result;
            }

            public function get_product_by_sku_internal($params) {
                $result = array();
                $args = array(
                    'post_type' => 'product', 
                    'meta_key' => '_sku',
                    'meta_value' => $params['sku']
                );
                $wcProductsArray = get_posts($args);
                if (count($wcProductsArray) == 1) {
                    $product = new WC_Product($wcProductsArray[0]->ID);
                    array_push($result,$product->get_data());
                }              
                return $result;
            }

            public function update_product_by_sku_internal($params,$put_data) {
                $args = array(
                    'post_type' => 'product', 
                    'meta_key' => '_sku',
                    'meta_value' => $params['sku']
                );
                $wcProductsArray = get_posts($args);                        
                $count = count($wcProductsArray);

                if ($count == 1){
                    $id = $wcProductsArray[0]->ID;
                    $product = new WC_Product($id);
                   
                    foreach ($put_data as $key => $val) {              
                         
                        switch ($key) {
                            case 'name':
                                $product->set_name($val);
                                break;
                            case 'description':
                                $product->set_description($val);
                                break;
                            case 'manage_stock':
                                $product->set_manage_stock($val);
                                break;
                            case 'stock_quantity':
                                $product->set_stock_quantity($val);
                                break;
                            default: 
                        }                                         
                    }                    
                    $product->save($id);
                    $result = $product->get_data();
                                       
                } else if ($count == 0 ) {
                    $result = 'ko co ma sp nay';
                } else {
                    $result = 'Loi.ton tai nhiu ma sp qua';
                } 
                return $result;
            }
        /*----------------Developing----------------------- */
            // public function create_new_product_internal($post_data) {
            //     $result = $this->woo->post('products',$post_data);
            //     return $result;
            // }

            // public function del_product_by_sku_internal($params) {
            //     $arr = $this->woo->get('products',$params);
            //     $count = count($arr);
            //     if ($count == 1){
            //         $id = $arr[0]->id;
            //         $end_point = 'products/'.$id; 
                
            //         //echo 'xoa thanh cong';
            //         $result = $this->woo->delete($end_point);
                    
            //     } else if ($count == 0 ) {
            //         $result = 'ko co ma sp nay';
            //     } else {
            //         $result = 'ton tai nhiu ma sp qua';
            //     }   
            //     return $result;
            // }
    }