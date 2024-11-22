<?php

class ControllerExtensionModuleServicesSectionHome extends Controller {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function index() {
        if ($this->config->get($this->prefix . 'services_section_home_status')) {
            $data = $this->load->language('extension/module/services_section_home');

            $title_servise_home = $this->load->controller('custom/setting/getValue', array(
                'section' => 'servise_home', // Уникальный индификатор секции
                'setting' => 'title_servise_home', // Уникальный индификатор поля
                'page' => 'module_services_section_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
              $data['title_servise_home'] = isset($title_servise_home[$this->config->get('config_language_id')]) ? 
            html_entity_decode($title_servise_home[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            $text_servise_home = $this->load->controller('custom/setting/getValue', array(
                'section' => 'servise_home', // Уникальный индификатор секции
                'setting' => 'text_servise_home', // Уникальный индификатор поля
                'page' => 'module_services_section_home' // Код формы в админ-панеле
            ));
            
            
            // Инициализируем полученные данные
            $data['text_servise_home'] = isset($text_servise_home[$this->config->get('config_language_id')]) ? 
            html_entity_decode($text_servise_home[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            $link_servise_home = $this->load->controller('custom/setting/getValue', array(
                'section' => 'servise_home', // Уникальный индификатор секции
                'setting' => 'link_servise_home', // Уникальный индификатор поля
                'page' => 'module_services_section_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['link_servise_home'] = $link_servise_home;

            $name_servise_btn = $this->load->controller('custom/setting/getValue', array(
                'section' => 'servise_home', // Уникальный индификатор секции
                'setting' => 'name_servise_btn', // Уникальный индификатор поля
                'page' => 'module_services_section_home' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['name_servise_btn'] = isset($name_servise_btn[$this->config->get('config_language_id')]) ? 
            html_entity_decode($name_servise_btn[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            $item_servises_home = $this->load->controller('custom/setting/getValue', array(
                'section' => 'servise_home',
                'setting' => 'item_servises_home',
                'page' => 'module_services_section_home'
            ));
            
            // Инициализация массива данных
            $data['item_servises_home'] = array(); // Инициализация массива для шаблона

            // Получаем текущий идентификатор языка
            $language_id = $this->config->get('config_language_id');

            // Проверяем, что $item_servises содержит данные
            if (!empty($item_servises_home)) {
                foreach ($item_servises_home as $item_servise) {
                    // Проверяем наличие данных для текущего языка
                    if (isset($item_servise['titile_servise_posluga'][$language_id], $item_servise['link_serv_home_item'][$language_id])) {
                        $data['item_servises_home'][] = array(
                            'titile_servise_posluga' => html_entity_decode($item_servise['titile_servise_posluga'][$language_id], ENT_QUOTES, 'UTF-8'),
                            'link_serv_home_item' => html_entity_decode($item_servise['link_serv_home_item'], ENT_QUOTES, 'UTF-8'),
                            'text_servises_home'     => html_entity_decode($item_servise['text_servises_home'][$language_id], ENT_QUOTES, 'UTF-8'),
                            'img_servise_posluga'    => html_entity_decode($item_servise['img_servise_posluga'], ENT_QUOTES, 'UTF-8'),
                        );
                    }
                }
            } else {
                error_log("No valid data in \$item_servises");
            }


            if (isset($data['title'])) {
                $data['heading_title'] = $data['title'];
            }
            return $this->load->view('extension/module/services_section_home', $data);
        }
    }

}
