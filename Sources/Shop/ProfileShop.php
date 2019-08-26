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

class ProfileShop
{
	public static function hookAreas(&$profile_areas)
	{
		global $context, $scripturl, $txt, $modSettings;

		loadLanguage('Shop');

		// Profile information
		$before = 'statistics';
		$temp_buttons = array();
		foreach ($profile_areas['info']['areas'] as $k => $v) {
			if ($k == $before) {
				$temp_buttons['inventory'] = array(
					'label' => $txt['Shop_view_inventory'],
					'custom_url' => $scripturl . '?action=shop;sa=inventory',
					'file' => 'Shop/Shop-Inventory.php',
					'function' => 'Shop_mainInv',
					'icon' => 'replies',
					'enabled' => !empty($modSettings['Shop_enable_shop']),
					'permission' => array(
						'own' => array('shop_viewInventory', 'shop_canManage'),
						'any' => array('shop_viewInventory', 'shop_canManage'),
					),
				);
				$temp_buttons['mytrades'] = array(
					'label' => $txt['Shop_view_mytrades'],
					'custom_url' => $scripturl . '?action=shop;sa=mytrades',
					'file' => 'Shop/Shop-Trade.php',
					'function' => 'Shop_tradeProfile',
					'icon' => 'inbox',
					'enabled' => !empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_trade']),
					'permission' => array(
						'own' => array('shop_viewInventory', 'shop_canTrade', 'shop_canManage'),
						'any' => array('shop_viewInventory', 'shop_canTrade', 'shop_canManage'),
					),
				);
			}
			$temp_buttons[$k] = $v;
		}
		$profile_areas['info']['areas'] = $temp_buttons;

		// Clean it
		unset($before);
		unset($temp_buttons);

		// Profile information
		$before = 'report';
		$temp_buttons = array();
		foreach ($profile_areas['profile_action']['areas'] as $k => $v) {
			if ($k == $before) {
				$temp_buttons['gift'] = array(
					'label' => $txt['Shop_shop_gift'],
					'custom_url' => $scripturl . '?action=shop',
					'icon' => 'packages',
					'enabled' => !empty($modSettings['Shop_enable_shop']) && allowedTo('profile_view') && !empty($modSettings['Shop_enable_gift']),
					'subsections' => array(
						'gift' => array($txt['Shop_gift_send_item'], array('shop_canGift', 'shop_canManage')),
						'sendmoney' => array(sprintf($txt['Shop_gift_send_money'], $modSettings['Shop_credits_suffix']), array('shop_canGift', 'shop_canManage')),
					),
					'permission' => array(
						'own' => array(),
						'any' => array('shop_canGift', 'shop_canManage'),
					),
				);
			}
			$temp_buttons[$k] = $v;
		}
		$profile_areas['profile_action']['areas'] = $temp_buttons;

		// Profile fields for editing shop user settings
		add_integration_function('integrate_load_profile_fields', 'ProfileShop::Fields', false);
		add_integration_function('integrate_setup_profile_context', 'ProfileShop::Setup', false);
	}

	public static function Data(&$columns, &$tables, &$set)
	{
		switch ($set)
		{
			case 'normal':
				$columns .= ', mem.shopMoney, mem.shopBank, mem.shopInventory_hide, mem.gamesPass';
				break;
			case 'profile':
				$columns .= ', mem.shopMoney, mem.shopBank, mem.shopInventory_hide, mem.gamesPass';
				break;
			case 'minimal':
				$columns .= ', mem.shopMoney, mem.shopBank, mem.shopInventory_hide, mem.gamesPass';
				break;
			default:
				trigger_error('loadMemberData(): Invalid member data set \'' . $set . '\'', E_USER_WARNING);
		}
	}

	public static function Info()
	{
		global $user_info, $user_settings;

		$user_info += array(
			'shopMoney' => isset($user_settings['shopMoney']) ? $user_settings['shopMoney'] : 0,
			'shopBank' => isset($user_settings['shopBank']) ? $user_settings['shopBank'] : 0,
			'shopInventory_hide' => isset($user_settings['shopInventory_hide']) ? $user_settings['shopInventory_hide'] : 0,
			'gamesPass' => isset($user_settings['gamesPass']) ? $user_settings['gamesPass'] : 0,
		);
	}

