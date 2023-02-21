<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register theme defaults.
add_action('after_setup_theme', static function () {
    show_admin_bar(false);

    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');

    register_nav_menus([
        'navigation' => __('Navigation'),
    ]);
});

// Register scripts and styles.
add_action('wp_enqueue_scripts', static function () {
    $manifestPath = get_theme_file_path('assets/manifest.json');

    if (wp_get_environment_type() === 'local' && is_array(wp_remote_get('http://localhost:5173/'))) {
        wp_enqueue_script('vite', 'http://localhost:5173/@vite/client', [], null);
        wp_enqueue_script('wordplate', 'http://localhost:5173/resources/js/index.js', [], null);
    }
    if (file_exists($manifestPath) && (wp_get_environment_type() === 'production' || !is_array(wp_remote_get('http://localhost:5173/')))) {
        $manifest = json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        wp_enqueue_script('wordplate', get_theme_file_uri('assets/' . $manifest['resources/js/index.js']['file']), [], null);
        wp_enqueue_style('wordplate', get_theme_file_uri('assets/' . $manifest['resources/js/index.css']['file']), [], null);
    }
});

// Load scripts as modules.
add_filter('script_loader_tag', static function (string $tag, string $handle, string $src) {
    if (in_array($handle, ['vite', 'wordplate'])) {
        return '<script type="module" src="' . esc_url($src) . '" defer></script>';
    }

    return $tag;
}, 10, 3);

// Remove admin menu items.
add_action('admin_init', static function () {
    remove_menu_page('edit-comments.php'); // Comments
});

// Remove admin toolbar menu items.
add_action('admin_bar_menu', static function (WP_Admin_Bar $menu) {
    $menu->remove_node('comments');  // Comments
    $menu->remove_node('customize'); // Customize
    $menu->remove_node('themes');    // Themes
    $menu->remove_node('updates');   // Updates
    $menu->remove_node('wp-logo');   // WordPress Logo
}, 999);

// Remove admin dashboard widgets.
add_action('wp_dashboard_setup', static function () {
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'], $wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health'], $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'], $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']); // Activity
});

// Add custom login form logo.
add_action('login_head', static function () {
    $url = get_theme_file_uri('favicon.svg');
    $width = 200;

    $styles = [
        sprintf('background-image: url(%s);', $url), sprintf('width: %dpx;', $width), 'background-position: center;',
        'background-size: contain;',
    ];

    echo sprintf('<style> .login h1 a { %s } </style>', implode('', $styles));
});

/* Custom functions.php commence ici */

add_theme_support('menus');

add_action('admin_menu', 'my_remove_admin_menus');

add_theme_support('title-tag');
add_theme_support('description');

function my_remove_admin_menus(): void
{
    remove_menu_page('edit-comments.php');
}

// Removes from post and pages
add_action('init', 'remove_comment_support', 100);

function remove_comment_support(): void
{
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
}

// Removes from admin bar
function mytheme_admin_bar_render(): void
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}

add_action('wp_before_admin_bar_render', 'mytheme_admin_bar_render');

add_theme_support('post-thumbnails');

// Update CSS within in Admin
add_action('admin_enqueue_scripts', static function () {
    wp_enqueue_script('admin-styles', 'https://cdn.tailwindcss.com');
    wp_enqueue_script('admin-tailwind', get_template_directory_uri() . '/admin.js');
});

// ADD image size

// add options page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page();
}

function layout_get($content): void
{
    foreach ($content as $layout) {
        foreach ($layout['layouts'] as $value) {
            // prevent acf automatic style enqueue
            if (function_exists('acf_slugify')) :
                $handle = acf_slugify($layout['name']) . '-layout-' . acf_slugify($value['name']);
                wp_register_style($handle, false);
                wp_register_script($handle, false);
            endif;
        }
    }
}

// Enlever les styles acf extended en front
add_action('wp_enqueue_scripts', static function () {
    if (function_exists('acf_get_fields')) :
        layout_get(acf_get_fields('group_63bd314b2704f'));
        layout_get(acf_get_fields('group_63b586eac7c60'));
    endif;
    if (!is_user_logged_in()) {
        // CSS
        wp_register_style('admin-bar', false);
        wp_register_style('classic-theme-styles', false);
        wp_register_style('wp-block-library', false);
        wp_deregister_style('wp-block-library');
        wp_deregister_style('admin-bar');
        wp_deregister_style('classic-theme-styles');
        // Javascript
        wp_register_script('wp-emoji', false);
        wp_deregister_script('wp-emoji');
    }
});

/* Autoriser les fichiers SVG */
add_filter('upload_mimes', static function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';

    return $mimes;
});

// Miniatures
include "inc/miniatures.php";

// SMTP email settings
add_action('phpmailer_init', static function ($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.gmail.com';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = '465';
    $phpmailer->Username = '';
    $phpmailer->Password = '';
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->FromName = 'Template';
    $phpmailer->From = '';
});

function get_image_path($image): string
{
    if (is_array(wp_remote_get('http://localhost:5173/'))) {
        $hrefImages = "http://localhost:5173/resources/static/img/$image";
    } else {
        $hrefImages = get_theme_file_uri("assets/img/$image");
    }

    return $hrefImages;
}
