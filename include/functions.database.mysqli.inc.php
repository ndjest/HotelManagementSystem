<?php
/**
* @project uHotelBooking
* @copyright (c) 2018 ApPHP
* @author ApPHP <info@apphp.com>
* @site http://www.hotel-booking-script.com
* @license http://hotel-booking-script.com/license.php
*/

// DATABASE FUNCTIONS 27.05.2011

// setup connection
//------------------------------------------------------------------------------
$database_connection = @mysqli_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if(mysqli_connect_errno()){  
    $error_content = @file_get_contents('html/site_error.html');
    if(!empty($error_content)){
        $error_content = str_ireplace(
            array('{HEADER_TEXT}', '{ERROR_SIGNATURE}', '{ERROR_MESSAGE}'),
            array('System Fatal Error', 'Database connection', (SITE_MODE == 'development') ? 'Reporting details:<br>'.mysqli_connect_error() : 'Please check your database connection parameters!'),
            $error_content
        );                    
    }else{
        $error_content = 'System Fatal Error: '.mysqli_connect_error();
    }
    echo $error_content;
    exit;
}
// set collation
set_collation();
// set group_concat max length
set_group_concat_max_length();
/// set sql_mode to empty if you have Mixing of GROUP columns SQL issue
///set_sql_mode();

/**
 * Database query
 * 		@param $sql
 * 		@param $return_type
 * 		@param $first_row_only
 * 		@param $fetch_func
 * 		@param $debug
 */
function database_query($sql, $return_type = DATA_ONLY, $first_row_only = ALL_ROWS, $fetch_func = FETCH_ASSOC, $debug=false)
{
	global $database_connection;
	$data_array = array();
	$num_rows = 0;
	$fields_len = 0;
	if($fetch_func == 'mysqli_fetch_assoc') $fetch_func = 'mysqli_fetch_assoc';
	else if($fetch_func == 'mysqli_fetch_array') $fetch_func = 'mysqli_fetch_array';

	$result = mysqli_query($database_connection, $sql);
	if($debug == true) echo $sql.'-'.database_error();
	if($result){
		if($return_type == 0 || $return_type == 2){
			while($row_array = $fetch_func($result)){
				if(!$first_row_only){
					array_push($data_array, $row_array);
				}else{
					$data_array = $row_array;
					break;
				}
			}
		}		
		
		$num_rows = mysqli_num_rows($result);
		$fields_len = mysqli_num_fields($result);
		mysqli_free_result($result);
	}
	
	switch($return_type){
		case DATA_ONLY:
			return $data_array;
		case ROWS_ONLY:
			return $num_rows;
		case DATA_AND_ROWS:
			return array($data_array, $num_rows);
		case FIELDS_ONLY:
			return $fields_len;
	}	
}


/**
 * Database void query
 * 		@param $sql
 * 		@param $debug
 * 		@param $zero_affected
 */
function database_void_query($sql, $debug = false, $zero_affected = true)
{
	global $database_connection;
	$result = mysqli_query($database_connection, $sql);	
	if($debug == true) echo $sql.' - '.database_error();
	$affected_rows = mysqli_affected_rows($database_connection);
	if(preg_match('/update /i', $sql)){
		if($zero_affected && $affected_rows >= 0) return true;
		if(!$zero_affected && $affected_rows > 0) return true;
	}else if(preg_match('/drop t/i', $sql)){
		if($affected_rows >= 0) return true;
	}else if(preg_match('/create t/i', $sql)){
		if($affected_rows >= 0) return true;
	}else if($affected_rows > 0){ 
		return true;
	}
	return false;
}

/**
 * Set collation
 */
function set_collation()
{
	$encoding = 'utf8';
	$collation = 'utf8_unicode_ci';
	
	$sql_variables = array(
		'character_set_client'  =>$encoding,
		'character_set_server'  =>$encoding,
		'character_set_results' =>$encoding,
		'character_set_database'=>$encoding,
		'character_set_connection'=>$encoding,
		'collation_server'      =>$collation,
		'collation_database'    =>$collation,
		'collation_connection'  =>$collation
	);

	foreach($sql_variables as $var => $value){
		$sql = 'SET '.$var.'='.$value.';';
		database_void_query($sql);
	}        
}

/**
 * Set group_concat maximal length
 */
function set_group_concat_max_length()
{
	database_void_query('SET SESSION group_concat_max_len = 1024');	
}

/**
 * Set sql_mode
 */
function set_sql_mode()
{
	database_void_query('SET sql_mode = ""');
}

/**
 * Return database error
 */
function database_error(){
	global $database_connection;
	return mysqli_error($database_connection);
}

/**
 * Return database last inset ID
 */
function database_insert_id(){
	global $database_connection;
	return mysqli_insert_id($database_connection);
}
