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

class AddToPostCount extends Helper\Module
{
	function __construct()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Add xxx to Post Count';
		$this->desc = 'Increase your Post Count by xxx!';
		$this->price = 50;

		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;

		// 100 posts by default
		$this->item_info[1] = 100;
	}

	function getAddInput()
	{
		return '
			<dl class="settings">
				<dt>
					'.Shop::getText('atpc_setting1').'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" value="' . $this->item_info[1] . '" />
				</dd>
			</dl>';
	}

	function onUse()
	{
		global $user_info;

		// Update info
		updateMemberData($user_info['id'], array('posts' => ($this->item_info[1] + $user_info['posts'])));
		
		return '
			<div class="infobox">
				' . sprintf(Shop::getText('atpc_success'), $this->item_info[1]) . '
			</div>';
	}
}