<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Integration;

class Signup
{
	/**
	 * Signup::register()
	 *
	 * Gives money to the users upon registration
	 * 
	 * @param array $regOptions An array of registration options
	 * @return void
	 */
	public function register(array &$regOptions) : void
	{
		global $modSettings;

		$regOptions['register_vars']['shopMoney'] = !empty($modSettings['Shop_credits_register']) ? $modSettings['Shop_credits_register'] : 0;
	}
}