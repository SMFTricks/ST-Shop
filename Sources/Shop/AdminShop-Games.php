<?php

/**
 * @package ST Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2018, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

class AdminShop_Games extends AdminShop
{
	public static function Main()
	{
		global $context, $txt;

		$subactions = array(
			'slots' => 'self::Slots',
			'lucky2' => 'self::Lucky2',
			'number' => 'self::Number',
			'pairs' => 'self::Pairs',
			'dice' => 'self::Dice',
		);

		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'general';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['Shop_tab_settings'],
			'description' => $txt['Shop_settings_general_desc'],
			'tabs' => array(
				'slots' => array('description' => $txt['Shop_settings_slots_desc']),
				'lucky2' => array('description' => $txt['Shop_settings_lucky2_desc']),
				'number' => array('description' => $txt['Shop_settings_number_desc']),
				'pairs' => array('description' => $txt['Shop_settings_pairs_desc']),
				'dice' => array('description' => $txt['Shop_settings_dice_desc']),
			),
		);

		$subactions[$sa]();
	}

	public static function Save($config_vars, $return_config, $sa)
	{
		global $context, $scripturl;

		if ($return_config)
			return $config_vars;

		// URL
		$context['post_url'] = $scripturl . '?action=admin;area=shopgames;sa='. $sa. ';save';

		// Saving?
		if (isset($_GET['save'])) {
			checkSession();
			saveDBSettings($config_vars);
			redirectexit('action=admin;area=shopgames;sa='. $sa. '');
		}
		prepareDBSettingContext($config_vars);
	}

	public static function Slots($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_games_slots'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('int', 'Shop_settings_slots_losing', 'min' => '0'),
			'',
			array('int', 'Shop_settings_slots_7', 'min' => '0'),
			array('int', 'Shop_settings_slots_bell', 'min' => '0'),
			array('int', 'Shop_settings_slots_cherry', 'min' => '0'),
			array('int', 'Shop_settings_slots_lemon', 'min' => '0'),
			array('int', 'Shop_settings_slots_orange', 'min' => '0'),
			array('int', 'Shop_settings_slots_plum', 'min' => '0'),
			array('int', 'Shop_settings_slots_dollar', 'min' => '0'),
			array('int', 'Shop_settings_slots_melon', 'min' => '0'),
			array('int', 'Shop_settings_slots_grapes', 'min' => '0'),
		);

		self::Save($config_vars, $return_config, 'slots');
	}

	public static function Lucky2($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_games_lucky2'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('int', 'Shop_settings_lucky2_losing', 'min' => '0'),
			'',
			array('int', 'Shop_settings_lucky2_price', 'min' => '0'),
		);

		self::Save($config_vars, $return_config, 'lucky2');
	}

	public static function Number($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_games_number'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('int', 'Shop_settings_number_losing', 'min' => '0'),
			'',
			array('int', 'Shop_settings_number_complete', 'min' => '0'),
			array('int', 'Shop_settings_number_firsttwo', 'min' => '0'),
			array('int', 'Shop_settings_number_secondtwo', 'min' => '0'),
			array('int', 'Shop_settings_number_firstlast', 'min' => '0'),
		);

		self::Save($config_vars, $return_config, 'number');
	}

	public static function Pairs($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_games_pairs'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('int', 'Shop_settings_pairs_losing', 'min' => '0'),

			array('title', 'Shop_settings_pairs_clubs', 'help' => $txt['Shop_settings_pairs_price']),
			array('int', 'Shop_settings_pairs_clubs_1', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_2', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_3', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_4', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_5', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_6', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_7', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_8', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_9', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_10', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_11', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_12', 'min' => '0'),
			array('int', 'Shop_settings_pairs_clubs_13', 'min' => '0'),

			array('title', 'Shop_settings_pairs_diamonds', 'help' => $txt['Shop_settings_pairs_price']),
			array('int', 'Shop_settings_pairs_diam_1', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_2', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_3', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_4', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_5', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_6', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_7', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_8', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_9', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_10', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_11', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_12', 'min' => '0'),
			array('int', 'Shop_settings_pairs_diam_13', 'min' => '0'),

			array('title', 'Shop_settings_pairs_hearts', 'help' => $txt['Shop_settings_pairs_price']),
			array('int', 'Shop_settings_pairs_hearts_1', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_2', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_3', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_4', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_5', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_6', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_7', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_8', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_9', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_10', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_11', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_12', 'min' => '0'),
			array('int', 'Shop_settings_pairs_hearts_13', 'min' => '0'),

			array('title', 'Shop_settings_pairs_spades', 'help' => $txt['Shop_settings_pairs_price']),
			array('int', 'Shop_settings_pairs_spades_1', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_2', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_3', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_4', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_5', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_6', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_7', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_8', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_9', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_10', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_11', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_12', 'min' => '0'),
			array('int', 'Shop_settings_pairs_spades_13', 'min' => '0'),
		);

		self::Save($config_vars, $return_config, 'pairs');
	}

	public static function Dice($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_games_dice'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('int', 'Shop_settings_dice_losing', 'min' => '0'),
			'',
			array('int', 'Shop_settings_dice_1', 'min' => '0'),
			array('int', 'Shop_settings_dice_2', 'min' => '0'),
			array('int', 'Shop_settings_dice_3', 'min' => '0'),
			array('int', 'Shop_settings_dice_4', 'min' => '0'),
			array('int', 'Shop_settings_dice_5', 'min' => '0'),
			array('int', 'Shop_settings_dice_6', 'min' => '0'),
		);

		self::Save($config_vars, $return_config, 'dice');
	}
}