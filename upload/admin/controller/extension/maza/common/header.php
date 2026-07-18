<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */

class ControllerExtensionMazaCommonHeader extends Controller {
        public function index($data = array()) {
                $this->load->language('extension/maza/common/header');
                
                $this->document->addStyle('view/stylesheet/maza/mz_stylesheet.css');
                $this->document->addScript('view/javascript/maza/mz_common.js');
                
                
                $data['route'] = $this->request->get['route'];
                $data['user_token'] = $this->session->data['user_token'];
                
                $this->config->set('template_engine', 'template');
                return $this->load->view('extension/maza/common/header', $data);
        }
        
        public function main() {
            return str_replace('view/javascript/bootstrap/js/bootstrap.js', 'view/maza/javascript/bootstrap/js/bootstrap.min.js', $this->load->controller('common/header'));
        }
}
