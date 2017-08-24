<?php
/**
 * Plugin Name: Help My Missions Conference Resources
 * Plugin URI: https://github.com/macbookandew/wwntbm-hmmc/
 * Description: Add custom post types and other backend features
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * Copyright: 2017 AndrewRMinion Design

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Flush rewrite rules on (de)activation
 */
function hmmc_activate() {
    hmmc_post_type();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'hmmc_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Set up CPT
 */
function hmmc_post_type() {

    $labels = array(
        'name'                  => 'Resources',
        'singular_name'         => 'Resource',
        'menu_name'             => 'Resources',
        'name_admin_bar'        => 'Resource',
        'archives'              => 'Resource Archives',
        'attributes'            => 'Resource Attributes',
        'parent_item_colon'     => 'Parent Resource:',
        'all_items'             => 'All Resources',
        'add_new_item'          => 'Add New Resource',
        'add_new'               => 'Add New',
        'new_item'              => 'New Resource',
        'edit_item'             => 'Edit Resource',
        'update_item'           => 'Update Resource',
        'view_item'             => 'View Resource',
        'view_items'            => 'View Resources',
        'search_items'          => 'Search Resource',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into resource',
        'uploaded_to_this_item' => 'Uploaded to this resource',
        'items_list'            => 'Resources list',
        'items_list_navigation' => 'Resources list navigation',
        'filter_items_list'     => 'Filter resources list',
    );
    $args = array(
        'label'                 => 'Resource',
        'description'           => 'Resource',
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 4,
        'menu_icon'             => 'dashicons-media-spreadsheet',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'hmmc_resource', $args );

}
add_action( 'init', 'hmmc_post_type', 0 );

/**
 * Set ACF local JSON save directory
 * @param  string $path ACF local JSON save directory
 * @return string ACF local JSON save directory
 */
add_filter( 'acf/settings/save_json', 'hmmc_acf_json_save_point' );
function hmmc_acf_json_save_point( $path ) {
    return plugin_dir_path( __FILE__ ) . '/acf-json';
}

/**
 * Set ACF local JSON open directory
 * @param  array $path ACF local JSON open directory
 * @return array ACF local JSON open directory
 */
add_filter( 'acf/settings/load_json', 'hmmc_acf_json_load_point' );
function hmmc_acf_json_load_point( $path ) {
    $paths[] = plugin_dir_path( __FILE__ ) . '/acf-json';
    return $paths;
}
