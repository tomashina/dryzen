<?php
class ControllerExtensionModuleSecurityHeaders extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/security_headers');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

                $store_id = $this->getStoreId();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

                        if (empty($this->request->post['module_security_headers_status']['status']) || ($this->request->post['module_security_headers_status']['status'] == '0')) {
                                $this->model_setting_setting->editSettingValue('module_security_headers', 'module_security_headers_status', 0, $store_id);
                        } else {
			        $this->model_setting_setting->editSetting('module_security_headers', $this->request->post, $store_id);
                        }
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
                // Texts
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_insecure'] = $this->language->get('text_insecure');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_stores'] = $this->language->get('text_stores');
		$data['text_select_store'] = $this->language->get('text_select_store');
		// Entries
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_X_Powered_By'] = $this->language->get('entry_X_Powered_By');
		$data['entry_forward'] = $this->language->get('entry_forward');
		$data['entry_ranges'] = $this->language->get('entry_ranges');
		$data['entry_proxy'] = $this->language->get('entry_proxy');
		$data['entry_X_HTTP_Method_Override'] = $this->language->get('entry_X_HTTP_Method_Override');
		$data['entry_X_XSS_Protection'] = $this->language->get('entry_X_XSS_Protection');
		$data['entry_X_Frame_Options'] = $this->language->get('entry_X_Frame_Options');
		$data['entry_X_Content_Type_Options'] = $this->language->get('entry_X_Content_Type_Options');
		$data['entry_Referrer_Policy'] = $this->language->get('entry_Referrer_Policy');
		$data['entry_Content_Security_Policy'] = $this->language->get('entry_Content_Security_Policy');
		$data['entry_max_age'] = $this->language->get('entry_max_age');
		// Buttons
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		// Placeholders
		$data['placeholder_strict_transport_security'] = $this->language->get('placeholder_strict_transport_security');
		$data['placeholder_expect_ct_max_age'] = $this->language->get('placeholder_expect_ct_max_age');
		$data['placeholder_expect_ct_report_uri'] = $this->language->get('placeholder_expect_ct_report_uri');
		// About
		$data['about_extension'] = $this->language->get('about_extension');
		$data['about_X_Powered_By'] = $this->language->get('about_X_Powered_By');
		$data['about_forward'] = $this->language->get('about_forward');
		$data['about_ranges'] = $this->language->get('about_ranges');
		$data['about_proxy'] = $this->language->get('about_proxy');
		$data['about_X_HTTP_Method_Override'] = $this->language->get('about_X_HTTP_Method_Override');
		$data['about_X_XSS_Protection'] = $this->language->get('about_X_XSS_Protection');
		$data['about_X_Frame_Options'] = $this->language->get('about_X_Frame_Options');
		$data['about_X_Content_Type_Options'] = $this->language->get('about_X_Content_Type_Options');
		$data['about_Referrer_Policy'] = $this->language->get('about_Referrer_Policy');
		$data['about_Strict_Transport_Security'] = $this->language->get('about_Strict_Transport_Security');
		$data['about_Expect_CT'] = $this->language->get('about_Expect_CT');
		$data['about_Content_Security_Policy'] = $this->language->get('about_Content_Security_Policy');
		$data['about_Feature_Policy'] = $this->language->get('about_Feature_Policy');
		// Legends
		$data['legend_extension'] = $this->language->get('legend_extension');
		$data['legend_X_Powered_By'] = $this->language->get('legend_X_Powered_By');
		$data['legend_forward'] = $this->language->get('legend_forward');
		$data['legend_ranges'] = $this->language->get('legend_ranges');
		$data['legend_proxy'] = $this->language->get('legend_proxy');
		$data['legend_X_HTTP_Method_Override'] = $this->language->get('legend_X_HTTP_Method_Override');
		$data['legend_X_XSS_Protection'] = $this->language->get('legend_X_XSS_Protection');
		$data['legend_X_Frame_Options'] = $this->language->get('legend_X_Frame_Options');
		$data['legend_X_Content_Type_Options'] = $this->language->get('legend_X_Content_Type_Options');
		$data['legend_Referrer_Policy'] = $this->language->get('legend_Referrer_Policy');
		$data['legend_Strict_Transport_Security'] = $this->language->get('legend_Strict_Transport_Security');
		$data['legend_Expect_CT'] = $this->language->get('legend_Expect_CT');
		$data['legend_Content_Security_Policy'] = $this->language->get('legend_Content_Security_Policy');
		$data['legend_Feature_Policy'] = $this->language->get('legend_Feature_Policy');
		
		// Feature Policy List
		$data['type_accelerometer'] = $this->language->get('type_accelerometer');
		$data['type_ambient_light_sensor'] = $this->language->get('type_ambient_light_sensor');
		$data['type_autoplay'] = $this->language->get('type_autoplay');
		$data['type_camera'] = $this->language->get('type_camera');
		$data['type_fullscreen'] = $this->language->get('type_fullscreen');
		$data['type_display_capture'] = $this->language->get('type_display_capture');
		$data['type_document_domain'] = $this->language->get('type_document_domain');
		$data['type_encrypted_media'] = $this->language->get('type_encrypted_media');
		$data['type_geolocation'] = $this->language->get('type_geolocation');
		$data['type_gyroscope'] = $this->language->get('type_gyroscope');
		$data['type_layout_animations'] = $this->language->get('type_layout_animations');
		$data['type_legacy_image_format'] = $this->language->get('type_legacy_image_format');
		$data['type_magnetometer'] = $this->language->get('type_magnetometer');
		$data['type_microphone'] = $this->language->get('type_microphone');
		$data['type_midi'] = $this->language->get('type_midi');
		$data['type_oversized_images'] = $this->language->get('type_oversized_images');
		$data['type_payment'] = $this->language->get('type_payment');
		$data['type_picture_in_picture'] = $this->language->get('type_picture_in_picture');
		$data['type_speaker'] = $this->language->get('type_speaker');
		$data['type_sync_xhr'] = $this->language->get('type_sync_xhr');
		$data['type_unoptimized_images'] = $this->language->get('type_unoptimized_images');
		$data['type_unsized_media'] = $this->language->get('type_unsized_media');
		$data['type_usb'] = $this->language->get('type_usb');
		$data['type_vr'] = $this->language->get('type_vr');
		$data['type_vibrate'] = $this->language->get('type_vibrate');
		$data['type_webauthn'] = $this->language->get('type_webauthn');
		
		// Help Tooltip Feature Policies
		$data['help_accelerometer'] = $this->language->get('help_accelerometer');
		$data['help_ambient_light_sensor'] = $this->language->get('help_ambient_light_sensor');
		$data['help_autoplay'] = $this->language->get('help_autoplay');
		$data['help_camera'] = $this->language->get('help_camera');
		$data['help_fullscreen'] = $this->language->get('help_fullscreen');
		$data['help_display_capture'] = $this->language->get('help_display_capture');
		$data['help_document_domain'] = $this->language->get('help_document_domain');
		$data['help_encrypted_media'] = $this->language->get('help_encrypted_media');
		$data['help_geolocation'] = $this->language->get('help_geolocation');
		$data['help_gyroscope'] = $this->language->get('help_gyroscope');
		$data['help_layout_animations'] = $this->language->get('help_layout_animations');
		$data['help_legacy_image_format'] = $this->language->get('help_legacy_image_format');
		$data['help_magnetometer'] = $this->language->get('help_magnetometer');
		$data['help_microphone'] = $this->language->get('help_microphone');
		$data['help_midi'] = $this->language->get('help_midi');
		$data['help_oversized_images'] = $this->language->get('help_oversized_images');
		$data['help_payment'] = $this->language->get('help_payment');
		$data['help_picture_in_picture'] = $this->language->get('help_picture_in_picture');
		$data['help_speaker'] = $this->language->get('help_speaker');
		$data['help_sync_xhr'] = $this->language->get('help_sync_xhr');
		$data['help_unoptimized_images'] = $this->language->get('help_unoptimized_images');
		$data['help_unsized_media'] = $this->language->get('help_unsized_media');
		$data['help_usb'] = $this->language->get('help_usb');
		$data['help_vr'] = $this->language->get('help_vr');
		$data['help_vibrate'] = $this->language->get('help_vibrate');
		$data['help_webauthn'] = $this->language->get('help_webauthn');
		
		// Warnings
		$data['warning_Strict_Transport_Security'] = $this->language->get('warning_Strict_Transport_Security');
		$data['warning_Expect_CT'] = $this->language->get('warning_Expect_CT');
		$data['warning_Referrer_Policy'] = $this->language->get('warning_Referrer_Policy');
		$data['warning_X_Content_Type_Options'] = $this->language->get('warning_X_Content_Type_Options');
		$data['warning_X_Frame_Options'] = $this->language->get('warning_X_Frame_Options');
		$data['warning_X_XSS_Protection'] = $this->language->get('warning_X_XSS_Protection');
		
		
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['expect_ct_report_uri'])) {
			$data['expect_ct_report_uri'] = $this->error['expect_ct_report_uri'];
		} else {
			$data['expect_ct_report_uri'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/security_headers', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/security_headers', 'user_token=' . $this->session->data['user_token'] . '&store_id='.$store_id, true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

                $this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name'),
			'url'      => $this->url->link('extension/module/security_headers', 'user_token=' . $this->session->data['user_token'] . '&store_id=0', true)
			);
                
		$results = $this->model_setting_store->getStores();

		if ($results) {
			foreach ($results as $key => $value) {
				$data['stores'][] = array(
					'store_id' => $value['store_id'],
					'name'     => $value['name'],
					'url'      => $this->url->link('extension/module/security_headers', 'user_token=' . $this->session->data['user_token'] . '&store_id='.$value['store_id'], true)
					);
			}
		}

                $data['settings'] = array();
                
		$data['settings'] = json_decode($this->model_setting_setting->getSettingValue('module_security_headers_status', $store_id), true);
                
                $data['store_id'] = $this->getStoreId();
                
                $data['store'] = $this->getCurrentStore($store_id);

                if (isset($data['settings']['status'])) {
                        $data['status'] = $data['settings']['status'];
                } else {
                        $data['status'] = 0;
                }
                
                if (isset($data['settings']['X_XSS_Protection'])) {
                        $data['X_XSS_Protection'] = $data['settings']['X_XSS_Protection'];
                } else {
                        $data['X_XSS_Protection'] = '';
                }
                
                if (isset($data['settings']['X_Frame_Options'])) {
                        $data['X_Frame_Options'] = $data['settings']['X_Frame_Options'];
                } else {
                        $data['X_Frame_Options'] = '';
                }
                
                if (isset($data['settings']['X_Content_Type_Options'])) {
                        $data['X_Content_Type_Options'] = $data['settings']['X_Content_Type_Options'];
                } else {
                        $data['X_Content_Type_Options'] = '';
                }
      
                if (isset($data['settings']['Referrer_Policy'])) {
                        $data['Referrer_Policy'] = $data['settings']['Referrer_Policy'];
                } else {
                        $data['Referrer_Policy'] = '';
                }
                
                if (isset($data['settings']['CSP'])) {
                        $data['CSP'] = $data['settings']['CSP'];
                } else {
                        $data['CSP'] = '';
                }
                
                if (isset($data['settings']['X_Powered_By'])) {
                        $data['X_Powered_By'] = $data['settings']['X_Powered_By'];
                } else {
                        $data['X_Powered_By'] = 1;
                }
                
                if (isset($data['settings']['forward'])) {
                        $data['forward'] = $data['settings']['forward'];
                } else {
                        $data['forward'] = 1;
                }
                
                if (isset($data['settings']['ranges'])) {
                        $data['ranges'] = $data['settings']['ranges'];
                } else {
                        $data['ranges'] = 1;
                }
                
                if (isset($data['settings']['proxy'])) {
                        $data['proxy'] = $data['settings']['proxy'];
                } else {
                        $data['proxy'] = 1;
                }
                
                if (isset($data['settings']['X_HTTP_Method_Override'])) {
                        $data['X_HTTP_Method_Override'] = $data['settings']['X_HTTP_Method_Override'];
                } else {
                        $data['X_HTTP_Method_Override'] = 1;
                }
                
                if (isset($data['settings']['Expect_CT'])) {
                        foreach ($data['settings']['Expect_CT'] as $key => $value) {
                                if (isset($data['settings']['Expect_CT'][$key])) {
                                        $data['Expect_CT'][$key] = $value;
                                } else {
                                        $data['Expect_CT'][$key] = '';
                                }
                        }
                }
                
                if (isset($data['settings']['Feature_Policy'])) {
                        foreach ($data['settings']['Feature_Policy'] as $key => $value) {
                                if (isset($data['settings']['Feature_Policy'][$key])) {
                                        $data['Feature_Policy'][$key] = $value;
                                } else {
                                        $data['Feature_Policy'][$key] = '';
                                }
                        }
                }
                
                if (isset($data['settings']['Strict_Transport_Security'])) {
                        $data['Strict_Transport_Security'] = $data['settings']['Strict_Transport_Security'];
                } else {
                        $data['Strict_Transport_Security'] = '';
                }
                

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/security_headers', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/security_headers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
                if ($this->request->post['module_security_headers_status']['Expect_CT']['max_age']) {

        		if (empty($this->request->post['module_security_headers_status']['Expect_CT']['report_uri'])) {
                                        $this->error['expect_ct_report_uri'] = $this->language->get('error_expect_ct_report_uri');
                                        $this->error['warning'] = $this->language->get('error_data');
                        }
                        
                }
                
		return !$this->error;
	}
	
	protected function getStoreId() {
		$store_id = 0;

		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} elseif (isset($this->request->post['store_id'])) {
			$store_id = $this->request->post['store_id'];
		} else {
			$store_id = 0;
		}

		return $store_id;
	}
	
	protected function getCurrentStore($store_id) {    
		if($store_id && $store_id != 0) {
			$store = $this->model_setting_store->getStore($store_id);
		} else {
			$store['store_id'] = 0;
			$store['name'] = $this->config->get('config_name');
		}
		return $store;
	}
	
}
