<?php

class ImageConverter
{
    private $scribo_init;
    private $sizes;

    public function __construct($scribo_init)
    {
        $this->scribo_init = $scribo_init;
        $this->sizes = get_option('image_sizes', $this->scribo_init->ValConstant('default_Size'));

        // debug :
        error_log("Start Upload");

        // Hook pour convertir les images uploadées
        add_filter('wp_handle_upload', [$this, 'convert_uploaded_image'], 10, 2);
    }

    /**
     * Convertir l'image uploadée aux différentes tailles et formats.
     *
     * @param array $uploaded_file
     * @param string $context
     * @return array
     */
    public function convert_uploaded_image($uploaded_file, $context)
    {

        // debug :
        error_log("Upload file : " . $uploaded_file['file']);
        $file_path = $uploaded_file['file'];
        $image_editor = wp_get_image_editor('http://localhost:8090/wp-content/uploads/2024/10/logo-corbycats_logo_min_v2.23-2.png'); //$file_path);
        // debug :
        error_log("Error editor : " . is_wp_error($image_editor));
        if (!is_wp_error($image_editor)) {
            foreach ($this->sizes as $size) {
                $this->resize_image($image_editor, $size);
            }

            // Convertir au format JPEG et PNG
            $this->convert_image_formats($image_editor);
        } else {

            $error_string = $image_editor->get_error_message();
            // debug :
            error_log("Error editor : " . $error_string);
        }


        return $uploaded_file;
    }

    /**
     * Redimensionner l'image à une taille spécifique.
     *
     * @param object $image_editor
     * @param string $size
     * @return void
     */
    private function resize_image($image_editor, $size)
    {
        $size = explode('x', trim($size));
        if (count($size) === 2) {
            // debug :
            error_log("Size : " . (int)$size[0] . " x " . (int)$size[1]);
            $image_editor->resize((int)$size[0], (int)$size[1], true);
            $new_file = $image_editor->generate_filename($size[0] . 'x' . $size[1]);
            // debug :
            error_log("Newfile : " . $new_file);
            $image_editor->save($new_file);
        }
    }

    /**
     * Convertir l'image en différents formats (JPEG, PNG, etc.).
     *
     * @param object $image_editor
     * @return void
     */
    private function convert_image_formats($image_editor)
    {
        $formats = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        foreach ($formats as $extension => $mime_type) {
            $file = $image_editor->generate_filename('converted', null, $extension);
            $result = $image_editor->save($file, $mime_type);

            if (is_wp_error($result)) {
                error_log("Erreur lors de la conversion en $extension: " . $result->get_error_message());
            } else {
                error_log("Image convertie avec succès en $extension: $file");
            }
        }
    }
}
