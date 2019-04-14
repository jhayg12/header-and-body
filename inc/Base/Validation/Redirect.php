<?php

function redirect_to_dashboard()
{   
    // Clear Form Inputs
    $_POST = array();

    $plugin_link = get_site_url() . '/wp-admin/options-general.php?page=header_body';
    
    ?>

    <script> window.location.href = '<?php echo $plugin_link ?>'; </script>

    <?php

}