<?php
Class order_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_gross_monthly_sales($year)
	{
		$this->db->select('SUM(coupon_discount) as coupon_discounts');
		$this->db->select('SUM(gift_card_discount) as gift_card_discounts');
		$this->db->select('SUM(subtotal) as product_totals');
		$this->db->select('SUM(shipping) as shipping');
		$this->db->select('SUM(tax) as tax');
		$this->db->select('SUM(total) as total');
		$this->db->select('YEAR(ordered_on) as year');
		$this->db->select('MONTH(ordered_on) as month');
		$this->db->group_by(array('MONTH(ordered_on)'));
		$this->db->order_by("ordered_on", "desc");
		$this->db->where('YEAR(ordered_on)', $year);
		
		return $this->db->get('orders')->result();
	}
	
	function get_sales_years()
	{
		$this->db->order_by("ordered_on", "desc");
		$this->db->select('YEAR(ordered_on) as year');
		$this->db->group_by('YEAR(ordered_on)');
		$records	= $this->db->get('orders')->result();
		$years		= array();
		foreach($records as $r)
		{
			$years[]	= $r->year;
		}
		return $years;
	}
	
	function get_newordersForAdmin(){
		$date = date("Y-m-d 00:00:00");
		$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE a.`status` = 'Order placed' and a.`restaurant_id` = b.restaurant_id 
		and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.ordered_on >='".$date."'");
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	function get_neworders(){
		$userdata = $this->session->userdata('admin');
		$date = date("Y-m-d 00:00:00");
		// echo "SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE  a.`restaurant_id` = b.restaurant_id 
		// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_manager='".$userdata['id']."' and a.ordered_on >='".$date."' order by a.ordered_on desc"; exit;
		// $sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,b.preparation_time FROM `orders` a, restaurant b, order_type d, admin c WHERE  a.`restaurant_id` = b.restaurant_id 
		// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_manager='".$userdata['id']."' and a.status IN ('Order Placed', 'Assigned','Accepted','Picked Up')  and a.ordered_on >='".$date."' order by a.ordered_on desc");
		$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,b.preparation_time,
			e.firstname,e.phone,c.enabled FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id  and a.`customer_id` = e.id  
		and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_manager='".$userdata['id']."' and a.status IN ('Order Placed', 'Assigned','Accepted','Picked Up')  order by a.ordered_on desc");
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	
	function get_delpartnerorders(){
		$userdata = $this->session->userdata('admin');
		$date = date("Y-m-d 00:00:00");
		
		// $sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE a.`restaurant_id` = b.restaurant_id 
		// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.restaurant_manager_status = 'Accepted' and a.status!='Shipped' and a.status!='Rejected' and a.status!='order cancelled' and a.order_type != 3 and a.ordered_on >= '".$date."' and a.delivery_partner != '123' and a.delivery_partner=".$userdata['id']." order by a.ordered_on desc");

		$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE a.`restaurant_id` = b.restaurant_id 
		and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.restaurant_manager_status = 'Accepted' and a.status!='Shipped' and a.status!='Rejected' and a.status!='order cancelled' and a.order_type != 3 and a.delivery_partner != '123' and a.delivery_partner=".$userdata['id']." order by a.ordered_on desc");

		// echo "SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE a.`restaurant_id` = b.restaurant_id 
		// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.restaurant_manager_status = 'Accepted' and a.status!='Shipped' and a.status!='Rejected' and a.order_type != 3 and a.ordered_on >= '".$date."' and a.delivery_partner != '123'  and a.delivery_partner=".$userdata['id']." order by a.ordered_on desc";exit;
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	
	function AssignDeliveryBoy($data){
		$userdata = $this->session->userdata('admin');
		$sql1=$this->db->query("SELECT * FROM orders a,delivery_boy b where a.id='".$data['id']."' and a.delivered_by=b.id ");
		//print_r($sql1->num_rows());exit;
		$sql2=$this->db->query("SELECT * FROM delivery_boy  where id='".$data['delBoy']."' ");
			$result1= $sql2->result_array();
			$name=$result1[0]['name'];
		if($sql1->num_rows()==0){
			
		
		$sql = $this->db->query("update orders set restaurant_manager ='".$userdata['id']."', delivered_by='".$data['delBoy']."',delivered_name='".$name."', status='Assigned' where id='".$data['id']."'");
		
            if($sql){ 
			$query = $this->db->query("SELECT `did` FROM `delivery_boy` WHERE `id` = '".$data['delBoy']."'");	
			if($query->num_rows() > 0){					
				$result	= $query->result_array();
				
				$did=$result[0]['did'];
				$registatoin_ids = array($did);
				$message = array("type" => "order_updated");    
				$url = 'https://android.googleapis.com/gcm/send';



				$fields = array(

				'registration_ids' => $registatoin_ids,

				'data' => $message,

				);



				$headers = array(

					// 'Authorization: key=AIzaSyCB4r56wVzKQdte4Rw8QUwoK9k7AMP0fr4',
					'Authorization: key=AIzaSyDdeH0_2r-RJMpMn05pb-0qNF8rM1UlwAM',

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
	}else
	{
		
        $sql = $this->db->query("update orders set restaurant_manager ='".$userdata['id']."', delivered_by='".$data['delBoy']."',delivered_name='".$name."', status='Assigned' where id='".$data['id']."'");
		


		if($sql){ 
			$query = $this->db->query("SELECT `did` FROM `delivery_boy` WHERE `id` = '".$data['delBoy']."'");	
			if($query->num_rows() > 0){					
				$result	= $query->result_array();
				$result1= $sql1->result_array();
				//print_r($result);exit;
				$did=$result[0]['did'];
				$did1=$result1[0]['did'];
				//$did=$result[0]['did'];
				$registatoin_ids = array($did,$did1);
				//print_r($registatoin_ids);exit;

				$message = array("type" => "order_assigned");    
				$url = 'https://android.googleapis.com/gcm/send';



				$fields = array(

				'registration_ids' => $registatoin_ids,

				'data' => $message,

				);



				$headers = array(

					// 'Authorization: key=AIzaSyCB4r56wVzKQdte4Rw8QUwoK9k7AMP0fr4',

					'Authorization: key=AIzaSyDdeH0_2r-RJMpMn05pb-0qNF8rM1UlwAM',

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
	}
	
	function get_deliveryboys(){
		$userdata = $this->session->userdata('admin');
		// $sql = $this->db->query("select * from delivery_boy where delivery_partner='".$userdata['id']."'");
		$sql = $this->db->query("select * from delivery_boy where restaurant_manager='".$userdata['id']."'");
		//echo "select * from delivery_boy where restaurant_manager='".$userdata['id']."' ";exit;
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	function get_admin($id){
		$sql = $this->db->query("select * from admin where id='".$id."'");
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	function get_deliverypartnerneworders(){
		$userdata = $this->session->userdata('admin');
		$date = date("Y-m-d");
		$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE a.`status` = 'Order placed' and a.`restaurant_id` = b.restaurant_id 
		and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.order_type != 3 and a.ordered_on='".$date."' and a.delivery_partner = ''");
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	function get_previousorders($data){
		
		$userdata = $this->session->userdata('admin');
		if($this->auth->check_access('Restaurant manager')){
		
			$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,e.firstname,e.phone FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id  
		and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_manager='".$userdata['id']."' and a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <='".$data['todate']."'  and a.status IN ('Delivered', 'Shipped','Rejected','order cancelled')  order by ordered_on desc");
			
		}elseif($this->auth->check_access('Deliver manager')){
			
			if($this->auth->check_access('Deliver manager')){
				$delivery_partner = $userdata['id'];
			}elseif(isset($data['delpartner']) && $data['delpartner'] != ""){
				$delivery_partner =  $data['delpartner'];
			}else{
				$delivery_partner = 0;
			}
			
			$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.*,e.firstname,e.phone FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id and a.order_type!=3
			and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.delivery_partner = '".$delivery_partner."' and a.status IN ('Shipped','Rejected','order cancelled') and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on < '".$data['todate']."') order by ordered_on desc");
			
		}else{
			
			$where = '';
			if(isset($data['delpartner']) && $data['delpartner'] != ""){
				$where.=" and a.delivery_partner = '".$data['delpartner']."'";
			}
			if(isset($data['restaurant']) && $data['restaurant'] != ""){
				$where.=" and a.restaurant_id = '".$data['restaurant']."'";
			}
			//print_r($data['restaurant']);exit;

			//  echo "SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE  a.`restaurant_id` = b.restaurant_id 
			// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id   and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <= '".$data['todate']."') ".$where."
			//  order by ordered_on desc"; exit; 
			$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,e.firstname,e.phone FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id and a.status!='Payment pending'
			and d.ordertype_id =a.order_type and b.restaurant_manager = c.id   and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on < '".$data['todate']."') ".$where."
			 order by ordered_on desc");

			
			
			
		}
		
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	
	function GetCustomerReview($id){
		$sql = $this->db->query("select a.*,b.firstname,c.feedbacktype from feedback a, customers b,feedbacktype c where a.feedbackfrom=b.id and a.feedbacktype=6 and a.feedbacktype=c.feedbacktype_id and a.feedbackto='".$id."' order by date desc");
		if($sql->num_rows() > 0){
			$result['data']	= $sql->result();
			$sql1 = $this->db->query("select AVG(ratings) as avg from feedback where feedbacktype=6 and feedbackto='".$id."'");
			$result['avg']	= $sql1->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	function GetRestaurantreview($id){
		$sql = $this->db->query("select a.*,b.restaurant_name from feedback a, restaurant b where a.feedbackfrom=b.restaurant_id and a.feedbacktype=9 and a.feedbackto='".$id."'");
		if($sql->num_rows() > 0){
			$result['data']	= $sql->result();
			$sql1 = $this->db->query("select AVG(ratings) as avg from feedback where feedbacktype=6 and feedbackto='".$id."'");
			$result['avg']	= $sql1->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	function GetDelPartnerReview($id){
		$sql = $this->db->query("select a.*,b.firstname,c.feedbacktype from feedback a, admin b,feedbacktype c  where a.feedbackfrom=b.id and a.feedbacktype=4 and a.feedbacktype=c.feedbacktype_id and a.feedbackto='".$id."' order by date desc");
		if($sql->num_rows() > 0){
			$result['data']	= $sql->result();
			$sql1 = $this->db->query("select AVG(ratings) as avg from feedback where feedbacktype=4 and feedbackto='".$id."'");
			$result['avg']	= $sql1->result();
		}else{
			$result = 0;
		}
		return $result;

	}
	
	function GetDelBoyReview($id){
		$sql = $this->db->query("select a.*,b.name,c.feedbacktype from feedback a, delivery_boy b,feedbacktype c  where a.feedbackfrom=b.id and a.feedbacktype=7 and a.feedbacktype=c.feedbacktype_id and a.feedbackto='".$id."' order by date desc");
		if($sql->num_rows() > 0){
			$result['data']	= $sql->result();
			$sql1 = $this->db->query("select AVG(ratings) as avg from feedback where feedbacktype=7 and feedbackto='".$id."'");
			$result['avg']	= $sql1->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	
	function get_restpreviousorders($data){
		$userdata = $this->session->userdata('admin');
		// echo $sql = $this->db->query"SELECT a.*,d.order_type,d.ordertype_id,b.* FROM `orders` a, restaurant b, order_type d, admin c WHERE  a.`restaurant_id` = b.restaurant_id 
		// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_id='".$data['id']."' and a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <= '".$data['todate']."' order by ordered_on desc"; exit;

		$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,e.firstname,e.phone FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id
		and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_id='".$data['id']."' and a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <='".$data['todate']."' and a.status IN ('Delivered', 'Shipped','Rejected','order cancelled') order by ordered_on desc");
        
		
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}

	

	
	function CheckFeedback($order,$type){
		$sql = $this->db->query("select * from feedback where order_number='".$order."' and feedbacktype=".$type."");
		
		if($sql->num_rows() > 0){
			$result	= 1;
		}else{
			$result = 0;
		}
		return $result;
	}
	function InserReview($data){
		$date = date("Y-m-d H:i:s");
		$sql = "insert into feedback (feedbackfrom,feedbackto,comments,ratings,feedbacktype,order_number,date) values('".$data['feedbackfrom']."',
		'".$data['feedbackto']."','".$data['comments']."','".$data['ratings']."','".$data['feedbacktype']."','".$data['order_number']."','".$date."')";
		 $this->db->query($sql);
	}
	function GetMenudetails($data){
		// echo "select a.*,b.menu from order_items a, restaurant_menu b where a.menu_id=b.menu_id and order_id='".$data['id']."'"; exit;
		$sql = $this->db->query("select a.*,b.*,c.*,d.*,e.order_type from order_items a, restaurant_menu b,restaurant c,orders d,order_type e  where a.menu_id=b.menu_id and c.restaurant_id=d.restaurant_id and a.order_id=d.id and d.order_type=e.ordertype_id and order_id='".$data['id']."'");
		
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}

	function GetMenudetails1($data){
		// echo "select a.*,b.menu from order_items a, restaurant_menu b where a.menu_id=b.menu_id and order_id='".$data['id']."'"; exit;
		$sql = $this->db->query("select a.restaurant_name,b.*,c.order_type from restaurant a,orders b,order_type c  where a.restaurant_id=b.restaurant_id and  b.order_type=c.ordertype_id and id='".$data['id']."'");
		
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	
	function ChangeRestMangerStatus($status,$id){
		date_default_timezone_set('Asia/Calcutta');
		 	$date = date('Y-m-d H:i:s',time());
		if($status == "1"){ 
			$data = "Accepted"; 
			 $sql = $this->db->query('update orders set restaurant_manager_status="'.$data.'" where id="'.$id.'"');
			//$sql = $this->db->query('update orders a,restaurant b,admin c set a.restaurant_manager_status="'.$data.'",a.delivery_partner=b.del_partner where b.del_partner = c.id and a.id="'.$id.'"');
		}else if($status == "2"){ 
			$data = "Delivered"; 
			$sql = $this->db->query('update orders set status="'.$data.'",actualdelivery_time=
				"'.$date.'",actualpickup_time="'.$date.'" where id="'.$id.'"');

			 $sql2=$this->db->query("select * from orders where id='".$id."'");
		   //print_r( $sql1->result_array());exit;
           $message="";
		if($sql2->num_rows()>0){
			$res = $sql2->result_array();
			// print_r($res[0]['status']);
			// print_r($res[0]['payment_mode']);exit;
			//$result = $res[0]['status'];
			if($res[0]['status']=='Delivered' && $res[0]['payment_mode']==1)
			{
			$i=0;
				foreach($res as $row){ 

				$sql1 = "select restaurant_name,restaurant_phone,GST from restaurant where restaurant_id='".$row['restaurant_id']."'";

				$query1 = $this->db->query($sql1);

				if($query1->num_rows()>0){

					$res1 = $query1->result_array();
					$result[$i]['restaurant_name'] = $res1[0]['restaurant_name'];
					$result[$i]['restaurant_phone'] = $res1[0]['restaurant_phone'];
					$result[$i]['GST'] = $res1[0]['GST'];
					
				}else{
					$result[$i]['restaurant_name'] = "";
					$result[$i]['restaurant_phone'] = "";
					$result[$i]['GST'] = "";
					
				}

				$sql2 = "select firstname,email from customers where id='".$row['customer_id']."'";

				$query2 = $this->db->query($sql2);

				if($query2->num_rows()>0){

					$res2 = $query2->result_array();
					$result[$i]['firstname'] = $res2[0]['firstname'];
					$result[$i]['email'] = $res2[0]['email'];
				}else{
					$result[$i]['firstname'] = "";
					$result[$i]['email'] = "";
				}
				    $logo1='https://eatsapp.in/login/uploads/images/3.png';
			        $image1="<img src='".$logo1."' height='150' width='150'  alt='logo' >";
					//$result[$i]['id'] = $row['id'];
					$result[$i]['order_id'] = $row['id'];
					$result[$i]['order_number'] = $row['order_number'];
					$result[$i]['shipping'] = $row['shipping'];
					$result[$i]['total_amount'] = $row['total_amount'];
					$result[$i]['coupon_discount'] = $row['coupon_discount'];
					$result[$i]['discount1'] = $row['discount1'];
					$result[$i]['discount2'] = $row['discount2'];

					$result[$i]['gstonfood'] = $row['gstonfood'];
					if($row['discount1']==0){
						$discount1= "";
					}else{
						
						$discount1= "<tr><td>Discount</td><td align=right>".$result[$i]['discount1']."</td></tr>";
					}
					if($row['discount2']==0){
						$discount2= "";
					}else{
						
						$discount2= "<tr><td>Discount</td><td align=right>".$result[$i]['discount2']."</td></tr>";
					}
					if($row['gstonfood']==0){
						$gst= "";
					}else{
						
						$gst= "<tr><td>GST</td><td align=right>".$result[$i]['gstonfood']."</td></tr>";
					}
					$result[$i]['netordervalue'] = $row['netordervalue'];
					$netordervalue=$result[$i]['netordervalue']+$result[$i]['coupon_discount'];
					
					$result[$i]['ordered_on'] = date("d-m-Y", strtotime($row['ordered_on']));
					$result[$i]['delivery_location'] = $row['delivery_location'];
					if($row['order_type']!=3){
						$delivery_location="<p style=text-align:center;>Delivery Address: ".$result[$i]['delivery_location']."</p>";
					
					}else{
						
							$delivery_location ="";
					}
					$result[$i]['total_cost'] = $row['total_cost'];
                   
					
					
					$sql1 = "select contents,cost from order_items  where order_id='".$row['id']."' ";
				$data1 = $this->db->query($sql1);
				
				if($data1->num_rows()>0){
					$j=0;
					foreach($data1->result_array() as $row1){
						$result[$i]['items'][]=$row1['contents'];
						$result[$i]['cost'][]=$row1['cost'];
					$j++;
					}

				}
				
				if(isset($result[$i]['items'])){	
					$result[$i]['items']= implode("<br>",$result[$i]['items']);
				}
				if(isset($result[$i]['cost'])){	
					$result[$i]['cost']= implode("<br>",$result[$i]['cost']);
				}

				
         $message.="<p style=text-align:center;>".$image1."  </p>
		  <p style=text-align:center;>Dear ".$result[$i]['firstname'].",</p>
		  <p style=text-align:center;>Thank you for placing the order with eatsapp</p>
		  <p style=text-align:center;>Order No: ".$result[$i]['order_number']."</p>
		  <p style=text-align:center;>Order Placed On: ".$result[$i]['ordered_on']."</p>
		  ".$delivery_location." 
		  <p style=text-align:center;>Store: ".$result[$i]['restaurant_name']."</p>
		
		  
		  
<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
	
}
.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   text-align: center;
}


</style>			
		

<table align=center>
  <tr>
    <th>Item Name</th>
    <th>Price (INR)</th>
   </tr>
  <tr>
    <td>".$result[$i]['items']."</td>
    <td align=right>".$result[$i]['cost']."</td>
   </tr>
  <tr>
    <td>Total</td>
    <td align=right>".$result[$i]['total_amount']."</td>
  </tr>
  ".$discount1."  
  
   ".$discount2."

   <tr>
    <td>Vocher Discount</td>
    <td align=right>".$result[$i]['coupon_discount']."</td>
  </tr>
  <tr>
    <td>Net Order Value</td>
    <td align=right>".$netordervalue."</td>
  </tr>
  ".$gst."  
  <tr>
    <td>Convenience Charge</td>
    <td align=right>".$result[$i]['shipping']."</td>
  </tr>
  
   <tr>
    <td>Total</td>
    <td align=right>".$result[$i]['total_cost']."</td>
  </tr>
  
  
</table>

<br><br><br><br><br><br><br><br><br><br><br><br>
<div class=footer>	   
<hr>
<p style=font-size:10px;>Disclaimer: This is an acknowledgement of the Order and not an actual invoice. Details mentioned above including the menu prices and taxes (as applicable) as provided by the Store to Eatsapp. It has been assumed that the said prices include GST. Responsibility of charging (or not charging) taxes lies with the Store and Eatsapp disclaims any liability that may arise in this respect.</p>
</div>
         
		";
		
	
		 $i++;
		
		$message1=" <center>".$image1." 
					          <p>Dear ".$res2[0]['firstname'].",</p>
							 <p>Thank you for using eatsapp.</p>
							 <p>The Bill(s) are attached herewith.</p>
							 <p><b>Looking forward to serve you soon again.</b></p>
							 <p style=color:#bdbdbf;>152, 15th Floor, Mittal Court (B), Nariman Point, Mumbai 400021<br><a href=http://eatsapp.in style=text-decoration:none;color:#bdbdbf;>eatsapp.in</a></p>
                             </center>
							 <p><b>Attachments:</b> </p>
					          ";

			$config = Array(
				'protocol' => 'smtp',
				'smtp_host' => 'ssl://smtp.gmail.com',
				'smtp_port' => 465,
				'smtp_user' => 'billing@eatsapp.in',
				'smtp_pass' => 'DEVANG123d',
				'mailtype'  => 'html', 
				'charset'   => 'iso-8859-1',
				'crlf' => "\r\n",
				'newline' => "\r\n"
			);

			
			$this->load->library('email',$config);
			$this->email->from('billing@eatsapp.in', 'eatsapp');
			$this->email->to($res2[0]['email']);
			$this->email->bcc('eatsapp.customer.billing@gmail.com');
			$this->email->subject('Your Requested Bill(s)');
			//$filename  = "orderbill.pdf";
			$filename1  = $row['order_number'];
			$filename  = "$filename1.pdf";
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($message);
		    $this->m_pdf->pdf->Output($filename, "F");
		    $this->email->attach($filename);
			$this->email->message($message1);
			$this->email->send(); 
		    //print($this->email->print_debugger());exit;
			//return true;
		  
			
		}
		}
		$result = $res[0]['status'];	
		}else{
			$result = '';	
		}

		  
		}
		else if($status == "3"){ 
			$data = "Shipped"; 
			$sql = $this->db->query('update orders set status="'.$data.'",actualdelivery_time=
				"'.$date.'",actualpickup_time="'.$date.'" where id="'.$id.'"');

			 $sql2=$this->db->query("select * from orders where id='".$id."'");
		   //print_r( $sql1->result_array());exit;
           $message="";
		if($sql2->num_rows()>0){
			$res = $sql2->result_array();
			// print_r($res[0]['status']);
			// print_r($res[0]['payment_mode']);exit;
			//$result = $res[0]['status'];
			if($res[0]['status']=='Shipped' && $res[0]['payment_mode']==1)
			{
			$i=0;
				foreach($res as $row){ 

				$sql1 = "select restaurant_name,restaurant_phone,GST from restaurant where restaurant_id='".$row['restaurant_id']."'";

				$query1 = $this->db->query($sql1);

				if($query1->num_rows()>0){

					$res1 = $query1->result_array();
					$result[$i]['restaurant_name'] = $res1[0]['restaurant_name'];
					$result[$i]['restaurant_phone'] = $res1[0]['restaurant_phone'];
					$result[$i]['GST'] = $res1[0]['GST'];
					
				}else{
					$result[$i]['restaurant_name'] = "";
					$result[$i]['restaurant_phone'] = "";
					$result[$i]['GST'] = "";
					
				}

				$sql2 = "select firstname,email from customers where id='".$row['customer_id']."'";

				$query2 = $this->db->query($sql2);

				if($query2->num_rows()>0){

					$res2 = $query2->result_array();
					$result[$i]['firstname'] = $res2[0]['firstname'];
					$result[$i]['email'] = $res2[0]['email'];
				}else{
					$result[$i]['firstname'] = "";
					$result[$i]['email'] = "";
				}
				    $logo1='https://eatsapp.in/login/uploads/images/3.png';
			        $image1="<img src='".$logo1."' height='150' width='150'  alt='logo' >";
					//$result[$i]['id'] = $row['id'];
					$result[$i]['order_id'] = $row['id'];
					$result[$i]['order_number'] = $row['order_number'];
					$result[$i]['shipping'] = $row['shipping'];
					$result[$i]['total_amount'] = $row['total_amount'];
					$result[$i]['coupon_discount'] = $row['coupon_discount'];
					$result[$i]['discount1'] = $row['discount1'];
					$result[$i]['discount2'] = $row['discount2'];

					$result[$i]['gstonfood'] = $row['gstonfood'];
					if($row['discount1']==0){
						$discount1= "";
					}else{
						
						$discount1= "<tr><td>Discount</td><td align=right>".$result[$i]['discount1']."</td></tr>";
					}
					if($row['discount2']==0){
						$discount2= "";
					}else{
						
						$discount2= "<tr><td>Discount</td><td align=right>".$result[$i]['discount2']."</td></tr>";
					}
					if($row['gstonfood']==0){
						$gst= "";
					}else{
						
						$gst= "<tr><td>GST</td><td align=right>".$result[$i]['gstonfood']."</td></tr>";
					}
					$result[$i]['netordervalue'] = $row['netordervalue'];
					$netordervalue=$result[$i]['netordervalue']+$result[$i]['coupon_discount'];
					
					$result[$i]['ordered_on'] = date("d-m-Y", strtotime($row['ordered_on']));
					$result[$i]['delivery_location'] = $row['delivery_location'];
					if($row['order_type']!=3){
						$delivery_location="<p style=text-align:center;>Delivery Address: ".$result[$i]['delivery_location']."</p>";
					
					}else{
						
							$delivery_location ="";
					}
					$result[$i]['total_cost'] = $row['total_cost'];
                   
					
					
					$sql1 = "select contents,cost from order_items  where order_id='".$row['id']."' ";
				$data1 = $this->db->query($sql1);
				
				if($data1->num_rows()>0){
					$j=0;
					foreach($data1->result_array() as $row1){
						$result[$i]['items'][]=$row1['contents'];
						$result[$i]['cost'][]=$row1['cost'];
					$j++;
					}

				}
				
				if(isset($result[$i]['items'])){	
					$result[$i]['items']= implode("<br>",$result[$i]['items']);
				}
				if(isset($result[$i]['cost'])){	
					$result[$i]['cost']= implode("<br>",$result[$i]['cost']);
				}

				
         $message.="<p style=text-align:center;>".$image1."  </p>
		  <p style=text-align:center;>Dear ".$result[$i]['firstname'].",</p>
		  <p style=text-align:center;>Thank you for placing the order with eatsapp</p>
		  <p style=text-align:center;>Order No: ".$result[$i]['order_number']."</p>
		  <p style=text-align:center;>Order Placed On: ".$result[$i]['ordered_on']."</p>
		  ".$delivery_location." 
		  <p style=text-align:center;>Store: ".$result[$i]['restaurant_name']."</p>
		
		  
		  
<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
	
}
.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   text-align: center;
}


</style>			
		

<table align=center>
  <tr>
    <th>Item Name</th>
    <th>Price (INR)</th>
   </tr>
  <tr>
    <td>".$result[$i]['items']."</td>
    <td align=right>".$result[$i]['cost']."</td>
   </tr>
  <tr>
    <td>Total</td>
    <td align=right>".$result[$i]['total_amount']."</td>
  </tr>
  ".$discount1."  
  
   ".$discount2."

   <tr>
    <td>Vocher Discount</td>
    <td align=right>".$result[$i]['coupon_discount']."</td>
  </tr>
  <tr>
    <td>Net Order Value</td>
    <td align=right>".$netordervalue."</td>
  </tr>
  ".$gst."  
  <tr>
    <td>Convenience Charge</td>
    <td align=right>".$result[$i]['shipping']."</td>
  </tr>
  
   <tr>
    <td>Total</td>
    <td align=right>".$result[$i]['total_cost']."</td>
  </tr>
  
  
</table>

<br><br><br><br><br><br><br><br><br><br><br><br>
<div class=footer>	   
<hr>
<p style=font-size:10px;>Disclaimer: This is an acknowledgement of the Order and not an actual invoice. Details mentioned above including the menu prices and taxes (as applicable) as provided by the Store to Eatsapp. It has been assumed that the said prices include GST. Responsibility of charging (or not charging) taxes lies with the Store and Eatsapp disclaims any liability that may arise in this respect.</p>
</div>
         
		";
		
	
		 $i++;
		
		$message1=" <center>".$image1." 
					          <p>Dear ".$res2[0]['firstname'].",</p>
							 <p>Thank you for using eatsapp.</p>
							 <p>The Bill(s) are attached herewith.</p>
							 <p><b>Looking forward to serve you soon again.</b></p>
							 <p style=color:#bdbdbf;>152, 15th Floor, Mittal Court (B), Nariman Point, Mumbai 400021<br><a href=http://eatsapp.in style=text-decoration:none;color:#bdbdbf;>eatsapp.in</a></p>
                             </center>
							 <p><b>Attachments:</b> </p>
					          ";

			$config = Array(
				'protocol' => 'smtp',
				'smtp_host' => 'ssl://smtp.gmail.com',
				'smtp_port' => 465,
				'smtp_user' => 'billing@eatsapp.in',
				'smtp_pass' => 'DEVANG123d',
				'mailtype'  => 'html', 
				'charset'   => 'iso-8859-1',
				'crlf' => "\r\n",
				'newline' => "\r\n"
			);

			
			$this->load->library('email',$config);
			$this->email->from('billing@eatsapp.in', 'eatsapp');
			$this->email->to($res2[0]['email']);
			$this->email->bcc('eatsapp.customer.billing@gmail.com');
			$this->email->subject('Your Requested Bill(s)');
			//$filename  = "orderbill.pdf";
			$filename1  = $row['order_number'];
			$filename  = "$filename1.pdf";
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($message);
		    $this->m_pdf->pdf->Output($filename, "F");
		    $this->email->attach($filename);
			$this->email->message($message1);
			$this->email->send(); 
		    //print($this->email->print_debugger());exit;
			//return true;
		  
			
		}
		}
		$result = $res[0]['status'];	
		}else{
			$result = '';	
		}
			//return $result;
		}
		else{ 
			$data = "Rejected"; 
			$sql = $this->db->query('update orders set restaurant_manager_status="'.$data.'", status="'.$data.'" where id="'.$id.'"');
			
			$query1 = $this->db->query("SELECT a.`phone` FROM `customers` a,orders b where b.customer_id=a.id and b.id=".$id." ");
	     
	
	         if($query1->num_rows() > 0){
		      $res= $query1->result_array();
		
		      foreach($res as $result){
			   $registatoin_ids[0]=$result['phone'];
			
			    //print_r($registatoin_ids[0]);exit;
		     }

		    

		    // $url =file("http://193.105.74.159/api/v3/sendsms/plain?user=wolotech&password=FBXM0Fv4&&sender=EATSAP&SMSText=We+regret+to+inform+you+that+the+Food+Outlet+you+selected+is+unable+to+accept+your+order+.+Please+order+from+some+other+Food+Outlet+.+We+will+refund+your+payment+within+5+working+days&type=longsms&GSM=91".$registatoin_ids[0]." ");

		     $url =file("http://193.105.74.159/api/v3/sendsms/plain?user=wolotech&password=FBXM0Fv4&&sender=EATSAP&SMSText=We+regret+to+inform+that+the+Outlet+you+selected+is+unable+to+accept+your+order+.+Please+order+from+another+Outlet+.+We+will+refund+your+payment+at+earliest+.&type=longsms&GSM=91".$registatoin_ids[0]." ");
		     
    

	       }
            $query2 = $this->db->query("SELECT * FROM `customers` a,orders b where b.customer_id=a.id and b.id=".$id." ");
			if($query2->num_rows() > 0){
				foreach($query2->result_array() as $row){ 
					$logo1='https://eatsapp.in/login/uploads/images/3.png';
			        $image1="<img src='".$logo1."' height='150' width='150'  alt='logo'>";
					$message=" <center>".$image1." 
					         <p>Dear ".$row['firstname'].",</p>
							 <p>Thank you for placing the order with us. We regret to inform you that the order has been cancelled. We will refund the amount at earliest.</p>
							 <p><b>Order No:</b> ".$row['order_number']."</p>
							 
							 <p style=color:#bdbdbf;>152, 15th Floor, Mittal Court (B), Nariman Point, Mumbai 400021<br><a href=http://eatsapp.in style=text-decoration:none;color:#bdbdbf;>eatsapp.in</a></p>
                             </center>
					          ";
					// <h6>".$data['message']."</h6>";
						  $config = Array(
							'protocol' => 'smtp',
							'smtp_host' => 'ssl://smtp.gmail.com',
							'smtp_port' => 465,
							'smtp_user' => 'orders@eatsapp.in',
							'smtp_pass' => 'DEVANG123d',
							'mailtype'  => 'html', 
							'charset'   => 'iso-8859-1',
							'crlf' => "\r\n",
							'newline' => "\r\n"
						);
						$this->load->library('email',$config);
						$this->email->from('orders@eatsapp.in', 'eatsapp');
						$this->email->to($row['email']);
						$this->email->bcc('eatsapp.orders@gmail.com');
						$this->email->subject('eatsapp: Thanks for Placing Order on eatsapp');
						$this->email->message($message);
						$this->email->send();
						
				}
			} 
	       

		}
		if($sql){
			return true;
		}else{
			return false;
		}
	}
	
	function ChangeDelPartnerStatus($status,$id){
		
		$userdata = $this->session->userdata('admin');
		if($status == "1"){
			$data = "Accepted"; 
			$sql = $this->db->query('update orders set delivery_partner ="'.$userdata['id'].'",delivery_partner_status="'.$data.'" where id="'.$id.'"');
		}else{ 
			$data = "Rejected";
			//echo 'update orders set delivery_partner ="'.$userdata['id'].'",delivery_partner_status="'.$data.'", status="'.$data.'" where id="'.$id.'"'; exit;
			$sql = $this->db->query('update orders set delivery_partner ="'.$userdata['id'].'",delivery_partner_status="'.$data.'", status="'.$data.'" where id="'.$id.'"');

           $query1 = $this->db->query("SELECT `phone` FROM `customers` a,orders b where b.customer_id=a.id and b.id=".$id." ");
	     
	
	    if($query1->num_rows() > 0){
		$res	= $query1->result_array();
		
		foreach($res as $result){
			$registatoin_ids[0]=$result['phone'];
			
			//print_r($registatoin_ids[0]);exit;
		}

		

		//$url =file("http://193.105.74.159/api/v3/sendsms/plain?user=wolotech&password=FBXM0Fv4&&sender=EATSAP&SMSText=We+regret+to+inform+you+that+your+order+was+not+accepted+.+We+will+refund+your+payment+within+5+working+days&type=longsms&GSM=91".$registatoin_ids[0]." ");

		//$url =file("http://193.105.74.159/api/v3/sendsms/plain?user=wolotech&password=FBXM0Fv4&&sender=EATSAP&SMSText=We+regret+to+inform+you+that+your+order+was+not+accepted+.+We+will+refund+your+payment+at+earliest+.&type=longsms&GSM=91".$registatoin_ids[0]." ");
	}

	$query2 = $this->db->query("SELECT * FROM `customers` a,orders b where b.customer_id=a.id and b.id=".$id." ");
			if($query2->num_rows() > 0){
				foreach($query2->result_array() as $row){ 
					
					$logo1='http://eatsapp.in/login/uploads/images/3.png';
			        $image1="<img src='".$logo1."' height='150' width='150'  alt='logo'>";
					$message=" <center>".$image1." 
					         <p>Dear ".$row['firstname'].",</p>
							 <p>Thank you for placing the order with us. We regret to inform you that the order has been cancelled. We will refund the amount at earliest.</p>
							 <p><b>Order No:</b> ".$row['order_number']."</p>
							 
							 <p style=color:#bdbdbf;>152, 15th Floor, Mittal Court (B), Nariman Point, Mumbai 400021<br><a href=http://eatsapp.in style=text-decoration:none;color:#bdbdbf;>eatsapp.in</a></p>
                             </center>
					          ";
					// <h6>".$data['message']."</h6>";
						  $config = Array(
							'protocol' => 'smtp',
							'smtp_host' => 'ssl://smtp.gmail.com',
							'smtp_port' => 465,
							'smtp_user' => 'orders@eatsapp.in',
							'smtp_pass' => 'DEVANG123d',
							'mailtype'  => 'html', 
							'charset'   => 'iso-8859-1',
							'crlf' => "\r\n",
							'newline' => "\r\n"
						);
						$this->load->library('email',$config);
						$this->email->from('orders@eatsapp.in', 'eatsapp');
						$this->email->to($row['email']);
						$this->email->bcc('eatsapp.orders@gmail.com');
						$this->email->subject('eatsapp: Thanks for Placing Order on eatsapp');
						$this->email->message($message);
						$this->email->send();
						
				}
			}
  


		}
		
		if($sql){
			return true;
		}else{
			return false;
		}
	}
	//get an individual customers orders
	function get_customer_orders($id, $offset=0)
	{
		$this->db->join('order_items', 'orders.id = order_items.order_id');
		$this->db->order_by('ordered_on', 'DESC');
		return $this->db->get_where('orders', array('customer_id'=>$id), 15, $offset)->result();
	}
	
	function count_customer_orders($id)
	{
		$this->db->where(array('customer_id'=>$id));
		return $this->db->count_all_results('orders');
	}
	
	function get_order($id)
	{
		$this->db->where('id', $id);
		$result 			= $this->db->get('orders');
		
		$order				= $result->row();
		//$order->contents	= $this->get_items($order->id);
		
		return $order;
	}
	
	function get_items($id)
	{
		$this->db->select('order_id, contents');
		$this->db->where('order_id', $id);
		$result	= $this->db->get('order_items');
		
		$items	= $result->result_array();
		
		$return	= array();
		$count	= 0;
		foreach($items as $item)
		{

			$item_content	= unserialize($item['contents']);
			
			//remove contents from the item array
			unset($item['contents']);
			$return[$count]	= $item;
			
			//merge the unserialized contents with the item array
			$return[$count]	= array_merge($return[$count], $item_content);
			
			$count++;
		}
		return $return;
	}
	
	function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('orders');
		
		//now delete the order items
		$this->db->where('order_id', $id);
		$this->db->delete('order_items');
	}
	
	function save_order($data, $contents = false)
	{
		if (isset($data['id']))
		{
			$this->db->where('id', $data['id']);
			$this->db->update('orders', $data);
			$id = $data['id'];
			
			// we don't need the actual order number for an update
			$order_number = $id;
		}
		else
		{
			$this->db->insert('orders', $data);
			$id = $this->db->insert_id();
			
			//create a unique order number
			//unix time stamp + unique id of the order just submitted.
			$order	= array('order_number'=> date('U').$id);
			
			//update the order with this order id
			$this->db->where('id', $id);
			$this->db->update('orders', $order);
						
			//return the order id we generated
			$order_number = $order['order_number'];
		}
		
		//if there are items being submitted with this order add them now
		if($contents)
		{
			// clear existing order items
			$this->db->where('order_id', $id)->delete('order_items');
			// update order items
			foreach($contents as $item)
			{
				$save				= array();
				$save['contents']	= $item;
				
				$item				= unserialize($item);
				$save['product_id'] = $item['id'];
				$save['quantity'] 	= $item['quantity'];
				$save['order_id']	= $id;
				$this->db->insert('order_items', $save);
			}
		}
		
		return $order_number;

	}
	
	function get_best_sellers($start, $end)
	{
		if(!empty($start))
		{
			$this->db->where('ordered_on >=', $start);
		}
		if(!empty($end))
		{
			$this->db->where('ordered_on <',  $end);
		}
		
		// just fetch a list of order id's
		$orders	= $this->db->select('id')->get('orders')->result();
		
		$items = array();
		foreach($orders as $order)
		{
			// get a list of product id's and quantities for each
			$order_items	= $this->db->select('product_id, quantity')->where('order_id', $order->id)->get('order_items')->result_array();
			
			foreach($order_items as $i)
			{
				
				if(isset($items[$i['product_id']]))
				{
					$items[$i['product_id']]	+= $i['quantity'];
				}
				else
				{
					$items[$i['product_id']]	= $i['quantity'];
				}
				
			}
		}
		arsort($items);
		
		// don't need this anymore
		unset($orders);
		
		$return	= array();
		foreach($items as $key=>$quantity)
		{
			$product				= $this->db->where('id', $key)->get('products')->row();
			if($product)
			{
				$product->quantity_sold	= $quantity;
			}
			else
			{
				$product = (object) array('sku'=>'Deleted', 'name'=>'Deleted', 'quantity_sold'=>$quantity);
			}
			
			$return[] = $product;
		}
		
		return $return;
	}
	
	public function get_delpartnerremarks($data){ 
		$sql =$this->db->query("select comments from feedback where feedbackto=".$data->restaurant_id." and order_number='".$data->order_number."' and feedbacktype=5");
		$result = $sql->result();
		return $result;
	}
	
	public function CheckReview($order_id,$feedback_type,$feedbackfrom){
		$sql =$this->db->query("select comments from feedback where feedbackfrom=".$feedbackfrom." and order_number='".$order_id."' and feedbacktype='".$feedback_type."'");
		if($sql->num_rows() > 0){
			$result = $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	
	public function GetChargesForOrder($date){
		$sql = $this->db->query("select * from charges where start_date <= '".$date."' and end_date >= '".$date."' limit 1");
		if($sql->num_rows() > 0){
			$res	= $sql->result_array();
			$data['servicetax'] = $res[0]['servicetax'];
			$data['deliverycharge'] = $res[0]['deliverycharge'];
		}else{
			$sql = $this->db->query("select * from charges order by start_date desc  limit 1");
			if($sql->num_rows() > 0){
				$res	= $sql->result_array();
				$data['servicetax'] = $res[0]['servicetax'];
				$data['deliverycharge'] = $res[0]['deliverycharge'];
			}else{
				$data['servicetax'] = '';
				$data['deliverycharge'] = '';
			}
		}
		return $data;
	}
	
	public function DelPartnerDeliveryCharge($distance,$id){
		$iddd = $this->uri->segment(4);
		if(isset($iddd)){
			$delpartnerid = $this->uri->segment(4);
		}else{
			$userdata = $this->session->userdata('admin');
			$delpartnerid =  $userdata['id'];
		}
		
		$distance = trim(str_replace("KM","",$distance));
		if($distance == 0){
			$data['rate'] = 0;
		}else{
			// $sql= $this->db->query("select * from delpartner_charges where fromKm <= '".$distance."' and toKm >= '".$distance."' and delpartner_id = '".$delpartnerid."' limit 1");
			$sql= $this->db->query("select * from delpartner_charges a,orders b where a.fromKm <= '".$distance."' and a.toKm >= '".$distance."' and  b.delivery_partner=a.delpartner_id and b.id = '".$id."' limit 1");
			if($sql->num_rows() > 0){
				$res	= $sql->result_array();
				$data['rate'] = $res[0]['rate'];
			}else{
				$data['rate'] = 0;
			}
		}
		return $data;
	}

	public function getdata(){
		$userdata = $this->session->userdata('admin');
      // $sql=$this->db->query("select date(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY date(ordered_on)");

		 // $sql=$this->db->query("select MONTHNAME(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY month(ordered_on)");


     $sql=$this->db->query("select MONTHNAME(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."' and c.status IN ('Delivered', 'Shipped') and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY month(ordered_on)");




		 // echo "select MONTHNAME(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY month(ordered_on)";exit;

		// $sql=$this->db->query("select month(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted' and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '74' and c.ordered_on >= '2018-09-01' and c.ordered_on <= '2018-09-31' GROUP BY month(ordered_on)");
      // echo "select date(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY date(ordered_on)";exit;



      if($sql->num_rows() > 0){
      	$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
  

      

 
    }

    public function getdata1(){
		$userdata = $this->session->userdata('admin');
      $sql=$this->db->query("select date(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."' and c.status IN ('Delivered', 'Shipped') and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY date(ordered_on)");

		 // $sql=$this->db->query("select MONTHNAME(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY month(ordered_on)");


		 // echo "select MONTHNAME(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY month(ordered_on)";exit;

		// $sql=$this->db->query("select month(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted' and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '74' and c.ordered_on >= '2018-09-01' and c.ordered_on <= '2018-09-31' GROUP BY month(ordered_on)");
      // echo "select date(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY date(ordered_on)";exit;



      if($sql->num_rows() > 0){
      	$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
  

      

 
    }



    function get_previousorders1($id){
		
		$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.*,e.firstname,e.phone,c.firstname FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id 
			and d.ordertype_id =a.order_type and a.delivery_partner = c.id  and  a.delivery_partner=".$id."
			 order by ordered_on desc");
		
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
}

function get_deliveryboy($id){
		
		$sql = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.*,e.firstname,e.phone,c.firstname,f.name FROM `orders` a, restaurant b, order_type d, admin c,customers e,delivery_boy f WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id  and a.delivered_by = f.id
			and d.ordertype_id =a.order_type and a.delivery_partner = c.id  and  a.delivered_by=".$id."
			 order by ordered_on desc");
		
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
}

function get_orders($id)
    {
        $result = $this->db->get_where('orders', array('id'=>$id));
        return $result->row();
    }

    
		
}
