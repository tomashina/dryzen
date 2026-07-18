<?php
class ModelExtensionBaselDemoStores6Installer extends Model {

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
	  array('extension_id' => '421','type' => 'module','code' => 'basel_categories'),
	  array('extension_id' => '420','type' => 'module','code' => 'basel_instagram'),
	  array('extension_id' => '419','type' => 'module','code' => 'category'),
	  array('extension_id' => '418','type' => 'module','code' => 'account'),
	  array('extension_id' => '417','type' => 'module','code' => 'basel_content'),
	  array('extension_id' => '416','type' => 'module','code' => 'basel_layerslider'),
	  array('extension_id' => '415','type' => 'module','code' => 'basel_megamenu'),
	  array('extension_id' => '414','type' => 'module','code' => 'basel_products'),
	  array('extension_id' => '413','type' => 'module','code' => 'blog_latest')
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
	
	$get_category_id = $this->model_extension_basel_demo_stores_base->getCategoryId1();
	
	$oc_module = array(
  array('module_id' => '32','name' => 'Main Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"32","import_module":"0","name":"Main Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '54','name' => 'Main Menu (Divided - Right)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"54","import_module":"32","name":"Main Menu (Divided - Right)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '55','name' => 'Main Menu (Divided - Left)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"55","import_module":"32","name":"Main Menu (Divided - Left)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '35','name' => 'Categories Menu','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"35","import_module":"0","name":"Categories Menu","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '36','name' => 'Categories Menu (With icons)','code' => 'basel_megamenu','setting' => '{"button-save":"","moduleid":"36","import_module":"35","name":"Categories Menu (With icons)","status":"1","sort_order":0,"orientation":0,"home_text":0,"home_item":0,"icon_font":"","class_menu":"","show_itemver":10,"head_name":[],"disp_mobile_module":[]}'),
  array('module_id' => '37','name' => 'Home Page Slider','code' => 'basel_layerslider','setting' => '{"save":"stay","status":"1","name":"Home Page Slider","lang":"1","width":"1140","height":"580","minheight":"250","fullwidth":"1","margin_bottom":"60px","slide_transition":"fade","speed":"10","loop":"0","nav_buttons":"circle-arrows","nav_bullets":"0","nav_timer_bar":"0","sections":{"1":{"sort_order":"1","duration":"10","slide_kenburn":"0","bg_color":"#f7f7f7","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo6\\/slideshow\\/woman1.jpg","3":"catalog\\/basel-demo\\/demo6\\/slideshow\\/woman1.jpg","2":"catalog\\/basel-demo\\/demo6\\/slideshow\\/woman1.jpg"},"left":{"1":"524","3":"915","2":"915"},"top":{"1":"291","3":"215","2":"215"},"minheight":"250","transitionin":"right(short)","easingin":"ease","durationin":"1200","transitionout":"right(short)","easingout":"ease","durationout":"1200","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"DISCOVER MORE SKINCARE","3":"DISCOVER MORE SKINCARE","2":"DISCOVER MORE SKINCARE"},"left":{"1":"150","3":"150","2":"150"},"top":{"1":"90","3":"90","2":"90"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"18px","color":"#999999","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"letter-spacing:2px;","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"top(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"2","p_index":"0","start":"1500","end":"9500"},{"type":"text","description":{"1":"Pink and gold lipsticks for every &lt;br&gt;skintone new trending in 2017 season","3":"Pink and gold lipsticks for every &lt;br&gt;skintone new trending in 2017 season","2":"Pink and gold lipsticks for every &lt;br&gt;skintone new trending in 2017 season"},"left":{"1":"-13","3":"-13","2":"-13"},"top":{"1":"158","3":"158","2":"158"},"font":"\'Sumana\', serif","fontweight":"400","fontsize":"38px","color":"#222222","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"line-height:1.25;\\r\\nletter-spacing:-0.75px;\\r\\ntext-align:center;","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"top(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"3","p_index":"0","start":"1000","end":"9500"},{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo6\\/slideshow\\/makeup.jpg","3":"catalog\\/basel-demo\\/demo6\\/slideshow\\/makeup.jpg","2":"catalog\\/basel-demo\\/demo6\\/slideshow\\/makeup.jpg"},"left":{"1":"110","3":"110","2":"110"},"top":{"1":"354","3":"354","2":"354"},"minheight":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"4","p_index":"0","start":"1000","end":"9500"},{"type":"button","description":{"1":"&lt;b&gt;Brands&lt;\\/b&gt;","3":"&lt;b&gt;Brands&lt;\\/b&gt;","2":"&lt;b&gt;Brands&lt;\\/b&gt;"},"left":{"1":"146","3":"146","2":"146"},"top":{"1":"500","3":"500","2":"500"},"button_class":"btn btn-lg btn-primary","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"5","p_index":"0","start":"1500","end":"9500"},{"type":"button","description":{"1":"&lt;b&gt;Products&lt;\\/b&gt;","3":"&lt;b&gt;Products&lt;\\/b&gt;","2":"&lt;b&gt;Products&lt;\\/b&gt;"},"left":{"1":"290","3":"290","2":"290"},"top":{"1":"500","3":"500","2":"500"},"button_class":"btn btn-lg btn-primary","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"6","p_index":"0","start":"1500","end":"9500"}]},"2":{"sort_order":"2","duration":"10","slide_kenburn":"0","bg_color":"#f7f7f7","link":"","link_new_window":"0","thumb_image":"","groups":[{"type":"image","image":{"1":"catalog\\/basel-demo\\/demo6\\/slideshow\\/woman2.jpg","3":"catalog\\/basel-demo\\/demo6\\/slideshow\\/woman2.jpg","2":"catalog\\/basel-demo\\/demo6\\/slideshow\\/woman2.jpg"},"left":{"1":"-94","3":"-94","2":"-94"},"top":{"1":"288","3":"288","2":"288"},"minheight":"250","transitionin":"left(short)","easingin":"ease","durationin":"1200","transitionout":"left(short)","easingout":"ease","durationout":"1200","sort_order":"1","p_index":"0","start":"500","end":"10000"},{"type":"text","description":{"1":"DISCOVER MORE SKINCARE","3":"DISCOVER MORE SKINCARE","2":"DISCOVER MORE SKINCARE"},"left":{"1":"689","3":"689","2":"689"},"top":{"1":"118","3":"118","2":"118"},"font":"\'Karla\', sans-serif","fontweight":"700","fontsize":"18px","color":"#999999","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"letter-spacing:2px;","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"top(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"2","p_index":"0","start":"1500","end":"9500"},{"type":"text","description":{"1":"Pink and gold lipsticks for every &lt;br&gt;skintone new trending in 2017 season","3":"Pink and gold lipsticks for every &lt;br&gt;skintone new trending in 2017 season","2":"Pink and gold lipsticks for every &lt;br&gt;skintone new trending in 2017 season"},"left":{"1":"520","3":"520","2":"520"},"top":{"1":"198","3":"198","2":"198"},"font":"\'Sumana\', serif","fontweight":"400","fontsize":"38px","color":"#222222","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"line-height:1.25;\\r\\nletter-spacing:-0.75px;\\r\\ntext-align:center;","transitionin":"top(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"top(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"3","p_index":"0","start":"1000","end":"9500"},{"type":"text","description":{"1":"A gravida in eu est parturient dolor ad malesuada in sociosqu &lt;br&gt;condimentum dapibus lorem semper fermentum posuere eu. ","3":"Layer Caption","2":"Layer Caption"},"left":{"1":"604","3":"604","2":"604"},"top":{"1":"300","3":"300","2":"300"},"font":"\'Karla\', sans-serif","fontweight":"400","fontsize":"16px","color":"#999999","bg":"","padding":"10px 15px 10px 15px","radius":"3px 3px 3px 3px","customcss":"text-align:center;","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"4","p_index":"0","start":"1000","end":"9500"},{"type":"button","description":{"1":"&lt;b&gt;Brands&lt;\\/b&gt;","3":"&lt;b&gt;Brands&lt;\\/b&gt;","2":"&lt;b&gt;Brands&lt;\\/b&gt;"},"left":{"1":"708","3":"708","2":"708"},"top":{"1":"386","3":"386","2":"386"},"button_class":"btn btn-lg btn-primary","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"5","p_index":"0","start":"1500","end":"9500"},{"type":"button","description":{"1":"&lt;b&gt;Products&lt;\\/b&gt;","3":"&lt;b&gt;Products&lt;\\/b&gt;","2":"&lt;b&gt;Products&lt;\\/b&gt;"},"left":{"1":"849","3":"849","2":"849"},"top":{"1":"386","3":"386","2":"386"},"button_class":"btn btn-lg btn-primary","button_href":"","button_target":"0","transitionin":"bottom(short)","easingin":"easeOutQuint","durationin":"900","transitionout":"bottom(short)","easingout":"easeOutQuint","durationout":"900","sort_order":"6","p_index":"0","start":"1500","end":"9500"}]}}}'),
  array('module_id' => '40','name' => 'Information Boxes - 3x Bordered Boxes with Buttons','code' => 'basel_content','setting' => '{"save":"stay","name":"Information Boxes - 3x Bordered Boxes with Buttons","status":"1","b_setting":{"title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"&lt;i class=&quot;contrast-font&quot; style=&quot;font-size:18px&quot;&gt;\\r\\nWelcome to Basel &amp; Co. \\u2013 worldwide cosmetics and make up salon. Check our products here on our &lt;a class=&quot;primary-color&quot; href=&quot;#&quot;&gt;Catalog&lt;\\/a&gt;\\r\\n&lt;\\/i&gt;","3":"&lt;i class=&quot;contrast-font&quot; style=&quot;font-size:18px&quot;&gt;\\r\\nWelcome to Basel &amp; Co. \\u2013 worldwide cosmetics and make up salon. Check our products here on our &lt;a class=&quot;primary-color&quot; href=&quot;#&quot;&gt;Catalog&lt;\\/a&gt;\\r\\n&lt;\\/i&gt;","2":"&lt;i class=&quot;contrast-font&quot; style=&quot;font-size:18px&quot;&gt;\\r\\nWelcome to Basel &amp; Co. \\u2013 worldwide cosmetics and make up salon. Check our products here on our &lt;a class=&quot;primary-color&quot; href=&quot;#&quot;&gt;Catalog&lt;\\/a&gt;\\r\\n&lt;\\/i&gt;"},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding-top:35px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;SCULPTED BLUSH AND LASH OVERLOAD TREND\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;SCULPTED BLUSH AND LASH OVERLOAD TREND\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info1.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;SCULPTED BLUSH AND LASH OVERLOAD TREND\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"2":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;THE ULTIMATE BEAUTIFYING MAKEUP LOOK THAT SUITS\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;THE ULTIMATE BEAUTIFYING MAKEUP LOOK THAT SUITS\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info2.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;THE ULTIMATE BEAUTIFYING MAKEUP LOOK THAT SUITS\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}},"3":{"w":"col-sm-4","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-top text-center","data1":{"1":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;HOW TO WEAR BRONZER BEAUTIFULY FRASH DAY LOOK\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","3":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;HOW TO WEAR BRONZER BEAUTIFULY FRASH DAY LOOK\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;","2":"&lt;div class=&quot;bordered-item-block&quot;&gt;\\r\\n&lt;img src=&quot;image\\/catalog\\/basel-demo\\/demo6\\/infoboxes\\/info3.jpg&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;div class=&quot;bordered-content&quot;&gt;\\r\\n&lt;span&gt;EXCLUSIVE&lt;\\/span&gt;\\r\\n&lt;h5&gt;HOW TO WEAR BRONZER BEAUTIFULY FRASH DAY LOOK\\u2122&lt;\\/h5&gt;\\r\\n&lt;p&gt;Nascetur a purus pulvinar suspendisse dis. Facilisis duis penatibus.&lt;\\/p&gt;\\r\\n&lt;\\/div&gt;\\r\\n&lt;a class=&quot;btn btn-contrast&quot; href=&quot;#&quot;&gt;Products&lt;\\/a&gt;\\r\\n&lt;\\/div&gt;"}}}}'),
  array('module_id' => '41','name' => 'Home Page Products','code' => 'basel_products','setting' => '{"name":"Home Page Products","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"LATEST PRODUCTS","3":"LATEST PRODUCTS","2":"LATEST PRODUCTS"},"title_b":{"1":"Lorem ipsum dolor sit amet, consectetur adipiscin &lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et","3":"Lorem ipsum dolor sit amet, consectetur adipiscin &lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et","2":"Lorem ipsum dolor sit amet, consectetur adipiscin &lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et"},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"6","image_width":"360","image_height":"459","columns":"3","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"1","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"1","margin":"40px"}'),
  array('module_id' => '42','name' => 'Home Page Latest News','code' => 'blog_latest','setting' => '{"name":"Home Page Latest News","status":"1","contrast":"1","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"COSMETICS MAGAZINE","3":"COSMETICS MAGAZINE","2":"COSMETICS MAGAZINE"},"title_b":{"1":"Lorem ipsum dolor sit amet, consectetur adipiscin&lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et","3":"Lorem ipsum dolor sit amet, consectetur adipiscin&lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et","2":"Lorem ipsum dolor sit amet, consectetur adipiscin&lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et"},"characters":"120","use_thumb":"1","width":"360","height":"221","limit":"3","columns":"3","carousel":"1","rows":"1","carousel_a":"0","carousel_b":"0","use_button":"0","use_margin":"0","margin":"60px"}'),
  array('module_id' => '51','name' => 'Testimonial Slider','code' => 'basel_content','setting' => '{"save":"stay","name":"Testimonial Slider","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"0","mt":"","mr":"","mb":"","ml":"","fw":"0","block_bg":"0","bg_color":"","block_bgi":"1","bg_par":"0","bg_pos":"center center","bg_repeat":"no-repeat","block_bgv":"0","bg_video":"","block_css":"0","css":""},"bg_image":"catalog\\/basel-demo\\/demo6\\/testimonials-bg.png","c_setting":{"fw":"0","block_css":"1","css":"max-width:760px;\\r\\npadding:30px 0px 15px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"tm","data1":"3","data7":"1","data8":"plain"}}}'),
  array('module_id' => '50','name' => 'Home Page Top Categories','code' => 'basel_categories','setting' => '{"name":"Home Page Top Categories","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"TOP CATEGORIES","3":"TOP CATEGORIES","2":"TOP CATEGORIES"},"title_b":{"1":"Lorem ipsum dolor sit amet, consectetur adipiscin&lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et","3":"Lorem ipsum dolor sit amet, consectetur adipiscin&lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et","2":"Lorem ipsum dolor sit amet, consectetur adipiscin&lt;br&gt;\\r\\nlorem solo tempor incididunt ut labore et"},"category":["'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'","'.$get_category_id.'"],"image_width":"262","image_height":"335","subs":"0","limit":"5","count":"1","columns":"4","carousel":"1","rows":"1","carousel_a":"1","carousel_b":"0","use_margin":"0","margin":"60px"}'),
  array('module_id' => '45','name' => 'Instagram Feed','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed","status":"1","full_width":"1","use_title":"1","title_inline":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"INSTAGRAM","3":"INSTAGRAM","2":"INSTAGRAM"},"title_b":{"1":"Posuere eget ut sed consectetur litora &lt;br&gt;\\r\\nlobortis cras&lt;br&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-contrast btn-tiny&quot;&gt;Follow Us&lt;\\/a&gt;","3":"Posuere eget ut sed consectetur litora &lt;br&gt;\\r\\nlobortis cras&lt;br&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-contrast btn-tiny&quot;&gt;Follow Us&lt;\\/a&gt;","2":"Posuere eget ut sed consectetur litora &lt;br&gt;\\r\\nlobortis cras&lt;br&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-contrast btn-tiny&quot;&gt;Follow Us&lt;\\/a&gt;"},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"12","resolution":"0","columns":"6","columns_md":"6","columns_sm":"4","padding":"0","use_margin":"1","margin":"-50px"}'),
  array('module_id' => '46','name' => 'Column Product List','code' => 'basel_products','setting' => '{"name":"Column Product List","status":"1","contrast":"0","use_title":"1","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"Popular Items","3":"Popular Items","2":"Popular Items"},"title_b":{"1":"","3":"","2":""},"selected_tabs":{"tabs":["1"]},"tabstyle":"nav-tabs-sm text-center","limit":"4","image_width":"60","image_height":"76","columns":"list","carousel":"0","rows":"1","carousel_a":"1","carousel_b":"0","countdown":"0","use_button":"0","link_title":{"1":"","3":"","2":""},"link_href":"","use_margin":"0","margin":"60px"}'),
  array('module_id' => '48','name' => 'Instagram Feed (Column Style)','code' => 'basel_instagram','setting' => '{"name":"Instagram Feed (Column Style)","status":"1","full_width":"0","use_title":"1","title_inline":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"@Instagram","3":"@Instagram","2":"@Instagram"},"title_b":{"1":"","3":"","2":""},"username":"basel_opencart","access_token":"basel_opencart.c03e81d.7ae0668fe07b47409787c978a7f64e3a","limit":"9","resolution":"0","columns":"3","columns_md":"3","columns_sm":"3","padding":"5","use_margin":"0","margin":"60px"}'),
  array('module_id' => '49','name' => 'Jumbotron With Video Background','code' => 'basel_content','setting' => '{"save":"stay","name":"Jumbotron With Video Background","status":"1","b_setting":{"title":"0","title_pl":{"1":"","3":"","2":""},"title_m":{"1":"","3":"","2":""},"title_b":{"1":"","3":"","2":""},"custom_m":"1","mt":"0","mr":"0","mb":"80","ml":"0","fw":"1","block_bg":"1","bg_color":"rgba(0,0,0,0.5)","block_bgi":"0","bg_par":"0","bg_pos":"left top","bg_repeat":"no-repeat","block_bgv":"1","bg_video":"0IPAdxGD4s8","block_css":"0","css":""},"bg_image":"","c_setting":{"fw":"0","block_css":"1","css":"padding:110px 6px 70px 6px;\\r\\nmax-width:760px;","nm":"0","eh":"0"},"columns":{"1":{"w":"col-sm-12","w_sm":"col-xs-1","w_md":"col-sm-1","w_lg":"col-md-1","type":"html","data7":"vertical-middle text-center","data1":{"1":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/video-jumbotron-overlay.png&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h2 class=&quot;contrast-font&quot; style=&quot;font-size:40px;color:#ffffff&quot;&gt;\\r\\n#HandPicked By Our Beauty Experts \\r\\n&lt;\\/h2&gt;\\r\\n&lt;p style=&quot;color:#ffffff;font-size:16px;margin-bottom:30px;&quot;&gt;\\r\\nUt sagittis sem erat accumsan parturient accumsan pharetra neque per euismod parturient\\r\\ndictumst ad praesent laoreet non ornare ante fames sem sem\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-light&quot;&gt;Read More&lt;\\/a&gt;","3":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/video-jumbotron-overlay.png&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h2 class=&quot;contrast-font&quot; style=&quot;font-size:40px;color:#ffffff&quot;&gt;\\r\\n#HandPicked By Our Beauty Experts \\r\\n&lt;\\/h2&gt;\\r\\n&lt;p style=&quot;color:#ffffff;font-size:16px;margin-bottom:30px;&quot;&gt;\\r\\nUt sagittis sem erat accumsan parturient accumsan pharetra neque per euismod parturient\\r\\ndictumst ad praesent laoreet non ornare ante fames sem sem\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-light&quot;&gt;Read More&lt;\\/a&gt;","2":"&lt;img src=&quot;image\\/catalog\\/basel-demo\\/video-jumbotron-overlay.png&quot; alt=&quot;&quot; \\/&gt;\\r\\n&lt;h2 class=&quot;contrast-font&quot; style=&quot;font-size:40px;color:#ffffff&quot;&gt;\\r\\n#HandPicked By Our Beauty Experts \\r\\n&lt;\\/h2&gt;\\r\\n&lt;p style=&quot;color:#ffffff;font-size:16px;margin-bottom:30px;&quot;&gt;\\r\\nUt sagittis sem erat accumsan parturient accumsan pharetra neque per euismod parturient\\r\\ndictumst ad praesent laoreet non ornare ante fames sem sem\\r\\n&lt;\\/p&gt;\\r\\n&lt;a href=&quot;#&quot; class=&quot;btn btn-light&quot;&gt;Read More&lt;\\/a&gt;"}}}}')
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
  array('layout_module_id' => '290','layout_id' => '1','code' => 'basel_instagram.45','position' => 'top','sort_order' => '8'),
  array('layout_module_id' => '201','layout_id' => '3','code' => 'category','position' => 'column_left','sort_order' => '1'),
  array('layout_module_id' => '289','layout_id' => '1','code' => 'blog_latest.42','position' => 'top','sort_order' => '6'),
  array('layout_module_id' => '202','layout_id' => '3','code' => 'basel_products.46','position' => 'column_left','sort_order' => '2'),
  array('layout_module_id' => '288','layout_id' => '1','code' => 'basel_categories.50','position' => 'top','sort_order' => '5'),
  array('layout_module_id' => '287','layout_id' => '1','code' => 'basel_content.49','position' => 'top','sort_order' => '4'),
  array('layout_module_id' => '205','layout_id' => '14','code' => 'basel_products.46','position' => 'column_right','sort_order' => '2'),
  array('layout_module_id' => '203','layout_id' => '3','code' => 'basel_instagram.48','position' => 'column_left','sort_order' => '3'),
  array('layout_module_id' => '206','layout_id' => '14','code' => 'basel_instagram.48','position' => 'column_right','sort_order' => '3'),
  array('layout_module_id' => '286','layout_id' => '1','code' => 'basel_products.41','position' => 'top','sort_order' => '3'),
  array('layout_module_id' => '285','layout_id' => '1','code' => 'basel_content.40','position' => 'top','sort_order' => '2'),
  array('layout_module_id' => '284','layout_id' => '1','code' => 'basel_layerslider.37','position' => 'top','sort_order' => '1'),
  array('layout_module_id' => '291','layout_id' => '1','code' => 'basel_content.51','position' => 'top','sort_order' => '7')
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
  array('layout_route_id' => '104','layout_id' => '1','store_id' => '0','route' => 'common/home'),
  array('layout_route_id' => '95','layout_id' => '2','store_id' => '0','route' => 'product/product'),
  array('layout_route_id' => '102','layout_id' => '11','store_id' => '0','route' => 'information/information'),
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
  array('setting_id' => '9581','store_id' => '0','code' => 'basel','key' => 'basel_fonts','value' => '{"1":{"import":"Karla:400,400i,700,700i","name":"\'Karla\', sans-serif"},"2":{"import":"Lora:400,400i","name":"\'Lora\', serif"},"3":{"import":"Sumana","name":"\'Sumana\', serif"}}','serialized' => '1'),
  array('setting_id' => '9516','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_m','value' => '767','serialized' => '0'),
  array('setting_id' => '9515','store_id' => '0','code' => 'basel','key' => 'basel_footer_columns','value' => '','serialized' => '1'),
  array('setting_id' => '9514','store_id' => '0','code' => 'basel','key' => 'countdown_status','value' => '1','serialized' => '0'),
  array('setting_id' => '9513','store_id' => '0','code' => 'basel','key' => 'product_tabs_style','value' => 'nav-tabs-lg text-center','serialized' => '0'),
  array('setting_id' => '9512','store_id' => '0','code' => 'basel','key' => 'basel_links','value' => '','serialized' => '1'),
  array('setting_id' => '9511','store_id' => '0','code' => 'basel','key' => 'basel_header','value' => 'header4','serialized' => '0'),
  array('setting_id' => '9501','store_id' => '0','code' => 'basel','key' => 'footer_infoline_3','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '9502','store_id' => '0','code' => 'basel','key' => 'footer_block_2','value' => '{"1":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","3":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site.","2":"STORE - worldwide fashion store since 1978. We sell over 1000+ branded products on our web-site."}','serialized' => '1'),
  array('setting_id' => '11707','store_id' => '0','code' => 'basel','key' => 'top_line_style','value' => '0','serialized' => '0'),
  array('setting_id' => '11708','store_id' => '0','code' => 'basel','key' => 'basel_home_overlay_header','value' => '0','serialized' => '0'),
  array('setting_id' => '16884','store_id' => '0','code' => 'basel','key' => 'logo_maxwidth','value' => '175','serialized' => '0'),
  array('setting_id' => '9504','store_id' => '0','code' => 'basel','key' => 'basel_sticky_header','value' => '1','serialized' => '0'),
  array('setting_id' => '9505','store_id' => '0','code' => 'basel','key' => 'main_menu_align','value' => 'menu-aligned-left','serialized' => '0'),
array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_normal','value' => '80','serialized' => '0'),
  array('setting_id' => '21404','store_id' => '0','code' => 'basel','key' => 'menu_height_sticky','value' => '70','serialized' => '0'),  array('setting_id' => '9507','store_id' => '0','code' => 'basel','key' => 'main_header_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '9508','store_id' => '0','code' => 'basel','key' => 'main_header_height','value' => '80','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_sticky','value' => '70','serialized' => '0'),
  array('setting_id' => '1217','store_id' => '0','code' => 'basel','key' => 'main_header_height_mobile','value' => '70','serialized' => '0'),
  array('setting_id' => '9509','store_id' => '0','code' => 'basel','key' => 'top_line_width','value' => 'full-width','serialized' => '0'),
  array('setting_id' => '9500','store_id' => '0','code' => 'basel','key' => 'basel_payment_img','value' => 'catalog/basel-demo/payments.png','serialized' => '0'),
  array('setting_id' => '9499','store_id' => '0','code' => 'basel','key' => 'footer_infoline_1','value' => '{"1":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","3":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York","2":"&lt;i class=&quot;fa fa-location-arrow&quot;&gt;&lt;\\/i&gt; 451 Wall Street, USA, New York"}','serialized' => '1'),
  array('setting_id' => '9498','store_id' => '0','code' => 'basel','key' => 'footer_infoline_2','value' => '{"1":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","3":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233","2":"&lt;i class=&quot;fa fa-mobile&quot;&gt;&lt;\\/i&gt; Phone: (064) 332-1233"}','serialized' => '1'),
  array('setting_id' => '11735','store_id' => '0','code' => 'basel','key' => 'basel_custom_js_status','value' => '0','serialized' => '0'),
  array('setting_id' => '11736','store_id' => '0','code' => 'basel','key' => 'basel_custom_js','value' => '','serialized' => '0'),
  array('setting_id' => '11284','store_id' => '0','code' => 'basel','key' => 'basel_custom_css_status','value' => '1','serialized' => '0'),
  array('setting_id' => '10290','store_id' => '0','code' => 'basel','key' => 'basel_custom_css','value' => '.bordered-item-block .bordered-content h5 {
font-family:\'Sumana\', serif;
}','serialized' => '0'),
  array('setting_id' => '9493','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_height','value' => '590','serialized' => '0'),
  array('setting_id' => '9492','store_id' => '0','code' => 'basel','key' => 'subcat_image_height','value' => '264','serialized' => '0'),
  array('setting_id' => '9491','store_id' => '0','code' => 'basel','key' => 'quickview_popup_image_width','value' => '465','serialized' => '0'),
  array('setting_id' => '9490','store_id' => '0','code' => 'basel','key' => 'subcat_image_width','value' => '200','serialized' => '0'),
  array('setting_id' => '9489','store_id' => '0','code' => 'basel','key' => 'menu_font_ls','value' => '0.5px','serialized' => '0'),
  array('setting_id' => '9488','store_id' => '0','code' => 'basel','key' => 'menu_font_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '9487','store_id' => '0','code' => 'basel','key' => 'menu_font_size','value' => '14px','serialized' => '0'),
  array('setting_id' => '9486','store_id' => '0','code' => 'basel','key' => 'menu_font_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '9485','store_id' => '0','code' => 'basel','key' => 'menu_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '9484','store_id' => '0','code' => 'basel','key' => 'widget_sm_ls','value' => '0.75px','serialized' => '0'),
  array('setting_id' => '9483','store_id' => '0','code' => 'basel','key' => 'widget_sm_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '9482','store_id' => '0','code' => 'basel','key' => 'widget_sm_size','value' => '17px','serialized' => '0'),
  array('setting_id' => '9481','store_id' => '0','code' => 'basel','key' => 'widget_sm_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '9480','store_id' => '0','code' => 'basel','key' => 'widget_sm_fam','value' => '\'Sumana\', serif','serialized' => '0'),
  array('setting_id' => '9479','store_id' => '0','code' => 'basel','key' => 'widget_lg_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '9478','store_id' => '0','code' => 'basel','key' => 'widget_lg_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '9477','store_id' => '0','code' => 'basel','key' => 'widget_lg_size','value' => '25px','serialized' => '0'),
  array('setting_id' => '9476','store_id' => '0','code' => 'basel','key' => 'widget_lg_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '9475','store_id' => '0','code' => 'basel','key' => 'h1_inline_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '9474','store_id' => '0','code' => 'basel','key' => 'h1_inline_trans','value' => 'none','serialized' => '0'),
  array('setting_id' => '9473','store_id' => '0','code' => 'basel','key' => 'h1_inline_size','value' => '34px','serialized' => '0'),
  array('setting_id' => '9472','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_fam','value' => '\'Sumana\', serif','serialized' => '0'),
  array('setting_id' => '9471','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '9470','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_size','value' => '31px','serialized' => '0'),
  array('setting_id' => '9460','store_id' => '0','code' => 'basel','key' => 'body_font_size_12','value' => '12px','serialized' => '0'),
  array('setting_id' => '9461','store_id' => '0','code' => 'basel','key' => 'headings_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '9462','store_id' => '0','code' => 'basel','key' => 'headings_size_sm','value' => '20px','serialized' => '0'),
  array('setting_id' => '9463','store_id' => '0','code' => 'basel','key' => 'headings_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '9464','store_id' => '0','code' => 'basel','key' => 'h1_inline_weight','value' => '400','serialized' => '0'),
  array('setting_id' => '9465','store_id' => '0','code' => 'basel','key' => 'h1_inline_fam','value' => '\'Sumana\', serif','serialized' => '0'),
  array('setting_id' => '9466','store_id' => '0','code' => 'basel','key' => 'headings_size_lg','value' => '30px','serialized' => '0'),
  array('setting_id' => '9467','store_id' => '0','code' => 'basel','key' => 'widget_lg_fam','value' => '\'Sumana\', serif','serialized' => '0'),
  array('setting_id' => '9468','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_ls','value' => '0px','serialized' => '0'),
  array('setting_id' => '9469','store_id' => '0','code' => 'basel','key' => 'h1_breadcrumb_trans','value' => 'uppercase','serialized' => '0'),
  array('setting_id' => '9455','store_id' => '0','code' => 'basel','key' => 'contrast_font_fam','value' => '\'Lora\', serif','serialized' => '0'),
  array('setting_id' => '9456','store_id' => '0','code' => 'basel','key' => 'body_font_size_15','value' => '15px','serialized' => '0'),
  array('setting_id' => '9457','store_id' => '0','code' => 'basel','key' => 'body_font_size_16','value' => '16px','serialized' => '0'),
  array('setting_id' => '9458','store_id' => '0','code' => 'basel','key' => 'body_font_size_14','value' => '14px','serialized' => '0'),
  array('setting_id' => '9459','store_id' => '0','code' => 'basel','key' => 'body_font_size_13','value' => '13px','serialized' => '0'),
  array('setting_id' => '9448','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color_hover','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '9449','store_id' => '0','code' => 'basel','key' => 'basel_footer_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '10971','store_id' => '0','code' => 'basel','key' => 'basel_typo_status','value' => '1','serialized' => '0'),
  array('setting_id' => '9451','store_id' => '0','code' => 'basel','key' => 'basel_footer_h5_sep','value' => '#edc5c7','serialized' => '0'),
  array('setting_id' => '9452','store_id' => '0','code' => 'basel','key' => 'body_font_fam','value' => '\'Karla\', sans-serif','serialized' => '0'),
  array('setting_id' => '9453','store_id' => '0','code' => 'basel','key' => 'body_font_italic_status','value' => '1','serialized' => '0'),
  array('setting_id' => '9454','store_id' => '0','code' => 'basel','key' => 'body_font_bold_weight','value' => '700','serialized' => '0'),
  array('setting_id' => '9447','store_id' => '0','code' => 'basel','key' => 'basel_contrast_btn_bg','value' => '#edc5c7','serialized' => '0'),
  array('setting_id' => '9446','store_id' => '0','code' => 'basel','key' => 'basel_footer_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '9445','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg_hover','value' => '#3e3e3e','serialized' => '0'),
  array('setting_id' => '9444','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg','value' => '#212121','serialized' => '0'),
  array('setting_id' => '9443','store_id' => '0','code' => 'basel','key' => 'basel_vertical_menu_bg_hover','value' => '#d62e6c','serialized' => '0'),
  array('setting_id' => '9440','store_id' => '0','code' => 'basel','key' => 'basel_price_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '9441','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '9442','store_id' => '0','code' => 'basel','key' => 'basel_default_btn_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '9438','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '9439','store_id' => '0','code' => 'basel','key' => 'basel_newbadge_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '9437','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '9436','store_id' => '0','code' => 'basel','key' => 'basel_salebadge_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '9435','store_id' => '0','code' => 'basel','key' => 'basel_primary_accent_color','value' => '#edc5c7','serialized' => '0'),
  array('setting_id' => '9434','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '9433','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '9432','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '9415','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_att','value' => 'scroll','serialized' => '0'),
  array('setting_id' => '9416','store_id' => '0','code' => 'basel','key' => 'basel_top_note_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '9417','store_id' => '0','code' => 'basel','key' => 'basel_top_note_bg','value' => '#000000','serialized' => '0'),
  array('setting_id' => '9418','store_id' => '0','code' => 'basel','key' => 'basel_top_line_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '9419','store_id' => '0','code' => 'basel','key' => 'basel_top_line_bg','value' => '#222222','serialized' => '0'),
  array('setting_id' => '9420','store_id' => '0','code' => 'basel','key' => 'basel_header_bg','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '9421','store_id' => '0','code' => 'basel','key' => 'basel_header_color','value' => '#000000','serialized' => '0'),
  array('setting_id' => '9422','store_id' => '0','code' => 'basel','key' => 'basel_header_accent','value' => '#edc5c7','serialized' => '0'),
  array('setting_id' => '9423','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_bg','value' => '#111111','serialized' => '0'),
  array('setting_id' => '9424','store_id' => '0','code' => 'basel','key' => 'basel_header_menu_color','value' => '#eeeeee','serialized' => '0'),
  array('setting_id' => '9425','store_id' => '0','code' => 'basel','key' => 'basel_search_scheme','value' => 'dark-search','serialized' => '0'),
  array('setting_id' => '9426','store_id' => '0','code' => 'basel','key' => 'basel_menutag_sale_bg','value' => '#d41212','serialized' => '0'),
  array('setting_id' => '9427','store_id' => '0','code' => 'basel','key' => 'basel_menutag_new_bg','value' => '#edc5c7','serialized' => '0'),
  array('setting_id' => '9428','store_id' => '0','code' => 'basel','key' => 'basel_bc_color','value' => '#f7f7f7','serialized' => '0'),
  array('setting_id' => '9429','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '11734','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '9431','store_id' => '0','code' => 'basel','key' => 'basel_bc_bg_color','value' => '#111111','serialized' => '0'),
  array('setting_id' => '9414','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_repeat','value' => 'no-repeat','serialized' => '0'),
  array('setting_id' => '9413','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_size','value' => 'auto','serialized' => '0'),
  array('setting_id' => '9412','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img_pos','value' => 'top left','serialized' => '0'),
  array('setting_id' => '11733','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_img','value' => '','serialized' => '0'),
  array('setting_id' => '9410','store_id' => '0','code' => 'basel','key' => 'basel_body_bg_color','value' => '#ffffff','serialized' => '0'),
  array('setting_id' => '11731','store_id' => '0','code' => 'basel','key' => 'basel_content_width','value' => '','serialized' => '0'),
  array('setting_id' => '9408','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns_offset','value' => '100','serialized' => '0'),
  array('setting_id' => '10094','store_id' => '0','code' => 'basel','key' => 'basel_widget_title_style','value' => '2','serialized' => '0'),
  array('setting_id' => '11126','store_id' => '0','code' => 'basel','key' => 'basel_design_status','value' => '1','serialized' => '0'),
  array('setting_id' => '9405','store_id' => '0','code' => 'basel','key' => 'basel_sticky_columns','value' => '1','serialized' => '0'),
  array('setting_id' => '11730','store_id' => '0','code' => 'basel','key' => 'basel_main_layout','value' => '0','serialized' => '0'),
  array('setting_id' => '9403','store_id' => '0','code' => 'basel','key' => 'basel_cart_icon','value' => 'global-cart-handbag','serialized' => '0'),
  array('setting_id' => '9402','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_block','value' => '{"1":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","3":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}","2":"&lt;p style=&quot;font-size:16px;color:#666666&quot;&gt;\\r\\nBe the first to learn about our latest trends and get exclusive offers.\\r\\n&lt;\\/p&gt;\\r\\n{signup}"}','serialized' => '1'),
  array('setting_id' => '9401','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_title','value' => '{"1":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","3":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;","2":"&lt;b&gt;HEY YOU, SIGN UP AND CONNECT TO BASEL &amp; CO&lt;\\/b&gt;"}','serialized' => '1'),
  array('setting_id' => '9400','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_img','value' => 'catalog/basel-demo/popup-note.jpg','serialized' => '0'),
  array('setting_id' => '9399','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_delay','value' => '8000','serialized' => '0'),
  array('setting_id' => '9398','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_w','value' => '920','serialized' => '0'),
  array('setting_id' => '9397','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_h','value' => '480','serialized' => '0'),
  array('setting_id' => '9396','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_home','value' => '1','serialized' => '0'),
  array('setting_id' => '10125','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_once','value' => '1','serialized' => '0'),
  array('setting_id' => '11729','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_url','value' => '','serialized' => '0'),
  array('setting_id' => '9393','store_id' => '0','code' => 'basel','key' => 'basel_popup_note_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11728','store_id' => '0','code' => 'basel','key' => 'basel_cookie_bar_status','value' => '0','serialized' => '0'),
  array('setting_id' => '9391','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_text','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '11727','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_align','value' => '','serialized' => '0'),
  array('setting_id' => '11726','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_close','value' => '0','serialized' => '0'),
  array('setting_id' => '11725','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_width','value' => '','serialized' => '0'),
  array('setting_id' => '11724','store_id' => '0','code' => 'basel','key' => 'basel_top_promo_status','value' => '0','serialized' => '0'),
  array('setting_id' => '9386','store_id' => '0','code' => 'basel','key' => 'basel_copyright','value' => '{"1":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","3":"\\u00a9 Copyright - All rights reserved. 2010 - {year}","2":"\\u00a9 Copyright - All rights reserved. 2010 - {year}"}','serialized' => '1'),
  array('setting_id' => '9385','store_id' => '0','code' => 'basel','key' => 'footer_block_title','value' => '{"1":"About Us","3":"About Us","2":"About Us"}','serialized' => '1'),
  array('setting_id' => '11723','store_id' => '0','code' => 'basel','key' => 'overwrite_footer_links','value' => '0','serialized' => '0'),
  array('setting_id' => '9383','store_id' => '0','code' => 'basel','key' => 'footer_block_1','value' => '{"1":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","3":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;","2":"&lt;p style=&quot;text-align:center;&quot;&gt;\\r\\n&lt;img alt=&quot;Logo&quot; src=&quot;image\\/catalog\\/basel-demo\\/logo-white.png&quot;&gt;\\r\\n&lt;\\/p&gt;"}','serialized' => '1'),
  array('setting_id' => '11722','store_id' => '0','code' => 'basel','key' => 'basel_compare_action','value' => '0','serialized' => '0'),
  array('setting_id' => '9380','store_id' => '0','code' => 'basel','key' => 'compare_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11721','store_id' => '0','code' => 'basel','key' => 'basel_wishlist_action','value' => '0','serialized' => '0'),
  array('setting_id' => '9377','store_id' => '0','code' => 'basel','key' => 'newlabel_status','value' => '30','serialized' => '0'),
  array('setting_id' => '11720','store_id' => '0','code' => 'basel','key' => 'basel_cart_action','value' => '0','serialized' => '0'),
  array('setting_id' => '9379','store_id' => '0','code' => 'basel','key' => 'wishlist_status','value' => '1','serialized' => '0'),
  array('setting_id' => '9376','store_id' => '0','code' => 'basel','key' => 'quickview_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11715','store_id' => '0','code' => 'basel','key' => 'basel_map_style','value' => '0','serialized' => '0'),
  array('setting_id' => '9373','store_id' => '0','code' => 'basel','key' => 'basel_prod_grid','value' => '3','serialized' => '0'),
  array('setting_id' => '9374','store_id' => '0','code' => 'basel','key' => 'basel_subs_grid','value' => '5','serialized' => '0'),
  array('setting_id' => '9375','store_id' => '0','code' => 'basel','key' => 'salebadge_status','value' => '1','serialized' => '0'),
  array('setting_id' => '11716','store_id' => '0','code' => 'basel','key' => 'basel_map_lat','value' => '','serialized' => '0'),
  array('setting_id' => '11717','store_id' => '0','code' => 'basel','key' => 'basel_map_lon','value' => '','serialized' => '0'),
  array('setting_id' => '11718','store_id' => '0','code' => 'basel','key' => 'basel_map_api','value' => '','serialized' => '0'),
  array('setting_id' => '9367','store_id' => '0','code' => 'basel','key' => 'basel_list_style','value' => '6','serialized' => '0'),
  array('setting_id' => '11719','store_id' => '0','code' => 'basel','key' => 'catalog_mode','value' => '0','serialized' => '0'),
  array('setting_id' => '9366','store_id' => '0','code' => 'basel','key' => 'basel_cut_names','value' => '1','serialized' => '0'),
  array('setting_id' => '11732','store_id' => '0','code' => 'basel','key' => 'items_mobile_fw','value' => '0','serialized' => '0'),
  array('setting_id' => '11713','store_id' => '0','code' => 'basel','key' => 'questions_new_status','value' => '0','serialized' => '0'),
  array('setting_id' => '9363','store_id' => '0','code' => 'basel','key' => 'basel_rel_prod_grid','value' => '4','serialized' => '0'),
  array('setting_id' => '11714','store_id' => '0','code' => 'basel','key' => 'category_thumb_status','value' => '0','serialized' => '0'),
  array('setting_id' => '9360','store_id' => '0','code' => 'basel','key' => 'questions_per_page','value' => '5','serialized' => '0'),
  array('setting_id' => '9361','store_id' => '0','code' => 'basel','key' => 'category_subs_status','value' => '1','serialized' => '0'),
  array('setting_id' => '9359','store_id' => '0','code' => 'basel','key' => 'meta_description_status','value' => '1','serialized' => '0'),
  array('setting_id' => '9358','store_id' => '0','code' => 'basel','key' => 'product_page_countdown','value' => '1','serialized' => '0'),
  array('setting_id' => '11711','store_id' => '0','code' => 'basel','key' => 'ex_tax_status','value' => '0','serialized' => '0'),
  array('setting_id' => '9357','store_id' => '0','code' => 'basel','key' => 'basel_share_btn','value' => '1','serialized' => '0'),
  array('setting_id' => '9355','store_id' => '0','code' => 'basel','key' => 'full_width_tabs','value' => '1','serialized' => '0'),
  array('setting_id' => '11712','store_id' => '0','code' => 'basel','key' => 'product_question_status','value' => '0','serialized' => '0'),
  array('setting_id' => '9353','store_id' => '0','code' => 'basel','key' => 'basel_hover_zoom','value' => '1','serialized' => '0'),
  array('setting_id' => '9349','store_id' => '0','code' => 'basel','key' => 'basel_titles_default','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '9350','store_id' => '0','code' => 'basel','key' => 'basel_titles_blog','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '9351','store_id' => '0','code' => 'basel','key' => 'basel_titles_contact','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '9352','store_id' => '0','code' => 'basel','key' => 'product_layout','value' => 'images-left','serialized' => '0'),
  array('setting_id' => '9346','store_id' => '0','code' => 'basel','key' => 'basel_titles_checkout','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '9347','store_id' => '0','code' => 'basel','key' => 'basel_titles_account','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '11710','store_id' => '0','code' => 'basel','key' => 'basel_back_btn','value' => '0','serialized' => '0'),
  array('setting_id' => '9345','store_id' => '0','code' => 'basel','key' => 'basel_titles_product','value' => 'default_bc full_width_bc','serialized' => '0'),
  array('setting_id' => '9344','store_id' => '0','code' => 'basel','key' => 'basel_promo','value' => '{"1":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","3":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;","2":"&lt;i class=&quot;fa fa-phone-square&quot;&gt;&lt;\\/i&gt; OUR PHONE NUMBER: &lt;span style=&quot;margin-left:10px; border-bottom: 1px solid rgba(255,255,255,0.3);&quot;&gt;+77 (756) 334 876&lt;\\/span&gt;"}','serialized' => '1'),
  array('setting_id' => '9339','store_id' => '0','code' => 'basel','key' => 'basel_titles_listings','value' => 'title_in_bc normal_height_bc','serialized' => '0'),
  array('setting_id' => '11709','store_id' => '0','code' => 'basel','key' => 'use_custom_links','value' => '0','serialized' => '0'),
  array('setting_id' => '10881','store_id' => '0','code' => 'basel','key' => 'secondary_menu','value' => '54','serialized' => '0'),
  array('setting_id' => '9342','store_id' => '0','code' => 'basel','key' => 'primary_menu','value' => '55','serialized' => '0'),
  array('setting_id' => '9343','store_id' => '0','code' => 'basel','key' => 'basel_promo2','value' => '{"1":"","3":"","2":""}','serialized' => '1'),
  array('setting_id' => '9337','store_id' => '0','code' => 'basel','key' => 'header_login','value' => '1','serialized' => '0'),
  array('setting_id' => '1073','store_id' => '0','code' => 'basel','key' => 'basel_thumb_swap','value' => '1','serialized' => '0'),
  array('setting_id' => '9338','store_id' => '0','code' => 'basel','key' => 'header_search','value' => '1','serialized' => '0')
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