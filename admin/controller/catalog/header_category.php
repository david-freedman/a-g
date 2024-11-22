<?php

class ControllerCatalogHeaderCategory extends Controller {

	public function autocomplete() {
		$json = array();
	
		// Перевіряємо, чи є параметр 'parent_name' в запиті
		if (isset($this->request->post['parent_name'])) {
			$this->load->model('catalog/header_category'); // Припускаємо, що у вас є модель 'catalog/header_category'
	
			// Отримуємо значення параметра 'parent_name'
			$parent_name = $this->request->post['parent_name'];
	
			// Задаємо ліміт для автозаповнення
			$limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 5;
	
			// Формуємо фільтр даних
			$filter_data = array(
				'parent_name' => $parent_name,
				'start'       => 0,
				'limit'       => $limit
			);
	
			// Отримуємо результати з моделі
			$results = $this->model_catalog_header_category->getAutocomplate($filter_data);


	
			// Формуємо JSON-відповідь
			foreach ($results as $result) { 
				$json[] = array(
					'header_item_id' => $result['header_item_id'],
					'name'           => $result['name'],
				);
			}
		}

		if(isset($this->request->post['category_name'])) {
			$this->load->model('catalog/category');
			$category_name = $this->request->post['category_name'];

			$limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 5;

			$filter_data = array(
				'category_name' => $category_name,
				'start'       => 0,
				'limit'       => $limit
			);

			$results = $this->model_catalog_category->getAutocomplate($filter_data);
	
			// Формуємо JSON-відповідь 
			foreach ($results as $result) {  
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'           => $result['name'],
					'runame' 		=> $this->model_catalog_category->getRuName($result['category_id'])['name'],
					'href'			 => 'ua/index.php?route=product/category&path=' . $result['category_id'],
					'ruhref'			 => 'index.php?route=product/category&path=' . $result['category_id'], 
					'parent_name'    => $this->model_catalog_category->getCategory($result['parent_id'])['name'],
					'status'		 => $result['status'],
					'sort_order'	 => $result['sort_order'],
					'column'		 => $result['column'],
					'parent_id'		 => $result['parent_id'],
				);
			}
		}
	
		// Встановлюємо заголовок типу контенту та повертаємо JSON
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


    private $error = array();

