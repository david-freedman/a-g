<?php

// Свой контроллер

class ControllerCustomDevSettingHandle extends Model {
    
    public function Field($data = array()) {

        $name = 'custom_setting' . $data['setting']['name'];

        switch ($data['controller']) {
            case 'demo_field':

                $output =  '<input type="text" name="' . $name . '" value="' . $data['value'] . '"/>'; // output
                $output = $output . '<pre>' . print_r($data, 1) . '</pre>'; // delete!

                break;
            case 'demo_field2':
                break;
        }

        return $output;
    }

    public function getValues($data = array()) {

        $output = array();

        switch ($data['controller']) {

            case 'demo_values': 

                $this->load->model('localisation/order_status');

                if (!empty($data['value'])) {

                    // получаем имя, которое задано в поле, если в таблице будет изменено имя, 
                    // то в настройках оно будет отображаться корректно
                    // $data['value'] = Значение которое установлено 

                    if ($result = $this->model_localisation_order_status->getOrderStatus($data['value'])) {
                        $output = array(
                            'title' => $this->clear($result['name']),
                        );
                    }
                } else {

                    // Список всех значений
                    // $data['filter_data'] = фильтр, например, можно задать limit для autocomplete
                    // а для select выводить все
                    // значение filter_name для autocomplete передается автоматически $data['filter_data']['filter_name]

                    if ($results = $this->model_localisation_order_status->getOrderStatuses($data['filter_data'])) {

                        foreach ($results as $key => $result) {

                            $output[$key] = array(
                                'title' =>  $this->clear($result['name']), // заголовок
                                'value' => $result['order_status_id'], // значение
                                'group' => '' // группировка значений
                            );
                        }
                    }
                }

                break;

            case 'demo_values2':

                break;
        }
        return $output; // возвращаем полученные результаты
    }

    // return controller, setting, value
    public function validate($data = array()) {
        // Значение
        $value = $data['value']; // print_r($value);

        switch ($data['controller']) {

            case 'demo_validate':

                if (!empty($value) && (int)$value < 15) {
                    return 'demo error';
                }

                break;

            case 'demo_validate2':

                break;
        }
    }

    public function clear($value = '') {
        return strip_tags(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
    }
}
