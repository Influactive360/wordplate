<?php

// Path de templates dans le thème
$theme_dir = get_template_directory();
$dir = "$theme_dir/layouts/";

// Récupérer tous les fichiers et dossiers dans le répertoire
$files = scandir($dir);

foreach ($files as $file) {
    // Vérifier si le fichier ou dossier est un dossier
    if (is_dir($dir . $file)) {
        add_filter("acfe/flexible/thumbnail/layout=$file", static function ($thumbnail, $field, $layout) use ($file) {
            // Must return a URL or Attachment ID
            return get_template_directory_uri() . "/layouts/$file/miniature.png";
        }, 10, 3);
    }
}
