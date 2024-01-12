<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'wp-load.php');

class ils_pre_data_query{

	private $table;
	
	public function __construct(){
	  global $wpdb;
	  $this->table = $wpdb->prefix.'newsletter_requests';
	}
	
	/*
	* Get All Records
	* Added on: 30/07/2019
	*/
	public function getAllRecords($limit = 0) {
	  global $wpdb;
	  $sql = "SELECT $this->table.id,$this->table.gt_email,$this->table.ip_addr,$this->table.added_on FROM {$this->table}";
	  if($limit > 0){
	  	$sql.= " LIMIT 0, $limit ";
	  }
	  $sql.=" WHERE $this->table.flag = 0 ORDER BY $this->table.id DESC";
	  $result = $wpdb->get_results( $sql, 'ARRAY_A' );
	  return $result;
	}
	/*
	* Get Record by ID
	* Added on: 30/07/2019
	*/
	public function getRecordById($id = NULL){
	  global $wpdb;
	  $sql = "SELECT $this->table.id,$this->table.gt_email,$this->table.ip_addr,$this->table.added_on FROM {$this->table}";
	  $sql.=" WHERE $this->table.flag = 0 AND $this->table.id = '{$id}'" ;
	  $result = $wpdb->get_row( $sql, 'ARRAY_A' );
	  return $result;	
	}
	/*
	* Delete a record
	* Added on: 30/07/2019
	*/
	public function removeById($id = NULL){
		global $wpdb;
		$sql = "UPDATE  {$this->table} SET flag=%d,updated_on=NOW() WHERE id=%d";
		//print_r($sql); exit;
		$update = $wpdb->query($wpdb->prepare($sql,2,$id));
		return $update;	
	}
}
?>