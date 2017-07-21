<?php
/*
 * Plugin Name: Appointment Adjustment Plugin
 * Version: 1.1
 * Description: This is a custom appoinment plugin for IJSoft
 * Author: OD Base 
 * Author URI: #
 * License: Plugin comes under GPL Licence.
 */
 
include( plugin_dir_path( __FILE__ ) . 'functions.php');
 
 
add_action('admin_init', 'input_admin_enqueue_scripts');

function input_admin_enqueue_scripts() {

	// Chosen
	wp_register_script('chosen_script', plugins_url('chosen/chosen.jquery.js', __FILE__));
	wp_register_script('custom_script', plugins_url('custom_script.js', __FILE__));
	wp_register_style('chosen_style', plugins_url('chosen/chosen.css', __FILE__));

	// scripts
	wp_enqueue_script('custom_script');
	wp_enqueue_script('chosen_script');
	wp_enqueue_style('chosen_style');
	
}

 
add_action('show_user_profile', 'extra_user_profile_fields');
add_action('edit_user_profile', 'extra_user_profile_fields');
add_action('user_new_form', 'extra_user_profile_fields');

function extra_user_profile_fields($user) {
  ?>
  <div class="custom_fields_client">
	<style>
		table#add_staff tr th {
			border-bottom: 1px solid #000 !important;
			padding: 10px 25px !important;
		}
		table#add_staff tr td {
			border-bottom: 1px solid #ccc !important;
			padding: 10px !important;
			text-transform: capitalize;
		}
	</style>
	
		<?php 
			global $pagenow;
			if ( $pagenow == 'user-edit.php' ) { ?>
			<?php 
	// Your CODE with user data
	$userp = $_GET['user_id'];
	$user_info = get_userdata( $userp ); 
	$user_role = implode(', ', $user_info->roles);
	// Your CODE with user capability check
	if ( $user_role == 'customer' ) { ?> 
		<table class="form-table emp">
		  <tr>
			<th><label for="category"><?php _e("Category"); ?></label></th>
			<td>
			  <?php
			  global $wpdb;
			  $prod_cat_args = array(
				  'taxonomy'     => 'product_cat', //woocommerce
				  'orderby'      => 'name',
				  'empty'        => 0
				);

				$woo_categories = get_categories( $prod_cat_args );
			 
					
			  ?>
			  <select name="odb_category" id="odb_category" required class="">
				<option value="">Please select Category</option>
				<?php
					foreach ($woo_categories as $master) {
						$selected = ($master->term_id == get_the_author_meta('odb_category', $user->ID)) ? 'selected' : '';
						?>
						<option value="<?php echo $master->term_id; ?>" <?php echo $selected; ?>><?php echo $master->name; ?></option>
						<?php
					}
					?>

			  </select>
			  <br>
			</td>
		  </tr>
		</table>
	<?php } ?>
			<label><h3>Please Check your Services.</h3></label>
		
			<table id="add_staff">
				<thead>
					<tr>
						<th>Select</th>
						<th>Service Name</th>
						<th>Number of Services</th>
						<th>Employee Name</th>
					</tr>
				</thead>
				<tbody>
					<?php
						global $wpdb;
						$userp = $_GET['user_id']; 
						// Your CODE with user data
						$user_info = get_userdata( $userp ); 
						$user_role = implode(', ', $user_info->roles);
						$table_name = $wpdb->prefix . "app_services";
						// Your CODE with user capability check
						if ( $user_role == 'customer' ) {
							$get_services = $wpdb->get_results("SELECT * FROM $table_name where user_id=$userp");
						} else {
							$get_services = $wpdb->get_results("SELECT * FROM $table_name where emp_name=$userp");
						}
						// echo'<pre>';
						// print_r($get_services);
						// echo'</pre>';
						$i=1;
						foreach ($get_services as $get){ ?>
						<?php $user_info = get_userdata( $get->emp_name ); ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $get->service_name; ?></td>
								<td><?php echo $get->number_service; ?></td>
								<td><?php echo $user_info->user_login; ?></td>
							</tr>
						
					<?php $i++; 	}
					?>
				</tbody>
			</table>
		<?php	} else {
		?>
		<style>
		.custom_fields_client {
    display: none;
}
.chosen-container {
    min-width: 200px;
}
		</style>
		
    <table class="form-table emp">
    <?php 
		$roles = $user->roles[0];
	?>
      <tr>
        <th><label for="category"><?php _e("Category"); ?></label></th>
        <td>
          <?php
          global $wpdb;
		  $prod_cat_args = array(
			  'taxonomy'     => 'product_cat', //woocommerce
			  'orderby'      => 'name',
			  'empty'        => 0
			);

			$woo_categories = get_categories( $prod_cat_args );
		  
          ?>
			<select name="odb_category" id="odb_category" required class="">
            <option value="">Please select Category</option>
            <?php
				foreach ($woo_categories as $master) {
					$selected = ($master->term_id == get_the_author_meta('odb_category', $user->ID)) ? 'selected' : '';
					?>
					<option value="<?php echo $master->term_id; ?>" <?php echo $selected; ?>><?php echo $master->name; ?></option>
					<?php
				}
            ?>
			</select>
          <br>
          <span class="description"><?php _e("Please select category."); ?></span>
        </td>
      </tr>
    </table>
		<label>
			<h3>Please Select Services.</h3>
		</label>
		<table class="text_cpl">
			<tr>
				<td>
					
					<?php
						$category = get_the_author_meta('odb_category', $user->ID);
						$services = $wpdb->get_results("SELECT 
							ID, post_title AS title FROM $wpdb->posts p
							JOIN $wpdb->term_relationships tr ON (p.ID = tr.object_id)
							JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
							JOIN $wpdb->terms t ON (tt.term_id = t.term_id)
							WHERE p.post_type='product'
							AND p.post_status = 'publish'
							AND tt.taxonomy = 'product_cat'
							AND t.term_id = $category
							ORDER BY post_date DESC"
						);
						//$services = $wpdb->get_results("SELECT id,title FROM $table_name  where service_parent=$category ");
						$service = get_the_author_meta('odb_service', $_GET['user_id']);
						$service_array = explode(",", $service);
						//$category = get_the_author_meta( 'odb_category', $user->ID );
						//print_r($services);
					?>
					
					<select name="odb_service[]" id="service" required class="odb_services">

						<option value="">Please select Service</option>
						
						<?php
						foreach ($services as $service) {

						  if (in_array($service->id, $service_array)) {
							$selected = 'selected';
						  } else {
							$selected = 'false';
						  }

						  echo "<option value='$service->id' $selected>$service->title</option>";
						  
						}
						?> 
						
					</select>
				</td>
				<td>
					<input type="text" id="number_services" placeholder="Add number of Services">
				</td>
				<td>
					<select name="emp_name[]" id="emp_name" required class="emp_names">

						<option value="">Please select Employee</option>

					</select>
				</td>
				<td>
					<input type="button" class="add-row" value="Add Row">
				</td>
			</tr>
		</table>
	 <table id="add_staff">
        <thead>
            <tr>
                <th>Select</th>
                <th>Service Name</th>
                <th>Number of Services</th>
                <th>Employee Name</th>
            </tr>
        </thead>
        <tbody>
			
        </tbody>
    </table>
    <button type="button" class="delete-row">Delete Row</button>
			<?php } ?>
	

  </div>


  <?php
}

