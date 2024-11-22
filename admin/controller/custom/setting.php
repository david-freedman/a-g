<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class ControllerCustomSetting extends Model {

    private $error = array();

    public $page = '';
    public $upload = array();
    public $page_id = 0;
    public $languages = array();
    public $custom_setting = array(
        'modification' => 'custom_config.ocmod.xml',
        'version' => '1.0.0',
    );

    public $default_setting = array(
        'setting_code' => '',
        'values_route' => '',
        'section_id' => '',
        'type' => '',
        'required' => 1,
        'status' => 1,
        'show_developer' => 0,
        'default_value' => '',
        'values_options' => array(),
        'regex' => '',
        'sort_order' => 0,
    );

    public function languages() {
        if (!empty($this->languages)) {
            return $this->languages;
        }
        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        $this->languages = $languages;

        return $this->languages;
    }

    public function index() {
        $this->load->language('custom/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (!$this->model_custom_setting->checkTables($this->tables())) {
            $this->install();
        }

        $this->getList();
    }

    public function add() {
        $this->load->language('custom/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $setting_id = $this->model_custom_setting->addSetting($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';
            if (isset($this->request->get['filter_title'])) {
                $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_section_id'])) {
                $url .= '&filter_section_id=' . $this->request->get['filter_section_id'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_integrated'])) {
                $url .= '&filter_integrated=' . $this->request->get['filter_integrated'];
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

            if (isset($this->request->get['redirect']) && $this->request->get['redirect']) {
                $this->response->redirect($this->url->link('custom/setting/edit', 'user_token=' . $this->session->data['user_token'] . '&setting_id=' . $setting_id . $url, true));
            } else {
                $this->response->redirect($this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
            }
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('custom/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_custom_setting->editSetting($this->request->get['setting_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_title'])) {
                $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_section_id'])) {
                $url .= '&filter_section_id=' . $this->request->get['filter_section_id'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_integrated'])) {
                $url .= '&filter_integrated=' . $this->request->get['filter_integrated'];
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
            if (isset($this->request->get['redirect']) && $this->request->get['redirect']) {
                $this->response->redirect($this->url->link('custom/setting/edit', 'user_token=' . $this->session->data['user_token'] . '&setting_id=' . $this->request->get['setting_id'] . $url, true));
            } else {
                $this->response->redirect($this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
            }
        }

        $this->getForm();
    }

    public function disintegrated() {
        $this->load->language('custom/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['setting_id'])) {
            $selected = array($this->request->get['setting_id']);
        }

        if (isset($selected) && $this->validateDisintegrated()) {
            foreach ($selected as $setting_id) {
                $this->model_custom_setting->disintegratedSetting($setting_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function integrated() {
        $this->load->language('custom/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['setting_id'])) {
            $selected = array($this->request->get['setting_id']);
        }

        if (isset($selected) && $this->validateintegrated()) {
            foreach ($selected as $setting_id) {
                $this->model_custom_setting->integratedSetting($setting_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function delete() {
        $this->load->language('custom/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['setting_id'])) {
            $selected = array($this->request->get['setting_id']);
        }

        if (isset($selected) && $this->validateDelete()) {
            foreach ($selected as $setting_id) {
                $this->model_custom_setting->deleteSetting($setting_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['setting_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $this->document->addStyle('view/javascript/summernote/summernote.css');
        $this->document->addScript('view/javascript/summernote/summernote.js');
        $this->document->addScript('view/javascript/summernote/summernote-image-attributes.js');
        $this->document->addScript('view/javascript/summernote/opencart.js');

        $this->document->addScript('view/javascript/custom_setting_form.js');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        foreach ($this->error as $error => $value) {
            $data['error'][$error] = $value;
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }

        if (isset($this->error['custom_handle'])) {
            $data['error_custom_handle'] = $this->error['custom_handle'];
        } else {
            $data['error_custom_handle'] = '';
        }

        if (isset($this->error['code'])) {
            $data['error_code'] = $this->error['code'];
        } else {
            $data['error_code'] = '';
        }

        if (isset($this->error['section'])) {
            $data['error_section'] = $this->error['section'];
        } else {
            $data['error_section'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_section_id'])) {
            $url .= '&filter_section_id=' . $this->request->get['filter_section_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_integrated'])) {
            $url .= '&filter_integrated=' . $this->request->get['filter_integrated'];
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

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['setting_id'])) {
            $data['action'] = $this->url->link('custom/setting/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('custom/setting/edit', 'user_token=' . $this->session->data['user_token'] . '&setting_id=' . $this->request->get['setting_id'] . $url, true);
        }

        $lists = array('select', 'radio', 'radio_old', 'checkbox', 'array', 'multiple_autocomplete', 'block');

        $data['cancel'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['setting_id'])) {
            $setting_info = $this->model_custom_setting->getSetting($this->request->get['setting_id']);
        }

        $data['sections'] = $this->model_custom_setting->getSections();

        $data['settings'] = $this->model_custom_setting->getSettings(array('filter_types' => $lists));

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->languages();

        $data['developer_status'] = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting');

        $data['types'] = $this->getTypes();
        $data['extensions'] = $this->getExtensions();
        $data['pages'] = $this->getPages();
        $data['controllers'] = $this->getControllers();

        $data['add'] = !isset($this->request->get['setting_id']);

        if (!empty($setting_info)) {
            $data['integrated'] = $setting_info['integrated'];
        } else {
            $data['integrated'] = 0;
        }

        if (isset($this->request->post['setting_page'])) {
            $data['setting_page'] = $this->request->post['setting_page'];
        } elseif (!empty($setting_info)) {
            $data['setting_page'] = $this->model_custom_setting->getSettingPages($this->request->get['setting_id']);
        } else {
            $data['setting_page'] = array('setting', 'store');
        }

        if (isset($this->request->post['setting_description'])) {
            $data['setting_description'] = $this->request->post['setting_description'];
        } elseif (!empty($setting_info)) {
            $data['setting_description'] = $this->model_custom_setting->getSettingDescriptions($this->request->get['setting_id']);
        } else {
            $data['setting_description'] = array();
        }

        foreach ($this->default_setting as $value_key => $default_value) {
            if (isset($this->request->post[$value_key])) {
                $data[$value_key] = $this->request->post[$value_key];
            } elseif (isset($setting_info[$value_key])) {
                $data[$value_key] = $setting_info[$value_key];
            } else {
                $data[$value_key] =  $default_value;
            }
        }

        $html_entity_decodes = array('default_value', 'regex');

        foreach ($html_entity_decodes as $value) {
            if (!empty($data[$value])) {
                $data[$value] = html_entity_decode($data[$value]);
            }
        }

        if (!empty($data['values_options'])) {
            $data['values_options'] = $this->valuesOptionsConvert($data['values_options']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('custom/setting/setting_form', $data));
    }

    protected function valuesOptionsConvert($values_options) {

        if (!empty($values_options['text'])) {
            foreach ($values_options['text'] as $language_id => $text) {
                $values_options['text'][$language_id] = html_entity_decode($text);
            }
        }
        if (!empty($values_options['accordion'])) {
            foreach ($values_options['accordion'] as $key => $accordion) {
                foreach ($accordion['descriptions'] as $language_id => $description) {
                    $values_options['accordion'][$key]['descriptions'][$language_id]['description'] = html_entity_decode($description['description']);
                }
            }
        }
        if (!empty($values_options['html'])) {
            foreach ($values_options['html'] as $language_id => $alert) {
                $values_options['html'][$language_id] = html_entity_decode($alert);
            }
        }

        if (!empty($values_options['settings'])) {
            foreach ($values_options['settings'] as $id => &$setting) {
                if ($setting_info = $this->model_custom_setting->getSetting($setting['setting']['setting_id'])) {
                    $setting['setting']['title'] = $setting_info['title'];
                } else {
                    unset($setting['setting']);
                }
                unset($setting);
            }
        }

        $array_values = array('model_filter', 'settings', 'values');

        foreach ($array_values as $array) {
            if (!empty($values_options[$array])) {
                $values_options[$array] = array_values($values_options[$array]);
            }
        }

        return $values_options;
    }

    protected function getList() {

        $developer_status = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting');

        if ($developer_status) {
            $this->document->addStyle('view/javascript/codemirror/lib/codemirror.css');
            $this->document->addStyle('view/javascript/codemirror/theme/monokai.css');
            $this->document->addScript('view/javascript/codemirror/lib/codemirror.js');
            $this->document->addScript('view/javascript/custom_setting/codemirror/php/php.js');
            $this->document->addScript('view/javascript/custom_setting/codemirror/clike/clike.js');
        }

        if (isset($this->request->get['filter_title'])) {
            $filter_title = $this->request->get['filter_title'];
        } else {
            $filter_title = '';
        }

        if (isset($this->request->get['filter_type'])) {
            $filter_type = $this->request->get['filter_type'];
        } else {
            $filter_type = '';
        }

        if (isset($this->request->get['filter_section_id'])) {
            $filter_section_id = $this->request->get['filter_section_id'];
        } else {
            $filter_section_id = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }

        if (isset($this->request->get['filter_integrated'])) {
            $filter_integrated = $this->request->get['filter_integrated'];
        } else {
            $filter_integrated = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'csd.title';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_type'])) {
            $url .= '&filter_type=' . $this->request->get['filter_type'];
        }

        if (isset($this->request->get['filter_section_id'])) {
            $url .= '&filter_section_id=' . $this->request->get['filter_section_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_integrated'])) {
            $url .= '&filter_integrated=' . $this->request->get['filter_integrated'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['breadcrumbs'] = array();


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['types'] = $this->getTypes();

        $data['developer_status'] = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting');

        $data['disintegrated'] = $this->url->link('custom/setting/disintegrated', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['integrated'] = $this->url->link('custom/setting/integrated', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['add'] = $this->url->link('custom/setting/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('custom/setting/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['settings'] = array();

        $filter_data = array(
            'filter_title'      => $filter_title,
            'filter_type'      => $filter_type,
            'filter_section_id'      => $filter_section_id,
            'filter_status'      => $filter_status,
            'filter_integrated'      => $filter_integrated,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $setting_total = $this->model_custom_setting->getTotalSettings($filter_data);

        if ($results = $this->model_custom_setting->getSettings($filter_data)) {
            foreach ($results as $result) {
                $data['settings'][] = array(
                    'setting_id'    => $result['setting_id'],
                    'setting_code'    => $result['setting_code'],
                    'title'            => $result['title'],
                    'integrated'               => $result['integrated'],
                    'section' => $result['section'],
                    'sort_order'      => $result['sort_order'],
                    'type'      => $this->language->get('text_type_' . $result['type']),
                    'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'edit'            => $this->url->link('custom/setting/edit', 'user_token=' . $this->session->data['user_token'] . '&setting_id=' . $result['setting_id'] . $url, true),
                    'delete'            => $this->url->link('custom/setting/delete', 'user_token=' . $this->session->data['user_token'] . '&setting_id=' . $result['setting_id'] . $url, true),
                    'integrate'            => $this->url->link('custom/setting/integrated', 'user_token=' . $this->session->data['user_token'] . '&setting_id=' . $result['setting_id'] . $url, true),
                    'disintegrate'            => $this->url->link('custom/setting/disintegrated', 'user_token=' . $this->session->data['user_token'] . '&setting_id=' . $result['setting_id'] . $url, true),
                );
            }
        }
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';
        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_type'])) {
            $url .= '&filter_type=' . $this->request->get['filter_type'];
        }

        if (isset($this->request->get['filter_section_id'])) {
            $url .= '&filter_section_id=' . $this->request->get['filter_section_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_integrated'])) {
            $url .= '&filter_integrated=' . $this->request->get['filter_integrated'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sections'] = $this->model_custom_setting->getSections();

        $data['filter_title'] = $filter_title;
        $data['filter_section_id'] = $filter_section_id;
        $data['filter_status'] = $filter_status;
        $data['filter_integrated'] = $filter_integrated;
        $data['filter_type'] = $filter_type;
        $data['sort_title'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . '&sort=csd.title' . $url, true);
        $data['sort_type'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . '&sort=cs.type' . $url, true);
        $data['sort_section'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . '&sort=section' . $url, true);
        $data['sort_status'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . '&sort=cs.status' . $url, true);
        $data['sort_setting_code'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . '&sort=cs.setting_code' . $url, true);
        $data['sort_sort_order'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . '&sort=cs.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_type'])) {
            $url .= '&filter_type=' . $this->request->get['filter_type'];
        }

        if (isset($this->request->get['filter_section_id'])) {
            $url .= '&filter_section_id=' . $this->request->get['filter_section_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_integrated'])) {
            $url .= '&filter_integrated=' . $this->request->get['filter_integrated'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $setting_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($setting_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($setting_total - $this->config->get('config_limit_admin'))) ? $setting_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $setting_total, ceil($setting_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('custom/setting/setting_list', $data));
    }

    public function settingsFields() {

        $json = array();
        $this->load->model('custom/setting');

        if (isset($this->request->get['setting_id'])) {
            $setting_id = $this->request->get['setting_id'];
        } else {
            $setting_id = 0;
        }

        $setting_info = $this->getSetting($setting_id);

        $values = $setting_info['values_options']['settings'];

        $allow_types = array('select', 'text');

        foreach ($values as $value) {
            $setting_child_info = $this->getSetting($value['setting']['setting_id']);
            if (in_array($setting_child_info['type'], $allow_types)) {
                $json[] = array(
                    'title' => $value['setting']['title'],
                    'setting_id' => $value['setting']['setting_id'],
                    'group' => $this->language->get('text_type_' . $setting_child_info['type']),
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function mb_unserialize($string) {
        $string = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($matches) {
                if (isset($matches[2]))
                    return 's:' . strlen($matches[2]) . ':"' . $matches[2] . '";';
            },
            $string
        );
        return unserialize($string);
    }

    public function getDownloadSetting($ids) {

        $settings = $this->model_custom_setting->getDownloadSettings(array('filter_ids' => $ids));
        foreach ($settings as $setting) {
            $children_ids = array();

            if (!empty($setting['values_options'])) {
                $setting['values_options'] = $this->mb_unserialize($setting['values_options']);
                if (!empty($setting['values_options']['settings'])) {
                    foreach ($setting['values_options']['settings'] as $key => $children_setting) {
                        if ($children_setting_info = $this->model_custom_setting->getSetting($children_setting['setting']['setting_id'])) {
                            $children_ids[$children_setting_info['setting_id']] = $children_setting_info['setting_id'];
                            $setting['values_options']['settings'][$key]['setting']['section_code'] = $children_setting_info['section_code'];
                            $setting['values_options']['settings'][$key]['setting']['setting_code'] = $children_setting_info['setting_code'];
                        }
                    }

                    $this->upload  = array_merge($this->upload, $this->getDownloadSetting($children_ids));
                }

                if (!empty($setting['values_options']['values_route']) && !is_array($setting['values_options']['values_route'])) {

                    if ($children_value_info = $this->model_custom_setting->getSetting($setting['values_options']['values_route'])) {
                        $this->upload  = array_merge($this->upload, $this->getDownloadSetting(array($setting['values_options']['values_route'])));
                        $setting['values_options']['values_route'] = array(
                            'section_code' => $children_value_info['section_code'],
                            'setting_code' => $children_value_info['setting_code']
                        );
                    }
                }

                $setting['values_options'] = serialize($setting['values_options']);
            }

            $this->upload['settings'][$setting['setting_id']] = $setting;
            $this->upload['sections'][$setting['section_id']] = $setting['section_id'];
        }

        return $this->upload;
    }

    public function download() {
        $this->load->language('custom/setting');
        $json = array();
        $this->load->model('custom/setting');

        $data = array();

        if (!empty($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } else {
            $selected = array();;
        }
        $this->upload = array();
        $languages = $this->languages();

        foreach ($languages as $language) {
            $data['languages'][$language['language_id']] = $language;
        }

        $filter_data = array(
            'filter_ids' => $selected,
        );


        $data['upload'] = array();
        $data['upload']['settings'] = array();
        $data['upload']['sections'] = array();



        $upload = $this->getDownloadSetting($selected);


        if (!empty($upload['settings'])) {
            foreach ($upload['settings'] as $key => $setting) {

                unset($setting['section_id']);
                unset($setting['setting_id']);
                $data['upload']['settings'][$key] = $setting;
            }
        }

        if (!empty($upload['sections'])) {
            foreach ($upload['sections'] as $section_id) {

                if ($section_data = $this->model_custom_setting->getDownloadSection($section_id)) {

                    $data['upload']['sections'][$section_data['section_id']] = $section_data;
                    unset($data['upload']['sections'][$section_data['section_id']]['section_id']);
                }
            }
        }
        $json['upload'] =  $data['upload'];

        $json['content'] = $this->load->view('custom/setting/download', $data);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function settingsPages() {

        $json = array();
        $this->load->model('custom/setting');

        if (isset($this->request->get['setting_id'])) {
            $setting_id = $this->request->get['setting_id'];
        } else {
            $setting_id = 0;
        }

        $json = $this->getSettingPages($setting_id);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getSettingPages($setting_id) {

        $this->load->language('custom/setting');
        $this->load->model('custom/setting');

        $pages = array();

        $results = $this->model_custom_setting->getSettingPages($setting_id);
        $allow_pages = $this->getPages($results);

        if (count($allow_pages)) {
            $pages['pages'] = array(
                'heading_title' => $this->language->get('text_pages'),
                'modules' => $allow_pages,
            );
        }

        $allow_extensions = $this->getExtensions($results);

        foreach ($allow_extensions as $code => $extension) {
            $pages[$code] = $extension;
        }

        return $pages;
    }

    public function getTypes() {

        $types = array();

        $types[] = array(
            'value' => 'text',
            'title' =>  $this->language->get('text_type_text'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'text_language',
            'title' =>  $this->language->get('text_type_text_language'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'textarea',
            'title' =>  $this->language->get('text_type_textarea'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'textarea_language',
            'title' =>  $this->language->get('text_type_textarea_language'),
            'group' => $this->language->get('text_group_text'),
        );


        $types[] = array(
            'value' => 'texteditor',
            'title' =>  $this->language->get('text_type_texteditor'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'texteditor_language',
            'title' =>  $this->language->get('text_type_texteditor_language'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'codemirror',
            'title' =>  $this->language->get('text_type_codemirror'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'number',
            'title' =>  $this->language->get('text_type_number'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'password',
            'title' =>  $this->language->get('text_type_password'),
            'group' => $this->language->get('text_group_text'),
        );

        $types[] = array(
            'value' => 'select',
            'title' =>  $this->language->get('text_type_select'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'select_route',
            'title' =>  $this->language->get('text_type_select_route'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'radio',
            'title' =>  $this->language->get('text_type_radio'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'old_radio',
            'title' =>  $this->language->get('text_type_old_radio'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'checkbox',
            'title' =>  $this->language->get('text_type_checkbox'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'autocomplete',
            'title' =>  $this->language->get('text_type_autocomplete'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'multiple_autocomplete',
            'title' =>  $this->language->get('text_type_multiple_autocomplete'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'range',
            'title' =>  $this->language->get('text_type_range'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'colorpicker',
            'title' =>  $this->language->get('text_type_colorpicker'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'datetimepicker',
            'title' =>  $this->language->get('text_type_datetimepicker'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'iconpicker',
            'title' =>  $this->language->get('text_type_iconpicker'),
            'group' => $this->language->get('text_group_select'),
        );

        $types[] = array(
            'value' => 'image',
            'title' =>  $this->language->get('text_type_image'),
            'group' =>  $this->language->get('text_group_image'),
        );

        $types[] = array(
            'value' => 'image_language',
            'title' =>  $this->language->get('text_type_image_language'),
            'group' =>  $this->language->get('text_group_image'),
        );

        $types[] = array(
            'value' => 'block',
            'title' =>  $this->language->get('text_type_block'),
            'group' =>  $this->language->get('text_group_extended'),
        );

        $types[] = array(
            'value' => 'tab',
            'title' =>  $this->language->get('text_type_tab'),
            'group' =>  $this->language->get('text_group_extended'),
        );

        $types[] = array(
            'value' => 'add_tab',
            'title' =>  $this->language->get('text_type_add_tab'),
            'group' =>  $this->language->get('text_group_extended'),
        );

        $types[] = array(
            'value' => 'juxtapose',
            'title' =>  $this->language->get('text_type_juxtapose'),
            'group' =>  $this->language->get('text_group_extended'),
        );

        $types[] = array(
            'value' => 'geocode',
            'title' =>  $this->language->get('text_type_geocode'),
            'group' =>  $this->language->get('text_group_map'),
        );


        $types[] = array(
            'value' => 'title',
            'title' =>  $this->language->get('text_type_title'),
            'group' =>  $this->language->get('text_group_html'),
        );

        $types[] = array(
            'value' => 'alert',
            'title' =>  $this->language->get('text_type_alert'),
            'group' =>  $this->language->get('text_group_html'),
        );

        $types[] = array(
            'value' => 'html',
            'title' =>  $this->language->get('text_type_html'),
            'group' =>  $this->language->get('text_group_html'),
        );

        $types[] = array(
            'value' => 'accordion',
            'title' =>  $this->language->get('text_type_accordion'),
            'group' =>  $this->language->get('text_group_html'),
        );

        $types[] = array(
            'value' => 'array',
            'title' =>  $this->language->get('text_type_array'),
            'group' =>  $this->language->get('text_group_other'),
        );

        $types[] = array(
            'value' => 'controller',
            'title' =>  $this->language->get('text_type_controller'),
            'group' =>  $this->language->get('text_group_other'),
        );
        return $types;
    }

    public function getControllers() {

        $categories = array();

        $categories[] = array(
            'value' => 'product',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_product'),
        );

        $categories[] = array(
            'value' => 'category',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_category'),
        );

        $categories[] = array(
            'value' => 'manufacturer',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_manufacturer'),
        );

        $categories[] = array(
            'value' => 'attribute',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_attribute'),
        );

        $categories[] = array(
            'value' => 'attribute_group',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_attribute_group'),
        );

        $categories[] = array(
            'value' => 'option',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_option'),
        );

        $categories[] = array(
            'value' => 'option_value',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_option_value'),
        );

        $categories[] = array(
            'value' => 'filter_group',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_filter_group'),
        );

        $categories[] = array(
            'value' => 'filter',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_filter'),
        );

        $categories[] = array(
            'value' => 'information',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_information'),
        );

        $categories[] = array(
            'value' => 'download',
            'group' => $this->language->get('select_catalog'),
            'title' =>  $this->language->get('select_download'),
        );

        $categories[] = array(
            'value' => 'stock_status',
            'group' => $this->language->get('select_localisation'),
            'title' =>  $this->language->get('select_stock_status'),
        );

        $categories[] = array(
            'value' => 'language',
            'group' => $this->language->get('select_localisation'),
            'title' =>  $this->language->get('select_language'),
        );

        $categories[] = array(
            'value' => 'country',
            'group' => $this->language->get('select_localisation'),
            'title' =>  $this->language->get('select_country'),
        );

        $categories[] = array(
            'value' => 'zone',
            'group' => $this->language->get('select_localisation'),
            'title' =>  $this->language->get('select_zone'),
        );

        $categories[] = array(
            'value' => 'geo_zone',
            'group' => $this->language->get('select_localisation'),
            'title' =>  $this->language->get('select_geo_zone'),
        );


        $categories[] = array(
            'value' => 'tax_class',
            'group' => $this->language->get('select_tax'),
            'title' =>  $this->language->get('select_tax_class'),
        );


        $categories[] = array(
            'value' => 'tax_rate',
            'group' => $this->language->get('select_tax'),
            'title' =>  $this->language->get('select_tax_rate'),
        );

        $categories[] = array(
            'value' => 'marketing',
            'group' => $this->language->get('select_marketing'),
            'title' =>  $this->language->get('select_marketing'),
        );

        $categories[] = array(
            'value' => 'coupon',
            'group' => $this->language->get('select_marketing'),
            'title' =>  $this->language->get('select_coupon'),
        );

        $categories[] = array(
            'value' => 'banner',
            'group' => $this->language->get('select_design'),
            'title' =>  $this->language->get('select_banner'),
        );

        $categories[] = array(
            'value' => 'layout',
            'group' => $this->language->get('select_design'),
            'title' =>  $this->language->get('select_layout'),
        );

        $categories[] = array(
            'value' => 'customer',
            'group' => $this->language->get('select_customer'),
            'title' =>  $this->language->get('select_customer'),
        );

        $categories[] = array(
            'value' => 'customer_group',
            'group' => $this->language->get('select_customer'),
            'title' =>  $this->language->get('select_customer_group'),
        );

        $categories[] = array(
            'value' => 'location',
            'group' => $this->language->get('select_setting'),
            'title' =>  $this->language->get('select_location'),
        );

        $categories[] = array(
            'value' => 'user_permission',
            'group' => $this->language->get('select_setting'),
            'title' =>  $this->language->get('select_user_permission'),
        );

        $categories[] = array(
            'value' => 'store',
            'group' => $this->language->get('select_setting'),
            'title' =>  $this->language->get('select_store'),
        );

        $categories[] = array(
            'value' => 'section',
            'group' => $this->language->get('select_other'),
            'title' =>  $this->language->get('select_section'),
        );

        $categories[] = array(
            'value' => 'setting',
            'group' => $this->language->get('select_other'),
            'title' =>  $this->language->get('select_setting'),
        );

        $categories[] = array(
            'value' => 'table',
            'group' => $this->language->get('select_other'),
            'title' =>  $this->language->get('select_table'),
        );

        $categories[] = array(
            'value' => 'api',
            'group' => $this->language->get('select_other'),
            'title' =>  $this->language->get('select_api'),
        );

        $categories[] = array(
            'value' => 'permission',
            'group' => $this->language->get('select_other'),
            'title' =>  $this->language->get('select_permission'),
        );

        $categories[] = array(
            'value' => 'other',
            'group' => $this->language->get('select_other'),
            'title' =>  $this->language->get('select_controller'),
        );

        return $categories;
    }

    public function getPages($filter = array()) {
        $pages = array();

        $categories = array(
            'setting/setting' => 'setting',
            'setting/store' => 'store',
            'custom/setting' => 'custom_setting',
            'catalog/product' => 'product',
            'catalog/category' => 'category',
            'catalog/manufacturer' => 'manufacturer',
            'catalog/information' => 'information',
            'catalog/option' => 'option',
            'catalog/attribute' => 'attribute',
            'catalog/attribute_group' => 'attribute_group',
            'catalog/download' => 'download',
            'catalog/review' => 'review',
            'catalog/filter' => 'filter',
            'customer/customer' => 'customer',
            'customer/customer_group' => 'customer_group',
            'customer/custom_field' => 'custom_field',
            'design/banner' => 'banner',
            'localisation/location' => 'location',
            'localisation/language' => 'language',
            'localisation/currency' => 'currency',
            'localisation/country' => 'country',
            'localisation/zone' => 'zone',
            'localisation/geo_zone' => 'geo_zone',
            'localisation/length_class' => 'length_class',
            'localisation/weight_class' => 'weight_class',
            'localisation/tax_class' => 'tax_class',
            'localisation/tax_rate' => 'tax_rate',
            'sale/return' => 'return',
            'sale/voucher' => 'voucher',
            'sale/voucher_theme' => 'voucher_theme',
        );

        foreach ($categories as $path => $category) {
            if (!empty($filter) && !in_array($category, $filter)) {
                continue;
            }
            if ($this->user->hasPermission('modify', $path)) {
                $this->load->language($path, $category);
                $pages[] = array(
                    'id' => $category,
                    'name' =>  $this->language->get($category)->get('heading_title'),
                );
            }
        }
        return $pages;
    }

    public function getExtensions($filter = array()) {

        $extensions = array();
        $files_extension = glob(DIR_APPLICATION . 'controller/extension/extension/*.php', GLOB_BRACE);

        $modules = array('analytics', 'captcha', 'dashboard', 'feed', 'feed', 'module', 'payment', 'report', 'shipping', 'total', 'theme');

        foreach ($files_extension as $file_extension) {
            $code = basename($file_extension, '.php');

            if (!in_array($code, $modules)) {
                continue;
            }

            if ($this->user->hasPermission('modify', 'extension/extension/' . $code)) {
                $extensions[$code] = array();
                $extensions[$code]['modules'] = array();
                $this->load->language('extension/extension/' . $code, 'extension');
                $extensions[$code]['heading_title'] =  $this->language->get('extension')->get('heading_title');

                $files = glob(DIR_APPLICATION . 'controller/extension/' . $code . '/*.php');

                if ($files) {
                    foreach ($files as $file) {
                        $extension = basename($file, '.php');
                        if (!empty($filter) && !in_array($code . '_' . $extension, $filter)) {
                            continue;
                        }
                        if ($this->user->hasPermission('modify', 'extension/' . $code . '/' . $extension)) {

                            $this->load->language('extension/' . $code . '/' . $extension, 'extension');

                            $extensions[$code]['modules'][] = array(
                                'name'      => $this->language->get('extension')->get('heading_title'),
                                'id'      => $code . '_' . $extension,
                            );
                        }
                    }
                }
            }

            if (isset($extensions[$code]) && !count($extensions[$code]['modules'])) {
                unset($extensions[$code]);
            }
        }

        return $extensions;
    }

    protected function validateForm() {

        if (!$this->user->hasPermission('modify', 'custom/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('custom/setting');

        if (!preg_match("/^[a-za-z0-9_]+$/", $this->request->post['setting_code'])) {
            $this->error['code'] = $this->language->get('error_latin');
        }

        if ($this->model_custom_setting->getTotalSettingByCode((isset($this->request->get['setting_id']) ? $this->request->get['setting_id'] : ''), $this->request->post['setting_code'], $this->request->post['section_id'])) {
            $this->error['code'] = $this->language->get('error_code_repeat');
        }

        if (empty($this->request->post['setting_code'])) {
            $this->error['code'] = $this->language->get('error_text');
        }
        if ($this->request->post['setting_code'] == 'descriptions') {
            $this->error['code'] = $this->language->get('error_reserve');
        }
        if (!$this->request->post['section_id']) {
            $this->error['section'] = $this->language->get('error_text');
        }

        if (!empty($this->request->post['regex']) && $this->invalidRegex($this->request->post['regex'])) {
            $this->error['regex'] = $this->invalidRegex($this->request->post['regex']);
        }

        foreach ($this->request->post['setting_description'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 64)) {
                $this->error['title'][$language_id] = $this->language->get('error_name');
            }
        }
        if ($this->request->post['type'] == 'controller') {
            if (isset($this->request->post['values_options']['model_filter'])) {
                foreach ($this->request->post['values_options']['model_filter'] as $filter_id => $filter) {
                    if (empty($filter['key'])) {
                        $this->error['setting_model_filter_' . $filter_id . '_key'] = $this->language->get('error_text');
                    }
                }
            }
            if ((empty($this->request->post['values_options']['controller']))) {
                $this->error['setting_values_options_controller'] = $this->language->get('error_text');
            }
            if (empty($this->request->post['values_options']['custom_handle'])) {
                $this->error['custom_handle'] = $this->language->get('error_text');
            }
            if (!empty($this->request->post['values_options']['custom_handle'])) {
                $parts = explode('/', preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->request->post['values_options']['custom_handle']));

                // Break apart the route
                while ($parts) {
                    $file = DIR_APPLICATION . 'controller/' . implode('/', $parts) . '.php';

                    if (is_file($file)) {
                        $route = true;
                        break;
                    } else {
                        $route = false;
                        array_pop($parts);
                    }
                }
                if (!$route) {
                    $this->error['custom_handle'] = $this->language->get('error_controller');
                }
            }
        }
        if (in_array($this->request->post['type'], array('range'))) {
            if ($this->request->post['values_options']['min'] == '') {
                $this->error['setting_values_options_min'] = $this->language->get('error_text');
            }
            if ($this->request->post['values_options']['max'] == '') {
                $this->error['setting_values_options_max'] = $this->language->get('error_text');
            }
            if ($this->request->post['values_options']['min'] > $this->request->post['values_options']['max']) {
                $this->error['setting_values_options_max'] = sprintf($this->language->get('error_min_count'), $this->request->post['values_options']['min']);
            }
            if ($this->request->post['values_options']['step'] <= 0) {
                $this->error['setting_values_options_step'] = sprintf($this->language->get('error_min_count'), 1);
            }
            if ($this->request->post['values_options']['step'] > abs($this->request->post['values_options']['max']) + abs($this->request->post['values_options']['min'])) {
                $this->error['setting_values_options_step'] = sprintf($this->language->get('error_max_count'), abs($this->request->post['values_options']['max']) + abs($this->request->post['values_options']['min']));
            }
        }

        if (in_array($this->request->post['type'], array('accordion'))) {
            if (empty($this->request->post['values_options']['accordion'])) {
                $this->error['setting_block_accordion'] = $this->language->get('error_text');
            } else {
                foreach ($this->request->post['values_options']['accordion'] as $key => $row) {
                    foreach ($row['descriptions'] as $language_id => $description) {
                        if (empty($description['title'])) {
                            $this->error['setting_block_accordion_' . $key . '_title_' . $language_id] = $this->language->get('error_text');
                        }
                        if (empty($description['description'])) {
                            $this->error['setting_block_accordion_' . $key . '_description_' . $language_id] = $this->language->get('error_text');
                        }
                    }
                }
            }
        }
        if (in_array($this->request->post['type'], array('autocomplete', 'multiple_autocomplete', 'select',  'add_tab',  'checkbox', 'radio', 'old_radio', 'array'))) {
            if (!empty($this->request->post['values_options']['json_filter']) && !preg_match(
                '/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/',
                preg_replace('/"(\\.|[^"\\\\])*"/', '', $this->request->post['values_options']['json_filter'])
            ) && !empty($this->request->post['values_options']['model_route'])) {
                $this->error['setting_values_options_model_route_json_filter'] = $this->language->get('error_json');
            }

            if (isset($this->request->post['values_options']['model_filter']) && $this->request->post['values_options']['model_route'] != '0') {
                foreach ($this->request->post['values_options']['model_filter'] as $filter_id => $filter) {
                    if (empty($filter['key'])) {
                        $this->error['setting_model_filter_' . $filter_id . '_key'] = $this->language->get('error_text');
                    }
                }
            }

            if (!empty($this->request->post['values_options'])) {

                if ((!empty($this->request->post['values_options']['custom_validate']) || !empty($this->request->post['values_route'])) && empty($this->request->post['values_options']['custom_handle'])) {
                    $this->error['custom_handle'] = $this->language->get('error_text');
                }

                if (!empty($this->request->post['values_options']['custom_handle'])) {
                    $parts = explode('/', preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->request->post['values_options']['custom_handle']));

                    // Break apart the route
                    while ($parts) {
                        $file = DIR_APPLICATION . 'controller/' . implode('/', $parts) . '.php';

                        if (is_file($file)) {
                            $route = true;
                            break;
                        } else {
                            $route = false;
                            array_pop($parts);
                        }
                    }
                    if (!$route) {
                        $this->error['custom_handle'] = $this->language->get('error_controller');
                    }
                }
                if ($this->request->post['values_options']['model_route'] == 'other') {
                    if (empty($this->request->post['values_route'])) {
                        $this->error['setting_values_route'] = $this->language->get('error_text');
                    } else {
                    }

                    if (empty($this->request->post['values_options']['title_row'])) {
                        $this->error['setting_values_row_title'] = $this->language->get('error_text');
                    }
                    if (empty($this->request->post['values_options']['value_row'])) {
                        $this->error['setting_values_row_value'] = $this->language->get('error_text');
                    }
                }
            }
        }
        if ($this->request->post['type'] == 'geocode') {
            if (empty($this->request->post['values_options']['height'])) {
                $this->error['setting_values_options_height'] = $this->language->get('error_text');
            } elseif ((int)$this->request->post['values_options']['height'] < 100 || (int)$this->request->post['values_options']['height'] > 800) {
                $this->error['setting_values_options_height'] = $this->language->get('error_size_map');
            }
        }

        if ($this->request->post['type'] == 'add_tab' || $this->request->post['type'] == 'tab' || $this->request->post['type'] == 'juxtapose') {

            if (!empty($this->request->post['values_options']['settings'])) {
                $settings_ready = array();
                foreach ($this->request->post['values_options']['settings'] as $row_id => $setting) {
                    if (empty($setting['setting']['setting_id'])) {
                        $this->error['setting_children_' . $row_id . '_value'] = $this->language->get('error_text');
                    }
                    if (in_array($setting['setting']['setting_id'], array_keys($settings_ready))) {
                        $this->error['setting_children_' . $row_id . '_value'] = $this->language->get('error_dublicate');
                        $this->error['setting_children_' . $settings_ready[$setting['setting']['setting_id']] . '_value'] = $this->language->get('error_dublicate');
                    }

                    if ($setting_info = $this->getSetting($setting['setting']['setting_id'])) {
                        if (in_array($setting_info['type'], array('alert', 'title', 'html'))) {
                            $this->error['setting_children_' . $row_id . '_value'] = $this->language->get('error_field_block');
                        }
                        if (isset($this->request->post['section_id']) && (int)$setting_info['section_id'] != (int)$this->request->post['section_id']) {
                            $this->error['setting_children_' . $row_id . '_value'] =  $this->language->get('error_field_section');
                        }
                    } else {
                        $this->error['setting_children_' . $row_id . '_value'] = $this->language->get('error_field_empty');
                    }

                    if (isset($this->request->get['setting_id']) && $setting['setting']['setting_id'] == $this->request->get['setting_id']) {
                        $this->error['setting_children_' . $row_id . '_value'] = $this->language->get('error_field_in_block');
                    }

                    $settings_ready[$setting['setting']['setting_id']] = $row_id;
                }
            } else {
                $this->error['setting_children_values'] = $this->language->get('error_text');
            }
        }

        if ($this->request->post['type'] == 'tab' ||  $this->request->post['type'] == 'juxtapose') {
            switch ($this->request->post['type']) {
                case 'tab':
                    $value = 'tabs';
                    break;
                case 'juxtapose':
                    $value = 'rows';
                    break;
            }
            if (isset($this->request->post['values_options']['values_route']) && $this->request->post['values_options']['values_route'] == '0') {
                if (empty($this->request->post['values_options'][$value])) {
                    $this->error['setting_values_options_childrens'] = $this->language->get('error_text');
                }
                if (empty($this->request->post['values_options']['settings'])) {
                    $this->error['setting_childrens_values'] = $this->language->get('error_text');
                }
                if (!empty($this->request->post['values_options'][$value])) {
                    $values_ready = array();
                    foreach ($this->request->post['values_options'][$value] as $row_id => $v) {
                        $key = (!empty($v['key']) ? $v['key'] : $row_id);

                        if (!preg_match("/^[a-za-z0-9_]+$/", $key)) {
                            $this->error['setting_values_options_childrens_row_' . $row_id . '-value'] = $this->language->get('error_latin');
                        }
                        if (in_array($key, array_keys($values_ready))) {
                            $this->error['setting_values_options_childrens_row_' . $row_id . '-value'] = $this->language->get('error_dublicate');
                            $this->error['setting_values_options_childrens_row_' . $values_ready[$key] . '-value'] = $this->language->get('error_dublicate');
                        }
                        foreach ($v['description'] as $language_id => $lang) {
                            if (empty(trim($lang['title']))) {
                                $this->error['setting_values_options_childrens_row_' . $row_id . '_description_' . $language_id . '_title'] = $this->language->get('error_text');
                            }
                        }
                        $values_ready[$key] = $row_id;
                    }
                }
            }
        }

        if ($this->request->post['type'] == 'block') {
            if ($this->request->post['values_options']['pre_rows'] && $this->request->post['values_options']['rows_limit'] && (int)$this->request->post['values_options']['rows_limit'] < (int)$this->request->post['values_options']['pre_rows']) {
                $this->error['setting_values_options_block_pre_rows'] = $this->language->get('error_pre_rows');
            }
            if (!empty($this->request->post['values_options']['rows_limit']) && (int)$this->request->post['values_options']['rows_limit'] <= 0) {
                $this->error['setting_values_options_block_rows_limit'] = sprintf($this->language->get('error_min_count'), 0);
            }
            if (!empty($this->request->post['values_options']['pre_rows']) && (int)$this->request->post['values_options']['pre_rows'] <= 0) {
                $this->error['setting_values_options_block_pre_rows'] = sprintf($this->language->get('error_min_count'), 0);
            }
            if (isset($this->request->post['values_options']['settings'])) {
                $settings_ready = array();

                foreach ($this->request->post['values_options']['settings'] as $column_id => $setting) {
                    foreach ($setting['description'] as $language_id => $lang) {
                        if (empty(trim($lang['title']))) {
                            $this->error['setting_block_options_values_row_' . $column_id . '_description_' . $language_id . '_title'] = $this->language->get('error_text');
                        }
                    }

                    if (empty($setting['setting']['setting_id'])) {
                        $this->error['setting_block_' . $column_id . '_value'] = $this->language->get('error_text');
                    }

                    if (in_array($setting['setting']['setting_id'], array_keys($settings_ready))) {
                        $this->error['setting_block_' . $column_id . '_value'] = $this->language->get('error_dublicate');
                        $this->error['setting_block_' . $settings_ready[$setting['setting']['setting_id']] . '_value'] = $this->language->get('error_dublicate');
                    }

                    if ($setting_info = $this->getSetting($setting['setting']['setting_id'])) {
                        if (in_array($setting_info['type'], array('alert', 'title', 'html'))) {
                            $this->error['setting_block_' . $column_id . '_value'] = $this->language->get('error_field_block');
                        }
                        if (isset($this->request->post['section_id']) && (int)$setting_info['section_id'] != (int)$this->request->post['section_id']) {
                            $this->error['setting_block_' . $column_id . '_value'] =  $this->language->get('error_field_section');
                        }
                    } else {
                        $this->error['setting_block_' . $column_id . '_value'] = $this->language->get('error_field_empty');
                    }
                    if (isset($this->request->get['setting_id']) && $setting['setting']['setting_id'] == $this->request->get['setting_id']) {
                        $this->error['setting_block_' . $column_id . '_value'] = $this->language->get('error_field_in_block');
                    }

                    $settings_ready[$setting['setting']['setting_id']] = $column_id;
                }
            }
        }

        if (isset($this->request->post['values_options']['values']) && (((!$this->request->post['values_options']['model_route']) || !isset($this->request->post['values_options']['model_route'])))) {
            foreach ($this->request->post['values_options']['values'] as $value_id => $value) {
                if ((utf8_strlen($value['value']) < 1) || (utf8_strlen($value['value']) > 64)) {
                    $this->error['setting_values_options_values_row_' . $value_id . '_value'] = $this->language->get('error_row_value');
                }
                foreach ($value['description'] as $language_id => $description) {
                    if ((utf8_strlen($description['title']) < 1) || (utf8_strlen($description['title']) > 64)) {
                        $this->error['setting_values_options_values_row_' . $value_id . '_description_' . $language_id . '_title'] = $this->language->get('error_row_title');
                    }
                }
            }
        }

        if (((isset($this->request->post['values_options']['model_route']) && !$this->request->post['values_options']['model_route']) || !isset($this->request->post['values_options']['model_route'])) && (!isset($this->request->post['values_options']['values']) || (isset($this->request->post['values_options']['values']) && count($this->request->post['values_options']['values']) < 2)) && in_array($this->request->post['type'], array('select', 'radio', 'old_radio'))) {
            // $this->error['warning'] = sprintf($this->language->get('error_min_setting'), 2);
            $this->error['setting_values_options_values'] = sprintf($this->language->get('error_min_setting'), 2);
        }

        if (((isset($this->request->post['values_options']['model_route']) && !$this->request->post['values_options']['model_route']) || !isset($this->request->post['values_options']['model_route'])) && (!isset($this->request->post['values_options']['values']) || (isset($this->request->post['values_options']['values']) && count($this->request->post['values_options']['values']) < 1)) && in_array($this->request->post['type'], array('checkbox', 'array'))) {
            //  $this->error['warning'] = sprintf($this->language->get('error_min_setting'), 1);
            $this->error['setting_values_options_values'] = sprintf($this->language->get('error_min_setting'), 1);
        }
        if ((!isset($this->request->post['values_options']['settings']) || (isset($this->request->post['values_options']['settings']) && count($this->request->post['values_options']['settings']) < 1)) && in_array($this->request->post['type'], array('block'))) {
            //  $this->error['warning'] = sprintf($this->language->get('error_min_column'), 1);
            $this->error['setting_block_values'] = sprintf($this->language->get('error_min_column'), 1);
        }


        if (in_array($this->request->post['type'], array('image', 'image_language'))) {

            if ((int)$this->request->post['values_options']['placeholder_size']['width'] <= 0) {
                $this->error['setting_values_options_placeholder_size'] = $this->language->get('error_text');
            }
            if ((int)$this->request->post['values_options']['placeholder_size']['height'] <= 0) {
                $this->error['setting_values_options_placeholder_size'] = $this->language->get('error_text');
            }
        }

        if (empty($this->error['warning']) && !empty($this->error)) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDisintegrated() {
        if (!$this->user->hasPermission('modify', 'custom/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->config->get('config_custom_setting_developer_status')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateintegrated() {
        if (!$this->user->hasPermission('modify', 'custom/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->config->get('config_custom_setting_developer_status')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'custom/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['setting_id'])) {
            $selected = array($this->request->get['setting_id']);
        }

        foreach ($selected as $setting_id) {
            $setting_info = $this->model_custom_setting->getSetting($setting_id);

            if ($setting_info['integrated']) {
                $this->error['warning'] = $this->language->get('error_integrated');
            }
        }
        return !$this->error;
    }

    protected function invalidRegex($regex) {
        if (@preg_match($regex, null) !== false) {
            return '';
        }

        $errors = array(
            PREG_NO_ERROR               => 'Code 0 : No errors',
            PREG_INTERNAL_ERROR         => 'Code 1 : There was an internal PCRE error',
            PREG_BACKTRACK_LIMIT_ERROR  => 'Code 2 : Backtrack limit was exhausted',
            PREG_RECURSION_LIMIT_ERROR  => 'Code 3 : Recursion limit was exhausted',
            PREG_BAD_UTF8_ERROR         => 'Code 4 : The offset didn\'t correspond to the begin of a valid UTF-8 code point',
            PREG_BAD_UTF8_OFFSET_ERROR  => 'Code 5 : Malformed UTF-8 data',
        );

        return $errors[preg_last_error()];
    }

    public function input_autocomplete() {
        $json = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if ($setting_info = $this->getSetting($this->request->post['setting_id'])) {
                $json = $setting_info['values'];
            };

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return $json;
        }
    }

    public function getTabSetting() {

        $json = array();
        $json['data'] = array();

        if ($setting_info = $this->getSetting($this->request->post['setting_id'])) {

            $json['section_code'] = $setting_info['section_code'];
            $json['setting_code'] = $setting_info['setting_code'];

            $json['tab_name'] = $this->language->get('text_tab') . ' ' . ((int)$this->request->post['index'] + 1);

            if (!empty($setting_info['values_options']['model_route'])) {
                if ($model_route_info = $this->getSettingValueModelTitle($setting_info, $this->request->post['index'])) {
                    $json['tab_name'] = (isset($model_route_info['title']) ? $model_route_info['title'] : $json['tab_name']);
                }
            }
            if (!isset($setting_info['id'])) {
                $setting_info['id'] = $setting_info['section_code'] . '-' . $setting_info['setting_code'];
            }
            if (!isset($setting_info['name'])) {
                $setting_info['name'] =  '[' . $setting_info['section_code'] . '][' . $setting_info['setting_code'] . ']';
            }

            if ($setting_info['type'] == 'add_tab') {
                $json['setting_info'] = $setting_info;

                foreach ($setting_info['values_options']['settings'] as $column) {

                    if ($children_setting = $this->getSetting($column['setting']['setting_id'])) {

                        $children_setting['id'] = $this->request->post['data_id'] . '-' . $this->request->post['index'] . '-' . $children_setting['setting_code'];
                        $children_setting['name'] =  $this->request->post['data_name'] . '[' . $this->request->post['index'] . '][' . $children_setting['setting_code'] . ']';
                        $children_setting['wrap'] = true;
                        $children_setting['children'] = true;
                        $setting = $this->settingHandle($children_setting);

                        $json['data'][] = array(
                            'setting_info' => $children_setting,
                            'setting' => $setting
                        );
                    }
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getBlockSetting() {

        $json = array();
        $json['data'] = array();

        if ($setting_info = $this->getSetting($this->request->post['setting_id'])) {

            $json['section_code'] = $setting_info['section_code'];
            $json['setting_code'] = $setting_info['setting_code'];

            if (!isset($setting_info['id'])) {
                $setting_info['id'] = $setting_info['section_code'] . '-' . $setting_info['setting_code'];
            }

            if (!isset($setting_info['name'])) {
                $setting_info['name'] =  '[' . $setting_info['section_code'] . '][' . $setting_info['setting_code'] . ']';
            }

            if ($setting_info['type'] == 'block') {
                foreach ($setting_info['values_options']['settings'] as $column) {

                    if ($children_setting = $this->getSetting($column['setting']['setting_id'])) {

                        $children_setting['id'] = $this->request->post['data_id'] . '-' . $this->request->post['index'] . '-' . $children_setting['setting_code'];
                        $children_setting['name'] =  $this->request->post['data_name'] . '[' . $this->request->post['index'] . '][' . $children_setting['setting_code'] . ']';
                        $children_setting['children'] = true;
                        $setting = $this->settingHandle($children_setting);

                        $json['data'][] = array(
                            'section_code' => $setting['section_code'],
                            'setting_code' => $setting['setting_code'],
                            'setting' => $setting,
                        );
                    }
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete() {

        $this->load->model('custom/setting');

        $json = array();

        if (isset($this->request->get['filter_title'])) {
            $filter_title = $this->request->get['filter_title'];
        } else {
            $filter_title = '';
        }

        if (isset($this->request->get['filter_nottype'])) {
            $filter_nottype = $this->request->get['filter_nottype'];
        } else {
            $filter_nottype = '';
        }

        if (isset($this->request->get['filter_types'])) {
            $filter_types = $this->request->get['filter_types'];
        } else {
            $filter_types = array();
        }

        if (isset($this->request->get['filter_section_id'])) {
            $filter_section_id = $this->request->get['filter_section_id'];
        } else {
            $filter_section_id = '';
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = 5;
        }

        $filter_data = array(
            'filter_title'  => $filter_title,
            'filter_nottype'  => $filter_nottype,
            'filter_types'  => $filter_types,
            'filter_section_id'  => $filter_section_id,
            'start'        => 0,
            'limit'        => $limit
        );

        $results = $this->model_custom_setting->getSettings($filter_data);

        foreach ($results as $result) {

            $json[] = array(
                'setting_id' => $result['setting_id'],
                'type' => $result['type'],
                'title'       => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getSetting($setting_id) {

        $this->load->language('custom/setting');
        $this->load->model('custom/setting');

        if ($setting_id) {
            if ($setting_info = $this->model_custom_setting->getSetting($setting_id)) {
                return $this->settingConvert($setting_info);
            }
        }
    }

    protected function settingConvert($setting_info, $getValues = true) {

        $html_entity_decodes = array('default_value', 'regex');

        foreach ($html_entity_decodes as $value) {
            if (!empty($setting_info[$value])) {
                $setting_info[$value] = html_entity_decode($setting_info[$value]);
            }
        }

        if (!empty($setting_info['values_options'])) {
            $setting_info['values_options'] = $this->valuesOptionsConvert($setting_info['values_options']);
        }

        if ($getValues) {
            $setting_info['values'] = $this->getSettingValues($setting_info);
        }

        return $setting_info;
    }

    protected function fieldValidate($setting_id, $value) {

        $error = array();

        if ($setting = $this->getSetting($setting_id)) {

            if (isset($setting['values_options']['custom_validate']) && !empty($setting['values_options']['custom_validate'])) {
                if ($cutom_error = $this->load->controller($setting['values_options']['custom_handle'] . '/validate', array('controller' => $setting['values_options']['custom_validate'], 'setting' => $setting, 'value' => $value))) {
                    $error[$setting_id] = $cutom_error;
                }
            }

            $error_text = (trim($setting['error_text']) ?: $this->language->get('error_text'));

            switch ($setting['type']) {
                case 'checkbox':

                    if ((int)$setting['required'] && !array_filter($value, function ($row) {
                        return $row != '';
                    })) {
                        $error[$setting_id] = $error_text;
                    }
                    break;

                case 'text_language':
                case 'textarea_language':
                case 'texteditor_language':
                case 'image_language':
                    if (is_array($value)) {
                        foreach ($value as $language_id => $value_lang) {
                            if ((int)$setting['required'] && empty($value_lang)) {
                                $error[$setting_id][$language_id] = $error_text;
                            }
                            if (!empty($setting['regex']) && !preg_match($setting['regex'], $value_lang)) {

                                $error[$setting_id][$language_id] = $error_text;
                            }
                        }
                    }
                    break;
                case 'multiple_autocomplete':
                    if ((int)$setting['required'] && empty($value)) {
                        $error[$setting_id] = $error_text;
                    }
                    break;
                case 'autocomplete':

                    if ((int)$setting['required'] && isset($value['value']) && trim($value['value']) == '') {
                        $error[$setting_id]  = $error_text;
                    }
                    if (!empty($setting['regex']) && !preg_match($setting['regex'], $value['value'])) {

                        $error[$setting_id]  = $error_text;
                    }
                    break;

                case 'block':
                case 'juxtapose':
                case 'tab':
                case 'add_tab':

                    if ((int)$setting['required']) {

                        if (empty($value)) {
                            $error[$setting_id]  = $error_text;
                        }
                    }
                    if (!empty($value)) {
                        foreach ($value as  $section) {
                            foreach ($section as $setting_code => $v) {

                                if ($children_setting_id = $this->model_custom_setting->getSettingIdByCode($setting['section_code'], $setting_code)) {
                                    if ($this->fieldValidate($children_setting_id, $v)) {
                                        $error[$children_setting_id]  = $error_text;
                                        $error[$setting_id]  = '';
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    break;
                default:
                    if ((int)$setting['required'] && $value == '') {
                        $error[$setting_id]  = $error_text;
                    }
                    if (!empty($setting['regex']) && !preg_match($setting['regex'], $value)) {

                        $error[$setting_id]  = $error_text;
                    }
                    break;
            }
        }

        return $error;
    }

    public function handleValidate() {

        if (!empty($this->request->post['custom_setting'])) {

            $this->load->model('custom/setting');

            $error = array();

            foreach ($this->request->post['custom_setting'] as $section_code => $section) {
                foreach ($section as $setting_code => $value) {
                    if ($setting_id = $this->model_custom_setting->getSettingIdByCode($section_code, $setting_code)) {
                        if ($result = $this->fieldValidate($setting_id, $value)) {
                            if (isset($result[$setting_id])) {
                                $error[$setting_id] = $result[$setting_id];
                            }
                        }
                    }
                }
            }

            if (!empty($error)) {
                return $error;
            }
        }
    }

    public function setValue($value, $setting_id, $page, $id = 0) {
        if ($setting_id && $page) {
            $this->load->model('custom/setting');
            $this->model_custom_setting->setValue($value, $setting_id, $page, $id);
        }
    }

    public function getValue($setting_id, $page, $id = 0) {
        if ($setting_id && $page) {
            $this->load->model('custom/setting');
            return $this->model_custom_setting->getValue($setting_id, $page, $id);
        }
    }

    protected function settingHandle($setting, $value = null) {

        $this->load->model('tool/image');

        $language_id = (int)$this->config->get('config_language_id');
        $placeholder = $this->model_tool_image->resize('no_image.png', (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['width'] : 100), (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100));
        $this->load->model('localisation/language');

        $values_options = &$setting['values_options'];

        if (!isset($value)) {
            $value = $setting['default_value'];
        }

        if (!isset($setting['id'])) {
            $setting['id'] = $setting['section_code'] . '-' . $setting['setting_code'];
        }
        if (!isset($setting['name'])) {
            $setting['name'] =  '[' . $setting['section_code'] . '][' . $setting['setting_code'] . ']';
        }

        if (!empty($values_options['placeholder'][$language_id])) {
            $setting['placeholder'] = $values_options['placeholder'][$language_id];
        } else {
            $setting['placeholder'] = '';
        }

        if (in_array($setting['type'], array('image', 'image_language'))) {
            $setting['placeholder'] =  $placeholder;
        }

        if ($setting['type'] == 'block') {
            foreach ($values_options['settings'] as $index => $children_setting) {
                $children_setting_info =  $this->getSetting($children_setting['setting']['setting_id']);
                $values_options['settings'][$index]['required'] = $children_setting_info['required'];
                $values_options['settings'][$index]['help'] = $children_setting_info['help'];
            }

            $value = $this->getSettingValue($setting, $value);
        }
        if ($setting['type'] == 'select_route') {

            if (!empty($values_options['values_route'])) {

                if ($route_setting_info = $this->getSetting($values_options['values_route'])) {

                    $setting['options'] = $this->getRouteChildrenValues($setting, $route_setting_info);
                }
            }

            $value = $this->getSettingValue($setting, $value);
        }

        if ($setting['type'] == 'juxtapose') {
            $setting['rows'] = array();
            $setting['settings'] = array();
            foreach ($values_options['settings'] as $index => $children_setting) {

                $children_setting_info =  $this->getSetting($children_setting['setting']['setting_id']);

                $values_options['settings'][$index]['required'] = $children_setting_info['required'];
                $values_options['settings'][$index]['help'] = $children_setting_info['help'];
            }

            if (!empty($values_options['values_route'])) {

                if ($route_setting_info = $this->getSetting($values_options['values_route'])) {
                    $setting['column_title'] =  $route_setting_info['title'];
                    $setting['rows'] = $this->getRouteChildrenValues($setting, $route_setting_info);
                }
            } else {

                $setting['column_title'] =  $this->language->get('entry_values');
                foreach ($values_options['rows'] as $index => $tab) {
                    $key = (!empty($tab['key']) ? $tab['key'] : $index);
                    $setting['rows'][$key] = array('title' => $tab['description'][$language_id]['title']);
                }
            }

            $value = $this->getSettingValue($setting, $value);
        }
        if ($setting['type'] == 'controller') {
            $setting['output'] = $this->load->controller($setting['values_options']['custom_handle'] . '/Field', array('controller' => $setting['values_options']['controller'], 'setting' => $setting, 'value' => $value));
        }
        if ($setting['type'] == 'add_tab') {
            $setting['tabs'] = array();
            $setting['settings'] = array();

            if (!empty($values_options['model_route'])) {
                $setting['text_route'] = $this->language->get('select_' . $values_options['model_route']);
                if (!empty($value) && is_array($value)) {

                    foreach ($value as $index => $tab) {
                        if ($model_route_info = $this->getSettingValueModelTitle($setting, $index)) {
                            $key = $index;
                            $title = (isset($model_route_info['title']) ? $model_route_info['title']  : $this->language->get('text_tab') . ' ' . ($key + 1));

                            $setting['tabs'][$key] = array(
                                'title' => $title
                            );
                        }
                    }
                } else {
                }
            } else {
                if (!empty($value) && is_array($value)) {
                    $value = array_values($value);
                    foreach ($value as $index => $tab) {
                        $key = $index;
                        $title = (!empty($tab['descriptions'][$language_id]['title']) ? $tab['descriptions'][$language_id]['title'] : $this->language->get('text_tab') . ' ' . ($key + 1));

                        $setting['tabs'][$key] = array(
                            'title' => $title
                        );
                        foreach ($this->languages() as $language) {
                            $setting['tabs'][$key]['descriptions'][$language['language_id']]['title'] = (!empty($tab['descriptions'][$language['language_id']]['title']) ? $tab['descriptions'][$language['language_id']]['title'] :  $title);
                        }
                    }
                }
            }

            $value = $this->getSettingValue($setting, $value);
        }

        if ($setting['type'] == 'tab') {
            $setting['tabs'] = array();
            if (!empty($values_options['values_route'])) {
                if ($route_setting_info = $this->getSetting($values_options['values_route'])) {
                    $setting['tabs'] = $this->getRouteChildrenValues($setting, $route_setting_info);
                }
            } else {
                foreach ($values_options['tabs'] as $index => $tab) {
                    $key = (!empty($tab['key']) ? $tab['key'] : $index);
                    $setting['tabs'][$key] = array('title' => $tab['description'][$language_id]['title']);
                }
            }
            $value = $this->getSettingValue($setting, $value);
        }

        if (!empty($values_options['text'])) {
            foreach ($values_options['text'] as &$html) {
                $html = html_entity_decode($html);
                unset($html);
            }
        }
        if (!empty($values_options['accordion'])) {
            foreach ($values_options['accordion'] as $key => &$accordion) {
                foreach ($accordion['descriptions'] as $language_id => $description) {
                    $accordion['descriptions'][$language_id]['description'] = html_entity_decode($description['description']);
                }
                unset($description);
            }
        }

        if (!empty($values_options['html'])) {
            foreach ($values_options['html'] as &$html) {
                $html = html_entity_decode($html);
                unset($html);
            }
        }

        if (is_array($value) && in_array($setting['type'], array('autocomplete'))) {
            $value = $this->getSettingValue($setting, $value);
        }

        if (is_array($value) && in_array($setting['type'], array('multiple_autocomplete'))) {
            $value = $this->getSettingValue($setting, array('values' => $value))['values'];
        }

        if (in_array($setting['type'], array('image'))) {
            if (!is_array($value) && is_file(DIR_IMAGE . $value)) {
                $image = $this->model_tool_image->resize($value, (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100), (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100));
            } else {
                $image = $this->model_tool_image->resize('no_image.png', (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100), (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100));
            }

            $setting['thumb'] = $image;
        }

        if (in_array($setting['type'], array('image_language'))) {
            if (is_array($value)) {
                foreach ($value as $language_id => $val) {
                    if (is_file(DIR_IMAGE . $val)) {
                        $image = $this->model_tool_image->resize($val, (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100), (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100));
                    } else {
                        $image = $this->model_tool_image->resize('no_image.png', (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100), (isset($setting['values_options']['placeholder_size']['height']) ? $setting['values_options']['placeholder_size']['height'] : 100));
                    }

                    $setting['thumb'][$language_id] = $image;
                }
            }
        }

        // Получаем список возможных значений
        $setting['value'] = $value;

        $setting['values'] = $this->getSettingValues($setting);

        if (!empty($setting['popover'])) {
            $setting['popover'] =  $this->load->view('custom/setting/fields/popover', array('popover' => $setting['popover']));
        }

        $setting['languages'] = $this->languages();

        $setting['disabled_label'] = array('title', 'alert', 'html');

        $setting['developer_status'] = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting');
        $setting['output'] = $this->load->view('custom/setting/fields/' . $setting['type'], $setting);

        if ($setting['developer_status'] && empty($setting['children'])) {
            $setting['view_code'] = $this->load->view('custom/setting/view_code', array(
                'id' => $setting['id'],
                'section_code' =>  $setting['section_code'],
                'setting_code' =>  $setting['setting_code'],
                'page' =>  $this->page,
                'page_id' =>  $this->page_id,
                'text_code_php_helper' =>  sprintf($this->language->get('text_code_php_helper'), $setting['setting_code'], $setting['section_code']),
            ));
        } else {
            $setting['view_code'] = '';
        }

        if (isset($setting['wrap']) && $setting['wrap']) {

            $setting['output'] = $this->load->view('custom/setting/wrapper', $setting);
        }

        return $setting;
    }

    public function getSettingType($setting_id) {
        $this->load->model('custom/setting');
        $type = $this->model_custom_setting->getSettingType($setting_id);
        return $type;
    }

    public function handle($config = array()) {

        if (!isset($config['page'])) {
            return;
        }

        if (!isset($config['id'])) {
            $config['id'] = 0;
        }

        $this->page = $config['page'];
        $this->page_id = $config['id'];

        $this->load->language('custom/setting');
        $this->load->model('custom/setting');
        $this->load->model('tool/image');

        if (!$this->model_custom_setting->checkTables($this->tables())) {
            return;
        }

        $error = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $error = $this->handleValidate();
        }

        $data['user_token'] = $this->session->data['user_token'];

        $output = array();

        $data['disabled_label'] = array('title', 'alert', 'html');

        $this->load->model('localisation/language');
        $data['languages'] = $this->languages();
        $data['language_id'] = (int)$this->config->get('config_language_id');

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['sections'] = $this->model_custom_setting->getSections(array('sort' => 'cst.sort_order', 'order' => 'ASC', 'get_settings' => $config['page']));

        $handleSettingsType = array();
        $handleSettings =  $this->arraySearchMergeKey($data['sections'], 'setting_id');

        if (!empty($handleSettings) && is_array($handleSettings)) {
            foreach ($handleSettings as $setting_id) {
                $handleSettingsType[$setting_id] = $this->getSettingType($setting_id);
            }
        }

        $handleTypes = $handleSettingsType;

        $data['developer_status'] = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting');

        foreach ($data['sections'] as $section_id => &$section) {
            if (!empty($section['settings'])) {

                foreach ($section['settings'] as &$setting) {
                    $value = $this->getValue($setting['setting_id'], $config['page'], $config['id']);
                    if (!isset($value)) {
                        $value = $setting['default_value'];
                    }
                    // Подставляем отправленные значения
                    if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['custom_setting'][$setting['section_code']][$setting['setting_code']])) {
                        $value = $this->request->post['custom_setting'][$setting['section_code']][$setting['setting_code']];
                    }

                    if (isset($error[$setting['setting_id']])) {
                        $setting['error'] = $error[$setting['setting_id']];
                    }

                    $setting['wrap'] = true;
                    $setting = $this->settingHandle($setting, $value);
                }
                unset($values_options);
                unset($setting);
            } else {
                unset($data['sections'][$section_id]);
            }
            unset($section);
        }

        if (!count($data['sections'])) {
            return;
        }

        $field_addons = array(
            'colorpickers' => array('colorpicker'),
            'iconpickers' => array('iconpicker'),
            'codemirrors' => array('codemirror'),
            'texteditors' => array('texteditor', 'texteditor_language'),
            'maps' => array('geocode'),
            'range' => array('range'),
        );

        $this->addScripts($handleTypes, $field_addons);

        $tab_limit = $this->config->get('config_custom_setting_tab_limit');

        if (isset($tab_limit)) {
            $data['tab_limit'] = $tab_limit;
        } else {
            $data['tab_limit'] = 1;
        }
        $data['view_code'] = '';

        $output['content'] = $this->load->view('custom/setting/content', $data);
        $output['nav'] = $this->load->view('custom/setting/nav');

        return $output;
    }

    public function addScripts($handleTypes, $field_addons) {
        $developer_status = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting');
        $yandex_map_apikey = $this->config->get('config_custom_setting_yandex_map_apikey');
        $google_map_apikey = $this->config->get('config_custom_setting_google_map_apikey');

        $this->document->addScript('view/javascript/custom_setting/inputmask/jquery.inputmask.js');

        if (array_intersect($handleTypes, $field_addons['colorpickers'])) {
            $this->document->addStyle('view/javascript/custom_setting/colorpicker/bootstrap-colorpicker.min.css');
            $this->document->addScript('view/javascript/custom_setting/colorpicker/bootstrap-colorpicker.min.js');
        }

        if (array_intersect($handleTypes, $field_addons['range'])) {
            $this->document->addStyle('view/javascript/custom_setting/rangeSlider/ion.rangeSlider.css');
            $this->document->addScript('view/javascript/custom_setting/rangeSlider/ion.rangeSlider.min.js');
        }

        if (array_intersect($handleTypes, $field_addons['iconpickers'])) {
            $this->document->addStyle('view/javascript/custom_setting/iconpicker/css/fontawesome-iconpicker.css');
            $this->document->addScript('view/javascript/custom_setting/iconpicker/js/fontawesome-iconpicker.js');
        }

        if (array_intersect($handleTypes, $field_addons['codemirrors']) || $developer_status) {
            $this->document->addStyle('view/javascript/codemirror/lib/codemirror.css');
            $this->document->addStyle('view/javascript/codemirror/theme/monokai.css');
            $this->document->addScript('view/javascript/codemirror/lib/codemirror.js');
            if ($developer_status) {
                $this->document->addScript('view/javascript/custom_setting/codemirror/php/php.js');
                $this->document->addScript('view/javascript/custom_setting/codemirror/twig/twig.js');
            }
            $this->document->addScript('view/javascript/codemirror/lib/xml.js');
            $this->document->addScript('view/javascript/codemirror/lib/formatting.js');
            $this->document->addScript('view/javascript/custom_setting/codemirror/htmlmixed/htmlmixed.js');
            $this->document->addScript('view/javascript/custom_setting/codemirror/mode/multiplex.js');
            $this->document->addScript('view/javascript/custom_setting/codemirror/clike/clike.js');
        }

        if (array_intersect($handleTypes, $field_addons['texteditors'])) {
            $this->document->addStyle('view/javascript/summernote/summernote.css');
            $this->document->addScript('view/javascript/summernote/summernote.js');
            $this->document->addScript('view/javascript/summernote/summernote-image-attributes.js');
            $this->document->addScript('view/javascript/summernote/opencart.js');
        }

        if ($geocodes = array_intersect($handleTypes, $field_addons['maps'])) {
            $mapTypeHandles = array();

            foreach ($geocodes as $setting_id => $type) {
                $setting_info =  $this->getSetting($setting_id);
                if (isset($setting_info['values_options']['map_type'])) {
                    $mapTypeHandles[] = $setting_info['values_options']['map_type'];
                }
            }

            if (array_intersect($mapTypeHandles, array('yandex'))) {
                $this->document->addScript('//api-maps.yandex.ru/2.1/?lang=' . $this->language->get('code') . '&apikey=' . $yandex_map_apikey);
            }

            if (array_intersect($mapTypeHandles, array('google'))) {
                $this->document->addScript('//maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places&key=' . $google_map_apikey);
            }

            if (array_intersect($mapTypeHandles, array('2gis'))) {
                $this->document->addScript('https://maps.api.2gis.ru/2.0/loader.js?pkg=full');
            }
        }

        $this->document->addScript('view/javascript/custom_setting.js');
    }

    public function arraySearchMergeKey($array, $key) {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key])) {
                $results[$array[$key]] = $array[$key];
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->arraySearchMergeKey($subarray, $key));
            }
        }

        return $results;
    }

    protected function getRouteChildrenValues($setting, $route_setting) {
        $childrens = array();
        // if(!isset($setting['values_options']['values_route_page'])){

        // }
        $values = $this->getValue($setting['values_options']['values_route'], $setting['values_options']['values_route_page'], $setting['values_options']['values_route_page_id']);

        switch ($route_setting['type']) {
            case 'autocomplete':
                if (!empty($values) && is_array($values)) {
                    if ($v = $this->getSettingValue($route_setting, $values)) {
                        $key = (!empty($v['value']) ? $v['value'] : 0);
                        $childrens[$key] = array(
                            'key' => $key,
                            'title' => $v['title']
                        );
                    }
                }

                break;
            case 'radio':
            case 'old_radio':
            case 'select':
                if (!empty($values) && !is_array($values)) {
                    if ($v = $this->getSettingValue($route_setting, $values)) {
                        $key = (!empty($v['value']) ? $v['value'] : 0);
                        $childrens[$key] = array(
                            'key' => $key,
                            'title' => $v['title']
                        );
                    }
                }
                break;
            case 'checkbox':
                if (!empty($values) && is_array($values)) {
                    foreach ($values as $index => $setting_value) {
                        if ($setting_value) {
                            if ($v = $this->getSettingValue($route_setting, $setting_value)) {
                                $key = (!empty($v['value']) ? $v['value'] : $index);
                                $childrens[$key] = array(
                                    'key' => $key,
                                    'title' => $v['title']
                                );
                            }
                        }
                    }
                }
                break;
            case 'multiple_autocomplete':
                $values_setting_info = $this->getSettingValue($route_setting, array('values' => $values));
                if (!empty($values_setting_info) && is_array($values_setting_info['values'])) {
                    foreach ($values_setting_info['values'] as $index => $setting_value) {
                        $key = (!empty($setting_value['value']) ? $setting_value['value'] : $index);
                        $childrens[$key] = array(
                            'key' => $key,
                            'title' => $setting_value['title']
                        );
                    }
                }
                break;
            case 'block':
                $value_setting_info  = $this->getSetting($setting['values_options']['values_route_field']);

                if (!empty($values) && is_array($values)) {
                    foreach ($values as $index => $v) {
                        $values_setting_info  = $this->getSettingValue($value_setting_info, $v[$value_setting_info['setting_code']]);
                        if (isset($v[$value_setting_info['setting_code']])) {
                            if ($values_setting_info) {
                                $key = (!empty($values_setting_info['value']) ? $values_setting_info['value'] : $index);
                            } else {
                                $key = $index;
                            }
                            $childrens[$key] = array(
                                'key' => $key,
                                'title' => $v[$value_setting_info['setting_code']]
                            );
                        }
                    }
                }

                break;
            default:
                if ($setting['type'] == 'juxtapose') {
                    $route_setting = $this->getSetting($setting['values_options']['values_route']);
                }
                if (!empty($route_setting['values']) && is_array($route_setting['values'])) {
                    foreach ($route_setting['values'] as $index => $setting_value) {
                        $key = (!empty($setting_value['value']) ? $setting_value['value'] : $index);
                        $childrens[$key] = array(
                            'key' => $key,
                            'title' => $setting_value['title']
                        );
                    }
                }

                break;
        }
        return $childrens;
    }

    protected function getChildrenHandle($setting, $childrens, $field, $wrap = false) {
        $data = array();
        foreach ($childrens as $index => $children) {
            $key = (!empty($children['key']) ? $children['key'] : (string)$index);

            $data[$key]['settings'] = array();

            foreach ($setting['values_options']['settings'] as  $settings) {
                if ($children_setting = $this->getSetting($settings['setting']['setting_id'])) {

                    $value = (isset($field[$key][$children_setting['setting_code']]) ? $field[$key][$children_setting['setting_code']] : null);

                    $error = (isset($value) ? $this->fieldValidate($settings['setting']['setting_id'], $value) : array());

                    if (!empty($error[$settings['setting']['setting_id']])) {
                        $children_setting['error'] = $error[$settings['setting']['setting_id']];
                    } else {
                        $children_setting['error'] = '';
                    }

                    $children_setting['id'] = $setting['id'] . '-' . $key . '-' . $children_setting['setting_code'];
                    $children_setting['name'] =   $setting['name'] . '[' . $key . '][' . $children_setting['setting_code'] . ']';
                    $children_setting['children'] = true;
                    $children_setting['wrap'] = $wrap;
                    $output = $this->settingHandle($children_setting, $value);

                    $data[$key]['settings'][] = $output;
                }
            }
        }
        return $data;
    }

    protected function getSettingValue($setting, $field = null) {

        $this->load->model('custom/setting');
        if ($setting['type'] == 'juxtapose') {
            $columns = array();
            if (!empty($setting['values_options']['values_route'])) {
                if ($route_setting = $this->getSetting($setting['values_options']['values_route'])) {
                    $columns = $this->getRouteChildrenValues($setting, $route_setting);
                }
            } else {
                $columns = $setting['values_options']['rows'];
            }
            $childrens = $this->getChildrenHandle($setting, $columns, $field, false);

            return $childrens;
        }
        // if ($setting['type'] == 'select_route') {
        //     $values = array();
        //     if (!empty($setting['values_options']['values_route'])) {
        //         if ($route_setting = $this->getSetting($setting['values_options']['values_route'])) {
        //             $values = $this->getRouteChildrenValues($setting, $route_setting);

        //         }
        //     } 

        //     return $values;
        // }
        if ($setting['type'] == 'tab') {
            $tabs = array();
            if (!empty($setting['values_options']['values_route'])) {
                if ($route_setting = $this->getSetting($setting['values_options']['values_route'])) {
                    $tabs = $this->getRouteChildrenValues($setting, $route_setting);
                }
            } else {
                $tabs = $setting['values_options']['tabs'];
            }

            $childrens = $this->getChildrenHandle($setting, $tabs, $field, true);
            return $childrens;
        }
        if ($setting['type'] == 'add_tab') {
            $tabs = array();

            if (!empty($field) && is_array($field)) {
                $value = array_values($field);
                foreach ($field as $index => $tab) {
                    $key = $index;
                    $tabs[$key] = array('key' => $key);
                }
            }

            $childrens = $this->getChildrenHandle($setting, $tabs, $field, true);

            return $childrens;
        }


        if ($setting['type'] == 'block') {
            $childrens = array();

            if ($field) {
                $field  = array_values($field);
            }

            $rows_limit = (!empty($setting['values_options']['rows_limit']) ? $setting['values_options']['rows_limit'] : (!empty($field) ? count($field) : (!empty($setting['values_options']['pre_rows']) ? $setting['values_options']['pre_rows'] : 0)));

            foreach ($setting['values_options']['settings'] as $index => $settings) {

                if ($children_setting = $this->getSetting($settings['setting']['setting_id'])) {

                    for ($i = 0; $i < $rows_limit; $i++) {

                        $value = (isset($field[$i][$children_setting['setting_code']]) ? $field[$i][$children_setting['setting_code']] : null);
                        $error = (isset($value) ? $this->fieldValidate($settings['setting']['setting_id'], $value) : array());

                        if (!empty($error[$settings['setting']['setting_id']])) {
                            $children_setting['error'] = $error[$settings['setting']['setting_id']];
                        } else {
                            $children_setting['error'] = '';
                        }
                        $children_setting['id'] = $setting['id'] . '-' . $i . '-' . $children_setting['setting_code'];
                        $children_setting['name'] =   $setting['name'] . '[' . $i . '][' . $children_setting['setting_code'] . ']';
                        $children_setting['children'] = true;
                        $childrens[$i][$children_setting['setting_code']] = $this->settingHandle($children_setting, $value);
                    }
                }
            }
            return $childrens;
        }

        if (!empty($setting['values_options']['model_route'])) {

            if ($setting['type'] == 'autocomplete' && isset($field['value'])) {
                $field = $this->getSettingValuesModel($setting, $field);
                $field = $field['values'][0];

                if (!empty($field['deleted'])) {
                    unset($field);
                } elseif (!trim($field['title'])) {
                    $field['value']  = trim($field['value']);
                }
            }
            if ($setting['type'] == 'multiple_autocomplete') {
                if (!empty($field['values'])) {
                    $field = $this->getSettingValuesModel($setting, $field);
                    foreach ($field['values'] as $value_id => $value) {
                        if (!empty($value['deleted'])) {
                            unset($field['values'][$value_id]);
                        }
                    }
                } else if (!empty($field['value'])) {
                    foreach ($field['value'] as $value_id => $value) {
                        if (empty($value['title'])) {
                            unset($field);
                        }
                    }
                } else if (empty($field['value'])) {
                    //  unset($field);
                }
            }

            if (in_array($setting['type'], array('radio', 'checkbox', 'old_radio', 'select'))) {

                $field = $this->getSettingValuesModel($setting, array('value' => $field));
                $field = $field['values'][0];

                if (!empty($vfield['deleted'])) {
                    unset($fieldv);
                } elseif (!trim($field['title'])) {
                    $field['value']  = trim($field['value']);
                }
            }
        }

        if (isset($field)) {
            return $field;
        }
    }

    protected function getValueTitleSetting($setting, $value) {

        $v = $this->getSettingValuesModel($setting, array('_value' => $value));
        $v = $v['values'][0];

        if (!empty($v['deleted'])) {
            unset($v);
        } elseif (!trim($v['title'])) {
            $v['value']  = trim($v['value']);
        }

        return $v;
    }

    protected function getSettingValueModelTitle($setting, $value) {

        if ($v = $this->getSettingValuesModel($setting, array('_value' => $value))) {
            if (empty($v['deleted'])) {
                return $v['values'][0];
            }
        }
    }

    protected function getSettingValuesModel($setting, $field = false) {
        $this->load->model('setting/setting');
        $this->load->model('custom/setting');
        $type = $setting['type'];

        $language_id = (int)$this->config->get('config_language_id');

        $filter_data = array(
            'start' => 0,
            'limit' => $this->config->get('config_limit_admin')
        );

        $values = array();

        if (in_array($type, array('autocomplete', 'radio', 'old_radio', 'checkbox', 'add_tab', 'select')) && isset($field['value'])) {
            $field['values'][] = $field;
        }

        if (isset($field['_value'])) {
            $field['values'][] = array('value' => $field['_value']);
        }

        if (!empty($setting['values_options']['model_filter'])) {
            foreach ($setting['values_options']['model_filter'] as $filter) {
                $filter_data[$filter['key']] = $filter['value'];
            }
        }

        if (!empty($this->request->post['filter_name'])) {
            $filter_data['filter_name'] = $this->request->post['filter_name'];
        }

        $model_route = (isset($setting['values_options']['model_route']) ? $setting['values_options']['model_route'] : null);

        switch ($model_route) {
            case 'section':
                if ($field) {
                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_custom_setting->getSection($value['value'])) {
                            $value['title']   = strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'));
                        } else {
                            $value['deleted'] = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_custom_setting->getSections($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['section_id'],
                        );
                    }
                }
                break;
            case 'setting':

                if ($field) {
                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_custom_setting->getSetting($value['value'])) {
                            $value['title']   = strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'));
                        } else {
                            $value['deleted'] = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_custom_setting->getSettings($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['setting_id'],
                        );
                    }
                }
                break;
            case 'api':
                $this->load->model('user/api');
                if ($field) {
                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_user_api->getApi($value['value'])) {
                            $value['title']  = $result['username'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_user_api->getApis($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>   $result['username'],
                            'value' =>  $result['api_id'],
                        );
                    }
                }
                break;
            case 'permission':
                $this->load->model('tool/backup');
                $permissions = array();
                $ignore = array(
                    'common/dashboard',
                    'common/startup',
                    'common/login',
                    'common/logout',
                    'common/forgotten',
                    'common/reset',
                    'common/footer',
                    'common/header',
                    'error/not_found',
                    'error/permission'
                );

                $files = array();
                // Make path into an array
                $path = array(DIR_APPLICATION . 'controller/*');

                // While the path array is still populated keep looping through
                while (count($path) != 0) {
                    $next = array_shift($path);

                    foreach (glob($next) as $file) {
                        // If directory add to path array
                        if (is_dir($file)) {
                            $path[] = $file . '/*';
                        }

                        // Add the file to the files to be deleted array
                        if (is_file($file)) {
                            $files[] = $file;
                        }
                    }
                }

                // Sort the file array
                sort($files);

                foreach ($files as $file) {
                    $controller = substr($file, strlen(DIR_APPLICATION . 'controller/'));

                    $permission = substr($controller, 0, strrpos($controller, '.'));

                    if (!in_array($permission, $ignore)) {
                        $permissions[] = $permission;
                    }
                }

                if ($field) {

                    foreach ($field['values'] as &$value) {


                        if (isset($permissions[array_search($value['value'], $permissions)])) {
                            $value['title']  =  $permissions[array_search($value['value'], $permissions)];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {


                    foreach ($permissions as $result) {
                        if ((!empty($filter_data['filter_name']) && !preg_match("/" . $filter_data['filter_name'] . "/is", var_export($result, true)))) continue;
                        $values[] = array(
                            'title' =>   $result,
                            'value' => $result,

                        );
                    }
                    $values = array_slice($values, 0, $filter_data['limit']);
                }
                break;
            case 'table':
                $this->load->model('tool/backup');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        $result = $this->model_tool_backup->getTables();

                        if (isset($result[array_search($value['value'], $result)])) {
                            $value['title']  =  $result[array_search($value['value'], $result)];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_tool_backup->getTables();

                    foreach ($results as $result) {
                        if ((!empty($filter_data['filter_name']) && !preg_match("/" . $filter_data['filter_name'] . "/is", var_export($result, true)))) continue;
                        $values[] = array(
                            'title' =>   $result,
                            'value' => $result,

                        );
                    }
                    $values = array_slice($values, 0, $filter_data['limit']);
                }
                break;
            case 'tax_rate':
                $this->load->model('localisation/tax_rate');

                if ($field) {

                    foreach ($field['values'] as &$value) {

                        if ($result = $this->model_localisation_tax_rate->getTaxRate($value['value'])) {
                            $value['title']  =  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_tax_rate->getTaxRates($filter_data);
                    foreach ($results as $result) {

                        $values[] = array(
                            'title' =>   strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['tax_rate_id'],

                        );
                    }
                }
                break;
            case 'tax_class':
                $this->load->model('localisation/tax_class');

                if ($field) {

                    foreach ($field['values'] as &$value) {

                        if ($result = $this->model_localisation_tax_class->getTaxClass($value['value'])) {
                            $value['title']  =  strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'));
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_tax_class->getTaxClasses($filter_data);
                    foreach ($results as $result) {

                        $values[] = array(
                            'title' =>   strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['tax_class_id'],

                        );
                    }
                }
                break;
            case 'download':
                $this->load->model('catalog/download');

                if ($field) {

                    foreach ($field['values'] as &$value) {

                        if ($result = $this->model_catalog_download->getDownload($value['value'])) {
                            $value['title']  =  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_download->getDownloads($filter_data);
                    foreach ($results as $result) {

                        $values[] = array(
                            'title' =>   strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['download_id'],

                        );
                    }
                }
                break;
            case 'filter_group':
                $this->load->model('catalog/filter');

                if ($field) {

                    foreach ($field['values'] as &$value) {

                        if ($result = $this->model_catalog_filter->getFilterGroup($value['value'])) {
                            $value['title']  =  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_filter->getFilterGroups($filter_data);
                    foreach ($results as $result) {

                        $values[] = array(
                            'title' =>   strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['filter_group_id'],

                        );
                    }
                }
                break;
            case 'filter':
                $this->load->model('catalog/filter');

                if ($field) {

                    foreach ($field['values'] as &$value) {

                        if ($result = $this->model_catalog_filter->getFilter($value['value'])) {
                            if ($type == 'select') {
                                $value['title']  =  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
                            } else {
                                $value['title']  =  strip_tags(html_entity_decode($result['group'] . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8'));
                            }
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_filter->getFilters($filter_data);
                    foreach ($results as $result) {
                        if ($type == 'select') {
                            $values[] = array(
                                'title' =>   strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                                'value' => $result['filter_id'],
                                'group' => $result['group']
                            );
                        } else {
                            $values[] = array(
                                'title' =>   strip_tags(html_entity_decode($result['group'] . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8')),
                                'value' => $result['filter_id'],

                            );
                        }
                    }
                }
                break;
            case 'zone':
                $this->load->model('localisation/zone');
                $this->load->model('localisation/country');
                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_zone->getZone($value['value'])) {
                            $group = $this->model_localisation_country->getCountry($result['country_id'])['name'];
                            if ($type == 'select') {
                                $value['title']  = $result['name'];
                            } else {

                                $value['title']  = strip_tags(html_entity_decode($group . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8'));
                            }
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_zone->getZones($filter_data);
                    foreach ($results as $result) {
                        if ($type == 'select') {

                            $values[] = array(
                                'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                                'value' => $result['zone_id'],
                                'group' => $result['country'],
                            );
                        } else {
                            $values[] = array(
                                'title' =>  strip_tags(html_entity_decode($result['country'] . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8')),
                                'value' => $result['zone_id'],

                            );
                        }
                    }
                }
                break;
            case 'option_value':
                $this->load->model('catalog/option');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_catalog_option->getOptionValue($value['value'])) {
                            $group = $this->model_catalog_option->getOption($result['option_id'])['name'];
                            if ($type == 'select') {
                                $value['title']  = $result['name'];
                            } else {

                                $value['title']  = strip_tags(html_entity_decode($group . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8'));
                            }
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_option->getAllOptionValues($filter_data);
                    foreach ($results as $result) {
                        $group = $this->model_catalog_option->getOption($result['option_id'])['name'];
                        if ($type == 'select') {

                            $values[] = array(
                                'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                                'value' => $result['option_value_id'],
                                'group' => $group,
                            );
                        } else {
                            $values[] = array(
                                'title' =>  strip_tags(html_entity_decode($group . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8')),
                                'value' => $result['option_value_id'],

                            );
                        }
                    }
                }
                break;
            case 'option':
                $this->load->model('catalog/option');
                $this->load->language('catalog/option');
                if ($field) {

                    foreach ($field['values'] as &$value) {

                        if ($result = $this->model_catalog_option->getOption($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_option->getOptions($filter_data);
                    foreach ($results as $result) {
                        $type = '';

                        if ($result['type'] == 'select' || $result['type'] == 'radio' || $result['type'] == 'checkbox') {
                            $type = $this->language->get('text_choose');
                        }

                        if ($result['type'] == 'text' || $result['type'] == 'textarea') {
                            $type = $this->language->get('text_input');
                        }

                        if ($result['type'] == 'file') {
                            $type = $this->language->get('text_file');
                        }

                        if ($result['type'] == 'date' || $result['type'] == 'datetime' || $result['type'] == 'time') {
                            $type = $this->language->get('text_date');
                        }
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['option_id'],
                            'group' => $type
                        );
                    }
                }
                break;
            case 'attribute_group':
                $this->load->model('catalog/attribute_group');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_catalog_attribute_group->getAttributeGroupDescriptions($value['value'])) {
                            $value['title']  = $result[$language_id]['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_attribute_group->getAttributeGroups($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['attribute_group_id'],
                        );
                    }
                }
                break;

            case 'attribute':
                $this->load->model('catalog/attribute');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_catalog_attribute->getAttribute($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_attribute->getAttributes($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['attribute_id'],
                            'group' => $result['attribute_group']
                        );
                    }
                }
                break;
            case 'category':
                $this->load->model('catalog/category');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_catalog_category->getCategory($value['value'])) {
                            $value['title']  =  ($result['path']) ? $result['path'] . ' &gt; ' . $result['name'] : $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_category->getCategories($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['category_id'],
                        );
                    }
                }
                break;
            case 'information':
                $this->load->model('catalog/information');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_catalog_information->getInformationDescriptions($value['value'])) {
                            $value['title']  = $result[$language_id]['title'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_information->getInformations($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['information_id'],
                        );
                    }
                }
                break;
            case 'manufacturer':
                $this->load->model('catalog/manufacturer');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_catalog_manufacturer->getManufacturer($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_manufacturer->getManufacturers($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['manufacturer_id'],
                        );
                    }
                }
                break;
            case 'location':
                $this->load->model('localisation/location');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_location->getLocation($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_location->getLocations($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['location_id'],
                        );
                    }
                }
                break;
            case 'layout':
                $this->load->model('design/layout');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_design_layout->getLayout($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_design_layout->getLayouts($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['layout_id'],
                        );
                    }
                }
                break;
            case 'banner':
                $this->load->model('design/banner');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_design_banner->getBanner($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_design_banner->getBanners($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['banner_id'],
                        );
                    }
                }
                break;
            case 'customer':
                $this->load->model('customer/customer');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_customer_customer->getCustomer($value['value'])) {
                            $value['title']  = $result['firstname'] . ' ' . $result['lastname'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_customer_customer->getCustomers($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['customer_id'],
                        );
                    }
                }
                break;
            case 'customer_group':
                $this->load->model('customer/customer_group');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_customer_customer_group->getCustomerGroup($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_customer_customer_group->getCustomerGroups($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['customer_group_id'],
                        );
                    }
                }
                break;
            case 'user_permission':
                $this->load->model('user/user_group');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_user_user_group->getUserGroup($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_user_user_group->getUserGroups($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['user_group_id'],
                        );
                    }
                }
                break;
            case 'marketing':
                $this->load->model('marketing/marketing');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_marketing_marketing->getMarketing($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_marketing_marketing->getMarketings($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['marketing_id'],
                        );
                    }
                }
                break;
            case 'coupon':
                $this->load->model('marketing/coupon');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_marketing_coupon->getCoupon($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_marketing_coupon->getCoupons($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['coupon_id'],
                        );
                    }
                }
                break;
            case 'geo_zone':
                $this->load->model('localisation/geo_zone');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_geo_zone->getGeoZone($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_geo_zone->getGeoZones($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['geo_zone_id'],
                        );
                    }
                }
                break;
            case 'order_status':
                $this->load->model('localisation/order_status');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_order_status->getOrderStatus($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_order_status->getOrderStatuses($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['order_status_id'],
                        );
                    }
                }
                break;
            case 'stock_status':
                $this->load->model('localisation/stock_status');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_stock_status->getStockStatus($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_stock_status->getStockStatuses($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['stock_status_id'],
                        );
                    }
                }
                break;
            case 'currency':
                $this->load->model('localisation/currency');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_currency->getCurrency($value['value'])) {
                            $value['title']  = $result['title'] . (($result['code'] == $this->config->get('config_currency')) ? ' ' . $this->language->get('text_default') . ')' : null);
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_currency->getCurrencies($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' => $result['title'] . (($result['code'] == $this->config->get('config_currency')) ?  ' (' . $this->language->get('text_default') . ')' : null),
                            'value' => $result['currency_id'],
                        );
                    }
                }
                break;


            case 'country':
                $this->load->model('localisation/country');

                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_country->getCountry($value['value'])) {
                            $value['title']  = $result['name'] . (($result['country_id'] == $this->config->get('config_country_id')) ? ' ' . $this->language->get('text_default') . ')' : null);
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_country->getCountries($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' => $result['name'] . (($result['country_id'] == $this->config->get('config_country_id')) ?  ' (' . $this->language->get('text_default') . ')' : null),
                            'value' => $result['country_id'],
                        );
                    }
                }
                break;
            case 'product':
                $this->load->model('catalog/product');
                $this->load->model('catalog/option');
                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_catalog_product->getProduct($value['value'])) {
                            $value['title']  = strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_catalog_product->getProducts($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['product_id'],
                        );
                    }
                }
                break;

            case 'language':
                $this->load->model('localisation/language');
                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_language->getLanguage($value['value'])) {
                            $value['title']  = $result['name'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_language->getLanguages($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['language_id'],
                        );
                    }
                }
                break;

            case 'length_class':
                $this->load->model('localisation/length_class');
                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_length_class->getLengthClass($value['value'])) {
                            $value['title']  = $result['title'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_length_class->getLengthClasses($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['length_class_id'],
                        );
                    }
                }
                break;

            case 'weight_class':
                $this->load->model('localisation/weight_class');
                if ($field) {

                    foreach ($field['values'] as &$value) {
                        if ($result = $this->model_localisation_weight_class->getWeightClass($value['value'])) {
                            $value['title']  = $result['title'];
                        } else {
                            $value['deleted']  = true;
                        }
                        unset($value);
                    }
                } else {
                    $results = $this->model_localisation_weight_class->getWeightClasses($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['weight_class_id'],
                        );
                    }
                }
                break;
            case 'store':
                $this->load->model('setting/store');
                if ($field) {
                    foreach ($field['values'] as &$value) {
                        if ($value['value'] == '0') {
                            $value['title'] = $this->config->get('config_name');
                        } else {
                            if ($result = $this->model_setting_store->getStore($value['value'])) {
                                $value['title']   = $result['name'];
                            } else {
                                $value['deleted'] = true;
                            }
                        }
                        unset($value);
                    }
                } else {

                    $values[] = array(
                        'title' => $this->config->get('config_name'),
                        'value' => '0',
                    );
                    $results = $this->model_setting_store->getStores($filter_data);
                    foreach ($results as $result) {
                        $values[] = array(
                            'title' =>  strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                            'value' => $result['store_id'],
                        );
                    }
                }
                break;

            case 'other':
                if ($field) {
                    foreach ($field['values'] as &$value) {
                        $results =  $this->load->controller($setting['values_options']['custom_handle'] . '/getValues', array('controller' => $setting['values_route'], 'filter_data' => $filter_data, 'setting' => $setting, 'value' => $value['value']));
                        if (!empty($results)) {
                            $value['title']  =  (isset($result[$setting['values_options']['title_row']]) ? $result[$setting['values_options']['title_row']] : 'null');
                        }
                        unset($value);
                    }
                } else {
                    if (!empty($setting['values_route']) && !empty($setting['values_options']['title_row']) && !empty($setting['values_options']['value_row'])) {

                        $results =  $this->load->controller($setting['values_options']['custom_handle'] . '/getValues', array('controller' => $setting['values_route'], 'filter_data' => $filter_data, 'setting' => $setting));
                        if (!empty($results)) {
                            foreach ($results as $key => $result) {
                                $values[] = array(
                                    'title' => (isset($result[$setting['values_options']['title_row']]) ? $result[$setting['values_options']['title_row']] : 'null'),
                                    'value' => (isset($result[$setting['values_options']['value_row']]) ? $result[$setting['values_options']['value_row']] : $key),
                                    'group' => (isset($result['group']) ? $result['group'] : ''),
                                );
                            }
                        }
                    }
                }
                break;
        }
        if ($field) {

            return $field;
        } else {
            return $values;
        }
    }

    public function getSettingValues($setting) {
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $values = array();

        $language_id = (int)$this->config->get('config_language_id');

        if (empty($setting['values_options']['model_route'])) {
            if (!empty($setting['values_options']['values'])) {
                foreach ($setting['values_options']['values'] as $value_id => $value) {
                    $values[$value_id] = $value;
                    $values[$value_id]['title'] = $value['description'][$language_id]['title'];
                }
            }
        } else {
            $values = $this->getSettingValuesModel($setting);
        }

        return $values;
    }

    public function column_left() {

        $setting = array();
        $menu = array();
        $this->load->language('custom/setting');
        $this->load->model('custom/setting');

        if ($this->user->hasPermission('access', 'custom/setting_section')) {
            $setting[] = array(
                'name'       => $this->language->get('menu_section'),
                'href'     => $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'], true),
                'children' => array()
            );
        }

        if ($this->user->hasPermission('access', 'custom/setting')) {
            $setting[] = array(
                'name'       => $this->language->get('menu_setting'),
                'href'     => $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'], true),
                'children' => array()
            );
        }
        if ($this->user->hasPermission('access', 'custom/setting_page')) {
            $menu[] = array(
                'name'       => $this->language->get('menu_page'),
                'href'     => $this->url->link('custom/setting_page', 'user_token=' . $this->session->data['user_token'], true),
                'children' => array()
            );
        }
        if (count($setting)) {
            $menu[] = array(
                'id'       => 'menu-custom-setting',
                'icon'       => 'fa-cog',
                'name'       => $this->language->get('menu_title'),
                'href'     => '',
                'children' => $setting
            );
        }



        if (count($menu)) {
            return array(
                'id'       => 'menu-custom',
                'icon'       => 'fa-cogs',
                'name'       => $this->language->get('menu_custom'),
                'href'     => '',
                'children' => $menu
            );
        }
    }

    public function installSections($sections) {

        $languages = $this->languages();
        $status = array();
        foreach ($sections as $section) {

            if ($section_info = $this->model_custom_setting->getSectionByCode($section['section_code'])) {
                //   $this->model_custom_setting->deleteSection($section_info['section_id']);
            }
            foreach ($section['descriptions'] as $language_code => $description) {
                if (isset($languages[$language_code])) {
                    $section['section_description'][$languages[$language_code]['language_id']] = $description;
                }
                unset($section['descriptions'][$language_code]);
            }
            if ($section_info) {
                $status[$section['section_code']] = $this->model_custom_setting->editSection($section_info['section_id'], $section);
            } else {
                $status[$section['section_code']] = $this->model_custom_setting->addSection($section);
            }
        }
        return $status;
    }

    public function installSettings($settings) {

        $languages = $this->languages();
        $status = array();
        $perse_settings = array();
        foreach ($settings as $setting) {

            $perse_settings[$setting['section_code'] . '_' . $setting['setting_code']] = $setting;
        }
        foreach ($perse_settings as $id => $setting) {

            if ($setting_info = $this->model_custom_setting->getSettingByCode($setting['section_code'], $setting['setting_code'])) {
                //   $this->model_custom_setting->deleteSetting($setting_info['setting_id']);
            }
            if (!empty($setting['values_options'])) {
                $setting['values_options'] = $this->mb_unserialize($setting['values_options']);

                if (!empty($setting['values_options']['settings'])) {
                    foreach ($setting['values_options']['settings'] as $key => $children_setting) {

                        if ($children_setting_info =  $this->model_custom_setting->getSettingByCode($children_setting['setting']['section_code'], $children_setting['setting']['setting_code'])) {
                            $setting['values_options']['settings'][$key]['setting']['setting_id'] = $children_setting_info['setting_id'];
                        } else {
                            $children_setting_id = $this->model_custom_setting->addSetting($perse_settings[$children_setting['setting']['section_code'] . '_' . $children_setting['setting']['setting_code']]);
                            $setting['values_options']['settings'][$key]['setting']['setting_id'] = $children_setting_id;
                        }
                        unset($setting['values_options']['settings'][$key]['setting']['section_code']);
                        unset($setting['values_options']['settings'][$key]['setting']['setting_code']);
                    }
                }

                if (!empty($setting['values_options']['values_route']['section_code'])) {
                        if ($children_setting_info =  $this->model_custom_setting->getSettingByCode($setting['values_options']['values_route']['section_code'], $setting['values_options']['values_route']['setting_code'])) {
                            $setting['values_options']['values_route'] = $children_setting_info['setting_id'];
                        } else {
                            $children_setting_id = $this->model_custom_setting->addSetting($perse_settings[$setting['values_options']['values_route']['section_code'] . '_' . $setting['values_options']['values_route']['setting_code']]);
                            $setting['values_options']['values_route'] = $children_setting_id;
                        }
                      
                }

                $setting['values_options'] = serialize($setting['values_options']);
            }

            foreach ($setting['descriptions'] as $language_code => $description) {
                if (isset($languages[$language_code])) {
                    $setting['setting_description'][$languages[$language_code]['language_id']] = $description;
                }
                unset($setting['descriptions'][$language_code]);
            }
            foreach ($setting['pages'] as $key => $page) {
                $setting['setting_page'][] = $page;
                unset($setting['pages'][$key]);
            }
            if (!empty($setting['values_options'])) {
                $setting['values_options'] = $this->mb_unserialize($setting['values_options']);
            }

            $setting['build'] = true;

            if ($section_info =  $this->model_custom_setting->getSectionByCode($setting['section_code'])) {
                $setting['section_id'] = $section_info['section_id'];
                if ($setting_info) {
                    $status[$setting['section_code']][$setting['setting_code']] = $this->model_custom_setting->editSetting($setting_info['setting_id'], $setting);
                } else {
                    $status[$setting['section_code']][$setting['setting_code']] = $this->model_custom_setting->addSetting($setting);
                }
            }
        }
        return $status;
    }

    public function installBuild($data = array()) {
        $this->load->model('custom/setting');
        $this->install();
        $status = array();
        if (!empty($data)) {

            if (!empty($data['sections'] && is_array($data['sections']))) {
                $status['sections'] = $this->installSections($data['sections']);
            }

            if (!empty($data['settings'] && is_array($data['settings']))) {
                $status['settings'] = $this->installSettings($data['settings']);
            }
        }

        return $status;
    }

    public function tables() {
        $tables = array(
            'custom_setting_section',
            'custom_setting_section_description',
            'custom_setting',
            'custom_setting_description',
            'custom_setting_to_page',
            'custom_setting_value',
        );
        return $tables;
    }

    public function install() {

        $this->modificator(true);
        $this->addPermission(true);

        $this->load->model('custom/setting');
        $this->model_custom_setting->createTables();
    }

    public function uninstall() {
        $this->modificator(false);
        $this->dropTables();
    }

    protected function addPermission($install = true) {
        $this->load->model('user/user_group');
        $pages = array('custom_setting', 'custom_setting_section', 'custom_setting_page');
        $types = array('access', 'modify');

        foreach ($pages as $page) {
            foreach ($types as $type) {
                $this->model_user_user_group->addPermission($this->user->getGroupId(), $type, $page);
            }
        }
    }

    protected function modificator($install = true) {
        $modificator = $this->custom_setting['modification'];

        if ($install) {
            if (file_exists(DIR_SYSTEM . $modificator . '_disabled')) {
                rename(DIR_SYSTEM . $modificator . '_disabled', DIR_SYSTEM . $modificator);
            }
        } else {
            if (file_exists(DIR_SYSTEM . $modificator)) {
                rename(DIR_SYSTEM . $modificator, DIR_SYSTEM . $modificator . '_disabled');
            }
        }
    }

    protected function dropTables() {
        $this->load->model('custom/setting');

        $tables = $this->tables();

        $this->model_custom_setting->drop($tables);
    }
}
