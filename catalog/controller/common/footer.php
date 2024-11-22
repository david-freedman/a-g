<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// menu footer
		$logo_foter = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'logo_foter', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['logo_foter'] = $logo_foter;

		$text_foter = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'text_foter', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		
		$data['text_foter'] = isset($text_foter[$this->config->get('config_language_id')]) ? 
		html_entity_decode($text_foter[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

		$text_under_contact = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'text_under_contact', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['text_under_contact'] = isset($text_under_contact[$this->config->get('config_language_id')]) ? 
		html_entity_decode($text_under_contact[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

		$contact_foter_text = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'contact_foter_text', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['contact_foter_text'] = isset($contact_foter_text[$this->config->get('config_language_id')]) ? 
		html_entity_decode($contact_foter_text[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

		$adress_foter = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'adress_foter', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['adress_foter'] = isset($adress_foter[$this->config->get('config_language_id')]) ? 
		html_entity_decode($adress_foter[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

		$phone_foter = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'phone_foter', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['phone_foter'] = $phone_foter;

		$email_foter = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'email_foter', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['email_foter'] = $email_foter;

		$title_section_one_foter = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'title_section_one_foter', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['title_section_one_foter'] = isset($title_section_one_foter[$this->config->get('config_language_id')]) ? 
		html_entity_decode($title_section_one_foter[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

		$item_menu_footer = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'item_menu_footer', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализация массива данных
		$data['item_menu_footer'] = array();
		
		// Получаем текущий идентификатор языка
		$language_id = $this->config->get('config_language_id');
		
		// Проверяем, что $offer_item_prop содержит данные и что они в правильном формате
		if (!empty($item_menu_footer)) {
			foreach ($item_menu_footer as $item_menu) {
				if (isset($item_menu['text_link_menu_footer'][$language_id]) && isset($item_menu['link_item_menu_footer'])) {
					$data['item_menu_footer'][] = array(
						'text_link_menu_footer' => html_entity_decode($item_menu['text_link_menu_footer'][$language_id], ENT_QUOTES, 'UTF-8'),
						'link_item_menu_footer' => html_entity_decode($item_menu['link_item_menu_footer'], ENT_QUOTES, 'UTF-8'),
					);
				}
			}
		} else {
			error_log("No valid data in item_menu_footer");
		}
		
		

		$title_section_one_foter2 = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'title_section_one_foter2', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['title_section_one_foter2'] = isset($title_section_one_foter2[$this->config->get('config_language_id')]) ? 
		html_entity_decode($title_section_one_foter2[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';


		$item_menu_footer2 = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'item_menu_footer2', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализация массива данных
		$data['item_menu_footer2'] = array();
		
		// Получаем текущий идентификатор языка
		$language_id = $this->config->get('config_language_id');
		
		// Проверяем, что $offer_item_prop содержит данные и что они в правильном формате
		if (!empty($item_menu_footer2)) {
			// Проходим по каждому элементу массива $offer_item_prop
			foreach ($item_menu_footer2 as $item_menu) {
				// Проверяем, что данные для текущего языка существуют
				if (isset($item_menu['text_link_menu_footer'][$language_id]) && isset($item_menu['link_item_menu_footer'])) {
					$data['item_menu_footer2'][] = array(
						'text_link_menu_footer' => html_entity_decode($item_menu['text_link_menu_footer'][$language_id], ENT_QUOTES, 'UTF-8'),
						'link_item_menu_footer' => html_entity_decode($item_menu['link_item_menu_footer'][$language_id], ENT_QUOTES, 'UTF-8'),
					);
				}
			}
		} else {
			error_log("No valid data in offer_item_prop");
		}

		$title_section_one_foter3 = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'title_section_one_foter3', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		$data['title_section_one_foter3'] = isset($title_section_one_foter3[$this->config->get('config_language_id')]) ? 
		html_entity_decode($title_section_one_foter3[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

		$item_menu_footer3 = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'item_menu_footer3', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализация массива данных
		$data['item_menu_footer3'] = array();
		
		// Получаем текущий идентификатор языка
		$language_id = $this->config->get('config_language_id');
		
		// Проверяем, что $offer_item_prop содержит данные и что они в правильном формате
		if (!empty($item_menu_footer3)) {
			// Проходим по каждому элементу массива $offer_item_prop
			foreach ($item_menu_footer3 as $item_menu) {
				// Проверяем, что данные для текущего языка существуют
				if (isset($item_menu['text_link_menu_footer'][$language_id]) && isset($item_menu['link_item_menu_footer'])) {
					$data['item_menu_footer3'][] = array(
						'text_link_menu_footer' => html_entity_decode($item_menu['text_link_menu_footer'][$language_id], ENT_QUOTES, 'UTF-8'),
						'link_item_menu_footer' => html_entity_decode($item_menu['link_item_menu_footer'][$language_id], ENT_QUOTES, 'UTF-8'),
					);
				}
			}
		} else {
			error_log("No valid data in offer_item_prop");
		}

		$social_foter_name = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'social_foter_name', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		$data['social_foter_name'] = isset($social_foter_name[$this->config->get('config_language_id')]) ? 
		html_entity_decode($social_foter_name[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

		$block_soc_footer = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'block_soc_footer', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализация массива данных
		$data['block_soc_footer'] = array();
		$language_id = $this->config->get('config_language_id');

		if (!empty($block_soc_footer)) {
			foreach ($block_soc_footer as $item_menu) {
				if (!empty($item_menu['icon_soc_footer']) && !empty($item_menu['link_soc_footer'])) {
					$data['block_soc_footer'][] = array(
						'icon_soc_footer' => html_entity_decode($item_menu['icon_soc_footer'], ENT_QUOTES, 'UTF-8'),
						'link_soc_footer' => html_entity_decode($item_menu['link_soc_footer'], ENT_QUOTES, 'UTF-8'),
					);
				}
			}
		}


		
		$link_order_foter = $this->load->controller('custom/setting/getValue', array(
			'section' => 'foter_seting', // Уникальный индификатор секции
			'setting' => 'link_order_foter', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализация массива данных
		$data['link_order_foter'] = array();
		
		// Получаем текущий идентификатор языка
		$language_id = $this->config->get('config_language_id');
		
		// Проверяем, что $offer_item_prop содержит данные и что они в правильном формате
		if (!empty($link_order_foter)) {
			// Проходим по каждому элементу массива $offer_item_prop
			foreach ($link_order_foter as $item_menu) {
				// Проверяем, что данные для текущего языка существуют
				if (isset($item_menu['text_link_menu_footer'][$language_id]) && isset($item_menu['link_item_menu_footer'])) {
					$data['link_order_foter'][] = array(
						'text_link_menu_footer' => html_entity_decode($item_menu['text_link_menu_footer'][$language_id], ENT_QUOTES, 'UTF-8'),
						'link_item_menu_footer' => html_entity_decode($item_menu['link_item_menu_footer'][$language_id], ENT_QUOTES, 'UTF-8'),
					);
				}
			}
		} else {
			error_log("No valid data in offer_item_prop");
		}

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles('footer');
		
		return $this->load->view('common/footer', $data);
	}
}
