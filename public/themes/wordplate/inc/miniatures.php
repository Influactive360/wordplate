<?php
add_filter('acfe/flexible/thumbnail/layout=layout-exemple', static function ($thumbnail, $field, $layout) {
    // Must return a URL or Attachment ID
    return get_template_directory_uri() . '/templates/layout-exemple/miniature.png';
}, 10, 3);
