<?php
/**
 * Login User Time
*/

//update login user time on db
add_action( 'wp_login', 'wplalr_collect_user_login_timestamp', 20, 2 );
function wplalr_collect_user_login_timestamp( $user_login, $user ) {

    update_user_meta( $user->ID, 'wplalr_last_login', time() );

}



//Add last login column on admin dashboard users table
add_filter( 'manage_users_columns', 'wplalr_add_user_table_column' );
function wplalr_add_user_table_column( $columns ) {
 
	$columns['wplalr_last_login'] = 'Last Login'; // column ID / column Title
	return $columns;
 
}


//Retrive Value of each user last login time and show on our added user table column
add_filter( 'manage_users_custom_column', 'wplalr_user_last_login_time', 10, 3 );
function wplalr_user_last_login_time( $output, $column_id, $user_id ){
 
	if( $column_id == 'wplalr_last_login' ) {

		$wplalr_last_login = get_user_meta( $user_id, 'wplalr_last_login', true );
		$wplalr_date_format = get_option( 'date_format' ) .' '. get_option( 'time_format' );
 
		$wplalr_output = $wplalr_last_login ? date( $wplalr_date_format, $wplalr_last_login ) : '-';
 
	}
 
	return $wplalr_output;
 
}


//Making the Last Login Column Sortable
add_filter( 'manage_users_sortable_columns', 'wplalr_user_login_time_sortable_columns' );
add_action( 'pre_get_users', 'wplalr_sort_user_last_login_column' );
 
function wplalr_user_login_time_sortable_columns( $columns ) {
 
	return wp_parse_args( array(
	 	'wplalr_last_login' => 'wplalr_last_login'
	), $columns );
 
}
 
function wplalr_sort_user_last_login_column( $query ) {
 
	if( !is_admin() ) {
		return $query;
	}
 
	$screen = get_current_screen();
 
	if( isset( $screen->id ) && $screen->id !== 'users' ) {
		return $query;
	}
 
	if( isset( $_GET[ 'orderby' ] ) && $_GET[ 'orderby' ] == 'wplalr_last_login' ) {
 
		$query->query_vars['meta_key'] = 'wplalr_last_login';
		$query->query_vars['orderby'] = 'meta_value';
 
	}
 
	return $query;
 
}