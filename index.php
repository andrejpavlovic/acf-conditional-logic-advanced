<?php
/*
Plugin Name: ACF Conditional Logic Advanced
Description: Add an advanced conditional logic option that can show/hide individual fields based on taxonomy, post format, etc.
Version: 0.0.1
Author: All Boats Rise Inc.
Author URI: https://allboatsrise.com
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
