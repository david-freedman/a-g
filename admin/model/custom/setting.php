<?php
class ModelCustomSetting extends Model {

    public function addSection($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_section SET section_code = '" . $this->db->escape($data['section_code']) . "', sort_order = '" . (int)$data['sort_order'] . "', icon = '" . $this->db->escape($data['icon']) . "'");
        $section_id = $this->db->getLastId();

        foreach ($data['section_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_section_description SET section_id = '" . (int)$section_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title'])  . "'");
        }

        return $section_id;
    }

    public function editSection($section_id, $data) {
        $section_info = $this->getSection($section_id);
        $sql = "UPDATE " . DB_PREFIX . "custom_setting_section SET sort_order = '" . (int)$data['sort_order'] . "', icon = '" . $this->db->escape($data['icon']) . "'";

        if (!$section_info['integrated']) {
            $sql .= ", section_code = '" . $this->db->escape($data['section_code']) . "'";
            $this->db->query("UPDATE " . DB_PREFIX . "custom_setting SET section_code = '" . $this->db->escape($data['section_code']) . "' WHERE section_id = '" . (int)$section_id . "'");
        }

        $sql .= " WHERE section_id = '" . (int)$section_id . "'";

        $this->db->query($sql);

        $this->db->query("DELETE FROM " . DB_PREFIX . "custom_setting_section_description WHERE section_id = '" . (int)$section_id . "'");

        foreach ($data['section_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_section_description SET section_id = '" . (int)$section_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title'])  . "'");
        }

        return 'edited';
    }

    public function getSection($section_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "custom_setting_section cst LEFT JOIN " . DB_PREFIX . "custom_setting_section_description cstd ON (cst.section_id = cstd.section_id) WHERE cstd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cst.section_id = '" . (int)$section_id . "'");

        if ($query->row) {
            $data = $query->row;

            //   $data['description'] = $this->getSectionDescriptions($section_id);
            return $data;
        }
    }
    public function getSectionByCode($section_code) {
        $query = $this->db->query("SELECT section_id FROM " . DB_PREFIX . "custom_setting_section WHERE section_code = '" . $section_code . "'");
        if ($query->row) {
            return $this->getSection($query->row['section_id']);
        }
    }


    public function getDownloadSection($section_id) {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_setting_section WHERE section_id = '" . (int)$section_id . "'");

        if ($query->row) {
            $data = $query->row;

            $data['descriptions'] = $this->getSectionDescriptions($section_id);

            return $data;
        }
    }

    public function getDownloadSettings($data = array()) {

        $sql = "SELECT  * FROM " . DB_PREFIX . "custom_setting";

        if (isset($data['filter_ids']) && !empty($data['filter_ids']) && is_array($data['filter_ids'])) {

            $sql .= ' WHERE setting_id IN ("' . implode('", "', $data['filter_ids']) . '")';
        }

        $query = $this->db->query($sql);
        $output = array();

        if ($query->rows) {
            foreach ($query->rows as $setting_data) {

                $output[$setting_data['setting_id']] = $setting_data;

                $output[$setting_data['setting_id']]['pages'] = $this->getSettingPages($setting_data['setting_id']);
                $output[$setting_data['setting_id']]['descriptions'] = $this->getSettingDescriptions($setting_data['setting_id']);
            }
        }

        return $output;
    }

    public function getSectionDescriptions($section_id) {
        $section_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_setting_section_description WHERE section_id = '" . $section_id . "'");

        foreach ($query->rows as $result) {
            $section_description_data[$result['language_id']] = array(
                'title'  => $result['title']
            );
        }

        return $section_description_data;
    }

    public function deleteSection($section_id) {
        $this->db->query("DELETE cst, cstd FROM " . DB_PREFIX . "custom_setting_section cst LEFT JOIN " . DB_PREFIX . "custom_setting_section_description cstd ON (cst.section_id = cstd.section_id) WHERE cst.section_id = '" . (int)$section_id . "' AND cst.integrated = '0'");
    }

    public function getSections($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "custom_setting_section cst LEFT JOIN " . DB_PREFIX . "custom_setting_section_description cstd ON (cst.section_id = cstd.section_id) WHERE cstd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_title'])) {
            $sql .= " AND cstd.title LIKE '" . $this->db->escape($data['filter_title']) . "%'";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND cstd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_integrated']) && $data['filter_integrated'] !== '') {
            $sql .= " AND cst.integrated = " . (int)$data['filter_integrated'];
        }

        $sort_data = array(
            'cstd.title',
            'cst.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY cstd.title";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        if (!empty($data['get_settings'])) {
            foreach ($query->rows as &$section) {

                $section['settings'] =  $this->getSettingsPage($section['section_id'], $data['get_settings']);
            
                unset($section);
            }
        
        }
        return $query->rows;
    }

