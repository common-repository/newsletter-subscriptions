<?php
/*
Plugin Name: Newsletter Subscriptions
Plugin URI: https://profiles.wordpress.org/hanif-khan
Description: This plugin is for newsletter subscriptions. 
Version: 2.1
Author: Hanif Khan
Author URI: https://www.facebook.com/hanif.khan.5249
Text Domain: newsletter-subcriptions
Domain Path: /lang/

*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly	
// Creating the widget 
class newsletter_wpb_widget extends WP_Widget {

function __construct() {

	parent::__construct(
	// Base ID of your widget
	'newsletter_wpb_widget', 
	
	// Widget name will appear in UI
	__('Newsletter Subscriptions Widget', 'wpb_widget_domain'), 
	
	// Widget description
	array( 'description' => __( 'Add Newsletter Subscriptions widget for users', 'wpb_widget_domain' ), ) 
	);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];


if ( ! empty( $title ) )
$title = $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output
custom_newsletter_news_letter($title);

/*echo __( 'Hello, World!', 'wpb_widget_domain' );
echo $args['after_widget'];*/
}
		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'Newsletter', 'wpb_widget_domain' );
}
// Widget admin form
?><p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

add_action ( 'admin_enqueue_scripts', function () {
	
	if (is_admin ())
		wp_enqueue_media ();
		wp_enqueue_script('news-admin-javascript',plugins_url('newsletter-subcriptions-admin.js',__FILE__),array('jquery'),'2.5.1');
} );

add_action('wp_enqueue_scripts','news_subcriber_init');

function news_subcriber_init() {
	
	wp_enqueue_script('jquery');
    wp_enqueue_script( 'news-subcriber-js', plugins_url( 'news_subcriber.js', __FILE__ ));
}

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'newsletter_wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );	



add_action('admin_menu','newsletter_subscriptions_menu_tlwm');
add_filter('widget_text', 'do_shortcode');

