<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */
class ControllerExtensionMazaTfCron extends Controller {
        public function index() {
                $this->load->library('cart/user');
            
                if(!$this->config->get('module_tf_filter_cron_status')){
                    die('<h1>503 Service Unavailable</h1>');
                }

                $this->load->model('extension/maza/cron');

                if(!$this->model_extension_maza_cron->login()){
                    die('<h1>401 Unauthorized</h1>');
                };

                ignore_user_abort(true);
                set_time_limit(0);
                
                $this->model_extension_maza_cron->callToAdmin('extension/maza/tf_filter/sync');
        }
}
