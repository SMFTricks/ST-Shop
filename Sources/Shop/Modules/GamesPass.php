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

class GamesPass extends Helper\Module
{
	var $seconds;
	var $days;
	var $time;
	var $expires;

	function __construct()
	{
		$this->authorName = 'Sleepy Arcade';
		$this->authorWeb = 'https://www.simplemachines.org/community/index.php?action=profile;u=84438';
		$this->authorEmail = 'wdm2005@blueyonder.co.uk';

		$this->name = 'Games Room Pass xxx days';
		$this->desc = 'Allows access to Games Room for xxx days';
		$this->price = 50;

		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;

		// By default 30 days
		$this->item_info[1] = 30;

		// 1 day
		$this->seconds = 86400;
	}

	function getAddInput()
	{
		return '
			<dl class="settings">
				<dt>
					'.Shop::getText('games_setting1').'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" value="' . $this->item_info[1] . '" />
				</dd>
			</dl>';
	}

	function onUse()
	{
		global $user_info;

		// By default 30 days
		if (empty($this->item_info[1]))
			$this->item_info[1] = 30;

		// Get the time in seconds
		$this->days = ($this->seconds * $this->item_info[1]);

		// He still have access?
		if (!empty($user_info['gamesPass']) && $user_info['gamesPass'] > time())
			$this->time = $user_info['gamesPass'] + $this->days;
		// Expired.. Then calculate from this moment
		else
			$this->time = time() + $this->days;

		// Update the information
		updateMemberData($user_info['id'], array('gamesPass' => $this->time));

		// Give him the exact amount of days he now has
		$this->expires = Helper\Format::gamespass($user_info['gamesPass']) + $this->item_info[1];
		
		return '<div class="infobox">' . sprintf(Shop::getText('games_success'), $this->expires) . '</div>';
	}
}