function save_custom_user_profile_fields($user_id) {
  # again do this only if you can
  if (!current_user_can('manage_options'))
    return false;
  global $wpdb;
  //print_r($_POST['service_name']);
  echo $count_service = count($_POST['service_name']);
  // die();
  get_userdata( $userid );

  update_usermeta($user_id, 'odb_category', $_POST['odb_category']);
  
  for($i=0;$i<$count_service;$i++){
	$number_service[] = $_POST['number_service'][$i];
  }
  //print_r($number_service);
  $total_number_services = array_sum($number_service);
  //die();
  if($total_number_services){
	update_usermeta($user_id, 'total_number_services', $total_number_services);
  }
  
  for($i=0;$i<$count_service;$i++){
	
	$table_name = $wpdb->prefix . "app_services";
	  $wpdb->insert( 
			$table_name, 
			array( 
				'odb_category' => $_POST['odb_category'], 
				'service_id' => $_POST['service_id'][$i] ,
				'service_name' => $_POST['service_name'][$i] ,
				'number_service' => $_POST['number_service'][$i] ,
				'emp_name' => $_POST['empname'][$i] ,
				'user_id' => $user_id
			)
		);
  }
  
//die();
//echo $_POST['odb_emps'];

}

add_action('user_register', 'save_custom_user_profile_fields');

add_action('edit_user_profile_update', 'save_custom_user_profile_fields');

// Add WooCommerce MyAccount Section

function iconic_account_menu_items( $items ) {
	
	$items['listservices'] 	= __( 'List of Services', 'iconic' );
		
	return $items;

}

add_filter( 'woocommerce_account_menu_items', 'iconic_account_menu_items', 10, 1 );


/**
 * Add endpoint
 */
function iconic_add_my_account_endpoint() {

	add_rewrite_endpoint( 'listservices', EP_PAGES );

}

add_action( 'init', 'iconic_add_my_account_endpoint' );


