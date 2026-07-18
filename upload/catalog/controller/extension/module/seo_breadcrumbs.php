<?php

/*
This file is subject to the terms and conditions defined in the "EULA.txt"
file, which is part of this source code package and is also available on the
page: https://raw.githubusercontent.com/ocmod-space/license/main/EULA.txt.
*/

class ControllerExtensionModuleSeoBreadcrumbs extends Controller {
	// catalog/view/*/before
	public function substBreadcrumbs(&$route, &$data) {
		if (isset($data['breadcrumbs']) && is_array($data['breadcrumbs'])) {
			if ($this->config->get('module_seo_breadcrumbs_status')) {
				$settings = $this->config->get('module_seo_breadcrumbs');

				if ($settings['breadcrumbs_path'] !== 'default' && isset($this->request->get['product_id'])) {
					$continue = true;

					if (!$settings['breadcrumbs_force'] && isset($this->request->get['path'])) {
						$continue = false;
					}

					if (!$settings['breadcrumbs_search'] &&
						(isset($this->request->get['search']) || isset($this->request->get['tag']))
					) {
						$continue = false;
					}

					if (!$settings['breadcrumbs_manufacturer'] && isset($this->request->get['manufacturer_id'])) {
						$continue = false;
					}

					if ($continue) {
						if (isset($settings['breadcrumbs_level']) && isset($settings['breadcrumbs_nlevel'])) {
							$settings['breadcrumbs_level'] = $settings['breadcrumbs_nlevel']
								? -1 * $settings['breadcrumbs_level']
								: $settings['breadcrumbs_level'];
						} else {
							$settings['breadcrumbs_level'] = 0;
						}

						$data['breadcrumbs'] = $this->getProductBreadcrumbs(
							$settings['breadcrumbs_path'], $this->request->get, $settings['breadcrumbs_level']
						);
					}
				}

				// restore full category path even if there is only last id
				if (isset($this->request->get['path']) && !isset($this->request->get['product_id'])) {
					$data['breadcrumbs'] = $this->getCategoryBreadcrumbs($this->request->get);
				}

				if (!empty($settings['breadcrumbs_json']) && method_exists($this->document, 'addTag')) {
					$data['json_breadcrumbs'] = $this->getBreadcrumbsJson($data['breadcrumbs']);

					if (is_null($data['json_breadcrumbs'])) {
						return;
					}

					$tag = array(
						'name'    => 'script',
						'attrs'   => array('type' => 'application/ld+json'),
						'content' => $data['json_breadcrumbs'],
						'closing' => true,
					);
					$this->document->addTag($tag, $route, 'header');
				}

				if ($settings['breadcrumbs_nolink']) {
					$data['breadcrumbs'] = $this->removeLastBreadcrumb($data['breadcrumbs']);
				}
			}
		}
	}

	// catalog/controller/common/header/before
	public function styleBreadcrumbs(&$route, &$data) {
		if ($this->config->get('module_seo_breadcrumbs_status')) {
			$dir_stylesheet = 'catalog/view/theme/' . $this->config->get('config_theme') . '/stylesheet/';
			$settings = $this->config->get('module_seo_breadcrumbs');

			if ($settings['breadcrumbs_bold']) {
				$css_bold = $dir_stylesheet . 'breadcrumbs_bold.css';

				if (file_exists($css_bold)) {
					$this->document->addStyle($css_bold);
				}
			}

			if ($settings['breadcrumbs_nolink']) {
				$css_nolink = $dir_stylesheet . 'breadcrumbs_nolink.css';

				if (file_exists($css_nolink)) {
					$this->document->addStyle($css_nolink);
				}
			}
		}
	}

	//catalog/view/*/after
	public function addJsonLdScript(&$route, &$data, &$template) {
		if ($this->config->get('module_seo_breadcrumbs_status')) {
			$settings = $this->config->get('module_seo_breadcrumbs');

			if (!empty($settings['breadcrumbs_json']) && method_exists($this->document, 'getTags')) {
				$json_ld = '';

				if ($this->document->getTags('header')) {
					foreach ($this->document->getTags('header') as $tag) {
						if ($tag['id'] == $route) {
							$json_ld .= '<' . $tag['tag']['name'];

							$attrs = '';

							foreach ($tag['tag']['attrs'] as $attr => $value) {
								$attrs .= $attr . '="' . $value . '" ';
							}

							$json_ld .= ' ' . $attrs;

							if ($tag['tag']['closing']) {
								$json_ld .= '>';
							} else {
								$json_ld .= '/>';
							}

							$json_ld .= $tag['tag']['content'];

							if ($tag['tag']['closing']) {
								$json_ld .= '</' . $tag['tag']['name'] . '>';
							}
						}
					}
				}

				if ($json_ld) {
					$template = preg_replace('/<\/head>/', $json_ld . '</head>', $template);
				}
			}
		}
	}

