<?php
class ControllerExtensionModuleSpecial extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/special');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$tittle_akcii = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_akcii', // Уникальный индификатор секции
			'setting' => 'tittle_akcii', // Уникальный индификатор поля
			'page' => 'module_special', // Код формы в админ-панеле
			'id' => '37', // Id объекта в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['tittle_akcii'] = html_entity_decode($tittle_akcii[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');


		$text_akcii = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_akcii', // Уникальный индификатор секции
			'setting' => 'text_akcii', // Уникальный индификатор поля
			'page' => 'module_special', // Код формы в админ-панеле
			'id' => '37', // Id объекта в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['text_akcii'] = html_entity_decode($text_akcii[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

		$img_akcii = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_akcii', // Уникальный индификатор секции
			'setting' => 'img_akcii', // Уникальный индификатор поля
			'page' => 'module_special', // Код формы в админ-панеле
			'id' => '37', // Id объекта в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['img_akcii'] = $img_akcii;

		$link_akcii = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_akcii', // Уникальный индификатор секции
			'setting' => 'link_akcii', // Уникальный индификатор поля
			'page' => 'module_special', // Код формы в админ-панеле
			'id' => '37', // Id объекта в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['link_akcii'] = $link_akcii;

		$name_btn_akcii = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_akcii', // Уникальный индификатор секции
			'setting' => 'name_btn_akcii', // Уникальный индификатор поля
			'page' => 'module_special', // Код формы в админ-панеле
			'id' => '37', // Id объекта в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['name_btn_akcii'] = html_entity_decode($name_btn_akcii[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

		$data['products'] = array();

		$filter_data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProductSpecials($filter_data);

		if ($results) {
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
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				$stock_status = '';
				if ($result['quantity'] <= 0) {
					
				if ($result['stock_status_id'] == 8) {
						$stock_status = $this->language->get('text_special_order'); 
				} else {
						$stock_status = $this->language->get('text_out_of_stock'); // Получаем строку "Нет в наличии"
					}
				} elseif ($this->config->get('config_stock_display')) {
							$stock_status = $result['quantity']; 
				} else {
							$stock_status = $this->language->get('text_instock'); 
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'                 => $image,
					'images'                => $product_images,
					'show_slider_controls'  => $show_slider_controls,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'stock'       => $stock_status,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			return $this->load->view('extension/module/special', $data);
		}
	}
}