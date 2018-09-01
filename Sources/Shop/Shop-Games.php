<?php

/**
 * @package SA Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

function Shop_mainGames()
{
	global $context, $txt, $user_info, $scripturl, $modSettings, $boardurl;

	// Games Room is enabled?
	if (empty($modSettings['Shop_enable_games']))
		fatal_error($txt['Shop_currently_disabled_games'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_playGames');

	// Everything okay, let's see if the games pass it's valid
	if (time() >= $user_info['gamesPass'])
		fatal_error($txt['Shop_games_invalidpass'], false);

	// Get the days!
	if ($user_info['gamesPass'] <= time())
		$context['user']['gamedays'] = 0;
	else
		$context['user']['gamedays'] = round((($user_info['gamesPass'] - time()) / 86400));

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_games'];
	$context['page_description'] = sprintf($txt['Shop_games_welcome_desc'], $context['user']['gamedays']);
	$context['sub_template'] = 'Shop_mainGames';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=games',
		'name' => $txt['Shop_shop_games'],
	);

	// Shop games
	$context['shop']['games'] = Shop_gamesList();

	// Let's set those games...
	$subactions = array(
		'slots' => 'Shop_playSlots',
		'lucky2' => 'Shop_playLucky2',
		'number' => 'Shop_playNumber',
		'pairs' => 'Shop_playPairs',
		'dice' => 'Shop_playDice',
	);

	// Load the game function
	if (isset($_REQUEST['play']) && !empty($_REQUEST['play']))
	{
		// Game
		$sa = $_REQUEST['play'];

		// Set all the page stuff
		$context['template_layers'][] = 'Shop_gamesPlay';
		$context['sub_template'] = 'Shop_gamesPlay';
		$context['page_title'] .= ' - '. $txt['Shop_games_'.$sa];
		$context['game']['title'] = $txt['Shop_games_'.$sa];
		$context['page_description'] = $txt['Shop_games_'.$sa.'_desc'];
		$context['spin'] = $txt['Shop_games_spin'];

		// Load the game
		$subactions[$sa]();
	}
}

function Shop_gamesList()
{
	global $context, $txt, $boardurl;

	// Shop games
	$context['shop']['games'] = array(
		'slots' => array(
			'name' => $txt['Shop_games_slots'],
			'src' => $boardurl . Shop::$gamesdir . '/slots.png',
			'action' => array('slots'),
		),
		'lucky2' => array(
			'name' => $txt['Shop_games_lucky2'],
			'src' => $boardurl . Shop::$gamesdir . '/lucky2.png',
			'action' => array('lucky2'),
		),
		'number' => array(
			'name' => $txt['Shop_games_number'],
			'src' => $boardurl . Shop::$gamesdir . '/numberslots.png',
			'action' => array('number'),
		),
		'pairs' => array(
			'name' => $txt['Shop_games_pairs'],
			'src' => $boardurl . Shop::$gamesdir . '/pairs.png',
			'action' => array('pairs'),
		),
		'dice' => array(
			'name' => $txt['Shop_games_dice'],
			'src' => $boardurl . Shop::$gamesdir . '/dice.png',
			'action' => array('dice'),
		),
	);

	/*'bet' => array(
			'name' => $txt['Shop_games_bet'],
			'src' => $boardurl . Shop::$gamesdir . '/bet.png',
			'image' => 'bet',
		),
		'seven' => array(
			'name' => $txt['Shop_games_seven'],
			'src' => $boardurl . Shop::$gamesdir . '/seven.png',
			'image' => 'seven',
		),
		'blackjack' => array(
			'name' => $txt['Shop_games_blackjack'],
			'src' => $boardurl . Shop::$gamesdir . '/blackjack.png',
			'image' => 'twentyone',
		),*/

	return $context['shop']['games'];
}

