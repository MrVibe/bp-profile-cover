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


class bp_profile_cover_functions{

	var $message;

	function __construct(){
		add_action( 'bp_xprofile_setup_nav', array($this,'bp_profile_cover_setup_nav'));
		add_action('bp_before_group_home_content',array($this,'bp_group_profile_cover'));
		add_action('bp_before_member_home_content',array($this,'display_cover'));
		add_action('bp_before_member_plugin_template',array($this,'display_cover'));
	}

	function bp_profile_cover_setup_nav() {
	    global $bp;
	    $profile_link = bp_loggedin_user_domain() . $bp->profile->slug . '/';
	    bp_core_new_subnav_item(
	        array(
	            'name' => __('Change Cover', 'bpcp'),
	            'slug' => 'change-cover',
	            'parent_url' => $profile_link,
	            'parent_slug' => $bp->profile->slug,
	            'screen_function' => array($this,'bp_profile_cover_upload'),
	            'user_has_access' => (bp_is_my_profile() || is_super_admin()),
	            'position' => 40
	        )
	    );

	}
	function bp_profile_cover_upload() {
		global $bp;

		if(isset($_POST['action']) && $_POST['action'] == 'bp_cover_upload' ){ 

			if ( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'],'bp_cover_upload') ){
			    $this->message = '<div class="error message">'.__('Security check Failed. Contact Administrator.','vibe').'</div>';
			}else{
				if(! empty( $_FILES['profile_cover']['name'])){
					$attachment = new BP_Profile_Cover();
					$file = $attachment->upload( $_FILES );
					if ( ! empty( $file['error'] ) ) {
						$this->message =  '<div class="error message">'.$file['error'].'</div>';
					} else{
						update_user_meta($bp->loggedin_user->id,'cover',$file['url']);
						$this->message =  '<div class="success message">'.__('Cover image uploaded successfully','vibe').'</div>';
					}
				}else if(isset($_POST['delete_profile_cover'])){
					delete_user_meta($bp->loggedin_user->id,'cover');
				}
			}
			
		}
	    
	    add_action('bp_template_content',array($this,  'bp_profile_cover_page_content'));
	    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}

	//Upload page content
	function bp_profile_cover_page_content() {
		echo $this->message;
		?>
		<h4><?php _e( 'Add/Edit profile cover photo', 'buddypress' ); ?></h4>
		<?php do_action( 'bp_before_profile__upload_content' ); ?>

			<p><?php _e( 'You can upload an image from your computer.', 'buddypress'); ?></p>

			<form action="" method="post" id="cover-upload-form" class="standard-form" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bp_cover_upload' ); ?>
					<p><?php _e( 'Click below to select a JPG or PNG format photo from your computer and then click \'Upload Image\' to proceed.', 'buddypress' ); ?></p>

					<p id="cover-upload"><br />
						<input type="file" name="profile_cover" id="profile_cover" /><br />
						<input type="submit" name="profile_cover" id="upload" value="<?php esc_attr_e( 'Upload Image', 'buddypress' ); ?>" />
						<input type="hidden" name="action" id="action" value="bp_cover_upload" />
					</p>

					<?php if ( bp_get_user_has_cover() ) : ?>
						<p><?php _e( "If you'd like to delete your current cover but not upload a new one, please use the delete cover button.", 'buddypress' ); ?></p>
						<input type="submit" name="delete_profile_cover" id="delete" value="<?php esc_attr_e( 'Delete Cover', 'buddypress' ); ?>" />
					<?php endif; ?>

			</form>
		<?php do_action( 'bp_after_profile_cover_upload_content' ); 
		
	}

	function display_cover(){ 
       $user_id = bp_displayed_user_id();
       $cover_url = get_user_meta($user_id,'cover',true);

       if(empty($cover_url) ){
           $cover_url = plugin_dir_url( __FILE__ ).'/default_cover.jpeg';
       }

       echo '<img src="'.$cover_url.'" class="cover_image" />';
    }
	function bp_group_profile_cover(){ 
		global $bp;
		$cover_url = groups_get_groupmeta( $bp->group->id, 'cover' );
		if(empty($cover_url) ){
           $cover_url = plugin_dir_url( __FILE__ ).'/default_cover.jpeg';
       }
       echo '<img src="'.$cover_url.'" class="cover_image" />';
	}
}

new bp_profile_cover_functions;



function bp_get_user_has_cover(){
	global $bp;
	$check = get_user_meta($bp->loggedin_user->id,'cover',true);
	if(!empty($check)){
		return true;
	}else{
		return false;
	}
}

function bp_get_group_has_cover(){
	return 0;
}


