<?php
/*

F:'token=
R: $this->mpgdpr->token.'=
F:'token=
R: $this->mpgdpr->token.'=
F: ->data['token']
R: ->data[$this->mpgdpr->token]
F: , true)
R: , $this->mpgdpr->ssl)
F: $this->load->view(
R: $this->mpgdpr->view(

$data['token'] = $this->session->data[$this->mpgdpr->token];
$data['var_token'] = $this->mpgdpr->token;

View file
F: &token=
R: &<?php echo $var_token; ?>=
*/

class ModulePoints {

	protected $request;
	protected $load;
	protected $language;
	protected $db;
	protected $config;
	protected $registry;

	public $token = 'token';
	public $ssl = true;
	public function __construct($registry) 	{
		// do any startup work here
		$this->request = $registry->get('request');
		$this->load = $registry->get('load');
		$this->language = $registry->get('language');
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$this->registry = $registry;

		if(VERSION < '2.2.0.0') {
			$this->ssl = 'ssl';
		}
		if(VERSION >= '3.0.0.0') {
			$this->token = 'user_token';
		}
	}
	/*13 sep 2019 gdpr session starts*/
    public function session_id() {
    	if(VERSION >= '3.0.0.0') {
    		return $this->registry->get('session')->getId();
    	} else {
    		return session_id();
    	}
    }
    /*13 sep 2019 gdpr session ends*/
    public function captcha($captcha, $error='', $lang_data=array()) {
    	$controller_captcha = '';
    	if(VERSION == '2.0.0.0') {
    		$controller_captcha = '';
    		if($lang_data) {
				$controller_captcha .= '<div class="form-group required">';
				$controller_captcha .= '	<label class="col-sm-2 control-label" for="input-captcha">'.$lang_data['entry_captcha'].'</label>';
				$controller_captcha .= '	<div class="col-sm-10">';
				$controller_captcha .= '		<input type="text" name="captcha" id="input-captcha" class="form-control" />';
				$controller_captcha .= '	</div>';
				$controller_captcha .= '</div>';
				$controller_captcha .= '<div class="form-group">';
				$controller_captcha .= '	<div class="col-sm-10 pull-right">';
				$controller_captcha .= '		<img src="index.php?route=tool/captcha" alt="" />';
				if ($error) {
				$controller_captcha .= '		<div class="text-danger">'.$error.'</div>';
				}
				$controller_captcha .= '	</div>';
				$controller_captcha .= '</div>';
			}
    	} else if(VERSION == '2.0.2.0') {
    		$this->registry->get('document')->addScript('https://www.google.com/recaptcha/api.js');
    		$site_key = $this->config->get('config_google_captcha_public');
    		$controller_captcha = '';
    		if($site_key) {
				$controller_captcha .= '<div class="form-group">';
				$controller_captcha .= '	<div class="col-sm-offset-2 col-sm-10">';
				$controller_captcha .= '		<div class="g-recaptcha" data-sitekey="'.$site_key .'"></div>';
				if ($error) {
				$controller_captcha .= '		<div class="text-danger">'. $error .'</div>';
				}
				$controller_captcha .= '	</div>';
				$controller_captcha .= '</div>';
    		}
    	} else if (VERSION > '2.0.2.0' && VERSION <= '2.2.0.0') {
    		$controller_captcha = $this->load->controller('captcha/' . $captcha, $error);
    	} else {

	    	$controller_captcha = $this->load->controller('extension/captcha/' . $captcha, $error);
    	}

    	return $controller_captcha;
    }
    public function captchaValidate($captcha) {
    	$controller_captcha = '';

    	if(VERSION == '2.0.0.0') {

    		$controller_captcha = empty($this->registry->get('session')->data['captcha']) || ($this->registry->get('session')->data['captcha'] != $this->registry->get('request')->post['captcha']);

    	} else if(VERSION == '2.0.2.0') {

    		$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->registry->get('config')->get('config_google_captcha_secret')) . '&response=' . $this->registry->get('request')->post['g-recaptcha-response'] . '&remoteip=' . $this->registry->get('request')->server['REMOTE_ADDR']);

