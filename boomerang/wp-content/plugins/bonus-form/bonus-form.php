<?php
/**
 * Plugin Name:       Bonus Form
 * Description:       A block to display a pseudo-form for bonuses.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bonus-form
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_bonus_form_block_init() {
	register_block_type( __DIR__ . '/build/bonus-form' );

}
add_action( 'init', 'create_block_bonus_form_block_init' );

function bonus_form_enqueue_extra_style() {
	if (!is_singular() || is_admin()) {
        return;
    }
    add_action('wp_footer', function() {
        global $post;

        if ($post && has_block('boomerang/bonus-form', $post->post_content)) {
            $realpathStyle = plugin_dir_path(__FILE__) . 'css/style.css';
            $styles = plugin_dir_url(__FILE__) . 'css/style.css';

            wp_enqueue_style(
                'wp-block-bonus-form-style-extra',
                $styles,
                array(),
                filemtime($realpathStyle)
            );
        }
    });
}
add_action('wp_enqueue_scripts', 'bonus_form_enqueue_extra_style');

function remove_bonus_form_inline_styles() {
	if (is_admin()) {
        return;
    }
    global $post;

    if (!$post || !has_block('boomerang/bonus-form', $post->post_content)) {
        wp_dequeue_style('create-block-bonus-form-style-inline');
        wp_dequeue_style('create-block-bonus-form-style');
    }
}
add_action('wp_enqueue_scripts', 'remove_bonus_form_inline_styles', 20);


