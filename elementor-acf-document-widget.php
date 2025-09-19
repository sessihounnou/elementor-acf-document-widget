<?php

/**
 * Plugin Name: Elementor ACF Document Widget
 * Plugin URI: https://example.com/elementor-acf-document-widget
 * Description: Widget Elementor personnalisé pour afficher des documents ACF dans une iframe avec support des balises dynamiques.
 * Version: 1.0.0
 * Author: Grok Assistant
 * Author URI: https://x.ai
 * License: GPL-2.0+
 * Text Domain: elementor-acf-document-widget
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Vérifier les dépendances
function eadw_check_dependencies()
{
    $has_elementor = class_exists('Elementor\Plugin');
    $has_acf = function_exists('get_field');

    if (!$has_elementor) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>Elementor ACF Document Widget</strong> nécessite <strong>Elementor</strong> pour fonctionner.</p></div>';
        });
        return false;
    }

    if (!$has_acf) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>Elementor ACF Document Widget</strong> nécessite <strong>Advanced Custom Fields (ACF)</strong> pour fonctionner.</p></div>';
        });
        return false;
    }

    return true;
}

// Initialiser le plugin
if (eadw_check_dependencies()) {
    // Charger les fichiers nécessaires
    require_once(__DIR__ . '/includes/class-widget-handler.php');

    // Initialiser le widget
    add_action('elementor/widgets/register', 'eadw_register_widgets');
    function eadw_register_widgets($widgets_manager)
    {
        require_once(__DIR__ . '/includes/class-acf-document-widget.php');
        $widgets_manager->register(new \Elementor_ACF_Document_Widget());
    }

    // Ajouter la catégorie
    add_action('elementor/elements/categories_registered', 'eadw_add_category');
    function eadw_add_category($elements_manager)
    {
        $elements_manager->add_category(
            'acf-documents',
            [
                'title' => __('ACF Documents', 'elementor-acf-document-widget'),
                'icon' => 'fa fa-file-pdf-o',
            ]
        );
    }
}
