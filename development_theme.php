<?
/*
Plugin Name: Development Theme 2
Plugin URI: rgdesing.org
Description: Let use diferent themes installed for each user role actived and visitors. Read the "readme.txt" file on plugin´s folder.
Version: 0.2
Author: Roberto Garcia
Author URI: http://rgdesign.org/
License: GPL2
*/

/*  Copyright 2012  Roberto Garcia (email: roberto@rgdesign.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

! defined( 'ABSPATH' ) and exit;


define('rg_location','rg_loc');
define('rg_dev_themes','development_themes');

add_action( 'admin_init', 'reg_dev_themes_init' );
add_action( 'admin_menu', 'reg_dev_themes_add_page' );

/* Init plugin options */
function reg_dev_themes_init(){
	register_setting( 'development_themes_settings', 'development_themes_options', 'theme_options_validate' );
}

/* Load up the menu page */
function reg_dev_themes_add_page() {
	add_theme_page( __( 'Development Themes', rg_location ), __( 'Development Themes Options', rg_location ), 'edit_theme_options', 'reg_dev_themes_options', 'theme_options_do_page' );
}

/**
 * Create arrays for our select themes
 */
 $my_blog_id = $GLOBALS['blog_id'];
 if($my_blog_id){
	 	$themes = wp_get_themes(array( 'errors' => false , 'allowed' => null, 'blog_id' => $my_blog_id ));
	 }else{
		$themes = wp_get_themes();
		}


/**
 * Create the options page
 */
 
function theme_options_do_page() {
	global $themes;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>"  . __( ' Development Themes Options', rg_location ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
			<div class="updated fade"><p><strong><?php _e( 'Options saved', rg_location ); ?></strong></p></div>
		<?php endif; ?>

		<? // echo $GLOBALS['blog_id']; ?>
		<? // print_r($themes);?>
		 

		<form method="post" action="options.php">
			<?php settings_fields( 'development_themes_settings' ); ?>
			<?php global $options; $options = get_option( 'development_themes_options' ); ?>
            
            <? if(!$options){ ?>
				<div class="updated fade"><p><strong><?php _e( 'Hi!, this is your first time here, please choose something and save.', rg_location ); ?></strong></p></div>
			<? } ?>
            
            <p><?php _e( 'Select which theme will use each user:', rg_location ); ?></p>
            
            <?
			function do_user_select($role){
				global $themes;
				global $options;
				$user_role = "".$role."_theme";
				if(!$options){ $selected = wp_get_theme();
				} else { $selected = $options[$user_role]; }
				
				if($role == 'users'){
						echo "<tr style=' border-bottom:1px solid #999; '><th scope='row' style='vertical-align:middle; text-align:left; font-weight: 600; width:200px; '>".__( "Visitors (not logged users): ", rg_location )."</th>";
					}else{
						echo "<tr style=' border-bottom:1px solid #999; '><th scope='row' style='vertical-align:middle; text-align:left; font-weight: 600; '>".__( "<span style='text-transform:capitalize;'>$role</span>: ", rg_location )."</th>";
				}
				
				echo "<td style='text-align:left; '>";
				echo "<select name='development_themes_options[$user_role]'>";
				$p = ''; $r = '';
				  
				foreach ($themes as $theme ) {
					$label = $theme['Name']; 
					$theme_st = esc_attr($theme->get_stylesheet());
					if(!$options){ $label_ch = $label; }else{ $label_ch = $theme_st; }
					if ( $label_ch == $selected ) { // Make default first on list
							$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . $theme_st . "'>$label</option>";
							$imgpath = $theme->get_stylesheet();
							$theme_thumb = get_theme_root_uri() ."/".$imgpath."/screenshot.png";
					}else{
							$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . $theme_st . "'>$label</option>";
					}
				}
				echo $p . $r;
				echo "</select></td>";
				
				
				echo "<td style='text-align:left;'><span style='width:50px; display:block;'><img style='width:100%; height:auto;' src='". $theme_thumb ."'/></span></td>";
				
				echo "</tr>";
				}
			?>

			<table class="form-table">
            
            	<?
				global $wp_roles; 
				$allroles = $wp_roles->roles;
				foreach($allroles as $role_name => $role_info){
					do_user_select($role_name);
				}
				?>
				
                <? do_user_select('users'); ?>
                
                <tr valign="top"><th scope="row" colspan="3"><input id="development_themes_options[use_theme]" name="development_themes_options[use_theme]" type="checkbox" value="1" <?php checked( '1', $options['use_theme'] ); ?> /> <?php _e( '<strong>Turn OFF Development Themes</strong>: ', rg_location ); ?></th>
                </tr>
                
			</table>
			
            <?
			if($options['use_theme']){
			$my_theme = wp_get_theme();
			?>
			<p style="padding:20px; background:#FFCC00; color:#000;">The box "Turn OFF Development Themes" is checked, that means <strong>ALL users and visitors</strong> to your site, will see the current theme in use: <strong><? echo $my_theme->Name; ?></strong>.
			<? } ?>
            
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', rg_location ); ?>" />
			</p>
		</form>
	</div>
	<?php
}



function theme_options_validate( $input ) {
	global $themes;

	if ( ! isset( $input['use_theme'] ) )
	$input['use_theme'] = null;
	$input['use_theme'] = ( $input['use_theme'] == 1 ? 1 : 0 );

/*
	if ( ! array_key_exists( $input['administrator_theme'], $themes ) )
		$input['administrator_theme'] = null;
		
	if ( ! array_key_exists( $input['editor_theme'], $themes ) )
		$input['editor_theme'] = null;
*/

	global $wp_roles; 
	$allroles = $wp_roles->roles;
	foreach($allroles as $role_name => $role_info){
		if ( ! array_key_exists( $input[''.$role_name.'_theme'], $themes ) )
		$input[''.$role_name.'_theme'] = null;
	}
	
	if ( ! array_key_exists( $input['users_theme'], $themes ) )
		$input['users_theme'] = null;

	return $input;
}


$options_used = get_option( 'development_themes_options' );
if($options_used && !$options_used['use_theme']){
	add_action( 'plugins_loaded', 'wpse_theme_init' );
	function wpse_theme_init(){
		//add_filter( 'option_template', 'dontchoose' );
		add_filter( 'template', 'dontchoose' );
		add_filter( 'stylesheet', 'dontchoose' ); 
		add_filter( 'option_template', 'dontchoose' );
		add_filter( 'option_stylesheet', 'dontchoose' );
		
		function dontchoose( $template = '' ) {
			global $user_ID, $options_used;
			
			global $wp_roles; 
			 
			$current_user = wp_get_current_user();
			
			if(!is_user_logged_in()){
					$template = $options_used['users_theme'];
				}else{
					$current_user_role = $current_user->roles[0];
					$template = $options_used[''.$current_user_role.'_theme'];
					}
			if ( !($current_user instanceof WP_User) ){
				    
				}else{
					
					}
			
			/*
			if( current_user_can('administrator') ) {
					$template = $options_used['administrator_theme'];	
				}elseif( current_user_can('editor') ) {
					$template = $options_used['editor_theme'];	
				}else{
					$template = $options_used['users_theme'];
			}
			*/
			return $template;
		}
		}
	
}
?>