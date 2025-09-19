<?php
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class Elementor_ACF_Document_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'acf-document-viewer';
    }

    public function get_title()
    {
        return __('ACF Document Viewer', 'elementor-acf-document-widget');
    }

    public function get_icon()
    {
        return 'eicon-document-file';
    }

    // ✅ CORRECTION : Disponible dans TOUTES les catégories
    public function get_categories()
    {
        return [
            'general',      // Catégorie générale (partout)
            'basic',        // Catégorie basique
            'pro',          // Si Elementor Pro
            'acf-documents' // Notre catégorie custom
        ];
    }

    public function get_keywords()
    {
        return ['acf', 'document', 'pdf', 'iframe', 'file', 'viewer', 'custom fields'];
    }

    // ✅ CORRECTION : Fonctionne partout (modèles, archives, etc.)
    public function get_panel_template()
    {
        return '';
    }

    protected function register_controls()
    {

        // CONTENT TAB
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Contenu', 'elementor-acf-document-widget'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // ✅ NOUVEAU : Choix du contexte (Post/Page/Archive/Modèle)
        $this->add_control(
            'source_context',
            [
                'label' => __('Contexte de source', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'current',
                'options' => [
                    'current' => __('Post/Page courant', 'elementor-acf-document-widget'),
                    'specific_post' => __('Post/Page spécifique (ID)', 'elementor-acf-document-widget'),
                    'archive' => __('Archive/Taxonomie courante', 'elementor-acf-document-widget'),
                    'custom' => __('URL personnalisée', 'elementor-acf-document-widget'),
                ],
                'description' => __('Choisissez d\'où récupérer les données ACF.', 'elementor-acf-document-widget'),
            ]
        );

        $this->add_control(
            'specific_post_id',
            [
                'label' => __('ID du Post/Page', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => '123',
                'description' => __('ID numérique du post/page spécifique (ex: 123). Laissez vide pour utiliser le courant.', 'elementor-acf-document-widget'),
                'condition' => [
                    'source_context' => 'specific_post',
                ],
            ]
        );

        // ✅ NOUVEAU : Support des balises dynamiques pour l'ID
        $this->add_control(
            'dynamic_post_id',
            [
                'label' => __('ID dynamique', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Oui', 'elementor-acf-document-widget'),
                'label_off' => __('Non', 'elementor-acf-document-widget'),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'source_context' => 'specific_post',
                ],
            ]
        );

        $this->add_control(
            'document_source',
            [
                'label' => __('Source du document', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'acf_dynamic',
                'options' => [
                    'acf_dynamic' => __('Balise dynamique ACF', 'elementor-acf-document-widget'),
                    'acf_static' => __('Champ ACF statique', 'elementor-acf-document-widget'),
                    'post_meta' => __('Meta du post courant', 'elementor-acf-document-widget'),
                ],
                'description' => __('Choisissez comment récupérer l\'URL du document.', 'elementor-acf-document-widget'),
                'condition' => [
                    'source_context!' => 'custom',
                ],
            ]
        );

        // Balise dynamique ACF
        $this->add_control(
            'document_field',
            [
                'label' => __('Champ Document', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => 'documents:document',
                'placeholder' => 'documents:document',
                'description' => __('Format: nom_du_groupe:nom_du_champ (ex: documents:document)', 'elementor-acf-document-widget'),
                'condition' => [
                    'document_source' => 'acf_static',
                    'source_context!' => 'custom',
                ],
            ]
        );

        // ✅ NOUVEAU : Support dynamique pour le champ statique
        $this->add_control(
            'dynamic_document_field',
            [
                'label' => __('Champ dynamique', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Oui', 'elementor-acf-document-widget'),
                'label_off' => __('Non', 'elementor-acf-document-widget'),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'document_source' => 'acf_static',
                    'source_context!' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'document_title_field',
            [
                'label' => __('Champ Titre (optionnel)', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => 'documents:titre_du_document',
                'placeholder' => 'documents:titre_du_document',
                'description' => __('Format: nom_du_groupe:nom_du_champ. Laissez vide pour utiliser le titre du post.', 'elementor-acf-document-widget'),
                'condition' => [
                    'source_context!' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'custom_document_url',
            [
                'label' => __('URL du document', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::URL,
                'placeholder' => 'https://example.com/document.pdf',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'source_context' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Afficher le titre', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Oui', 'elementor-acf-document-widget'),
                'label_off' => __('Non', 'elementor-acf-document-widget'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_actions',
            [
                'label' => __('Afficher les boutons d\'action', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Oui', 'elementor-acf-document-widget'),
                'label_off' => __('Non', 'elementor-acf-document-widget'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'iframe_height',
            [
                'label' => __('Hauteur iframe', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 600,
                ],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eadw-iframe' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // STYLE TAB (identique à avant)
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __('Style du Titre', 'elementor-acf-document-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Couleur', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eadw-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'selector' => '{{WRAPPER}} .eadw-title',
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => __('Espacement', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eadw-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'iframe_style_section',
            [
                'label' => __('Style de l\'iframe', 'elementor-acf-document-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'iframe_border',
                'label' => __('Bordure', 'elementor-acf-document-widget'),
                'selector' => '{{WRAPPER}} .eadw-iframe-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'iframe_shadow',
                'label' => __('Ombre', 'elementor-acf-document-widget'),
                'selector' => '{{WRAPPER}} .eadw-iframe-container',
            ]
        );

        $this->add_responsive_control(
            'iframe_margin',
            [
                'label' => __('Espacement extérieur', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eadw-iframe-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'actions_style_section',
            [
                'label' => __('Style des Boutons', 'elementor-acf-document-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_actions' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'actions_alignment',
            [
                'label' => __('Alignement', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Gauche', 'elementor-acf-document-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Centre', 'elementor-acf-document-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Droite', 'elementor-acf-document-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .eadw-actions' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_view_color',
            [
                'label' => __('Couleur bouton "Voir"', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eadw-btn-view' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_actions' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_download_color',
            [
                'label' => __('Couleur bouton "Télécharger"', 'elementor-acf-document-widget'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_SUCCESS,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eadw-btn-download' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_actions' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    // ✅ CORRECTION : Fonction améliorée pour TOUS les contextes
    protected function get_post_id_for_context($settings)
    {
        $context = $settings['source_context'];

        switch ($context) {
            case 'current':
                // Post/Page courant
                global $post;
                return $post ? $post->ID : get_the_ID();

            case 'specific_post':
                // Post spécifique par ID
                $post_id = '';

                // Si ID dynamique activé
                if ($settings['dynamic_post_id'] === 'yes') {
                    $dynamic_value = $this->get_dynamic_post_id();
                    $post_id = $dynamic_value ? intval($dynamic_value) : '';
                } else {
                    $post_id = !empty($settings['specific_post_id']) ? intval($settings['specific_post_id']) : '';
                }

                return $post_id;

            case 'archive':
                // Archive courante (premier post de la requête)
                global $wp_query;
                if ($wp_query->have_posts()) {
                    $posts = $wp_query->posts;
                    return !empty($posts) ? $posts[0]->ID : 0;
                }
                return 0;

            default:
                return get_the_ID();
        }
    }

    // ✅ NOUVEAU : Récupérer ID depuis balise dynamique
    private function get_dynamic_post_id()
    {
        // Cette méthode utilise le système dynamique d'Elementor
        // Pour simplifier, on utilise une approche ACF dynamique
        $dynamic_field = $this->get_settings('specific_post_id');

        if (is_array($dynamic_field) && isset($dynamic_field['dynamic']) && $dynamic_field['dynamic']['active']) {
            $dynamic_tag = $dynamic_field['dynamic'];
            if ($dynamic_tag['id'] === 'post.id') {
                global $post;
                return $post ? $post->ID : 0;
            }
        }

        return 0;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $document_url = '';
        $document_title = '';

        // ✅ Récupérer le bon post ID selon le contexte
        $post_id = $this->get_post_id_for_context($settings);

        // Debug (à supprimer en production)
        if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('administrator')) {
            error_log('EADW Debug - Post ID: ' . $post_id . ', Context: ' . $settings['source_context']);
        }

        // Récupérer l'URL du document
        switch ($settings['source_context']) {
            case 'custom':
                $document_url = $settings['custom_document_url']['url'];
                break;

            case 'acf_dynamic':
                // Utiliser la balise dynamique ACF native d'Elementor
                $dynamic_url = $this->get_dynamic_document_url();
                $document_url = $dynamic_url ? $dynamic_url : '';
                break;

            default:
                // ACF statique ou meta
                if ($settings['document_source'] === 'acf_static') {
                    $document_url = EADW_Widget_Handler::get_acf_document_url($settings['document_field'], $post_id);
                } elseif ($settings['document_source'] === 'post_meta') {
                    $document_url = get_post_meta($post_id, $settings['document_field'], true);
                }

                // Titre du document
                $document_title = EADW_Widget_Handler::get_document_title($settings['document_title_field'], $post_id);
                break;
        }

        // Si pas d'URL, afficher un message
        if (empty($document_url)) {
            echo '<div class="eadw-no-document">';
            echo '<p>' . esc_html__('Aucun document disponible', 'elementor-acf-document-widget') . '</p>';
            if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('administrator')) {
                echo '<small style="color: #999;">Debug: Post ID = ' . intval($post_id) . '</small>';
            }
            echo '</div>';
            return;
        }

        // Classes CSS
        $wrapper_classes = ['eadw-widget'];
        if ($settings['show_title'] === 'yes') {
            $wrapper_classes[] = 'has-title';
        }
        if ($settings['show_actions'] === 'yes') {
            $wrapper_classes[] = 'has-actions';
        }

        $this->add_render_attribute('wrapper', 'class', implode(' ', $wrapper_classes));
?>

        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
            <?php if ($settings['show_title'] === 'yes') : ?>
                <div class="eadw-header">
                    <h3 class="eadw-title"><?php echo esc_html($document_title); ?></h3>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_actions'] === 'yes') : ?>
                <div class="eadw-actions">
                    <a href="<?php echo esc_url($document_url); ?>"
                        target="_blank"
                        class="eadw-btn eadw-btn-view"
                        rel="noopener noreferrer">
                        <?php esc_html_e('👁️ Voir en grand', 'elementor-acf-document-widget'); ?>
                    </a>
                    <a href="<?php echo esc_url($document_url); ?>"
                        download
                        class="eadw-btn eadw-btn-download">
                        <?php esc_html_e('⬇️ Télécharger', 'elementor-acf-document-widget'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="eadw-iframe-container">
                <iframe class="eadw-iframe"
                    src="<?php echo esc_url($document_url); ?>"
                    width="100%"
                    title="<?php echo esc_attr($document_title); ?>">
                    <p><?php esc_html_e('Votre navigateur ne supporte pas les iframes.', 'elementor-acf-document-widget'); ?>
                        <a href="<?php echo esc_url($document_url); ?>" target="_blank"><?php esc_html_e('Cliquez ici pour voir le document', 'elementor-acf-document-widget'); ?></a>.
                    </p>
                </iframe>
            </div>

            <?php if ($settings['show_actions'] === 'yes') : ?>
                <div class="eadw-footer">
                    <small><?php echo esc_html(basename($document_url)); ?></small>
                </div>
            <?php endif; ?>
        </div>

    <?php
        $this->print_styles();
    }

    // ✅ NOUVEAU : Récupérer URL depuis balise dynamique
    private function get_dynamic_document_url()
    {
        // Cette méthode utilise le système natif d'Elementor pour les balises dynamiques
        // On crée un contrôle temporaire pour récupérer la valeur dynamique
        $dynamic_value = $this->get_settings('custom_document_url');

        if (is_array($dynamic_value) && isset($dynamic_value['dynamic']) && $dynamic_value['dynamic']['active']) {
            // La balise dynamique est active, on laisse Elementor la traiter
            // Pour ACF, on utilise le système natif
            $dynamic_tag = $dynamic_value['dynamic'];

            if ($dynamic_tag['id'] === 'acf.field') {
                $field_name = $dynamic_tag['settings']['field'];
                $post_id = $this->get_post_id_for_context($this->get_settings_for_display());
                return EADW_Widget_Handler::get_acf_document_url($field_name, $post_id);
            }
        }

        return $dynamic_value['url'] ?? '';
    }

    private function print_styles()
    {
        $settings = $this->get_settings_for_display();
    ?>
        <style>
            .eadw-widget {
                --eadw-primary-color: #0073aa;
                --eadw-success-color: #28a745;
                --eadw-border-radius: 8px;
            }

            .eadw-header {
                background: linear-gradient(135deg, var(--eadw-primary-color), #005a87);
                color: white;
                padding: 15px 20px;
                margin: 0;
            }

            .eadw-title {
                margin: 0;
                font-size: 20px;
                font-weight: 600;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            .eadw-actions {
                padding: 15px 20px;
                background: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }

            .eadw-btn {
                display: inline-block;
                padding: 8px 16px;
                margin: 0 5px 5px 0;
                border-radius: var(--eadw-border-radius);
                text-decoration: none;
                font-weight: 500;
                font-size: 14px;
                transition: all 0.2s ease;
                text-align: center;
                min-width: 120px;
            }

            .eadw-btn-view {
                background: var(--eadw-primary-color);
                color: white;
            }

            .eadw-btn-view:hover {
                background: #005a87;
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0, 115, 170, 0.3);
            }

            .eadw-btn-download {
                background: var(--eadw-success-color);
                color: white;
            }

            .eadw-btn-download:hover {
                background: #218838;
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
            }

            .eadw-iframe-container {
                position: relative;
                background: white;
                border-radius: 0 0 var(--eadw-border-radius) var(--eadw-border-radius);
                overflow: hidden;
            }

            .eadw-iframe {
                display: block;
                border: none;
                width: 100%;
            }

            .eadw-footer {
                padding: 10px 20px;
                background: #f8f9fa;
                text-align: center;
                color: #6c757d;
                font-size: 12px;
            }

            .eadw-no-document {
                text-align: center;
                padding: 40px 20px;
                color: #999;
                background: #f8f9fa;
                border: 2px dashed #dee2e6;
                border-radius: var(--eadw-border-radius);
            }

            .eadw-no-document::before {
                content: "📂";
                display: block;
                font-size: 48px;
                margin-bottom: 15px;
                opacity: 0.5;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .eadw-actions {
                    text-align: center;
                }

                .eadw-btn {
                    display: block;
                    width: 100%;
                    max-width: 200px;
                    margin: 5px auto;
                }

                .eadw-iframe {
                    height: 400px !important;
                }
            }
        </style>
<?php
    }
}
