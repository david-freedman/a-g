<?php

class ControllerExtensionModuleBanerTop extends Controller {

    public function index($setting) {
        if ($setting['status']) {
            $data = $this->load->language('extension/module/baner_top');

            $data['name'] = $setting['name'];
            $data['title'] = $setting['title'][$this->config->get('config_language_id')];

            if (isset($data['title'])) {
                $data['heading_title'] = $data['title'];
            }
            return $this->load->view('extension/module/baner_top', $data);
        }
    }

}
