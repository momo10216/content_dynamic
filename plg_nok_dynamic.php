<?php
/**
* @Copyright Copyright (C) 2017 Norbert Kuemin <momo_102@bluemail.ch>
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgContentplg_nok_dynamic extends JPlugin {
	private $_fields = array('date');
	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		$app = JFactory::getApplication();
	  	$globalParams = $this->params;
		$found = false;
		$document = JFactory::getDocument();
		foreach ($fields as $field) {
			$hits = preg_match_all('#{'.$field.'[\s]+([^}]*)}#s', $article->text, $matches);
			if (!empty($hits)) {
				for ($i=0; $i<$hits; $i++) {
					$entryParamsText = $matches[1][$i];
					$plgParams = $this->_get_params($globalParams, $entryParamsText);
					switch ($field) {
						case 'date':
							$html = $this->_date_create_html($plgParams);
							$article->text = str_replace($matches[0][$i], $html, $article->text);
							break;
						default:
							break;
					}
				}
			}
		}
		return $found;
	}

	private function _get_params($globalParams, $entryParamsText) {
		// Initialize with the global paramteres
		//$entryParamsList['width'] = $globalParams->get('width');

		// Overwrite with the local paramteres
		$items = explode('] ', $entryParamsText);
		foreach ($items as $item) {
			if ($item != '') {
				$item	= explode('[', $item);
				$name 	= trim($item[0], '=[]');
				$value	= trim($item[1], '[]');
				$entryParamsList[$name] = $value;
			}
		}
		return $entryParamsList;
	}

	private function _date_create_html($id, $params) {
		$format = $this->_hashget($params,'format');
		switch ($format) {
			case '':
				return date($format);
				break;
			case 'date':
				break;
			default:
				return date($format);
				break;
		}
		return $html;
	}


	private function _hashget($hashmap, $key) {
		if (isset($hashmap[$key])) {
			return $hashmap[$key];
		}
		return '';
	}
}
