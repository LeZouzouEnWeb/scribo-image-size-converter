<?php

// // Hook pour ajouter une option à l'interface d'admin
// add_action('admin_menu', 'isc_add_plugin_page');

// function isc_add_plugin_page()
// {
//     add_menu_page(
//         'Image Size Converter', // Title
//         'Image Converter',      // Menu Title
//         'manage_options',       // Capability
//         'image-size-converter', // Slug
//         'isc_render_plugin_page' // Function to display the page
//     );
// }

function isc_render_plugin_page()
{
?>
    <div class="wrap">
        <h1>Image Size and Format Converter</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('isc_plugin_options_group');
            do_settings_sections('image-size-converter');
            submit_button();
            ?>
        </form>

        <h2>Image Sizes</h2>
        <div id="image-sizes-container">
            <?php
            // Récupérer les tailles enregistrées ou définir des tailles par défaut
            $image_sizes = get_option('isc_image_sizes', ['300x300', '600x600', '1024x1024']);

            // Affichage des champs pour chaque taille
            $i = 0;
            foreach ($image_sizes as $index => $size) {
            ?>
                <div class="image-size-row" data-index="<?php echo $index; ?>">
                    <input type="text" name="isc_image_sizes[]" value="<?php echo esc_attr($size); ?>" />
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

// Hook pour initialiser les paramètres
add_action('admin_init', 'isc_register_plugin_settings');

function isc_register_plugin_settings()
{
    add_settings_section(
        'isc_plugin_main_section',
        'Image Settings',
        null,
        'image-size-converter'
    );

    // On enregistre les tailles comme un tableau
    register_setting('isc_plugin_options_group', 'isc_image_sizes', [
        'type' => 'array',
        'sanitize_callback' => 'isc_sanitize_image_sizes',
        'default' => ['300x300', '600x600', '1024x1024']
    ]);
}

// Fonction pour nettoyer et valider les tailles d'images
function isc_sanitize_image_sizes($input)
{
    // Debug :
    error_log('Input received: ' . print_r($input, true));

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

// Hook pour convertir les images uploadées
add_filter('wp_handle_upload', 'isc_convert_uploaded_image', 10, 2);

function isc_convert_uploaded_image($uploaded_file, $context)
{
    // Récupère les tailles configurées sous forme de tableau
    $sizes = get_option('isc_image_sizes', ['300x300', '600x600', '1024x1024']);
    $file_path = $uploaded_file['file'];
    $image_editor = wp_get_image_editor($file_path);

    if (!is_wp_error($image_editor)) {
        foreach ($sizes as $size) {
            $size = explode('x', trim($size));
            if (count($size) === 2) {
                $image_editor->resize((int)$size[0], (int)$size[1], true);
                $new_file = $image_editor->generate_filename($size[0] . 'x' . $size[1]);
                $image_editor->save($new_file);
            }
        }

        // Convertir au format JPEG et PNG
        $jpeg_file = $image_editor->generate_filename('converted', null, 'jpg');
        $png_file = $image_editor->generate_filename('converted', null, 'png');
        $image_editor->save($jpeg_file, 'image/jpeg');
        $image_editor->save($png_file, 'image/png');
    }

    return $uploaded_file;
}
