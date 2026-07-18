<?php
class ControllerExtensionMazaCommonColumnLeft extends Controller {
        public function module($data) {
                if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ($this->request->get['user_token'] == $this->session->data['user_token'])) {
                        $this->load->language('extension/maza/common/column_left');
                        $this->load->model('tool/image');
                        $this->load->model('setting/module');
                        
                        $code = $data['code'];
                        
			// Menu
                        $data['modules'][] = array(
                                'id'       => 'mz-menu-add',
                                'icon'     => 'fa-plus-circle',
                                'name'     => $this->language->get('text_add_module'),
                                'active'   => (!isset($this->request->get['module_id']) && $this->request->get['route'] == 'extension/module/' . $code)?TRUE: FALSE,
                                'href'     => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'], true),
                                'children' => array()
                        );
                        
                        // module list
                        $modules = $this->model_setting_module->getModulesByCode($code);
                        foreach ($modules as $module) {
                            $data['modules'][] = array(
                                    'id'       => 'mz-menu-edit-' . $module['module_id'],
                                    'icon'     => 'fa-edit',
                                    'name'     => $module['name'],
                                    'active'   => (isset($this->request->get['module_id']) && $this->request->get['module_id'] === $module['module_id'])?TRUE: FALSE,
                                    'href'     => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true),
                                    'children' => array()
                            );
                        }
                        
                        return $this->load->view('extension/maza/common/column_left', $data);
                }
                
                
        }
}