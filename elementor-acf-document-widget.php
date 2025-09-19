<?php

/**
 * Plugin Name: Elementor ACF Document Widget
 * Plugin URI: https://sessihounnou.art
 * Description: Widget Elementor personnalisé pour afficher des documents ACF dans une iframe avec support des balises dynamiques.
 * Version: 1.0.0
 * Author: Sessi Hounnou
 * Author URI: https://sessihounnou.art
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
    // ✅ CORRECTION : Enregistrement sur plusieurs hooks pour plus de compatibilité
    add_action('elementor/widgets/register', 'eadw_register_widgets');
    add_action('elementor/frontend/after_register', 'eadw_register_widgets');
    add_action('init', 'eadw_register_widgets');

    function eadw_register_widgets($widgets_manager = null)
    {
        // Charger la classe du widget
        if (!class_exists('Elementor_ACF_Document_Widget')) {
            require_once(__DIR__ . '/includes/class-acf-document-widget.php');
        }

        // Enregistrer le widget
        if ($widgets_manager) {
            $widgets_manager->register(new \Elementor_ACF_Document_Widget());
        } else {
            // Fallback si pas de $widgets_manager
            add_action('elementor/widgets/register', function ($wm) {
                $wm->register(new \Elementor_ACF_Document_Widget());
            });
        }
    }

    // ✅ CORRECTION : Catégorie ajoutée plus tôt
    add_action('elementor/elements/categories_registered', 'eadw_add_category', 10);
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

    // ✅ NOUVEAU : Support des templates/modèles
    add_action('elementor/theme/register_locations', 'eadw_register_theme_locations');
    function eadw_register_theme_locations($elementor_theme_manager)
    {
        // Pas nécessaire pour ce widget, mais garde la compatibilité
    }

    // ✅ NOUVEAU : Debug pour vérifier l'enregistrement
    if (defined('WP_DEBUG') && WP_DEBUG) {
        add_action('wp_footer', 'eadw_debug_widget_registration');
        function eadw_debug_widget_registration()
        {
            if (current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $registered_widgets = \Elementor\Plugin::$instance->widgets_manager->get_widget_types();
                $widget_exists = isset($registered_widgets['acf-document-viewer']);
                error_log('EADW Debug: Widget ACF Document Viewer enregistré = ' . ($widget_exists ? 'OUI' : 'NON'));
            }
        }
    }
}
