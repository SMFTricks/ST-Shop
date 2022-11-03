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

class Posting
{
	/**
	 * @var array Will set default values for credits, bonuses and the user id.
	 */
	private $_post_shop = [
		'credits' => 0,
		'bonus' => 0,
		'user' => 0
	];

	/**
	 * Posting::after_create_post()
	 *
	 * Used for giving money/credits/points to the users when posting.
	 * This will run even if the shop is disabled, it allows stand-alone integration.
	 * @param array $topicOptions An array of information/options for the topic
	 * @param array $posterOptions An array of information/options for the poster
	 */
	public function after_create_post($msgOptions, array $topicOptions, array $posterOptions) : void
	{
		global $modSettings, $board_info;

		// Is it even enabled?
		if (empty($board_info['Shop_credits_count']) || empty($posterOptions['id']))
			return;

		// Set the user
		$this->_post_shop['user'] = $posterOptions['id'];

		// Figure out the correct initial amount
		$this->_post_shop['credits'] = (empty($topicOptions['id']) ? (!empty($board_info['Shop_credits_topic']) ? $board_info['Shop_credits_topic'] : $modSettings['Shop_credits_topic']) : (!empty($board_info['Shop_credits_post']) ? $board_info['Shop_credits_post'] : $modSettings['Shop_credits_post']));
			
		// Bonus
		if (!empty($board_info['Shop_credits_bonus']) && (($modSettings['Shop_credits_word'] > 0) || ($modSettings['Shop_credits_character'] > 0)))
		{
			// no, BBCCode won't count
			$plaintext = preg_replace('[\[(.*?)\]]', ' ', $_POST['message']);
			// convert newlines to spaces
			$plaintext = str_replace(['<br />', "\r", "\n"], ' ', $plaintext);
			// convert multiple spaces into one
			$plaintext = preg_replace('/\s+/', ' ', $plaintext);
			// bonus for each word
			$this->_post_shop['bonus'] += ($modSettings['Shop_credits_word'] * str_word_count($plaintext));
			// Remove the spaces
			$plaintext = str_replace(' ', '', $plaintext);
			// And now count the characters
			$this->_post_shop['bonus'] += ($modSettings['Shop_credits_character'] * strlen($plaintext));

			// Limit?
			if (isset($modSettings['Shop_credits_limit']) && $modSettings['Shop_credits_limit'] != 0 && $this->_post_shop['bonus'] > $modSettings['Shop_credits_limit'])
				$this->_post_shop['bonus'] = $modSettings['Shop_credits_limit'];
		}

		// and finally, give credits
		Database::Update(
			'members',
			$this->_post_shop,
			'shopMoney = shopMoney + {int:credits} + {int:bonus}',
			'WHERE id_member = {int:user}'
		);
	}

	/**
	 * Posting::remove_message()
	 *
	 * Deduct points from user if post was deleted.
	 * @param int $message id of the message
	 * @return void
	 */
	public function remove_message($message, $row, $recycle)
	{
		global $smcFunc, $modSettings, $board_info;

		if(!empty($modSettings['Shop_enable_shop']))
		{
			$result_shop = $smcFunc['db_query']('', '
				SELECT Shop_credits_count, Shop_credits_topic, Shop_credits_post, Shop_credits_bonus
				FROM {db_prefix}boards
				WHERE id_board = {int:key}',
				array(
					'key' => $board_info['id'],
				)
			);
			$shop_info = $smcFunc['db_fetch_assoc']($result_shop);
			$smcFunc['db_free_result']($result_shop);

			// Credits enabled for this board?
			if (!empty($shop_info['Shop_credits_count']))
			{
				$credits = !empty($shop_info['Shop_credits_post']) ? $shop_info['Shop_credits_post'] : $modSettings['Shop_credits_post'];
				if (!empty($modSettings['search_custom_index_config']))
					$deleted_message['body'] = $row['body'];
				elseif (!empty($recycle))
				{
					$getMessage = $smcFunc['db_query']('', '
						SELECT id_msg, body
						FROM {db_prefix}messages
						WHERE id_msg = {int:key}',
						array(
							'key' => $message,
						)
					);
					$deleted_message = $smcFunc['db_fetch_assoc']($getMessage);
					$smcFunc['db_free_result']($getMessage);
				}
				else
					$deleted_message['body'] = '';
				
				// Bonus
				$bonus = 0;
				if (!empty($shop_info['Shop_credits_bonus']) && (($modSettings['Shop_credits_word'] > 0) || ($modSettings['Shop_credits_character'] > 0)))
				{
						// no, BBCCode won't count
						$plaintext = preg_replace('[\[(.*?)\]]', ' ', $deleted_message['body']);
						// convert newlines to spaces
						$plaintext = str_replace(array('<br />', "\r", "\n"), ' ', $plaintext);
						// convert multiple spaces into one
						$plaintext = preg_replace('/\s+/', ' ', $plaintext);

						// bonus for each word
						$bonus += ($modSettings['Shop_credits_word'] * str_word_count($plaintext));
						// and for each letter
						$bonus += ($modSettings['Shop_credits_character'] * strlen($plaintext));
						
						// Limit?
						if (isset($modSettings['Shop_credits_limit']) && $modSettings['Shop_credits_limit'] != 0 && $bonus > $modSettings['Shop_credits_limit'])
							$bonus = $modSettings['Shop_credits_limit'];
				}
				// Credits + Bonus
				$point = ($bonus + $credits);
				// and finally, deduct credits
				$smcFunc['db_query']('','
					UPDATE {db_prefix}members
					SET shopMoney = shopMoney - {int:point}
					WHERE id_member = {int:id_member}',
					[
						'point' => $point,
						'id_member' => $row['id_member'],
					]
				);
			}
		}
	}
}