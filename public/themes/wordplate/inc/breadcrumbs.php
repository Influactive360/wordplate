<?php

function the_breadcrumb(): void {
    echo '<ul id="crumbs">';
    if (!is_home()) {
        echo '<li><a href="';
        echo get_option('home');
        echo '">';
        echo 'Accueil';
        echo "</a>   |</li>";
        if (is_category() || is_single()) {
            echo '<li>';
            the_category(' </li><li> ');
            if (is_single()) {
                echo "   |</li><li class='active'>";
                the_title();
                echo '</li>';
            }
        } else if (is_page()) {
            echo '<li class="active">';
            echo the_title();
            echo '</li>';
        }
    }
    echo '</ul>';
}
