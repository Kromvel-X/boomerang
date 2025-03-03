<?php
/**
 * Plugin Name:    Slot Catalog
 * Description:    A plugin to display a slot catalog.
 * Version:        1.0.0
 * Author:         Kromvel
 * Author URI:     kromveln@gmail.com
 * Requires PHP:   7.4
 * Text Domain:    slot-catalog
*/  

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// use SlotCatalog\SlotCatalog;

// $slotCatalog = new SlotCatalog();
// $slotCatalog->show();

// Запускаем плагин


add_action('after_setup_theme', function(){
    // var_dump(value: 'wp_head');
    SlotCatalog\Core\Plugin::init();
});