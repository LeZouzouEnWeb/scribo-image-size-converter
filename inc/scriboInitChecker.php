<?php
class ScriboInitChecker
{

    const TRANSIENT_NAME = 'admin_notice';
    private $plugin_file;

    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
        // Ajouter les hooks nécessaires
        add_action('admin_init', [$this, 'check_scribo_init_class']);
        add_action('admin_notices', [$this, 'show_admin_notice']);
        add_action('activate_plugin', [$this, 'check_scribo_init_class'], 10, 2);
    }

    /**
     * Vérifie si la classe ScriboInit existe et désactive le plugin si nécessaire.
     */
    public function check_scribo_init_class($plugin = '', $network_wide = false)
    {
        if (!class_exists('ScriboInit')) {
            deactivate_plugins(plugin_basename($this->plugin_file)); // Désactive le plugin
            unset($_GET['activate']);

            // Ajoute un message d'alerte temporaire en utilisant un transient
            set_transient(self::TRANSIENT_NAME, true, 5); // Message temporaire qui expire après 5 secondes
        }
    }

    /**
     * Affiche le message d'alerte dans l'admin si ScriboInit n'est pas activé.
     */
    public function show_admin_notice()
    {
        // Vérifie si l'alerte doit être affichée
        if (get_transient(self::TRANSIENT_NAME)) {
            $className = "notice notice-error is-dismissible";
            $message = __('The plugin requires the <b>ScriboInit</b> class to function. Please install or activate the “ScriboInit” plugin.');

            // Affiche le message d'erreur
            printf(
                '<div class="%s"><p>%s</p></div>',
                esc_attr($className),
                $message
            );

            // Supprime le message après l'avoir affiché
            delete_transient(self::TRANSIENT_NAME);
        }
    }
}
