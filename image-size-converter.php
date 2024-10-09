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

// Vérifie si la classe est déjà définie
if (!class_exists('ScriboInitChecker')) {
    // Chemin vers le fichier de la classe
    require_once plugin_dir_path(__FILE__) . 'inc/scriboInitChecker.php';
}
// Instanciation de la classe pour que les hooks soient actifs
new ScriboInitChecker(__FILE__);



add_action('plugins_loaded', 'load_other_plugin_before_mine', 1);

function load_other_plugin_before_mine()
{

    if (!class_exists('ScriboInit')) {
        // Charger l'autre plugin manuellement
        include_once(WP_PLUGIN_DIR . '/scribo-init/scribo-init.php');
    }


    // if (class_exists('ScriboInit')) {

    // DEBUG:
    // error_log("Plugin file ISC : " . __FILE__);
    // Utiliser la classe
    $scribo_init = new ScriboInit(__FILE__);


    $scribo_init->DefineConstant('scribo_init', $scribo_init);

    // Définition des constantes
    $scribo_init->DefineConstant('Plugin_abr', $scribo_init->VarEntete('Text Domain min'));
    $scribo_init->DefineConstant('Plugin_name', $scribo_init->VarEntete('Text Domain'));
    $scribo_init->DefineConstant('default_Size', array('300x300', '600x600', '1024x1024'));
    $scribo_init->DefineConstant('plugin_file', __FILE__);

    // Exemple d'utilisation
    try {
        $scribo_init->CallConstante([
            'NAME' => 'scribo-smtp',
            'VERSION' => '1.0.17',
            'CSS_VERSION' => '1.1.20',
            'JS_VERSION' => '1.1.07',
            'DIR_PATH' => plugin_dir_path(__FILE__),
            'URL_PATH' => plugin_dir_url(__FILE__),
            'INC_PATH' => 'inc',
            'ADMIN_PATH' => 'admin',
            'ASSETS_PATH' => 'assets',
        ]);
    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    // Inclusion des fichiers nécessaires
    // $scribo_init->RequireFile('URI_INC', '/class_functions.php');



    // Chargement des fichiers d'admin si dans l'interface admin
    if (is_admin()) {
        $scribo_init->RequireFile('URI_ADMIN', '/inc/isc_settings.php');
        $scribo_init->RequireFile('URI_ADMIN', '/admin.php');
        // } else {
        $scribo_init->RequireFile('URI_DIR', '/load.php');
    }
    // }
}