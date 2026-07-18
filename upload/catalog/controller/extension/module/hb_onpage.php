<?php
class ControllerExtensionModuleHbOnpage extends Controller {
    public function auto() {
        $auto = true;

        $this->load->model('extension/module/hb_onpage');

        $this->model_extension_module_hb_onpage->addlog('**AUTO MODE STARTED**');
        $this->load->model('extension/module/hb_onpage');

        if (isset($this->request->get['authkey'])) {
            $authkey = $this->request->get['authkey'];
        } else {
            $authkey = '';
        }

        $actual_authkey = $this->config->get('hb_onpage_authkey');
        if ($authkey != $actual_authkey or $authkey == '') {
            die('AUTHORIZATION FAILED');
        }

        $page_type = array('product', 'category', 'manufacturer', 'information');

        $this->load->model('setting/setting');
        $data['stores'][] = 0;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store");
        if ($query->num_rows > 0) {
            $results = $query->rows;
            foreach ($results as $result) {
                $data['stores'][] = $result['store_id'];
            }
        }

        foreach ($data['stores'] as $store_id) {
            foreach ($page_type as $type) {
                $this->model_extension_module_hb_onpage->generateByPage($type, $store_id, $auto);
            }
        }

        $this->model_extension_module_hb_onpage->addlog('**AUTO MODE COMPLETED**');
        die('AUTO MODE COMPLETED');
    }

    public function generate_pages() {
        //MANUAL BY ADMIN
        $auto = false;

        $json = [];

        $this->load->model('extension/module/hb_onpage');

        $authkey = isset($this->request->get['authkey']) ? $this->request->get['authkey'] : '';
        $type    = isset($this->request->get['page_type']) ? $this->request->get['page_type'] : 'product';

        $actual_authkey = $this->config->get('hb_onpage_authkey');

        if ($authkey != $actual_authkey or $authkey == '') {
            $json['error'] = 'AUTHORIZATION FAILED';
        }

        if (!$json) {
            $this->load->model('setting/setting');
            $data['stores'][] = 0;

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store");
            if ($query->num_rows > 0) {
                $results = $query->rows;
                foreach ($results as $result) {
                    $data['stores'][] = $result['store_id'];
                }
            }

            foreach ($data['stores'] as $store_id) {
                $this->model_extension_module_hb_onpage->generateByPage($type, $store_id, $auto);
            }

            $json['success'] = 'On-Page Elements Generated for ' . $type . ' pages';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function generate_element() {
        //MANUAL BY ADMIN
        $auto = false;
        $json = [];
        $this->load->model('extension/module/hb_onpage');

        $store_id       = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
        $previous_count = isset($this->request->get['previous_count']) ? $this->request->get['previous_count'] : 0;
        $page_type      = isset($this->request->post['page_type']) ? $this->request->post['page_type'] : 'product';
        $element_type   = isset($this->request->post['element']) ? $this->request->post['element'] : 'h1';

        $authkey        = isset($this->request->post['authkey']) ? $this->request->post['authkey'] : '';
        $actual_authkey = $this->config->get('hb_onpage_authkey');

        if ($authkey != $actual_authkey or $authkey == '') {
            $json['error'] = 'AUTHORIZATION FAILED';
        }

        if (!$json) {
            $limit_start = 0;
            $limit_count = 10;

            $json['count'] = 0;

            $records_total = $this->model_extension_module_hb_onpage->getTotalEmptyTags($page_type, $element_type);

            if ($records_total > 0) {
                if ($previous_count != $records_total) {
                    $json['count'] = $records_total;
                    $records = $this->model_extension_module_hb_onpage->getEmptyTags($page_type, $element_type, $limit_start, $limit_count);

                    if ($records_total > $limit_count) {
                        $json['success'] = 'Processing ' . $limit_count . ' of remaining ' . $records_total . ' records';
                        $json['next'] = 'set';
                    } else {
                        $json['success'] = 'Completed: ' . $element_type . ' generated for ' . $page_type;
                    }

                    foreach ($records as $record) {
                        if ($this->model_extension_module_hb_onpage->generateSeoElement($page_type, $record['id'], $element_type, $store_id) === false) {
                            $json['error'] = 'Error: ' . $element_type . ' generation failed for ' . $page_type;
                            break;
                        }
                    }
                } else {
                    $json['error'] = 'Stopped: ' . $element_type . ' generation stopped for ' . $page_type . '. Check logs for more details.';
                }
            } else {
                $json['success'] = 'All '. $element_type . ' elements generated for ' . $page_type . ' pages.';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function preview() {
        $this->load->model('extension/module/hb_onpage');

        $store_id    = (int)$this->request->post['store_id'];
        $template_id = (int)$this->request->post['template_id'];

        $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hb_onpage_templates` WHERE id = '" . $template_id . "'");

        if ($result->rows) {
            $page_type    = $result->row['page_type'];
            $element_type = $result->row['element_type'];
            $language_id  = $result->row['language_id'];
            $template     = $result->row['template'];

            $random_id    = $this->model_extension_module_hb_onpage->getRandomId($page_type);
            $info         = $this->model_extension_module_hb_onpage->getPageInfo($page_type, $random_id, $language_id);
            $composed_seo = $this->model_extension_module_hb_onpage->replaceParameters($template, $info);

            $json['success'] = $composed_seo;
            $json['count']   = 'Number of Characters : ' . strlen($composed_seo);
        } else {
            $json['error'] = 'Template not found';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
