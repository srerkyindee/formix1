<?php
Class User extends CI_Model
{
 function login($username, $password)
 {
   $this -> db -> select('userID, username, password,email');
   $this -> db -> from('member_system');
   $this -> db -> where('username', $username);
   $this -> db -> where('password', MD5($password));
   $this -> db -> limit(1);

   $query = $this -> db -> get();

   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }
 }

  function insertRegister($data){
    $this->db->insert("member_system", $data);
  }
}
?>

