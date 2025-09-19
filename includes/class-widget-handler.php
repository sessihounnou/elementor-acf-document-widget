<?php
if (!defined('ABSPATH')) {
    exit;
}

class EADW_Widget_Handler
{

    /**
     * Récupérer l'URL du document ACF
     */
    public static function get_acf_document_url($field_name, $post_id = null)
    {
        if (empty($field_name)) {
            return '';
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
     * Obtenir le titre du document
     */
    public static function get_document_title($title_field, $post_id = null)
    {
        if (empty($title_field)) {
            return get_the_title($post_id);
        }

        // Si c'est un groupe de champs
        if (strpos($title_field, ':') !== false) {
            list($group_name, $title_field) = explode(':', $title_field, 2);
            $group = get_field($group_name, $post_id);
            return isset($group[$title_field]) ? $group[$title_field] : get_the_title($post_id);
        }

        // Champ direct
        $title = get_field($title_field, $post_id);
        return $title ? $title : get_the_title($post_id);
    }

    /**
     * Récupérer l'URL depuis une balise dynamique
     */
    public static function get_dynamic_url($settings_key, $widget)
    {
        $value = $widget->get_settings($settings_key);

        // Vérifier si c'est une balise dynamique
        if (isset($value['dynamic']) && $value['dynamic']['active']) {
            $dynamic_tag = $value['dynamic'];
            if ($dynamic_tag['id'] === 'acf.field') {
                $field_name = $dynamic_tag['settings']['field'];
                return self::get_acf_document_url($field_name);
            }
        }

        // Valeur statique
        return $value;
    }

    /**
     * Récupérer le titre depuis une balise dynamique
     */
    public static function get_dynamic_title($settings_key, $widget)
    {
        $value = $widget->get_settings($settings_key);

        // Vérifier si c'est une balise dynamique
        if (isset($value['dynamic']) && $value['dynamic']['active']) {
            $dynamic_tag = $value['dynamic'];
            if ($dynamic_tag['id'] === 'acf.field') {
                $field_name = $dynamic_tag['settings']['field'];
                return self::get_document_title($field_name);
            }
        }

        // Valeur statique
        return $value;
    }
}
