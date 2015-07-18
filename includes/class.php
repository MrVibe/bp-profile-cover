<?php
/**
 * Installation related functions and actions.
 *
 * @author 		VibeThemes
 * @category 	Admin
 * @package 	Vibe Projects/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'BP_Attachment') ) :

class BP_Profile_Cover extends BP_Attachment {

	public function __construct() {
		parent::__construct( array(
			'action'                => 'bp_cover_upload',
			'file_input'            => 'profile_cover',
			'allowed_mime_types'    => array( 'png', 'jpg' ),
			'base_dir'              => 'member_cover',
		) );
	}
	public function validate_upload( $file = array() ) {

		$file = parent::validate_upload( $file );
		if ( ! empty( $file['error'] ) ) {
			return $file;
		}
		return $file;
	}
	public function upload_dir_filter() {
		$upload_dir_data = parent::upload_dir_filter();
		if ( ! is_user_logged_in() ) {
			return $upload_dir_data;
		}
		return array(
			'path'    => $this->upload_path . '/' . bp_loggedin_user_id(),
			'url'     => $this->url . '/' . bp_loggedin_user_id(),
			'subdir'  => '/' . bp_loggedin_user_id(),
			'basedir' => $this->upload_path . '/' . bp_loggedin_user_id(),
			'baseurl' => $this->url . '/' . bp_loggedin_user_id(),
			'error'   => false
		);
	}
}



class BP_Group_Profile_Cover extends BP_Attachment {

	public function __construct() {
		parent::__construct( array(
			'action'                => 'bp_group_cover_upload',
			'file_input'            => 'group_cover',
			'allowed_mime_types'    => array( 'png', 'jpg' ),
			'base_dir'              => 'group_cover',
		) );
	}
	public function validate_upload( $file = array() ) {

		$file = parent::validate_upload( $file );
		if ( ! empty( $file['error'] ) ) {
			return $file;
		}
		return $file;
	}
	public function upload_dir_filter() {
		$upload_dir_data = parent::upload_dir_filter();
		if ( ! is_user_logged_in() ) {
			return $upload_dir_data;
		}
		return array(
			'path'    => $this->upload_path . '/' . bp_loggedin_user_id(),
			'url'     => $this->url . '/' . bp_loggedin_user_id(),
			'subdir'  => '/' . bp_loggedin_user_id(),
			'basedir' => $this->upload_path . '/' . bp_loggedin_user_id(),
			'baseurl' => $this->url . '/' . bp_loggedin_user_id(),
			'error'   => false
		);
	}
}
endif;



if ( class_exists( 'BP_Group_Extension' ) ) :

    class BP_Group_Cover extends BP_Group_Extension {

    	var $message;

        function __construct() { 
            $args = array(
                'slug' => 'group-cover',
                'name' => __( 'Cover Photo', 'bp-profile-cover' ),
                'visibility'        => 'noone',
                'screens' => array(
                    'admin' => array(
						'metabox_context'  => 'side',
						'metabox_priority' => 'core'
					),
					'create' => array(
						'enabled' => false,
					),
					'edit' => array(
						'enabled' => true,
					),
                )
            );
            parent::init( $args );
        }
        /**
         * settings_screen() is the catch-all method for displaying the content
         * of the edit, create, and Dashboard admin panels
         * @param integer|null $group_id
         */
        function settings_screen( $group_id = NULL ) {

            $image_url = groups_get_groupmeta( $group_id, 'cover' );

            if ( ! empty( $image_url ) ): ?>
                <div id="bg-delete-wrapper">
                    <input type="submit" name="delete_group_profile_cover" id='delete_group_profile_cover' class='btn btn-default btn-xs' value="<?php _e('Delete existing cover', 'bpcp'); ?>" />
                </div>
            <?php endif; ?>

            <p><?php _e('If you want to change your group cover, please upload a new image.', 'bpcp'); ?></p>
            <?php wp_nonce_field( $group_id ); ?>
            <input type="file" name="group_cover" id="group_cover" class="group_cover">
            <input type="hidden" name="action" id="action" value="bp_group_cover_upload" />
            <input type="hidden" name="group_id" value="<?php echo $group_id;?>" />
        <?php
        }

        /**
         * settings_screen_save() contains the catch-all logic for saving
         * settings from the edit, create, and Dashboard admin panels
         * @param $group_id int
         */
        function settings_screen_save( $group_id = NULL ) {

        	if(isset($_POST['action']) && $_POST['action'] == 'bp_group_cover_upload' ){ 
			
			$group_id = $_POST['group_id'];

			if ( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'],$group_id) ){
				    $this->message = '<div class="error message">'.__('Security check Failed. Contact Administrator.','vibe').'</div>';
			}else{
					if(! empty( $_FILES['group_cover']['name'])){
						$attachment = new BP_Group_Profile_Cover();
						$file = $attachment->upload( $_FILES );
						if ( ! empty( $file['error'] ) ) {
							$this->message =  '<div class="error message">'.$file['error'].'</div>';
						} else{
							groups_update_groupmeta( $group_id, 'cover', $file['url']);
							$this->message =  '<div class="success message">'.__('Cover image uploaded successfully','vibe').'</div>';
						}
					}else if(isset($_POST['delete_group_profile_cover'])){
						groups_delete_groupmeta($group_id,'cover');
					}
				}
				
			}

        }
    }

function bp_group_cover_register_group_extension() {
	bp_register_group_extension( 'BP_Group_Cover' );
}

add_action( 'bp_init', 'bp_group_cover_register_group_extension' );  
endif;