<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Integration;

if (!defined('SMF'))
	die('No direct access...');

class Likes
{
	/**
	 * Likes::likePost()
	 *
	 * Gives or removes points to author of the post for each like.
	 * @param int $message id of the message
	 * @return void
	 */
	public static function likePost($like_type, $like_content, $like_userid, $alreadyLiked, $validlikes)
	{
		global $smcFunc, $modSettings;

		//Are we giving credits per like?
		if (!empty($modSettings['Shop_credits_likes_post']))
		{
			// We are only interested in messages for now
			if ($like_type == 'msg')
			{
				$msglikes = $smcFunc['db_query']('', '
					SELECT id_member
					FROM {db_prefix}messages
					WHERE id_msg = {int:like}',
					array(
						'like' => $like_content,
					)
				);
				$likedAuthor = $smcFunc['db_fetch_assoc']($msglikes);
				$smcFunc['db_free_result']($msglikes);

				// Like removed, points too!
				if ($alreadyLiked)
				{
					$result = $smcFunc['db_query']('','
						UPDATE {db_prefix}members
						SET shopMoney = shopMoney - {int:likepost}
						WHERE id_member = {int:id_member}',
						array(
							'likepost' => $modSettings['Shop_credits_likes_post'],
							'id_member' => $likedAuthor['id_member'],
						)
					);
				}
				// Post liked, points delivered!
				else
				{
					$result = $smcFunc['db_query']('','
						UPDATE {db_prefix}members
						SET shopMoney = shopMoney + {int:likepost}
						WHERE id_member = {int:id_member}',
						array(
							'likepost' => $modSettings['Shop_credits_likes_post'],
							'id_member' => $likedAuthor['id_member'],
						)
					);
				}
			}
		}
	}
}