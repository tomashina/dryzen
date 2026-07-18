<?php
class ControllerExtensionMazaStartupTfFilter extends Controller {
	public function index() {
                $this->registry->set('tf_db', new maza\DB($this->config->get('db_engine'), $this->config->get('db_hostname'), $this->config->get('db_username'), $this->config->get('db_password'), $this->config->get('db_database'), $this->config->get('db_port')));
	}
}
