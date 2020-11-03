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

use Shop\Shop;
use Shop\Integration\Addons\Addons;
use Shop\Helper\Log;

if (!defined('SMF'))
	die('No direct access...');

class Arcade implements Addons
{
	private static $_language = false;

	private static $_settings = [];

	/**
	 * @var object Log the user history playing at the arcade.
	 */
	private static $_log;

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

		// Log the creditss the user gets
		self::$_log = new Log;
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
				case 'arcade':
					add_integration_function('integrate_arcade_score', __CLASS__ . '::score', false);
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
			['int', 'Shop_integration_arcade_score', 'subtext' => Shop::getText('integration_arcade_score_desc')],
		];
		$settings = array_merge(self::$_settings, $settings);
	}

	/**
	 * Arcade::score()
	 *
	 * Gives credits for saving a score or for breaking a record
	 * 
	 */
	public static function score($game, $member, $score)
	{
		global $modSettings;

		// Just submitting a score? lame...
		if (!empty($modSettings['Shop_integration_arcade_score']))
			self::$_log->arcade($member['id'], $modSettings['Shop_integration_arcade_score'], $game['name'], $game['id']);
	}
}