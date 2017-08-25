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
    $rewrite = array(
        'slug'                => 'resources',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
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
        'rewrite'               => $rewrite,
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
function hmmc_acf_json_save_point( $path ) {
    return plugin_dir_path( __FILE__ ) . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'hmmc_acf_json_save_point' );

/**
 * Set ACF local JSON open directory
 * @param  array $path ACF local JSON open directory
 * @return array ACF local JSON open directory
 */
function hmmc_acf_json_load_point( $path ) {
    $paths[] = plugin_dir_path( __FILE__ ) . '/acf-json';
    return $paths;
}
add_filter( 'acf/settings/load_json', 'hmmc_acf_json_load_point' );

/**
 * Add download link
 * @param  string $content HTML content
 * @return string HTML content with download button added
 */
function hmmc_download_link( $content ) {
    if ( 'hmmc_resource' == get_post_type() && get_field( 'download_url' ) ) {
        $content = '<p><a href="' . get_field( 'download_url' ) . '" target="_blank" class="download button">Download</a></p>' . $content;
    }
    return $content;
}
add_action( 'the_content', 'hmmc_download_link', 8 );
add_action( 'the_excerpt', 'hmmc_download_link', 8 );

/**
 * Add basic styles
 */
function hmmc_styles() {
    wp_enqueue_style( 'hmmc-custom-styles', plugin_dir_url( __FILE__ ) . 'hmmc-resources.css' );
}
add_action( 'wp_enqueue_scripts', 'hmmc_styles' );

/**
 * Shortcode to output category content
 * @param  array  $attributes shortcode parameters
 * @return string HTML output
 */
function hmmc_category_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'post_type'         => 'hmmc_resource',
        'posts_per_page'    => -1,
        'offset'            => NULL,
        'orderby'           => 'post_title',
        'order'             => 'ASC',
        'tax_terms'         => NULL,
    ), $attributes );

    $hmmc_query = new WP_Query( array_merge( $shortcode_attributes, array(
        'tax_query'         => array(
            'taxonomy'  => 'category',
            'field'     => 'slug',
            'terms'     => $shortcode_attributes['tax_terms'],
        ),
    )));

    if ( $hmmc_query->have_posts() ) {
        echo '<section class="hmmc-grid">';
        while ( $hmmc_query->have_posts() ) {
            $hmmc_query->the_post();

            echo '<article id="post-' . get_the_ID() . '" '; post_class(); echo '>';

            if ( has_post_thumbnail() ) {
                echo '<a href="' . get_field( 'download_url' ) . '" target="_blank" title="Download Resource">' . get_the_post_thumbnail( get_the_ID(), 'hmmc-m' ) . '</a>';
            }

            echo '<h2><a href="' . get_field( 'download_url' ) . '" target="_blank" title="Download Resource">' . get_the_title() . '</a></h2>';

            echo '<p>Categories: ';
            the_category( ', ', 'multiple' );
            echo '</p>';

            the_content();

            echo '</article>';
        }
        echo '</section>';
    }

    wp_reset_postdata();

    ob_start();

    return ob_get_clean();
}
add_shortcode( 'hmmc_resources', 'hmmc_category_shortcode' );

/**
 * Add custom image sizes
 */
function hmmc_image_sizes() {
    add_image_size( 'hmmc-sm', '450', '225', true );
    add_image_size( 'hmmc-m', '600', '300', true );
    add_image_size( 'hmmc-l', '900', '450', true );
}
add_action( 'after_setup_theme', 'hmmc_image_sizes' );

/**
 * Include resources in default tag/category archives
 * @param  object $query WP_Query object
 * @return object modified WP_Query object
 */
function hmmc_tax_archive( $query ) {
    if ( is_category() || is_tag() ) {
        $post_type = get_query_var( 'post_type' );
        if ( $post_type ) {
            $post_type = $post_type;
        } else {
            $post_type = array( 'post', 'hmmc_resource' );
            $query->set( 'orderby', 'post_title' );
            $query->set( 'order', 'ASC' );
            $query->set( 'posts_per_page', -1 );
        }
        $query->set( 'post_type', $post_type );
    }

    return $query;
}
add_filter('pre_get_posts', 'hmmc_tax_archive');
