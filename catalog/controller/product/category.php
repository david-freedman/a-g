<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerProductCategory extends Controller {
	public function index() {

		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->load->model('catalog/manufacturer');


// Получаем ID категории из запроса
// Получаем данные для категории
$category_id = (int)$this->request->get['path']; // Получаем ID категории

// Получаем данные баннера
$img_banner_cat = $this->load->controller('custom/setting/getValue', [
    'section' => 'banner_image',
    'setting' => 'img_banner_cat',
    'page' => 'category',
    'id' => $category_id,
]);

$text_baner_cat = $this->load->controller('custom/setting/getValue', [
    'section' => 'banner_image',
    'setting' => 'text_baner_cat',
    'page' => 'category',
    'id' => $category_id,
]);

$link_baner_cat = $this->load->controller('custom/setting/getValue', [
    'section' => 'banner_image',
    'setting' => 'link_baner_cat',
    'page' => 'category',
    'id' => $category_id,
]);

// Логируем данные
error_log("Banner Image: " . print_r($img_banner_cat, true));
error_log("Text Banner: " . print_r($text_baner_cat, true));
error_log("Banner Link: " . print_r($link_baner_cat, true));

// Декодируем текст
$text_baner_cat = html_entity_decode($text_baner_cat[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

// Отправляем данные в шаблон
$data['img_banner_cat'] = $img_banner_cat;
$data['text_baner_cat'] = $text_baner_cat;
$data['link_baner_cat'] = $link_baner_cat;

// Логируем идентификатор категории для отладки
error_log("Category ID: " . $category_id);

// Получаем значения для title_acc, text_acc и img_acc
$title_acc = $this->load->controller('custom/setting/getValue', [
    'section' => 'akordeon_catalogy',
    'setting' => 'title_acc',
    'page' => 'category',
    'id' => $category_id,
]);

$title_acc = isset($title_acc[$this->config->get('config_language_id')])
    ? html_entity_decode($title_acc[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8')
    : '';

$text_acc = $this->load->controller('custom/setting/getValue', [
    'section' => 'akordeon_catalogy',
    'setting' => 'text_acc',
    'page' => 'category',
    'id' => $category_id,
]);

$text_acc = isset($text_acc[$this->config->get('config_language_id')])
    ? html_entity_decode($text_acc[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8')
    : '';

$img_acc = $this->load->controller('custom/setting/getValue', [
    'section' => 'akordeon_catalogy',
    'setting' => 'img_acc',
    'page' => 'category',
    'id' => $category_id,
]);

// Отправляем данные в шаблон
$data['img_acc'] = $img_acc;
$data['title_acc'] = $title_acc;
$data['text_acc'] = $text_acc;

// Логируем полученные данные для аккордеона (массовые значения)
$mass_add_acc = $this->load->controller('custom/setting/getValue', [
    'section' => 'akordeon_catalogy',
    'setting' => 'mass_add_acc',
    'page' => 'category',
    'id' => $category_id,
]);

error_log("Mass Add Acc raw for category $category_id: " . print_r($mass_add_acc, true));

// Инициализируем массив для передачи в шаблон
$data['mass_add_acc'] = array();
$language_id = $this->config->get('config_language_id');

// Проверяем, есть ли данные для массового аккордеона
if (!empty($mass_add_acc)) {
    foreach ($mass_add_acc as $mass_add_item) {
        // Логируем данные каждого элемента для текущей категории
        error_log("Mass Add Item for category $category_id: " . print_r($mass_add_item, true));

        // Проверяем наличие данных для текущего языка
        if (isset($mass_add_item['title_acc_v_cat'][$language_id]) && isset($mass_add_item['text_acc_v_cat'][$language_id])) {
            $data['mass_add_acc'][] = array(
                'title' => html_entity_decode($mass_add_item['title_acc_v_cat'][$language_id], ENT_QUOTES, 'UTF-8'),
                'text' => html_entity_decode($mass_add_item['text_acc_v_cat'][$language_id], ENT_QUOTES, 'UTF-8'),
            );
        } else {
            // Логируем отсутствие данных для текущего языка в категории
            error_log("No language data for mass_add_acc at language ID $language_id in category $category_id");
        }
    }
} else {
    error_log("No valid data in mass_add_acc for category $category_id");
}




// Подключаем файл шаблона
$this->response->setOutput($this->load->view('product/category', $data));


		$data['text_empty'] = $this->language->get('text_empty');

        if ($this->config->get('config_noindex_disallow_params')) {
            $params = explode ("\r\n", $this->config->get('config_noindex_disallow_params'));
            if(!empty($params)) {
                $disallow_params = $params;
            }
        }

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
			if (!in_array('filter', $disallow_params, true) && $this->config->get('config_noindex_status')){
                $this->document->setRobots('noindex,follow');
            }
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
            if (!in_array('sort', $disallow_params, true) && $this->config->get('config_noindex_status')) {
                $this->document->setRobots('noindex,follow');
            }
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
            if (!in_array('order', $disallow_params, true) && $this->config->get('config_noindex_status')) {
                $this->document->setRobots('noindex,follow');
            }
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
            if (!in_array('page', $disallow_params, true) && $this->config->get('config_noindex_status')) {
                $this->document->setRobots('noindex,follow');
            }
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
            if (!in_array('limit', $disallow_params, true) && $this->config->get('config_noindex_status')) {
                $this->document->setRobots('noindex,follow');
            }
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {

			if ($category_info['meta_title']) {
				$this->document->setTitle($category_info['meta_title']);
			} else {
				$this->document->setTitle($category_info['name']);
			}

			if ($category_info['noindex'] <= 0 && $this->config->get('config_noindex_status')) {
				$this->document->setRobots('noindex,follow');
			}

			if ($category_info['meta_h1']) {
				$data['heading_title'] = $category_info['meta_h1'];
			} else {
				$data['heading_title'] = $category_info['name'];
			}

			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			$width = 200;  // Укажите нужную ширину
			$height = 200; // Укажите нужную высоту
			
			$data['categories'] = array(); // Инициализация массива
			
			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);
			
				// Получаем количество товаров в категории
				$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
			
				// Проверка, есть ли товары в категории
				if ($product_total > 0) {
					$image = !empty($result['image']) ? $result['image'] : 'placeholder.png'; // Убедитесь, что здесь используется правильный путь к изображению
			
					$data['categories'][] = array(
						'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
						'thumb' => $this->model_tool_image->resize($image, $width, $height)
					);
				}
			}
			


			

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_sub_category' => true,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$data['product_total'] = $product_total;

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				if ($result['image']) {
					// Основное изображение продукта
					$image = $this->model_tool_image->resize($result['image'], 
						$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), 
						$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
					);
				} else {
					// Если основное изображение отсутствует, используем изображение-заполнитель
					$image = $this->model_tool_image->resize('placeholder.png', 
						$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), 
						$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
					);
				}
				
				// Получаем дополнительные изображения
				$additional_images = $this->model_catalog_product->getProductImages($result['product_id']);
				
				// Массив для дополнительных изображений
				$product_images = array();
				
				// Добавляем основное изображение
				$product_images[] = $image;
				
				// Проверяем, есть ли дополнительные изображения
				if (!empty($additional_images)) {
					foreach ($additional_images as $additional_image) {
						$product_images[] = $this->model_tool_image->resize($additional_image['image'], 
							$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), 
							$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
						);
					}
				}
				// Определяем, нужно ли показывать кнопки слайдера
				$show_slider_controls = count($product_images) <= 1;

			
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}
			
				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
			
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}
			
				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
			
				// Добавление получения производителя
				$manufacturer = '';
				$manufactur_id = $result['manufacturer_id'];
				if (isset($result['manufacturer_id']) && $result['manufacturer_id']) {
					
					// Получаем информацию о производителе
					$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufactur_id);
					
					// Если информация о производителе найдена, используем имя
					if ($manufacturer_info) {
						$manufacturer = $manufacturer_info['name'];
					} else {
						$manufacturer = false; 
					}
				}

				$video = false;
				if (isset($result['video']) && !empty($result['video'])) {
   				 $video = $result['video'];
				}

				
				$data['text_special_order'] = $this->language->get('text_special_order');
				$data['text_out_of_stock'] = $this->language->get('text_out_of_stock');
				$data['text_instock'] = $this->language->get('text_instock');

				if (isset($result['stock_status_id']) && $result['stock_status_id'] == 8) {
					$stock_status = $this->language->get('text_special_order');
				} else {
					$stock_status = $this->language->get('text_out_of_stock');
				}
				

				$stock_status = '';
				if ($result['quantity'] <= 0) {
					if ($result['stock_status_id'] == 8) {
						$stock_status = $data['text_special_order'];
					} else {
						$stock_status = $data['text_out_of_stock'];
					}
				} elseif ($this->config->get('config_stock_display')) {
					$stock_status = $result['quantity'];
				} else {
					$stock_status = $data['text_instock'];
				}

				

								
				$data['products'][] = array(
					'product_id'            => $result['product_id'],
					'thumb'                 => $image,
					'images'                => $product_images,
					'show_slider_controls'  => $show_slider_controls,
					'name'                  => $result['name'],
					'description'           => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'                 => $price,
					'special'               => $special,
					'tax'                   => $tax,
					'minimum'               => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'                => $rating,
					'stock'                 => $stock_status,
					'href'                  => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url),
					'manufacturer'          => $manufacturer,
					'video'                 => $video,
					
				);
				
			}
			

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 15, 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$total_pages = ceil($product_total / $limit);
			$max_links = 6; //сколько отображать кннопок

			
			$start = max(1, $page - floor($max_links / 2));
			$end = min($total_pages, $start + $max_links - 1);
			//если страницы меньше 6
			if ($end - $start < $max_links - 1) {
				$start = max(1, $end - $max_links + 1);
			}

			$data['custom_pagination'] = [];

			for ($i = $start; $i <= $end; $i++) {
				if ($i == 1) {
					$data['custom_pagination'][] = [
						'text' => $i,
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url), // Без параметра page=1
						'active' => $i == $page
					];
				} else {
					$data['custom_pagination'][] = [
						'text' => $i,
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page=' . $i),
						'active' => $i == $page
					];
				}
			}
			
			$data['prev_link'] = $page > 1 ? 
				($page == 2 
					? $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url) // Для сторінки 2 попередній без page=1
					: $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page=' . ($page - 1))) 
				: '';
			
			$data['next_link'] = $page < $total_pages ? 
				$this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page=' . ($page + 1)) 
				: '';

			$data['next_page'] = $page < $total_pages ? $page + 1 : false;
			$data['total_pages'] = $total_pages;


			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

            if (!$this->config->get('config_canonical_method')) {
                // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
                if ($page == 1) {
                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
                } elseif ($page == 2) {
                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'prev');
                } else {
                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page - 1)), 'prev');
                }

                if ($limit && ceil($product_total / $limit) > $page) {
                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page + 1)), 'next');
                }
            } else {

                if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                    $server = $this->config->get('config_ssl');
                } else {
                    $server = $this->config->get('config_url');
                };

                $request_url = rtrim($server, '/') . $this->request->server['REQUEST_URI'];
                $canonical_url = $this->url->link('product/category', 'path=' . $category_info['category_id']);

                if (($request_url != $canonical_url) || $this->config->get('config_canonical_self')) {
                    $this->document->addLink($canonical_url, 'canonical');
                }

                if ($this->config->get('config_add_prevnext')) {

                    if ($page == 2) {
                        $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'prev');
                    } elseif ($page > 2)  {
                        $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page - 1)), 'prev');
                    }

                    if ($limit && ceil($product_total / $limit) > $page) {
                        $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page + 1)), 'next');
                    }
                }
            }

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;
			$data['default_limit'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if ($this->request->server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
				$this->response->setOutput($this->load->view('product/partial_list', $data));
			} else {
				$this->response->setOutput($this->load->view('product/category', $data));
			}
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/category', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

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