    public function addSetting($data) {
        if ($section = $this->getSection($data['section_id'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting SET section_code = '" . $this->db->escape($section['section_code']) . "', section_id = '" . (int)$data['section_id'] . "', integrated = '" . (isset($data['integrated']) ? (int)$data['integrated'] : 0) . "',  setting_code = '" . $this->db->escape($data['setting_code']) . "', required = '" . (int)$data['required'] . "',  regex = '" . (isset($data['regex']) ? $this->db->escape($data['regex']) : '') . "', show_developer = '" . (int)$data['show_developer'] . "', values_route = '" . (isset($data['values_route']) ? $this->db->escape($data['values_route'])  : '') . "', default_value = '" . $this->db->escape($data['default_value']) . "', type = '" . $data['type'] . "', values_options = '" . (isset($data['values_options']) ? $this->db->escape(serialize($data['values_options'])) : '')  . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

            $setting_id = $this->db->getLastId();

            foreach ($data['setting_description'] as $language_id => $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_description SET setting_id = '" . (int)$setting_id . "',  language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title'])  . "', help = '" . $this->db->escape($value['help'])  . "',  popover = '" . $this->db->escape($value['popover'])  . "', error_text = '" . $this->db->escape($value['error_text'])  . "'");
            }
            if (isset($data['setting_page'])) {
                foreach ($data['setting_page'] as $page) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_to_page SET setting_id = '" . (int)$setting_id . "', page = '" . $page . "'");
                }
            }
            $this->cache->delete('custom_setting_pages_' . $setting_id);
            $this->cache->delete('custom_setting_' . $setting_id);
            $this->cache->delete('custom_settings');
            return $setting_id;
        } else {
            return false;
        }
    }

    public function editSettingIntegrated($setting_info, $section, $data) {

        $type = $setting_info['type'];
        $setting_id = $setting_info['setting_id'];
        $values_options = $setting_info['values_options'];
        $replaces = array('inline', 'placeholder', 'block_width', 'shown_tab', 'placeholder_size', 'text', 'rows', 'regex', 'vertical_tab');

        $developer_status = $this->config->get('config_custom_setting_developer_status');

        if (isset($data['values_options'])) {
            if (in_array($type, array('block'))) {
                if (isset($data['values_options']['settings'])) {
                    foreach ($data['values_options']['settings'] as $row_id => $v) {
                        if (isset($values_options['settings'][$row_id])) {
                            if ($developer_status) {
                                $values_options['settings'][$row_id]['setting'] =  $v['setting'];
                            }
                            $values_options['settings'][$row_id]['description'] =  $v['description'];
                        }
                    }
                }
            }
            if (in_array($type, array('juxtapose'))) {
                $integrate_values = $data['values_options']['integrate_values'];
                if (!$integrate_values) {
                    if ($developer_status) {
                        if (isset($data['values_options']['values_route'])) {
                            $values_options['values_route'] =  $data['values_options']['values_route'];
                        }
                        if (isset($data['values_options']['settings'])) {
                            $values_options['columns'] =  $data['values_options']['settings'];
                        }
                    }
                    $values_options['columns'] =  $data['values_options']['columns'];
                } else {
                    foreach ($data['values_options']['settings'] as  $row => $s) {
                        $values_options['settings'][$row]['width'] =  $s['width'];
                    }
                    if (isset($data['values_options']['columns'])) {
                        foreach ($data['values_options']['columns'] as $row_id => $v) {
                            if (isset($values_options['columns'][$row_id])) {
                                $values_options['columns'][$row_id]['description'] =  $v['description'];
                            }
                            if (isset($values_options['columns'][$row_id]['key']) && !$integrate_values) {
                                $values_options['columns'][$row_id]['key'] =  $v['key'];
                            }
                        }
                    }
                }
            }
            if (in_array($type, array('tab'))) {
                $integrate_values = $data['values_options']['integrate_values'];
                if (!$integrate_values) {
                    if ($developer_status) {
                        if (isset($data['values_options']['values_route'])) {
                            $values_options['values_route'] =  $data['values_options']['values_route'];
                        }
                        if (isset($data['values_options']['settings'])) {
                            $values_options['settings'] =  $data['values_options']['settings'];
                        }
                    }
                    $values_options['tabs'] =  $data['values_options']['tabs'];
                } else {
                    if (isset($data['values_options']['tabs'])) {
                        foreach ($data['values_options']['tabs'] as $row_id => $v) {
                            if (isset($values_options['tabs'][$row_id])) {
                                $values_options['tabs'][$row_id]['description'] =  $v['description'];
                            }
                            if (isset($values_options['tabs'][$row_id]['key']) && !$integrate_values) {
                                $values_options['tabs'][$row_id]['key'] =  $v['key'];
                            }
                        }
                    }
                }
            }

            if (in_array($type, array('select', 'radio', 'old_radio', 'checkbox'))) {
                if (isset($data['values_options']['values'])) {
                    foreach ($data['values_options']['values'] as $row_id => $v) {
                        if (isset($values_options['values'][$row_id])) {
                            $values_options['values'][$row_id]['description'] =  $v['description'];
                        }
                    }
                }
            }

            foreach ($replaces as $replace) {
                if (isset($data['values_options'][$replace])) {
                    $values_options[$replace] =  $data['values_options'][$replace];
                }
            }
        }

        $this->db->query("UPDATE " . DB_PREFIX . "custom_setting SET section_code = '" . $this->db->escape($section['section_code']) . "', section_id = '" . (int)$data['section_id'] . "', regex = '" .  (isset($data['regex']) ? $this->db->escape($data['regex']) : '') . "',  show_developer = '" . (int)$data['show_developer'] . "', default_value = '" . $this->db->escape($data['default_value']) . "',  values_options = '" . (isset($values_options) ? $this->db->escape(serialize($values_options)) : '') . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE setting_id = '" . (int)$setting_id . "'");
    }

    public function editSetting($setting_id, $data) {
        if ($section = $this->getSection($data['section_id'])) {

            $setting_info = $this->getSetting($setting_id);

            if ($setting_info['integrated'] && empty($setting_info['build'])) {

                $this->editSettingIntegrated($setting_info, $section, $data);
            } else {

                $this->db->query("UPDATE " . DB_PREFIX . "custom_setting SET section_code = '" . $this->db->escape($section['section_code']) . "', setting_code = '" . $this->db->escape($data['setting_code']) . "', section_id = '" . (int)$data['section_id'] . "', required = '" . (int)$data['required'] . "', regex = '" .  (isset($data['regex']) ? $this->db->escape($data['regex']) : '') . "',  show_developer = '" . (int)$data['show_developer'] . "', values_route = '" . (isset($data['values_route']) ? $this->db->escape($data['values_route'])  : '') . "', default_value = '" . $this->db->escape($data['default_value']) . "', type = '" . $this->db->escape($data['type']) . "', values_options = '" . (isset($data['values_options']) ? $this->db->escape(serialize($data['values_options'])) : '') . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE setting_id = '" . (int)$setting_id . "'");

                $this->db->query("DELETE FROM " . DB_PREFIX . "custom_setting_to_page WHERE setting_id = '" . (int)$setting_id . "'");

                if (isset($data['setting_page'])) {
                    foreach ($data['setting_page'] as $page) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_to_page SET setting_id = '" . (int)$setting_id . "', page = '" . $page . "'");
                    }
                }
            }

            $this->db->query("DELETE FROM " . DB_PREFIX . "custom_setting_description WHERE setting_id = '" . (int)$setting_id . "'");

            foreach ($data['setting_description'] as $language_id => $value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_description SET setting_id = '" . (int)$setting_id . "',  language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title'])  . "', help = '" . $this->db->escape($value['help'])  . "',  popover = '" . $this->db->escape($value['popover'])  . "', error_text = '" . $this->db->escape($value['error_text'])  . "'");
            }
            $this->cache->delete('custom_settings');
            $this->cache->delete('custom_setting_' . $setting_id);
            $this->cache->delete('custom_setting_pages_' . $setting_id);

            return 'edited';
        }
    }

    public function getSetting($setting_id) {
        $cache = $this->cache->get('custom_setting_' . $setting_id);
        if (!$cache) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_setting cs LEFT JOIN " . DB_PREFIX . "custom_setting_description csd ON (cs.setting_id = csd.setting_id) WHERE cs.setting_id = '" . (int)$setting_id . "' AND csd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
            $data = $query->row;

            if (!empty($data['values_options'])) {
                $data['values_options'] = $this->mb_unserialize($data['values_options']);
            }

            $cache = $data;
            $this->cache->set('custom_setting_' . $setting_id, $cache);
        }
        return $cache;
    }

    public function getSettingType($setting_id) {

        $query = $this->db->query("SELECT type FROM " . DB_PREFIX . "custom_setting WHERE setting_id = '" . $setting_id . "'");
        if ($query->row) {
            return $query->row['type'];
        }
    }

    public function getSettingDescriptions($setting_id) {
        $setting_description_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_setting_description WHERE setting_id = '" . $setting_id . "'");
        foreach ($query->rows as $result) {
            $setting_description_data[$result['language_id']] = array(
                'title'  => $result['title'],
                'help'  => $result['help'],
                'popover'  => $result['popover'],
                'error_text'  => $result['error_text'],
            );
        }
        return $setting_description_data;
    }

    public function getSettingByCode($section_code, $setting_code, $lang = false) {
        $query = $this->db->query("SELECT setting_id FROM " . DB_PREFIX . "custom_setting WHERE section_code = '" . $section_code . "' AND setting_code = '" . $setting_code . "'");
        if ($query->row) {
            return $this->getSetting($query->row['setting_id'], $lang);
        }
    }

    public function getSettingsPage($section_id, $page) {
        $settings_data = array();
        $query = $this->db->query("SELECT cs.setting_id FROM " . DB_PREFIX . "custom_setting cs LEFT JOIN " . DB_PREFIX . "custom_setting_to_page cs2p ON (cs.setting_id = cs2p.setting_id) WHERE cs2p.page = '" . $page . "' AND cs.section_id = '" . (int)$section_id . "' AND cs.status = '1' GROUP BY cs.setting_id ORDER BY cs.sort_order,cs.setting_id");
        foreach ($query->rows as $setting) {
            $settings_data[] = $this->getSetting($setting['setting_id'],  true);
        }

        return $settings_data;
    }

    public function disintegratedSetting($setting_id) {

        $this->db->query("UPDATE " . DB_PREFIX . "custom_setting SET integrated = '0' WHERE setting_id = '" . (int)$setting_id . "'");

        // if ($setting_info = $this->getSetting($setting_id)) {
        //     if (!empty($setting_info['values_options']['values_route']) && !empty($setting_info['values_options']['integrate_values'])) {
        //         $this->disintegratedSetting($setting_info['values_options']['values_route']);
        //     }
        //     if (!empty($setting_info['values_options']['settings'])) {
        //         foreach ($setting_info['values_options']['settings'] as $setting) {
        //             $this->disintegratedSetting($setting['setting']['setting_id']);
        //         }
        //     }
        // }

        $this->cache->delete('custom_setting_' . $setting_id);
        $this->cache->delete('custom_settings');
    }

    public function integratedSetting($setting_id) {

        $this->db->query("UPDATE " . DB_PREFIX . "custom_setting SET integrated = '1' WHERE setting_id = '" . (int)$setting_id . "'");

        if ($setting_info = $this->getSetting($setting_id)) {
            if (!empty($setting_info['values_options']['values_route']) && !empty($setting_info['values_options']['integrate_values'])) {
                $this->integratedSetting($setting_info['values_options']['values_route']);
            }
            if (!empty($setting_info['values_options']['settings'])) {
                foreach ($setting_info['values_options']['settings'] as $setting) {
                    $this->integratedSetting($setting['setting']['setting_id']);
                }
            }
        }
        $this->cache->delete('custom_setting_' . $setting_id);
        $this->cache->delete('custom_settings');
    }

    public function integratedSection($section_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "custom_setting_section SET integrated = '1' WHERE section_id = '" . (int)$section_id . "'");
    }

