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

class Buttons
{
	/**
	 * Buttons::hookButtons()
	 *
	 * Insert a Shop button on the menu buttons array
	 * @param array $buttons An array containing all possible tabs for the main menu.
	 */
	public function menu_buttons(&$buttons) : void
	{
		global $scripturl, $modSettings;

		// Language
		loadLanguage('Shop/Shop');

		$temp_buttons = [];
		foreach ($buttons as $k => $v)
		{
			if ($k === 'mlist')
			{
				$temp_buttons['shop'] = [
					'title' => Shop::getText('main_button'),
					'href' => $scripturl . '?action=shop',
					'icon' => 'icons/shop.png',
					'show' => (allowedTo('shop_canAccess') || allowedTo('shop_canManage')) && !empty($modSettings['Shop_enable_shop']),
					'sub_buttons' => [
						'shopadmin' => [
							'title' => Shop::getText('admin_button'),
							'href' => $scripturl . '?action=admin;area=shopinfo',
							'show' => allowedTo('shop_canManage'),
						],
					],
				];
			}
			$temp_buttons[$k] = $v;
		}
		$buttons = $temp_buttons;

		// Shop admins/managers can also see the admin button
		$buttons['admin']['show'] = $buttons['admin']['show'] || allowedTo('shop_canManage');
	}
}