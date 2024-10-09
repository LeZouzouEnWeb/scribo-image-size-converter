<?php

class ISC_Settings
{

    private $plugin_name;
    private $scribo_init;
    private $default_size;


    public function __construct($scribo_init)
    {
        $this->scribo_init = $scribo_init;

        $this->default_size = $this->scribo_init->ValConstant('default_Size');
        $this->plugin_name = $this->scribo_init->VarEntete('Text Domain');
        // DEBUG :
        // error_log("Default size : " . print_r($this->default_size, true));
        // error_log("Plugin name : " . $this->plugin_name);
        // error_log(__('Image Size Converter', $this->plugin_name));
        // Hooks
        add_action('admin_menu', [$this, 'add_plugin_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Ajouter la page du plugin dans le menu WordPress
     */
    public function add_plugin_page()
    {
        // DEBUG :
        // error_log(__('Image Size Converter', $this->plugin_name));
        add_menu_page(
            'Image Size Converter', // Title
            'Image Converter',      // Menu Title
            'manage_options',       // Capability
            'image-size-converter', // Slug
            [$this, 'render_plugin_page'] // Function to display the page

        );
    }

    /**
     * Rendre la page des paramètres du plugin
     */
    public function render_plugin_page()
    {
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
        echo '<form action="options.php" method="post" name="isc_settings">';
        echo '<div>';

        settings_fields('settings_fields');
        do_settings_sections('settings_section');
        submit_button();

        echo '</div>';
        echo '</form>';
    }

    /**
     * Enregistrement des paramètres du plugin
     */
    public function register_settings()
    {
        // Enregistrement du paramètre 'isc_image_sizes'
        register_setting(
            'settings_fields',
            'isc_image_sizes',
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize_image_sizes'],
                'default' => $this->default_size
            ]
        );

        // Ajout d'une section pour les paramètres
        add_settings_section(
            'settings_section',
            '', //__('Paramètres', $this->plugin_name),
            '', // 'isc_settings_section_introduction',
            'settings_section'
        );

        // Ajout du champ pour l'introduction des tailles d'images
        add_settings_field(
            'settings_field_introduction',
            __('Set different image sizes', $this->plugin_name),
            [$this, 'settings_field_introduction_output'],
            'settings_section',
            'settings_section'
        );
    }

    /**
     * Affichage des champs pour les tailles d'images
     */
    public function settings_field_introduction_output()
    {
        // Récupérer les tailles enregistrées ou définir des tailles par défaut
        $image_sizes = get_option('isc_image_sizes', $this->default_size);

        printf('<div id="image-sizes-container">');
        $i = 0;
        foreach ($image_sizes as $index => $size) {
            printf(
                '<div class="image-size-row" data-index="%s"><input type="text" name="isc_image_sizes[]" value="%s" /><button type="button" class="remove-size-button">%s</button></div>',
                esc_attr($index),
                esc_attr($size),
                __('Delete', $this->plugin_name)
            );
        }
        printf('</div>');
        printf('<button type="button" id="add-size-button">%s</button>', __('Add a size', $this->plugin_name));


        // Script pour gérer l'ajout et la suppression des tailles d'images
        $this->enqueue_js();
    }

    /**
     * Valider et nettoyer les tailles d'images
     */
    public function sanitize_image_sizes($input)
    {
        $sanitized = [];
        if (is_array($input)) {
            foreach ($input as $size) {
                // Validation simple pour vérifier le format [largeur]x[hauteur]
                if (preg_match('/^\d+x\d+$/', $size)) {
                    $sanitized[] = $size;
                }
            }
        }
        return $sanitized;
    }

    /**
     * Enqueue JavaScript pour gérer la dynamique des tailles d'images
     */
    private function enqueue_js()
    {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('image-sizes-container');

                // Ajout dynamique d'une nouvelle taille
                document.getElementById('add-size-button').addEventListener('click', function() {
                    const newRow = document.createElement('div');
                    newRow.classList.add('image-size-row');
                    newRow.innerHTML =
                        `<input type="text" name="isc_image_sizes[]" placeholder="Ex: 800x800" />
                                        <button type="button" class="remove-size-button"><?= __('Delete', $this->plugin_name); ?></button>`;
                    container.appendChild(newRow);
                });

                // Suppression d'une taille
                container.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('remove-size-button')) {
                        e.target.parentElement.remove();
                    }
                });
            });
        </script>
<?php
    }
}
