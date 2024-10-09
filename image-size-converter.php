<?php

/*
 * Plugin Name:       Convertisseur de taille et de format d'image
 * Plugin URI:        https://scribo-images-size-converter.corbisier.fr
 * Update URI:        https://www.corbisier.fr/wordpress/
 * Description:       Plugin pour choisir la taille des images et convertir les images téléchargées en plusieurs formats.
 * Author:            Eric CORBISIER
 * Author URI:        https://corbisier.fr
 * Version:           1.02
 * Requires at least: 6.3
 * Requires PHP:      8
 * Tags:              images, photos, lescorbycats, scribo
 * Text Domain:       scribo-images-size-converter
 * Text Domain min:   isc
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 */


defined('ABSPATH') or die();

$plugin_file = __FILE__;

require_once plugin_dir_path($plugin_file) . 'inc/functions.php';


// Définition des constantes
$abrPlugin = varEntete('Text Domain min');
$namePlugin = VarEntete('Text Domain');
$defaultSize = array('300x300', '600x600', '1024x1024');

// Tableau associatif pour regrouper les constantes
$isc_constants = array(
    'NAME' => 'scribo-smtp',
    'VERSION' => '1.0.17',
    'CSS_VERSION' => '1.1.20',
    'JS_VERSION' => '1.1.07',
    'DIR_PATH' => plugin_dir_path($plugin_file),
    'URL_PATH' => plugin_dir_url($plugin_file),
    'INC_PATH' => 'inc',
    'ADMIN_PATH' => 'admin',
    'ASSETS_PATH' => 'assets'
);

CallConstante($isc_constants);

// Inclusion des fichiers nécessaires
// RequireFile('URI_INC', '/class_functions.php');



// Chargement des fichiers d'admin si dans l'interface admin
if (is_admin()) {
    RequireFile('URI_ADMIN', '/inc/functions.php');
    RequireFile('URI_ADMIN', '/admin.php');
} else {
    RequireFile('URI_DIR', '/load.php');
}
