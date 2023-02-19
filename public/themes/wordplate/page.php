<?php

get_header();

if (function_exists('has_flexible')) :
    if (has_flexible('')) :
        the_flexible('');
    endif;

    if (has_flexible('')) :
        the_flexible('');
    endif;
endif;

get_footer();
