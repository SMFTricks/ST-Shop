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

class ChangeUsername extends Helper\Module
{
	function _construct()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Change Username';
		$this->desc = 'Change your Username!';
		$this->price = 50;
	}

	function getUseInput()
	{
		global $txt;

		$input =
			$txt['Shop_cu_new_username'].'&nbsp;<input class="input_text" type="text" id="newusername" name="newusername" size="60" /><br />
				<span class="smalltext">'.$txt['Shop_cu_new_username_desc'].'</span><br />';

		return $input;
	}

	function onUse()
	{
		global $user_info, $smcFunc, $context, $sourcedir, $item_info, $txt;

		$value = trim(preg_replace('~[\t\n\r \x0B\0' . ($context['utf8'] ? '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}' : '\x00-\x08\x0B\x0C\x0E-\x19\xA0') . ']+~' . ($context['utf8'] ? 'u' : ''), ' ', $_REQUEST['newusername']));

		// Name can't be empty!
		if (trim($value) == '')
			fatal_error($txt['Shop_cu_error_empty'], false);
		// It's too long! :o
		elseif ($smcFunc['strlen']($value) > 25)
			fatal_error($txt['Shop_cdn_error_long'], false);
		// Why you want the same name?
		elseif ($user_info['username'] == $value)
			fatal_error($txt['Shop_cdn_error_same'], false);
		// Alright everything fine. But, is it a reserved name?
		elseif ($user_info['username'] != $value)
		{
			require_once($sourcedir . '/Subs-Members.php');
			if (isReservedName($value, $user_info['id']))
				fatal_error($txt['Shop_cdn_error_taken'], false);
		}

		// Update the information
		updateMemberData($user_info['id'], array('member_name' => $value));

		return '<div class="infobox">' . sprintf($txt['Shop_cu_success'], $value) . '</div>';
	}
}