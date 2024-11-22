<?php

class ControllerExtensionModuleBannerTwoHome extends Controller {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function index() {
        if ($this->config->get($this->prefix . 'banner_two_home_status')) {
            $data = $this->load->language('extension/module/banner_two_home');

            $text_baner_two = $this->load->controller('custom/setting/getValue', array(
                'section' => 'baner_two_home', // Уникальный индификатор секции
                'setting' => 'text_baner_two', // Уникальный индификатор поля
                'page' => 'module_banner_two_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['text_baner_two'] = html_entity_decode($text_baner_two[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
    
            $image_baner_two = $this->load->controller('custom/setting/getValue', array(
                'section' => 'baner_two_home', // Уникальный индификатор секции
                'setting' => 'image_baner_two', // Уникальный индификатор поля
                'page' => 'module_banner_two_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['image_baner_two'] = $image_baner_two;


            if (isset($data['title'])) {
                $data['heading_title'] = $data['title'];
            }
            return $this->load->view('extension/module/banner_two_home', $data);
        }
    }

}
