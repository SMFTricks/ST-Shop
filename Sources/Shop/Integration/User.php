<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Integration;

use Shop\Shop;
use Shop\Helper\Format;
use Shop\View\Inventory;

if (!defined('SMF'))
	die('No direct access...');

class User
{
	public function load_member_data(&$columns, &$tables, &$set)
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

	public function user_info()
	{
		global $user_info, $user_settings;

		$user_info = array_merge($user_info, [
			'shopMoney' => isset($user_settings['shopMoney']) ? $user_settings['shopMoney'] : 0,
			'shopBank' => isset($user_settings['shopBank']) ? $user_settings['shopBank'] : 0,
			'shopInventory_hide' => isset($user_settings['shopInventory_hide']) ? $user_settings['shopInventory_hide'] : 0,
			'gamesPass' => isset($user_settings['gamesPass']) ? $user_settings['gamesPass'] : 0,
		]);
	}

	public function simple_actions()
	{
		global $context, $user_info;

		if (!empty($user_info))
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

	public function member_context(&$data, $user, $display_custom_fields)
	{
		global $user_profile, $modSettings;

		// Set the data
		$profile = $user_profile[$user];

		$data = array_merge($data, [
			'shopMoney' => $profile['shopMoney'],
			'shopBank' => $profile['shopBank'],
			'shopInventory_hide' => $profile['shopInventory_hide'],
			'gamesPass' => $profile['gamesPass'],
		]);

		// Pocket credits
		if ($modSettings['Shop_display_pocket'] == 1 || $modSettings['Shop_display_pocket'] == 3)
		{
			$data['custom_fields']['shopMoney'] = array(
				'title' => Shop::getText('posting_credits_pocket'),
				'col_name' => 'Shop_pocket',
				'value' => Format::cash($profile['shopMoney'], $modSettings['Shop_display_pocket_placement'] == 0),
				'placement' => $modSettings['Shop_display_pocket_placement'],
			);
		}
		// Bank credits
		if (($modSettings['Shop_display_bank'] == 1) || ($modSettings['Shop_display_bank'] == 3) && !empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_bank']))
		{
			$data['custom_fields']['shopBank'] = array(
				'title' => Shop::getText('posting_credits_bank'),
				'col_name' => 'Shop_bank',
				'value' => Format::cash($profile['shopBank'], $modSettings['Shop_display_bank_placement'] == 0),
				'placement' => $modSettings['Shop_display_bank_placement'],
			);
		}
		// Inventory
		if (empty($profile['shopInventory_hide']) && !empty($modSettings['Shop_inventory_enable']) && !empty($modSettings['Shop_enable_shop']))
		{
			// Load template
			loadTemplate('Shop/Inventory');

			// Load language just in case
			loadLanguage('Shop/Shop');

			$data['custom_fields']['shop_inventory'] = array(
				'title' => Shop::getText('posting_inventory'),
				'col_name' => 'Shop_inventory',
				'value' => template_shop_inventory(Inventory::display($user)),
				'placement' => $modSettings['Shop_inventory_placement'],
			);
		}
	}

	public function fetch_alerts(&$alerts, &$formats)
	{
		global $settings, $scripturl;

		foreach ($alerts as $alert_id => $alert)
			if ($alert['content_type'] == 'shop')
				$alerts[$alert_id]['icon'] = '<img class="alert_icon" src="' . $settings['images_url'] . '/icons/shop/'.$alert['extra']['item_icon'].'.png">';
	}
}