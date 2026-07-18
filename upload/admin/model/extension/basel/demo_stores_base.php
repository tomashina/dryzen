<?php

class ModelExtensionBaselDemoStoresBase extends Model {
		
    /*--------------------------------------------
	--------------- ADD PRODUCTS -----------------
	--------------------------------------------*/
	public function addSampleProducts() {
		$this->load->model('catalog/product');
		
		//Remove existing
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE LOWER(model) LIKE 'basel-sample%'");

        foreach ($query->rows as $result) {
            if (!empty($result['product_id'])) {
                $this->model_catalog_product->deleteProduct($result['product_id']);
            }
        }
		
		// Add new
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();
        $product_stores = array();
        $product_stores[] = 0;
        foreach ($stores as $result) {
            $product_stores[] = $result['store_id'];
        }

        $description = array();
        foreach ($languages as $language) {
            $description[$language['language_id']] = array(
                'name' => 'Sample Product',
                'description' => '<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for lorem ipsum will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>',
                'meta_title' => 'Sample Product',
                'tag' => '',
                'meta_description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here.',
                'meta_keyword' => ''
            );
        }
		
		$product_to_category = array(
			$this->getCategoryId1(),
			$this->getCategoryId2(),
			$this->getCategoryId3(),
			$this->getCategoryId4()
		);
		
        $products = array();

        $products[] = array(
            'model' => 'basel-sample-1',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );

        $products[] = array(
            'model' => 'basel-sample-2',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );

        $products[] = array(
            'model' => 'basel-sample-3',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );

        $products[] = array(
            'model' => 'basel-sample-4',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );
		
		$products[] = array(
            'model' => 'basel-sample-5',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );
		
		$products[] = array(
            'model' => 'basel-sample-6',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );
		
		$products[] = array(
            'model' => 'basel-sample-7',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );
		
		$products[] = array(
            'model' => 'basel-sample-8',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );
		
		$products[] = array(
            'model' => 'basel-sample-9',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );
		
		$products[] = array(
            'model' => 'basel-sample-10',
            'product_description' => $description,
            'image' => 'catalog/basel-demo/product.png',
            'sku' => '', 'upc' => '', 'ean' => '', 'jan' => '', 'isbn' => '', 'mpn' => '', 'location' => '',
            'quantity' => '99',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '1',
            'date_available' => date('Y/m/d'),
            'manufacturer_id' => '',
            'product_store' => $product_stores,
			'product_category' => $product_to_category,
            'shipping' => '1',
            'price' => '100',
            'points' => '', 'weight' => '', 'weight_class_id' => '', 'length' => '', 'width' => '', 'height' => '', 'length_class_id' => '',
            'status' => '1',
            'tax_class_id' => '',
            'sort_order' => '',
            'keyword' => ''
        );
		
        foreach ($products as $product) {
        $this->model_catalog_product->addProduct($product);
        }
    }
	
	public function getProductId1 () {
		$existing = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE model = 'basel-sample-1'");
		if (!empty($existing->row['product_id'])) { return $existing->row['product_id']; } else { return 0; }
	}	
	
	
	/*--------------------------------------------
	--------------- ADD CATEGORY -----------------
	--------------------------------------------*/
	public function addSampleCategories() {
		$this->load->model('catalog/category');
		
		//Remove existing
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category  WHERE LOWER(sort_order) LIKE '900%' ");
        foreach ($query->rows as $result) {
            if (!empty($result['category_id'])) {
                $this->model_catalog_category->deleteCategory($result['category_id']);
            }
        }
		
		// Add new
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();
        $category_stores = array();
        $category_stores[] = 0;
        foreach ($stores as $result) {
            $category_stores[] = $result['store_id'];
        }

        $description = array();
        foreach ($languages as $language) {
            $description[$language['language_id']] = array(
                'name' => 'Sample Category',
                'description' => '',
                'meta_title' => 'Sample Category',
                'meta_description' => '',
                'meta_keyword' => ''
            );
        }
		
		$category = array();

        $category[] = array(
            'category_description' => $description,
            'image' => 'catalog/basel-demo/category.png',
            'category_store' => $category_stores,
            'status' => '1',
            'sort_order' => '9001',
            'parent_id' => '',
			'column' => '4'
        );
		
		$category[] = array(
            'category_description' => $description,
            'image' => 'catalog/basel-demo/category.png',
            'category_store' => $category_stores,
            'status' => '1',
            'sort_order' => '9002',
            'parent_id' => '',
			'column' => '4'
        );
		
		$category[] = array(
            'category_description' => $description,
            'image' => 'catalog/basel-demo/category.png',
            'category_store' => $category_stores,
            'status' => '1',
            'sort_order' => '9003',
            'parent_id' => '',
			'column' => '4'
        );
		
		$category[] = array(
            'category_description' => $description,
            'image' => 'catalog/basel-demo/category.png',
            'category_store' => $category_stores,
            'status' => '1',
            'sort_order' => '9004',
            'parent_id' => '',
			'column' => '4'
        );
		
		foreach ($category as $category_id) {
        $this->model_catalog_category->addCategory($category_id);
        }
    }
	
	
	public function getCategoryId1 () {
		$existing = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE sort_order = '9001'");
		if (!empty($existing->row['category_id'])) { return $existing->row['category_id']; } else { return 0; }
	}
	
