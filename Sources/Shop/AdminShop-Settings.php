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

class AdminShop_Settings extends AdminShop
{
	public static function Main()
	{
		global $context, $txt;

		$subactions = array(
			'general' => 'self::General',
			'credits' => 'self::Credits',
			'permissions' => 'self::Perms',
			'profile' => 'self::Profile',
		);

		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'general';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['Shop_tab_settings'],
			'description' => $txt['Shop_settings_general_desc'],
			'tabs' => array(
				'general' => array('description' => $txt['Shop_settings_general_desc']),
				'credits' => array('description' => $txt['Shop_settings_credits_desc']),
				'permissions' => array('description' => $txt['Shop_settings_permissions_desc']),
				'profile' => array('description' => $txt['Shop_settings_profile_desc']),
			),
		);

		$subactions[$sa]();
	}

	public static function Save($config_vars, $return_config, $sa, $is_perm = false)
	{
		global $context, $scripturl;

		$shop_permissions = array(
			-1 => array(
				'shop_canAccess',
				'shop_playGames',
				'shop_canTrade',
				'shop_canBank',
				'shop_canGift',
				'shop_viewInventory',
				'shop_canBuy',
				'shop_viewStats',
				'shop_canManage'
			),
		);

		if ($return_config)
			return $config_vars;

		$context['post_url'] = $scripturl . '?action=admin;area=shopsettings;sa='. $sa. ';save';

		// Saving?
		if (isset($_GET['save'])) {
			checkSession();

			/*// Since we're saving, we need to make sure that a certain permission never ever gets saved.
			// Guests should never be able to manage the shop.
			if ($is_perm == true)
				foreach ($shop_permissions as $group => $perm_list)
					foreach ($perm_list as $perm)
						$_POST[$perm][$group] = 'off';*/

			saveDBSettings($config_vars);
			redirectexit('action=admin;area=shopsettings;sa='. $sa. '');
		}
		prepareDBSettingContext($config_vars);

		// There are certain permissions we do not want giving out. For example, admin rights!
		if($is_perm == true)
		foreach ($shop_permissions as $group => $perm_list)
			foreach ($perm_list as $perm)
				unset ($context[$perm][$group]);
	}

	public static function General($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_settings_general'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('check', 'Shop_enable_shop', 'subtext' => $txt['Shop_enable_shop_desc']),
			array('check', 'Shop_enable_games'),
			array('check', 'Shop_enable_bank'),
			array('check', 'Shop_enable_gift'),
			array('check', 'Shop_enable_trade'),
			array('check', 'Shop_enable_stats'),
			'',
			array('int', 'Shop_stats_refresh', 'subtext' => $txt['Shop_stats_refresh_desc']),
			'',
			array('check', 'Shop_enable_maintenance', 'subtext' => $txt['Shop_enable_maintenance_desc'])
		);

		self::Save($config_vars, $return_config, 'general');
	}

	public static function Credits($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_settings_credits'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('int', 'Shop_credits_register'),
			array('int', 'Shop_credits_topic'),
			array('int', 'Shop_credits_post'),
			'',
			array('int', 'Shop_credits_word'),
			array('int', 'Shop_credits_character'),
			array('int', 'Shop_credits_limit', 'subtext' => $txt['Shop_credits_limit_desc']),
			array('title', 'Shop_bank_settings'),
			array('float', 'Shop_bank_interest', 'subtext' => $txt['Shop_bank_interest_desc'], 'min' => ''),
			array('check', 'Shop_bank_interest_yesterday', 'subtext' => $txt['Shop_bank_interest_yesterday_desc']),
			array('int', 'Shop_bank_withdrawal_fee'),
			array('int', 'Shop_bank_deposit_fee'),
			array('int', 'Shop_bank_withdrawal_min', 'subtext' => $txt['Shop_bank_max_min_desc']),
			array('int', 'Shop_bank_withdrawal_max', 'subtext' => $txt['Shop_bank_max_min_desc']),
			array('int', 'Shop_bank_deposit_min', 'subtext' => $txt['Shop_bank_max_min_desc']),
			array('int', 'Shop_bank_deposit_max', 'subtext' => $txt['Shop_bank_max_min_desc']),
			array('title', 'Shop_credits_general_settings'),
			array('text', 'Shop_credits_prefix', 'subtext' => $txt['Shop_credits_prefix_desc']),
			array('text', 'Shop_credits_suffix', 'subtext' => $txt['Shop_credits_suffix_desc']),
			array('check', 'Shop_images_resize', 'subtext' => $txt['Shop_images_resize_desc']),
			array('text', 'Shop_images_width'),
			array('text', 'Shop_images_height'),
			array('int', 'Shop_items_perpage', 'subtext' => $txt['Shop_items_perpage_desc']),
			array('float', 'Shop_items_trade_fee', 'subtext' => $txt['Shop_items_trade_fee_desc']),
		);

		self::Save($config_vars, $return_config, 'credits');
	}

	public static function Perms($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_settings_permissions'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		// Shop do not play nice with guests. Permissions are already hidden for them, let's exterminate any hint of them in this section.
		$context['permissions_excluded'] = array(-1);

		$config_vars = array(
			array('permissions', 'shop_canAccess', 'subtext' => $txt['permissionhelp_shop_canAccess']),
			array('permissions', 'shop_canBuy', 'subtext' => $txt['permissionhelp_shop_canBuy']),
			array('permissions', 'shop_canGift', 'subtext' => $txt['permissionhelp_shop_canGift']),
			array('permissions', 'shop_viewInventory', 'subtext' => $txt['permissionhelp_shop_viewInventory']),
			array('permissions', 'shop_canTrade', 'subtext' => $txt['permissionhelp_shop_canTrade']),
			array('permissions', 'shop_canBank', 'subtext' => $txt['permissionhelp_shop_canBank']),
			array('permissions', 'shop_viewStats', 'subtext' => $txt['permissionhelp_shop_viewStats']),
			array('permissions', 'shop_playGames', 'subtext' => $txt['permissionhelp_shop_playGames']),
			'',
			array('permissions', 'shop_canManage', 'subtext' => $txt['permissionhelp_shop_canManage']),
		);

		self::Save($config_vars, $return_config, 'permissions', true);
	}

	public static function Profile($return_config = false)
	{
		global $context, $scripturl, $sourcedir, $txt;

		require_once($sourcedir . '/ManageServer.php');
		loadTemplate('Admin');
		loadLanguage('ManageSettings');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $txt['Shop_tab_settings']. ' - ' . $txt['Shop_settings_profile'];
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];

		$config_vars = array(
			array('select', 'Shop_display_pocket',
				array(
					$txt['Shop_items_none_select'],
					$txt['Shop_display_post'],
					$txt['Shop_display_profile'],
					$txt['Shop_display_both']
				),
			),
			array('select', 'Shop_display_pocket_placement',
				array(
					$txt['custom_profile_placement_standard'],
					$txt['custom_profile_placement_icons'],
					$txt['custom_profile_placement_above_signature'],
					$txt['custom_profile_placement_below_signature'],
					$txt['custom_profile_placement_below_avatar'],
					$txt['custom_profile_placement_above_member'],
					$txt['custom_profile_placement_bottom_poster'],
				),
			),
			'',
			array('select', 'Shop_display_bank',
				array($txt['Shop_items_none_select'],
					$txt['Shop_display_post'],
					$txt['Shop_display_profile'],
					$txt['Shop_display_both']
				),
			),
			array('select', 'Shop_display_bank_placement',
				array(
					$txt['custom_profile_placement_standard'],
					$txt['custom_profile_placement_icons'],
					$txt['custom_profile_placement_above_signature'],
					$txt['custom_profile_placement_below_signature'],
					$txt['custom_profile_placement_below_avatar'],
					$txt['custom_profile_placement_above_member'],
					$txt['custom_profile_placement_bottom_poster'],
				),
			),
			'',
			array('select', 'Shop_inventory_enable',
				array(
					$txt['Shop_items_none_select'],
					$txt['Shop_display_post'],
					$txt['Shop_display_profile'],
					$txt['Shop_display_both']
				),
			),
			array('check', 'Shop_inventory_show_same_once'),
			array('int', 'Shop_inventory_items_num'),
			array('select', 'Shop_inventory_placement',
				array(
					$txt['custom_profile_placement_standard'],
					$txt['custom_profile_placement_icons'],
					$txt['custom_profile_placement_above_signature'],
					$txt['custom_profile_placement_below_signature'],
					$txt['custom_profile_placement_below_avatar'],
					$txt['custom_profile_placement_above_member'],
					$txt['custom_profile_placement_bottom_poster'],
				),
			),
			array('check', 'Shop_inventory_allow_hide', 'subtext' => $txt['Shop_inventory_allow_hide_desc']),
			'',
		);

		self::Save($config_vars, $return_config, 'profile');
	}
}