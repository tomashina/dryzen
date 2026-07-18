<?php
class ModelExtensionBaselDemoStores8Installer extends Model {

	public function demoSetup() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->model('extension/basel/demo_stores_base');
		
		//--------------- GLOBAL ITEMS ----------------
		
		// Add Sample Categories
		$this->model_extension_basel_demo_stores_base->addSampleCategories();
		
		// Add Sample Products
		$this->model_extension_basel_demo_stores_base->addSampleProducts();
		
		// Add Sample Blog Posts
		$this->model_extension_basel_demo_stores_base->addSampleBlogs();
		
		// Add Sample Product Groups
		$this->model_extension_basel_demo_stores_base->addSampleProductGroups();
		
		// Add Sample Testimonials
		$this->model_extension_basel_demo_stores_base->addSampleTestimonials();
		
		// Add Sample Menu
		$this->model_extension_basel_demo_stores_base->addSampleMenuItems();
		
		// Add Sample Banners
		$this->model_extension_basel_demo_stores_base->addSampleBanners();
		
		// DEMO STORE SPECIFIC ITEMS ----------
		$this->alterExtensionTable();
		
		$this->addSampleModules();
		
		$this->addSampleLayouts();
		
		$this->addSampleLayoutModules();
		
		$this->addSampleLayoutRoutes();
		
		$this->addSampleSettings();
		
		$this->cache->delete('megamenu');
		$this->cache->delete('language');
		$this->cache->delete('basel_instagram');
		$this->cache->delete('basel_styles_cache');
		$this->cache->delete('basel_fonts_cache');
		$this->cache->delete('basel_mandatory_css');

		$this->session->data['success'] = 'Demo store was successfully imported.';
		
