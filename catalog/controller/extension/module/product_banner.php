<?php

class ControllerExtensionModuleProductBanner extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/product_banner');
		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

        $data['title'] = $results[0]['title'];
        $data['description'] = html_entity_decode($results[0]['description'], ENT_QUOTES, 'UTF-8');
        $data['link'] = $results[0]['link'];
        $data['image'] = $results[0]['image'];

		return $this->load->view('extension/module/product_banner', $data);
    }
}