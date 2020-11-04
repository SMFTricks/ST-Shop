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
	 * @var array Store the data about the specific message id
	 */
	private $_likedAuthor;

	/**
	 * Posting::__construct()
	 *
	 * Add default values for the posting values, and create a new instance of Boards
	 */
	function __construct()
	{
		// Some defaults for fallback
		$this->_credits_like = 0;
	}

	/**
	 * Likes::likePost()
	 *
	 * Gives or removes points to author of the post for each like.
	 * @return void
	 */
	public function likePost($like_info)
	{
		global $modSettings;

		//Are we giving credits per like?
		if (!empty($modSettings['Shop_credits_likes_post']))
		{
			// Set the amount of credits
			$this->_credits_like = $modSettings['Shop_credits_likes_post'];

			// We are only interested in messages for now
			if ($this->_likes_class::get($like_info->_type) == 'msg')
			{
				$this->_likedAuthor = Database::Get('', '', '', 'messages', ['id_member'], 'WHERE id_msg = {int:like}', true, ['like' => $like_info->_content]);

				// Like removed, points too!
				if ($this->_likes_class::get($like_info->_alreadyLiked))
				{
					Database::Update('members', [
						'likepost' => $this->_credits_like,
						'id_member' => $this->_likedAuthor['id_member'],
					], 'SET shopMoney = shopMoney - {int:likepost}', 'WHERE id_member = {int:id_member}');
				}
				// Post liked, points delivered!
				else
				{
					Database::Update('members', [
						'likepost' => $this->_credits_like,
						'id_member' => $this->_likedAuthor['id_member'],
					], 'SET shopMoney = shopMoney + {int:likepost}', 'WHERE id_member = {int:id_member}');
				}
			}
		}
	}
}