    public function disintegratedSection($section_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "custom_setting_section SET integrated = '0' WHERE section_id = '" . (int)$section_id . "'");
    }

    public function deleteSetting($setting_id) {
        $this->db->query("DELETE cs, csd, csv FROM " . DB_PREFIX . "custom_setting cs LEFT JOIN " . DB_PREFIX . "custom_setting_description csd ON (cs.setting_id = csd.setting_id) LEFT JOIN " . DB_PREFIX . "custom_setting_value csv ON (cs.setting_id = csv.setting_id) WHERE cs.setting_id = '" . (int)$setting_id . "' AND cs.integrated = '0'");


        $this->cache->delete('custom_setting_pages_' . $setting_id);
        $this->cache->delete('custom_setting_' . $setting_id);
        $this->cache->delete('custom_settings');
    }

    public function getSettingIdByCode($section_code, $setting_code) {
        $query = $this->db->query("SELECT setting_id FROM " . DB_PREFIX . "custom_setting WHERE section_code = '" . $section_code . "' AND setting_code = '" . $setting_code . "' ");

        if ($query->row) {
            return $query->row['setting_id'];
        }
    }

    public function getSettingPages($setting_id) {
        $cache = $this->cache->get('custom_setting_pages_' . $setting_id);
        if (!$cache) {
            $data = array();

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_setting_to_page WHERE setting_id = '" . (int)$setting_id . "'");

            foreach ($query->rows as $result) {
                $data[] = $result['page'];
            }
            $cache = $data;
            $this->cache->set('custom_setting_pages_' . $setting_id, $cache);
        }
        return $cache;
    }

    public function getSettings($data = array()) {

        $sql = "SELECT *, (SELECT cstd.title FROM " . DB_PREFIX . "custom_setting_section_description cstd WHERE cstd.section_id = cs.section_id AND cstd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS section FROM " . DB_PREFIX . "custom_setting cs LEFT JOIN " . DB_PREFIX . "custom_setting_description csd ON (cs.setting_id = csd.setting_id) WHERE csd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_title'])) {
            $sql .= " AND csd.title LIKE '" . $this->db->escape($data['filter_title']) . "%'";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND csd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_type']) && $data['filter_type'] !== '') {
            $sql .= " AND cs.type = '" . $this->db->escape($data['filter_type']) . "'";
        }

        if (isset($data['filter_ids']) && !empty($data['filter_ids']) && is_array($data['filter_ids'])) {

            $sql .= ' AND cs.setting_id IN ("' . implode('", "', $data['filter_ids']) . '")';
        }

        if (isset($data['filter_types']) && !empty($data['filter_types']) && is_array($data['filter_types'])) {

            $sql .= ' AND cs.type IN ("' . implode('", "', $data['filter_types']) . '")';
        }

        if (!empty($data['filter_nottype']) && $data['filter_nottype'] !== '') {
            $sql .= " AND cs.type != '" . $data['filter_nottype'] . "'";
        }

        if (!empty($data['filter_section_id'])) {
            $sql .= " AND cs.section_id = " . (int)$data['filter_section_id'];
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND cs.status = " . (int)$data['filter_status'];
        }

        if (isset($data['filter_integrated']) && $data['filter_integrated'] !== '') {
            $sql .= " AND cs.integrated = " . (int)$data['filter_integrated'];
        }

        $sort_data = array(
            'csd.title',
            'section',
            'cs.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY section, csd.title";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalSectionByCode($section_id, $section_code) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "custom_setting_section WHERE section_code = '" . $section_code . "' AND section_id != '" . (int)$section_id . "'");

        return $query->row['total'];
    }

    public function getTotalSections() {
        $sql = "SELECT COUNT(DISTINCT cst.section_id) AS total FROM " . DB_PREFIX . "custom_setting_section cst LEFT JOIN " . DB_PREFIX . "custom_setting_section_description cstd ON (cst.section_id = cstd.section_id)";

        $sql .= " WHERE cstd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_title'])) {
            $sql .= " AND cstd.title LIKE '" . $this->db->escape($data['filter_title']) . "%'";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND cstd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_integrated']) && $data['filter_integrated'] !== '') {
            $sql .= " AND cst.integrated = " . (int)$data['filter_integrated'];
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTotalSettingBySectionId($section_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "custom_setting WHERE section_id = '" . (int)$section_id . "'");

        return $query->row['total'];
    }

    public function getTotalSettingByCode($setting_id, $setting_code, $section_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "custom_setting WHERE setting_code = '" . $setting_code . "' AND setting_id != '" . (int)$setting_id . "' AND section_id = '" . (int)$section_id . "'");

        return $query->row['total'];
    }

    public function getTotalSettings($data = array()) {
        $sql = "SELECT COUNT(DISTINCT cs.setting_id) AS total FROM " . DB_PREFIX . "custom_setting cs LEFT JOIN " . DB_PREFIX . "custom_setting_description csd ON (cs.setting_id = csd.setting_id)";

        $sql .= " WHERE csd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_title'])) {
            $sql .= " AND csd.title LIKE '" . $this->db->escape($data['filter_title']) . "%'";
        }
        if (!empty($data['filter_name'])) {
            $sql .= " AND csd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_nottype']) && $data['filter_nottype'] !== '') {
            $sql .= " AND cs.type != '" . $data['filter_nottype'] . "'";
        }
        if (!empty($data['filter_type']) && $data['filter_type'] !== '') {
            $sql .= " AND cs.type = '" . $this->db->escape($data['filter_type']) . "'";
        }

        if (!empty($data['filter_section_id'])) {
            $sql .= " AND cs.section_id = " . (int)$data['filter_section_id'];
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND cs.status = " . (int)$data['filter_status'];
        }

        if (isset($data['filter_integrated']) && $data['filter_integrated'] !== '') {
            $sql .= " AND cs.integrated = " . (int)$data['filter_integrated'];
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function setValues($values, $page, $id = 0) {
        if (!empty($page)) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "custom_setting_value WHERE page = '" . $page . "' AND id = '" . $id . "'");

            if (!empty($values)) {
                foreach ($values as $section_code => $section) {
                    foreach ($section as $setting_code => $value) {
                        if ($setting_id = $this->model_custom_setting->getSettingIdByCode($section_code, $setting_code)) {
                            $this->setValue($value, $setting_id, $page, $id);
                        }
                    }
                }
            }
        }
    }

    public function setValue($value, $setting_id, $page, $id = 0) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "custom_setting_value (value, setting_id, page, id) VALUES ('" . $this->db->escape(serialize($value)) . "', '" . (int)$setting_id . "', '" . $this->db->escape($page) . "', " .  $id . ")");
    }

    public function getValue($setting_id, $page, $id = 0) {

        $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "custom_setting_value WHERE setting_id = '" . (int)$setting_id . "' AND page = '" . $page . "' AND id = '" .  $id . "'");

        $data = $query->row;

        if (!empty($data['value'])) {
            return $this->mb_unserialize($data['value']);
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

    public function getModule($module_id) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");

        if ($query->row) {
            return $query->row;
        } else {
            return array();
        }
    }

    public function checkTables($tables) {
        $exists = true;
        foreach($tables as $table){
            if($this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $table."'")->num_rows == 0){
                $exists = false;
                break;
            }
        }
        return $exists;
    }
    public function createTables() {

        $this->cache->delete('custom_settings');

        $custom_setting_section = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "custom_setting_section'");
        if ($custom_setting_section->num_rows == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "custom_setting_section` (
                `section_id` int(11) NOT NULL AUTO_INCREMENT,
        		`section_code` varchar(32) NOT NULL,
        		`icon` varchar(32) NOT NULL,
                `integrated` tinyint(1) NOT NULL DEFAULT '0',
                `sort_order` int(3) NOT NULL DEFAULT '0',
                PRIMARY KEY (`section_id`),
                UNIQUE KEY `section_code` (`section_code`)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        $custom_setting_section_description = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "custom_setting_section_description'");
        if ($custom_setting_section_description->num_rows == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "custom_setting_section_description` (
        		`section_id` int(11) NOT NULL,
        		`language_id` int(11) NOT NULL,
        		`title` varchar(32) NOT NULL,
        		PRIMARY KEY (`section_id`,`language_id`)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        $custom_setting = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "custom_setting'");
        if ($custom_setting->num_rows == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "custom_setting` (
                `setting_id` int(11) NOT NULL AUTO_INCREMENT,
                `section_id` int(11) NOT NULL,
       		    `section_code` varchar(32) NOT NULL,
        		`setting_code` varchar(32) NOT NULL,
                `required` tinyint(1) NOT NULL DEFAULT '0',
                `regex` text NOT NULL,
                `show_developer` tinyint(1) NOT NULL DEFAULT '0',
                `values_route` varchar(256) NOT NULL,
                `default_value` text NOT NULL,
                `values_options` text NOT NULL,
                `integrated` tinyint(1) NOT NULL DEFAULT '0',
        		`type` varchar(32) NOT NULL,
                `status` tinyint(1) NOT NULL DEFAULT '1',
                `sort_order` int(3) NOT NULL DEFAULT '0',
                PRIMARY KEY (`setting_id`),
                UNIQUE KEY `setting_code` (`section_code`,`setting_code`)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        $custom_setting_description = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "custom_setting_description'");
        if ($custom_setting_description->num_rows == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "custom_setting_description` (
        		`setting_id` int(11) NOT NULL,
        		`language_id` int(11) NOT NULL,
        		`title` varchar(32) NOT NULL,
    		    `help` varchar(256) NOT NULL,
            	`popover` varchar(555) NOT NULL,
            	`error_text` varchar(555) NOT NULL,
        		PRIMARY KEY (`setting_id`,`language_id`)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        $custom_setting_to_page = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "custom_setting_to_page'");
        if ($custom_setting_to_page->num_rows == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "custom_setting_to_page` (
                `setting_id` int(11) NOT NULL,
                `page` varchar(32) NOT NULL,
                PRIMARY KEY (`setting_id`,`page`)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        $custom_setting_value = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "custom_setting_value'");
        if ($custom_setting_value->num_rows == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "custom_setting_value` (
        		`value` text NOT NULL,
        		`setting_id` int(11) NOT NULL,
                `page` varchar(32) NOT NULL,
                `id` varchar(32) NOT NULL DEFAULT '0',
        		PRIMARY KEY (`setting_id`,`page`,`id`)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }
    }

    public function drop($tables) {

        $this->cache->delete('custom_settings');

        if ($settings = $this->getSettings()) {
            foreach ($settings as $setting) {
                $this->cache->delete('custom_setting_pages_' . $setting['setting_id']);
                $this->cache->delete('custom_setting_' . $setting['setting_id']);
            }
        }

        foreach ($tables as $key => $table) {
            $tables[$key] =  '`' . DB_PREFIX . $table . '`';
        }

        $this->db->query('DROP TABLE IF EXISTS ' . implode(',', $tables));
    }
}
