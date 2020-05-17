<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Helper;

use Shop\Shop;

if (!defined('SMF'))
	die('No direct access...');

class Notify
{
	var $from = [];
	var $to = [];

	public function pm($user, $subject, $body)
	{
		global $sourcedir;

		// Required file
		require_once($sourcedir . '/Subs-Post.php');

		// Who is sending the message
		$this->from = [
			'id' => 0,
			'name' => Shop::getText('notification_sold_from'),
			'username' => Shop::getText('notification_sold_from'),
		];

		// Who receives the message
		$this->to = [
			'to' => is_array($user) ? $user : [$user],
			'bcc' => [],
		];

		// Send the PM
		sendpm($this->to, $subject, $body, false, $this->from);
	}

	public function alert($user, $action, $content, $extra_items)
	{

	}
}