function iconic_listservices_endpoint_content() {
	?>
		<label><h3>Please Check your Services.</h3></label>
		
			 <table id="add_staff">
				<thead>
					<tr>
						<th>S. No.</th>
						<th>Service Name</th>
						<th>Number of Services</th>
						<th>Employee Name</th>
						<th>Book Your service</th>
					</tr>
				</thead>
				<tbody>
					<?php
						global $wpdb;
						$current_user = wp_get_current_user();
						//echo $current_user->ID;
						$userp = $current_user->ID;
						$table_name = $wpdb->prefix . "app_services";
						$get_services = $wpdb->get_results("SELECT * FROM $table_name where user_id=$userp");
						// echo'<pre>';
						// print_r($get_services);
						// echo'</pre>';
						$i=1;
						foreach ($get_services as $get){ ?>
						<?php $user_info = get_userdata( $get->emp_name ); ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $get->service_name; ?></td>
								<td><?php echo $get->number_service; ?></td>
								<td><?php echo $user_info->user_login; ?></td>
								<td><a href="<?php echo get_permalink($get->service_id); ?>" target="_blank">Book</a></td>
							</tr>
						
					<?php $i++; 	}
					?>
				</tbody>
			</table>
	<?php 
}

add_action( 'woocommerce_account_listservices_endpoint', 'iconic_listservices_endpoint_content' );

function add_jscript() {
	$user = wp_get_current_user();
	$user_id = $user->ID;
	echo $total_number_services 	= get_user_meta( $user_id, 'total_number_services', 'true' ); 
	echo $add_servicestocarts 		= get_user_meta( $user_id, 'add_servicestocart', 'true' ); 
	
	if($total_number_services != $add_servicestocarts){
		wp_redirect( home_url( "cart" ) );
	}
}
 
add_action( 'woocommerce_before_checkout_form', 'add_jscript');

function action_woocommerce_add_to_cart() {
	
	$user = wp_get_current_user();
	$user_id = $user->ID;
	
    $add_servicestocart = get_user_meta($user_id, 'add_servicestocart', true);
	
	if($add_servicestocart){
		$add_servicestocart_more = $add_servicestocart+1;
		update_usermeta($user_id, 'add_servicestocart', $add_servicestocart_more);
	} else {
		update_usermeta($user_id, 'add_servicestocart', 1);
	}
	//die();
}
add_action('woocommerce_add_to_cart', 'action_woocommerce_add_to_cart' ); 

function sp_custom_notice() {
	$user = wp_get_current_user();
	$user_id = $user->ID;
	$total_number_services 	= get_user_meta( $user_id, 'total_number_services', 'true' ); 
	$add_servicestocarts 		= get_user_meta( $user_id, 'add_servicestocart', 'true' ); 
	
	if($total_number_services != $add_servicestocarts){
		wc_add_notice( "Please checkout all the services to proceed to checkout, which assigned by admin. Please check your services here - <a href='".home_url( "my-account/listservices/" )."'>click here</a>", 'error' );
	}
}
add_action( 'woocommerce_before_cart', 'sp_custom_notice' );

// define the woocommerce_remove_cart_item callback 
function action_woocommerce_remove_cart_item( $cart_item_key, $instance ) { 
    
	$user = wp_get_current_user();
	$user_id = $user->ID;
	
    echo $add_servicestocart = get_user_meta($user_id, 'add_servicestocart', true);
	
	if($add_servicestocart){
		$add_servicestocart_more = $add_servicestocart-1;
		update_usermeta($user_id, 'add_servicestocart', $add_servicestocart_more);
	} else {
		delete_user_meta($user_id, 'add_servicestocart');
	}
}; 
         
// add the action 
add_action( 'woocommerce_remove_cart_item', 'action_woocommerce_remove_cart_item', 10, 2 ); 
/* 
function remove_item_from_cart() {
	alert('Hello');
	console.log('Hello');
	die();
}

add_action('wp_ajax_remove_item_from_cart', 'remove_item_from_cart');
add_action('wp_ajax_nopriv_remove_item_from_cart', 'remove_item_from_cart');
/**
* WooCommerce: show all product attributes, separated by comma, on cart page
*/
/* 
function isa_woo_cart_attribute_values( $cart_item, $cart_item_key ) {
	global $wpdb;
	$current_user = wp_get_current_user();
	$product_id = $cart_item_key['product_id'];
	$userp = $current_user->ID;
	$table_name = $wpdb->prefix . "app_services";
	$get_services = $wpdb->get_results("SELECT emp_name FROM $table_name where user_id=$userp and service_id=$product_id");
	$emp_name = $get_services[0]->emp_name;
	$user_info = get_userdata($emp_name);
    echo '<b>Employee: </b>' . $user_info->user_login . "\n";
}
add_filter( 'woocommerce_cart_item_name', isa_woo_cart_attribute_values, 10, 2 ); */