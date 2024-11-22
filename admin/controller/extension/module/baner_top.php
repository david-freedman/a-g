<?php

class ControllerExtensionModuleBanerTop extends Controller {

    private $error = array();
    private $token_var;
    private $extension_var;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->token_var = (version_compare(VERSION, '3.0', '>=')) ? 'user_token' : 'token';
        $this->extension_var = (version_compare(VERSION, '3.0', '>=')) ? 'marketplace' : 'extension';
    }

    public function index() {
        $data = $this->load->language('extension/module/baner_top');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/module');

        // Обработка POST-запроса
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('baner_top', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            // Редирект на страницу с настройками
            $this->response->redirect($this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true));
        }

        // Установка ошибок для шаблона
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';

        // Хлебные крошки (breadcrumbs)
        $data['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', $this->token_var . '=' . $this->session->data[$this->token_var], true)
            ),
            array(
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true)
            ),
            array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/baner_top', $this->token_var . '=' . $this->session->data[$this->token_var] . '&module_id=' . (isset($this->request->get['module_id']) ? $this->request->get['module_id'] : ''), true)
            )
        );

        // Формируем данные для шаблона
        $data['action'] = $this->url->link('extension/module/baner_top', $this->token_var . '=' . $this->session->data[$this->token_var] . '&module_id=' . (isset($this->request->get['module_id']) ? $this->request->get['module_id'] : ''), true);
        $data['cancel'] = $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true);

        // Данные о модуле
        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        // Поля модуля
        $data['name'] = isset($this->request->post['name']) ? $this->request->post['name'] : (isset($module_info['name']) ? $module_info['name'] : '');
        $data['status'] = isset($this->request->post['status']) ? $this->request->post['status'] : (isset($module_info['status']) ? $module_info['status'] : '');

        // Загружаем общие элементы страницы
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        // Выводим шаблон
        $this->response->setOutput($this->load->view('extension/module/baner_top', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/baner_top')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['name'])) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }
}
