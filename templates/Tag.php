<?php
    
    $tagId;
    $tagName;       
    $pageText;
    $tagAction;
    $pageTitle;
    $tagInitial;

    $status;
    $content;
    $description;
    $active_check;
    $inactive_check;
    $isDisabled;
    
    $plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
    
    function pageTitleSettings( string $action, string $title  )
    {
        $pTitle = __( $action ) . ' ' . '&lt;'. __( $title ) .'&gt; ' . __('Tag');
        return $pTitle;
    }

    function pageTextSettings( string $action, string $title )
    {
        $pText =  __( $action ) . __(' (HTML, CSS, Script) code to the') . ' &lt;' . __( $title ) . '&gt; ' . __('tag') . '.';
        return $pText;
    }

    function editValues( $id, string $table )
    {
        global $wpdb;
        $table_name = $table;

        $obj_data = $wpdb->get_results( "SELECT id, description, content, position, status FROM {$table_name} WHERE id = '{$id}' LIMIT 1" );
        $obj_temp = json_decode( json_encode( $obj_data ), true );

        return $obj_temp;
    }

    if ( isset( $_GET['page'] ) ) {

        $pageAction = explode( '_', $_GET['page'] );
        $actionType = $pageAction[0];
        $actionTag = $pageAction[1];

        // Sets TagName and Initials if header Tag
        if ( $actionTag === 'header' ) {
            $tagName = 'header'; 
            $tagInitial = 'h';
            $tagId = ( isset( $_GET['hid'] ) ) ? $_GET['hid'] : '';
        }

        // Sets TagName and Initials if body Tag
        if ( $actionTag === 'body' ) {
            $tagName = 'body'; 
            $tagInitial = 'b';
            $tagId = ( isset( $_GET['bid'] ) ) ? $_GET['bid'] : '';
        }

        // Requires the Validation
        if ( $actionType === 'add' || $actionType === 'edit' ) { 
            require( $plugin_path . "header-and-body/inc/Base/Validation.php" );
        }

        // If Action Type is ADD
        if ( $actionType === 'add' ) {
            $tagAction = 'Add';
        }

        // If Action Type is EDIT
        if ( $actionType === 'edit' || $actionType === 'view' ) {
            $tagAction = 'Edit';

            $tbl_name = 'wp_'. $tagName .'_data_plugin';
            $obj_temp = editValues( $tagId, $tbl_name );

            $description = $obj_temp[0]['description'];
            $content = $obj_temp[0]['content'];
            $position = $obj_temp[0]['position'];
            $status = $obj_temp[0]['status'];

            $description = ( empty( $_POST[ 'desc' ] ) ) ? $description : $_POST[ 'desc' ];
            $content = ( empty( $_POST[ 'content' ] ) ) ? $content : $_POST[ 'content' ];
                
            if ( ! isset( $_POST[ 'position' ] ) ) {
                $edit_position_check = ( $position == 0 ) ? 'checked' : '';
            }

            if ( ! isset( $_POST[ 'status' ] ) ) {
                if  ( ! empty ( $status ) ) {
                    $edit_status_check = ( $status === 'Active' ) ? 'checked' : '';
                }
            }

        }

        // If Action Type is VIEW
        if ( $actionType === 'view' ) {
            $tagAction = 'View';
            $isDisabled = true;
        }
        
        // Tag Page Settings
        $pageTitle = pageTitleSettings( $tagAction, $tagName );
        $pageText = pageTextSettings( $tagAction, $tagName );
    
    }

?>

<?php if ( $isDisabled ): ?>
<style>.CodeMirror { pointer-events: none; }</style>
<?php endif; ?>

