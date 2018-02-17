<?php
/*
Plugin Name: ACF Conditional Logic Advanced
Plugin URI: https://github.com/andrejpavlovic/acf-conditional-logic-advanced
Description: Add an advanced conditional logic option that can show/hide individual fields based on taxonomy, post format, etc.
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
