<?php
class ControllerExtensionModuleLangdir extends Controller {
	private $error = [];

	public function index() {
		$this->load->language('extension/module/langdir');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
    
    $data['success'] = false;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('langdir', $this->request->post);

			//$this->session->data['success'] = $this->language->get('text_success');
			//$this->response->redirect($this->url->link('extension/module/langdir', 'user_token=' . $this->session->data['user_token'], true));
      
      $data['success'] = $this->language->get('text_success');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['errors'])) {
			$data['errors'] = $this->error['errors'];
		} else {
			$data['errors'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text'			 => $this->language->get('text_home'),
			'href'			 => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator'	 => false
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/langdir', 'user_token=' . $this->session->data['user_token'], true)
		];

		$module_info = $this->config->get('langdir');

		if (isset($this->request->post['langdir'])) {
			$data['langdir'] = $this->request->post['langdir'];
		} elseif (!empty($module_info)) {
			$data['langdir'] = $module_info;
		} else {
			$data['langdir'] = [];
		}

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'		 => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'		 => $store['name']
			);
		}

		if (isset($this->request->post['langdir_hreflang'])) {
			$data['langdir_hreflang'] = $this->request->post['langdir_hreflang'];
		} elseif ($this->config->get('langdir_hreflang')) {
			$data['langdir_hreflang'] = $this->config->get('langdir_hreflang');
		} else {
			$data['langdir_hreflang'] = [];
		}
		
		if (isset($this->request->post['langdir_dir'])) {
			$data['langdir_dir'] = $this->request->post['langdir_dir'];
		} elseif ($this->config->get('langdir_dir')) {
			$data['langdir_dir'] = $this->config->get('langdir_dir');
		} else {
			$data['langdir_dir'] = [];
		}

		if (isset($this->request->post['langdir_off'])) {
			$data['langdir_off'] = $this->request->post['langdir_off'];
		} elseif ($this->config->get('langdir_off')) {
			$data['langdir_off'] = $this->config->get('langdir_off');
		} else {
			$data['langdir_off'] = [];
		}


		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['langdir_data'])) {
			$data['langdir_data'] = $this->request->post['langdir_data'];
		} elseif ($this->config->get('langdir_data')) {
			$data['langdir_data'] = $this->config->get('langdir_data');
		} else {
			$data['langdir_data'] = [];
		}

		$data['action'] = $this->url->link('extension/module/langdir', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);

		$data['header']			 = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']			 = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/langdir', $data));
	}

	public function install() {
		$this->load->model('user/user_group');

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/langdir');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/langdir');

		$this->session->data['success'] = $this->language->get('text_success');
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/langdir')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if ((utf8_strlen($this->request->post['langdir_hreflang'][$language['language_id']]) < 2) || (utf8_strlen($this->request->post['langdir_hreflang'][$language['language_id']]) > 5)) {
				$this->error['errors']['hreflang'][$language['language_id']] = $this->language->get('error_hreflang');
			}
			
			if ((utf8_strlen($this->request->post['langdir_dir'][$language['language_id']]) < 2) || (utf8_strlen($this->request->post['langdir_dir'][$language['language_id']]) > 3)) {
				$this->error['errors']['dir'][$language['language_id']] = $this->language->get('error_dir');
			}
			
			
		}
		
		if (isset($this->error['errors'])) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
