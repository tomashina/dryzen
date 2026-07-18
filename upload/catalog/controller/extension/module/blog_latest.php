<?php
class ControllerExtensionModuleBlogLatest extends Controller {
	public function index($setting) {
		
		static $module = 0;
		
		$this->load->language('blog/blog');
		$this->load->model('extension/blog/blog');
		$this->load->model('tool/image');
		
		$data = array(
			'start' => 0,
			'limit' => $setting['limit']
		);
		
		// RTL support
		$data['direction'] = $this->language->get('direction');
		
		// Block title
		$data['block_title'] = $setting['use_title'];
		$data['title_preline'] = false;
		$data['title'] = false;
		$data['title_subline'] = false;
		
		if (!empty($setting['title_pl'][$this->config->get('config_language_id')])) {
		$data['title_preline'] = html_entity_decode($setting['title_pl'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['title_m'][$this->config->get('config_language_id')])) {
		$data['title'] = html_entity_decode($setting['title_m'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['title_b'][$this->config->get('config_language_id')])) {
		$data['title_subline'] = html_entity_decode($setting['title_b'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		
		$data['contrast'] = $setting['contrast'];
		$data['characters'] = $setting['characters'];
		$data['columns'] = $setting['columns'];
		$data['thumb'] = $setting['use_thumb'];
		$data['carousel'] = $setting['carousel'];
		$data['carousel_a'] = $setting['carousel_a'];
		$data['carousel_b'] = $setting['carousel_b'];
		$data['rows'] = $setting['rows'];
		$data['use_button'] = $setting['use_button'];
		$data['use_margin'] = $setting['use_margin'];
		$data['margin'] = $setting['margin'];
		$data['img_width'] = $setting['width'];
		
		
		foreach ($this->model_extension_blog_blog->getLatestBlogs($data) as $result) {
			
		if ($result['tags']) {
			$tags = explode(',', $result['tags']);
		} else {
			$tags = false;
		}
		
		if ($setting['characters']) {
			$description = utf8_substr(strip_tags(html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8')), 0, $setting['characters']) . '..';
		} else {
			$description = false;
		}
		
		$m = date("m",strtotime($result['date_added']));
			$months = array (
					1 => $this->language->get('text_month_jan'),
					2 => $this->language->get('text_month_feb'),
					3 => $this->language->get('text_month_mar'),
					4 => $this->language->get('text_month_apr'),
					5 => $this->language->get('text_month_may'),
					6 => $this->language->get('text_month_jun'),
					7 => $this->language->get('text_month_jul'),
					8 => $this->language->get('text_month_aug'),
					9 => $this->language->get('text_month_sep'),
					10 => $this->language->get('text_month_oct'),
					11 => $this->language->get('text_month_nov'),
					12 => $this->language->get('text_month_dec')
					);
		$date_added_month = $months[(int)$m];
			
      		$data['posts'][] = array(
			'title' => $result['title'],
			'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			'author' => $result['author'],
			'comment_total' => $this->model_extension_blog_blog->getTotalCommentsByBlogId($result['blog_id']),
			'date_added_day' 	=> date("d",strtotime($result['date_added'])),
			'date_added_month' 	=> $date_added_month,
			'short_description' => $description,
			'count_read' => $result['count_read'],
			'tags' 				=> $tags,
			'image'   		=> $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
			'href'  => $this->url->link('extension/blog/blog', 'blog_id=' . $result['blog_id'])
      		);
    	}
		
		$data['blog_show_all'] = $this->url->link('extension/blog/home');
		
		$data['text_show_all'] = $this->language->get('text_show_all');
		$data['text_posted_on'] = $this->language->get('text_posted_on');
		$data['text_posted_by'] = $this->language->get('text_posted_by');
		$data['text_read'] = $this->language->get('text_read');
		$data['text_comments'] = $this->language->get('text_comments');
		$data['text_not_found'] = $this->language->get('text_not_found');
		$data['heading_title_latest'] = $this->language->get('heading_title_latest');
		$data['text_read_more'] = $this->language->get('text_read_more');
		
		$data['date_added_status'] = $this->config->get('blogsetting_date_added');
		if (empty($data['date_added_status'])) {
		$data['date_added_status'] = 1;
		}
		
		$data['comments_count_status'] = $this->config->get('blogsetting_comments_count');
		if (empty($data['comments_count_status'])) {
		$data['comments_count_status'] = 1;
		}
		
		$data['page_view_status'] = $this->config->get('blogsetting_page_view');
		if (empty($data['page_view_status'])) {
		$data['page_view_status'] = 0;
		}
		
		$data['author_status'] = $this->config->get('blogsetting_author');
		if (empty($data['blogsetting_author'])) {
		$data['author_status'] = 1;
		}
		

		$data['module'] = $module++;
		
		if ($this->config->get('theme_default_directory') == 'basel')
		return $this->load->view('extension/module/blog_latest', $data);
	}
}