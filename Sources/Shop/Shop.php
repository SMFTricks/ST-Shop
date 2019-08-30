<?php

/**
 * @package ST Shop
 * @version 3.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2018, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

class Shop
{
	public static $name = 'Shop';
	public static $txtpattern = 'Shop_';
	public static $version = '3.2.1';
	public static $itemsdir = '/shop_items/items/';
	public static $modulesdir = '/shop_items/modules/';
	public static $gamesdir = '/shop_items/games';
	public static $supportSite = 'https://smftricks.com/index.php?action=.xml;sa=news;board=51;limit=10;type=rss2';

	public static function initialize()
	{
		self::setDefaults();
		self::defineHooks();
		self::dataHooks();
	}

	/**
	 * Shop::setDefaults()
	 *
	 * Sets almost every setting to a default value
	 * @return void
	 * @author Peter Spicer (Arantor)
	 */
	public static function setDefaults()
	{
		global $modSettings;

		$defaults = array(
			'Shop_enable_shop' => 0,
			'Shop_stats_refresh' => 900,
			'Shop_credits_register' => 200,
			'Shop_credits_topic' => 25,
			'Shop_credits_post' => 10,
			'Shop_credits_likes_post' => 0,
			'Shop_credits_word' => 0,
			'Shop_credits_character' => 0,
			'Shop_credits_limit' => 0,
			'Shop_bank_interest' => 2,
			'Shop_bank_interest_yesterday' => 0,
			'Shop_bank_withdrawal_fee' => 0,
			'Shop_bank_deposit_fee' => 0,
			'Shop_bank_withdrawal_max' => 0,
			'Shop_bank_withdrawal_min' => 0,
			'Shop_bank_deposit_max' => 0,
			'Shop_bank_deposit_min' => 0,
			'Shop_credits_prefix' => '',
			'Shop_credits_suffix' => 'Credits',
			'Shop_images_resize' => 0,
			'Shop_images_width' => '32px',
			'Shop_images_height' => '32px',
			'Shop_items_perpage' => 15,
			'Shop_items_trade_fee' => 0,
			'Shop_display_pocket' => 0,
			'Shop_display_pocket_placement' => 0,
			'Shop_display_bank' => 0,
			'Shop_display_bank_placement' => 0,
			'Shop_inventory_enable' => 0,
			'Shop_inventory_show_same_once' => 0,
			'Shop_inventory_items_num' => 5,
			'Shop_inventory_placement' => 0,
			'Shop_inventory_allow_hide' => 0,
			'Shop_settings_slots_losing' => 500,
			'Shop_settings_lucky2_losing' => 500,
			'Shop_settings_numberslots_losing' => 500,
			'Shop_settings_pairs_losing' => 500,
			'Shop_settings_dice_losing' => 500,
			'Shop_settings_slots_7' => 2000,
			'Shop_settings_slots_bell' => 150,
			'Shop_settings_slots_cherry' => 65,
			'Shop_settings_slots_lemon' => 20,
			'Shop_settings_slots_orange' => 75,
			'Shop_settings_slots_plum' => 50,
			'Shop_settings_slots_dollar' => 100,
			'Shop_settings_slots_melon' => 700,
			'Shop_settings_slots_grapes' => 400,
			'Shop_settings_lucky2_price' => 1000,
			'Shop_settings_number_losing' => 100,
			'Shop_settings_number_complete' => 700,
			'Shop_settings_number_firsttwo' => 450,
			'Shop_settings_number_secondtwo' => 250,
			'Shop_settings_number_firstlast' => 100,
			'Shop_settings_pairs_clubs_1' => 2000,
			'Shop_settings_pairs_clubs_2' => 2000,
			'Shop_settings_pairs_clubs_3' => 2000,
			'Shop_settings_pairs_clubs_4' => 2000,
			'Shop_settings_pairs_clubs_5' => 2000,
			'Shop_settings_pairs_clubs_6' => 2000,
			'Shop_settings_pairs_clubs_7' => 2000,
			'Shop_settings_pairs_clubs_8' => 2000,
			'Shop_settings_pairs_clubs_9' => 2000,
			'Shop_settings_pairs_clubs_10' => 2000,
			'Shop_settings_pairs_clubs_11' => 2000,
			'Shop_settings_pairs_clubs_12' => 2000,
			'Shop_settings_pairs_clubs_13' => 2000,
			'Shop_settings_pairs_diam_1' => 150,
			'Shop_settings_pairs_diam_2' => 150,
			'Shop_settings_pairs_diam_3' => 150,
			'Shop_settings_pairs_diam_4' => 150,
			'Shop_settings_pairs_diam_5' => 150,
			'Shop_settings_pairs_diam_6' => 150,
			'Shop_settings_pairs_diam_7' => 150,
			'Shop_settings_pairs_diam_8' => 150,
			'Shop_settings_pairs_diam_9' => 150,
			'Shop_settings_pairs_diam_10' => 150,
			'Shop_settings_pairs_diam_11' => 150,
			'Shop_settings_pairs_diam_12' => 150,
			'Shop_settings_pairs_diam_13' => 150,
			'Shop_settings_pairs_hearts_1' => 50,
			'Shop_settings_pairs_hearts_2' => 50,
			'Shop_settings_pairs_hearts_3' => 50,
			'Shop_settings_pairs_hearts_4' => 50,
			'Shop_settings_pairs_hearts_5' => 50,
			'Shop_settings_pairs_hearts_6' => 50,
			'Shop_settings_pairs_hearts_7' => 50,
			'Shop_settings_pairs_hearts_8' => 50,
			'Shop_settings_pairs_hearts_9' => 50,
			'Shop_settings_pairs_hearts_10' => 50,
			'Shop_settings_pairs_hearts_11' => 50,
			'Shop_settings_pairs_hearts_12' => 50,
			'Shop_settings_pairs_hearts_13' => 50,
			'Shop_settings_pairs_spades_1' => 200,
			'Shop_settings_pairs_spades_2' => 200,
			'Shop_settings_pairs_spades_3' => 200,
			'Shop_settings_pairs_spades_4' => 200,
			'Shop_settings_pairs_spades_5' => 200,
			'Shop_settings_pairs_spades_6' => 200,
			'Shop_settings_pairs_spades_7' => 200,
			'Shop_settings_pairs_spades_8' => 200,
			'Shop_settings_pairs_spades_9' => 200,
			'Shop_settings_pairs_spades_10' => 200,
			'Shop_settings_pairs_spades_11' => 200,
			'Shop_settings_pairs_spades_12' => 200,
			'Shop_settings_pairs_spades_13' => 200,
			'Shop_settings_dice_1' => 150,
			'Shop_settings_dice_2' => 550,
			'Shop_settings_dice_3' => 750,
			'Shop_settings_dice_4' => 900,
			'Shop_settings_dice_5' => 1500,
			'Shop_settings_dice_6' => 2000,
			'Shop_noty_trade' => 0,
			'Shop_noty_credits' => 0,
			'Shop_noty_items' => 0,
		);
		$modSettings = array_merge($defaults, $modSettings);
	}

	/**
	 * Shop::defineHooks()
	 *
	 * Load hooks quietly
	 * @return void
	 * @author Peter Spicer (Arantor)
	 */
	public static function defineHooks()
	{
		$hooks = array(
			'actions' => 'Shop::hookActions',
			'menu_buttons' => 'Shop::hookButtons',
			'after_create_post' => 'Shop::afterPost',
			//'remove_message' => 'Shop::removePost',
			//'issue_like' => 'Shop::likePost',
			'show_alert' => 'Shop::showAlerts',
			'fetch_alerts' => 'Shop::fetchAlerts',
		);
		foreach ($hooks as $point => $callable)
			add_integration_function('integrate_' . $point, $callable, false);
	}

	/**
	 * Shop::dataHooks()
	 *
	 * Load member and custom fields hooks
	 * @return void
	 * @author Peter Spicer (Arantor)
	 */
	public static function dataHooks()
	{
		global $sourcedir;

		// Load our Profile file
		require_once($sourcedir . '/Shop/ProfileShop.php');
		$hooks = array(
			'load_member_data' => 'ProfileShop::Data',
			'user_info' => 'ProfileShop::Info',
			'simple_actions' => 'ProfileShop::Actions',
			'member_context' => 'ProfileShop::Context',
			'load_custom_profile_fields' => 'ProfileShop::CustomFields',
			'register' => 'ProfileShop::Register',
			'alert_types' => 'ProfileShop::alertTypes',
		);
		foreach ($hooks as $point => $callable)
			add_integration_function('integrate_' . $point, $callable, false);
	}

	/**
	 * Shop::hookActions()
	 *
	 * Insert the actions needed by this mod
	 * @param array $actions An array containing all possible SMF actions. This includes loading different hooks for certain areas.
	 * @return void
	 * @author Peter Spicer (Arantor)
	 */
	public static function hookActions(&$actions)
	{
		global $sourcedir;

		// The main action
		$actions['shop'] = array('Shop/Shop-Home.php', 'ShopHome::Main');
		// Feed
		$actions['shopfeed'] = array(false, 'Shop::getFeed');

		// Add some hooks by action
		switch ($_REQUEST['action']) {
			case 'admin':
				require_once($sourcedir . '/Shop/AdminShop.php');
				add_integration_function('integrate_admin_areas', 'AdminShop::hookAreas', false);
				break;
			case 'profile':
				require_once($sourcedir . '/Shop/ProfileShop.php');
				add_integration_function('integrate_pre_profile_areas', 'ProfileShop::hookAreas', false);
				break;
			case 'who':
				loadLanguage('Shop');
				add_integration_function('who_allowed', 'Shop::whoAllowed', false);
				add_integration_function('integrate_whos_online', 'Shop::whoData', false);
				break;
		}
	}

	/**
	 * Shop::hookButtons()
	 *
	 * Insert a Shop button on the menu buttons array
	 * @param array $buttons An array containing all possible tabs for the main menu.
	 * @return void
	 */
	public static function hookButtons(&$buttons)
	{
		global $context, $txt, $scripturl, $modSettings, $settings;

		$before = 'mlist';
		$temp_buttons = array();
		foreach ($buttons as $k => $v) {
			if ($k == $before) {
				$temp_buttons['shop'] = array(
					'title' => self::text('main_button'),
					'href' => $scripturl . '?action=shop',
					'icon' => 'icons/shop.png',
					'show' => (allowedTo('shop_canAccess') || allowedTo('shop_canManage')) && !empty($modSettings['Shop_enable_shop']),
					'sub_buttons' => array(
						'shopadmin' => array(
							'title' => self::text('admin_button'),
							'href' => $scripturl . '?action=admin;area=shopinfo',
							'show' => allowedTo('shop_canManage'),
						),
					),
				);
			}
			$temp_buttons[$k] = $v;
		}
		$buttons = $temp_buttons;
		
		// Too lazy for adding the menu on all the sub-templates
		if (!empty($modSettings['Shop_enable_shop']))
			self::ShopLayer();

		// DUH! winning!
		self::shopCredits();
	}

	/**
	 * Shop::ShopLayer()
	 *
	 * Used for adding the shop tabs quickly
	 * @return void
	 * @author Diego Andrés
	 */
	public static function ShopLayer()
	{
		global $context;

		if (isset($context['current_action']) && $context['current_action'] === 'shop' && (allowedTo('shop_canAccess') || allowedTo('shop_canManage'))) {
			$position = array_search('body', $context['template_layers']);
			if ($position === false)
				$position = array_search('main', $context['template_layers']);

			if ($position !== false) {
				$before = array_slice($context['template_layers'], 0, $position + 1);
				$after = array_slice($context['template_layers'], $position + 1);
				$context['template_layers'] = array_merge($before, array('Shop'), $after);
			}
		}
	}

	/**
	 * Shop::shopCredits()
	 *
	 * Used in the credits action.
	 * @param boolean $return decide between returning a string or append it to a known context var.
	 * @return string A link for copyright notice
	 */
	public static function shopCredits($return = false)
	{
		global $context, $txt;

		if (isset($context['current_action']) && $context['current_action'] === 'shop')
			return '<br /><div style="text-align: center;"><span class="smalltext">Powered by <a href="https://smftricks.com" target="_blank" rel="noopener">ST Shop</a></span></div>';
	}

	/**
	 * Shop::whoAllowed()
	 *
	 * Used in the who's online action.
	 * @param $allowedActions is the array of actions that require a specific permission.
	 * @return void
	 */
	public static function whoAllowed(&$allowedActions)
	{
		$allowedActions += array(
			'shop' => array('shop_canAccess', 'shop_canManage'),
			'shopinfo' => array('shop_canManage'),
			'shopsettings' => array('shop_canManage'),
			'shopitems' => array('shop_canManage'),
			'shopmodules' => array('shop_canManage'),
			'shopcategories' => array('shop_canManage'),
			'shopgames' => array('shop_canManage'),
			'shopinventory' => array('shop_canManage'),
			'shoplogs' => array('shop_canManage'),
		);
	}

	/**
	 * Shop::whoData()
	 *
	 * Used in the who's online action.
	 * @param $action It gets the request parameters 
	 * @return string A text for the current action
	 */
	public static function whoData($actions)
	{
		global $memberContext, $txt, $modSettings;

		if (isset($actions['action']) && $actions['action'] === 'shop')
		{
			if (isset($actions['sa']))
			{
				// Buying items
				if ($actions['sa'] == 'buy' && allowedTo('shop_canBuy'))
					$who = $txt['whoallow_shop_buy'];
				// Gift items / Send money
				elseif (($actions['sa'] == 'gift' || $actions['sa'] == 'sendmoney') && allowedTo('shop_canGift'))
				{
					$who = $txt['whoallow_shop_gift'];
					if ($actions['sa'] == 'sendmoney')
						$who = sprintf($txt['whoallow_shop_sendmoney'], $modSettings['Shop_credits_suffix']);
				}
				// Viewing Inventory
				elseif (($actions['sa'] == 'inventory' || $actions['sa'] == 'search') && allowedTo('shop_viewInventory'))
				{
					// Searching
					if ($actions['sa'] == 'search')
						$who = $txt['whoallow_shop_search'];
					// Viewing
					else
					{
						$who = $txt['whoallow_shop_owninventory'];
						if (!empty($actions['u']))
						{
							$temp = loadMemberData($actions['u'], false, 'profile');
							loadMemberContext($actions['u']);
							$membername = $memberContext[$actions['u']]['name'];
							$who = sprintf($txt['whoallow_shop_inventory'], $membername, $actions['u']);
						}
					}
				}
				// Bank
				elseif ($actions['sa'] == 'bank' && allowedTo('shop_canBank'))
					$who = $txt['whoallow_shop_bank'];
				// Trade center
				elseif ($actions['sa'] == 'trade' && allowedTo('shop_canTrade'))
					$who = $txt['whoallow_shop_trade'];
				// Trade list
				elseif ($actions['sa'] == 'tradelist' && allowedTo('shop_canTrade'))
					$who = $txt['whoallow_shop_tradelist'];
				// Trade list
				elseif ($actions['sa'] == 'tradelog' && allowedTo('shop_canTrade'))
					$who = $txt['whoallow_shop_tradelog'];
				// Personal trade list
				elseif ($actions['sa'] == 'mytrades' && allowedTo('shop_canTrade'))
				{
					$who = $txt['whoallow_shop_owntrades'];
					if (!empty($actions['u']))
					{
						$temp = loadMemberData($actions['u'], false, 'profile');
						loadMemberContext($actions['u']);
						$membername = $memberContext[$actions['u']]['name'];
						$who = sprintf($txt['whoallow_shop_othertrades'], $membername, $actions['u']);
					}
				}
				// Stats
				elseif ($actions['sa'] == 'stats' && allowedTo('shop_viewStats'))
					$who = $txt['whoallow_shop_stats'];
				// Games Room
				elseif ($actions['sa'] == 'games' && allowedTo('shop_playGames'))
				{
					$who = $txt['whoallow_shop_games'];
					// Playing a game?
					if (isset($actions['play']))
					{
						// Slots
						if ($actions['play'] == 'slots')
							$who = $txt['whoallow_shop_games_slots'];
						// Lucky2
						elseif ($actions['play'] == 'lucky2')
							$who = $txt['whoallow_shop_games_lucky2'];
						// Number Slots
						elseif ($actions['play'] == 'number')
							$who = $txt['whoallow_shop_games_number'];
						// Pairs
						elseif ($actions['play'] == 'pairs')
							$who = $txt['whoallow_shop_games_pairs'];
						// Pairs
						elseif ($actions['play'] == 'dice')
							$who = $txt['whoallow_shop_games_dice'];
					}
				}
			}
		}

		if (!isset($who))
			return false;
		else
			return $who;
	}

	/**
	 * Shop::afterPost()
	 *
	 * Used for giving money/credits/points to the users when posting.
	 * @param array $msgOptions An array of information/options for the post
	 * @param array $topicOptions An array of information/options for the topic
	 * @param array $posterOptions An array of information/options for the poster
	 * @param array $message_columns An array containing the columns of topics table
	 * @param array $message_parameters An array containing the values for every column
	 * @return void
	 */
	public static function afterPost($msgOptions, $topicOptions, $posterOptions, $message_columns, $message_parameters)
	{
		global $smcFunc, $modSettings;

		if(!empty($modSettings['Shop_enable_shop']))
		{
			$result_shop = $smcFunc['db_query']('', '
				SELECT Shop_credits_count, Shop_credits_topic, Shop_credits_post, Shop_credits_bonus
				FROM {db_prefix}boards
				WHERE id_board = {int:key}
				LIMIT 1',
				array(
					'key' => $topicOptions['board'],
				)
			);				
			$shop_info = $smcFunc['db_fetch_assoc']($result_shop);
			$smcFunc['db_free_result']($result_shop);

			if (!empty($shop_info['Shop_credits_count']))
			{
				if (empty($topicOptions['id']))
					$credits = !empty($shop_info['Shop_credits_topic']) ? $shop_info['Shop_credits_topic'] : $modSettings['Shop_credits_topic'];
				else
					$credits = !empty($shop_info['Shop_credits_post']) ? $shop_info['Shop_credits_post'] : $modSettings['Shop_credits_post'];
			
				// Bonus
				$bonus = 0;
				if (!empty($shop_info['Shop_credits_bonus']) && (($modSettings['Shop_credits_word'] > 0) || ($modSettings['Shop_credits_character'] > 0))) {
					// no, BBCCode won't count
					$plaintext = preg_replace('[\[(.*?)\]]', ' ', $_POST['message']);
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
				// and finally, give credits
				$result = $smcFunc['db_query']('','
					UPDATE {db_prefix}members
					SET shopMoney = shopMoney + {int:point}
					WHERE id_member = {int:id_member}
					LIMIT 1',
					array(
						'point' => $point,
						'id_member' => $posterOptions['id']
					)
				);
			}
		}
	}

	/**
	 * Shop::removePost()
	 *
	 * Deduct points from user if post was deleted.
	 * @param int $message id of the message
	 * @return void
	 */
	public static function removePost($message, $row, $recycle)
	{
		global $smcFunc, $modSettings, $board_info;

		if(!empty($modSettings['Shop_enable_shop']))
		{
			$result_shop = $smcFunc['db_query']('', '
				SELECT Shop_credits_count, Shop_credits_topic, Shop_credits_post, Shop_credits_bonus
				FROM {db_prefix}boards
				WHERE id_board = {int:key}
				LIMIT 1',
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
						WHERE id_msg = {int:key}
						LIMIT 1',
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
				$result = $smcFunc['db_query']('','
					UPDATE {db_prefix}members
					SET shopMoney = shopMoney - {int:point}
					WHERE id_member = {int:id_member}
					LIMIT 1',
					array(
						'point' => $point,
						'id_member' => $row['id_member'],
					)
				);
			}
		}
	}

	/**
	 * Shop::likePost()
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
						WHERE id_member = {int:id_member}
						LIMIT 1',
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
						WHERE id_member = {int:id_member}
						LIMIT 1',
						array(
							'likepost' => $modSettings['Shop_credits_likes_post'],
							'id_member' => $likedAuthor['id_member'],
						)
					);
				}
			}
		}
	}

	public static function deployAlert($id_member, $type, $content_id, $link, $extra_items = array(), $ask = true)
	{
		global $smcFunc, $sourcedir, $user_info, $scripturl;

		// Should we ask the user?
		if (!empty($ask))
		{
			// Check user preferences
			require_once($sourcedir . '/Subs-Notify.php');
			$prefs = getNotifyPrefs($id_member, 'shop_user'.$type, true);

			// Check the value. If no value or it's empty, they didn't want alerts, oh well.
			if (empty($prefs[$id_member]['shop_user'.$type]))
				return true;
		}

		$author = false;
		// We need to figure out who the owner of this is.
		$request = $smcFunc['db_query']('', '
			SELECT mem.id_member, mem.pm_ignore_list
			FROM {db_prefix}members AS mem
			WHERE mem.id_member = {int:user}
			LIMIT 1',
			array(
				'user' => $id_member,
			)
		);
		if ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$author = $row['id_member'];
		}
		$smcFunc['db_free_result']($request);

		// If we didn't have a member... leave.
		if (empty($author))
			return true;

		// If the person who sent the notification is the person whose content it is, do nothing.
		if ($author == $user_info['id'])
			return true;

		// Don't spam the alerts: if there is an existing unread alert of the
		// requested type for the target user from the sender, don't make a new one.
		$request = $smcFunc['db_query']('', '
			SELECT id_alert
			FROM {db_prefix}user_alerts
			WHERE id_member = {int:id_member}
				AND is_read = 0
				AND content_type = {string:content_type}
				AND content_id = {int:content_id}
				AND content_action = {string:content_action}',
			array(
				'id_member' => $author,
				'content_type' => 'shop',
				'content_id' => $content_id,
				'content_action' => $type,
			)
		);

		if ($smcFunc['db_num_rows']($request) > 0)
			return true;
		$smcFunc['db_free_result']($request);

		// Alert link
		$extra_items['shop_link'] = $scripturl.$link;

		// Issue, update, move on.
		$smcFunc['db_insert']('insert',
			'{db_prefix}user_alerts',
			array(
				'alert_time' => 'int',
				'id_member' => 'int',
				'id_member_started' => 'int',
				'member_name' => 'string',
				'content_type' => 'string',
				'content_id' => 'int',
				'content_action' => 'string',
				'is_read' => 'int',
				'extra' => 'string'
			),
			array(
				time(),
				$author,
				$user_info['id'],
				$user_info['name'],
				'shop',
				$content_id,
				$type,
				0,
				$smcFunc['json_encode']($extra_items)
			),
			array('id_alert')
		);

		updateMemberData($author, array('alerts' => '+'));
	}

	public static function showAlerts(&$alert, &$link)
	{		
		if (isset($alert['extra']['shop_link']))
			$link = $alert['extra']['shop_link'];
	}

	public static function fetchAlerts(&$alerts, &$formats)
	{
		foreach ($alerts as $alert_id => $alert)
			if ($alert['content_type'] == 'shop' && isset($alert['extra']['item_icon']))
				$alerts[$alert_id]['icon'] = '<img class="alert_icon" src="'.$alert['extra']['item_icon'].'">';
	}

	/**
	 * Shop::scheduled_shopBank()
	 *
	 * Creates a scheduled task for making money in the bank of every user
	 * @return void
	 */
	public static function scheduled_shopBank()
	{
		global $smcFunc, $modSettings;

		// Create some cash out of nowhere. How? By magical means, of course!
		if (!empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_bank']) && $modSettings['Shop_bank_interest'] > 0)
		{
			// Thanks to Zerk for the idea
			$yesterday = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));

			$smcFunc['db_query']('', '
				UPDATE {db_prefix}members
				SET shopBank = shopBank + (shopBank * {float:interest})' . (!empty($modSettings['Shop_bank_interest_yesterday']) ?
				'WHERE last_login > {int:yesterday}' : ''),
				array(
					'interest' => ($modSettings['Shop_bank_interest'] / 100),
					'yesterday' => $yesterday,
				)
			);

			// ID of the task
			$query = $smcFunc['db_query']('', '
				SELECT s.id_task, s.task
				FROM {db_prefix}scheduled_tasks AS s
				WHERE s.task = {string:task}',
				array(
					'task' => 'bank_interest'
				)
			);
			$row = $smcFunc['db_fetch_assoc']($query);
			$smcFunc['db_free_result']($query);

			// Log the task
			$total_time = round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', time())), 3);
			$smcFunc['db_insert']('',
				'{db_prefix}log_scheduled_tasks',
				array(
					'id_task' => 'int',
					'time_run' => 'int',
					'time_taken' => 'float',
				),
				array(
					$row['id_task'],
					time(),
					(int) $total_time,
				),
				array()
			);
		}
	}

	/**
	 * Shop::formatCash()
	 *
	 * It gives the money a format, adding the suffix and prefix set in the admin
	 * @param $money An amount of Shop money 
	 * @return string A text containing the specified money with format
	 */
	public static function formatCash($money)
	{
		global $modSettings;

		// Make 100% sure it's an int
		$money = (int) $money;

		return $modSettings['Shop_credits_prefix'] . $money . ' ' . $modSettings['Shop_credits_suffix'];
	}

	/**
	 * Shop::getImageList()
	 *
	 * It provides the list of images that can be used for items and categories
	 * @return array The list of images
	 */
	public static function getImageList()
	{
		global $boarddir;

		// Start with an empty array
		$imageList = array();
		// Try to open the images directory
		
		if ($handle = opendir($boarddir. self::$itemsdir)) {
			// For each file in the directory...
			while (false !== ($file = readdir($handle))) {
				// ...if it's a valid file, add it to the list
				if (!in_array($file, array('.', '..', 'blank.gif')))
					$imageList[] = $file;
			}
			// Sort the list
			sort($imageList);
			return $imageList;
		}
		// Otherwise, if directory inaccessible, show an error
		else
			fatal_error(self::text('cannot_open_images'));
	}

	/**
	 * Shop::getCatList()
	 *
	 * It provides the list of categories added into the shop
	 * @return array The list of current categories
	 */
	public static function getCatList()
	{
		global $smcFunc;
		
		$cats = array();
		// Get all the categories
		$result = $smcFunc['db_query']('','
			SELECT catid, name, image, description
			FROM {db_prefix}shop_categories
			ORDER BY name ASC',
			array()
		);

		// Loop through all the categories
		while ($row =  $smcFunc['db_fetch_assoc']($result))
			// Let's add this to our array
			$cats[] = array(
				'id' => $row['catid'],
				'name' => $row['name'],
				'image' => $row['image'],
				'description' => $row['description'],
			);
		$smcFunc['db_free_result']($result);
		
		// Return the array
		return $cats;
	}

	/**
	 * Shop::getShopItemsList()
	 *
	 * It provides a FULL list of enabled items
	 * @param $stock A simple flag to indicate if you want to rule out items without stock
	 * @return array The list of items in the shop
	 */
	public static function getShopItemsList($stock = 0)
	{
		global $smcFunc;
		
		$shopitems = array();
		// Get all the categories
		$result = $smcFunc['db_query']('','
			SELECT s.itemid, s.count, s.name, s.status
			FROM {db_prefix}shop_items AS s 
			WHERE s.status = 1'. ($stock == 1 ? ' AND s.count <> 0' : '').'
			ORDER BY name ASC',
			array()
		);

		// Loop through all the categories
		while ($row =  $smcFunc['db_fetch_assoc']($result))
			// Let's add this to our array
			$shopitems[] = array(
				'id' => $row['itemid'],
				'name' => $row['name'],
			);
		$smcFunc['db_free_result']($result);
		
		// Return the array
		return $shopitems;
	}

	/**
	 * Shop::getUserItemsList()
	 *
	 * A FULL list of items from a specific member (Inventory)
	 * @param $id The id of the desired user
	 * @return array The inventory of a certain user
	 */
	public static function getUserItemsList($id)
	{
		global $smcFunc;
		
		$useritems = array();
		// Get all the categories
		$result = $smcFunc['db_query']('','
			SELECT p.id, p.itemid, p.userid, p.trading, s.name, s.status
			FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (p.itemid = s.itemid)
			WHERE p.userid = {int:userid} and p.trading = 0 and s.status = 1
			ORDER BY name ASC',
			array(
				'userid' => $id,
			)
		);

		// Loop through all the categories
		while ($row =  $smcFunc['db_fetch_assoc']($result))
			// Let's add this to our array
			$useritems[] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'itemid' => $row['itemid'],				
			);
		$smcFunc['db_free_result']($result);
		
		// Return the array
		return $useritems;
	}

	/**
	 * Shop::ShopImageFormat()
	 *
	 * Gives the provided item format with his image
	 * @param $image The image of an item
	 * @param $description Optional parameter for including the description in the title/alt
	 * @return string A formatted image
	 */
	public static function ShopImageFormat($image, $description = '')
	{
		global $scripturl, $modSettings, $context, $boardurl;

		// Resize the images...
		if (!empty($modSettings['Shop_images_resize']))
				$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
		else
			$context['itemOpt'] = 'width: 32px; height: 32px;';

		// Item images...
		$context['items_url'] = self::$itemsdir;

		$formatname = '<img src="'. $boardurl . $context['items_url'] . $image. '" alt="'.$description.'" title="'.$description.'" style="'. $context['itemOpt']. ' vertical-align: middle;" />';
		return $formatname;
	}
	
	/**
	 * Shop::text()
	 *
	 * Gets a string key, and returns the associated text string.
	 * @param string $var The text string key.
	 * @global $txt
	 * @return string|boolean
	 * @author Jessica González <suki@missallsunday.com>
	 */
	public static function text($var)
	{
		global $txt;

		if (empty($var))
			return false;

		// Load the mod's language file.
		loadLanguage(self::$name);

		if (!empty($txt[self::$txtpattern.$var]))
			return $txt[self::$txtpattern.$var];

		else
			return false;
	}

	/**
	 * Shop::credits()
	 *
	 * Includes a list of contributors, developers and third party scripts that helped to build this MOD
	 * @return array The list of credits
	 */
	public static function credits()
	{
		// Dear contributor, please feel free to add yourself here.
		$credits = array(
			'dev' => array(
				'name' => 'Developer(s)',
				'users' => array(
					'diego' => array(
						'name' => 'Diego Andr&eacute;s',
						'site' => 'https://smftricks.com',
					),
				),
			),
			'icons' => array(
				'name' => 'Icons',
				'users' => array(
					'fugue' => array(
						'name' => 'Fugue Icons',
						'site' => 'https://p.yusukekamiyamane.com/',
					),
				),
			),
			'thanksto' => array(
				'name' => 'Special Thanks',
				'users' => array(
					'daniel15' => array(
						'name' => 'Daniel15',
						'site' => 'https://www.simplemachines.org/community/index.php?action=profile;u=9547',
						'desc' => 'Original Shop mod',
					),
					'sa' => array(
						'name' => 'SA',
						'site' => 'https://www.simplemachines.org/community/index.php?action=profile;u=84438',
						'desc' => 'Original Developer',
					),
					'vbgamer45' => array(
						'name' => 'vbgamer45',
						'site' => 'https://www.smfhacks.com/',
						'desc' => 'SMF Shop Developer, for keeping his lovely mods always updated',
					),
					'suki' => array(
						'name' => 'Suki',
						'site' => 'https://www.simplemachines.org/community/index.php?action=profile;u=245528',
						'desc' => 'Consultant',
					),
					'hcfwesker' => array(
						'name' => 'hcfwesker',
						'site' => 'https://www.simplemachines.org/community/index.php?action=profile;u=244295',
						'desc' => 'Nice ideas and for making SA/ST Shop feel loved',
					),
					'gerard' => array(
						'name' => 'Zerk',
						'site' => 'https://www.simplemachines.org/community/index.php?action=profile;u=130323',
						'desc' => 'Suggestions, code and cool ideas',
					),
					'ospina' => array(
						'name' => 'Cristian Ospina',
						'site' => 'https://www.simplemachines.org/community/index.php?action=profile;u=215234',
						'desc' => 'Feedback and ideas for Shop Modules',
					),
				),
			),
		);

		// Oh well, one can dream...
		call_integration_hook('integrate_shop_credits', array(&$credits));

		return $credits;
	}

	/**
	 * Shop::getFeed()
	 *
	 * Proxy function to avoid Cross-origin errors.
	 * @return string
	 * @author Jessica González <suki@missallsunday.com>
	 */
	public static function getFeed()
	{
		global $sourcedir;

		require_once($sourcedir . '/Class-CurlFetchWeb.php');
		$fetch = new \curl_fetch_web_data();
		$fetch->get_url_data(Shop::$supportSite);
		if ($fetch->result('code') == 200 && !$fetch->result('error'))
			$data = $fetch->result('body');
		else
			exit;
		smf_serverResponse($data, 'Content-type: text/xml');
	}
}