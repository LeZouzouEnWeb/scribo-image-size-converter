<?php


// Hook pour ajouter une option à l'interface d'admin
add_action('admin_menu', 'isc_add_plugin_page');
add_action('admin_init', 'isc_settings_register');
