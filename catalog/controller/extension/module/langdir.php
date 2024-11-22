<?php
class ControllerExtensionModuleLangdir extends Controller {
	public function index() {
		$result = array();

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		$hreflangs = $this->config->get('langdir_hreflang');
		
		$locales = $this->config->get('langdir_dir');
		
		$initial_config_language_id = $this->config->get('config_language_id');

		foreach ($languages as $language) {
			$target_locale = $locales[$language['language_id']];
			
			if ($language['status']) {
				$this->config->set('config_language_id', $languages[$language['code']]['language_id']);
			}	
			// Remove dir for main lang if it is necessary
			if ($language['code'] == $this->config->get('config_language') && $this->config->get('langdir_off')) {
				$target_locale = '';
			}

			if ($target_locale) {
				$target_locale .= '/';
			}
			
			if (!isset($this->request->get['route'])) {
				$href = $this->url->link('common/home');
			} else {
				$url_data = $this->request->get;

				unset($url_data['_route_']);

				$route = $url_data['route'];

				unset($url_data['route']);

				$url = '';

				if ($url_data) {
					$url = '&' . urldecode(http_build_query($url_data, '', '&'));
				}

				$protocol = $this->request->server['HTTPS'];

				if (isset($route) && isset($url) && isset($protocol)) {
					$href = $this->url->link($route, $url, $protocol);
				} else {
					$href = $this->url->link('common/home');
				}
			}
			
			$href = $this->targetUrlProcessing($href, $target_locale);

			if (isset($hreflangs[$language['language_id']])) {
				$result[] = array(
					'code' => $hreflangs[$language['language_id']],
					'href' => $href,
					'rel'	 => 'alternate',
				);
			}
		}
		
		$this->config->set('config_language_id', $initial_config_language_id);

		return $result;
	}
	
	private function targetUrlProcessing($url, $target_locale) {
		$url = str_replace([HTTPS_SERVER, HTTP_SERVER], '', $url);
		
		// Home page without slash
		if (!isset($this->request->get['route'])) {
			$target_locale = rtrim($target_locale, '/');
		}
    
    $url = $this->removeAnyLocaleFromURL($url);
		
		$url = HTTPS_SERVER . $target_locale . $url;
		
		return $url;
	}
	
  /*
   * delete lang directory with slash
   */
	private function removeAnyLocaleFromURL($url) {
		foreach ($this->config->get('langdir_dir') as $locale) {
      if (0 === strpos($url, $locale)) {
        $url = $this->cutLocale($url, $locale);
      }
		}

		return $url;
	}
  
  private function cutLocale($string, $locale) {
    if (0 === strpos($string, $locale . '/')) {
			$string = mb_substr($string, mb_strlen($locale . '/'), null, 'UTF-8');
		} elseif (0 === strpos($string, $locale) && $locale == trim($string, '/')) {
			// Home page
			$string = '';
		}

    return $string;
  }

}
