<?php
/**
* @version	$Id$
* @package	Joomla
* @subpackage	Content-Dynamic
* @copyright	Copyright (c) 2017 Norbert Kuemin. All rights reserved.
* @license	http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
* @author	Norbert Kuemin
* @authorEmail	momo_102@bluemail.ch
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

class PlgContentNokdynamic extends CMSPlugin {
	private $_fields = array('date','daycount');
	public function onContentPrepare($context, &$row, $params, $page = 0) {
		foreach ($this->_fields as $field) {
			$hits = preg_match_all('#{'.$field.'[\s]+([^}]*)}#s', $row->text, $matches);
			if (!empty($hits)) {
				for ($i=0; $i<$hits; $i++) {
					$entryParamsText = $matches[1][$i];
					$plgParams = $this->_get_params($entryParamsText);
					switch ($field) {
						case 'date':
							$html = $this->_date_create_html($plgParams);
							$row->text = str_replace($matches[0][$i], $html, $row->text);
							break;
						case 'daycount':
							$html = $this->_daycount_create_html($plgParams);
							$row->text = str_replace($matches[0][$i], $html, $row->text);
							break;
						default:
							break;
					}
				}
			}
		}
	}

	private function _get_params($entryParamsText) {
		// Initialize with the global paramteres
		//$globalParams = $this->params;
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

	private function _date_create_html($params) {
		$format = $this->_hashget($params,'format');
		switch ($format) {
			case '':
				return date('Y-m-d H:i:s');
				break;
			case 'date':
				break;
			default:
				return date($format);
				break;
		}
		return '';
	}

	private function _daycount_create_html($params) {
		$startdate = $this->_hashget($params,'startdate');
		$enddate = $this->_hashget($params,'enddate');
		$today = date('Y-m-d');
		$textBeforeMulti = $this->_hashget($params,'text_before_multiple');
		$textBeforeSingle = $this->_hashget($params,'text_before_single');
		$textReached = $this->_hashget($params,'text_reached');
		$textAfterSingle = $this->_hashget($params,'text_after_single');
		$textAfterMulti = $this->_hashget($params,'text_after_multiple');
		if ($today < $startdate) {
			$datetime1 = new DateTime($startdate);
			$datetime2 = new DateTime($today);
			$days = $datetime1->diff($datetime2)->format('%a');
			if ($days > 1) {
				return sprintf($textBeforeMulti, $days);
			} else {
				return $textBeforeSingle;
			}
		} else if (($today >= $startdate) && ($today <= $enddate)) {
			return $textReached;
		} else {
			$datetime1 = new DateTime($enddate);
			$datetime2 = new DateTime($today);
			$days = $datetime2->diff($datetime1)->format('%a');
			if ($days > 1) {
				return sprintf($textAfterMulti, $days);
			} else {
				return $textAfterSingle;
			}
		}
		return '';
	}

	private function _hashget($hashmap, $key) {
		if (isset($hashmap[$key])) {
			return $hashmap[$key];
		}
		return '';
	}
}
?>
