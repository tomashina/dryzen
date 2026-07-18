<?php
class ControllerExtensionBaselQuestion extends Controller {
	public function index() {
		
		$this->load->language('basel/basel_theme');
		
		$data['basel_text_recent_questions'] = $this->language->get('basel_text_recent_questions');
		$data['basel_heading_ask'] = $this->language->get('basel_heading_ask');
		$data['basel_entry_name'] = $this->language->get('basel_entry_name');
		$data['basel_entry_email'] = $this->language->get('basel_entry_email');
		$data['basel_entry_question'] = $this->language->get('basel_entry_question');
		$data['basel_entry_captcha'] = $this->language->get('basel_entry_captcha');
		$data['text_note'] = $this->language->get('text_note');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['basel_button_send'] = $this->language->get('basel_button_send');
				
		if (isset($this->request->get['product_id'])) {
			$data['product_id'] = (int)$this->request->get['product_id'];
		} else {
			$data['product_id'] = 0;
		}

		return $this->load->view('product/question', $data);
		
	}
	
	
	
	public function question_list () {
		$this->load->language('basel/basel_theme');

		$this->load->model('extension/basel/question');
		
		$data['basel_text_question_from'] = $this->language->get('basel_text_question_from');
		$data['basel_text_no_questions'] = $this->language->get('basel_text_no_questions');
		$data['basel_text_no_answer'] = $this->language->get('basel_text_no_answer');
		$data['basel_text_our_answer'] = $this->language->get('basel_text_our_answer');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['questions'] = array();

		$questions_total = $this->model_extension_basel_question->getTotalQuestionsByProductId($this->request->get['product_id']);
		
		$limit = $this->config->get('questions_per_page');
		
		$results = $this->model_extension_basel_question->getQuestionsByProductId($this->request->get['product_id'], ($page - 1) * $limit, $limit);
		
		foreach ($results as $result) {
			$data['questions'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'answer'       => nl2br($result['answer']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $questions_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('questions_per_page');
		$pagination->url = $this->url->link('extension/basel/question/question_list', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($questions_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($questions_total - $limit)) ? $questions_total : ((($page - 1) * $limit) + $limit), $questions_total, ceil($questions_total / $limit));
		
		$this->response->setOutput($this->load->view('product/question_list', $data));
	}
	
	
	public function ask_question() {
		$this->load->language('basel/basel_theme');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 30)) {
				$json['error'] = $this->language->get('basel_error_name');
			}
			
			if ((utf8_strlen($this->request->post['email']) < 2) || (utf8_strlen($this->request->post['email']) > 60)) {
				$json['error'] = $this->language->get('basel_error_email');
			}

			if ((utf8_strlen($this->request->post['text']) < 10) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('basel_error_text');
			}

			if (empty($this->session->data['captcha_product_questions']) || ($this->session->data['captcha_product_questions'] != $this->request->post['captcha_product_questions'])) {
				$json['error'] = $this->language->get('basel_error_captcha');
			}

			if (!isset($json['error'])) {
				$this->load->model('extension/basel/question');

				$this->model_extension_basel_question->addQuestion($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('basel_text_success_qa');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	
	
	public function question_captcha() {
		$num1=rand(2,6);
		$num2=rand(2,6);
		$this->session->data['captcha_product_questions'] = $num1+$num2;
		$image = imagecreatetruecolor(58, 22);
		$width = imagesx($image);
		$height = imagesy($image);
		$black = imagecolorallocate($image, 100, 100, 100);
		$white = imagecolorallocate($image, 239, 239, 239);
		imagefilledrectangle($image, 0, 0, $width, $height, $white);
		imagestring($image, 4, 0, 3, "$num1"." + "."$num2"." =", $black);
		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
}
	
}