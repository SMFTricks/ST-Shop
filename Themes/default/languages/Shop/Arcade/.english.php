<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

global $modSettings;

$txt['Shop_integration_arcade'] = 'SMF Arcade';
$txt['Shop_integration_arcade_score'] = (!empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits') . ' for submitting score';
$txt['Shop_integration_arcade_score_desc'] = 'The user will receive credits after they submit their score.';