			$controller_captcha = json_decode($recaptcha, true);

    	} else if (VERSION > '2.0.2.0' && VERSION <= '2.2.0.0') {

    		$controller_captcha = $this->load->controller('captcha/' . $captcha . '/validate');
    	} else {
    		$controller_captcha = $this->load->controller('extension/captcha/' . $captcha . '/validate');
    	}
    	return $controller_captcha;
    }

    public function getLanguage(&$language) {
   		$language['flag'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
    }
    public function getLanguages($languages) {
    	if(VERSION >= '2.2.0.0') {
	    	foreach ($languages as &$language) {
	    		$language['flag'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
	    	}
        } else {
	    	foreach ($languages as &$language) {
	    		$language['flag'] = 'view/image/flags/'.$language['image'];
	    	}
        }
    	return $languages;
    }

    public function mkdir($dir) {
		if(!is_dir($dir)) {
			$oldmask = umask(0);
			mkdir($dir, 0777);
			umask($oldmask);
		}
	}

    public function getAdminCustomerModelString() {
    	if(VERSION < '2.1.0.1') {
	    	$this->load->model('sale/customer');
	    	$return = 'model_sale_customer';
    	} else {
	    	$this->load->model('customer/customer');
	    	$return = 'model_customer_customer';
    	}
    	return $return;
    }

    public function getMailObject() {
        if(VERSION >= '3.0.0.0') {
        	$mail = new \Mail($this->registry->get('config')->get('config_mail_engine'));
			$mail->parameter = $this->registry->get('config')->get('config_mail_parameter');
			$mail->smtp_hostname = $this->registry->get('config')->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->registry->get('config')->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->registry->get('config')->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->registry->get('config')->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->registry->get('config')->get('config_mail_smtp_timeout');
        } else if(VERSION >= '2.0.2.0') {
        	$mail = new \Mail();
			$mail->protocol = $this->registry->get('config')->get('config_mail_protocol');
			$mail->parameter = $this->registry->get('config')->get('config_mail_parameter');
			$mail->smtp_hostname = $this->registry->get('config')->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->registry->get('config')->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->registry->get('config')->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->registry->get('config')->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->registry->get('config')->get('config_mail_smtp_timeout');
		} else {
			$mail = new \Mail($this->registry->get('config')->get('config_mail'));
		}

		return $mail;
    }

    public function isAdminView($route, $data=array(), $template=false) {
    	// remove .tpl from route
		$route = str_replace(".tpl", "", $route);

		if(VERSION >= '3.0.0.0') {
			if($template) {
				// we load tpl view
	    		$old_template = $this->registry->get('config')->get('template_engine');
				$this->registry->get('config')->set('template_engine', 'template');
			}

			$file = $this->registry->get('load')->view($route, $data);
			if($template) {
				$this->registry->get('config')->set('template_engine', $old_template);
			}

		} else {
			$file = $this->registry->get('load')->view($route.'.tpl', $data);
		}

		return $file;
    }

    public function isCatalogView($route, $data=array(), $template=false) {
    	/*if (file_exists(DIR_TEMPLATE . $this->registry->get('config')->get('config_template') . '/template/account/account.tpl')) {
			$this->response->setOutput($this->load->view($this->registry->get('config')->get('config_template') . '/template/account/account.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/account/account.tpl', $data));

		}*/
		// remove .tpl from route
		$route = str_replace(".tpl", "", $route);


		if(VERSION < '2.2.0.0') {
			if (file_exists(DIR_TEMPLATE . $this->registry->get('config')->get('config_template') . '/template/'.$route.'.tpl')) {
				$file = $this->registry->get('load')->view($this->registry->get('config')->get('config_template').'/template/'.$route.'.tpl', $data);
			} else {
				$file = $this->registry->get('load')->view('default/template/'.$route.'.tpl', $data);
			}
		} else{
			$file = $this->registry->get('load')->view($route, $data);
		}
		return $file;
    }

    public function view($route, $data=array(),$template=false) {
    	// front end
		if (!defined('DIR_CATALOG')) {
			return $this->isCatalogView($route, $data, $template);
		} else {
		// backend
			return $this->isAdminView($route, $data, $template);
		}
    }

	/*https://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php*/
	public function getClientIp() {
		$ipaddress = '';
	    if (isset($this->request->server['HTTP_CLIENT_IP'])){
	        $ipaddress = $this->request->server['HTTP_CLIENT_IP'];
	    } else if(isset($this->request->server['HTTP_X_FORWARDED_FOR'])){
	        $ipaddress = $this->request->server['HTTP_X_FORWARDED_FOR'];
	    } else if(isset($this->request->server['HTTP_X_FORWARDED'])){
	        $ipaddress = $this->request->server['HTTP_X_FORWARDED'];
	    } else if(isset($this->request->server['HTTP_FORWARDED_FOR'])){
	        $ipaddress = $this->request->server['HTTP_FORWARDED_FOR'];
	    } else if(isset($this->request->server['HTTP_FORWARDED'])){
	        $ipaddress = $this->request->server['HTTP_FORWARDED'];
	    } else if(isset($this->request->server['REMOTE_ADDR'])){
	        $ipaddress = $this->request->server['REMOTE_ADDR'];
	    } else {
	        $ipaddress = 'UNKNOWN';
	    }
	    return $ipaddress;
	}

	/*https://stackoverflow.com/questions/5800927/how-to-identify-server-ip-address-in-php*/
	public function getServerIp() {
		$ipaddress = '';
		/*
		 * If you are using PHP in bash shell you can use:
		 * Because $this->request->server[] SERVER_ADDR, HTTP_HOST and SERVER_NAME are not set.
		 */
		if(php_sapi_name() == 'cli' || PHP_SAPI == 'cli'){
			$server_name = exec('hostname');
		} else {
			if(isset($this->request->server['SERVER_ADDR'])) {
				$ipaddress = $this->request->server['SERVER_ADDR'];
			} else if(isset($this->request->server['LOCAL_ADDR'])) {
				/*$this->request->server['LOCAL_ADDR'] is only available under IIS when PHP is running as a CGI module.*/
				$ipaddress = $this->request->server['LOCAL_ADDR'];
			}

			$server_name = $this->request->server['SERVER_NAME'];
		}

		if($ipaddress=='') {
			$ipaddress = gethostbyname($server_name);
		}

	    return $ipaddress;
	}

	public function getUserAgent() {
		$user_agent = '';
		if(isset($this->request->server['HTTP_USER_AGENT'])) {
			$user_agent = $this->request->server['HTTP_USER_AGENT'];
		} else {
			$user_agent = 'UNKNOWN';
		}
		return $user_agent;
	}
	public function getAcceptLanguage() {
		$accept_language = '';
		if(isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
			$accept_language = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
		} else {
			$accept_language = 'UNKNOWN';
		}
		return $accept_language;
	}

	public function token($length) {
		return token($length);
	}
}

if(!function_exists('token')) {
	function token($length = 32) {
		// Create random token
		$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$max = strlen($string) - 1;

		$token = '';

		for ($i = 0; $i < $length; $i++) {
			$token .= $string[mt_rand(0, $max)];
		}
		return $token;
	}
}