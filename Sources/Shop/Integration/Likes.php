<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Integration;

use Shop\Helper\Database;

if (!defined('SMF'))
	die('No direct access...');

class Likes
{
	/**
	 * @var array Value for credits
	 */
	private $_credits_like;

	/**
	 * @var int ID of the post author
	 */
	private $_author;

	/**
	 * Likes::likePost()
	 *
	 * Gives points to author of the post for each like.
	 * @return void
	 */
	public function likePost(&$type, &$content, &$user, &$time)
	{
		global $modSettings;

		//Are we giving credits per like?
		if (!empty($modSettings['Shop_credits_likes_post']))
		{
			// We are only interested in messages for now
			if ($type == 'msg')
			{
				// For some reason likes don't provide the id of the content author, only the one liking
				$this->_author = Database::Get('', '', '', 'messages', ['id_member'], 'WHERE id_msg = {int:id_msg}', true, '', ['id_msg' => $content]);

				// Post liked, points delivered!
				Database::Update('members', ['likepost' => $modSettings['Shop_credits_likes_post'], 'id_member' => $this->_author['id_member']], 'shopMoney = shopMoney + {int:likepost},', 'WHERE id_member = {int:id_member}');
			}
		}
	}
}