    public function index() {
        $this->load->language('catalog/header_category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/header_category');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/header_category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/header_category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $schema_items_id = $this->model_catalog_header_category->addHeaderCategory($this->request->post);



            $this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_title'])) {
				$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

            $this->response->redirect($this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() { 
        $this->load->language('catalog/header_category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/header_category');


        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		// dump($this->request->get['header_category_id'], __FILE__, __LINE__); 
        // dd($this->request->post, __FILE__, __LINE__);

            $this->model_catalog_header_category->editHeaderCategory($this->request->get['header_category_id'], $this->request->post);

            // $this->model_blog_schema_items->editSchemaItems($this->request->get['schema_items_id'], $this->request->post);

			// if (isset($this->request->post['schema_item'])) {
			// 	foreach ($this->request->post['schema_item'] as $schema_item) {
			// 		$this->model_blog_schema_items->editSchemaItem($this->request->get['schema_items_id'], $schema_item);
			// 	}
			// }

            $this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_title'])) {
				$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

            $this->response->redirect($this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/header_category');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/header_category');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $header_category_id) {
                $this->model_catalog_header_category->deleteHeaderCategory($header_category_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_title'])) {
				$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

            $this->response->redirect($this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_title'])) {
			$filter_title = $this->request->get['filter_title'];
		} else {
			$filter_title = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'si.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('header_category'),
			'href' => $this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/header_category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/header_category/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['header_category'] = array();

		$filter_data = array(
			'filter_title'	  => $filter_title,
			'filter_name'	  => $filter_name,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$header_category_total = $this->model_catalog_header_category->getTotalHeaderCategory($filter_data);

		$results = $this->model_catalog_header_category->getHeaderCategories($filter_data);


		foreach ($results as $result) {
			$data['header_category'][] = array(
				'header_category_id' => $result['header_category_id'],
				'name'            => $result['name'],
				'link'            => $result['link'],
				'sort_order'      => $result['sort_order'],
				'status'          => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'            => $this->url->link('catalog/header_category/edit', 'user_token=' . $this->session->data['user_token'] . '&header_category_id=' . $result['header_category_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . '&sort=sid.title' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . '&sort=si.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . '&sort=si.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $header_category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($header_category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($header_category_total - $this->config->get('config_limit_admin'))) ? $header_category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $header_category_total, ceil($header_category_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_title'] = $filter_title;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		// dd($data, __FILE__, __LINE__);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/header_category_list', $data));
	}

    protected function getForm() {
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['header_category_id'])) {
			$data['action'] = $this->url->link('catalog/header_category/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/header_category/edit', 'user_token=' . $this->session->data['user_token'] . '&header_category_id=' . $this->request->get['header_category_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/header_category', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['header_category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$header_category = $this->model_catalog_header_category->getHeaderCategory($this->request->get['header_category_id']);
		}



		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		// echo'<pre>';
		// var_dump($data['languages']);
		// echo'</pre>';


		if (isset($this->request->post['header_category_description'])) {
			$data['header_category_description'] = $this->request->post['header_category_description'];
		} elseif (isset($this->request->get['header_category_id'])) {
			$data['header_category_description'] = $this->model_catalog_header_category->getHeaderCategoryDescription($this->request->get['header_category_id']);
		} else {
			$data['header_category_description'] = array();
		}

        $header_category['header_category_description'] = $data['header_category_description'];



		$language_id = $this->config->get('config_language_id');
		if (isset($data['header_category_description'][$language_id]['title'])) {
			$data['title'] = $data['header_category_description'][$language_id]['title'];
		}

		if (isset($this->request->post['link'])) {
			$data['link'] = $this->request->post['link'];
		} elseif (!empty($data['header_category_description'])) {
			$data['link'] = $data['header_category_description'][$language_id]['link'];
		} else {
			$data['link'] = '';
		}

		// if (isset($this->request->post['header_category'])) {
		// 	$header_category = $this->request->post['header_category'];
		// } elseif (isset($this->request->get['header_category_id'])) {
		// 	$header_category = $this->model_catalog_header_category->getHeaderCategoryItem($this->request->get['header_category_id']);
		// } else {
		// 	$header_category = array();
		// }

        // dd($header_category['header_category_description'], __FILE__, __LINE__);
		// $header_category['header_category_items'] =

        if (isset($this->request->post['header_category'])) {
        	$header_category = $this->request->post['header_category'];
        } elseif (isset($this->request->get['header_category_id'])) {
        	$header_category['header_category_items'] = $this->model_catalog_header_category->getHeaderCategoryItems($this->request->get['header_category_id']);
        } else {
        	$header_category = array();
        }




        if($header_category) 
        { 
			$this->load->model('tool/image');
			
            foreach ($header_category['header_category_items'] as $key => $item) {
				$header_category['header_category_items'][$key]['thumb'] = $this->model_tool_image->resize($item['image'], 100, 100);
                $header_category['header_category_items'][$key]['description'] = $this->model_catalog_header_category->getHeaderCategoryItemDescription($item['header_item_id']);
            }
        }


        $data['header_category'] = $header_category;



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/header_category_form', $data));
    }

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/header_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// foreach ($this->request->post['schema_items_description'] as $language_id => $value) {
		// 	if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
		// 		$this->error['title'][$language_id] = $this->language->get('error_title');
		// 	}
		// }

		// if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 255)) {
		// 	$this->error['name'] = $this->language->get('error_name');
		// }

		// foreach ($this->request->post['schema_item'] as $schema_item) {
		// 	if ((utf8_strlen($schema_item['title']) < 3) || (utf8_strlen($schema_item['title']) > 255)) {
		// 		$this->error['title'] = $this->language->get('error_title');
		// 	}
		// }

		// if ($this->error && !isset($this->error['warning'])) {
		// 	$this->error['warning'] = $this->language->get('error_warning');
		// }

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/header_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


	
	// public function autocomplete() {
	// 	$json = array();

	// 	if (isset($this->request->get['filter_title'])) {
	// 		$this->load->model('blog/schema_items');

	// 		if (isset($this->request->get['filter_title'])) {
	// 			$filter_title = $this->request->get['filter_title'];
	// 		} else {
	// 			$filter_title = '';
	// 		}

	// 		if (isset($this->request->get['limit'])) {
	// 			$limit = $this->request->get['limit'];
	// 		} else {
	// 			$limit = $this->config->get('config_limit_autocomplete');
	// 		}

	// 		$filter_data = array(
	// 			'filter_title'  => $filter_title,
	// 			'start'        => 0,
	// 			'limit'        => $limit
	// 		);

	// 		$results = $this->model_blog_schema_items->getSchemaItemss($filter_data);

	// 		foreach ($results as $result) {
	// 			$json[] = array(
	// 				'schema_items_id' => $result['schema_items_id'],
	// 				'title'       => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
	// 			);
	// 		}
	// 	}

	// 	if (isset($this->request->get['filter_name'])) {
	// 		$this->load->model('blog/schema_items');

	// 		if (isset($this->request->get['filter_name'])) {
	// 			$filter_name = $this->request->get['filter_name'];
	// 		} else {
	// 			$filter_name = '';
	// 		}

	// 		if (isset($this->request->get['limit'])) {
	// 			$limit = $this->request->get['limit'];
	// 		} else {
	// 			$limit = $this->config->get('config_limit_autocomplete');
	// 		}

	// 		$filter_data = array(
	// 			'filter_name'  => $filter_name,
	// 			'start'        => 0,
	// 			'limit'        => $limit
	// 		);

	// 		$results = $this->model_blog_schema_items->getSchemaItemss($filter_data);

	// 		foreach ($results as $result) {
	// 			$json[] = array(
	// 				'schema_items_id' => $result['schema_items_id'],
	// 				'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
	// 			);
	// 		}
	// 	}

	// 	$this->response->addHeader('Content-Type: application/json');
	// 	$this->response->setOutput(json_encode($json));
	// }
}
