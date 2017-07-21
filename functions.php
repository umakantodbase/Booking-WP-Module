<?php

function odb_get_service_by_category() {

global $wpdb;

	$category_id = $_POST['odb_category'];
	$services = $wpdb->get_results("SELECT 
		ID, post_title AS title FROM $wpdb->posts p
		JOIN $wpdb->term_relationships tr ON (p.ID = tr.object_id)
		JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
		JOIN $wpdb->terms t ON (tt.term_id = t.term_id)
		WHERE p.post_type='product'
		AND p.post_status = 'publish'
		AND tt.taxonomy = 'product_cat'
		AND t.term_id = $category_id
		ORDER BY post_date DESC"
	);

	  echo json_encode(array('value' => $services));
	  die();
}

add_action('wp_ajax_odb_get_service_by_category', 'odb_get_service_by_category');
add_action('wp_ajax_nopriv_odb_get_service_by_category', 'odb_get_service_by_category');

function odb_get_employee() {

global $wpdb;

	$employees = $wpdb->get_results("SELECT 1qz5g_users.ID, 1qz5g_users.user_nicename 
FROM 1qz5g_users INNER JOIN 1qz5g_usermeta 
ON 1qz5g_users.ID = 1qz5g_usermeta.user_id 
WHERE 1qz5g_usermeta.meta_key = '1QZ5g_capabilities' 
AND 1qz5g_usermeta.meta_value LIKE '%shop_staff%' 
ORDER BY 1qz5g_users.user_nicename"
	);

	  echo json_encode(array('value' => $employees));
	  die();
}

add_action('wp_ajax_odb_get_employee', 'odb_get_employee');
add_action('wp_ajax_nopriv_odb_get_employee', 'odb_get_employee');