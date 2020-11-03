<?php

/**
 * SMF Arcade
 * 
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Integration\Addons\Arcade;

use Shop\Integration\Addons\Addons;

if (!defined('SMF'))
	die('No direct access...');

class Arcade implements Addons
{
	private static $_language = false;

	private static $_settings = [];

	/**
	 * Addons::integration()
	 *
	 * Loads the essentials of the integration for this addon
	 */
	public static function integration(&$theme)
	{
		// Language
		self::language();

		// Hooks
		self::defineHooks();
	}

	/**
	 * Arcade::defineHooks()
	 *
	 * Loads the hooks and languages for this addon
	 */
	public static function defineHooks()
	{
		// Add some hooks by action
		if (isset($_REQUEST['action']))
			switch ($_REQUEST['action'])
			{
				case 'admin': 
					if (isset($_REQUEST['area']) && $_REQUEST['area'] == 'shopsettings')
						add_integration_function('integrate_shop_addons_settings', __CLASS__ . '::settings', false);
				break;
			}
	}

	/**
	 * Arcade::language()
	 *
	 * Loads the hooks and languages for this addon
	 */
	public static function language()
	{
		// Actions
		if (isset($_REQUEST['action']))
			switch ($_REQUEST['action'])
			{
				case 'admin':
					if (isset($_REQUEST['area']) && $_REQUEST['area'] == 'shopsettings')
						self::$_language = true;
					break;
				case 'arcade':
					self::$_language = true;
					break;
			}

		// Load it when necessary only
		if (!empty(self::$_language))
			loadLanguage('Shop/Arcade/');
	}

	/**
	 * Arcade::settings()
	 *
	 * Loads the hooks and languages for this addon
	 * 
	 * Use array_merge to add your settings into the array
	 */
	public static function settings(&$settings)
	{
		self::$_settings = [
			['title', 'Shop_integration_arcade'],

		];
		$settings = array_merge(self::$_settings, $settings);
	}
}