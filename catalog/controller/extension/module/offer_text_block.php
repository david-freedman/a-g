<?php

class ControllerExtensionModuleOfferTextBlock extends Controller {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function index() {
        if ($this->config->get($this->prefix . 'offer_text_block_status')) {
            // Загружаем язык
            $data = $this->load->language('extension/module/offer_text_block');

            // Получаем заголовок предложения из настроек
            $title_offer_text = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный идентификатор секции
                'setting' => 'title_offer_text', // Уникальный идентификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));

            // Проверяем и задаем значение переменной с учетом текущего языка
            if (!empty($title_offer_text) && isset($title_offer_text[$this->config->get('config_language_id')])) {
                $data['title_offer_text'] = html_entity_decode($title_offer_text[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

            } else {
                $data['title_offer_text'] = ''; // Если данных нет, можно задать пустую строку
            }
            $title_offer_text2 = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный идентификатор секции
                'setting' => 'title_offer_text2', // Уникальный идентификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));

            // Проверяем и задаем значение переменной с учетом текущего языка
            if (!empty($title_offer_text2) && isset($title_offer_text2[$this->config->get('config_language_id')])) {
                $data['title_offer_text2'] = html_entity_decode($title_offer_text2[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
            } else {
                $data['title_offer_text2'] = ''; // Если данных нет, можно задать пустую строку
            }

            $text_offer_home = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный идентификатор секции
                'setting' => 'text_offer_home', // Уникальный идентификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));

            $data['text_offer_home'] = html_entity_decode($text_offer_home[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

            $text_offer_home2 = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный идентификатор секции
                'setting' => 'text_offer_home2', // Уникальный идентификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));

            $data['text_offer_home2'] = html_entity_decode($text_offer_home2[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
            
            $offer_item_prop = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home',
                'setting' => 'offer_item_prop',
                'page' => 'module_offer_text_block'
            ));
            
            // Инициализация массива данных
            $data['offer_item_prop'] = array();
            
            // Получаем текущий идентификатор языка
            $language_id = $this->config->get('config_language_id');
            
            // Проверяем, что $offer_item_prop содержит данные и что они в правильном формате
            if (!empty($offer_item_prop)) {
                // Проходим по каждому элементу массива $offer_item_prop
                foreach ($offer_item_prop as $offer_item) {
                    // Проверяем, что данные для текущего языка существуют
                    if (isset($offer_item['offer_text_prop'][$language_id]) && isset($offer_item['offer_link_prop'][$language_id])) {
                        $data['offer_item_prop'][] = array(
                            'offer_text_prop' => html_entity_decode($offer_item['offer_text_prop'][$language_id], ENT_QUOTES, 'UTF-8'),
                            'offer_link_prop' => html_entity_decode($offer_item['offer_link_prop'][$language_id], ENT_QUOTES, 'UTF-8'),
                            'offer_img_prop' => html_entity_decode($offer_item['offer_img_prop'], ENT_QUOTES, 'UTF-8'),
                        );
                    }
                }
            } else {
                error_log("No valid data in offer_item_prop");
            }
            
            $item_galereya_1 = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный индификатор секции
                'setting' => 'item_galereya_1', // Уникальный индификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['item_galereya_1'] = $item_galereya_1;

            $item_galereya_2 = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный индификатор секции
                'setting' => 'item_galereya_2', // Уникальный индификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['item_galereya_2'] = $item_galereya_2;

            $item_galereya_3 = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный индификатор секции
                'setting' => 'item_galereya_3', // Уникальный индификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['item_galereya_3'] = $item_galereya_3;

            $item_galereya_4 = $this->load->controller('custom/setting/getValue', array(
                'section' => 'offer_text_bock_home', // Уникальный индификатор секции
                'setting' => 'item_galereya_4', // Уникальный индификатор поля
                'page' => 'module_offer_text_block' // Код формы в админ-панеле
            ));
            
            // Инициализируем полученные данные
            $data['item_galereya_4'] = $item_galereya_4;
            
            
           

            // Устанавливаем заголовок
            if (isset($data['title'])) {
                $data['heading_title'] = $data['title'];
            }

            // Возвращаем представление с переданными данными
            return $this->load->view('extension/module/offer_text_block', $data);
        }
    }
}
