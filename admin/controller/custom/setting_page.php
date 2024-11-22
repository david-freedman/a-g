<?php
class ControllerCustomSettingPage extends Model {
    private $error = array();

    public function index() {
        $this->load->language('custom/setting_page');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->load->model('custom/setting');

            if (!$this->model_custom_setting->checkTables($this->load->controller('custom/setting/tables'))) {
                $this->load->controller('custom/setting/install');
            }


            $options = array();
            foreach ($this->request->post as $key => $option) {
                $options['config_custom_setting_' . $key] = $option;
            }

            $this->model_setting_setting->editSetting('config_custom_setting', $options);
            $this->model_custom_setting->setValues($this->request->post['custom_setting'], 'custom_setting');

            $this->session->data['success'] = $this->language->get('text_success');

            if (isset($this->request->get['redirect']) && $this->request->get['redirect']) {
                $this->response->redirect($this->url->link('custom/setting_page', 'user_token=' . $this->session->data['user_token'], true));
            } else {
                $this->response->redirect($this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'], true));
            }
        }

        $url = '';

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['license_key'])) {
            $data['error_license_key'] = $this->error['license_key'];
        } else {
            $data['error_license_key'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('custom/setting_page', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $setting_info = $this->model_setting_setting->getSetting('config_custom_setting');

        foreach ($setting_info as $key => $value) {
            $data[str_replace('config_custom_setting' . '_', '', $key)] = $value;
        }

        if (isset($this->request->post['developer_status'])) {
            $data['developer_status'] = $this->request->post['developer_status'];
        } elseif (isset($data['developer_status'])) {
            $data['developer_status'] = $data['developer_status'];
        } else {
            $data['developer_status'] = 0;
        }

        if (isset($this->request->post['tab_limit'])) {
            $data['tab_limit'] = $this->request->post['tab_limit'];
        } elseif (isset($data['tab_limit'])) {
            $data['tab_limit'] = $data['tab_limit'];
        } else {
            $data['tab_limit'] = 1;
        }

        $data['action'] = $this->url->link('custom/setting_page', 'user_token=' . $this->session->data['user_token']  . $url, true);
        $data['cancel'] = $this->url->link('custom/setting', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['custom_setting'] = $this->load->controller('custom_setting/handle', array(
            'page' => 'custom_setting',
            'id' => 0,
        ));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('custom/setting/page_form', $data));
    }


    protected function validate() {

        if (!$this->user->hasPermission('modify', 'custom/setting_page')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        // if ($this->request->post['license_key']) {
        //     $this->error['license_key'] = $this->language->get('error_license_key');
        // }

        if ($custom_setting = $this->load->controller('custom/setting/handleValidate')) {
            $this->error['custom_setting'] = $custom_setting;
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }
}
