<?php

class ControllerExtensionModuleBannerOneHome extends Controller {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function index() {

        if ($this->config->get($this->prefix . 'banner_one_home_status')) {
            $data = $this->load->language('extension/module/banner_one_home');
            $text_baner_one = $this->load->controller('custom/setting/getValue', array(
                'section' => 'baner_one_home', // Уникальный индификатор секции
                'setting' => 'text_baner_one', // Уникальный индификатор поля
                'page' => 'module_banner_one_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['text_baner_one'] = html_entity_decode($text_baner_one[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
    
            $image_baner_one = $this->load->controller('custom/setting/getValue', array(
                'section' => 'baner_one_home', // Уникальный индификатор секции
                'setting' => 'image_baner_one', // Уникальный индификатор поля
                'page' => 'module_banner_one_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['image_baner_one'] = $image_baner_one;




            if (isset($data['title'])) {
                $data['heading_title'] = $data['title'];
            }
            return $this->load->view('extension/module/banner_one_home', $data);
        }
    }

}
