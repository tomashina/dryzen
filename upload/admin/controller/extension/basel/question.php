<?php
class ControllerExtensionBaselQuestion extends Controller {
	private $error = array();

	public function index() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/question');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/question');
		
		$this->getList();
	}

	public function add() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/question');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/question');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_basel_question->addQuestion($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/question');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/question');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_basel_question->editQuestion($this->request->get['question_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/question');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/question');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			
			foreach ($this->request->post['selected'] as $question_id) {
				$this->model_extension_basel_question->deleteQuestion($question_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		if (isset($this->request->get['filter_product'])) {
			$filter_product = $this->request->get['filter_product'];
		} else {
			$filter_product = null;
		}

		if (isset($this->request->get['filter_author'])) {
			$filter_author = $this->request->get['filter_author'];
		} else {
			$filter_author = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true)
		);
		
		$data['add'] = $this->url->link('extension/basel/question/add', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true);
		$data['delete'] = $this->url->link('extension/basel/question/delete', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true);

		$data['questions'] = array();

		$filter_data = array(
			'filter_product'    => $filter_product,
			'filter_author'     => $filter_author,
			'filter_status'     => $filter_status,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$question_total = $this->model_extension_basel_question->getTotalQuestions($filter_data);

		$results = $this->model_extension_basel_question->getQuestions($filter_data);

		foreach ($results as $result) {
			$data['questions'][] = array(
				'question_id'  => $result['question_id'],
				'name'       => $result['name'],
				'author'     => $result['author'],
				'email'     => $result['email'],
				'answer'     => $result['answer'],
				'text'     => $result['text'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('extension/basel/question/edit', $token_prefix . '=' . $this->session->data[$token_prefix] . '&question_id=' . $result['question_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
				
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_answered'] = $this->language->get('text_answered');
		$data['text_not_answered'] = $this->language->get('text_not_answered');
		
		$data['column_product'] = $this->language->get('column_product');
		$data['column_author'] = $this->language->get('column_author');
		$data['column_text'] = $this->language->get('column_text');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_info'] = $this->language->get('column_info');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		
		$data['global_status'] = $this->config->get('product_question_status');

		$data['token'] = $this->session->data[$token_prefix];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_product'] = $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . '&sort=pd.name' . $url, true);
		$data['sort_author'] = $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . '&sort=r.author' . $url, true);
		
		$data['sort_status'] = $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . '&sort=r.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . '&sort=r.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $question_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($question_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($question_total - $this->config->get('config_limit_admin'))) ? $question_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $question_total, ceil($question_total / $this->config->get('config_limit_admin')));

		$data['filter_product'] = $filter_product;
		$data['filter_author'] = $filter_author;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/basel/question_list', $data));
	}

	protected function getForm() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['question_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_answer'] = $this->language->get('entry_answer');
		$data['entry_notify'] = $this->language->get('entry_notify');
		$data['entry_notify_help'] = $this->language->get('entry_notify_help');

		$data['help_product'] = $this->language->get('help_product');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}
		
		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['text'])) {
			$data['error_text'] = $this->error['text'];
		} else {
			$data['error_text'] = '';
		}		

		$url = '';

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true)
		);
		
		if (!isset($this->request->get['question_id'])) {
			$data['action'] = $this->url->link('extension/basel/question/add', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/basel/question/edit', $token_prefix . '=' . $this->session->data[$token_prefix] . '&question_id=' . $this->request->get['question_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/basel/question', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, true);

		if (isset($this->request->get['question_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$question_info = $this->model_extension_basel_question->getQuestion($this->request->get['question_id']);
		}

		$data['token'] = $this->session->data[$token_prefix];

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($question_info)) {
			$data['product_id'] = $question_info['product_id'];
		} else {
			$data['product_id'] = '';
		}

		if (isset($this->request->post['product'])) {
			$data['product'] = $this->request->post['product'];
		} elseif (!empty($question_info)) {
			$data['product'] = $question_info['product'];
		} else {
			$data['product'] = '';
		}

		if (isset($this->request->post['author'])) {
			$data['author'] = $this->request->post['author'];
		} elseif (!empty($question_info)) {
			$data['author'] = $question_info['author'];
		} else {
			$data['author'] = '';
		}
		
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($question_info)) {
			$data['email'] = $question_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['text'])) {
			$data['text'] = $this->request->post['text'];
		} elseif (!empty($question_info)) {
			$data['text'] = $question_info['text'];
		} else {
			$data['text'] = '';
		}
		
		if (isset($this->request->post['answer'])) {
			$data['answer'] = $this->request->post['answer'];
		} elseif (!empty($question_info)) {
			$data['answer'] = $question_info['answer'];
		} else {
			$data['answer'] = '';
		}
		
		if (isset($this->request->post['notify'])) {
			$data['notify'] = $this->request->post['notify'];
		} elseif (!empty($question_info)) {
			$data['notify'] = $question_info['notify'];
		} else {
			$data['notify'] = '1';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($question_info)) {
			$data['status'] = $question_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/basel/question_form', $data));
	}
	


	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/basel/question')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['product_id']) {
			$this->error['product'] = $this->language->get('error_product');
		}

		if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
			$this->error['author'] = $this->language->get('error_author');
		}
		
		if ((utf8_strlen($this->request->post['email']) < 3) || (utf8_strlen($this->request->post['email']) > 64)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (utf8_strlen($this->request->post['text']) < 1) {
			$this->error['text'] = $this->language->get('error_text');
		}

		return !$this->error;
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/basel/question')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/basel/question')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}