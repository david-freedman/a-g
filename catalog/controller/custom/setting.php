<?php
class ControllerCustomSetting extends Controller {

    public function getValue($data = array()) {
        $this->load->model('custom/setting');
        
        $value = $this->model_custom_setting->getValue($data);

        return $value;
    }

    public function getValues($data = array()) {
        
    }
}
