<?php
    /*-------add library------- */
   

    /*-------declare class------- */
    class SyncKiotToWoo {
        public $woo;
        protected $kiot;
        public function __construct($woo_info,$kiot_info) {
            $this->woo = new WooProduct($woo_info);
            $this->kiot = new KiotProduct($kiot_info);
        }
        public function sync_stock_by_code($code) {
            $woo_param = array(
                'sku' => $code
            );
            $woo_found = $this->woo->get_product_by_sku_internal($woo_param);
            $woo_cnt = count($woo_found);

            $kiot_found = $this->kiot->get_kiot_product_by_code($code);
            $kiot_cnt =count($kiot_found);

            if ($woo_cnt == 1) {
                //Co ma tren woo
                if ($kiot_cnt > 1){
                    // co ma tren kiot -> cap nhat stock 
                    $kiot_stock_on_hand = $kiot_found['inventories'][0]['onHand'];
                    $update_data = array(
                        'manage_stock' => true,
                        'stock_quantity' => $kiot_stock_on_hand
                    );
                    // $id = $woo_found[0]->id;
                    // $end_point = 'products/'.$id;                                  
                    // $result = $this->woo->woo->put($end_point,$update_data);
                    $updated_prod = $this->woo->update_product_by_sku_internal($woo_param,$update_data);
                    $result = array(
                        'code' => $code,
                        'stock' => $kiot_stock_on_hand,
                        'status' => 'updated'
                    );
                } else {
                    // ko co ma tren kiot -> thong bao
                    $result = array(
                        'code' => $code,
                        'status' => 'Ma nay k co tren kiot, chi co tren woo, ko the cap nhat duoc'
                    );
                }
            } else {
                // chua co ma sp nay, di them moi
                $result = array(
                    'code' => $code,
                    'status' => 'Khong ton tai ma sp nay tren woo -> vui long cap nhat lai'
                );                
            }
            return $result;
        }
    }