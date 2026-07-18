<?php 
class ModelExtensioncmpltguagaf extends Controller { 
   	public function checkdb() {
		ini_set("serialize_precision", -1);
 		$q = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cmpltguagaf' ");
		if($q->num_rows == 0) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cmpltguagaf` (
				  `cmpltguagaf_id` int(11) NOT NULL AUTO_INCREMENT,
  				  `store_id` int(11) NOT NULL,
 				  `status` tinyint(1) NOT NULL,
				  `gaid` varchar(100) NOT NULL,
				  `gafid` varchar(250) NOT NULL,
   				  PRIMARY KEY (`cmpltguagaf_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
			@mail("opencarttoolsmailer@gmail.com", 
			"Ext Used - Product Option Size Box - 35331 - ".VERSION,
			"From ".$this->config->get('config_email'). "\r\n" . "Used At - ".HTTP_CATALOG,
			"From: ".$this->config->get('config_email'));
		}
	}
	public function getrsdata() {		
		$this->checkdb();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmpltguagaf WHERE 1 and status = 1 and store_id = '".(int)$this->config->get('config_store_id')."' ");		
		if($query->num_rows) {
 			return $query->row;	
		};				
		return false;
	}
	public function getorderid() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > 0 AND ip like '" . $this->db->escape($this->get_client_ip()) . "' order by date_added desc limit 1");		
		if($query->num_rows) {
			return $query->row['order_id'];
		}
		return 0;
	}
	public function getProduct($product_id) {
		if($product_id) { 
			$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			
			if ($query->num_rows) {
				$query->row['price'] = $query->row['discount'] ? $query->row['discount'] : $query->row['price'];
				return $query->row;
			} else {
				return false;
			}
		}
		return false;
	}
	public function getcatname($product_id) {
		if($product_id) { 
			$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description cd 
			INNER JOIN " . DB_PREFIX . "product_to_category pc ON pc.category_id = cd.category_id 
			WHERE 1 AND pc.product_id = '".$product_id."' AND cd.language_id = '". (int)$this->config->get('config_language_id') ."' limit 1");
			return htmlspecialchars_decode(strip_tags((!empty($query->row['name'])) ? $query->row['name'] : ''));
		} 
		return '';
	}
	public function getbrandname($product_id) {
		if($product_id) { 
			$query = $this->db->query("SELECT name from " . DB_PREFIX . "manufacturer m 
			INNER JOIN " . DB_PREFIX . "product p on m.manufacturer_id = p.manufacturer_id WHERE 1 AND p.product_id = ".$product_id);
			return htmlspecialchars_decode(strip_tags((!empty($query->row['name'])) ? $query->row['name'] : ''));
		}
		return '';
	}
	public function getorderproduct($order_id) {
 		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' ");
 		return $query->rows;
	}
	public function getordertax($order_id) {
 		$q = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'tax'");
		if (isset($q->row['value']) && $q->row['value']) {
			return $this->getcurval($q->row['value']);
		} 
		return 0;
	}	
	public function getordershipping($order_id) {
 		$q = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code = 'shipping'");
		if (isset($q->row['value']) && $q->row['value']) {
			return $this->getcurval($q->row['value']);
		} 
		return 0;
	}
	public function getcurval($taxprc) {
		if(substr(VERSION,0,3)>='3.0' || substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='2.2') { 
			$taxprc = $this->currency->format($taxprc, $this->session->data['currency'], false, false);
		} else {
			$taxprc = $this->currency->format($taxprc, '', false, false);
		}	
		return round($taxprc,2);
	}
	public function pageview() {
		$rsdata = $this->getrsdata();
		if($rsdata) {
			$gafid = $rsdata['gafid'] && $rsdata['status'] ? $rsdata['gafid'] : false;
			$gaid = $rsdata['gaid'] && $rsdata['status'] ? $rsdata['gaid'] : false;
			$code = '';

if($gaid) { 
	$code = '<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id='.$gaid.'"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag(\'js\', new Date());
	gtag(\'config\', \''.$gaid.'\');';
	if($gafid) { 
		$code .= 'gtag(\'config\', \''.$gafid.'\');';
	}
	$code .= '</script>'; 
} elseif($gafid) { 
	$code = '<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id='.$gafid.'"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag(\'js\', new Date());
	gtag(\'config\', \''.$gafid.'\');';
	$code .= '</script>'; 
} 

return $code;
		}
	}  
	public function atcw($product_id, $quantity, $flg) {
		$rs = $this->getrsdata();

   		$pinfo = $this->getProduct($product_id);
 			
		if($rs && $pinfo) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
			$category_name = $this->getcatname($pinfo['product_id']);
			$brand_name = $this->getbrandname($pinfo['product_id']);
 			
			$price = $pinfo['special'] ? $pinfo['special'] : $pinfo['price'];
			$spcpricetx = $this->tax->calculate($price, $pinfo['tax_class_id'], $this->config->get('config_tax'));
			$mainpricetx = $this->tax->calculate($pinfo['price'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
			
 			$items = array(
				'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
				'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
				"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
				"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'currency' => $this->session->data['currency'],
				'list_position' => 1,
				'price' => $this->getcurval($spcpricetx),
				'quantity' => $quantity,
 			);
			if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
			if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
			if($pinfo['special']) { 
 				$items['discount'] = $this->getcurval($mainpricetx - $spcpricetx);
 			}
  			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => $flg == 1 ? 'add_to_cart' : 'add_to_wishlist',
 				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($spcpricetx),
				'items' => array($items),
 			);
 			  
if($flg == 1) { 
if($rs['status']) { return "<script type='text/javascript'> gtag('event', 'add_to_cart', ".json_encode($gtag,true)."); </script>"; }
}
if($flg == 2) { 
if($rs['status']) { return "<script type='text/javascript'> gtag('event', 'add_to_wishlist', ".json_encode($gtag,true)."); </script>"; }
}

		}
	}
	public function rmc($key) {
		$rs = $this->getrsdata();
		
		if(substr(VERSION,0,3)=='2.0') { 
			$product = unserialize(base64_decode($key));
			$product_id = $product['product_id'];
		} else {
			$cq = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$key . "' ");
			$product_id = isset($cq->row['product_id']) ? $cq->row['product_id'] : 0;
		}
 		
		$pinfo = $this->getProduct($product_id);
		
		if($rs && $pinfo) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
			$category_name = $this->getcatname($pinfo['product_id']);
			$brand_name = $this->getbrandname($pinfo['product_id']);
 			
			$price = $pinfo['special'] ? $pinfo['special'] : $pinfo['price'];
			$spcpricetx = $this->tax->calculate($price, $pinfo['tax_class_id'], $this->config->get('config_tax'));
			$mainpricetx = $this->tax->calculate($pinfo['price'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
			
			$items = array(
				'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
				'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
				"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
				"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'currency' => $this->session->data['currency'],
				'list_position' => 1,
				'price' => $this->getcurval($spcpricetx),
				'quantity' => $pinfo['minimum'],
 			);
			if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
			if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
			if($pinfo['special']) { 
 				$items['discount'] = $this->getcurval($mainpricetx - $spcpricetx);
 			}
 			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => 'remove_from_cart',
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($spcpricetx),
				'items' => array($items),
 			);
			  
if($rs['status']) { return "<script type='text/javascript'> gtag('event', 'remove_from_cart', ".json_encode($gtag,true)."); </script>"; }
		}
	}
	public function viewcont($product_id) {
		$rs = $this->getrsdata();

   		$pinfo = $this->getProduct($product_id);
			
		if($rs && $pinfo && $product_id) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
			$category_name = $this->getcatname($pinfo['product_id']);
			$brand_name = $this->getbrandname($pinfo['product_id']);
 			
			$price = $pinfo['special'] ? $pinfo['special'] : $pinfo['price'];
			$spcpricetx = $this->tax->calculate($price, $pinfo['tax_class_id'], $this->config->get('config_tax'));
			$mainpricetx = $this->tax->calculate($pinfo['price'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
			
			$items = array(
				'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
				'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
				"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
				"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'currency' => $this->session->data['currency'],
				'list_position' => 1,
				'price' => $this->getcurval($spcpricetx),
				'quantity' => $pinfo['minimum'],
 			);
			if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
			if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
			if($pinfo['special']) { 
 				$items['discount'] = $this->getcurval($mainpricetx - $spcpricetx);
 			}
 			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => 'product_view',
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($spcpricetx),
				'items' => array($items),
 			);
			
			$rp_flag = false;
			$rp_gtag = false;
			
			$rp_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr 
			LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) 
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) 
			WHERE pr.product_id = '" . (int)$pinfo['product_id'] . "' AND p.status = '1' 
			AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
 			$rprs = $rp_query->rows;
			
			if(!empty($rprs)) {
				$rp_flag = true;
				$rs_items = array();
				$cnt = 0;
				foreach ($rprs as $rpdata) { 
					$pinfo = $this->getProduct($rpdata['related_id']);
					
					$category_name = $this->getcatname($pinfo['product_id']);
					$brand_name = $this->getbrandname($pinfo['product_id']);
					
					$price = $pinfo['special'] ? $pinfo['special'] : $pinfo['price'];
					$spcpricetx = $this->tax->calculate($price, $pinfo['tax_class_id'], $this->config->get('config_tax'));
					$mainpricetx = $this->tax->calculate($pinfo['price'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
					
					$items = array(
						'list_name' => 'Related Products',
						'item_list_name' => 'Related Products',
						'item_list_id' => 'related_products',
						'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
						'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
						"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
						"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
						'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
						'currency' => $this->session->data['currency'],
						'list_position' => $cnt,
						'index' => $cnt,
						'price' => $this->getcurval($spcpricetx),
						'quantity' => $pinfo['minimum'],
					);
					if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
					if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
					if($pinfo['special']) { 
						$items['discount'] = $this->getcurval($mainpricetx - $spcpricetx);
					}
 					 
					$rs_items[$cnt] = $items;
					
 					$cnt++;
				}
				
				$rp_gtag = array(
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'event_category' => 'ecommerce',
					'event_label' => 'related_products',
					'list_name' => 'Related Products',
					'item_list_name' => 'Related Products',
					'item_list_id' => 'related_products',
   					'items' => $rs_items,
				);
			}

$code = '';
if($rs['status']) {
	$code = "<script type='text/javascript'> gtag('event', 'view_item', ".json_encode($gtag,true)."); </script>";
	if($rp_flag && $rp_gtag) {
		$code .= "<script type='text/javascript'> gtag('event', 'view_item_list', ".json_encode($rp_gtag,true)."); </script>";
	}
}
return $code;
		}
	}
	public function viewsearch() {
		$rs = $this->getrsdata();
		
		$search_kywrd = '';
		if(isset($this->request->get['search'])) { 
			$search_kywrd = $this->request->get['search'];
		} else if(isset($this->request->get['tag'])) {
			$search_kywrd = $this->request->get['tag'];
		}
 		if($rs && !empty($search_kywrd)) {
 			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
			$filter_data = array('filter_name' => $search_kywrd, 'start' => 0, 'limit' => 5);
			$sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p 
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) 
			WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			AND p.status = '1' AND p.date_available <= NOW() 
			AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
			$data['filter_name'] = $search_kywrd;
			if (!empty($data['filter_name'])) {
				$sql .= " AND ( pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= ")";
			}
			$sql .= " GROUP BY p.product_id";
			$sql .= " LIMIT " . 0 . "," . 5;
		
 			$srch_query = $this->db->query($sql); 
 			$srch_rs = $srch_query->rows;
			
			if(!empty($srch_rs)) {
 				$itemsarr = array();
				$cnt = 0;
				foreach ($srch_rs as $srch_data) { 
					$pinfo = $this->getProduct($srch_data['product_id']);
					
					$category_name = $this->getcatname($pinfo['product_id']);
					$brand_name = $this->getbrandname($pinfo['product_id']);
					
					$price = $pinfo['special'] ? $pinfo['special'] : $pinfo['price'];
					$spcpricetx = $this->tax->calculate($price, $pinfo['tax_class_id'], $this->config->get('config_tax'));
					$mainpricetx = $this->tax->calculate($pinfo['price'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
					
					$items = array(
						'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
						'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
						"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
						"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
						'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
						'currency' => $this->session->data['currency'],
						'list_position' => $cnt,
						'price' => $this->getcurval($spcpricetx),
						'quantity' => $pinfo['minimum'],
					);
					if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
					if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
					if($pinfo['special']) { 
						$items['discount'] = $this->getcurval($mainpricetx - $spcpricetx);
					}
					
					$itemsarr[$cnt] = $items;
					
					$cnt++;
				} 

				$gtag = array(
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'event_category' => 'ecommerce',
					'event_label' => 'product_search',
					'currency' => $this->session->data['currency'],
					'search_term' => htmlspecialchars_decode(strip_tags($search_kywrd)),
					'items' => array($itemsarr),
				);
			 
$code = '';
if($rs['status']) {
	$code = "<script type='text/javascript'> gtag('event', 'search', ".json_encode($gtag,true)."); </script>";
}	
return $code;
			}
		}
	}
	public function viewcart() {
		$rs = $this->getrsdata();
  			
		if($rs && $this->cart->hasProducts()) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
 			$itemsarr = array();
			$cnt = 0;
			foreach ($this->cart->getProducts() as $pinfo) { 
 				$category_name = $this->getcatname($pinfo['product_id']);
				$brand_name = $this->getbrandname($pinfo['product_id']);
  				$totalprc = $this->tax->calculate($pinfo['total'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
 				$items = array(
					'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'currency' => $this->session->data['currency'],
					'list_position' => $cnt,
					'price' => $this->getcurval($totalprc),
					'quantity' => $pinfo['quantity'],
				);
				if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
				if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
				$itemsarr[$cnt] = $items;
 				$cnt++;
			}
			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => 'view_cart',
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($this->cart->getTotal()),
				'items' => $itemsarr,
 			);
			if(isset($this->session->data['coupon'])) {
				$gtag['coupon'] = $this->session->data['coupon'];
			} 
			  
if($rs['status']) { return "<script type='text/javascript'> gtag('event', 'view_cart', ".json_encode($gtag,true)."); </script>"; }
		}
	}
	public function beginchk() {
		$rs = $this->getrsdata();
  			
		if($rs && $this->cart->hasProducts()) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
 			$itemsarr = array();
			$cnt = 0;
			foreach ($this->cart->getProducts() as $pinfo) { 
 				$category_name = $this->getcatname($pinfo['product_id']);
				$brand_name = $this->getbrandname($pinfo['product_id']);
  				$totalprc = $this->tax->calculate($pinfo['total'], $pinfo['tax_class_id'], $this->config->get('config_tax')); 				
				$items = array(
					'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'currency' => $this->session->data['currency'],
					'list_position' => $cnt,
					'price' => $this->getcurval($totalprc),
					'quantity' => $pinfo['quantity'],
				);
				if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
				if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
				$itemsarr[$cnt] = $items;
				$cnt++;
			}
			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => 'begin_checkout',
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($this->cart->getTotal()),
				'items' => $itemsarr,
 			);
			if(isset($this->session->data['coupon'])) {
				$gtag['coupon'] = $this->session->data['coupon'];
			} 
			  
if($rs['status']) { return "<script type='text/javascript'> gtag('event', 'begin_checkout', ".json_encode($gtag,true)."); </script>"; }
		}
	}
	public function chkfunnel($stpno) {
		$rs = $this->getrsdata();
  			
		if($rs && $this->cart->hasProducts()) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
 			$itemsarr = array();
			$cnt = 0;
			foreach ($this->cart->getProducts() as $pinfo) { 
 				$category_name = $this->getcatname($pinfo['product_id']);
				$brand_name = $this->getbrandname($pinfo['product_id']);
  				$totalprc = $this->tax->calculate($pinfo['total'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
 				$items = array(
					'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'currency' => $this->session->data['currency'],
					'list_position' => $cnt,
					'price' => $this->getcurval($totalprc),
					'quantity' => $pinfo['quantity'],
				);
				if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
				if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
				$itemsarr[$cnt] = $items;
				$cnt++;
			}
			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => 'checkout_progress',				
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($this->cart->getTotal()),
 				'items' => $itemsarr,
 			);
			if(isset($this->session->data['coupon'])) {
				$gtag['coupon'] = $this->session->data['coupon'];
			} 
			  
$code = '';
if($rs['status']) {
	$code = "<script type='text/javascript'> gtag('event', 'checkout_progress', ".json_encode($gtag,true)."); </script>"; 
	if($stpno == 1) {
		$stpnm = 'Checkout Login';
	} elseif($stpno == 2) {
		$stpnm = 'Payment Address';
	} elseif($stpno == 3) {
		$stpnm = 'Shipping Address';
	} elseif($stpno == 4) {
		$stpnm = 'Shipping Method';
	} elseif($stpno == 5) {
		$stpnm = 'Payment Method';
	}
	
	$gtag = array(
		'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
		'event_category' => 'ecommerce',
		'event_label' => $stpnm,	
		'checkout_step' => $stpno,
		'checkout_option' => $stpnm,
	);
	$code .= "<script type='text/javascript'> gtag('event', 'set_checkout_option', ".json_encode($gtag,true)."); </script>";
}
return $code;
		}
	}
	public function addpayinfo() {
		$rs = $this->getrsdata();
  			
		if($rs && $this->cart->hasProducts() && !empty($this->session->data['payment_method'])) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
 			$itemsarr = array();
			$cnt = 0;
			foreach ($this->cart->getProducts() as $pinfo) { 
 				$category_name = $this->getcatname($pinfo['product_id']);
				$brand_name = $this->getbrandname($pinfo['product_id']);
  				$totalprc = $this->tax->calculate($pinfo['total'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
 				$items = array(
					'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'currency' => $this->session->data['currency'],
					'list_position' => $cnt,
					'price' => $this->getcurval($totalprc),
					'quantity' => $pinfo['quantity'],
				);
				if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
				if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
				$itemsarr[$cnt] = $items;
				$cnt++;
			}
			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => htmlspecialchars_decode(strip_tags($this->session->data['payment_method']['title'])),
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($this->cart->getTotal()),
				'payment_type' => htmlspecialchars_decode(strip_tags($this->session->data['payment_method']['title'])),
				'items' => $itemsarr,
 			);
			if(isset($this->session->data['coupon'])) {
				$gtag['coupon'] = $this->session->data['coupon'];
			} 
			  
if($rs['status']) { return "<script type='text/javascript'> gtag('event', 'add_payment_info', ".json_encode($gtag,true)."); </script>"; }
		}
	}
	public function addshpinfo() {
		$rs = $this->getrsdata();
  			
		if($rs && $this->cart->hasProducts() && !empty($this->session->data['shipping_method'])) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
 			$itemsarr = array();
			$cnt = 0;
			foreach ($this->cart->getProducts() as $pinfo) { 
 				$category_name = $this->getcatname($pinfo['product_id']);
				$brand_name = $this->getbrandname($pinfo['product_id']);
  				$totalprc = $this->tax->calculate($pinfo['total'], $pinfo['tax_class_id'], $this->config->get('config_tax'));
 				$items = array(
					'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'currency' => $this->session->data['currency'],
					'list_position' => $cnt,
					'price' => $this->getcurval($totalprc),
					'quantity' => $pinfo['quantity'],
				);
				if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
				if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
				$itemsarr[$cnt] = $items;
				$cnt++;
			}
			
			$gtag = array(
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'event_category' => 'ecommerce',
				'event_label' => htmlspecialchars_decode(strip_tags($this->session->data['shipping_method']['title'])),
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($this->cart->getTotal()),
				'shipping_tier' => htmlspecialchars_decode(strip_tags($this->session->data['shipping_method']['title'])),
				'items' => $itemsarr,
 			);
			if(isset($this->session->data['coupon'])) {
				$gtag['coupon'] = $this->session->data['coupon'];
			} 
			  
if($rs['status']) { return "<script type='text/javascript'> gtag('event', 'add_shipping_info', ".json_encode($gtag,true)."); </script>"; }
		}
	}
	public function purchase($order_id = 0) {
		$rs = $this->getrsdata();
		if(!$order_id && isset($this->session->data['order_id'])) { 
			$order_id = $this->session->data['order_id'];
		}
		if(!$order_id) { 
			$order_id = $this->getorderid();
		}
		
		if($rs && $order_id) {
			$stq = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '".(int)$this->config->get('config_store_id')."' ");
 			$storename = isset($stq->row['name']) ? $stq->row['name'] : $this->config->get('config_name');
			
 			$this->load->model('checkout/order');
 			$orderdata = $this->model_checkout_order->getOrder($order_id);
 			$order_products = $this->getorderproduct($order_id); 
			$order_tax = $this->getordertax($order_id);
			$order_shipping = $this->getordershipping($order_id);
 			
 			$itemsarr = array();
			$cnt = 0;
			foreach ($order_products as $pinfo) { 
 				$category_name = $this->getcatname($pinfo['product_id']);
				$brand_name = $this->getbrandname($pinfo['product_id']);
  				$totalprc = $pinfo['total'] + $pinfo['tax'];
 				$items = array(
					'id' => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					'name' => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					"item_id" => $pinfo['model'] ? $pinfo['model'] : $pinfo['product_id'],
					"item_name" => htmlspecialchars_decode(strip_tags($pinfo['name'])),
					'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
					'currency' => $this->session->data['currency'],
					'list_position' => $cnt,
					'price' => $this->getcurval($totalprc),
					'quantity' => $pinfo['quantity'],
				);
				if($category_name) { $items['category'] = $category_name; $items['item_category'] = $category_name; }
				if($brand_name) { $items['brand'] = $brand_name; $items['item_brand'] = $brand_name; }
				$itemsarr[$cnt] = $items;
				$cnt++;
			}
			
			$gtag = array(
				'transaction_id' => $order_id,
				'affiliation' => htmlspecialchars_decode(strip_tags($storename)),
				'currency' => $this->session->data['currency'],
				'value' => $this->getcurval($orderdata['total']),
				'tax' => $this->getcurval($order_tax),
				'shipping' => $this->getcurval($order_shipping),
 				'items' => $itemsarr,
 			);
			if(isset($this->session->data['coupon'])) {
				$gtag['coupon'] = $this->session->data['coupon'];
			}
			
$gacode = '';
if($rs['status']) {
$gacode = "<script type='text/javascript'> gtag('event', 'purchase', ".json_encode($gtag,true)."); </script>"; 
}

return $gacode;
		}
	}
}