<?php
class ModelExtensionBaselBaselMegamenu extends Model {

    private function resolveMenuLink($link, $lang_id) {
        $resolved = $link;
        $decoded = @unserialize($link);

        if (($decoded !== false || $link === 'b:0;') && is_array($decoded)) {
            if (isset($decoded[$lang_id])) {
                $resolved = $decoded[$lang_id];
            } elseif (isset($decoded[(string)$lang_id])) {
                $resolved = $decoded[(string)$lang_id];
            } else {
                $fallback = reset($decoded);
                $resolved = $fallback !== false ? $fallback : '';
            }
        }

        return $resolved;
    }
    
	public function getMenu($module_id, $mobile) {

	if ($mobile) {
		$output = $this->cache->get('megamenu.module_id_' . $module_id . '.mobile_true.' . (int)$this->config->get('config_language_id'));
	} else {
		$output = $this->cache->get('megamenu.module_id_' . $module_id . '.mobile_false' . (int)$this->config->get('config_language_id'));	
	}
	
	if (!$output) {
		
        $lang_id = $this->config->get('config_language_id');
		$output = array();
		
		if ($mobile) {
			$is_mobile = false;
        	$query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='0' AND status='0' AND disp_mobile_item = '1' AND module_id = '".$module_id."' ORDER BY rang");
		} else {
			$is_mobile = true;
			$query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='0' AND status='0' AND module_id = '".$module_id."' ORDER BY rang");	
		}
		
		foreach ($query->rows as $row) {
			
			$icon = false;
			if($row['icon']) {
                if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                    $icon = $this->config->get('config_ssl') . 'image/' . $row['icon'];
                } else {
                    $icon = $this->config->get('config_url') . 'image/' . $row['icon'];
                }
            }

            $description = false;
            $description_array = unserialize($row['description']);
            if(isset($description_array[$lang_id])) {
                if(!empty($description_array[$lang_id])) {
                    $description = html_entity_decode($description_array[$lang_id], ENT_QUOTES, 'UTF-8');
                }
            }
			
			if ($mobile) {
				$submenu_width = false;
			} else {
                $submenu_width = $row['submenu_width'];
            }
			
			if ($this->language->get('direction') == 'rtl') { 
				$search  = array('left', 'right');
				$replace = array('right', 'left');
				$bg_position = str_replace($search, $replace, $row['position']);
			} else { 
				$bg_position = $row['position'];
			}
			
			// Parent
            $output[] = array(
                'icon' => $icon,
                'name' => unserialize($row['name']),
                'link' => $this->resolveMenuLink($row['link'], $lang_id),
				'show_title' => $row['show_title'],
				'class_menu' => $row['class_menu'],
				'icon_font' => $row['icon_font'],
                'description' => $description,
                'new_window' => $row['new_window'],
                'position' => $bg_position,
                'submenu_width' => $submenu_width,
				'submenu_type' => $row['submenu_type'],
                'submenu' => $this->getSubmenu($row['id'], $mobile)
            );
		}
		
		if ($mobile) {
			$this->cache->set('megamenu.module_id_' . $module_id . '.mobile_true.' . (int)$this->config->get('config_language_id'), $output);
		} else {
			$this->cache->set('megamenu.module_id_' . $module_id . '.mobile_false' . (int)$this->config->get('config_language_id'), $output);	
		}
	
	}
        return $output;
    }

    public function getSubmenu($id, $mobile) {
		$registry = $this->registry;
        $output = array();
        $lang_id = $this->config->get('config_language_id');

        
        $this->load->model('catalog/product');
        $model = $registry->get('model_catalog_product');

      	$this->load->model('extension/basel/basel');
		
        $this->load->model('tool/image');
        $model_image = $registry->get('model_tool_image');
		
		if ($mobile) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='".$id."' AND disp_mobile_item = '1' AND status='0' ORDER BY rang");
		} else {
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."mega_menu WHERE parent_id='".$id."' AND status='0' ORDER BY rang");	
		}
		
		foreach ($query->rows as $row) {
            $content = json_decode($row['content'],true);
            
			if(isset($content['html']['text'][$lang_id])) {
                $html = htmlspecialchars_decode($content['html']['text'][$lang_id]);
            } else {
                $html = false;
            }

            if(isset($content['categories'])) {
                if(is_array($content['categories'])) {
                    $categories = $this->getCategories($content['categories']);
                } else {
                    $categories = false;
                }
            } else {
                $categories = false;
            }
			
			if(isset($content['image']['link']) && $content['image']['link']) {
                if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                    $link = $this->config->get('config_ssl') . 'image/' . $content['image']['link'];
                } else {
                    $link = $this->config->get('config_url') . 'image/' . $content['image']['link'];
                }
                $images['link'] = '<img src="'.$link.'" alt="">';
			}
			else
				$images = false;	
				
            if(isset($content['product']['id'])) {

                $product = $model->getProduct($content['product']['id']);
                if(is_array($product)) {
                    $product_link = $this->url->link('product/product', 'product_id=' . $content['product']['id']);
	
						if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$price = false;
						}

						if ((float)$product['special']) {
							$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
							$date_end = $this->model_extension_basel_basel->getSpecialEndDate($content['product']['id']);
						} else {
							$special = false;
							$date_end = false;
						}
					
                } else {
                    $product = false;
                    $product_link = false;
                    $price = false;
                    $special = false;
					$date_end = false;
                }
            } else {
                $product = false;
                $product_link = false;
                $price = false;
                $special = false;
				$date_end = false;
            }

            if(isset($product['image'])) {
                $product_image = $model_image->resize($product['image'], $content['product']['img_w'], $content['product']['img_h']);
            } else {
                $product_image = false;
            }
			
			$image2 = $this->model_catalog_product->getProductImages($content['product']['id']);
			if(isset($image2[0]['image']) && !empty($image2[0]['image'])){
				$image2 = $model_image->resize($image2[0]['image'], $content['product']['img_w'], $content['product']['img_h']);
			} else {
				$image2 = false;
			}
			
			if ($product && (float)$product['special']) {						
				$date_end = $this->model_extension_basel_basel->getSpecialEndDate($content['product']['id']);
			} else {
				$date_end = false;
			}
			if (!empty($product['date_available']) && strtotime($product['date_available']) > strtotime('-' . $this->config->get('newlabel_status') . ' day')) {
				$is_new = true;
			} else {
				$is_new = false;
			}
			
			$description = false;
            $description_array = unserialize($row['description']);
            if(isset($description_array[$lang_id])) {
                if(!empty($description_array[$lang_id])) {
                    $description = html_entity_decode($description_array[$lang_id], ENT_QUOTES, 'UTF-8');
                }
            }
			
			if (!empty($product['minimum'])) {
				$product_minimum = ($product['minimum'] > 0) ? $product['minimum'] : 1;
			} else{
				$product_minimum = 1;
			}
			
			
			// Child
            $output[] = array(
				'name' => unserialize($row['name']),
                'content_width' => intval($row['content_width']),
				'show_title' => $row['show_title'],
				'link' => $this->resolveMenuLink($row['link'], $lang_id),
				'icon_font' => $row['icon_font'],
				'new_window' => $row['new_window'],
				'description' => $description,
                'content_type' => $row['content_type'],
                'html' => $html,
                'product' => array(
					'name' => !empty($product['name']) ? $product['name'] : '',
					'id' => !empty($product['product_id']) ? $product['product_id'] : '',
					'minimum' => $product_minimum,
                    'link' => $product_link,
                    'image' => $product_image,
					'image2' => $image2,
					'rating' => !empty($product['rating']) ? $product['rating'] : '',
					'sale_end_date' => !empty($date_end['date_end']) ? $date_end['date_end'] : '',
                    'price' => $price,
					'new_label' => $is_new,
                    'special' => $special
                ),
                'categories' => $categories,
				'images'	=> $images,
				'class_menu'=> $row['class_menu'],
            );
        }
        return $output;
    }
	/*
	public function getCategoriesByCatId($parent_id = 0,$limit = 0) {
		$query = $this->db->query("SELECT * , c.category_id as id  FROM " . DB_PREFIX . "category c 
										LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
										LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) 
										WHERE c.parent_id = '" . (int)$parent_id . "' 
											AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
											AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  
											AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name) LIMIT ".(int)$limit."");

		return $query->rows;
	}
	*/
    
	public function getCategories($array = array()) {
        $output = false;
		$registry = $this->registry;
        // Category model
        $this->load->model('catalog/category');
        $model = $registry->get('model_catalog_category');

        $output .= '<div class="row">';
        $row_fluid = 12;
        if($array['columns'] == 2) { $row_fluid = 6; }
        if($array['columns'] == 3) { $row_fluid = 4; }
        if($array['columns'] == 4) { $row_fluid = 3; }
        if($array['columns'] == 5) { $row_fluid = 25; }
        if($array['columns'] == 6) { $row_fluid = 2; }
        if(!($array['columns'] > 0 && $array['columns'] < 7)) { $array['columns'] = 1; }
        $menu_class = 'hover-menu';
        if($array['submenu'] == 2) { $menu_class = 'static-menu'; }
		$limit = (isset($array['limit']) && $array['limit']) ? $array['limit'] : count($array['categories']);
        for ($i = 0; $i < count($array['categories']);) {
            $output .= '<div class="col-sm-'.$row_fluid.' '.$menu_class.'">';
            //$output .= '<div class="menu">';
            $output .= '<ul>';
            $j = $i + ceil(count($array['categories']) / $array['columns']);
			$lim = (isset($array['limit']) && $array['limit']) ? $array['limit'] : $j;
            for (; $i < $j; $i++) {
                if(isset($array['categories'][$i]['id'])) {
                    $info_category = $model->getCategory($array['categories'][$i]['id']);
                    if(isset($info_category['category_id'])) {
                        $path = '';
                        if($info_category['parent_id'] > 0) {
                            $path = $info_category['parent_id'];
                            $info_category2 = $model->getCategory($info_category['parent_id']);
                            if(isset($info_category2['parent_id']) && $info_category2['parent_id'] > 0) {
                                $path = $info_category2['parent_id'] . '_' . $path;
                                $info_category3 = $model->getCategory($info_category2['parent_id']);
                                if(isset($info_category3['parent_id']) && $info_category3['parent_id'] > 0) {
                                    $path = $info_category3['parent_id'] . '_' . $path;
                                }
                            }
                        }

                        if($path != '') {
                            $path = $path . '_';
                        }
                        if(is_array($info_category)) {
                            $link = $this->url->link('product/category', 'path=' . $path . $info_category['category_id']);
                            
							if(isset($array['categories'][$i]['children']) && $array['categories'][$i]['children']) {
								if($array['submenu'] == 2) {
								$output .= '<li class="has-sub"><a href="'.$link.'">'.$info_category['name'].'<i class="fa fa-angle-right"></i></a>';
								} else {
								$output .= '<li class="has-sub dropdown-wrapper from-bottom"><a href="'.$link.'">'.$info_category['name'].'<i class="fa fa-angle-right"></i></a>';
								}
							} else {
								$output .= '<li><a href="'.$link.'">'.$info_category['name'].'</a>';
							}
                            if(isset($array['categories'][$i]['children'])) {
                                if(!empty($array['categories'][$i]['children'])) {
                                    $output .= $this->getCategoriesChildren($array['categories'][$i]['children'], $info_category['category_id'], $array['submenu_columns'], $array['submenu'],$limit);
                                }
                            }
                            $output .= '</li>';
                        }
                    }
                }
            }
            $output .= '</ul>';
            $output .= '</div>';
            //$output .= '</div>';
        }
        $output .= '</div>';
        return $output;
    }

    public function getCategoriesChildren($array = array(), $path, $columns, $type, $submenu = false,$limit=4) {
        $output = false;
		$registry = $this->registry;
        // Category model
        $this->load->model('catalog/category');
        $model = $registry->get('model_catalog_category');
        if($type == 2) {
            $row_fluid = 12;
            if($columns == 2) { $row_fluid = 6; }
            if($columns == 3) { $row_fluid = 4; }
            if($columns == 4) { $row_fluid = 3; }
            if($columns == 5) { $row_fluid = 25; }
            if($columns == 6) { $row_fluid = 2; }
            if(!($columns > 0 && $columns < 7)) { $columns = 1; }
			if($submenu == 0) { $columns = 1; $row_fluid = 12; }
            if($columns != 1) {
                $output .= '<div class="row visible">';
            }
			$limit = (isset($array['limit']) && $array['limit']) ? $array['limit'] : count($array);
            for ($i = 0; $i < count($array);) {
                if($columns != 1) {
                    $output .= '<div class="col-sm-'.$row_fluid.'">';
                }
                
				$output .= '<ul class="sub-holder">';
				
                $j = $i + ceil(count($array) / $columns);
				$lim = (isset($array['limit']) && $array['limit']) ? $array['limit'] : $j;
					
                for (; $i < $j; $i++) {
                    if(isset($array[$i]['id'])) {
                        $info_category = $model->getCategory($array[$i]['id']);
                        if(isset($info_category['category_id'])) {
                            $path = '';

                            if($info_category['parent_id'] > 0) {
                                $path = $info_category['parent_id'];
                                $info_category2 = $model->getCategory($info_category['parent_id']);
                                if(isset($info_category2['parent_id']) &&  $info_category2['parent_id'] > 0) {
                                    $path = $info_category2['parent_id'] . '_' . $path;
                                    $info_category3 = $model->getCategory($info_category2['parent_id']);
                                    if(isset($info_category3['parent_id']) && $info_category3['parent_id'] > 0) {
                                        $path = $info_category3['parent_id'] . '_' . $path;
                                    }
                                }
                            }

                            if($path != '') {
                                $path = $path . '_';
                            }
                            if(is_array($info_category)) {
                                $link = $this->url->link('product/category', 'path=' . $path . $info_category['category_id']);
                                
								if(isset($array[$i]['children']) && !empty($array[$i]['children'])){
									$output .= '<li class="has-sub"><a href="'.$link.'">'.$info_category['name'].'';
									$output .= '<i class="fa fa-angle-right"></i>';
								} else {
									$output .= '<li><a href="'.$link.'">'.$info_category['name'].'';
								}
								$output .= '</a>';
                                if(isset($array[$i]['children'])) {
                                    if(!empty($array[$i]['children'])) {
                                        $output .= $this->getCategoriesChildren($array[$i]['children'], $path.'_'.$info_category['category_id'], $columns, $type, 0,$limit);
                                    }
                                }
                                $output .= '</li>';
                            }
                        }
                    }
                }
                $output .= '</ul>';
                if($columns != 1) {
                    $output .= '</div>';
                }
            }
            if($columns != 1) {
                $output .= '</div>';
            }
        } else {
            $output .= '<ul class="dropdown-content sub-holder dropdown-left">';
            foreach($array as $row) {
                $info_category = $model->getCategory($row['id']);
                if(isset($info_category['category_id'])) {
                    $path = '';

                    if($info_category['parent_id'] > 0) {
                        $path = $info_category['parent_id'];
                        $info_category2 = $model->getCategory($info_category['parent_id']);
                        if(isset($info_category2['parent_id']) && $info_category2['parent_id'] > 0) {
                            $path = $info_category2['parent_id'] . '_' . $path;
                            $info_category3 = $model->getCategory($info_category2['parent_id']);
                            if(isset($info_category3['parent_id']) && $info_category3['parent_id'] > 0) {
                                $path = $info_category3['parent_id'] . '_' . $path;
                            }
                        }
                    }

                    if($path != '') {
                        $path = $path . '_';
                    }

                    $link = $this->url->link('product/category', 'path=' . $path . $info_category['category_id']);
                    
					if(isset($row['children']) && !empty($row['children'])){
					$output .= '<li class="has-sub dropdown-wrapper from-bottom"><a href="'.$link.'">'.$info_category['name'].'<i class="fa fa-angle-right"></i>';
					} else {
					$output .= '<li><a href="'.$link.'">'.$info_category['name'].'';
					}
					
					$output .= '</a>';
                    if(isset($row['children'])) {
                        if(!empty($row['children'])) {
                            $output .= $this->getCategoriesChildren($row['children'], $path.'_'.$info_category['category_id'], $columns, $type);
                        }
                    }
                    $output .= '</li>';
                }
            }
            $output .= '</ul>';
        }
        return $output;
    }
}
