<?php

function form_validation( $description, $content, $codemirror_error )
{
    global $reg_errors;
    $reg_errors = new WP_Error;

    if ( empty( $description ) || empty( $content ) ) {
        $reg_errors->add('field', 'Required form field is missing');
    }

    if ( $codemirror_error === '1' ) {
        $reg_errors->add('field', 'The Content field is not a valid HTML, CSS, Script codes.');
    }

    if ( is_wp_error( $reg_errors ) ) {
 
        foreach ( $reg_errors->get_error_messages() as $error ) {
            
            echo '<input type="hidden" id="has_notice">';
            echo '<div class="notice notice-error is-dismissible" style="display: none;">
                    <p>Error: <strong>'. $error .'</strong></p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                  </div>';

        }
     
    }

}