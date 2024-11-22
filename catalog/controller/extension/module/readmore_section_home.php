<?php

class ControllerExtensionModuleReadmoreSectionHome extends Controller {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function index() {
        if ($this->config->get($this->prefix . 'readmore_section_home_status')) {
            $data = $this->load->language('extension/module/readmore_section_home');

            $h_seo_text = $this->load->controller('custom/setting/getValue', array(
                'section' => 'seo_text_home', // Уникальный индификатор секции
                'setting' => 'h_seo_text', // Уникальный индификатор поля
                'page' => 'module_readmore_section_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            
            $data['h_seo_text'] = isset($h_seo_text[$this->config->get('config_language_id')]) ? 
            html_entity_decode($h_seo_text[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            $prev_seo_text = $this->load->controller('custom/setting/getValue', array(
                'section' => 'seo_text_home', // Уникальный индификатор секции
                'setting' => 'prev_seo_text', // Уникальный индификатор поля
                'page' => 'module_readmore_section_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            
            $data['prev_seo_text'] = isset($prev_seo_text[$this->config->get('config_language_id')]) ? 
                html_entity_decode($prev_seo_text[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            $text_seo_home = $this->load->controller('custom/setting/getValue', array(
                'section' => 'seo_text_home', // Уникальный индификатор секции
                'setting' => 'text_seo_home', // Уникальный индификатор поля
                'page' => 'module_readmore_section_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            
            $data['text_seo_home'] = isset($text_seo_home[$this->config->get('config_language_id')]) ? 
                html_entity_decode($text_seo_home[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            if (isset($data['title'])) {
                $data['heading_title'] = $data['title'];
            }
            return $this->load->view('extension/module/readmore_section_home', $data);
        }
    }

}
