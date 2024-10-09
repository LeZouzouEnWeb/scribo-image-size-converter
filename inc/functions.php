<?php

function VarEntete(string $nameEntete)
{
    global $plugin_file;
    // Définir les en-têtes personnalisés à récupérer
    $headerEntete = str_replace(" ", "", ucwords($nameEntete));
    $plugin_headers = array(
        $headerEntete => $nameEntete,
    );

    // Récupérer les données du plugin
    $plugin_data = get_file_data($plugin_file, $plugin_headers);
    // Récupérer la valeur de De l'en-tête
    $text_domain_min = $plugin_data[$headerEntete];

    // Afficher ou utiliser la valeur de De l'en-tête
    return (!empty($text_domain_min)) ? $text_domain_min : "";
}


// Fonction pour définir les constantes si elles ne le sont pas déjà
function define_isc_constant($constant_name, $value)
{
    $myConst = strtoupper(VarEntete('Text Domain min') . "_" . $constant_name);
    if (!defined($myConst)) {
        define($myConst, $value);
    } else {
        throw new Exception("La constante $myConst est déjà définie.");
    }
}

/**
 * Fonction pour récupérer la valeur de la constante
 *
 * @param string $constant_name Nom de la constante (sans les guillemets)
 * @throws Exception Si la constante n'existe pas
 */
function Constante($constant_name)
{
    // Vérifie si la constante est définie
    $constant_rename = strtoupper(VarEntete('Text Domain min') . "_" . $constant_name);

    if (defined($constant_rename)) {
        // Récupère la valeur de la constante
        $const_value = constant($constant_rename);
        // Renvoi la valeur de la constante
        return $const_value;
    } else {
        throw new Exception("La constante $constant_rename n'est pas définie.");
    }
}

/**
 * Fonction pour inclure des fichiers en fonction des constantes
 *
 * @param string $constant_name Nom de la constante (sans les guillemets)
 * @param string $file_path Chemin relatif à partir de la constante
 * @throws Exception Si la constante n'existe pas
 */
function RequireFile($constant_name, $file_path)
{
    // Récupère la valeur de la wp_common_block_scripts_and_styles(  )
    $folder_path = Constante($constant_name);

    // Construit le chemin complet du fichier
    $full_path = $folder_path . $file_path;

    // Vérifie si le fichier existe avant de l'inclure
    if (file_exists($full_path)) {
        require_once $full_path;
    } else {
        throw new Exception("Le fichier $full_path n'existe pas.");
    }
}



function CallConstante($var_constants)
{
    // Vérification de la présence des clés requises
    $required_keys = ['NAME', 'VERSION', 'DIR_PATH', 'INC_PATH', 'ADMIN_PATH', 'URL_PATH', 'ASSETS_PATH'];
    foreach ($required_keys as $key) {
        if (!isset($var_constants[$key])) {
            throw new Exception("La clé '$key' est manquante dans \$var_constants.");
            return; // Sortie de la fonction si une clé est manquante
        }
    }

    // Chemins absolus
    $uri_dir = $var_constants['DIR_PATH'];
    // Chemins relatifs
    $url_dir = $var_constants['URL_PATH'];
    // Définition des constantes de base
    $constants_to_define = [
        'NAME' => $var_constants['NAME'],
        'VERSION' => $var_constants['VERSION'],
        'URI_DIR' => $uri_dir,
        'URI_INC' => $uri_dir . "/" . $var_constants['INC_PATH'],
        'URI_ADMIN' => $uri_dir . "/" . $var_constants['ADMIN_PATH'],
        'URL_DIR' => $url_dir,
        'URL_INC' => $url_dir . "/" . $var_constants['INC_PATH'],
        'URL_JS' => $url_dir . "/" . $var_constants['ASSETS_PATH'] . '/js',
        'URL_ADMIN' => $url_dir . "/" . $var_constants['ADMIN_PATH'],
    ];

    // Boucle pour définir les constantes
    foreach ($constants_to_define as $constant_name => $value) {
        define_isc_constant($constant_name, $value);
    }
}
