<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class ControllerCustomSettingSection extends Model {
    private $error = array();

    public function index() {
        $this->load->language('custom/setting_section');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (!$this->model_custom_setting->checkTables($this->load->controller('custom/setting/tables'))) {
            $this->load->controller('custom/setting/install');
        }

        $this->getList();
    }

    public function add() {
        $this->load->language('custom/setting_section');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $section_id = $this->model_custom_setting->addSection($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_title'])) {
                $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
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
                $this->response->redirect($this->url->link('custom/setting_section/edit', 'user_token=' . $this->session->data['user_token'] . '&section_id=' . $section_id . $url, true));
            } else {
                $this->response->redirect($this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true));
            }
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('custom/setting_section');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_custom_setting->editSection($this->request->get['section_id'], $this->request->post);

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

            if (isset($this->request->get['redirect']) && $this->request->get['redirect']) {
                $this->response->redirect($this->url->link('custom/setting_section/edit', 'user_token=' . $this->session->data['user_token'] . '&section_id=' . $this->request->get['section_id'] . $url, true));
            } else {
                $this->response->redirect($this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true));
            }
        }

        $this->getForm();
    }
    public function disintegrated() {
        $this->load->language('custom/setting_section');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['section_id'])) {
            $selected = array($this->request->get['section_id']);
        }

        if (isset($selected) && $this->validateDisintegrated()) {
            foreach ($selected as $section_id) {
                $this->model_custom_setting->disintegratedSection($section_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_title'])) {
                $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
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

            $this->response->redirect($this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }
    public function integrated() {
        $this->load->language('custom/setting_section');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['section_id'])) {
            $selected = array($this->request->get['section_id']);
        }

        if (isset($selected) && $this->validateintegrated()) {
            foreach ($selected as $section_id) {
                $this->model_custom_setting->integratedSection($section_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_title'])) {
                $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
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

            $this->response->redirect($this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function delete() {
        $this->load->language('custom/setting_section');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/setting');

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['section_id'])) {
            $selected = array($this->request->get['section_id']);
        }

        if (isset($selected) && $this->validateDelete()) {
            foreach ($selected as $section_id) {
                $this->model_custom_setting->deleteSection($section_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_title'])) {
                $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
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

            $this->response->redirect($this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['section_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }

        if (isset($this->error['code'])) {
            $data['error_code'] = $this->error['code'];
        } else {
            $data['error_code'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
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

        $this->document->addStyle('view/javascript/custom_setting/iconpicker/css/fontawesome-iconpicker.css');
        $this->document->addScript('view/javascript/custom_setting/iconpicker/js/fontawesome-iconpicker.js');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['section_id'])) {
            $data['action'] = $this->url->link('custom/setting_section/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('custom/setting_section/edit', 'user_token=' . $this->session->data['user_token'] . '&section_id=' . $this->request->get['section_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['section_id'])) {
            $section_info = $this->model_custom_setting->getSection($this->request->get['section_id']);
        }

        $data['add'] = !isset($this->request->get['section_id']);

        $this->load->model('localisation/language');

        $data['developer_status'] = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting_section');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (!empty($section_info)) {
            $data['integrated'] = $section_info['integrated'];
        } else {
            $data['integrated'] = 0;
        }

        if (isset($this->request->post['section_code'])) {
            $data['section_code'] = $this->request->post['section_code'];
        } elseif (!empty($section_info)) {
            $data['section_code'] = $section_info['section_code'];
        } else {
            $data['section_code'] = '';
        }

        if (isset($this->request->post['section_description'])) {
            $data['section_description'] = $this->request->post['section_description'];
        } elseif (!empty($section_info)) {
            $data['section_description'] = $this->model_custom_setting->getSectionDescriptions($this->request->get['section_id']);
        } else {
            $data['section_description'] = array();
        }

        if (isset($this->request->post['icon'])) {
            $data['icon'] = $this->request->post['icon'];
        } elseif (!empty($section_info)) {
            $data['icon'] = $section_info['icon'];
        } else {
            $data['icon'] = 'fa fa-code';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($section_info)) {
            $data['sort_order'] = $section_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('custom/setting/section_form', $data));
    }

    protected function getList() {

        if (isset($this->request->get['filter_title'])) {
            $filter_title = $this->request->get['filter_title'];
        } else {
            $filter_title = '';
        }
        if (isset($this->request->get['filter_integrated'])) {
            $filter_integrated = $this->request->get['filter_integrated'];
        } else {
            $filter_integrated = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'cstd.title';
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
            'href' => $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['user_token'] = $this->session->data['user_token'];
        $data['developer_status'] = $this->config->get('config_custom_setting_developer_status') && $this->user->hasPermission('modify', 'custom/setting_section');
        $data['disintegrated'] = $this->url->link('custom/setting_section/disintegrated', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['integrated'] = $this->url->link('custom/setting_section/integrated', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['add'] = $this->url->link('custom/setting_section/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('custom/setting_section/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['sections'] = array();

        $filter_data = array(
            'filter_title'      => $filter_title,
            'filter_integrated'      => $filter_integrated,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $section_total = $this->model_custom_setting->getTotalSections();

        $results = $this->model_custom_setting->getSections($filter_data);

        foreach ($results as $result) {
            $data['sections'][] = array(
                'section_id' => $result['section_id'],
                'title'               => $result['title'],
                'integrated'               => $result['integrated'],
                'section_code'               => $result['section_code'],
                'sort_order'         => $result['sort_order'],
                'edit'               => $this->url->link('custom/setting_section/edit', 'user_token=' . $this->session->data['user_token'] . '&section_id=' . $result['section_id'] . $url, true),
                'delete'            => $this->url->link('custom/setting_section/delete', 'user_token=' . $this->session->data['user_token'] . '&section_id=' . $result['section_id'] . $url, true),
                'integrate'            => $this->url->link('custom/setting_section/integrated', 'user_token=' . $this->session->data['user_token'] . '&section_id=' . $result['section_id'] . $url, true),
                'disintegrate'            => $this->url->link('custom/setting_section/disintegrated', 'user_token=' . $this->session->data['user_token'] . '&section_id=' . $result['section_id'] . $url, true),
            );
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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['filter_title'] = $filter_title;
        $data['filter_integrated'] = $filter_integrated;
        $data['sort_title'] = $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . '&sort=cstd.title' . $url, true);
        $data['sort_section_code'] = $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . '&sort=cst.section_code' . $url, true);
        $data['sort_sort_order'] = $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . '&sort=cst.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
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
        $pagination->total = $section_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('custom/setting_section', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($section_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($section_total - $this->config->get('config_limit_admin'))) ? $section_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $section_total, ceil($section_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('custom/setting/section_list', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'custom/setting_section')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('custom/setting');

        if (!preg_match("/^[a-za-z0-9_]+$/", $this->request->post['section_code'])) {
            $this->error['code'] = $this->language->get('error_latin');
        }

        if ($this->model_custom_setting->getTotalSectionByCode((isset($this->request->get['section_id']) ? $this->request->get['section_id'] : ''), $this->request->post['section_code'])) {
            $this->error['code'] = $this->language->get('error_code_repeat');
        }

        if (empty($this->request->post['section_code'])) {
            $this->error['code'] = $this->language->get('error_text');
        }
        foreach ($this->request->post['section_description'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
                $this->error['title'][$language_id] = $this->language->get('error_name');
            }
        }
        if (empty($this->error['warning']) && !empty($this->error)) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }

    protected function validateDisintegrated() {
        if (!$this->user->hasPermission('modify', 'custom/setting_section')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->config->get('config_custom_setting_developer_status')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
    protected function validateintegrated() {
        if (!$this->user->hasPermission('modify', 'custom/setting_section')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->config->get('config_custom_setting_developer_status')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'custom/setting_section')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('custom/setting');

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } elseif (isset($this->request->get['section_id'])) {
            $selected = array($this->request->get['section_id']);
        }

        foreach ($selected as $section_id) {
            $setting_total = $this->model_custom_setting->getTotalSettingBySectionId($section_id);

            if ($setting_total) {
                $this->error['warning'] = sprintf($this->language->get('error_setting'), $setting_total);
            }
        }
        foreach ($selected as $section_id) {
            $section_info = $this->model_custom_setting->getSection($section_id);

            if ($section_info['integrated']) {
                $this->error['warning'] = $this->language->get('error_integrated');
            }
        }
        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_title'])) {
            $this->load->model('custom/setting');


            if (isset($this->request->get['filter_title'])) {
                $filter_title = $this->request->get['filter_title'];
            } else {
                $filter_title = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            $filter_data = array(
                'filter_title'  => $filter_title,
                'start'        => 0,
                'limit'        => $limit
            );

            $results = $this->model_custom_setting->getSections($filter_data);

            foreach ($results as $result) {

                $json[] = array(
                    'section_id' => $result['section_id'],
                    'title'       => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8')),
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