		$this->response->redirect($this->url->link('extension/basel/basel', $token_prefix . '=' . $this->session->data[$token_prefix], true));
		
	}
	
	/*--------------------------------------------
	------- ADD ENTRIES TO EXTENSION TABLE -------
	--------------------------------------------*/
	public function alterExtensionTable() {
	$query = $this->db->query("DELETE FROM `".DB_PREFIX."extension` WHERE type='module' AND code != 'digitalElephantFilter' AND code != 'basel_installer'");
	
	$oc_extension = array(
	  array('extension_id' => '181','type' => 'module','code' => 'category'),
	  array('extension_id' => '182','type' => 'module','code' => 'basel_instagram'),
	  array('extension_id' => '180','type' => 'module','code' => 'account'),
	  array('extension_id' => '179','type' => 'module','code' => 'basel_content'),
	  array('extension_id' => '177','type' => 'module','code' => 'basel_megamenu'),
	  array('extension_id' => '176','type' => 'module','code' => 'basel_products'),
	  array('extension_id' => '175','type' => 'module','code' => 'blog_latest'),
	  array('extension_id' => '174','type' => 'module','code' => 'basel_carousel')
	);
	
	foreach ($oc_extension as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET "
				. "type = '" . $result['type'] . "', "
                . "code = '" . $result['code'] . "'");
    	}

	}
	
	/*--------------------------------------------
	---------------- ADD MODULES -----------------
	--------------------------------------------*/
	public function addSampleModules() {
	
	$current_banner_id = $this->model_extension_basel_demo_stores_base->getBannerId();
	
	$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '38','name' => '3 x Banner Home Page','code' => 'basel_content','setting' => '{"save":"stay","name":"3 x Banner Home Page","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"40","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo8\\/banner\\/banner1.jpg","data5":"","data7":"vertical-middle text-left","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#d9be39;&quot;&gt;WOMAN&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;NEW ARRIVALS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#d9be39;&quot;&gt;WOMAN&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;NEW ARRIVALS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#d9be39;&quot;&gt;WOMAN&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;NEW ARRIVALS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo8\\/banner\\/banner2.jpg","data5":"","data7":"vertical-middle text-center","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#2389e0;&quot;&gt;FOR STYLED&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;YOUNG MODEL&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#2389e0;&quot;&gt;FOR STYLED&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;YOUNG MODEL&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#2389e0;&quot;&gt;FOR STYLED&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;YOUNG MODEL&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"img","data2":"catalog\\/basel-demo\\/demo8\\/banner\\/banner3.jpg","data5":"","data7":"vertical-middle text-right","data1":{"1":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#d93939;&quot;&gt;COLLECTION&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;MENS CASUAL&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;","3":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#d93939;&quot;&gt;COLLECTION&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;MENS CASUAL&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;","2":"&lt;span class=&quot;hover-zoom&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;hover-darken&quot;&gt;&lt;\\/span&gt;\\r\\n&lt;span class=&quot;label&quot; style=&quot;background:#d93939;&quot;&gt;COLLECTION&lt;\\/span&gt;\\r\\n&lt;h3 style=&quot;margin-bottom:9px&quot;&gt;&lt;b&gt;MENS CASUAL&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p  style=&quot;margin-bottom:8px&quot;&gt;Lorem ipsum dolor &lt;br&gt;sit amet consectetur.&lt;\\/p&gt;\\r\\n&lt;a class=&quot;underline&quot; href=&quot;#&quot;&gt;Read More&lt;\\/a&gt;"}}}}'),
  array('module_id' => '39','name' => '4 x Selling Points','code' => 'basel_content','setting' => '{"save":"stay","name":"4 x Selling Points","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"35","ml":"0","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"0","css":"","nm":"0","eh":"0"},"columns":{"1":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#d93939;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;20,000+ SATISFIED&lt;br&gt; CUSTOMERS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#d93939;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;20,000+ SATISFIED&lt;br&gt; CUSTOMERS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#d93939;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-thumbs-up&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;20,000+ SATISFIED&lt;br&gt; CUSTOMERS&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#2b2b2b;&quot;&gt;\\r\\n&lt;span class=&quot;icon lighten&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;FREE WORLDWIDE &lt;br&gt;SHIPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#2b2b2b;&quot;&gt;\\r\\n&lt;span class=&quot;icon lighten&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;FREE WORLDWIDE &lt;br&gt;SHIPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#2b2b2b;&quot;&gt;\\r\\n&lt;span class=&quot;icon lighten&quot;&gt;&lt;i class=&quot;fa fa-rocket&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;FREE WORLDWIDE &lt;br&gt;SHIPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#d93939;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-undo&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;30 DAYS &lt;br&gt;RETURN POLICY&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#d93939;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-undo&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;30 DAYS &lt;br&gt;RETURN POLICY&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#d93939;&quot;&gt;\\r\\n&lt;span class=&quot;icon&quot;&gt;&lt;i class=&quot;fa fa-undo&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;30 DAYS &lt;br&gt;RETURN POLICY&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"4":{"w":"custom","w_sm":"col-xs-12","w_md":"col-sm-6","w_lg":"col-md-3","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#2b2b2b;&quot;&gt;\\r\\n&lt;span class=&quot;icon lighten&quot;&gt;&lt;i class=&quot;fa fa-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;TAX FREE &lt;br&gt;SHOPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#2b2b2b;&quot;&gt;\\r\\n&lt;span class=&quot;icon lighten&quot;&gt;&lt;i class=&quot;fa fa-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;TAX FREE &lt;br&gt;SHOPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;promo-style-4&quot; style=&quot;background:#2b2b2b;&quot;&gt;\\r\\n&lt;span class=&quot;icon lighten&quot;&gt;&lt;i class=&quot;fa fa-truck&quot;&gt;&lt;\\/i&gt;&lt;\\/span&gt;\\r\\n&lt;h3&gt;&lt;b&gt;TAX FREE &lt;br&gt;SHOPPING&lt;\\/b&gt;&lt;\\/h3&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;underline&quot;&gt;Read More&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Product Tabs','code' => 'basel_products','setting' => '{"name":"Home Page Product Tabs","status":"1","contrast":"0","use_title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1","2","3"]},"tabstyle":"nav-tabs-sm text-center","limit":"8","image_width":"262","image_height":"334","columns":"4","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"35px"}'),
  array('module_id' => '50','name' => 'Jumbotron Promo Message','code' => 'basel_content','setting' => '{"save":"stay","name":"Jumbotron Promo Message","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"1","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"6","bg_pos":"center top","bg_repeat":"repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/demo8\\/big-centered-baner.jpg","c_setting":{"fw":"0","block_css":"1","css":"border:5px solid #ffffff;\\r\\npadding:35px 20px 0px;\\r\\nmargin:115px 5%;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;light&quot;&gt;\r\n&lt;h2 class=&quot;contrast-font&quot; style=&quot;font-size:42px;&quot;&gt;\r\n-50% HOT SUMMER COLLECTION SALE\r\n&lt;\/h2&gt;\r\n&lt;p style=&quot;font-size:16px;margin-bottom:20px;&quot;&gt;\r\nCommodo pulvinar amet parturient mus sociis dis parturient conubia suspendisse nullamparturient&lt;br&gt;\r\nmus sociis dis parturient conubia suspendisse nullam\r\n&lt;\/p&gt;\r\n&lt;a href=&quot;#&quot; class=&quot;btn btn-tiny btn-light&quot;&gt;Read more&lt;\/a&gt;\r\n&lt;\/div&gt;\r\n","2":"&lt;div class=&quot;light&quot;&gt;\r\n&lt;h2 class=&quot;contrast-font&quot; style=&quot;font-size:42px;&quot;&gt;\r\n-50% HOT SUMMER COLLECTION SALE\r\n&lt;\/h2&gt;\r\n&lt;p style=&quot;font-size:16px;margin-bottom:20px;&quot;&gt;\r\nCommodo pulvinar amet parturient mus sociis dis parturient conubia suspendisse nullamparturient&lt;br&gt;\r\nmus sociis dis parturient conubia suspendisse nullam\r\n&lt;\/p&gt;\r\n&lt;a href=&quot;#&quot; class=&quot;btn btn-tiny btn-light&quot;&gt;Read more&lt;\/a&gt;\r\n&lt;\/div&gt;\r\n","3":"&lt;div class=&quot;light&quot;&gt;\r\n&lt;h2 class=&quot;contrast-font&quot; style=&quot;font-size:42px;&quot;&gt;\r\n-50% HOT SUMMER COLLECTION SALE\r\n&lt;\/h2&gt;\r\n&lt;p style=&quot;font-size:16px;margin-bottom:20px;&quot;&gt;\r\nCommodo pulvinar amet parturient mus sociis dis parturient conubia suspendisse nullamparturient&lt;br&gt;\r\nmus sociis dis parturient conubia suspendisse nullam\r\n&lt;\/p&gt;\r\n&lt;a href=&quot;#&quot; class=&quot;btn btn-tiny btn-light&quot;&gt;Read more&lt;\/a&gt;\r\n&lt;\/div&gt;\r\n"}}}}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"FROM OUR BLOG","3":"FROM OUR BLOG","2":"FROM OUR BLOG"},"title_b":{"1":"","3":"","2":""},"characters":"120","use_thumb":"1","width":"360","height":"220","limit":"3","columns":"3","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"0","use_button":"0","use_margin":"1","margin":"30px"}'),
  array('module_id' => '51','name' => 'Newsletter Form With Social Icons','code' => 'basel_content','setting' => '{"save":"0","name":"Newsletter Form With Social Icons","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/envelope.png","c_setting":{"fw":"0","block_css":"1","css":"border:10px solid #f2f2f2;\\r\\npadding:57px 20px 25px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;bordered-signup wide-signup dark&quot;&gt;\\r\\n&lt;h2&gt;Our Newsletter&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;x-separator&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p&gt;It only takes a second to be the first to find out about our latest&lt;br&gt;\\r\\nnews and promotions\\u2026&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n&lt;\\/div&gt;\\r\\n&lt;div class=&quot;social-icons round&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon facebook&quot;&gt;&lt;i class=&quot;fa fa-facebook&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon twitter&quot;&gt;&lt;i class=&quot;fa fa-twitter&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon google&quot;&gt;&lt;i class=&quot;icon-google-plus&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon pinterest&quot;&gt;&lt;i class=&quot;fa fa-pinterest&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon instagram&quot;&gt;&lt;i class=&quot;fa fa-instagram&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon vk&quot;&gt;&lt;i class=&quot;fa fa-vk&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;bordered-signup wide-signup dark&quot;&gt;\\r\\n&lt;h2&gt;Our Newsletter&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;x-separator&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p&gt;It only takes a second to be the first to find out about our latest&lt;br&gt;\\r\\nnews and promotions\\u2026&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n[unsubscribe_btn]\\r\\n&lt;\\/div&gt;\\r\\n&lt;div class=&quot;social-icons round&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon facebook&quot;&gt;&lt;i class=&quot;fa fa-facebook&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon twitter&quot;&gt;&lt;i class=&quot;fa fa-twitter&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon google&quot;&gt;&lt;i class=&quot;icon-google-plus&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon pinterest&quot;&gt;&lt;i class=&quot;fa fa-pinterest&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon instagram&quot;&gt;&lt;i class=&quot;fa fa-instagram&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;bordered-signup wide-signup dark&quot;&gt;\\r\\n&lt;h2&gt;Our Newsletter&lt;\\/h2&gt;\\r\\n&lt;p class=&quot;x-separator&quot;&gt;&lt;i class=&quot;icon-line-cross&quot;&gt;&lt;\\/i&gt;&lt;\\/p&gt;\\r\\n&lt;p&gt;It only takes a second to be the first to find out about our latest&lt;br&gt;\\r\\nnews and promotions\\u2026&lt;\\/p&gt;\\r\\n&lt;div class=&quot;dark_field&quot;&gt;\\r\\n[subscribe_field] \\r\\n[unsubscribe_btn]\\r\\n&lt;\\/div&gt;\\r\\n&lt;div class=&quot;social-icons round&quot;&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon facebook&quot;&gt;&lt;i class=&quot;fa fa-facebook&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon twitter&quot;&gt;&lt;i class=&quot;fa fa-twitter&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon google&quot;&gt;&lt;i class=&quot;icon-google-plus&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon pinterest&quot;&gt;&lt;i class=&quot;fa fa-pinterest&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;icon instagram&quot;&gt;&lt;i class=&quot;fa fa-instagram&quot;&gt;&lt;\\/i&gt;&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '48','name' => 'Instagram Feed (Column Style)','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed (Column Style)","status":"1","full_width":"0","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"@Instagram","3":"@Instagram","2":"@Instagram"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"9","resolution":"0","columns":"3","columns_md":"3","columns_sm":"3","padding":"5","use_margin":"0","margin":"60px"}'),
  array('module_id' => '52','name' => 'Brands Carousel','code' => 'basel_carousel','setting' => '{"name":"Brands Carousel","status":"1","contrast":"0","use_title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"banner_id":"'.$current_banner_id.'","image_width":"200","image_height":"50","columns":"6","rows":"1","carousel_a":"0","carousel_b":"0","use_margin":"1","margin":"0px"}')
);

    $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "module`");

    foreach ($oc_module as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET "
                . "name = '" . $result['name'] . "', "
				. "module_id = '" . $result['module_id'] . "', "
                . "code = '" . $result['code'] . "', "
                . "setting = '" . addslashes($result['setting']) . "'");
    	}
	}
	
	/*--------------------------------------------
	----------------- ADD LAYOUTS ----------------
	--------------------------------------------*/
	public function addSampleLayouts() {
	$this->db->query("TRUNCATE TABLE `".DB_PREFIX."layout`");
	
	$oc_layout = array(
  array('layout_id' => '1','name' => 'Home'),
  array('layout_id' => '2','name' => 'Product'),
  array('layout_id' => '3','name' => 'Category'),
  array('layout_id' => '4','name' => 'Default'),
  array('layout_id' => '14','name' => 'Blog'),
  array('layout_id' => '6','name' => 'Account'),
  array('layout_id' => '7','name' => 'Checkout'),
  array('layout_id' => '8','name' => 'Contact'),
  array('layout_id' => '9','name' => 'Sitemap'),
  array('layout_id' => '10','name' => 'Affiliate'),
  array('layout_id' => '11','name' => 'Information'),
  array('layout_id' => '12','name' => 'Compare'),
  array('layout_id' => '15','name' => 'Login Pages')
);
	
	foreach ($oc_layout as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "layout` SET "
				. "layout_id = '" . $result['layout_id'] . "', "
                . "name = '" . $result['name'] . "'");
    	}
	}
	
	/*--------------------------------------------
	-------------- ADD LAYOUT MODULES ------------
	--------------------------------------------*/
	public function addSampleLayoutModules() {
	$this->db->query("TRUNCATE TABLE `".DB_PREFIX."layout_module`");
	
	$oc_layout_module = array(
  array('layout_module_id' => '204','layout_id' => '14','code' => 'category','position' => 'column_right','sort_order' => '1'),
  array('layout_module_id' => '69','layout_id' => '10','code' => 'affiliate','position' => 'column_right','sort_order' => '1'),
  array('layout_module_id' => '68','layout_id' => '6','code' => 'account','position' => 'column_right','sort_order' => '1'),
  array('layout_module_id' => '287','layout_id' => '1','code' => 'basel_content.39','position' => 'bottom','sort_order' => '6'),
  array('layout_module_id' => '251','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
  array('layout_module_id' => '286','layout_id' => '1','code' => 'basel_content.51','position' => 'bottom','sort_order' => '5'),
  array('layout_module_id' => '285','layout_id' => '1','code' => 'blog_latest.42','position' => 'bottom','sort_order' => '4'),
  array('layout_module_id' => '284','layout_id' => '1','code' => 'basel_content.50','position' => 'bottom','sort_order' => '3'),
  array('layout_module_id' => '250','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
  array('layout_module_id' => '283','layout_id' => '1','code' => 'basel_products.41','position' => 'bottom','sort_order' => '2'),
  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '249','layout_id' => '3','code' => 'basel_megamenu.36','position' => 'column_left','sort_order' => '1'),
  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
  array('layout_module_id' => '281','layout_id' => '1','code' => 'basel_megamenu.36','position' => 'column_left','sort_order' => '1'),
  array('layout_module_id' => '282','layout_id' => '1','code' => 'basel_content.38','position' => 'content_top','sort_order' => '0'),
  array('layout_module_id' => '253','layout_id' => '2','code' => 'basel_megamenu.36','position' => 'column_left','sort_order' => '0'),
  array('layout_module_id' => '254','layout_id' => '2','code' => 'basel_products.46','position' => 'column_left','sort_order' => '0'),
  array('layout_module_id' => '288','layout_id' => '1','code' => 'basel_carousel.52','position' => 'bottom','sort_order' => '7')
);
		
	foreach ($oc_layout_module as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_module` SET "
				. "layout_module_id = '" . $result['layout_module_id'] . "', "
				. "layout_id = '" . $result['layout_id'] . "', "
				. "code = '" . $result['code'] . "', "
				. "position = '" . $result['position'] . "', "
                . "sort_order = '" . $result['sort_order'] . "'");
    	}
	}
	
	/*--------------------------------------------
	-------------- ADD LAYOUT ROUTES -------------
	--------------------------------------------*/
	public function addSampleLayoutRoutes() {
	$this->db->query("TRUNCATE TABLE `".DB_PREFIX."layout_route`");
	
	$oc_layout_route = array(
  array('layout_route_id' => '38','layout_id' => '6','store_id' => '0','route' => 'account/%'),
  array('layout_route_id' => '17','layout_id' => '10','store_id' => '0','route' => 'affiliate/%'),
  array('layout_route_id' => '92','layout_id' => '3','store_id' => '0','route' => 'product/category'),
  array('layout_route_id' => '102','layout_id' => '1','store_id' => '0','route' => 'common/home'),
  array('layout_route_id' => '95','layout_id' => '2','store_id' => '0','route' => 'product/product'),
  array('layout_route_id' => '71','layout_id' => '11','store_id' => '0','route' => 'information/information'),
  array('layout_route_id' => '23','layout_id' => '7','store_id' => '0','route' => 'checkout/%'),
  array('layout_route_id' => '31','layout_id' => '8','store_id' => '0','route' => 'information/contact'),
  array('layout_route_id' => '32','layout_id' => '9','store_id' => '0','route' => 'information/sitemap'),
  array('layout_route_id' => '34','layout_id' => '4','store_id' => '0','route' => ''),
  array('layout_route_id' => '81','layout_id' => '14','store_id' => '0','route' => 'extension/blog/%'),
  array('layout_route_id' => '52','layout_id' => '12','store_id' => '0','route' => 'product/compare'),
  array('layout_route_id' => '91','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer'),
  array('layout_route_id' => '98','layout_id' => '3','store_id' => '0','route' => 'product/manufacturer/info'),
  array('layout_route_id' => '90','layout_id' => '3','store_id' => '0','route' => 'product/search'),
  array('layout_route_id' => '93','layout_id' => '3','store_id' => '0','route' => 'product/special'),
  array('layout_route_id' => '96','layout_id' => '15','store_id' => '0','route' => 'account/login'),
  array('layout_route_id' => '97','layout_id' => '15','store_id' => '0','route' => 'affiliate/login')
);
	
	foreach ($oc_layout_route as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_route` SET "
				. "layout_route_id = '" . $result['layout_route_id'] . "', "
				. "layout_id = '" . $result['layout_id'] . "', "
				. "store_id = '" . $result['store_id'] . "', "
                . "route = '" . $result['route'] . "'");
    	}
	}
	
	/*--------------------------------------------
	---------------- ADD SETTINGS ----------------
	--------------------------------------------*/
	public function addSampleSettings() {
	
	$this->db->query("DELETE FROM `".DB_PREFIX."setting` WHERE `code` = 'basel'");
		
	$oc_setting = array(
array('setting_id' => '4529','store_id' => '0','code' => 'basel','key' => 'top_line_height','value' => '41','serialized' => '0'),
  array('setting_id' => '4445','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '4444','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '4438','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '4615','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '4614','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4441','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '1386','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '1385','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '4432','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '4433','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '4434','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '4478','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '','serialized' => '0'),
  array('setting_id' => '4477','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4437','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '4431','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '4430','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '4429','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '4428','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4426','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '4427','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '4424','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '4425','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '16px','serialized' => '0'),
  array('setting_id' => '4423','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4421','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '4422','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '4420','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '4419','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '4418','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4417','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '4416','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '4415','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '4414','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '4413','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4412','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '4411','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '4410','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '26px','serialized' => '0'),
  array('setting_id' => '4409','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '600','serialized' => '0'),
  array('setting_id' => '4408','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4407','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '28px','serialized' => '0'),
  array('setting_id' => '4406','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '4404','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '4405','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4402','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '4403','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '4401','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '4400','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '4399','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '4397','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '4398','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4392','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4393','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => 'Arial, Helvetica Neue, Helvetica, sans-serif','serialized' => '0'),
  array('setting_id' => '4613','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4395','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#666666','serialized' => '0'),
  array('setting_id' => '4396','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4391','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '4389','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4390','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#d93939','serialized' => '0'),
  array('setting_id' => '4388','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '4387','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '4386','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4385','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#d93939','serialized' => '0'),
  array('setting_id' => '4384','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#d93939','serialized' => '0'),
  array('setting_id' => '4383','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#fbbc34','serialized' => '0'),
  array('setting_id' => '4382','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '4381','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4380','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#d93939','serialized' => '0'),
  array('setting_id' => '4379','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#d93939','serialized' => '0'),
  array('setting_id' => '4378','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4377','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '4370','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#1daaa3','serialized' => '0'),
  array('setting_id' => '4376','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '4375','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '4374','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '4612','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '4372','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#fafafa','serialized' => '0'),
  array('setting_id' => '4371','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '4369','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '4368','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '4367','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '4366','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#d93939','serialized' => '0'),
  array('setting_id' => '4363','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4364','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '4365','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '4361','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#d93939','serialized' => '0'),
  array('setting_id' => '4362','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4360','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '4359','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '4358','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '4357','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '4356','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '4355','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '4351','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4610','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '0','serialized' => '0'),
  array('setting_id' => '4353','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '4611','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '4608','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '4350','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '4609','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '0','serialized' => '0'),
  array('setting_id' => '4347','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-basket','serialized' => '0'),
  array('setting_id' => '4607','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '4345','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '4344','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '4342','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '4343','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '4338','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '4606','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '0','serialized' => '0'),
  array('setting_id' => '4340','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '4341','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '4337','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4605','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '4604','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4602','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '4334','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '4332','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Lora:400,400i","name":"\'Lora\', serif"},"2":{"import":"Montserrat:400,600,700","name":"\'Montserrat\', sans-serif"},"3":{"import":"Karla:400,400i,700","name":"\'Karla\', sans-serif"}}','serialized' => '1'),
  array('setting_id' => '4603','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '4601','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '4600','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4328','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '4327','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '4599','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '1269','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '4323','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4598','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '4596','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '4597','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '4321','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4319','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '4318','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4595','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '4317','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4315','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '4314','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '1','serialized' => '0'),
  array('setting_id' => '4593','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '4594','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '4592','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '4310','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '4309','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '4591','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '4590','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '4585','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '0','serialized' => '0'),
  array('setting_id' => '4584','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4587','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4301','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '4303','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '4588','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4589','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '4299','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4298','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '4583','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '4296','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-bottom','serialized' => '0'),
  array('setting_id' => '4295','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '4294','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4293','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '4291','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '4292','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '4290','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '4289','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '4288','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '4287','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '4286','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '4582','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '4284','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '36','serialized' => '0'),
  array('setting_id' => '4283','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '32','serialized' => '0'),
  array('setting_id' => '4282','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '4265','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '4266','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '4267','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '4268','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0'),
  array('setting_id' => '4269','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '4581','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '195','serialized' => '0'),
  array('setting_id' => '4271','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '4272','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-center','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '64','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '64','serialized' => '0'),  array('setting_id' => '4274','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '4275','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '104','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '4276','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'boxed','serialized' => '0'),
  array('setting_id' => '4277','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '1','serialized' => '0'),
  array('setting_id' => '4278','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header2','serialized' => '0'),
  array('setting_id' => '4279','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '4586','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => '','serialized' => '0'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '4281','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1')
);

	
	foreach ($oc_setting as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET "
				. "code = '" . $result['code'] . "', "
				. "`key` = '" . $result['key'] . "', "
				. "value = '" . addslashes($result['value']) . "', "
                . "serialized = '" . $result['serialized'] . "'");
    	}
	}

	
	
		
}