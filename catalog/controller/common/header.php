<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerCommonHeader extends Controller {
	public function index() {
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		// Получение данных баннера
		$photo_left = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный идентификатор секции
			'setting' => 'photo_left', // Уникальный идентификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));

		// Инициализируем полученные данные
		$data['photo_left'] = $photo_left;

		$photo_right = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный идентификатор секции
			'setting' => 'photo_right', // Уникальный идентификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));
		// Инициализируем полученные данные
		$data['photo_right'] = $photo_right;


		$sale_banner_top = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный индификатор секции
			'setting' => 'sale_banner_top', // Уникальный индификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['sale_banner_top'] = $sale_banner_top;

		$link_baner_top = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный индификатор секции
			'setting' => 'link_baner_top', // Уникальный индификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['link_baner_top'] = $link_baner_top;

		$text_for = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный индификатор секции
			'setting' => 'text_baner_top', // Уникальный индификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));
		$data['text_for']= $text_for[$this->config->get('config_language_id')];

		$color_bagra = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный идентификатор секции
			'setting' => 'color_bagraund', // Уникальный идентификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));

		// Инициализируем полученные данные
		$data['color_bagra'] = $color_bagra;

		$color_sale = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный идентификатор секции
			'setting' => 'color_sale', // Уникальный идентификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));

		// Инициализируем полученные данные
		$data['color_sale'] = $color_sale;

		$link_baner_top = $this->load->controller('custom/setting/getValue', array(
			'section' => 'top_baner_ads', // Уникальный идентификатор секции
			'setting' => 'link_baner_top', // Уникальный идентификатор поля
			'page' => 'module_baner_top', // Код формы в админ-панеле
			'id' => '36', // Id объекта в админ-панеле
		));

		// Инициализируем полученные данные
		$data['link_baner_top'] = $link_baner_top;
	

		$block_menu_header_top = $this->load->controller('custom/setting/getValue', array(
			'section' => 'header_menu_top', // Уникальный индификатор секции
			'setting' => 'block_menu_header_top', // Уникальный индификатор поля
			'page' => 'setting' // Код формы в админ-панеле
		));
		
		// Инициализация массива данных
		$data['block_menu_header_top'] = array();
		
		// Получаем текущий идентификатор языка
		$language_id = $this->config->get('config_language_id');
		
		// Проверяем, что $offer_item_prop содержит данные и что они в правильном формате
		if (!empty($block_menu_header_top)) {
			foreach ($block_menu_header_top as $item_menu) {
				if (isset($item_menu['text_menu_top'][$language_id]) && isset($item_menu['link_menu_top'])) {
					$data['block_menu_header_top'][] = array(
						'text_menu_top' => html_entity_decode($item_menu['text_menu_top'][$language_id], ENT_QUOTES, 'UTF-8'),
						'link_menu_top' => html_entity_decode($item_menu['link_menu_top'], ENT_QUOTES, 'UTF-8'),
					);
				}
			}
		} else {
			error_log("No valid data in item_menu_footer");
		}
		
		
		


		$data['categories'] = array();
$categories = $this->model_catalog_category->getCategories(0);

foreach ($categories as $category) {
    // Получаем иконку для текущей категории, используя category_id
    $icon_category_header = $this->load->controller('custom/setting/getValue', array(
        'section' => 'icom_menu_heder',
        'setting' => 'icon_category_header',
        'page' => 'category',
        'id' => $category['category_id'], // Используем динамический ID
    ));

    // Проверка наличия иконки для категории
    if (!empty($icon_category_header) && isset($icon_category_header)) {
		$icon = $icon_category_header;
	} else {
		continue; // Пропускаем итерацию
	}

	$img_menu_left = $this->load->controller('custom/setting/getValue', array(
		'section' => 'icom_menu_heder', // Уникальный индификатор секции
		'setting' => 'img_menu_left', // Уникальный индификатор поля
		'page' => 'category', // Код формы в админ-панеле
		'id' => $category['category_id'],
	));
	
	// Инициализируем полученные данные
	
	if (!empty($img_menu_left) && isset($img_menu_left)) {
		$img_menu = $img_menu_left;
	} else {
		continue; // Пропускаем итерацию
	}

    // Проверяем, существует ли изображение категории
    if (!empty($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
        // Если изображение существует, изменяем его размер
        $image = $this->model_tool_image->resize($category['image'], 100, 100);
    } else {
        // Если изображение не существует, используем изображение-заполнитель
        $image = $this->model_tool_image->resize('placeholder.png', 100, 100);
    }

    // Получаем подкатегории (если есть)
    $children_data = array();
    $children = $this->model_catalog_category->getCategories($category['category_id']);

    foreach ($children as $child) {
        // Получаем подкатегории второго уровня
        $subchildren_data = array();
        $subchildren = $this->model_catalog_category->getCategories($child['category_id']);
        
        foreach ($subchildren as $subchild) {
            $subchildren_data[] = array(
                'name' => $subchild['name'],
                'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $subchild['category_id'])
            );
        }

        $children_data[] = array(
            'name' => $child['name'],
            'children' => $subchildren_data,
            'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
        );
    }

    // Сохраняем категорию в массив для вывода
    $data['categories'][] = array(
        'name' => $category['name'],
        'href' => $this->url->link('product/category', 'path=' . $category['category_id']),
        'image' => $image,
        'icon' => $icon, // Сохраняем иконку в массив
		'img_menu' => $img_menu,
        'children' => $children_data

    );
}




		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['robots'] = $this->document->getRobots();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');
		
		
		$host = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER : HTTP_SERVER;
		if ($this->request->server['REQUEST_URI'] == '/') {
			$data['og_url'] = $this->url->link('common/home');
		} else {
			$data['og_url'] = $host . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		}
		
		$data['og_image'] = $this->document->getOgImage();
		


		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['currency'] = $this->load->controller('common/currency');
		if ($this->config->get('configblog_blog_menu')) {
			$data['blog_menu'] = $this->load->controller('blog/menu');
		} else {
			$data['blog_menu'] = '';
		}
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/header', $data);


	
			}
	
}
