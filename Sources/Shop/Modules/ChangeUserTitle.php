<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Modules;

use Shop\Shop;
use Shop\Helper;

if (!defined('SMF'))
	die('Hacking attempt...');

class ChangeUserTitle extends Helper\Module
{
	function _construct()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Change User Title';
		$this->desc = 'Change your user title';
		$this->price = 50;
	}

	function getUseInput()
	{
		global $txt;

		$search =
			$txt['Shop_cot_title']. '
			&nbsp;<input class="input_text" type="text" name="newtitle" size="50" />
			<br /><br/>';

		return $search;
	}

	function onUse()
	{
		global $txt, $user_info;

		// Somehow we missed the title?
		if (!isset($_REQUEST['newtitle']))
			fatal_error($txt['Shop_cot_empty_title'], false);

		// Update the information
		updateMemberData($user_info['id'], array('usertitle' => $_REQUEST['newtitle']));

		return '<div class="infobox">' . sprintf($txt['Shop_cot_own_success'], $_REQUEST['newtitle']) . '</div>';
	}
}