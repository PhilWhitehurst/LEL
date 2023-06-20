<?php

/*
 * Phil Whitehurst
 * All Ninja Forms add on functionality for the lel2017 child theme
 *
 */


/*
 * Add opening div before fields on Ninja Forms
 */

function lel2017_display_before_field($field_id, $data) {

    if (strpos($data['class'], 'lel2017-half-block-before') !== false) {
        echo '<div class="col-md-6 col-sm-12">';
    }
}

add_action('ninja_forms_display_before_field', 'lel2017_display_before_field', 10, 2);
/*
 * Add closing div after fields on Ninja Forms
 */

function lel2017_display_after_field($field_id, $data) {

    if (strpos($data['class'], 'lel2017-half-block-after') !== false) {
        echo '</div>';
    }

    if (strpos($data['class'], 'lel2017-half-block-list') !== false) {
        echo '</div>';
    }
}

add_action('ninja_forms_display_after_field', 'lel2017_display_after_field', 10, 2);
/*
 *
 */

function lel2017_display_after_field_label($field_id, $data) {

    if (strpos($data['class'], 'lel2017-half-block-list') !== false) {
        echo '<div class="lel2017-half-block-list">';
    }
}

add_action('ninja_forms_display_after_field_label', 'lel2017_display_after_field_label', 99, 2);