<div class="wrap" id="tagPage">
    <div class="tab-content">

        <div id="tab-1" class="tab-pane active">

            <h1><?php echo $pageTitle; ?></h1>
            <p><?php echo $pageText; ?></p>

            <form method="post">

                <input type="hidden" name="tagName" value="<?php echo $tagName; ?>">
                <input type="hidden" name="tagAction" value="<?php echo $tagAction; ?>">
                <input type="hidden" name="<?php echo $tagInitial . 'id'; ?>" value="<?php echo $tagId; ?>">

                <table class="form-table">
                    <tbody>
                        <tr class="form-field form-required">
                            <th scope="row"><label for="desc"><?php echo __('Description'); ?> <span class="description"><?php echo __('(required)'); ?></span></label></th>
                            <td><input type="text" id="desc" name="desc" value="<?php echo ( ! empty( $description ) ? $description : $_POST[ 'desc' ] ) ?>"
                                    aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off"
                                    maxlength="60" style="min-width: 200px; max-width: 400px;" <?php echo ( $isDisabled ) ? 'readonly' : '' ?> ></td>
                        </tr>
                        <tr class="form-field form-required">
                            <th scope="row"><label for="content"><?php echo __('Content'); ?> <span class="content"><?php echo __('(required)'); ?></span></label></th>
                            <td><textarea name="content" id="content" cols="30" rows="10" class="regular-text"
                                    aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" <?php echo ( $isDisabled ) ? 'disabled' : '' ?> ><?php echo wp_unslash( ( ! empty( $content ) ) ? $content : $_POST[ 'content' ] ) ?></textarea></td>
                        </tr>
                        <tr class="form-field form-required">
                            <th scope="row"><label for="position"><?php echo __('Position'); ?> </label></th>
                            <td>

                                <?php 
                                    $position_value = ( $_POST[ $tagInitial . '-hidden-position' ] === '0' ) ? 'Inactive' : 'Active'; 
                                    $position_checked = ( $_POST[ $tagInitial . '-hidden-position' ] === '0' ) ? '' : 'checked'; 
                                ?>

                                <input type="hidden" name="<?php echo $tagInitial . '-hidden-position'; ?>" id="<?php echo $tagInitial . '-hidden-position'; ?>" value="<?php echo $_POST[ $tagInitial . '-hidden-position' ] ?>" >

                                <label class="toggle-check">
                                    <input type="checkbox" name="position" id="position" class="<?php echo $tagInitial . '-position-toggle-check-input'; ?>" value="<?php echo $position_value; ?>" <?php echo ( isset( $edit_position_check ) ) ? $edit_position_check : $position_checked; ?> 
                                        <?php echo ( $isDisabled ) ? 'disabled' : '' ?>/>
                                    <span class="toggle-check-text" style="padding-top: 0.1em;"></span>
                                </label>
                            </td>
                        </tr>
                        <tr class="form-field form-required">
                            <th scope="row"><label for="status"><?php echo __('Status'); ?> </label></th>
                            <td>
                                
                                <?php 
                                    $status_value = ( $_POST[ $tagInitial . '-hidden-status' ] === 'Inactive' ) ? 'Inactive' : 'Active'; 
                                    $status_checked = ( $_POST[ $tagInitial . '-hidden-status' ] === 'Inactive' ) ? '' : 'checked'; 
                                ?>
                                
                                <input type="hidden" name="<?php echo $tagInitial . '-hidden-status'; ?>" id="<?php echo $tagInitial . '-hidden-status'; ?>" value="<?php echo $_POST[ $tagInitial . '-hidden-status' ] ?>">

                                <label class="toggle-check">
                                    <input type="checkbox" name="status" id="status" class="<?php echo $tagInitial . '-status-toggle-check-input'; ?>" value="<?php echo $status_value; ?>" <?php echo ( isset( $edit_status_check ) ) ? $edit_status_check : $status_checked; ?> 
                                        <?php echo ( $isDisabled ) ? 'disabled' : '' ?>/>
                                    <span class="status-toggle-check-text" style="padding-top: 0.1em;"></span>
                                </label>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php if ( $isDisabled ): ?>
                                    <a href="<?php echo REDIRECT_LINK; ?>"><button type="button" class="button button-default"><?php echo __('Back'); ?></button></a>
                                <?php else: ?>
                                    <input type="submit" name="submit" id="<?php echo $tagName . 'SubmitBtn'; ?>" class="button button-primary" value="Save Changes">
                                    <a href="<?php echo REDIRECT_LINK; ?>"><button type="button" class="button button-default"><?php echo __('Cancel'); ?></button></a>
                                <?php endif; ?>
                            </th>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="<?php echo $tagInitial . '-codemirror-error'; ?>" id="<?php echo $tagInitial . '-codemirror-error'; ?>" value="<?php echo $_POST[  $tagInitial . '-codemirror-error' ] ?>">
            </form>
        </div>
    </div>
</div>

