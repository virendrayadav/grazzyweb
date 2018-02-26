<?php if(!defined('BASEPATH')) exit('No direct script allowed access');
class Message_model extends CI_Model
{
	
	public function get_restmessage($id){
		$query = $this->db->query("select a.*,b.restaurant_name from restaurant_messages a, restaurant b where a.restaurant_id = b.restaurant_id and a.restaurant_id=".$id." ORDER BY date DESC");
		$query1 = $this->db->query("select * from restaurant_messages where restaurant_id = 0");
		$result1 = array();
		if($query1->num_rows() > 0){
			
			$i=0;
			foreach($query1->result_array() as $row1){ 
				$result1[] = $row1;
			$i++;
			}
		}
		$result2 = array();
		if($query->num_rows() > 0){
			
			$i=0;
			foreach($query->result_array() as $row){ 
				$result2[] = $row;
			$i++;
			}
			
		}
		$result = array_merge($result1,$result2);
		if(count($result) > 0){
			return $result;
		}
		else{
			return 0;
		} 
	}
	
	
	public function get_delmessages($id){
		if($id == ''){
			$userdata = $this->session->userdata('admin');
		}else{
			$userdata['id'] = $id;
		}
		
		$where ='';
		if($this->auth->check_access('Deliver manager') == 1){
			$where.=" where delpartner_id = '".$userdata['id']."' or delpartner_id = 0 ";
		}
		if($id != ""){
			$where.=" where delpartner_id = '".$userdata['id']."' or delpartner_id = 0 ";
		}
		//echo "select * from delpartner_messages ".$where." ORDER BY date DESC "; exit;
		$query = $this->db->query("select * from delpartner_messages ".$where." ORDER BY date DESC");
		if($query->num_rows() > 0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[$i]['date'] = $row['date'];
				$result[$i]['message'] = $row['message'];
				
				$query1 = $this->db->query("select * from admin where id='".$row['delpartner_id']."'");
				if($query1->num_rows() > 0){
					$rest = $query1->result_array();
					$result[$i]['username'] = $rest[0]['username'];
				}else{
					$result[$i]['username'] = "All";
				}
				
				$result[] = $row;
			$i++;
			}
			return $result;
		}else{
			return 0;
		}
	}
	
	public function get_custmessage(){
		$query = $this->db->query("select * from customer_messages ORDER BY date DESC");
		
		if($query->num_rows() > 0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[] = $row;
			$i++;
			}
			return $result;
		}else{
			return 0;
		}
	}
	
	public function get_messagerest($data){
		$date = date('Y-m-d H:i:s');
		$rest_name = explode(") ",$data['rest_name']);
		if(isset($data['rest_nameall']) && $data['rest_nameall'] == "on"){
			$rest_name[0] = 0;
			$rest_name[1] = "";
			 $sqlrest = $this->db->query("select restaurant_email from restaurant");
			if($sqlrest->num_rows() > 0){
				foreach($sqlrest->result_array() as $row){ 
					$message="<h3>New message from Eatsapp</h3>
					<h6>".$data['message']."</h6>";
						  $config = Array(
							'protocol' => 'smtp',
							'smtp_host' => 'ssl://smtp.gmail.com',
							'smtp_port' => 465,
							'smtp_user' => 'suggest.eatsapp@gmail.com',
							'smtp_pass' => 'devang123',
							'mailtype'  => 'html', 
							'charset'   => 'iso-8859-1',
							'crlf' => "\r\n",
							'newline' => "\r\n"
						);
						$this->load->library('email',$config);
						$this->email->from('message@eatsapp.in', 'EatsApp');
						$this->email->to($row['restaurant_email']);
						$this->email->bcc('lvijetha90@gmail.com');
						$this->email->subject('EatsApp: New message');
						$this->email->message($message);
						$this->email->send();
				}
			} 
		}else{
			 $sqlrest = $this->db->query("select restaurant_email from restaurant where restaurant_id=".$rest_name[0]."");
			if($sqlrest->num_rows() > 0){
				$row = $sqlrest->result_array();
				$message="<h3>New message from Eatsapp</h3>
					<h6>".$data['message']."</h6>";
						   $config = Array(
							'protocol' => 'smtp',
							'smtp_host' => 'ssl://smtp.gmail.com',
							'smtp_port' => 465,
							'smtp_user' => 'suggest.eatsapp@gmail.com',
							'smtp_pass' => 'devang123',
							'mailtype'  => 'html', 
							'charset'   => 'iso-8859-1',
							'crlf' => "\r\n",
							'newline' => "\r\n"
						);
						$this->load->library('email',$config);
						$this->email->from('message@eatsapp.in', 'EatsApp');
						$this->email->to($row[0]['restaurant_email']);
						$this->email->subject('EatsApp: New message');
						$this->email->message($message);
						$this->email->send();
			} 
		}
		$sql = "insert into restaurant_messages (restaurant_id,rest_name, message, date) 
		values('".$rest_name[0]."','".$this->db->escape_str($rest_name[1])."',
		'".$this->db->escape_str($data['message'])."','".$date."')";	
		$query = $this->db->query($sql);
		if($this->db->insert_id()){
			return $this->db->insert_id();
		}
	}
	
	public function get_notifications(){
		$query = $this->db->query("select * from notification_message ORDER BY date DESC");
		
		if($query->num_rows() > 0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[] = $row;
			$i++;
			}
			return $result;
		}else{
			return 0;
		}
	}
	
	public function insert_notifications($data){
		$date = date('Y-m-d H:i:s');
		$sql = "insert into notification_message (message, date) 
		values('".$data['message']."','".$date."')";	
		$query = $this->db->query($sql);
		if($this->db->insert_id()){
				$id = $this->db->insert_id();
			$query1 = $this->db->query("SELECT `did` FROM `customers`");	
			if($query1->num_rows() > 0){
				$res	= $query1->result_array();
				$i=0;
				foreach($res as $result){
					$registatoin_ids[$i]=$result['did'];
				$i++;
				}
			}
			//print_r($registatoin_ids); exit;
					if(count($registatoin_ids) > 0){
						
						// $message = array("type" => "Message");  
						$message = array("msg" => $data['message']);  
						$url = 'https://android.googleapis.com/gcm/send';



						$fields = array(

						'registration_ids' => $registatoin_ids,

						'data' => $message,

						);



						$headers = array(

							'Authorization: key=AIzaSyDqoeeH8ACrf31vMWG9bs2R8QCaFkfB5ZI',

							'Content-Type: application/json'

						);

					
						$ch = curl_init();



						// Set the url, number of POST vars, POST data

						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POST, true);

						curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));


						$result = curl_exec($ch);

						if ($result === FALSE) {

							die('Curl failed: ' . curl_error($ch));

						}


						curl_close($ch);
					}
				
			
			return true;
		}
	}
	
	public function messagedel($data){
		$date = date('Y-m-d H:i:s');
		if(isset($data['delpartner_id']) && $data['delpartner_id'] == 0){
			
			$sqlrest = $this->db->query("select email from admin where access='Deliver manager'");
			if($sqlrest->num_rows() > 0){
				foreach($sqlrest->result_array() as $row){ 
					$message="<h3>New message from Eatsapp</h3>
					<h6>".$data['message']."</h6>";
						  $config = Array(
							'protocol' => 'smtp',
							'smtp_host' => 'ssl://smtp.gmail.com',
							'smtp_port' => 465,
							'smtp_user' => 'suggest.eatsapp@gmail.com',
							'smtp_pass' => 'devang123',
							'mailtype'  => 'html', 
							'charset'   => 'iso-8859-1',
							'crlf' => "\r\n",
							'newline' => "\r\n"
						);
						$this->load->library('email',$config);
						$this->email->from('message@eatsapp.in', 'EatsApp');
						$this->email->to($row['email']);
						$this->email->bcc('lvijetha90@gmail.com');
						$this->email->subject('EatsApp: New message');
						$this->email->message($message);
						$this->email->send();
				}
			} 
		}else{
		
			 $sqlrest = $this->db->query("select email from admin where id=".$data['delpartner_id']."");
			if($sqlrest->num_rows() > 0){
				$row = $sqlrest->result_array();
				
				$message="<h3>New message from Eatsapp</h3>
					<h6>".$data['message']."</h6>";
						   $config = Array(
							'protocol' => 'smtp',
							'smtp_host' => 'ssl://smtp.gmail.com',
							'smtp_port' => 465,
							'smtp_user' => 'suggest.eatsapp@gmail.com',
							'smtp_pass' => 'devang123',
							'mailtype'  => 'html', 
							'charset'   => 'iso-8859-1',
							'crlf' => "\r\n",
							'newline' => "\r\n"
						);
						$this->load->library('email',$config);
						$this->email->from('message@eatsapp.in', 'EatsApp');
						$this->email->to($row[0]['email']);
						$this->email->bcc('lvijetha90@gmail.com');
						$this->email->subject('EatsApp: New message');
						$this->email->message($message);
						 $this->email->send();
			} 
		}
		$sql = "insert into delpartner_messages (delpartner_id, message, date) 
		values('".$data['delpartner_id']."','".$data['message']."','".$date."')";	
		$query = $this->db->query($sql);
		if($this->db->insert_id()){
			return $this->db->insert_id();
		}
	}
	public function messagecust($data){
		$date = date('Y-m-d H:i:s');
		$sql = "insert into customer_messages (message, date) 
		values('".$data['message']."','".$date."')";	
		$query = $this->db->query($sql);
		
		if($this->db->insert_id()){
			$id = $this->db->insert_id();
			$query1 = $this->db->query("SELECT `did` FROM `customers`");	
			if($query1->num_rows() > 0){
				$res	= $query1->result_array();
				$i=0;
				foreach($res as $result){
					$registatoin_ids[$i]=$result['did'];
				$i++;
				}
			}
			print_r($registatoin_ids); exit;
					if(count($registatoin_ids) > 0){
						
						$message = array("type" => "Message");    
						$url = 'https://android.googleapis.com/gcm/send';



						$fields = array(

						'registration_ids' => $registatoin_ids,

						'data' => $message,

						);



						$headers = array(

							'Authorization: key=AIzaSyDqoeeH8ACrf31vMWG9bs2R8QCaFkfB5ZI',

							'Content-Type: application/json'

						);

					
						$ch = curl_init();



						// Set the url, number of POST vars, POST data

						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POST, true);

						curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));


						$result = curl_exec($ch);

						if ($result === FALSE) {

							die('Curl failed: ' . curl_error($ch));

						}


						curl_close($ch);
					}
				
			
			return true;
		}
	}
	public function get_restmessages(){
	
		$query = $this->db->query("select *  from restaurant_messages ORDER BY date DESC");
		if($query->num_rows() > 0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[$i]['date'] = $row['date'];
				$result[$i]['message'] = $row['message'];
				$query1 = $this->db->query("select * from restaurant where restaurant_id='".$row['restaurant_id']."'");
				if($query1->num_rows() > 0){
					$rest = $query1->result_array();
					$result[$i]['restaurant_name'] = $rest[0]['restaurant_name'];
				}else{
					$result[$i]['restaurant_name'] = "All";
				}
				
			$i++;
			}
			return $result;
		}else{
			return 0;
		}
	}
	
	public function get_restaurants(){
		$query = $this->db->query("select restaurant_id, restaurant_name, restaurant_branch from restaurant");
		if($query->num_rows() > 0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[] = $row['restaurant_id'].") ".$row['restaurant_name']." ".$row['restaurant_branch'];
			$i++;
			}
			return $result;
		}else{
			return 0;
		}
	}
	
	public function get_delpartners(){
		$query = $this->db->query("select id, username from admin where access= 'Deliver manager'");
		if($query->num_rows() > 0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[] = $row;
			$i++;
			}
			return $result;
		}else{
			return 0;
		}
	}
		public function get_restaurants1(){
		$query = $this->db->query("select restaurant_id,restaurant_name from restaurant");
		if($query->num_rows() > 0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[] = $row;
			$i++;
			}
			return $result;
		}else{
			return 0;
		}
	
	}
}