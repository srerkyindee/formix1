<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class site extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->size = 100;
	}
	public function index()
	{
	   if($this->session->userdata('logged_in'))
	   {
	     $session_data = $this->session->userdata('logged_in');
	     $data['username'] = $session_data['username'];
	     $this->load->view('home_view', $data);
	   }
	   else
	   {
	     //If no session, redirect to login page
	     redirect('login', 'refresh');
	   }
	}
 	
 	public function logout()
 		{
   		$this->session->unset_userdata('logged_in');
   		session_destroy();
   		redirect('login', 'refresh');
 		}
 	
 	public function process_register()
	{
		$this->load->model("user");

		if ($_POST['password'] != $_POST['password_confirm']) {
			echo "<script>alert('Password does not match please try again.')</script>";

		}
		else
		{
		$regis = array(
              'username'  	=>	$_POST['username'],
              'password'  	=> 	md5($_POST['password']),
              'email'    	=> 	$_POST['email']
              );
		print_r($regis);
		$this->user->insertRegister($regis);

		
		redirect('site');
		}
		// print_r($regis);
	}
 	public function map_supply_chain()
 	{
	   if($this->session->userdata('logged_in'))
	   {
	     $session_data = $this->session->userdata('logged_in');
	     $data['username'] = $session_data['username'];
	     $this->load->view('supply_chain_view',$data);
	   }
	   else
	   {
	     // If no session, redirect to login page
	     redirect('login', 'refresh');
	   }
 	}
 	public function map_disaster()
 	{
	   if($this->session->userdata('logged_in'))
	   {
	     $session_data = $this->session->userdata('logged_in');
	     $data['username'] = $session_data['username'];
	     $this->load->view('disaster_view',$data);
	   }
	   else
	   {
	     // If no session, redirect to login page
	     redirect('login', 'refresh');
	   }
 	}
 	public function drop()
 	{
	    	print_r($_FILES);

 		if ($_FILES["file"]["error"] > 0)
	    {
		    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	    }
	  	else
	    {
	    	// print_r($_FILES);
		    move_uploaded_file($_FILES["file"]["tmp_name"],"application/uploads/input.csv");
		    $this->readFile();
		    // echo "<script>alert('".$_FILES["file"]["name"]." was uploaded');</script>";
			// redirect('site/welcome', 'refresh');
	    }
 	}
 	function readFile(){
	 		$session_data = $this->session->userdata('logged_in');
           $filePath = 'application/uploads/input.csv';
           $rawdata = array();
           if (($handle = fopen($filePath, "r")) !== FALSE) {
           		$row = 0;
           		print_r($handle);
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	if($row > 1){
		        	$rawdata[] = array("userID" => $session_data['id'],
		        						"Header" => $data[0],
										"nodeName" => $data[1],
										"nodeType" => $data[2],
										"latitude" => $data[3],
										"longitude" => $data[4],
										"cityName" => $data[5],
										"CountryName" => $data[6],
										"Description" => $data[7]);
		        }
		        $row++;
		    }
		    fclose($handle);
		}
		print_r($rawdata);
		$this->load->model("get_db");
		$this->get_db->insertInput($rawdata);
	 }

	 public function addPin(){


	 if($this->session->userdata('logged_in'))
	 {
		  $session_data = $this->session->userdata('logged_in');



	 	$this->load->model("get_db");
	 	$data = $this->get_db->getLocat();

	 	// print_r($data);
		$this->load->library('googlemaps');
		// Initialize the map, passing through any parameters

	     $config = array();
		 $config['center'] = 'Jurong east, Singapore';
		 $config['zoom'] = 'auto';
		 $config['map_height'] = "600px";
		 $config['map_width'] = "100%";



		$this->googlemaps->initialize($config);

		// Get the co-ordinates from the database using our model
		// $coords = $this->map_model->get_coordinates();
		// Loop through the coordinates we obtained above and add them to the map
		foreach ($data as $coordinate) {
			$marker = array();
			$marker['position'] = $coordinate->latitude.','.$coordinate->longitude;
			$marker['infowindow_content'] = 

			'<input type="hidden" name="inputid" id="inputid" value="'.$coordinate->inputid.'">'.
			'<b>Node:</b> '.$coordinate->Header.
			'<br /><b>Name:</b> '.$coordinate->nodeName.
			'<br /><b>Type:</b> <input type="text" name="nodeType" id="nodeType" value="'.$coordinate->nodeType.'">'.
			'<br /><b>City:</b> '.$coordinate->cityName.
			'<br /><b> Country:</b> '.$coordinate->CountryName.
			'<br /><b>Description:</b> <input type="text" name="Description" id="Description" value="'.$coordinate->Description.'">'.
			'<br /><input type="button" value="Save & Close" onclick="saveData()"/>';

			$this->googlemaps->add_marker($marker);
		}

		// 	$dataB = $this->get_db->getLocatDisaster();
		
		// foreach ($dataB as $coordinate) {
		// 	$marker = array();
		// 	$marker['position'] = $coordinate->Lat.','.$coordinate->Long;
		// 	$marker['animation'] = 'DROP';
		// 	$marker['infowindow_content'] =  '<b>Type:</b> '.$coordinate->Type.'<br /><b>Alert level:</b>'.$coordinate->AlertLV.
		// 	'<br /><b>Date:</b> '.$coordinate->DateInfo.
		// 	'<br /><b>Magnitude:</b> '. $coordinate->Magnitude.'M'.'<b> Depth:</b>'. $coordinate->Depth.'km'.'<b> Speed:</b>'. $coordinate->Speed.'km/h'.
		// 	'<br /><b>Latitude:</b> '.$coordinate->Lat.
		// 	'<br /><b>longitude:</b> '.$coordinate->Long;
		// 	$this->googlemaps->add_marker($marker);

		// 	$circle = array();
		// 	$circle['center'] = $coordinate->Lat.','.$coordinate->Long;
		// 	$circle['radius'] = $this->size*1000;
		// 	$circle['fillColor'] = "#ff0000";
		// 	$circle['strokeColor'] = "#ff0000";
		// 	$circle['strokeOpacity'] = "0";
			
		// 	$this->googlemaps->add_circle($circle);
		// }	

		// Create the map
		$data = array();
		$data['username'] = $session_data['username'];
		$data['map'] = $this->googlemaps->create_map();

		 $this->load->view('supply_chain_view', $data);

	 	// print_r($data);
	 }
	 else
	 {
		     //If no session, redirect to login page
		     redirect('site/', 'refresh');
	 }
	}

		 public function addPin2(){


	 if($this->session->userdata('logged_in'))
	 {
		  $session_data = $this->session->userdata('logged_in');



	 	$this->load->model("get_db");
	 	$data = $this->get_db->getLocat();

	 	// print_r($data);
		$this->load->library('googlemaps');
		// Initialize the map, passing through any parameters

	     $config = array();
		 $config['center'] = 'Jurong east, Singapore';
		 $config['zoom'] = 'auto';
		 $config['map_height'] = "600px";
		 $config['map_width'] = "100%";
		 

		$this->googlemaps->initialize($config);

		// Get the co-ordinates from the database using our model
		// $coords = $this->map_model->get_coordinates();
		// Loop through the coordinates we obtained above and add them to the map
		foreach ($data as $coordinate) {
			$marker = array();
			$marker['position'] = $coordinate->latitude.','.$coordinate->longitude;
			$marker['infowindow_content'] = 

			'<input type="hidden" name="inputid" id="inputid" value="'.$coordinate->inputid.'">'.
			'<b>Node:</b> '.$coordinate->Header.
			'<br /><b>Name:</b> '.$coordinate->nodeName.
			'<br /><b>Type:</b> <input type="text" name="nodeType" id="nodeType" value="'.$coordinate->nodeType.'">'.
			'<br /><b>City:</b> '.$coordinate->cityName.
			'<br /><b> Country:</b> '.$coordinate->CountryName.
			'<br /><b>Description:</b> <input type="text" name="Description" id="Description" value="'.$coordinate->Description.'">'.
			'<br /><input type="button" value="Save & Close" onclick="saveData()"/>';

			$this->googlemaps->add_marker($marker);
		}

			$dataB = $this->get_db->getLocatDisaster();
		
		foreach ($dataB as $coordinate) {
			$marker = array();
			$marker['position'] = $coordinate->Lat.','.$coordinate->Long;
			$marker['animation'] = 'DROP';
			$marker['infowindow_content'] =  '<b>Type:</b> '.$coordinate->Type.'<br /><b>Alert level:</b>'.$coordinate->AlertLV.
			'<br /><b>Date:</b> '.$coordinate->DateInfo.
			'<br /><b>Magnitude:</b> '. $coordinate->Magnitude.'M'.'<b> Depth:</b>'. $coordinate->Depth.'km'.'<b> Speed:</b>'. $coordinate->Speed.'km/h'.
			'<br /><b>Latitude:</b> '.$coordinate->Lat.
			'<br /><b>longitude:</b> '.$coordinate->Long;
			$this->googlemaps->add_marker($marker);

			$circle = array();
			$circle['center'] = $coordinate->Lat.','.$coordinate->Long;
			$circle['radius'] = $this->size*1000;
			$circle['fillColor'] = "#ff0000";
			$circle['strokeColor'] = "#ff0000";
			$circle['strokeOpacity'] = "0";
			
			$this->googlemaps->add_circle($circle);
		}	

		// Create the map
		$data = array();
		$data['username'] = $session_data['username'];
		$data['map'] = $this->googlemaps->create_map();

		 $this->load->view('disaster_view', $data);

	 	// print_r($data);
	 }
	 else
	 {
		     //If no session, redirect to login page
		     redirect('site/', 'refresh');
	 }
	}

}