	public function getCategoryId2 () {
		$existing = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE sort_order = '9002'");
		if (!empty($existing->row['category_id'])) { return $existing->row['category_id']; } else { return 0; }
	}
	
	public function getCategoryId3 () {
		$existing = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE sort_order = '9003'");
		if (!empty($existing->row['category_id'])) { return $existing->row['category_id']; } else { return 0; }
	}
	
	public function getCategoryId4 () {
		$existing = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE sort_order = '9004'");
		if (!empty($existing->row['category_id'])) { return $existing->row['category_id']; } else { return 0; }
	}
	
	/*--------------------------------------------
	-------------- ADD BLOG POSTS ----------------
	--------------------------------------------*/
	public function addSampleBlogs() {
		$this->load->model('extension/blog/blog');
		
		//Remove existing
		$query = $this->db->query("SELECT blog_id FROM " . DB_PREFIX . "blog WHERE author = 'John Doe'");

        foreach ($query->rows as $result) {
            if (!empty($result['blog_id'])) {
                $this->model_extension_blog_blog->deleteBlog($result['blog_id']);
            }
        }
		// Add new
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();

        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();
        $blog_stores = array();
        $blog_stores[] = 0;
        foreach ($stores as $result) {
            $blog_stores[] = $result['store_id'];
        }

        $description = array();
        foreach ($languages as $language) {
            $description[$language['language_id']] = array(
                'title' => 'Sample Blog Post Title',
                'description' => '<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for lorem ipsum will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>
				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for lorem ipsum will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>',
				'short_description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English.',
                'page_title' => 'Sample Blog Post',
                'tags' => '',
                'meta_description' => '',
                'meta_keyword' => ''
            );
        }

        $blogs = array();

        $blogs[] = array(
            'author' => 'John Doe',
            'blog_description' => $description,
            'image' => 'catalog/basel-demo/blog.png',
            'status' => '1',
			'sort_order' => '',
			'keyword' => '',
            'allow_comment' => '1',
			'blog_store' => $blog_stores
        );
		$blogs[] = array(
            'author' => 'John Doe',
            'blog_description' => $description,
            'image' => 'catalog/basel-demo/blog.png',
            'status' => '1',
			'sort_order' => '',
			'keyword' => '',
            'allow_comment' => '1',
			'blog_store' => $blog_stores
        );
		$blogs[] = array(
            'author' => 'John Doe',
            'blog_description' => $description,
            'image' => 'catalog/basel-demo/blog.png',
            'status' => '1',
			'sort_order' => '',
			'keyword' => '',
            'allow_comment' => '1',
			'blog_store' => $blog_stores
        );
		$blogs[] = array(
            'author' => 'John Doe',
            'blog_description' => $description,
            'image' => 'catalog/basel-demo/blog.png',
            'status' => '1',
			'sort_order' => '',
			'keyword' => '',
            'allow_comment' => '1',
			'blog_store' => $blog_stores
        );

        foreach ($blogs as $blog) {
        $this->model_extension_blog_blog->addBlog($blog);
        }
    }
	
