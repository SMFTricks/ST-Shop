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
use Shop\Helper\Database;
use Shop\Helper\Module;

if (!defined('SMF'))
	die('Hacking attempt...');

class ChangeDisplayName extends Module
{
	/**
	 * @var string Saves the display name.
	 */
	private $display_name;

	/**
	 * ChangeDisplayName::__construct()
	 *
	 * Set the details and basics of the module, along with default values if needed.
	 */
	function __construct()
	{
		// We will of course override stuff...
		parent::__construct();

		// Item details
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'https://github.com/Daniel15';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';
		$this->name = Shop::getText('cdn_name');
		$this->desc = Shop::getText('cdn_desc');
		$this->price = 50;
		$this->require_input = true;
		$this->can_use_item = true;
		$this->addInput_editable = true;

		// Default minimum length
		$this->item_info[1] = 4;
	}

	function getAddInput()
	{
		return '
			<dl class="settings">
				<dt>
					' . Shop::getText('cdn_setting1') . '
				</dt>
				<dd>
					<input type="number" min="1" id="info1" name="info1" this->display_name="' . $this->item_info[1] . '" />
				</dd>
			</dl>';
	}

	function getUseInput()
	{
		return '
		<dl class="settings">
			<dt>
				' . Shop::getText('cdn_new_display_name') . '<br />
				<span class="smalltext">' . sprintf(Shop::getText('dn_new_display_name_desc'), $this->item_info[1]) . '</span>
			</dt>
			<dd>
			<input type="text" id="newDisplayName" name="newDisplayName" size="60" />
			</dd>
		</dl>';
	}

	function onUse()
	{
		global $user_info, $sourcedir;

		// Make sure we got a name
		if (empty($_REQUEST['newDisplayName']) || !isset($_REQUEST['newDisplayName']))
			fatal_error(Shop::getText('cdn_error_empty'), false);

		// The new display name
		$this->display_name = Database::sanitize($_REQUEST['newDisplayName']);

		checkSession();

		// It's not a matter of size, but that's not good enough
		if (trim($this->display_name) == '')
			fatal_error(Shop::getText('cdn_error_empty'), false);
		// Is it long enough then? ;)
		elseif (Database::strlen($this->display_name) < $this->item_info[1])
			fatal_error(sprintf(Shop::getText('cdn_error_short'), $this->item_info[1]), false);
		// It's too long! :o
		elseif (Database::strlen($this->display_name) > 60)
			fatal_error(Shop::getText('cdn_error_long'), false);
		// Well, we wanted a change for once
		elseif ($user_info['name'] == $this->display_name)
			fatal_error(Shop::getText('cdn_error_same'), false);
		// One last detail
		elseif ($user_info['name'] != $this->display_name)
		{
			// Check for reserved name
			require_once($sourcedir . '/Subs-Members.php');

			if (isReservedName($this->display_name, $user_info['id'], true))
				fatal_error(Shop::getText('cdn_error_taken'), false);
		}

		// Update the display name
		updateMemberData($user_info['id'], ['real_name' => $this->display_name]);

		return '
			<div class="infobox">
				' . sprintf(Shop::getText('cdn_success'), $this->display_name) . '
			</div>';
	}
}