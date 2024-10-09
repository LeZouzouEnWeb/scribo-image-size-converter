<?php


function isc_add_plugin_page()
{
    global $namePlugin;
    add_menu_page(
        __('Image Size Converter', $namePlugin), // Title
        __('Image Converter', $namePlugin),      // Menu Title
        'manage_options',       // Capability
        'image-size-converter', // Slug
        'isc_render_plugin_page', // Function to display the page
        'dashicons-admin-settings',
        60
    );
}


function isc_render_plugin_page()
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


function isc_settings_register()
{
    global $defaultSize, $namePlugin;
    // register_setting('settings_fields', 'settings_field', 'settings_fields_validate');
    register_setting(
        'settings_fields',
        'isc_image_sizes',
        [
            'type' => 'array',
            'sanitize_callback' => 'sanitize_image_sizes',
            'default' => $defaultSize
        ]
    );
    add_settings_section(
        'settings_section',
        '', //__('Paramètres', $namePlugin),
        '', // 'isc_settings_section_introduction',
        'settings_section'
    );
    add_settings_field(
        'settings_field_introduction',
        __('Set different image sizes', $namePlugin),
        'isc_settings_field_introduction_output',
        'settings_section',
        'settings_section'
    );
}


// function isc_settings_section_introduction()
// {
//     global $namePlugin;
//     echo __('Set different image sizes :', $namePlugin);
// }


function isc_settings_field_introduction_output()
{
    global $defaultSize;
    // Récupérer les tailles enregistrées ou définir des tailles par défaut
    $image_sizes = get_option('isc_image_sizes', $defaultSize);
?>
    <div id="image-sizes-container">
        <?php
        // Affichage des champs pour chaque taille
        $i = 0;
        foreach ($image_sizes as $index => $size) {
        ?>
            <div class="image-size-row" data-index="<?= $index; ?>">
                <input type="text" name="isc_image_sizes[]" value="<?= esc_attr($size); ?>" />
                <button type="button" class="remove-size-button">Supprimer</button>
            </div>
        <?php
        }
        ?>
    </div>
    <button type="button" id="add-size-button">Ajouter une taille</button>
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('image-sizes-container');

            // Ajout dynamique d'une nouvelle taille
            document.getElementById('add-size-button').addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.classList.add('image-size-row');
                newRow.innerHTML = `<input type="text" name="isc_image_sizes[]" placeholder="Ex: 800x800" />
                                    <button type="button" class="remove-size-button">Supprimer</button>`;
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



// Fonction pour nettoyer et valider les tailles d'images
function isc_sanitize_image_sizes($input)
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