function Shop_playSlots()
{
	global $context, $scripturl, $txt, $boardurl, $modSettings, $user_info;

	// Set all the page stuff
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=games;play=slots',
		'name' => $txt['Shop_games_slots'],
	);

	// Faces
	$context['game']['faces'] = array(
		'7' => $modSettings['Shop_settings_slots_7'],
		'bell' => $modSettings['Shop_settings_slots_bell'],
		'cherry' => $modSettings['Shop_settings_slots_cherry'],
		'lemon' => $modSettings['Shop_settings_slots_lemon'],
		'orange' => $modSettings['Shop_settings_slots_orange'],
		'plum' => $modSettings['Shop_settings_slots_plum'],
		'dollar' => $modSettings['Shop_settings_slots_dollar'],
		'melon' => $modSettings['Shop_settings_slots_melon'],
		'grapes' => $modSettings['Shop_settings_slots_grapes'],
	);
	
	// Slots directory
	$context['game_images']['src'] = $boardurl . Shop::$gamesdir . '/slots/';

	if (isset($_REQUEST['do']) && ($_REQUEST['play'] == 'slots'))
	{
		// Check session
		checkSession();

		// Construct wheels
		$wheel1 = array();
		foreach ($context['game']['faces'] as $face => $pay)
			$wheel1[] = $face;
		$wheel2 = array_reverse($wheel1);
		$wheel3 = $wheel1;

		// Set to zero just in case
		list($start1, $start2, $start3) = array(0,0,0);
		// Value of each wheel
		$stop1 = mt_rand(count($wheel1)+$start1, 10*count($wheel1)) % count($wheel1);
		$stop2 = mt_rand(count($wheel2)+$start2, 10*count($wheel2)) % count($wheel2);
		$stop3 = mt_rand(count($wheel3)+$start3, 10*count($wheel3)) % count($wheel3);

		// The results!!! Let's see if we are lucky
		$result1 = $wheel1[$stop1];
		$result2 = $wheel2[$stop2];
		$result3 = $wheel3[$stop3];

		// Format the images...
		$context['game']['wheel1'] = $result1;
		$context['game']['wheel2'] = $result2;
		$context['game']['wheel3'] = $result3;

		// By default he's a loser
		$context['nowin'] = sprintf($txt['Shop_games_loser'], Shop::formatCash($modSettings['Shop_settings_slots_losing']));
		$amount = (-1) * $modSettings['Shop_settings_slots_losing'];
		$final_value = $user_info['shopMoney'] - $modSettings['Shop_settings_slots_losing'];

		// You are very lucky
		if (($result1 == $result2) && ($result1 == $result3) && ($result2 == $result3))
		{
			// Tell him that he's a winner
			$context['win'] = sprintf($txt['Shop_games_winner'], Shop::formatCash($context['game']['faces'][$result1]));
			$final_value = $user_info['shopMoney'] + $context['game']['faces'][$result1];
			$amount = $context['game']['faces'][$result1];
		}
		// Update user cash
		Shop_logGames($user_info['id'], $amount, $_REQUEST['play']);
		// User real money
		$context['user']['games']['real_money'] = Shop::formatCash($final_value);
	}
	// User money
	else
		$context['user']['games']['real_money'] = Shop::formatCash($user_info['shopMoney']);
}

function Shop_playLucky2()
{
	global $context, $txt, $scripturl, $boardurl, $modSettings, $user_info;

	// Set all the page stuff
	$context['spin'] = $txt['Shop_games_roll'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=games;game=lucky2',
		'name' => $txt['Shop_games_lucky2'],
	);

	$context['game']['faces'] = array(
		1 => 0,
		2 => $modSettings['Shop_settings_lucky2_price'], 
		3 => 0, 
		4 => 0, 
		5 => 0, 
		6 => 0,
	);

	// Lucky2 directory
	$context['game_images']['src'] = $boardurl . Shop::$gamesdir . '/lucky2/';

	if (isset($_REQUEST['do']) && ($_REQUEST['play'] == 'lucky2'))
	{			
		// Check session
		checkSession();

		// Construct the dice
		$dice = array();
		foreach ($context['game']['faces'] as $luck => $pay)
			$dice[] = $luck;

		// Values
		$start = 0;
		$stop = mt_rand(count($dice)+$start, 10*count($dice)) % count($dice);

		// The result!
		$result1 = $dice[$stop];

		// Format this
		$context['game']['wheel1'] = $result1;

		// By default he's a loser
		$context['nowin'] = sprintf($txt['Shop_games_loser'], Shop::formatCash($modSettings['Shop_settings_lucky2_losing']));
		$amount = (-1) * $modSettings['Shop_settings_lucky2_losing'];
		$final_value = $user_info['shopMoney'] - $modSettings['Shop_settings_lucky2_losing'];

		if ($result1 == 2)
		{
			// What a surprise... he won!
			$context['win'] = sprintf($txt['Shop_games_winner'], Shop::formatCash($context['game']['faces'][$result1]));
			$final_value = $user_info['shopMoney'] + $context['game']['faces'][$result1];
			$amount = $context['game']['faces'][$result1];
		}
		// Update user cash
		Shop_logGames($user_info['id'], $amount, $_REQUEST['play']);
		// User real money
		$context['user']['games']['real_money'] = Shop::formatCash($final_value);
	}
	// User money
	else
		$context['user']['games']['real_money'] = Shop::formatCash($user_info['shopMoney']);
}

function Shop_playNumber()
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
		Shop_logGames($user_info['id'], $amount, $_REQUEST['play']);
		// User real money
		$context['user']['games']['real_money'] = Shop::formatCash($final_value);
	}
	// User money
	else
		$context['user']['games']['real_money'] = Shop::formatCash($user_info['shopMoney']);
}

function Shop_playPairs()
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
		Shop_logGames($user_info['id'], $amount, $_REQUEST['play']);
		// User real money
		$context['user']['games']['real_money'] = Shop::formatCash($final_value);
	}
	// User money
	else
		$context['user']['games']['real_money'] = Shop::formatCash($user_info['shopMoney']);
}

function Shop_playDice()
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
		Shop_logGames($user_info['id'], $amount, $_REQUEST['play']);
		// User real money
		$context['user']['games']['real_money'] = Shop::formatCash($final_value);
	}
	// User money
	else
		$context['user']['games']['real_money'] =  Shop::formatCash($user_info['shopMoney']);
}