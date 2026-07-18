<?php
class ModelExtensionModuleMegaSalesPro extends Model {
	
  public function add_new_tables() {

    $query1 = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "mega_sales' ");
    $msp_exist = count($query1->rows);

    if ($msp_exist==0) {
      $this->db->query(" CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "mega_sales (
        id int(11) AUTO_INCREMENT, 
        date_start varchar(50), 
        date_end varchar(50), 
        discount_value int(11), 
        discount_type varchar(20),
        exclude_child int(3),
        remove_individual_specials int(3),
        priority int(3),
        round_prices int(3),
        PRIMARY KEY (id) ) ");

      $this->db->query(" CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "mega_exclude_products (
        id int(11) AUTO_INCREMENT, 
        sale_id varchar(11), 
        product_id varchar(11),
        PRIMARY KEY (id) ) ");  

      $this->db->query(" CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "mega_category_to_sale (
        id int(11) AUTO_INCREMENT, 
        sale_id varchar(11), 
        category_id varchar(11),
        PRIMARY KEY (id) ) ");  

      $this->db->query(" CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "mega_customer_group_to_sale (
        id int(11) AUTO_INCREMENT, 
        sale_id varchar(11), 
        customer_group_id varchar(11),
        PRIMARY KEY (id) ) ");  

      $this->db->query(" CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "mega_manufacturer_to_sale (
        id int(11) AUTO_INCREMENT, 
        sale_id varchar(11), 
        manufacturer_id varchar(11),
        PRIMARY KEY (id) ) ");

      $this->db->query(" CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "mega_filter_to_sale (
        id int(11) AUTO_INCREMENT, 
        sale_id varchar(11), 
        filter_id varchar(11),
        PRIMARY KEY (id) ) ");
    }

    return 1;
  }

  public function get_selected_customer_group_ids($sale_id) {

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_customer_group_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $customer_groups_array = $query->rows;

    $results = array();

    if (!empty($customer_groups_array)) {
      foreach ($customer_groups_array as $one_customer_group) {
        $results[] =  $one_customer_group['customer_group_id'];
      }
    }

    return $results;
  }

  public function get_selected_filter_ids($sale_id) {

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_filter_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $filters_array = $query->rows;

    $results = array();

    if (!empty($filters_array)) {
      foreach ($filters_array as $one_filter) {
        $results[] =  $one_filter['filter_id'];
      }
    }

    return $results;
  }

  public function get_selected_category_ids($sale_id) {

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_category_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $categories_array = $query->rows;

    $results = array();

    if (!empty($categories_array)) {
      foreach ($categories_array as $one_category) {
        $results[] =  $one_category['category_id'];
      }
    }

    return $results;
  }

  public function get_selected_manufacturer_ids($sale_id) {

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_manufacturer_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $manufacturers_array = $query->rows;

    $results = array();

    if (!empty($manufacturers_array)) {
      foreach ($manufacturers_array as $one_manufacturer) {
        $results[] =  $one_manufacturer['manufacturer_id'];
      }
    }

    return $results;
  }

  public function get_sale_excluded_products($sale_id) {
    $this->load->model('catalog/product');

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_exclude_products WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $products_array = $query->rows;

    $results = array();

    if (!empty($products_array)) {
      foreach ($products_array as $product) {
        $results[] =  $this->model_catalog_product->getProduct($product['product_id']);
      }
    }

    return $results;
  }

  public function get_sale_categories($sale_id) {
    $this->load->model('catalog/category');

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_category_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $categories_array = $query->rows;

    $results = array();

    if (!empty($categories_array)) {
      foreach ($categories_array as $category) {
        $results[] =  $this->model_catalog_category->getCategory($category['category_id']);
      }
    }

    return $results;
  }

  public function get_sale_manufacturers($sale_id) {
    $this->load->model('catalog/manufacturer');

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_manufacturer_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $manufacturers_array = $query->rows;

    $results = array();

    if (!empty($manufacturers_array)) {
      foreach ($manufacturers_array as $manufacturer) {
        $results[] =  $this->model_catalog_manufacturer->getManufacturer($manufacturer['manufacturer_id']);
      }
    }

    return $results;
  }

  public function get_sale_customer_groups($sale_id) {
    $this->load->model('customer/customer_group');

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_customer_group_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $customer_groups_array = $query->rows;

    $results = array();

    if (!empty($customer_groups_array)) {
      foreach ($customer_groups_array as $one_group) {
        $results[] =  $this->model_customer_customer_group->getCustomerGroup($one_group['customer_group_id']);
      }
    }

    return $results;
  }

  public function get_sale_filters($sale_id) {
    $this->load->model('catalog/filter');

    $sql = "SELECT * FROM " . DB_PREFIX . "mega_filter_to_sale WHERE sale_id = '" . (int)$sale_id . "'";
    $query = $this->db->query($sql);
    $filters_array = $query->rows;

    $results = array();

    if (!empty($filters_array)) {
      foreach ($filters_array as $one_filter) {
        $results[] =  $this->model_catalog_filter->getFilter($one_filter['filter_id']);
      }
    }

    return $results;
  }


  public function remove_all_specials() {
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special");
    $this->db->query("DELETE FROM " . DB_PREFIX . "mega_sales");
    $this->db->query("DELETE FROM " . DB_PREFIX . "mega_filter_to_sale");
    $this->db->query("DELETE FROM " . DB_PREFIX . "mega_category_to_sale");
    $this->db->query("DELETE FROM " . DB_PREFIX . "mega_manufacturer_to_sale");
    $this->db->query("DELETE FROM " . DB_PREFIX . "mega_customer_group_to_sale");
    $this->db->query("DELETE FROM " . DB_PREFIX . "mega_exclude_products");
  }


  public function new_sale_to_db($array) {

    $this->load->model('catalog/product');
    $this->db->query("INSERT INTO " . DB_PREFIX . "mega_sales(
          date_start,
          date_end,
          discount_value,
          discount_type,
          exclude_child,
          remove_individual_specials,
          priority,
          round_prices
          ) VALUES(
          '".$array['date_start']."',
          '".$array['date_end']."',
          '".$array['discount_value']."',
          '".$array['discount_type']."',
          '".$array['exclude_child']."',
          '".$array['remove_individual_specials']."',
          '".$array['priority']."',
          '".$array['round_prices']."'
          )");

    $product_id = $this->db->getLastId();
    return $product_id;
  }

  public function customer_groups_to_sale($array) {
    $this->load->model('catalog/product');

    if ( (!empty($array['customer_groups'])) && (!empty($array['id'])) ) {
      foreach ($array['customer_groups'] as $customer_group_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mega_customer_group_to_sale(
          customer_group_id,
          sale_id
          ) VALUES(
          '".$customer_group_id."',
          '".$array['id']."'
          )");
      }
    }

    return 1;
  }

  public function categories_to_sale($array) {
    $this->load->model('catalog/product');

    if ( (!empty($array['categories'])) && (!empty($array['id'])) ) {
      foreach ($array['categories'] as $category_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mega_category_to_sale(
          category_id,
          sale_id
          ) VALUES(
          '".$category_id."',
          '".$array['id']."'
          )");
      }
    }

    return 1;
  }

  public function manufacturers_to_sale($array) {
    $this->load->model('catalog/product');

    if ( (!empty($array['manufacturers'])) && (!empty($array['id'])) ) {
      foreach ($array['manufacturers'] as $manufacturer_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mega_manufacturer_to_sale(
          manufacturer_id,
          sale_id
          ) VALUES(
          '".$manufacturer_id."',
          '".$array['id']."'
          )");
      }
    }

    return 1;
  }

  public function filters_to_sale($array) {
    $this->load->model('catalog/product');

    if ( (!empty($array['filters'])) && (!empty($array['id'])) ) {
      foreach ($array['filters'] as $filter_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mega_filter_to_sale(
          filter_id,
          sale_id
          ) VALUES(
          '".$filter_id."',
          '".$array['id']."'
          )");
      }
    }

    return 1;
  }

  public function exclude_products($array) {
    $this->load->model('catalog/product');

    if ( (!empty($array['exclude_products'])) && (!empty($array['id'])) ) {
      foreach ($array['exclude_products'] as $product_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "mega_exclude_products(
          product_id,
          sale_id
          ) VALUES(
          '".$product_id."',
          '".$array['id']."'
          )");
      }
    }

    return 1;
  }

}
?>