register_activation_hook(__FILE__,'newsletter_subcriber_install');
function newsletter_subcriber_install(){
#create database table	
global $wpdb;

		$query = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}news_letter` (
					`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY key,
					`name` varchar(250) NOT NULL,
					`email` varchar(250) NOT NULL,
					`status` varchar(15) NOT NULL,
					`create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
		$wpdb->query($query);	
	
}
function newsletter_subscriptions_menu_tlwm(){
	
	$pluginUrl = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );
	
	add_menu_page('Simple Newsletter Page','My Subscriptions','manage_options','newsletter-subscriptions','simple_newsletter_page',$pluginUrl . "images/news_subscription.png");
	add_submenu_page( 'newsletter-subscriptions', 'Subscriptions Setting', 'Subscriptions Setting', 'manage_options', 'newsletter-subscriptions-setting', 'news_subcriber_setting_view' ); 
	//add_optionewsletter_page( 'Settings', 'Settings', 'manage_options', 'newsletter-subcriptions', 'simple_newsletter_page' );
}

function news_subcriber_setting_view(){
	
	if( current_user_can( 'administrator' ) ){ 
	
			$news_subcription_option = array();

			if( get_option( 'news_subcription_setting' ) ){

				$news_subcription_option = json_decode( get_option( 'news_subcription_setting' ), true );	
			}
			
			$token_type = isset($news_subcription_option['token_type']) and $news_subcription_option['token_type'] ? $news_subcription_option['token_type'] : "";
			
			if( isset($_POST['save_setting']) && wp_verify_nonce( $_POST['news_subcription_post'], 'news_subcription_setting_update' ) ) {
		
				$news_subcription_option['name'] = isset( $_POST['news_subcription_enable_name'] ) ? 1 : "";
				$news_subcription_option['token_type'] = sanitize_text_field( $_POST['token_type'] );
				update_option( 'news_subcription_setting', json_encode( $news_subcription_option ) );
				$success = "News subscriptions updated.";
				
			}
			
			if( isset($_POST['save_css_setting']) && wp_verify_nonce( $_POST['news_subcription_css_post'], 'news_subcription_css_update' ) ) {
		
				$news_subcription_option['news_subcription_css'] = $_POST['news_subcription_css'];
				$news_subcription_option['token_type'] = sanitize_text_field( $_POST['token_type'] );
				update_option( 'news_subcription_setting', json_encode( $news_subcription_option ) );
				$success = "News subcription updated";
				
			}
			
			$news_subcription_option = json_decode( get_option( 'news_subcription_setting' ), true );
			
			if( isset( $_POST['token_type'] ) && !empty( $_POST['token_type'] ) ) {
				
				$token_type = $_POST['token_type'];
			} else {
				
				$token_type = isset($news_subcription_option['token_type']) and $news_subcription_option['token_type'] ? $news_subcription_option['token_type'] : "";
			}

			//echo $token_type;
			
			if(empty($token_type) || $token_type == 1 ){
				
				$token_type = "#tab1";
			}


	echo '<div class="wrap"><div class="icon32 icon32-posts-page" id="icon-edit-pages"></div>';
		echo '<h2>Newsletter Subscriptions Setting<br/><br/><p><strong>[SUBSCRIPTION_NEWSLETTER]</strong> Use Shortcode for newsletter subscriptions.</p></h2>';
	echo '</div>';
    ?>
    <div class="wrap woocommerce">
      <form name="sfrm" action="" method="post">
        <input type="hidden" name="token_type" id="token_type" value="<?php echo $token_type;?>"  />
        <nav class="nav-tab-wrapper woo-nav-tab-wrapper news-subcription-data"> <a href="#tab1" class="nav-tab <?php if($token_type == '#tab1') echo 'nav-tab-active';?>">Setting</a> <a href="#tab2" class="nav-tab <?php if($token_type == '#tab2') echo 'nav-tab-active';?>">Custom CSS</a> </nav>
        <?php if(isset($success) and !empty($success)){?>
        <div id="message" class="updated inline notice is-dismissible">
          <p><?php echo $success;?></p>
          <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
        <?php }?>
        <div class="production box" <?php if($token_type == '#tab1') echo 'style="display:block"'; else  echo 'style="display:none"';?> id="tab1">
          <table class="form-table">
            <tbody>
              <tr valign="top">
                <th scope="row" class="titledesc"><label>Enable Name</label>
                </th>
                <td class="forminp forminp-text"><label><input name="news_subcription_enable_name" id="news_subcription_enable_name" type="checkbox" class="" value="1" <?php echo (isset($news_subcription_option['name']) and $news_subcription_option['name']) ? ' checked="checked"' : "";?>>Enable name for news subscriptions</label></td>
              </tr>
            </tbody>
          </table>
          <p class="submit">
          <button name="save_setting" id="save_setting" class="button-primary woocommerce-save-button" type="submit" value="Update Setting">Update</button>
          <?php wp_nonce_field('news_subcription_setting_update', 'news_subcription_post');?>
        </p>
        </div>
        <div class="production box" <?php if($token_type == '#tab2') echo 'style="display:block"'; else  echo 'style="display:none"';?> id="tab2">
          <table class="form-table" width="70%" style="table-layout: initial;">
            <tbody>
            <tr valign="top">
                <td class="forminp forminp-text"><textarea name="news_subcription_css" rows="8" cols="80"><?php if( !empty( $news_subcription_option['news_subcription_css'] )) echo esc_attr( $news_subcription_option['news_subcription_css'] );?></textarea></td>
              </tr>
             <tr valign="top">
                <td class="forminp forminp-text" colspan="2"><p class="submit">
           <button name="save_css_setting" id="save_css_setting" class="button-primary woocommerce-save-button" type="submit" value="Update CSS">Update</button>
          <?php wp_nonce_field('news_subcription_css_update', 'news_subcription_css_post');?>
        </p></td>
              </tr>
            </tbody>
          </table>
        </div>
      </form>
    </div>
	<?php
	}
}
 

add_action ( 'admin_enqueue_scripts', function () {
	if (is_admin ())
		wp_enqueue_media ();
		wp_enqueue_script('jquery-ui-datepicker');
} );


add_action( 'admin_post_export_newssubsciptions_csv', 'news_letter_csv' );

function news_letter_csv()
{
	global $wpdb;	
    if ( ! current_user_can( 'manage_options' ) )
        return;

	$news_subcription_option = json_decode( get_option( 'news_subcription_setting' ), true );
	$name = $news_subcription_option['name'] ? 'Name,' : "";
	$file = "S NO,".$name."Email\r";

	$select_data = $wpdb->get_results("select * from {$wpdb->prefix}news_letter")  or $wpdb->last_error;
	
	$i=1;
	foreach($select_data as $res){
	
		$name_field = $news_subcription_option['name'] ? newsletter_correct_data($res->name).',' : "";
		$file.=  "\n".$i.','.
					$name_field.
					newsletter_correct_data($res->email).',';
		$i++;				
	}
	
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=news-subscription.csv');
    header('Pragma: no-cache');
	echo $file;
	exit;
    // output the CSV data
}

function simple_newsletter_page(){
	
	global $wpdb;
	if( current_user_can( 'administrator' ) ){
			
	require_once('newsletter-subscriptions-pager.php');
	$simple_p = new newsletter_Simple_Newsletter_Signup_Pager;
 
	$simple_limit = 10;
	 
	$simple_start = $simple_p->newsletter_findStart($simple_limit);

	if(isset($_GET['post_status']) && !empty($_GET['post_status'])){
		
		$results_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}news_letter where status = '".$_GET['post_status']."'");
	}else{
		
		$results_count = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}news_letter");	
	}
	
	$simple_count = $wpdb->num_rows;
	
	// to get pulish count 
	$results_publish = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}news_letter where status = 'publish'");
	$publish_count = $wpdb->num_rows;
	 
	// to get trash count 
	$results_trash = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}news_letter where status = 'trash'");
	$trash_count = $wpdb->num_rows;
	 
	$simple_pages = $simple_p->newsletter_findPages($simple_count, $simple_limit);
	
	if(isset($_GET['post_status']) && !empty($_GET['post_status'])){
		
		$qry = "SELECT * FROM {$wpdb->prefix}news_letter where status = '".$_GET['post_status']."' LIMIT ".$simple_start.", ".$simple_limit;
	}else{
		
		$qry = "SELECT * FROM {$wpdb->prefix}news_letter LIMIT ".$simple_start.", ".$simple_limit;	
	}
	 
	$simple_result = $wpdb->get_results($qry);
	$simple_pagelist = $simple_p->news_subcriber_newsletter_pageList($_GET['start'], $simple_pages);
	
	$news_subcription_option = json_decode( get_option( 'news_subcription_setting' ), true );
	
?><div class="wrap">

<?php if($simple_count > 0){

$plagin_uri = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );?><form name="frmcsv" method="post" action="">
<p class="search-box">
<a href="<?php echo admin_url( 'admin-post.php?action=export_newssubsciptions_csv' );?>" class="button">Export CSV</a>
<?php wp_nonce_field( 'newsletter_csvexport_action', 'newsletter_csvexport_hidden' ); ?></p>
</form>

<?php
 }?><h2>Newsletter Subscriptions</h2>
<div class="tablenav top"><br />
<div class="tablenav-pages">
<span class="displaying-num"><?php echo $simple_count." ";?>items</span>
<span class="pagination-links"><?php echo $simple_pagelist;?></span>
</div>
</div><form name="newssubcriber-actions-form" id="newssubcriber-actions-form" method="post" action="">
<table class="wp-list-table widefat pages" cellpadding="2" cellspacing="">
  <thead>
    <tr>
      <th>S No.</th>
      <?php if( isset($news_subcription_option['name']) and $news_subcription_option['name'] ) {?>
      <th>Name</th>
      <?php }?>
      <th>Email</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th>S No.</th>
      <?php if( isset($news_subcription_option['name']) and $news_subcription_option['name'] ) {?>
      <th>Name</th>
      <?php }?>
      <th>Email</th>
    </tr>
  </tfoot>
  <tbody>
<?php // START the for each  
   if($simple_count < 1){
	?><tr>
      <td colspan="3" align="center">There is no Subscribers!</td>
    </tr>
<?php }else{
   
	   $subcriber_check_the_rows = 0;
       //while($simple_rows = mysql_fetch_assoc($simple_result))
	foreach($simple_result as $simple_rows){
		
		if($subcriber_check_the_rows % 2 != 0){
			
				$bgcolor = 'bgcolor="#F2F2F2"';
		}else{
			
				$bgcolor = '';
		}
	?><tr <?php echo $bgcolor;?>>
    	
      <td><?php echo $simple_rows->id; ?></td>
      <?php if( $news_subcription_option['name'] ) {?>
      <td><?php echo ucfirst($simple_rows->name);?></td>
      <?php }?>
      <td><?php echo $simple_rows->email;?></td>
    </tr>
<?php $subcriber_check_the_rows++; } // END the for each
   } ?></tbody>
</table></form>
<div class="tablenav bottom">
<div class="tablenav-pages">
<span class="displaying-num"><?php echo $simple_count." ";?>items</span>
<span class="pagination-links">
<?php echo $simple_pagelist;?></span>
</div>
</div>
</div>
<?php

	}
}

function newsletter_correct_data($str){
	
		$str = str_replace('"', '\"', $str);
		return $str;
}


function newsletter_news_subcriber_mail(){

		global $wpdb;
		$news_subcription_option = json_decode( get_option( 'news_subcription_setting' ), true );

		$admin_email = get_option('admin_email');
		$email = sanitize_email($_POST['email']);
		$name = $news_subcription_option['name'] ? sanitize_text_field( $_POST['name'] ) : "";
		
		$email_results = $wpdb->get_results("select * from {$wpdb->prefix}news_letter where email='".$email."'");
		$num_rows = $wpdb->num_rows;
			
		$subject='Newsletter Subscription';
		$to=$email; 
		
		$message= "<font  style=font-family:arial; >Dear Subscriber,<br><br>
		Thank you for your joining. We will keep you updated with all up-to-date news, events and other info. <br><br>Thanks again.<br><br>
		</font>";
		
		$headers[] = "MIME-Version: 1.0\n";
		$headers[] ="Content-type: text/html; charset=iso-8859-1\n";
		$headers[] ="From: <".$admin_email.">";
		
		if($num_rows < 1){
			
				//$pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
				if( filter_var($email, FILTER_VALIDATE_EMAIL) == false )
				{
						echo $msg = "<font color='#FF0000' style='font-family:arial; font-size:11px;'><strong>Email Not Valid</strong></font>"; 
					
				}else{
						$mail = wp_mail($to,$subject,$message,$headers);
						if($mail)
						{
							echo $msg = "<font color='green' style='font-family:arial;  font-size:11px;'><strong>Thanks for subscribing</strong></font>";
						}else{
							
							echo $msg = "<font color='green' style='font-family:arial;  font-size:11px;'><strong>Thanks for subscribing</strong></font>";
						}
						$wpdb->query("insert into {$wpdb->prefix}news_letter set email='".$email."', name='".$name."', create_date=Now(), status='publish' ") or die($wpdb->last_error);
				 }
		}else{
			
			echo $msg = "<font color='#FF0000' style='font-family:arial; font-size:11px;'><strong>You are already subscribed to this newsletter!</strong></font>";
		}
			
		die(); // to prevent concatent zero in return value
}

add_action('wp_ajax_newsletter_news_subcriber_mail', 'newsletter_news_subcriber_mail'); // Call when user logged in
add_action('wp_ajax_nopriv_newsletter_news_subcriber_mail', 'newsletter_news_subcriber_mail'); // Call when user in not logged in

function newsletter_news_letter($name) {
    ob_start();
    custom_newsletter_news_letter($name);
    return ob_get_clean();
}

function custom_newsletter_news_letter($name){

		$plagin_uri = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );
		$news_subcription_option = json_decode( get_option( 'news_subcription_setting' ), true );
		
?><style>
<?php echo $news_subcription_option['news_subcription_css'];?>
input{
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;	
	}
.newsletter_news_input {
 color: #666;
 line-height: 100%;
 padding: 5px;
 min-height: 36px;
 border: 2px solid #ccc;
 font-size: 14px;
}
.newsletter_btn {
 font-size: 14px;
 border: 2px solid #ccc;
 color: #666;
 text-decoration: none;
 padding: 7px;
 font-weight:bold;
}
.newsletter_btn:hover{
	text-decoration:none;
}	
</style>    
<div><?php echo !empty($name) ? $name : 'Newsletter<br/><br/>';?><div id="status"></div>
	<?php if ( $news_subcription_option['name'] ) {?>
    <input type="text" name="name" id="name" class="newsletter_news_input" placeholder="Name"/>
    <?php } else {?>
    <input type="hidden" name="name" id="name" value="name"/>
    <?php }?>
    <input type="text" name="newsletter" id="newsletter" placeholder="Email" class="newsletter_news_input"/>&nbsp;&nbsp;&nbsp;&nbsp <a href="javascript:;" onclick="return newsletter_newslettermail();" class="newsletter_btn">Subcriber</a>
    <input type="hidden" id="plugin_url" value="<?php echo $plagin_uri;?>" />
    <input type="hidden" id="ajax_url" value="<?php echo admin_url('admin-ajax.php'); ?>" />
</div>
	
<?php
}
add_shortcode('SUBSCRIPTION_NEWSLETTER','newsletter_news_letter');