<?php
class ControllerExtensionMazaCommonFooter extends Controller {
	public function index() {
                
                $this->config->set('template_engine', 'template');
		return $this->load->view('extension/maza/common/footer');
	}
}
