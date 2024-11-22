<?php

class ControllerExtensionModuleTopProdajHome extends Controller {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function index() {
        if ($this->config->get($this->prefix . 'top_prodaj_home_status')) {
            $data = $this->load->language('extension/module/top_prodaj_home');


            if (isset($data['title'])) {
                $data['heading_title'] = $data['title'];
            }
            return $this->load->view('extension/module/top_prodaj_home', $data);
        }
    }

}
