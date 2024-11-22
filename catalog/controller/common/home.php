<?php
class ControllerCommonHome extends Controller {
	public function index() {
		


		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$canonical = $this->url->link('common/home');
			if ($this->config->get('config_seo_pro') && !$this->config->get('config_seopro_addslash')) {
				$canonical = rtrim($canonical, '/');
			}
			$this->document->addLink($canonical, 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/home', $data));

		$title_offer_text = $this->load->controller('custom/setting/getValue', array(
			'section' => 'offer_text_bock_home', // Уникальный индификатор секции
			'setting' => 'title_offer_text', // Уникальный индификатор поля
			'page' => 'module_offer_text_block' // Код формы в админ-панеле
		));
		
		// Инициализируем полученные данные
		$data['title_offer_text'] = $title_offer_text[$this->config->get('config_language_id')];


	}
}