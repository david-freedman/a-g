<?php

class ControllerExtensionModuleTopSalesHome extends Controller {

    public function index() {
        if ($this->config->get('module_top_sales_home_status')) {
            
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            $this->load->language('product/product');
            // Получение списка ID товаров из кастомного поля
            $top_prodaj = $this->load->controller('custom/setting/getValue', array(
                'section' => 'top_sales',
                'setting' => 'top_prodaj',
                'page' => 'module_top_sales_home'
            ));

            // Проверяем, что $top_prodaj инициализирован как массив
            if (!is_array($top_prodaj)) {
                $top_prodaj = [];
            }

            $data['products'] = array();

            // Обрабатываем массив товаров, если он не пуст
            if (!empty($top_prodaj)) {
                foreach ($top_prodaj as $product) {
                    // Проверка, существует ли 'value' в массиве
                    $product_id = isset($product['value']) ? $product['value'] : null;
            
                    if ($product_id) {
                        $product_info = $this->model_catalog_product->getProduct($product_id);
            
                        if ($product_info) {
                            // Основное изображение или заполнитель
                            $image = $product_info['image'] ?
                                $this->model_tool_image->resize($product_info['image'], 
                                    $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), 
                                    $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
                                ) : 
                                $this->model_tool_image->resize('placeholder.png', 
                                    $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), 
                                    $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
                                );
            
                            // Получаем дополнительные изображения
                            $additional_images = $this->model_catalog_product->getProductImages($product_id);
                            $product_images = array();
            
                            // Добавляем основное изображение в массив изображений для слайдера
                            $product_images[] = $image;
            
                            // Добавляем дополнительные изображения в массив для слайдера
                            if (!empty($additional_images)) {
                                foreach ($additional_images as $additional_image) {
                                    $product_images[] = $this->model_tool_image->resize($additional_image['image'], 
                                        $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), 
                                        $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')
                                    );
                                }
                            }
            
                            // Определяем, нужно ли показывать кнопки управления слайдером
                            $show_slider_controls = count($product_images) > 1;
            
                            // Цена, скидка и рейтинг
                            $price = $this->customer->isLogged() || !$this->config->get('config_customer_price') ? 
                                $this->currency->format($product_info['price'], $this->session->data['currency']) : false;
            
                            $special = (float)$product_info['special'] ? 
                                $this->currency->format($product_info['special'], $this->session->data['currency']) : false;
            
                            $rating = $this->config->get('config_review_status') ? $product_info['rating'] : false;

                            

                            $stock_status = '';
                            if ($product_info['quantity'] <= 0) {
                                
                            if ($product_info['stock_status_id'] == 8) {
                                    $stock_status = $this->language->get('text_special_order'); 
                            } else {
                                    $stock_status = $this->language->get('text_out_of_stock'); // Получаем строку "Нет в наличии"
                                }
                            } elseif ($this->config->get('config_stock_display')) {
                                        $stock_status = $product_info['quantity']; 
                            } else {
                                        $stock_status = $this->language->get('text_instock'); 
                            }

                            // Добавляем товар в массив данных с поддержкой слайдера
                            $data['products'][] = array(
                                'product_id'        => $product_info['product_id'],
                                'images'            => $product_images, // Все изображения для слайдера
                                'show_slider'       => $show_slider_controls, // Отображение кнопок слайдера
                                'name'              => $product_info['name'],
                                'description'       => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
                                'price'             => $price,
                                'special'           => $special,
                                'rating'            => $rating,
                                'stock'             => $stock_status,
                                'href'              => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
                            );

                        }
                    }
                }
            }
            

            // Получаем и инициализируем настройки
            $title_toppodaj = $this->load->controller('custom/setting/getValue', array(
                'section' => 'top_sales',
                'setting' => 'title_toppodaj',
                'page' => 'module_top_sales_home'
            ));

            $data['title_toppodaj'] = isset($title_toppodaj[$this->config->get('config_language_id')]) ? 
                html_entity_decode($title_toppodaj[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            $text_top_prodaj = $this->load->controller('custom/setting/getValue', array(
                'section' => 'top_sales',
                'setting' => 'text_top_prodaj',
                'page' => 'module_top_sales_home'
            ));

            $data['text_top_prodaj'] = isset($text_top_prodaj[$this->config->get('config_language_id')]) ? 
                html_entity_decode($text_top_prodaj[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            $img_top_prodaj = $this->load->controller('custom/setting/getValue', array(
                'section' => 'top_sales',
                'setting' => 'img_top_prodaj',
                'page' => 'module_top_sales_home'
            ));

            $data['img_top_prodaj'] = $img_top_prodaj;

            $link_btn_prodaj = $this->load->controller('custom/setting/getValue', array(
                'section' => 'top_sales',
                'setting' => 'link_btn_prodaj',
                'page' => 'module_top_sales_home'
            ));

            $data['link_btn_prodaj'] = $link_btn_prodaj;

            $name_btn_prodaj = $this->load->controller('custom/setting/getValue', array(
                'section' => 'top_sales',
                'setting' => 'name_btn_prodaj',
                'page' => 'module_top_sales_home'
            ));

            $data['name_btn_prodaj'] = isset($name_btn_prodaj[$this->config->get('config_language_id')]) ? 
                html_entity_decode($name_btn_prodaj[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8') : '';

            // Загрузка шаблона, если есть товары
            if (!empty($data['products'])) {
                return $this->load->view('extension/module/top_sales_home', $data);
            }
        }
    }
}
