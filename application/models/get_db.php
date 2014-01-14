<?php

class Get_db extends CI_Model{

	function login_model($username,$password)
	{
	   $this->db-> select('userID, username, email');
	   $this->db-> from('member_system');
	   $this->db-> where('username', $username);
	   $this->db-> where('password', MD5($password));
	   $this->db-> limit(1);

	   $query = $this->db-> get();

	   if($query -> num_rows() == 1)
	   {
	     return $query->result();
	   }
	   else
	   {
	     return false;
	   }
	 
	}
	function insertInput($data){
		$this->db->insert_batch("input", $data);
	}

	function insert1($data){
		$this->db->insert("member_system", $data);
	}

	function getLocat(){
	 	$session_data = $this->session->userdata('logged_in');

		
		$query = $this->db->query("SELECT * FROM input WHERE userID = '".$session_data['id']."'");

		return $query->result();
	}
	function getallLocate(){

		$query = $this->db->query("SELECT * FROM input ");

		return $query->result();
	}
	// function getLocatDisaster(){

	// 	$query = $this->db->query("SELECT * FROM disaster ");

	// 	return $query->result();
	// }
		function getLocatDisaster(){

		$query = $this->db->query("SELECT * FROM disaster_db ");

		return $query->result();
	}
	function updateInfowindows($inputid,$data){
		$this->db->update("input", $data, "inputID = '".$inputid."'");
		
	}

	function getuserdisater($userID){
	 	$session_data = $this->session->userdata('logged_in');
		$query = $this->db->query("SELECT * FROM member_system WHERE userID = '".$userID."'");
		return $query->result();
	}
}