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
	var $_details = [];
	var $types = [];
	var $columns = [];

	function __construct()
	{
		$this->types = [
			'task_file' => 'string',
			'task_class' => 'string',
			'task_data' => 'string',
			'claimed_time' => 'int'
		];

		$this->columns = [
			'$sourcedir/Shop/Tasks/Alerts.php',
			'Alerts'
		];
	}

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

	public function alert($user, $action, $content, $extra_items = [])
	{
		global $user_info, $scripturl;

		// Add forum link
		$extra_items['item_href'] = $scripturl;

		// Pointing somewhere else?
		if (!empty($extra_items['shop_href']))
			$extra_items['item_href'] .= $extra_items['shop_href'];

		// Insert the required details first
		$this->_details = [
			'sender_id' => $user_info['id'],
			'sender_name' => $user_info['name'],
			'receivers' => $user,
			'time' => time(),
			'action' => $action,
			'content_id' => $content,
			'extra_items' => $extra_items
		];

		// Include these details in the values
		$this->columns = array_merge($this->columns, [Database::json_encode($this->_details), 0]);
		
		// Just adding the background task, don't mind me
		Database::Insert('background_tasks', $this->columns, $this->types, ['id_task']);
	}
}