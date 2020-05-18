<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Tasks;

use Shop\Shop;
use Shop\Helper\Database;

class Alerts extends SMF_BackgroundTask
{
	
	function __construct()
	{
		
	}

	public function execute()
	{
		
		return true;
	}
}

?>