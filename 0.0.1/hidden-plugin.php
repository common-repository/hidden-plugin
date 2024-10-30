<?php
/**
 * @package Hidden_Plugin
 * @version 1.0
 */
/*
Plugin Name: Hidden Plugin
Plugin URI: #
Description: Hide plugins from Selected users.
Author: 
Version: 1.0
Author URI: https://www.techtic.com/
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class Hide_Plugins {

  public function __construct() { 
    add_action('admin_menu',  array( $this,'hidden_plugin_menu'));
    add_action('wp_enqueue_scripts',  array( $this,'hidden_plugin_scripts'));
  }

  public function hidden_plugin_menu() {
     wp_register_style( 'hide_plugin_select',  plugins_url('assets/css/jquery.ultraselect.min.css',__FILE__ ) );
    wp_enqueue_style( 'hide_plugin_select' );
    wp_enqueue_script( 'hide_plugin_select_js', plugins_url('assets/js/ultraselect.js',__FILE__ ) ); 
    wp_enqueue_script('hide_plugin_select_js');
    add_submenu_page('options-general.php', 'Hidden Plugin', 'Hidden Plugin', 'manage_options','hidden_plugin', array( $this,'hidden_plugin_cb_screen') );
    add_action( 'admin_init', array( $this,'register_user_hide_settings' ));
    add_filter( 'all_plugins', array( $this, 'prepare_items' ) );
/*  $userid=get_current_user_id();
    $user_id = get_option( 'hidden_user_options' );  
    if (in_array($userid, $user_id)) {   
     remove_submenu_page( 'options-general.php', 'hidden_plugin' );
   }*/
  }

  public function hidden_plugin_scripts() {
   
  }

  function prepare_items( $plugins ) {
    $userid=get_current_user_id();
    $user_id = get_option( 'hidden_user_options' ); 
    $pluginsToHide = get_option('hidden_plugin_options'); 

      if (in_array($userid, $user_id)) {   
      foreach($pluginsToHide as $pluginFile) {
        unset($plugins[$pluginFile]);
      }     
    }
    return $plugins;
  }

  function register_user_hide_settings() {
    register_setting( 'register_user_hide_setting', 'hidden_user_options' );
  	register_setting( 'register_user_hide_setting', 'hidden_plugin_options' );
  }

  function hidden_plugin_cb_screen()
  {
    $users = get_users( 'role=administrator' );
    $slected_user= get_option('hidden_user_options'); 
    $slected_plugin= get_option('hidden_plugin_options'); 
    ?>
    <div class="wrap">
      <script type="text/javascript">
      jQuery( document ).ready(function() {
        jQuery("#hidden_user_options").ultraselect();
        jQuery("#hidden_plugin_options").ultraselect();
       });
      </script>
     <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
     <?php 
      if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
    $all_plugins = get_plugins();
     ?>
     <form method="post" action="options.php">
      <?php settings_fields( 'register_user_hide_setting' ); ?>
      <table>
        <tr>
          <th scope="row"><label for="hidden_user_options">Select User</label></th>
          <td>
           <select multiple id="hidden_user_options" class="regular-text ltr" name="hidden_user_options[]">
             <?php 
             foreach ($users as $user) {
              ?>
              <option value="<?php echo $user->ID ?>" <?php echo ( !empty( $slected_user ) && in_array( $user->ID, $slected_user )) ? ' selected="selected"' : '' ?>><?php echo ucwords($user->display_name); ?></option>
              <?php
            }
            ?>
          </select>
          <select multiple id="hidden_plugin_options" class="regular-text ltr" name="hidden_plugin_options[]">
             <?php 
             foreach ($all_plugins as $key => $plugin) {
              ?>
              <option value="<?php echo $key ?>"  <?php echo ( !empty( $slected_plugin ) && in_array( $key, $slected_plugin )) ? ' selected="selected"' : '' ?>><?php echo ucwords($plugin['Name']); ?></option>
              <?php
            }
            ?>
          </select>
        </td>
      </tr>
    </table>
    <?php  submit_button('Save Setting'); ?>
  </form>
</div>
<?php
}

}
new Hide_Plugins();
