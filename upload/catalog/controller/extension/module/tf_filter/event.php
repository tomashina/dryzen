<?php
class ControllerExtensionModuleTfFilterEvent extends Controller {
        public function getProducts($route, $param) {
                $this->load->model('extension/maza/tf_product');
                
                if(empty($param[0])){
                    return $this->model_extension_maza_tf_product->getProducts();
                } else {
                    return $this->model_extension_maza_tf_product->getProducts($param[0]);
                }
                
        }
        
        public function getTotalProducts($route, $param) {
                $this->load->model('extension/maza/tf_product');
                
                if(empty($param[0])){
                    return $this->model_extension_maza_tf_product->getTotalProducts();
                } else {
                    return $this->model_extension_maza_tf_product->getTotalProducts($param[0]);
                }
                
        }
}