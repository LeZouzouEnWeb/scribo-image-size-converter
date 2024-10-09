<?php

// Hook pour convertir les images uploadées
add_filter('wp_handle_upload', 'convert_uploaded_image', 10, 2);

function isc_convert_uploaded_image($uploaded_file, $context)
{
    // Récupère les tailles configurées sous forme de tableau
    $sizes = get_option('image_sizes', ['300x300', '600x600', '1024x1024']);
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
