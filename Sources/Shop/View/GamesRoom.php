<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\View;

use Shop\Shop;
use Shop\Helper\Format;
use Shop\Helper\Log;

if (!defined('SMF'))
	die('No direct access...');

class GamesRoom
{
	/**
	 * @var object Log the user history playing at the games room.
	 */
	private $_log;

	/**
	 * @var array List of games.
	 */
	protected $_list = [];

	/**
	 * @var string The URL for the games images
	 */
	protected $_images_dir;

	/**
	 * @var array Array of games.
	 */
	protected $_games = [];

	/**
	 * @var string The current game.
	 */
	protected $_sa;

	/**
	 * GamesRoom::__construct()
	 *
	 * Set the tabs for the section and create instance of needed objects
	 */
	function __construct()
	{
		global $modSettings, $user_info, $boardurl;

		// Load language files
		loadLanguage('Shop/Games');

		// Set the images url
		$this->_images_dir = $boardurl . Shop::$gamesdir;

		// Build the tabs for this section
		$this->list();

		// Games
		$this->games();

		// Prepare to log the gift
		$this->_log = new Log;

		// Games Room is enabled?
		if (empty($modSettings['Shop_enable_games']))
			fatal_error(Shop::getText('currently_disabled_games'), false);

		// Is the user is allowed to the gamesroom?
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_playGames');

		// Let's see if the games pass it's valid
		if (time() >= $user_info['gamesPass'])
			fatal_error(Shop::getText('games_invalid'), false);
	}

	public function games()
	{
		// Big array of actions
		$this->_games = [
			'slots' => 'Slots',
			'lucky2' => 'Lucky2',
			'number' => 'Number',
			'pairs' => 'Pairs',
			'dice' => 'Dice',
		];
		$this->_sa = isset($_GET['play'], $this->_games[$_GET['play']]) ? $_GET['play'] : '';

		// More sections?
		call_integration_hook('integrate_shop_games_play', array(&$this->_games, &$this->_sa));
	}

	public function main()
	{
		global $context, $user_info, $scripturl;

		// Games Pass, get the days!
		$context['user']['gamedays'] = ($user_info['gamesPass'] <= time() || empty($user_info['gamesPass']) ? 0 : Format::gamespass($user_info['gamesPass']));

		// Set all the page stuff
		$context['page_title'] = Shop::getText('main_button') . ' - ' . Shop::getText('main_games');
		$context['page_description'] = sprintf(Shop::getText('games_welcome_desc'), $context['user']['gamedays']);
		$context['sub_template'] = 'games';
		$context['linktree'][] = [
			'url' => $scripturl . '?action=shop;sa=games',
			'name' => Shop::getText('main_games'),
		];

		// Shop games
		$context['shop']['games'] = $this->_list;

		// Load the game function
		if (!empty($this->_sa))
		{
			// Set all the page stuff
			$context['template_layers'][] = 'games_play';

			// Let's play
			call_helper('Shop\Games\\' . $this->_games[$this->_sa] . '::play#');
		}
	}

	public function list()
	{
		// Shop games
		$this->_list = [
			'slots' => [
				'name' => Shop::getText('games_slots'),
				'icon' => $this->_images_dir . 'slots.png',
				'action' => ['slots'],
			],
			'lucky2' => [
				'name' => Shop::getText('games_lucky2'),
				'icon' => $this->_images_dir . 'lucky2.png',
				'action' => ['lucky2'],
			],
			'number' => [
				'name' => Shop::getText('games_number'),
				'icon' => $this->_images_dir . 'numberslots.png',
				'action' => ['number'],
			],
			'pairs' => [
				'name' => Shop::getText('games_pairs'),
				'icon' => $this->_images_dir . 'pairs.png',
				'action' => ['pairs'],
			],
			'dice' => [
				'name' => Shop::getText('games_dice'),
				'icon' => $this->_images_dir . 'dice.png',
				'action' => ['dice'],
			],
		];
		// More games?
		call_integration_hook('integrate_shop_games_list', [&$this->_list, &$this->_images_dir]);
	}

