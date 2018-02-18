<?php
/*
Plugin Name: ACF Conditional Logic Advanced
Plugin URI: https://github.com/andrejpavlovic/acf-conditional-logic-advanced
Description: Adds an advanced conditional logic field setting to ACF that can show/hide individual fields based on post template, format, and/or category.
Version: 1.0.0
Author: Andrej Pavlovic
Author URI: https://www.pokret.org/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require 'lib/AcfConditionalLogicAdvanced.php';

$acfCondLogAdv = new AcfConditionalLogicAdvanced([
    // urls
    'basename'			=> plugin_basename( __FILE__ ),
    'path'				=> plugin_dir_path( __FILE__ ),
    'url'				=> plugin_dir_url( __FILE__ ),
]);
$acfCondLogAdv->initialize();
