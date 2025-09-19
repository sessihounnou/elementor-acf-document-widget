<?php
if (!defined('ABSPATH')) {
    exit;
}

class EADW_Widget_Handler
{

    /**
     * Récupérer l'URL du document ACF - Version universelle
     */
    public static function get_acf_document_url($field_name, $post_id = null)
    {
        if (empty($field_name)) {
            return '';
        }

        // Si pas de post ID, essayer de récupérer le contexte actuel
        if ($post_id === null || $post_id === 0) {
            $post_id = self::get_current_post_id();
        }

        // Si c'est un groupe de champs (format "group_name:field_name")
        if (strpos($field_name, ':') !== false) {
            list($group_name, $field_name) = explode(':', $field_name, 2);
            $group = get_field($group_name, $post_id);
            return isset($group[$field_name]) ? $group[$field_name] : '';
        }

        // Champ direct
        $url = get_field($field_name, $post_id);
        return $url ? $url : '';
    }

    /**
     * Obtenir le titre du document - Version universelle
     */
    public static function get_document_title($title_field, $post_id = null)
    {
        if (empty($title_field)) {
            return self::get_current_post_title();
        }

        if ($post_id === null || $post_id === 0) {
            $post_id = self::get_current_post_id();
        }

        // Si c'est un groupe de champs
        if (strpos($title_field, ':') !== false) {
            list($group_name, $title_field) = explode(':', $title_field, 2);
            $group = get_field($group_name, $post_id);
            return isset($group[$title_field]) ? $group[$title_field] : self::get_current_post_title();
        }

        // Champ direct
        $title = get_field($title_field, $post_id);
        return $title ? $title : self::get_current_post_title();
    }

    /**
     * Récupérer le post ID courant dans TOUS les contextes
     */
    private static function get_current_post_id()
    {
        global $post;

        // 1. Contexte d'un post/page
        if ($post && isset($post->ID)) {
            return $post->ID;
        }

        // 2. Modèles/templates Elementor
        if (function_exists('elementor')) {
            $elementor_post_id = get_queried_object_id();
            if ($elementor_post_id) {
                return $elementor_post_id;
            }
        }

        // 3. Archive/requête courante
        global $wp_query;
        if ($wp_query && $wp_query->have_posts()) {
            $posts = $wp_query->posts;
            if (!empty($posts)) {
                return $posts[0]->ID;
            }
        }

        // 4. Page d'accueil
        if (is_front_page()) {
            $front_page_id = get_option('page_on_front');
            if ($front_page_id) {
                return $front_page_id;
            }
        }

        // 5. Dernier fallback
        return get_the_ID();
    }

    /**
     * Récupérer le titre du post courant
     */
    private static function get_current_post_title()
    {
        global $post;

        if ($post && isset($post->post_title)) {
            return $post->post_title;
        }

        // Fallback
        return get_the_title();
    }
}
