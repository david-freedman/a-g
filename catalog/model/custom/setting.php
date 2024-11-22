<?php
class ModelCustomSetting extends Model {

    public function getValue($data = array()) {
        if ($data['section'] && $data['setting'] && $data['page']) {
            if ($setting_id = $this->getSettingIdByCode($data['section'], $data['setting'])) {

                if (empty($data['id'])) {
                    $data['id'] = 0;
                }

                $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "custom_setting_value WHERE setting_id = '" . (int)$setting_id . "' AND page = '" . $data['page'] . "' AND id = '" .  $data['id'] . "'");

                $data = $query->row;

                if (!empty($data['value'])) {
                    return $this->mb_unserialize($data['value']);
                }
            }
        }
    }

    public function getSettingIdByCode($section_code, $setting_code) {
        $query = $this->db->query("SELECT setting_id FROM " . DB_PREFIX . "custom_setting WHERE section_code = '" . $section_code . "' AND setting_code = '" . $setting_code . "' ");

        if ($query->row) {
            return $query->row['setting_id'];
        }
    }

    public function mb_unserialize($string) {
        $string = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($matches) {
                if (isset($matches[2]))
                    return 's:' . strlen($matches[2]) . ':"' . $matches[2] . '";';
            },
            $string
        );
        return unserialize($string);
    }
}