	public static function Actions()
	{
		global $context, $user_info;

		if (!empty($user_info)) {
			if (!$context['user']['is_guest']) {
				$context['user']['shopMoney'] = $user_info['shopMoney'];
				$context['user']['shopBank'] = $user_info['shopBank'];
				$context['user']['gamesPass'] = $user_info['gamesPass'];
			}
			else {
				$context['user']['shopMoney'] = 0;
				$context['user']['shopBank'] = 0;
				$context['user']['gamesPass'] = 0;
			}
		}
	}

	public static function Context(&$data, $user, $display_custom_fields)
	{
		global $user_profile, $modSettings, $txt, $sourcedir;

		loadLanguage('Shop');
		require_once($sourcedir.'/Shop/Shop-Inventory.php');
		$profile = $user_profile[$user];
		$profile['shop_inventory'] = ShopInventory::Profile($profile);
		$data += array(
			'shopMoney' => $profile['shopMoney'],
			'shopBank' => $profile['shopBank'],
			'shopInventory_hide' => $profile['shopInventory_hide'],
			'gamesPass' => $profile['gamesPass'],
			'shop_inventory' => $profile['shop_inventory'],
		);

		// Pocket credits
		if (($modSettings['Shop_display_pocket'] == 1) || ($modSettings['Shop_display_pocket'] == 3))
		{
			$pocket = $modSettings['Shop_display_pocket_placement'] == 0 ? $modSettings['Shop_credits_prefix'] . $profile['shopMoney'] : $modSettings['Shop_credits_prefix'] . $profile['shopMoney'] . $txt['Shop_posting_credits_pocket2'];
			$data['custom_fields']['shopMoney'] = array(
				'title' => $txt['Shop_posting_credits_pocket'],
				'col_name' => $txt['Shop_posting_credits_pocket'],
				'value' => $pocket,
				'placement' => $modSettings['Shop_display_pocket_placement'],
			);
		}
		// Bank credits
		if (($modSettings['Shop_display_bank'] == 1) || ($modSettings['Shop_display_bank'] == 3) && !empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_bank']))
		{
			$bank = $modSettings['Shop_display_bank_placement'] == 0 ? $modSettings['Shop_credits_prefix'] . $profile['shopBank'] : $modSettings['Shop_credits_prefix'] . $profile['shopBank'] . $txt['Shop_posting_credits_bank2'];
			$data['custom_fields']['shopBank'] = array(
				'title' => $txt['Shop_posting_credits_bank'],
				'col_name' => $txt['Shop_posting_credits_bank'],
				'value' => $bank,
				'placement' => $modSettings['Shop_display_bank_placement'],
			);
		}
		// Inventory
		if (!empty($profile['shop_inventory']) && ($modSettings['Shop_inventory_enable'] == 1) || ($modSettings['Shop_inventory_enable'] == 3) && !empty($modSettings['Shop_enable_shop'])) {
			// Empty or disabled inventory? Skip it
			if (empty($profile['shop_inventory']) || !empty($profile['shopInventory_hide']))
				return false;

			loadtemplate('Shop');
			$data['custom_fields']['shop_inventory'] = array(
				'title' => $txt['Shop_posting_inventory'],
				'col_name' => $txt['Shop_posting_inventory'],
				'value' => template_Shop_displayInventory($profile['shop_inventory']),
				'placement' => $modSettings['Shop_inventory_placement'],
			);
		}
	}

	public static function CustomFields($memID, $area)
	{
		global $context, $modSettings, $user_profile, $txt, $sourcedir;

		loadLanguage('Shop');

		// Pocket
		if (($area == 'summary') && (($modSettings['Shop_display_pocket'] == 2) || ($modSettings['Shop_display_pocket'] == 3)))
		{
			$context['custom_fields']['shopMoney'] = array(
				'name' => $txt['Shop_posting_credits_pocket'],
				'colname' => $txt['Shop_posting_credits_pocket'],
				'output_html' => $modSettings['Shop_credits_prefix'] . $user_profile[$memID]['shopMoney'] . ' ' . $modSettings['Shop_credits_suffix'],
				'placement' => 0,
			);
		}
		// Bank
		if (($area == 'summary') && (($modSettings['Shop_display_bank'] == 2) || ($modSettings['Shop_display_bank'] == 3)) && !empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_bank']))
		{
			$context['custom_fields']['shopBank'] = array(
				'name' => $txt['Shop_posting_credits_bank'],
				'colname' => $txt['Shop_posting_credits_bank'],
				'output_html' => $modSettings['Shop_credits_prefix'] . $user_profile[$memID]['shopBank'] . ' ' . $modSettings['Shop_credits_suffix'],
				'placement' => 0,
			);
		}
		// Inventory
		if (($area == 'summary') && (($modSettings['Shop_inventory_enable'] == 1) || ($modSettings['Shop_inventory_enable'] == 3)) && !empty($modSettings['Shop_enable_shop']))
		{
			require_once($sourcedir.'/Shop/Shop-Inventory.php');
			$profile = $user_profile[$memID];
			$profile['shop_inventory'] = ShopInventory::Profile($profile);

			// Empty or disabled inventory? Skip it
			if (empty($profile['shop_inventory']) || !empty($profile['shopInventory_hide']))
				return false;

			loadtemplate('Shop');
			$context['custom_fields']['shop_inventory'] = array(
				'name' => $txt['Shop_posting_inventory'],
				'colname' => $txt['Shop_posting_inventory'],
				'output_html' => template_Shop_displayInventory($profile['shop_inventory']),
				'placement' => 2,
			);
		}
	}

	public static function Fields(&$profile_fields)
	{
		global $modSettings, $txt;

		loadLanguage('Shop');
		// Profile information
		$before = 'posts';
		$temp_buttons = array();
		foreach ($profile_fields as $k => $v) {
			if ($k == $before) {
				$temp_buttons['shopMoney'] = array(
					'type' => 'int',
					'label' => $txt['Shop_posting_credits_pocket'],
					'log_change' => true,
					'size' => 7,
					'permission' => 'shop_canManage',
					'input_validate' => function(&$value) {
						if (!is_numeric($value))
							return 'digits_only';
						else
							$value = $value != '' ? strtr($value, array(',' => '', '.' => '', ' ' => '')) : 0;
						return true;
					},
				);
				$temp_buttons['shopBank'] = array(
					'type' => 'int',
					'label' => $txt['Shop_posting_credits_bank'],
					'log_change' => true,
					'size' => 7,
					'enabled' => !empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_bank']),
					'permission' => 'shop_canManage',
					'input_validate' => function(&$value) {
						if (!is_numeric($value))
							return 'digits_only';
						else
							$value = $value != '' ? strtr($value, array(',' => '', '.' => '', ' ' => '')) : 0;
						return true;
					},
				);
				$temp_buttons['shopInventory_hide'] = array(
					'type' => 'check',
					'label' => $txt['Shop_inventory_hide'],
					'permission' => 'profile_identity',
					'enabled' => !empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_inventory_allow_hide']) ,
				);
			}
			$temp_buttons[$k] = $v;
		}
		$profile_fields = $temp_buttons;
	}

	public static function Setup(&$fields)
	{
		if(isset($_REQUEST['area']) && $_REQUEST['area'] == 'account') {
			$before = 'posts';
			$temp_buttons = array();
			foreach ($fields as $k) {
				if ($k == $before) {
					array_push($temp_buttons, $before, 'hr', 'shopMoney', 'shopBank', 'shopInventory_hide');
					continue;
				}
				array_push($temp_buttons, $k);
			}
			$fields = $temp_buttons;
		}
	}

	public static function Register(&$regOptions, &$theme_vars, &$knownInts, &$knownFloats)
	{
		global $modSettings;

		$regOptions['register_vars'] += array(
			'shopMoney' => $modSettings['Shop_credits_register'],
		);
	}
}