	/*--------------------------------------------
	------------ ADD PRODUCT GROUPS --------------
	--------------------------------------------*/
	public function addSampleProductGroups() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'showintabs_tab'");
		
		$sql = '{"1":{"title":{"1":"Latest","2":"Latest","3":"Latest","4":"Latest"},"data_source":"PG","product_group":"LA","filter_category":"ALL","filter_manufacturer":"ALL","sort":"pd.name"},"2":{"title":{"1":"Sample Group","2":"Sample Group","3":"Sample Group","4":"Sample Group"},"data_source":"PG","product_group":"LA","filter_category":"ALL","filter_manufacturer":"ALL","sort":"pd.name"},"3":{"title":{"1":"Sample Group 2","2":"Sample Group 2","3":"Sample Group 2","4":"Sample Group 2"},"data_source":"PG","product_group":"LA","filter_category":"ALL","filter_manufacturer":"ALL","sort":"pd.name"}}';
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET "
		. "store_id = '0', "
		. "`code` = 'showintabs', "
		. "`key` = 'showintabs_tab', "
		. "`value` = '" . $sql . "', "
		. "serialized = '1'");
	}
	
	/*--------------------------------------------
	------------- ADD TESTIMONIALS ---------------
	--------------------------------------------*/
	public function addSampleTestimonials() {
	
		$sql = array();
		
		$sql[] = "TRUNCATE TABLE `".DB_PREFIX."testimonial`";
		$sql[] = "
		INSERT INTO `".DB_PREFIX."testimonial` (`testimonial_id`, `name`, `image`, `org`, `status`) VALUES 
		('1', 'John Doe', 'catalog/basel-demo/testimonial-author.png', 'New York', '1'),
		('2', 'Johanna Doe', 'catalog/basel-demo/testimonial-author.png', 'London', '1'),
		('3', 'Karl', 'catalog/basel-demo/testimonial-author.png', 'Stockholm', '1');
		";
		
		$sql[] = "TRUNCATE TABLE `".DB_PREFIX."testimonial_description`";
		$sql[] = "
		INSERT INTO `".DB_PREFIX."testimonial_description` (`testimonial_id`, `language_id`, `description`) VALUES 
		('1', '1', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('2', '1', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('3', '1', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('1', '2', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('2', '2', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('3', '2', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('1', '3', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('2', '3', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('3', '3', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('1', '4', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('2', '4', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.'),
		('3', '4', 'Basel is really a great theme! Diam a vestibulum diam nisi augue dictumst parturient a vestibulum tortor viverra inceptos adipiscing nec a ullamcorper.Ullamcorper aliquam rutrum.');
		";
		
		$sql[] = "TRUNCATE TABLE `".DB_PREFIX."testimonial_to_store`";
		$sql[] = "
		INSERT INTO `".DB_PREFIX."testimonial_to_store` (`testimonial_id`, `store_id`) VALUES 
		('1', '0'),
		('2', '0'),
		('3', '0');
		";

		foreach( $sql as $q ){
			$query = $this->db->query($q);
		}
	}
	
	
	
	/*--------------------------------------------
	------------ ADD MEGA MENU ITEMS -------------
	--------------------------------------------*/
	public function addSampleMenuItems() {
		
	$this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "mega_menu`");
	
	$c_id = $this->getCategoryId1();
	
	$oc_mega_menu = array(
  array('id' => '1','parent_id' => '0','rang' => '0','icon' => 'no_image.png','name' => 'a:4:{i:1;s:4:"Home";i:4;s:4:"Home";i:3;s:4:"Home";i:2;s:4:"Home";}','module_id' => '32','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '2','parent_id' => '0','rang' => '1','icon' => 'catalog/basel-demo/menu-shop-bg.jpg','name' => 'a:3:{i:1;s:4:"Shop";i:3;s:4:"Shop";i:2;s:4:"Shop";}','module_id' => '32','link' => '','description' => 'a:3:{i:1;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";i:3;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";i:2;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'right top','submenu_width' => '800','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '3','parent_id' => '2','rang' => '3','icon' => 'no_image.png','name' => 'a:3:{i:1;s:5:"Women";i:3;s:5:"Women";i:2;s:5:"Women";}','module_id' => '32','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '3','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '70','parent_id' => '2','rang' => '2','icon' => 'no_image.png','name' => 'a:3:{i:1;s:3:"Men";i:3;s:3:"Men";i:2;s:3:"Men";}','module_id' => '32','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '3','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '71','parent_id' => '8','rang' => '7','icon' => 'no_image.png','name' => 'a:3:{i:1;s:9:"Sub items";i:3;s:9:"Sub items";i:2;s:9:"Sub items";}','module_id' => '32','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '12','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.',"children":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.',"children":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}]},{"name":"Sample Category","id":'.$c_id.'}]},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '7','parent_id' => '0','rang' => '8','icon' => 'no_image.png','name' => 'a:3:{i:1;s:4:"Blog";i:3;s:4:"Blog";i:2;s:4:"Blog";}','module_id' => '32','link' => 'index.php?route=extension/blog/home','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '5','parent_id' => '2','rang' => '4','icon' => 'no_image.png','name' => 'a:3:{i:1;s:11:"Accessories";i:3;s:11:"Accessories";i:2;s:11:"Accessories";}','module_id' => '32','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '6','content_type' => '0','content' => '{"html":{"text":{"1":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;","3":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;","2":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;"}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '8','parent_id' => '0','rang' => '6','icon' => 'no_image.png','name' => 'a:3:{i:1;s:11:"Accessories";i:3;s:11:"Accessories";i:2;s:11:"Accessories";}','module_id' => '32','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '200','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '9','parent_id' => '0','rang' => '5','icon' => 'no_image.png','name' => 'a:3:{i:1;s:4:"Kids";i:3;s:4:"Kids";i:2;s:4:"Kids";}','module_id' => '32','link' => '#','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '29','parent_id' => '28','rang' => '1','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '30','parent_id' => '28','rang' => '2','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '31','parent_id' => '28','rang' => '3','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '32','parent_id' => '28','rang' => '4','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '33','parent_id' => '28','rang' => '5','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '34','parent_id' => '0','rang' => '6','icon' => 'catalog/basel-demo/menu-belt.png','name' => 'a:4:{i:1;s:16:"Men’s Clothing";i:4;s:16:"Men’s Clothing";i:3;s:16:"Men’s Clothing";i:2;s:16:"Men’s Clothing";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'right bottom','submenu_width' => '825','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '35','parent_id' => '34','rang' => '7','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '36','parent_id' => '34','rang' => '8','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '37','parent_id' => '34','rang' => '9','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '38','parent_id' => '34','rang' => '10','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '28','parent_id' => '0','rang' => '0','icon' => 'catalog/basel-demo/menu-female.jpg','name' => 'a:4:{i:1;s:18:"Women’s Clothing";i:4;s:18:"Women’s Clothing";i:3;s:18:"Women’s Clothing";i:2;s:18:"Women’s Clothing";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'right bottom','submenu_width' => '825','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '39','parent_id' => '34','rang' => '11','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '40','parent_id' => '0','rang' => '12','icon' => 'no_image.png','name' => 'a:4:{i:1;s:11:"Watches Man";i:4;s:11:"Watches Man";i:3;s:11:"Watches Man";i:2;s:11:"Watches Man";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '41','parent_id' => '0','rang' => '13','icon' => 'no_image.png','name' => 'a:4:{i:1;s:16:"Bags &amp; Shoes";i:4;s:16:"Bags &amp; Shoes";i:3;s:16:"Bags &amp; Shoes";i:2;s:16:"Bags &amp; Shoes";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '42','parent_id' => '0','rang' => '14','icon' => 'no_image.png','name' => 'a:4:{i:1;s:7:"Jewelry";i:4;s:7:"Jewelry";i:3;s:7:"Jewelry";i:2;s:7:"Jewelry";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '43','parent_id' => '0','rang' => '15','icon' => 'no_image.png','name' => 'a:4:{i:1;s:11:"Accessories";i:4;s:11:"Accessories";i:3;s:11:"Accessories";i:2;s:11:"Accessories";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '44','parent_id' => '0','rang' => '16','icon' => 'no_image.png','name' => 'a:4:{i:1;s:4:"Bags";i:4;s:4:"Bags";i:3;s:4:"Bags";i:2;s:4:"Bags";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '45','parent_id' => '0','rang' => '17','icon' => 'no_image.png','name' => 'a:4:{i:1;s:21:"Toys, Kids &amp; Baby";i:4;s:21:"Toys, Kids &amp; Baby";i:3;s:21:"Toys, Kids &amp; Baby";i:2;s:21:"Toys, Kids &amp; Baby";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '46','parent_id' => '0','rang' => '18','icon' => 'no_image.png','name' => 'a:4:{i:1;s:21:"Sports &amp; Outdoors";i:4;s:21:"Sports &amp; Outdoors";i:3;s:21:"Sports &amp; Outdoors";i:2;s:21:"Sports &amp; Outdoors";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '47','parent_id' => '0','rang' => '19','icon' => 'no_image.png','name' => 'a:4:{i:1;s:19:"Health &amp; Beauty";i:4;s:19:"Health &amp; Beauty";i:3;s:19:"Health &amp; Beauty";i:2;s:19:"Health &amp; Beauty";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '48','parent_id' => '0','rang' => '20','icon' => 'no_image.png','name' => 'a:4:{i:1;s:9:"Furniture";i:4;s:9:"Furniture";i:3;s:9:"Furniture";i:2;s:9:"Furniture";}','module_id' => '35','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '49','parent_id' => '0','rang' => '6','icon' => 'catalog/basel-demo/menu-belt.png','name' => 'a:4:{i:1;s:16:"Men’s Clothing";i:4;s:16:"Men’s Clothing";i:3;s:16:"Men’s Clothing";i:2;s:16:"Men’s Clothing";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'right bottom','submenu_width' => '825','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-male','class_menu' => ''),
  array('id' => '50','parent_id' => '49','rang' => '7','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '51','parent_id' => '49','rang' => '8','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '52','parent_id' => '49','rang' => '9','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '53','parent_id' => '49','rang' => '10','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '54','parent_id' => '49','rang' => '11','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '55','parent_id' => '0','rang' => '0','icon' => 'catalog/basel-demo/menu-female.jpg','name' => 'a:4:{i:1;s:18:"Women’s Clothing";i:4;s:18:"Women’s Clothing";i:3;s:18:"Women’s Clothing";i:2;s:18:"Women’s Clothing";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'right bottom','submenu_width' => '825','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-female','class_menu' => ''),
  array('id' => '56','parent_id' => '55','rang' => '1','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '57','parent_id' => '55','rang' => '2','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '58','parent_id' => '55','rang' => '3','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '59','parent_id' => '55','rang' => '4','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '60','parent_id' => '55','rang' => '5','icon' => 'no_image.png','name' => 'a:4:{i:1;s:8:"Sub item";i:4;s:8:"Sub item";i:3;s:8:"Sub item";i:2;s:8:"Sub item";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '2','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '61','parent_id' => '0','rang' => '12','icon' => 'no_image.png','name' => 'a:4:{i:1;s:11:"Watches Man";i:4;s:11:"Watches Man";i:3;s:11:"Watches Man";i:2;s:11:"Watches Man";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-clock-o','class_menu' => ''),
  array('id' => '62','parent_id' => '0','rang' => '13','icon' => 'no_image.png','name' => 'a:4:{i:1;s:16:"Bags &amp; Shoes";i:4;s:16:"Bags &amp; Shoes";i:3;s:16:"Bags &amp; Shoes";i:2;s:16:"Bags &amp; Shoes";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-briefcase','class_menu' => ''),
  array('id' => '63','parent_id' => '0','rang' => '14','icon' => 'no_image.png','name' => 'a:4:{i:1;s:7:"Jewelry";i:4;s:7:"Jewelry";i:3;s:7:"Jewelry";i:2;s:7:"Jewelry";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-diamond','class_menu' => ''),
  array('id' => '64','parent_id' => '0','rang' => '15','icon' => 'no_image.png','name' => 'a:4:{i:1;s:11:"Accessories";i:4;s:11:"Accessories";i:3;s:11:"Accessories";i:2;s:11:"Accessories";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-star','class_menu' => ''),
  array('id' => '65','parent_id' => '0','rang' => '16','icon' => 'no_image.png','name' => 'a:4:{i:1;s:4:"Bags";i:4;s:4:"Bags";i:3;s:4:"Bags";i:2;s:4:"Bags";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-shopping-cart','class_menu' => ''),
  array('id' => '66','parent_id' => '0','rang' => '17','icon' => 'no_image.png','name' => 'a:4:{i:1;s:21:"Toys, Kids &amp; Baby";i:4;s:21:"Toys, Kids &amp; Baby";i:3;s:21:"Toys, Kids &amp; Baby";i:2;s:21:"Toys, Kids &amp; Baby";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-child','class_menu' => ''),
  array('id' => '67','parent_id' => '0','rang' => '18','icon' => 'no_image.png','name' => 'a:4:{i:1;s:21:"Sports &amp; Outdoors";i:4;s:21:"Sports &amp; Outdoors";i:3;s:21:"Sports &amp; Outdoors";i:2;s:21:"Sports &amp; Outdoors";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-futbol-o','class_menu' => ''),
  array('id' => '68','parent_id' => '0','rang' => '19','icon' => 'no_image.png','name' => 'a:4:{i:1;s:19:"Health &amp; Beauty";i:4;s:19:"Health &amp; Beauty";i:3;s:19:"Health &amp; Beauty";i:2;s:19:"Health &amp; Beauty";}','module_id' => '36','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => 'fa fa-anchor','class_menu' => ''),
  array('id' => '74','parent_id' => '0','rang' => '0','icon' => 'no_image.png','name' => 'a:4:{i:1;s:4:"Home";i:4;s:4:"Home";i:3;s:4:"Home";i:2;s:4:"Home";}','module_id' => '52','link' => '','description' => 'a:4:{i:1;s:0:"";i:4;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","4":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '75','parent_id' => '0','rang' => '1','icon' => 'catalog/basel-demo/menu-shop-bg.jpg','name' => 'a:3:{i:1;s:4:"Shop";i:3;s:4:"Shop";i:2;s:4:"Shop";}','module_id' => '52','link' => '','description' => 'a:3:{i:1;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";i:3;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";i:2;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'right top','submenu_width' => '800','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '76','parent_id' => '75','rang' => '2','icon' => 'no_image.png','name' => 'a:3:{i:1;s:3:"Men";i:3;s:3:"Men";i:2;s:3:"Men";}','module_id' => '52','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '3','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Watches","id":61},{"name":"Kids","id":62},{"name":"Women\\u00a0\\u00a0\\u003E\\u00a0\\u00a0T-Shirts","id":73},{"name":"Electronics","id":79},{"name":"Furniture","id":77},{"name":"Accessories","id":59},{"name":"Mens\\u00a0\\u00a0\\u003E\\u00a0\\u00a0Jackets","id":65},{"name":"Women\\u00a0\\u00a0\\u003E\\u00a0\\u00a0Underwear","id":74},{"name":"Mens\\u00a0\\u00a0\\u003E\\u00a0\\u00a0Trousers & Chinos","id":67},{"name":"Watches","id":61}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '77','parent_id' => '75','rang' => '3','icon' => 'no_image.png','name' => 'a:3:{i:1;s:5:"Women";i:3;s:5:"Women";i:2;s:5:"Women";}','module_id' => '52','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '3','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Women\\u00a0\\u00a0\\u003E\\u00a0\\u00a0T-Shirts","id":73},{"name":"Women\\u00a0\\u00a0\\u003E\\u00a0\\u00a0Underwear","id":74},{"name":"Mens\\u00a0\\u00a0\\u003E\\u00a0\\u00a0Jackets","id":65},{"name":"Watches","id":61},{"name":"Accessories","id":59},{"name":"Mens\\u00a0\\u00a0\\u003E\\u00a0\\u00a0Trousers & Chinos","id":67},{"name":"Electronics","id":79},{"name":"Watches","id":61},{"name":"Furniture","id":77}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '78','parent_id' => '75','rang' => '4','icon' => 'no_image.png','name' => 'a:3:{i:1;s:11:"Accessories";i:3;s:11:"Accessories";i:2;s:11:"Accessories";}','module_id' => '52','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '6','content_type' => '0','content' => '{"html":{"text":{"1":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;","3":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;","2":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;"}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '79','parent_id' => '0','rang' => '5','icon' => 'no_image.png','name' => 'a:3:{i:1;s:4:"Blog";i:3;s:4:"Blog";i:2;s:4:"Blog";}','module_id' => '52','link' => 'index.php?route=extension/blog/home','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '84','parent_id' => '0','rang' => '1','icon' => 'catalog/basel-demo/menu-shop-bg.jpg','name' => 'a:3:{i:1;s:4:"Shop";i:3;s:4:"Shop";i:2;s:4:"Shop";}','module_id' => '55','link' => '','description' => 'a:3:{i:1;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";i:3;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";i:2;s:55:"&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'right top','submenu_width' => '800','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  
  array('id' => '85','parent_id' => '84','rang' => '2','icon' => 'no_image.png','name' => 'a:3:{i:1;s:3:"Men";i:3;s:3:"Men";i:2;s:3:"Men";}','module_id' => '55','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '3','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  
  array('id' => '83','parent_id' => '0','rang' => '0','icon' => 'no_image.png','name' => 'a:3:{i:1;s:4:"Home";i:3;s:4:"Home";i:2;s:4:"Home";}','module_id' => '55','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  
  array('id' => '86','parent_id' => '84','rang' => '2','icon' => 'no_image.png','name' => 'a:3:{i:1;s:5:"Women";i:3;s:5:"Women";i:2;s:5:"Women";}','module_id' => '55','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '3','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  
  array('id' => '87','parent_id' => '84','rang' => '4','icon' => 'no_image.png','name' => 'a:3:{i:1;s:11:"Accessories";i:3;s:11:"Accessories";i:2;s:11:"Accessories";}','module_id' => '55','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '1','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '6','content_type' => '0','content' => '{"html":{"text":{"1":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;","3":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;","2":"&lt;ul&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Kids playground&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;E-cigarettes&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Socks &amp; footwear&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bags &amp; luggage&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Social shopping&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Autumn collection&lt;i class=&quot;menu-tag hot&quot;&gt;HOT&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Jewellary&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Outgoind items&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;\\/i&gt;&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;li&gt;&lt;a href=&quot;index.php?route=product\\/category&amp;path='.$c_id.'&quot;&gt;Bedroom accessories&lt;\\/a&gt;&lt;\\/li&gt;\\r\\n&lt;\\/ul&gt;"}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '91','parent_id' => '0','rang' => '5','icon' => 'no_image.png','name' => 'a:3:{i:1;s:4:"Kids";i:3;s:4:"Kids";i:2;s:4:"Kids";}','module_id' => '55','link' => '#','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '97','parent_id' => '0','rang' => '2','icon' => 'no_image.png','name' => 'a:3:{i:1;s:4:"Blog";i:3;s:4:"Blog";i:2;s:4:"Blog";}','module_id' => '54','link' => 'index.php?route=extension/blog/home','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '98','parent_id' => '0','rang' => '0','icon' => 'no_image.png','name' => 'a:3:{i:1;s:11:"Accessories";i:3;s:11:"Accessories";i:2;s:11:"Accessories";}','module_id' => '54','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '1','show_title' => '0','position' => 'left top','submenu_width' => '200','submenu_type' => '0','content_width' => '4','content_type' => '0','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => ''),
  array('id' => '99','parent_id' => '98','rang' => '1','icon' => 'no_image.png','name' => 'a:3:{i:1;s:9:"Sub items";i:3;s:9:"Sub items";i:2;s:9:"Sub items";}','module_id' => '54','link' => '','description' => 'a:3:{i:1;s:0:"";i:3;s:0:"";i:2;s:0:"";}','new_window' => '0','status' => '0','disp_mobile_item' => '1','item_type' => '0','show_title' => '0','position' => 'left top','submenu_width' => '600','submenu_type' => '0','content_width' => '12','content_type' => '2','content' => '{"html":{"text":{"1":"","3":"","2":""}},"product":{"id":"","name":"","img_w":"262","img_h":"334"},"image":{"link":"no_image.png"},"categories":{"categories":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.',"children":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.',"children":[{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}]}]},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'},{"name":"Sample Category","id":'.$c_id.'}],"columns":"1","submenu":"1","submenu_columns":"1"}}','icon_font' => '','class_menu' => '')
);
    foreach ($oc_mega_menu as $result) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "mega_menu` SET "
                . "id = '" . $result['id'] . "', "
				. "parent_id = '" . $result['parent_id'] . "', "
				. "rang = '" . $result['rang'] . "', "
				. "icon = '" . $result['icon'] . "', "
				. "name = '" . $result['name'] . "', "
				. "module_id = '" . $result['module_id'] . "', "
				. "link = '" . $result['link'] . "', "
				. "description = '" . $result['description'] . "', "
				. "new_window = '" . $result['new_window'] . "', "
				. "status = '" . $result['status'] . "', "
				. "disp_mobile_item = '" . $result['disp_mobile_item'] . "', "
				. "item_type = '" . $result['item_type'] . "', "
				. "show_title = '" . $result['show_title'] . "', "
				. "position = '" . $result['position'] . "', "
				. "submenu_width = '" . $result['submenu_width'] . "', "
				. "submenu_type = '" . $result['submenu_type'] . "', "
				. "content_width = '" . $result['content_width'] . "', "
				. "content_type = '" . $result['content_type'] . "', "
				. "content = '" . addslashes($result['content']) . "', "
				. "icon_font = '" . $result['icon_font'] . "', "
                . "class_menu = '" . $result['class_menu'] . "'");
    	}
	}

	
	/*--------------------------------------------
	---------- ADD SAMPLE BANNER ITEMS -----------
	--------------------------------------------*/
	public function addSampleBanners() {
	
	$this->load->model('design/banner');
	
	$existing = $this->db->query("SELECT banner_id FROM " . DB_PREFIX . "banner WHERE name = 'Sample Brands'");
	
	if (!empty($existing->row['banner_id'])) { 
	$this->model_design_banner->deleteBanner($existing->row['banner_id']);
	} 
	
	$content = json_decode('
	{"name":"Sample Brands","status":"1","banner_image":
	{
		"1":[
		{"title":"Brand 1","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"1"},
		{"title":"Brand 2","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"2"},
		{"title":"Brand 3","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"3"},
		{"title":"Brand 4","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"4"},
		{"title":"Brand 5","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"5"},
		{"title":"Brand 6","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"6"},
		{"title":"Brand 7","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"7"}
		],
		"2":{
		"7":{"title":"Brand 1","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"1"},
		"8":{"title":"Brand 2","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"2"},
		"9":{"title":"Brand 3","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"3"},
		"10":{"title":"Brand 4","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"4"},
		"11":{"title":"Brand 5","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"5"},
		"12":{"title":"Brand 6","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"6"},
		"13":{"title":"Brand 7","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"7"}
		},
		"3":{
		"14":{"title":"Brand 1","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"1"},
		"15":{"title":"Brand 2","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"2"},
		"16":{"title":"Brand 3","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"3"},
		"17":{"title":"Brand 4","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"4"},
		"18":{"title":"Brand 5","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"5"},
		"19":{"title":"Brand 6","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"6"},
		"20":{"title":"Brand 7","link":"","image":"catalog\/basel-demo\/sample-brand.png","sort_order":"7"}
		}}} 
	', true);
	
	$this->model_design_banner->addBanner($content);

	}
	
	public function getBannerId() {
		$banner_id = $this->db->query("SELECT banner_id FROM " . DB_PREFIX . "banner WHERE name = 'Sample Brands'");
		if (!empty($banner_id->row['banner_id'])) { return $banner_id->row['banner_id']; } else { return 0; }
	}
	
	
	
}