	public static function Number()
	{
		global $context, $txt, $scripturl, $boardurl, $modSettings, $user_info;

		// Set all the page stuff
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=games;game=number',
			'name' => $txt['Shop_games_number'],
		);

		// Payout
		$context['game']['payout'] = array(
			'complete' => $modSettings['Shop_settings_number_complete'],
			'firsttwo' => $modSettings['Shop_settings_number_firsttwo'],
			'secondtwo' => $modSettings['Shop_settings_number_secondtwo'],
			'firstlast' => $modSettings['Shop_settings_number_firstlast'],
		);

		if (isset($_REQUEST['do']) && ($_REQUEST['play'] == 'number'))
		{
			// Check session
			checkSession();

			// The results!!! Let's see if we are lucky
			$result1 = mt_rand(0,9);
			$result2 = mt_rand(0,9);
			$result3 = mt_rand(0,9);

			// Format the images...
			$context['game']['wheel1'] = $result1;
			$context['game']['wheel2'] = $result2;
			$context['game']['wheel3'] = $result3;

			// Complete
			if (($result1 == $result2) && ($result1 == $result3) && ($result2 == $result3))
			{
				$winner = 1;
				$pay = $context['game']['payout']['complete'];
			}
			// First two
			elseif ($result1 == $result2)
			{
				$winner = 1;
				$pay = $context['game']['payout']['firsttwo'];
			}
			// Last two
			elseif ($result2 == $result3)
			{
				$winner = 1;
				$pay = $context['game']['payout']['secondtwo'];
			}
			// First and last
			elseif ($result1 == $result3)
			{
				$winner = 1;
				$pay = $context['game']['payout']['firstlast'];
			}
			else
				$winner = 0;

			// By default he's a loser
			$context['nowin'] = sprintf($txt['Shop_games_loser'], Shop::formatCash($modSettings['Shop_settings_number_losing']));
			$amount = (-1) * $modSettings['Shop_settings_number_losing'];
			$final_value = $user_info['shopMoney'] - $modSettings['Shop_settings_number_losing'];

			// You are very lucky
			if ($winner == 1)
			{
				// Tell him that he's a winner
				$context['win'] = sprintf($txt['Shop_games_winner'], Shop::formatCash($pay));
				$final_value = $user_info['shopMoney'] + $pay;
				$amount = $pay;
			}
			// Update user cash
			parent::logGames($user_info['id'], $amount, $_REQUEST['play']);
			// User real money
			$context['user']['games']['real_money'] = Shop::formatCash($final_value);
		}
		// User money
		else
			$context['user']['games']['real_money'] = Shop::formatCash($user_info['shopMoney']);
	}

	public static function Pairs()
	{
		global $context, $txt, $scripturl, $boardurl, $modSettings, $user_info;

		// Set all the page stuff
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=games;game=pairs',
			'name' => $txt['Shop_games_pairs'],
		);

		// Faces
		$context['game']['faces'] = array(
			'clubs_1' => $modSettings['Shop_settings_pairs_clubs_1'],
			'clubs_2' => $modSettings['Shop_settings_pairs_clubs_2'],
			'clubs_3' => $modSettings['Shop_settings_pairs_clubs_3'],
			'clubs_4' => $modSettings['Shop_settings_pairs_clubs_4'],
			'clubs_5' => $modSettings['Shop_settings_pairs_clubs_5'],
			'clubs_6' => $modSettings['Shop_settings_pairs_clubs_6'],
			'clubs_7' => $modSettings['Shop_settings_pairs_clubs_7'],
			'clubs_8' => $modSettings['Shop_settings_pairs_clubs_8'],
			'clubs_9' => $modSettings['Shop_settings_pairs_clubs_9'],
			'clubs_10' => $modSettings['Shop_settings_pairs_clubs_10'],
			'clubs_11' => $modSettings['Shop_settings_pairs_clubs_11'],
			'clubs_12' => $modSettings['Shop_settings_pairs_clubs_12'],
			'clubs_13' => $modSettings['Shop_settings_pairs_clubs_13'],
			'diamonds_1' => $modSettings['Shop_settings_pairs_diam_1'],
			'diamonds_2' => $modSettings['Shop_settings_pairs_diam_2'],
			'diamonds_3' => $modSettings['Shop_settings_pairs_diam_3'],
			'diamonds_4' => $modSettings['Shop_settings_pairs_diam_4'],
			'diamonds_5' => $modSettings['Shop_settings_pairs_diam_5'],
			'diamonds_6' => $modSettings['Shop_settings_pairs_diam_6'],
			'diamonds_7' => $modSettings['Shop_settings_pairs_diam_7'],
			'diamonds_8' => $modSettings['Shop_settings_pairs_diam_8'],
			'diamonds_9' => $modSettings['Shop_settings_pairs_diam_9'],
			'diamonds_10' => $modSettings['Shop_settings_pairs_diam_10'],
			'diamonds_11' => $modSettings['Shop_settings_pairs_diam_11'],
			'diamonds_12' => $modSettings['Shop_settings_pairs_diam_12'],
			'diamonds_13' => $modSettings['Shop_settings_pairs_diam_13'],
			'hearts_1' => $modSettings['Shop_settings_pairs_hearts_1'],
			'hearts_2' => $modSettings['Shop_settings_pairs_hearts_2'],
			'hearts_3' => $modSettings['Shop_settings_pairs_hearts_3'],
			'hearts_4' => $modSettings['Shop_settings_pairs_hearts_4'],
			'hearts_5' => $modSettings['Shop_settings_pairs_hearts_5'],
			'hearts_6' => $modSettings['Shop_settings_pairs_hearts_6'],
			'hearts_7' => $modSettings['Shop_settings_pairs_hearts_7'],
			'hearts_8' => $modSettings['Shop_settings_pairs_hearts_8'],
			'hearts_9' => $modSettings['Shop_settings_pairs_hearts_9'],
			'hearts_10' => $modSettings['Shop_settings_pairs_hearts_10'],
			'hearts_11' => $modSettings['Shop_settings_pairs_hearts_11'],
			'hearts_12' => $modSettings['Shop_settings_pairs_hearts_12'],
			'hearts_13' => $modSettings['Shop_settings_pairs_hearts_13'],
			'spades_1' => $modSettings['Shop_settings_pairs_spades_1'],
			'spades_2' => $modSettings['Shop_settings_pairs_spades_2'],
			'spades_3' => $modSettings['Shop_settings_pairs_spades_3'],
			'spades_4' => $modSettings['Shop_settings_pairs_spades_4'],
			'spades_5' => $modSettings['Shop_settings_pairs_spades_5'],
			'spades_6' => $modSettings['Shop_settings_pairs_spades_6'],
			'spades_7' => $modSettings['Shop_settings_pairs_spades_7'],
			'spades_8' => $modSettings['Shop_settings_pairs_spades_8'],
			'spades_9' => $modSettings['Shop_settings_pairs_spades_9'],
			'spades_10' => $modSettings['Shop_settings_pairs_spades_10'],
			'spades_11' => $modSettings['Shop_settings_pairs_spades_11'],
			'spades_12' => $modSettings['Shop_settings_pairs_spades_12'],
			'spades_13' => $modSettings['Shop_settings_pairs_spades_13'],
		);
		
		// Slots directory
		$context['game_images']['src'] = $boardurl . Shop::$gamesdir . '/pairs/';

		if (isset($_REQUEST['do']) && ($_REQUEST['play'] == 'pairs'))
		{
			// Check session
			checkSession();

			$wheel1 = array();
			foreach ($context['game']['faces'] as $face => $pay)
				$wheel1[] = $face;
			$wheel2 = array_reverse($wheel1);

			list($start1, $start2) = array(0,0);
			$stop1 = mt_rand(count($wheel1)+$start1, 10*count($wheel1)) % count($wheel1);
			$stop2 = mt_rand(count($wheel2)+$start2, 10*count($wheel2)) % count($wheel2);

			// The results!!! Let's see if we are lucky
			$result1 = $wheel1[$stop1];
			$result2 = $wheel2[$stop2];

			// Format the images...
			$context['game']['wheel1'] = $result1;
			$context['game']['wheel2'] = $result2;

			// By default he's a loser
			$context['nowin'] = sprintf($txt['Shop_games_loser'], Shop::formatCash($modSettings['Shop_settings_pairs_losing']));
			$amount = (-1) * $modSettings['Shop_settings_pairs_losing'];
			$final_value = $user_info['shopMoney'] - $modSettings['Shop_settings_pairs_losing'];

			// You are very lucky
			if (($result1 == $result2))
			{
				// Tell him that he's a winner
				$context['win'] = sprintf($txt['Shop_games_winner'], Shop::formatCash($context['game']['faces'][$result1]));
				$final_value = $user_info['shopMoney'] + $context['game']['faces'][$result1];
				$amount = $context['game']['faces'][$result1];
			}
			// Update user cash
			parent::logGames($user_info['id'], $amount, $_REQUEST['play']);
			// User real money
			$context['user']['games']['real_money'] = Shop::formatCash($final_value);
		}
		// User money
		else
			$context['user']['games']['real_money'] = Shop::formatCash($user_info['shopMoney']);
	}

	public static function Dice()
	{
		global $context, $txt, $scripturl, $boardurl, $modSettings, $user_info;

		// Set all the page stuff
		$context['spin'] = $txt['Shop_games_roll'];
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=games;game=dice',
			'name' => $txt['Shop_games_dice'],
		);

		// Faces
		$context['game']['faces'] = array(
			1 => $modSettings['Shop_settings_dice_1'],
			2 => $modSettings['Shop_settings_dice_2'],
			3 => $modSettings['Shop_settings_dice_3'],
			4 => $modSettings['Shop_settings_dice_4'],
			5 => $modSettings['Shop_settings_dice_5'],
			6 => $modSettings['Shop_settings_dice_6'],
		);
		
		// Slots directory
		$context['game_images']['src'] = $boardurl . Shop::$gamesdir . '/dice/';

		if (isset($_REQUEST['do']) && ($_REQUEST['play'] == 'dice'))
		{
			// Check session
			checkSession();

			$wheel1 = array();
			foreach ($context['game']['faces'] as $face => $pay)
				$wheel1[] = $face;
			$wheel2 = array_reverse($wheel1);

			// Set the values
			list($start1, $start2) = array(0,0);
			$stop1 = mt_rand(count($wheel1)+$start1, 10*count($wheel1)) % count($wheel1);
			$stop2 = mt_rand(count($wheel2)+$start2, 10*count($wheel2)) % count($wheel2);

			// The results!!! Let's see if we are lucky
			$result1 = $wheel1[$stop1];
			$result2 = $wheel2[$stop2];

			// Format the images...
			$context['game']['wheel1'] = $result1;
			$context['game']['wheel2'] = $result2;

			// By default he's a loser
			$context['nowin'] = sprintf($txt['Shop_games_loser'], Shop::formatCash($modSettings['Shop_settings_dice_losing']));
			$amount = (-1) * $modSettings['Shop_settings_dice_losing'];
			$final_value = $user_info['shopMoney'] - $modSettings['Shop_settings_dice_losing'];

			// You are very lucky
			if (($result1 == $result2))
			{
				// What a surprise... he won!
				$context['win'] = sprintf($txt['Shop_games_winner'], Shop::formatCash($context['game']['faces'][$result1]));
				$final_value = $user_info['shopMoney'] + $context['game']['faces'][$result1];
				$amount = $context['game']['faces'][$result1];
			}
			// Update user cash
			parent::logGames($user_info['id'], $amount, $_REQUEST['play']);
			// User real money
			$context['user']['games']['real_money'] = Shop::formatCash($final_value);
		}
		// User money
		else
			$context['user']['games']['real_money'] =  Shop::formatCash($user_info['shopMoney']);
	}
}