	private function getCategoryBreadcrumbs($get = array()) {
		$breadcrumbs = array();

		$breadcrumbs[] = array(
			'text' => '<i class="fa fa-home"></i>',
			'href' => $this->url->link('common/home'),
		);

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $get['limit'];
			}

			$parts = explode('_', (string)$get['path']);
			$category_id = (int)array_pop($parts);

			$this->load->model('extension/module/seo_breadcrumbs');

			$path = '';

			foreach ($this->model_extension_module_seo_breadcrumbs->getCategoryPathIds($category_id) as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_name = $this->model_extension_module_seo_breadcrumbs->getCategoryName($path_id);

				if ($category_name) {
					$breadcrumbs[] = array(
						'text' => $category_name,
						'href' => $this->url->link('product/category', 'path=' . $path . $url),
					);
				}
			}
		}

		return $breadcrumbs;
	}

	private function getProductBreadcrumbs($breadcrumbs_path, $get = array(), $level = 0) {
		$breadcrumbs = array();

		if (isset($get['product_id']) && (int)$get['product_id']) {
			$product_id = (int)$get['product_id'];

			$breadcrumbs[] = array(
				// 'text' => $this->language->get('text_home'),
				'text' => '<i class="fa fa-home"></i>',
				'href' => $this->url->link('common/home'),
			);

			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

			if (!$product_info) {
				$this->response->redirect($this->url->link('error/not_found', '', true));

				exit;
			}

			if (isset($get['search']) || isset($get['tag'])) {
				$url = '';

				if (isset($get['search'])) {
					$url .= '&search=' . $get['search'];
				}

				if (isset($get['tag'])) {
					$url .= '&tag=' . $get['tag'];
				}

				if (isset($get['description'])) {
					$url .= '&description=' . $get['description'];
				}

				if (isset($get['category_id'])) {
					$url .= '&category_id=' . $get['category_id'];
				}

				if (isset($get['sub_category'])) {
					$url .= '&sub_category=' . $get['sub_category'];
				}

				if (isset($get['sort'])) {
					$url .= '&sort=' . $get['sort'];
				}

				if (isset($get['order'])) {
					$url .= '&order=' . $get['order'];
				}

				if (isset($get['page'])) {
					$url .= '&page=' . $get['page'];
				}

				if (isset($get['limit'])) {
					$url .= '&limit=' . $get['limit'];
				}

				$breadcrumbs[] = array(
					'text' => $this->language->get('text_search'),
					'href' => $this->url->link('product/search', $url),
				);
			}

			if ($breadcrumbs_path === 'direct') {
				//
			} elseif (in_array($breadcrumbs_path, array('short', 'full', 'last'))) {
				$url = '';

				if (isset($get['sort'])) {
					$url .= '&sort=' . $get['sort'];
				}

				if (isset($get['order'])) {
					$url .= '&order=' . $get['order'];
				}

				if (isset($get['page'])) {
					$url .= '&page=' . $get['page'];
				}

				if (isset($get['limit'])) {
					$url .= '&limit=' . $get['limit'];
				}

				$this->load->model('extension/module/seo_breadcrumbs');
				$category_ids = $this->model_extension_module_seo_breadcrumbs->getProductPathIds($product_id);

				if ($category_ids) {
					$path_ids = array();

					if ($breadcrumbs_path === 'short') {
						$path_ids = reset($category_ids);
						$path_ids = $this->getPathSlice($path_ids, $level);
					} elseif ($breadcrumbs_path === 'full') {
						$path_ids = end($category_ids);
						$path_ids = $this->getPathSlice($path_ids, $level);
					} elseif ($breadcrumbs_path === 'last') {
						$path_ids = end($category_ids);
						$path_ids = array(end($path_ids));
					}

					$path = '';
					$counter = 1;

					foreach ($path_ids as $category_id) {
						if (!$path) {
							$path = $category_id;
						} else {
							$path .= '_' . $category_id;
						}

						$category_name = $this->model_extension_module_seo_breadcrumbs->getCategoryName($category_id);

						if ($category_name) {
							if ($counter == count($path_ids)) {
								// Last category breadcrumb
								$breadcrumbs[] = array(
									'text' => $category_name,
									'href' => $this->url->link('product/category', 'path=' . $path . $url),
								);
							} else {
								$breadcrumbs[] = array(
									'text' => $category_name,
									'href' => $this->url->link('product/category', 'path=' . $path),
								);
							}
						}

						++$counter;
					}
				}
			} elseif (isset($get['manufacturer_id']) || $breadcrumbs_path === 'manufacturer') {
				$url = '';

				if (isset($get['sort'])) {
					$url .= '&sort=' . $get['sort'];
				}

				if (isset($get['order'])) {
					$url .= '&order=' . $get['order'];
				}

				if (isset($get['page'])) {
					$url .= '&page=' . $get['page'];
				}

				if (isset($get['limit'])) {
					$url .= '&limit=' . $get['limit'];
				}

				$breadcrumbs[] = array(
					'text' => $this->language->get('text_brand'),
					'href' => $this->url->link('product/manufacturer'),
				);

				if (isset($get['manufacturer_id'])) {
					$manufacturer_id = $get['manufacturer_id'];
				} elseif (isset($product_info['manufacturer_id'])) {
					$manufacturer_id = $product_info['manufacturer_id'];
				} else {
					$manufacturer_id = 0; // just for secure
				}

				$this->load->model('extension/module/seo_breadcrumbs');
				$manufacturer_name = $this->model_extension_module_seo_breadcrumbs->getManufacturerName($manufacturer_id);

				if ($manufacturer_name) {
					$breadcrumbs[] = array(
						'text' => $manufacturer_name,
						'href' => $this->url->link('product/manufacturer/info',	'manufacturer_id=' . $manufacturer_id . $url),
					);
				}
			}

			$url = '';

			if (isset($get['path'])) {
				$url .= '&path=' . $get['path'];
			}

			if (isset($get['filter'])) {
				$url .= '&filter=' . $get['filter'];
			}

			if (isset($get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $get['manufacturer_id'];
			}

			if (isset($get['search'])) {
				$url .= '&search=' . $get['search'];
			}

			if (isset($get['tag'])) {
				$url .= '&tag=' . $get['tag'];
			}

			if (isset($get['description'])) {
				$url .= '&description=' . $get['description'];
			}

			if (isset($get['category_id'])) {
				$url .= '&category_id=' . $get['category_id'];
			}

			if (isset($get['sub_category'])) {
				$url .= '&sub_category=' . $get['sub_category'];
			}

			if (isset($get['sort'])) {
				$url .= '&sort=' . $get['sort'];
			}

			if (isset($get['order'])) {
				$url .= '&order=' . $get['order'];
			}

			if (isset($get['page'])) {
				$url .= '&page=' . $get['page'];
			}

			if (isset($get['limit'])) {
				$url .= '&limit=' . $get['limit'];
			}

			$breadcrumbs[] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', '&product_id=' . $product_id . $url),
			);
		}

		return $breadcrumbs;
	}

	// Cuts array of ids by depth level
	private function getPathSlice($path_ids, $level = 0) {
		if ($level && $path_ids) {
			$offset = 0;
			$length = count($category_path);

			if ($level < 0 && $length >= abs($level)) {
				$offset = count($category_path) + $level;
				$length = abs($level);
			}

			$path_ids = array_slice($path_ids, $offset, $length);
		}

		return  $path_ids;
	}

	private function getBreadcrumbsJson(array $breadcrumbs) {
		if (!$breadcrumbs || !is_array($breadcrumbs)) {
			return null;
		}

		$item_list = array();
		$loop = 0;

		foreach ($breadcrumbs as $key => $breadcrumb) {
			$e = array();

			$e['@type'] = 'ListItem';
			$e['position'] = $loop;

			if ($loop == 0) {
				$e['name'] = htmlspecialchars($this->config->get('config_name'));
			} else {
				$e['name'] = htmlspecialchars($breadcrumb['text']);
			}

			$e['item'] = $breadcrumb['href'];

			$item_list[] = $e;

			++$loop;
		}

		$json = array(
			'@context'        => 'http://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $item_list,
		);

		return json_encode($json);
	}

	private function removeLastBreadcrumb($breadcrumbs) {
		$count = count($breadcrumbs);

		if ($count > 1) {
			$breadcrumbs[$count - 1]['href'] = '';
		}

		return $breadcrumbs;
	}
}
