<?php
class ControllerExtensionMazaStartupTfFilter extends Controller {
	public function index() {
                $this->registry->set('tf_db', new maza\DB($this->config->get('db_engine'), $this->config->get('db_hostname'), $this->config->get('db_username'), $this->config->get('db_password'), $this->config->get('db_database'), $this->config->get('db_port')));
                        
                // Create tempoaray table for Applicable tax on user
                $tax_class = $this->tax->tf_getTaxRates();
                
                $sql_values['p'] = $sql_values['f'] = array();
                
                foreach($tax_class as $tax_class_id => $tax_rates){
                    foreach($tax_rates as $tax_rate){
                        if($tax_rate['type'] == 'P'){
                            $sql_values['p'][] = "('" . (int)$tax_class_id . "', '" . (int)$tax_rate['tax_rate_id'] . "', '" . (float)$tax_rate['rate'] . "', '" . (int)$tax_rate['priority'] . "')";
                        } else {
                            $sql_values['f'][] = "('" . (int)$tax_class_id . "', '" . (int)$tax_rate['tax_rate_id'] . "', '" . (float)$tax_rate['rate'] . "', '" . (int)$tax_rate['priority'] . "')";
                        }
                    }
                }
                
                $this->db->query(
                "CREATE TEMPORARY TABLE " . DB_PREFIX . "tf_user_ptax_rates (
                     `tax_class_id` int(11) NOT NULL,
                     `tax_rate_id` int(11) NOT NULL,
                     `rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
                     `priority` int(5) NOT NULL DEFAULT '1'
                )");
                
                $this->db->query(
                "CREATE TEMPORARY TABLE " . DB_PREFIX . "tf_user_ftax_rates (
                     `tax_class_id` int(11) NOT NULL,
                     `tax_rate_id` int(11) NOT NULL,
                     `rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
                     `priority` int(5) NOT NULL DEFAULT '1'
                )");
                
                if($sql_values['p']){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "tf_user_ptax_rates VALUES " . implode(',', $sql_values['p']));
                }
                
                if($sql_values['f']){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "tf_user_ftax_rates VALUES " . implode(',', $sql_values['f']));
                }
	}
}
