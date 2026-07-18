<?php 
class ControllerExtensionBlogCategory extends Controller {
	
	public function index() {  
	
	$this->language->load('blog/blog');
	
	$this->load->model('extension/blog/blog_category');
	
	$this->load->model('extension/blog/blog');

	$limit = $this->config->get('blogsetting_blogs_per_page');
	$img_width = $this->config->get('blogsetting_thumbs_w');
	$img_height = $this->config->get('blogsetting_thumbs_h');
	$data['date_added_status'] = $this->config->get('blogsetting_date_added');
	$data['comments_count_status'] = $this->config->get('blogsetting_comments_count');
	$data['page_view_status'] = $this->config->get('blogsetting_page_view');
	$data['author_status'] = $this->config->get('blogsetting_author');
	$data['list_columns'] = $this->config->get('blogsetting_layout');	
	
	$data['breadcrumbs'] = array();

	$data['breadcrumbs'][] = array(
		'text'      => $this->language->get('text_home'),
		'href'      => $this->url->link('common/home')
	);

	$data['breadcrumbs'][] = array(
		'text'      => $this->language->get('text_blog'),
		'href'      => $this->url->link('extension/blog/home')
	);	
		
				
	if (isset($this->request->get['blogpath'])) {
		$path = '';
		
		$parts = explode('_', $this->request->get['blogpath']);
		
		foreach ($parts as $path_id) {
			$blog_category_info = $this->model_extension_blog_blog_category->getBlogCategory($path_id);
				
			if ($blog_category_info) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

	       		$data['breadcrumbs'] [] = array(
   	    			'href'      => $this->url->link('extension/blog/category', 'blogpath=' . $path),
    	   			'text'      => $blog_category_info['name']
        		);
			}
		}
		
		$blog_category_id = array_pop($parts);
			} else {
		$blog_category_id = 0;
			}
		
		$blog_category_info = $this->model_extension_blog_blog_category->getBlogCategory($blog_category_id);
		
		if ($blog_category_info) {
			
			if ($blog_category_info['page_title']) {
			$this->document->setTitle($blog_category_info['page_title']);
			} else {
			$this->document->setTitle($blog_category_info['name']);
			}
			
			$this->document->setDescription($blog_category_info['meta_description']);
			$this->document->setKeywords($blog_category_info['meta_keywords']);
			
			$data['heading_title'] = $blog_category_info['name'];
			
			if ($blog_category_info['description'] == '&lt;p&gt;&lt;br&gt;&lt;/p&gt;') {
			$data['blog_category_description'] = '';
			} else {
			$data['blog_category_description'] = html_entity_decode($blog_category_info['description']);
			}
			
      		$data['text_posted_on'] = $this->language->get('text_posted_on');
			$data['text_read'] = $this->language->get('text_read');
			$data['text_posted_by'] = $this->language->get('text_posted_by');
			$data['text_comments'] = $this->language->get('text_comments');
			$data['text_posted_on'] = $this->language->get('text_posted_on');
			
      		$data['text_no_blog_posts'] = $this->language->get('text_no_blog_posts');
         	$data['text_read_more'] = $this->language->get('text_read_more');

			$blog_description = $blog_category_info['description'];
			if (empty($blog_description) || ($blog_description == '&lt;p&gt;&lt;br&gt;&lt;/p&gt;')) {
			$data['description'] = false;
			} else {
			$data['description'] = html_entity_decode($blog_category_info['description'], ENT_QUOTES, 'UTF-8');
			}
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			} 
			
			$pagefix = ($page - 1) * $limit;
			
			if ($pagefix < 1) { $pagefix = 0;}
			
			$this->load->model('extension/blog/blog');
			$this->load->model('tool/image');
				
			$data['blogs'] = array();
			
			$blogs = array(
				'start' => ($page - 1) * $limit,
				'limit' => $limit,
			);
			        		
			$results = $this->model_extension_blog_blog->getBlogsByBlogCategoryId($blog_category_id, $pagefix, $limit);
						
			foreach ($results as $result) {
			
			if ($result['tags']) {
				$tags = explode(',', $result['tags']);
			} else {
				$tags = false;
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
      		
			$data['blogs'][] = array(
			'count_read' 		=> $result['count_read'],
			'comment_total' 	=> $this->model_extension_blog_blog->getTotalCommentsByBlogId($result['blog_id']),
			'blog_id' 			=> $result['blog_id'],
			'tags' 				=> $tags,
			'title'     		=> $result['title'],
			'short_description' => html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8'),
			'date_added_day' 	=> date("d",strtotime($result['date_added'])),
			'date_added_month' 	=> $date_added_month,
			'author' 			=> $result['author'],
			'image'   			=> $this->model_tool_image->resize($result['image'], $img_width, $img_height),
			'href' 				=> $this->url->link('extension/blog/blog', 'blog_id=' . $result['blog_id'])
			);
    		}
					
			$blog_total = $this->model_extension_blog_blog->getTotalBlogsByBlogCategoryId($blog_category_id);
			
			$pagination = new Pagination();
			$pagination->total = $blog_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('blogsetting_blogs_per_page');
			if (empty($pagination->limit)) {$pagination->limit = 5;}
			$pagination->text = $this->language->get('text_pagination');
			$pagination->url = $this->url->link('extension/blog/category', 'blogpath=' . $this->request->get['blogpath'] . '&page={page}');
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($blog_total) ? ($pagefix) + 1 : 0, ((($page - 1) * $limit) > ($blog_total - $limit)) ? $blog_total : (($pagefix) + $limit), $blog_total, ceil($blog_total / $limit));
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			$this->response->setOutput($this->load->view('blog/blog_category', $data));

		} else {
		
			$this->language->load('error/not_found');
			
			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}	
			
			if (isset($this->request->get['blogpath'])) {	
				$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/blog/category', 'blogpath=' . $path)
			);
			}
			
			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
      	
      		$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
	
}