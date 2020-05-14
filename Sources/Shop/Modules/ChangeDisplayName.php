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

class ChangeDisplayName extends Helper\Module
{
	// Name
	var $display_name;

	function __construct()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Change Display Name';
		$this->desc = 'Change your display name!';
		$this->price = 50;
		
		$this->require_input = true;
		$this->can_use_item = true;
		$this->addInput_editable = true;

		// Default minimum length
		$this->item_info[1] = 4;
	}

	function getAddInput()
	{
		global $item_info, $txt;

		$info = '
			<dl class="settings">
				<dt>
					'.Shop::getText('cdn_setting1').'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" this->display_name="' . $this->item_info[1] . '" />
				</dd>
			</dl>';

		return $info;
	}

	function getUseInput()
	{
		return
			Shop::getText('cdn_new_display_name').'&nbsp;
			<input class="input_text" type="text" id="newDisplayName" name="newDisplayName" size="60" /><br />
			<span class="smalltext">' . sprintf(Shop::getText('dn_new_display_name_desc'), $this->item_info[1]) . '</span>';
	}

	function onUse()
	{
		global $user_info, $context, $sourcedir;

		$this->display_name = trim(preg_replace('~[\t\n\r \x0B\0' . ($context['utf8'] ? '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}' : '\x00-\x08\x0B\x0C\x0E-\x19\xA0') . ']+~' . ($context['utf8'] ? 'u' : ''), ' ', $_REQUEST['newDisplayName']));

		// Name can't be empty!
		if (trim($this->display_name) == '')
			fatal_error(Shop::getText('cdn_error_empty'), false);
		// Is it long enough then? ;)
		elseif (Helper\Database::strlen($this->display_name) < $this->item_info[1])
			fatal_error(sprintf(Shop::getText('cdn_error_short'), $this->item_info[1]), false);
		// It's too long! :o
		elseif (Helper\Database::strlen($this->display_name) > 60)
			fatal_error(Shop::getText('cdn_error_long'), false);
		// Why you want the same name?
		elseif ($user_info['name'] == $this->display_name)
			fatal_error(Shop::getText('cdn_error_same'), false);
		// Alright everything fine. But, is it a reserved name?
		elseif ($user_info['name'] != $this->display_name)
		{
			require_once($sourcedir . '/Subs-Members.php');
			if (isReservedName($this->display_name, $user_info['id']))
				fatal_error(Shop::getText('cdn_error_taken'), false);
		}

		// Update the information
		updateMemberData($user_info['id'], array('real_name' => $this->display_name));

		return '
			<div class="infobox">
				' . sprintf(Shop::getText('cdn_success'), $this->display_name) . '
			</div>';
	}
}