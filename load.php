<?php


// Utiliser la classe
// $scribo_init = new ScriboInit(ISC_PLUGIN_FILE);
ISC_SCRIBO_INIT->RequireFile('URI_INC', '/imageConverter.php');
// Initialiser la classe avec l'objet ScriboInit
$image_converter = new ImageConverter(ISC_SCRIBO_INIT);