<?php

get_header();

if (function_exists('has_flexible') && function_exists('get_queried_object')) :
    if (get_page_by_path(get_queried_object()->name)):
        $id = get_page_by_path(get_queried_object()->name)->ID;
        if (has_flexible('', $id)) :
            the_flexible('', $id);
        endif;

        if (has_flexible('', $id)) :
            the_flexible('', $id);
        endif;
    else : return false;
    endif;
endif;

get_footer();
