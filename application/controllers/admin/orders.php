<?php

class Orders extends Admin_Controller {

    function __construct()
    {
        parent::__construct();

        //$this->auth->check_access('Admin', true);
        $this->lang->load('category');
        $this->load->model('Order_model');
		$this->load->model('Customer_model');
		$this->load->model('Message_model');
		$this->load->model('Restaurant_model');
		$this->load->model('Roadrunner_model');
		$this->load->model('Pitstop_model');
		$this->load->model('Deliveryboy_model');
		$this->load->helper('url');
		$this->load->library('session');

    }

	function dashboard(){
		$data['orders'] = $this->Order_model->get_neworders();
		$data['deliveryboys'] = $this->Order_model->get_deliveryboys();
		$this->view($this->config->item('admin_folder').'/restaurantdatshboard',$data);
	}
    function neworders()
    {
        $data['orders'] = $this->Order_model->get_neworders();
        $this->view($this->config->item('admin_folder').'/neworders', $data);
    }

	function delpartnerorders()
    {
        $data['orders'] = $this->Order_model->get_delpartnerorders();
		$data['deliveryboys'] = $this->Order_model->get_deliveryboys();
        $this->view($this->config->item('admin_folder').'/delpartnerorders', $data);
    }

	function AssignDeliveryBoy($id){
		$data['delBoy'] = $this->input->post('deliveryboy');
		$data['id'] = $id;
		//print_r($data['id']);exit;
        $results = $this->Order_model->AssignDeliveryBoy($data);
		if($results){
			 // redirect('admin/orders/delpartnerorders', 'refresh');
			 redirect('admin/orders/dashboard', 'refresh');
		}

	}

	function orders()
    {
        $data['orders'] = $this->Order_model->get_deliverypartnerneworders();
        $this->view($this->config->item('admin_folder').'/delorders', $data);
    }

	function previousorders(){
		$data['fromdate'] =  date('Y-m-d',strtotime('first day of this month'));
		$data['todate'] =  date('Y-m-d H:i:s',strtotime('last day of this month'));
		$data['orders'] = $this->Order_model->get_previousorders($data);
		//$data['orders'] = "";
        $this->view($this->config->item('admin_folder').'/previousorders',$data);
	}
	function previousordersdelpartner(){

		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdated'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			// $data['todate'] = $_SESSION['todated'] = date("y-m-d H:i:s",strtotime($this->input->post('todate')));
			$data['todate'] = $_SESSION['todated'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));
			$delpartner_post = $this->input->post('delpartner');
			$delpartner_get = $this->uri->segment(4);
			$data['delpartner'] = isset($delpartner_get) ? $delpartner_get : $delpartner_post;
			$restaurant_post = $this->input->post('restaurant');
			$restaurant_get = $this->uri->segment(4);
			$data['restaurant'] = isset($restaurant_get) ? $restaurant_get : $restaurant_post;
		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] = $_SESSION['fromdated'] = date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] =  $_SESSION['todated'] =date('Y-m-d H:i:s',strtotime('last day of last month'));
			$delpartner_post = $this->input->post('delpartner');
			$delpartner_get = $this->uri->segment(4);
			$data['delpartner'] = isset($delpartner_get) ? $delpartner_get : $delpartner_post;
			$restaurant_post = $this->input->post('restaurant');
			$restaurant_get = $this->uri->segment(4);
			$data['restaurant'] = isset($restaurant_get) ? $restaurant_get : $restaurant_post;
		}else{

			$data['fromdate'] =  $_SESSION['fromdated'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] = $_SESSION['todated'] = date('Y-m-d H:i:s',strtotime('last day of this month'));
			$delpartner_post = $this->input->post('delpartner');
			$delpartner_get = $this->uri->segment(4);
			$data['delpartner'] = isset($delpartner_get) ? $delpartner_get : $delpartner_post;
			$restaurant_post = $this->input->post('restaurant');
			$restaurant_get = $this->uri->segment(4);
			$data['restaurant'] = isset($restaurant_get) ? $restaurant_get : $restaurant_post;
		}
		
		//$sql = $this->db->query("select * from delpartner_charges where id = 1");
               $sql = $this->db->query("select * from charges where id = 1");
		if($sql->num_rows() > 0){
			$res	= $sql->result_array();
			$data['servicetax'] = $res[0]['servicetax'];
			$data['deliverycharge'] = $res[0]['deliverycharge'];
		}else{
			$data['servicetax'] = '';
			$data['deliverycharge'] = '';    
		}
		
		$data['orders'] = $this->Order_model->get_previousorders($data);
		// $data['orders1'] = $this->Order_model->get_previousorders1($data);
		$this->view($this->config->item('admin_folder').'/previousordersdelpartner',$data);
	}
	function GetPreviousOrders(){
		
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			//$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));
			$data['delpartner'] = $this->input->post('delpartner');
		}elseif($this->input->post('action') == "Previous Month"){
			$data['fromdate'] = $_SESSION['fromdate'] =  date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));
			$data['delpartner'] = $this->input->post('delpartner');
		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));
			$data['delpartner'] = $this->input->post('delpartner');
		}
		$sql = $this->db->query("select * from charges where id = 1");
		if($sql->num_rows() > 0){
			$res	= $sql->result_array();
			$data['servicetax'] = $res[0]['servicetax'];
			$data['deliverycharge'] = $res[0]['deliverycharge'];
		}else{
			$data['servicetax'] = '';
			$data['deliverycharge'] = '';
		}
		$data['orders'] = $this->Order_model->get_previousorders($data);
		$this->view($this->config->item('admin_folder').'/previousorders',$data);
	}

	

	function GetRestPreviousOrders($id){
		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			// $data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));

		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		$data['id'] = $id;
		$restaurant  = $this->Restaurant_model->get_restaurant($id);

		$data['page_title'] = "Previous Orders and sales of ".$restaurant->restaurant_name;
		$data['orders'] = $this->Order_model->get_restpreviousorders($data);
		$sql = $this->db->query("select * from charges where id = 1");
		if($sql->num_rows() > 0){
			$res	= $sql->result_array();
			$data['servicetax'] = $res[0]['servicetax'];
			$data['deliverycharge'] = $res[0]['deliverycharge'];
		}else{
			$data['servicetax'] = '';
			$data['deliverycharge'] = '';
		}
		$this->view($this->config->item('admin_folder').'/previousordersrest',$data);
	}

	function GetRestPreviousOrdersbill(){
		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d H:i:s",strtotime($this->input->post('fromdate')));
			$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));

		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d H:i:s',strtotime('first day of last month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d H:i:s',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		
		
		$this->view($this->config->item('admin_folder').'/previousordersrestbill',$data);
	}

	function GetDelpartnerBill(){
		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d H:i:s",strtotime($this->input->post('fromdate')));
			$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));

		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d H:i:s',strtotime('first day of last month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d H:i:s',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		
		
		$this->view($this->config->item('admin_folder').'/previousordersdelbill',$data);
	}

	function GetDelpartnerBill1(){
		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			//$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));
			$data['todate'] = $_SESSION['todated'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));

		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		
		
		$this->view($this->config->item('admin_folder').'/previousordersdelbill1',$data);
	}

	function GetRestPreviousOrdersbill1($id){
		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));

		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		
		
		$this->view($this->config->item('admin_folder').'/previousordersrestbill1',$data);
	}

	function GetPreviousOrdersbill($id){
		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));

		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		
		
		$this->view($this->config->item('admin_folder').'/restpreviousordersbill',$data);
	}
	
	function restbill($id,$type){
		$fromdate=$this->input->post('fromdate');
		$todate=$this->input->post('todate');

		if($this->input->post('action') == "Go"){
			$data['fromdate'] =  $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			$data['todate'] = $_SESSION['todate'] = date("Y-m-d",strtotime($this->input->post('todate')));
			// $data['todate'] = $_SESSION['todate'] = date('Y-m-d',strtotime($this->input->post('todate').' +1 day'));
			// $data['todate1'] = $_SESSION['todate1'] = date("Y-m-d",strtotime($this->input->post('todate')));
		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] =  $_SESSION['fromdate'] =  date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));

		}
		
		
		$data['date'] = date("Y-m-d");
		$restaurant       = $this->Restaurant_model->get_restaurant($id);
		//print_r($restaurant); exit;
		$orders = $this->Restaurant_model->get_restaurantorders($id);
		$corders = $this->Restaurant_model->get_restaurantorderscancel($id);
		$data['name'] = $restaurant->restaurant_name;
		$data['address'] = $restaurant->restaurant_address;
		$data['branch'] = $restaurant->restaurant_branch;
		$data['email'] = $restaurant->restaurant_email;
		
		
		 $sql1=$this->db->query("select SUM(netordervalue),SUM(tax),SUM(commission) FROM `orders` where restaurant_id='".$id."' and ordered_on >='".$_SESSION['fromdate']."' and restaurant_manager_status='Accepted' and delivery_partner_status!='Rejected' and status!='Rejected' and ordered_on <= '".$_SESSION['todate']."'  ");
		
		if($sql1->num_rows() > 0){
			$res1	= $sql1->result_array();
			$data['netordervalue'] = $res1[0]['SUM(netordervalue)'];
			$data['tax'] = $res1[0]['SUM(tax)'];
			$data['commission'] = $res1[0]['SUM(commission)'];
			
			}else{

			$data['netordervalue'] = '';
			$data['tax'] = '';
			$data['commission'] = '';
			
		}

		 $sql=$this->db->query("select SUM(reimb) FROM `orders` where restaurant_id='".$id."' and ordered_on >='".$_SESSION['fromdate']."' and restaurant_manager_status!='Rejected' and delivery_partner_status!='Rejected' and ordered_on <= '".$_SESSION['todate']."'  ");
		
		if($sql->num_rows() > 0){
			$res1	= $sql->result_array();
			$data['reimb'] = $res1[0]['SUM(reimb)'];
			
		}else{

			$data['reimb'] = '';
			
			
		}
	
		
		 $sql2 = $this->db->query("select SUM(penalty) FROM `orders` where restaurant_id='".$id."' and ordered_on >='".$_SESSION['fromdate']."' and restaurant_manager_status!='Accepted' and delivery_partner_status!='Rejected'  and ordered_on <= '".$_SESSION['todate']."' ");
		 if($sql2->num_rows() > 0){
			$res	= $sql2->result_array();
			$data['penalty'] = $res[0]['SUM(penalty)'];
			
		 }else{
			$data['penalty'] = '';
			
		}



		$data['netamount1']=$data['penalty']+$data['commission']+$data['reimb'];
		$data['giveback']=$data['netordervalue']+$data['tax']-$data['netamount1'];
		
		
		$html =$this->load->view($this->config->item('admin_folder').'/restbill',$data,true);
		$filename = $restaurant->restaurant_name;
		if($type == "pdf"){
            $fnamee = $filename.date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".pdf";
			$filename  = "bills/".$fnamee;
		
		}else{
			
			$fnamee =  $filename.date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".xls";
			 $filename  = "bills/".$fnamee;
			 
		} 
		
	     fopen($filename,"w");
		chmod($fnamee,0777);
		$this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
		$this->m_pdf->pdf->Output($filename, "F");
	    //redirect("http://localhost/grazzyweb/".$filename);
		redirect("https://eatsapp.in/login/".$filename);

	}


	public function custbill($id,$type){

       $data['date'] = date("Y-m-d");

       $customer       = $this->Customer_model->get_customer1($id);
		//print_r($customer); exit;
	  
		
		$data['firstname'] = $customer->firstname;
		$data['email'] = $customer->email;
		
		$sql = $this->db->query("select * from charges order by start_date desc limit 1  ");
		if($sql->num_rows() > 0){
			$res	= $sql->result_array();
			$data['servicetax'] = $res[0]['servicetax'];
			$data['deliverycharge'] = $res[0]['deliverycharge'];
			
		}else{
			$data['servicetax'] = '';
			$data['deliverycharge'] = '';
			
		}

		$data['deliverycharge1'] = (($data['deliverycharge'])/1.18);
		$data['servicetax1']=(($data['deliverycharge1']*$data['servicetax']/100)/2);
		$data['totalbill']=$data['deliverycharge1']+$data['servicetax1']+$data['servicetax1'];
		
		
		


		$html =$this->load->view($this->config->item('admin_folder').'/custbill',$data,true);
		$filename ='custbill';
		if($type == "pdf"){
            $fnamee = $filename.".pdf";
			$filename  = "bills/".$fnamee;
		
		}
		// else{
			
		// 	$fnamee =  $filename.".xls";
		// 	 $filename  = "bills/".$fnamee;
			 
		// } 
		
	     fopen($filename,"w");
		chmod($fnamee,0777);
		$this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
		$this->m_pdf->pdf->Output($filename, "F");
		//redirect("http://localhost/grazzyweb/".$filename);
		redirect("https://app.eatsapp.in/login/".$filename);

	}

	public function custrestbill($type){

       $data['date'] = date("Y-m-d");
		
		// $sql = $this->db->query("select * from charges order by start_date desc limit 1  ");
		// if($sql->num_rows() > 0){
		// 	$res	= $sql->result_array();
		// 	$data['servicetax'] = $res[0]['servicetax'];
		// 	$data['deliverycharge'] = $res[0]['deliverycharge'];
			
		// }else{
		// 	$data['servicetax'] = '';
		// 	$data['deliverycharge'] = '';
			
		// }
		//  $data['deliverycharge1']=(($data['deliverycharge'])/1.18);
		//  $data['servicetax1']=(($data['servicetax']*9)/100);
		//  $data['totalbill']=$data['deliverycharge1']+$data['servicetax1']+$data['servicetax1'];
		
		


		$html =$this->load->view($this->config->item('admin_folder').'/custrestbill',$data,true);
		$filename ='custrestbill';
		if($type == "pdf"){
            $fnamee = $filename.".pdf";
			$filename  = "bills/".$fnamee;
		
		}else{
			
			$fnamee =  $filename.".xls";
			 $filename  = "bills/".$fnamee;
			 
		} 
		
	     fopen($filename,"w");
		chmod($fnamee,0777);
		$this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
		$this->m_pdf->pdf->Output($filename, "F");
		//redirect("http://localhost/grazzyweb/".$filename);
		redirect("https://app.eatsapp.in/login/".$filename);

	}


	// function excel($id)
	// {

	// 	$this->load->model('Restaurant_model');
	// 	if($this->input->post('action') == "Go"){
	// 		$data['fromdate'] =  $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
	// 		// $data['todate'] = $_SESSION['todate'] = date("Y-m-d",strtotime($this->input->post('todate')));
	// 		$data['todate'] = $_SESSION['todate'] = date('Y-m-d',strtotime($this->input->post('todate').' +1 day'));
	// 	}elseif($this->input->post('action') == "PreviousMonth"){
	// 		$data['fromdate'] =  $_SESSION['fromdate'] =  date('Y-m-d',strtotime('first day of last month'));
	// 		 $data['todate'] = $_SESSION['todate'] = date('Y-m-d',strtotime('last day of last month'));

	// 	}else{
	// 		$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
	// 		$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));

	// 	}
	// 	//$orders1 = $this->Order_model->get_restpreviousorders1($id);
	// 	 $restaurant       = $this->Restaurant_model->get_restaurant($id);
	// 	 $orders = $this->Restaurant_model->get_restaurantorders($id);
	//      $corders = $this->Restaurant_model->get_restaurantorderscancel($id);
	// 	//print_r($orders1);exit;
	//      //print_r($orders1->)
		
	// $this->load->library('excel');
 //    //Create a new Object
 //    $objPHPExcel = new PHPExcel();
 //    // Set the active Excel worksheet to sheet 0
 //    $objPHPExcel->setActiveSheetIndex(0); 

 //    $heading=array('Fromdate','Todate','Value of Orders Forwarded','GST Collected on behalf of Restaurant','Commission','Penalty','Reimbursement of Delivery charges','Net Amount of Service provided','eatsapp Keep Amount','Give Back'); //set title in excel sheet
 //    $rowNumberH = 1; //set in which row title is to be printed
 //    $colH = 'A'; //set in which column title is to be printed
    
 //    $objPHPExcel->getActiveSheet()->getStyle($rowNumberH)->getFont()->setBold(true);
    
	// for($col = ord('A'); $col <= ord('J'); $col++){ //set column dimension 
	// 	 $objPHPExcel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
 //         $objPHPExcel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
	// }
 //    foreach($heading as $h){ 

 //        $objPHPExcel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
 //        $colH++;    
 //    }


	// $sql1=$this->db->query("select SUM(netordervalue),SUM(tax),SUM(commission) FROM `orders` where restaurant_id='".$id."' and ordered_on >='".$_SESSION['fromdate']."' and restaurant_manager_status='Accepted' and delivery_partner_status!='Rejected' and ordered_on <= '".$_SESSION['todate']."'  ");
		
	// 	if($sql1->num_rows() > 0){
	// 		$res1	= $sql1->result_array();
	// 		$data['netordervalue'] = $res1[0]['SUM(netordervalue)'];
	// 		$data['tax'] = $res1[0]['SUM(tax)'];
	// 		$data['commission'] = $res1[0]['SUM(commission)'];
			
	// 		}else{

	// 		$data['netordervalue'] = '';
	// 		$data['tax'] = '';
	// 		$data['commission'] = '';
			
	// 	}

	// 	$sql=$this->db->query("select SUM(reimb) FROM `orders` where restaurant_id='".$id."' and ordered_on >='".$_SESSION['fromdate']."' and restaurant_manager_status!='Rejected' and delivery_partner_status!='Rejected' and ordered_on <= '".$_SESSION['todate']."'  ");
		
	// 	if($sql->num_rows() > 0){
	// 		$res1	= $sql->result_array();
	// 		$data['reimb'] = $res1[0]['SUM(reimb)'];
			
	// 	}else{

	// 		$data['reimb'] = '';
			
			
	// 	}
	
		
	// 	 $sql2 = $this->db->query("select SUM(penalty) FROM `orders` where restaurant_id='".$id."' and ordered_on >='".$_SESSION['fromdate']."' and restaurant_manager_status!='Accepted' and delivery_partner_status!='Rejected'  and ordered_on <= '".$_SESSION['todate']."' ");
	// 	 if($sql2->num_rows() > 0){
	// 		$res	= $sql2->result_array();
	// 		$data['penalty'] = $res[0]['SUM(penalty)'];
			
	// 	 }else{
	// 		$data['penalty'] = '';
			
	// 	}



	// 	$data['netamount1']=$data['penalty']+$data['commission']+$data['reimb'];
	// 	$data['giveback']=$data['netordervalue']+$data['tax']-$data['netamount1'];


		
		
 //        $rowCount = 2; // set the starting row from which the data should be printed
     
	

 //        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $data['fromdate']); 
 //        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $data['todate']); 
 //        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $data['netordervalue']); 
 //        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $data['tax']); 
 //        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $data['commission']); 
 //        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $data['penalty']); 
 //        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $data['reimb']);
 //        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $data['netamount1']);
 //        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $data['netamount1']);
 //        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $data['giveback']);
        

        
          
 //         $rname= $restaurant->restaurant_name;
	//  	 $filename=$rname.date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).'.xls'; //save our workbook as this file name

 //    // Instantiate a Writer 
 //    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

 //    header('Content-Type: application/vnd.ms-excel');
 //    header('Content-Disposition: attachment;filename="'.$filename.'" ');
 //    header('Cache-Control: max-age=0');

 //    $objWriter->save('php://output');
 //    //exit();

	// $this->load->view($this->config->item('admin_folder').'/restbill',$data,true);
		
		
	// }



function excel($id)
	{

		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] =  $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			//$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));
		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] =  $_SESSION['fromdate'] =  date('Y-m-d',strtotime('first day of last month'));
			 $data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		
		 $restaurant       = $this->Restaurant_model->get_restaurant($id);
		 $orders = $this->Restaurant_model->get_restaurantorders($id);
	     $corders = $this->Restaurant_model->get_restaurantorderscancel($id);
		
		
	$this->load->library('excel');
    //Create a new Object
    $objPHPExcel = new PHPExcel();
    // Set the active Excel worksheet to sheet 0
    $objPHPExcel->setActiveSheetIndex(0); 

    $heading=array('Order type','Delivered On','Order number','Customer Name','Customer Mobile','Delivery Boy',
    	'Delivery Location','Order value(Rs)','Discount(%)','Discount(Rs)','Net Order Value','Net Order Value fulfilled','Commission','Penalty','Reimbursement of delivery charges','Payment Mode','Customer Payment','Eatsapp to Store','Store to Eatsapp','Store Earnings','Status','Passcode'); //set title in excel sheet
    $rowNumberH = 1; //set in which row title is to be printed
    $colH = 'A'; //set in which column title is to be printed
   
    $objPHPExcel->getActiveSheet()->getStyle($rowNumberH)->getFont()->setBold(true);
    
	for($col = ord('A'); $col <= ord('U'); $col++){ //set column dimension 
		 $objPHPExcel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
	}
    foreach($heading as $h){ 

        $objPHPExcel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
        $colH++;    
    }


	

$export_excel = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,e.firstname,e.phone FROM `orders` a,restaurant b, order_type d,admin c,customers e WHERE a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_id='".$id."' and a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <= '".$data['todate']."' and a.status IN ('Delivered', 'Shipped','Rejected','order cancelled') order by ordered_on desc  ")->result_array();




    $rowCount = 2; // set the starting row from which the data should be printed
    foreach($export_excel as $excel)
    {  
    	 
    	$Delivered=date("Y-m-d", strtotime($excel['ordered_on']));
		$charges = $this->Order_model->GetChargesForOrder($excel['ordered_on']);
	    $deliverycharge = $charges['deliverycharge'];
        
        $orders1 = $this->Order_model->get_previousorders1($excel['delivery_partner']);
    
        $netordervalue=$excel['netordervalue']+$excel['coupon_discount']; 
        $gstonnetordervalue=$excel['tax'];

        if($excel['delivered_by'] == "0"){ 
			$deliveryboy="No Deliveryboy";
		 }else{
						
		    $deliveryboy= $excel['delivered_name'];
		}


		 if($excel['order_type'] == 1 && $excel['pitstop_id ']!= ""){
					$pitstop = $this->Pitstop_model->get_pitstop($excel['pitstop_id']);
					$data['toaddress'] = $pitstop->address;
				}else{
					$data['toaddress'] = $excel['delivery_location'];
				}

		if($excel['payment_mode'] ==0) 
		{ 
			$paymentmode="Paid Online";
		}else{
             $paymentmode= "Collect Cash";
	    }

	    if($excel['payment_mode'] ==1 && ($excel['status']=='order cancelled' || $excel['status']=='Rejected')) 
		{ 
			$payment= 0;
					   
		}else
		{
				    	
			$payment=$excel['total_cost'];
	   }



        if($excel['order_type'] !="I'll pickup"){ 
			$Conviencecharge = $deliverycharge;
					}else{
						
						$Conviencecharge= 0;
		} 



	   if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'){
						$netordervalue1 = 0;
			}elseif($excel['restaurant_manager_status'] == "Accepted"){ $netordervalue1=$netordervalue ; }else{ $netordervalue1 = "0"; }


        if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'){
			$gstonnetordervalue1 = 0;
		}elseif($excel['restaurant_manager_status'] == "Accepted"){ $gstonnetordervalue1=$gstonnetordervalue; }else{ $gstonnetordervalue1 = "0"; }
					
		if($excel['delivery_partner_status'] == "Rejected" || $excel['status']=='order cancelled'){
						$commission = 0;
		}elseif($excel['restaurant_manager_status'] == "Accepted"){ $commission = 
						$netordervalue*($excel['commission']/100); }else{ $commission = "0"; }


	    // if($excel['delivery_partner_status'] == "Rejected"){
					// 	$penalty = 0;
					// }elseif($excel['restaurant_manager_status'] == "Accepted"){ $penalty="0"; }else{ $penalty = ($excel['penalty']);  }

		if($excel['status']=='order cancelled'&& $excel['restaurant_manager_status'] == "0"){
			$penalty = ($excel['penalty']);
			}elseif($excel['restaurant_manager_status'] == "Rejected"){
				$penalty = ($excel['penalty']);
				}else{
				 $penalty = 0; 
				}
					
					
	   if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled' ){
						$reimb =  0;
					}elseif($excel['restaurant_manager_status'] == "Rejected" ){
						$reimb = 0;
					}else{
						
						if($excel['order_type']!="I'll pickup")
						{
							$reimb = $excel['reimb'];
							
						}
						else{
						    $reimb = 0;
						} 
		}

	 //  if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
		// 		 	$excel['restaurant_manager_status'] == "Accepted"){
		// 				$netamount = 0;
		// }else{
		// 		$netamount = $commission + $penalty + $reimb; ; 
		// 	}

		if($excel['payment_mode'] ==1) 
		{ 
			$netamount = 0;
					   
		}else
		{
			$netamount = $netordervalue1-$commission - $penalty - $reimb; 
	    }


	 //   if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
		// 		 	$excel['restaurant_manager_status'] == "Accepted"){
		// 				$keepamt = 0;
		// }
		// // elseif($excel['restaurant_manager_status'] == "Rejected" || $excel['status']=='order cancelled' )
		// // 			{
		// // 				$keepamt =  $netamount;
		// // 			}
		// else{
		// 		$keepamt =  $netamount;
		// 	}


		if($excel['payment_mode'] ==1) 
		{ 
			$keepamt = $payment-$netordervalue1+$commission + $penalty + $reimb;
					   
		}else
		{
			$keepamt = 0;
				    	 
		}


		// if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
		// 		 	$excel['restaurant_manager_status'] == "Accepted"){
		// 				$givetorest=0;
		// 			}elseif($excel['restaurant_manager_status'] == "Accepted"){
		// 				//echo $order->total_cost - $keepamt;
		// 				$givetorest=$netordervalue+$gstonnetordervalue-$keepamt;
		// 			}else{
		// 				$givetorest="-".$keepamt;
		// 			}	

	     $givetorest=$netordervalue1-$commission - $penalty - $reimb;


		 if($excel['restaurant_manager_status'] == "Rejected"){
					 $givetocust=$netordervalue+$gstonnetordervalue+$Conviencecharge-$excel['coupon_discount'];
				      
					}elseif($excel['delivery_partner_status']== "Rejected"){
						$givetocust=$netordervalue+$gstonnetordervalue+$Conviencecharge-$excel['coupon_discount'];
						
					}elseif($excel['status']=='order cancelled'){
						$givetocust=$netordervalue+$gstonnetordervalue+$Conviencecharge-$excel['coupon_discount'];
				      
					}
					else{
						$givetocust=0;
					}				
					
					 
					 
				 if( $excel['delivery_partner_status'] == "Rejected"){ 
				      $username=$orders1[0]->firstname;
						$status= "Rejected by $username";
      //            }elseif($excel['delivery_partner_status'] == "Accepted"){
						// $status=$excel['status'];
				 }else if($excel['restaurant_manager_status'] == "Accepted" && $excel['status'] == "order cancelled" ){
						//  $username=$orders1[0]->firstname;
						// $status= "Rejected by $username";
				 	if($excel['order_type']!="I'll pickup")
						{
						     $username=$orders1[0]->firstname;
						     $status= "Rejected by $username";
					    }
					    else{
					    	 $restname=$excel['restaurant_name'];
						     $status="Rejected by $restname";
					    }
					}elseif($excel['restaurant_manager_status'] == "Accepted"){
						//$status=$excel['status'];
						if($excel['status'] == "Shipped" || $excel['status'] == "Delivered" )
						{
							 $status= "Delivered";
						}
					}else if($excel['restaurant_manager_status'] == "Rejected"){
						$restname=$excel['restaurant_name'];
						$status="Rejected by $restname";
					}
					else if($excel['status'] == "order cancelled"){
						$restname=$excel['restaurant_name'];
						$status="Rejected by $restname";
					}
					else{
						$status="Not acted yet";
					}						
					


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $excel['order_type']); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Delivered); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $excel['order_number']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $excel['firstname']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $excel['phone']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $deliveryboy);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $data['toaddress']);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $excel['total_amount']);
        // $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,  $Conviencecharge);
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount,$excel['discount1']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount,$excel['discount2']); 
        // $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount,$excel['coupon_discount']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount,$netordervalue);
        //$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount,$gstonnetordervalue); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount,$netordervalue1);
        //$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount,$gstonnetordervalue1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $commission); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $penalty);
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount,$reimb); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount,$paymentmode);
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount,$payment);
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount,$netamount); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount,$keepamt);
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount,$givetorest); 
        //$objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount,$givetocust);
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount,$status);
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount,$excel['passcode']);
         
        
        
        
        
         
        
       
          
        $rowCount++; 
    } 
        
          
         $rname= $restaurant->restaurant_name;
	 	 $filename=$rname.date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).'.xls'; //save our workbook as this file name

    // Instantiate a Writer 
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'" ');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
    //exit();

	$this->load->view($this->config->item('admin_folder').'/restbill',$data,true);
		
		
	}


	function restexcel($id)
	{

		$this->load->model('Restaurant_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] =  $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			//$data['todate'] = $_SESSION['todate'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));
		 $data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));
		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] =  $_SESSION['fromdate'] =  date('Y-m-d',strtotime('first day of last month'));
			 $data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
		
		 $restaurant       = $this->Restaurant_model->get_restaurant($id);
		 $orders = $this->Restaurant_model->get_restaurantorders($id);
	     $corders = $this->Restaurant_model->get_restaurantorderscancel($id);
		
		
	$this->load->library('excel');
    //Create a new Object
    $objPHPExcel = new PHPExcel();
    // Set the active Excel worksheet to sheet 0
    $objPHPExcel->setActiveSheetIndex(0); 

    $heading=array('Order type','Delivered On','Order number','Customer Name','Customer Mobile','Delivery Boy',
    	'Delivery Location','Order value(Rs)','Discount(%)','Discount(Rs)','Net Order Value','Net Order Value fulfilled','Commission','Penalty','Reimbursement of delivery charges','Payment Mode','Customer Payment','Eatsapp to Store','Store to Eatsapp','Store Earnings','Status','Passcode'); //set title in excel sheet
    $rowNumberH = 1; //set in which row title is to be printed
    $colH = 'A'; //set in which column title is to be printed
   
    $objPHPExcel->getActiveSheet()->getStyle($rowNumberH)->getFont()->setBold(true);
    
	for($col = ord('A'); $col <= ord('U'); $col++){ //set column dimension 
		 $objPHPExcel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
	}
    foreach($heading as $h){ 

        $objPHPExcel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
        $colH++;    
    }


	
  $userdata = $this->session->userdata('admin');
  $export_excel = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,e.firstname,e.phone FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id  
		and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and b.restaurant_manager='".$userdata['id']."' and a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <= '".$data['todate']."' and a.status IN ('Delivered', 'Shipped','Rejected','order cancelled') order by ordered_on desc")->result_array();


		
	$rowCount = 2; // set the starting row from which the data should be printed
    foreach($export_excel as $excel)
    {  
    	
		
        $orders1 = $this->Order_model->get_previousorders1($excel['delivery_partner']);
        $Delivered=date("Y-m-d", strtotime($excel['ordered_on']));
    
        $netordervalue=$excel['netordervalue']+$excel['coupon_discount'];
        $gstonnetordervalue=$excel['tax'];


        if($excel['delivered_by'] == "0"){
        	$deliveryboy="No Deliveryboy";
        }else{
        	$deliveryboy=$excel['delivered_name'];
        	
        }

        if($excel['order_type'] == 1 && $excel['pitstop_id ']!= ""){
					$pitstop = $this->Pitstop_model->get_pitstop($excel['pitstop_id']);
					$data['toaddress'] = $pitstop->address;
				}else{
					$data['toaddress'] = $excel['delivery_location'];
				}


        if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'){
						$netordervalue1 = 0;
			}elseif($excel['restaurant_manager_status'] == "Accepted"){ $netordervalue1=$netordervalue ; }else{ $netordervalue1 = "0"; }


       if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'){
			$gstonnetordervalue1 = 0;
		}elseif($excel['restaurant_manager_status'] == "Accepted"){ $gstonnetordervalue1=$gstonnetordervalue; }else{ $gstonnetordervalue1 = "0"; }
					
					
		if($excel['delivery_partner_status'] == "Rejected" || $excel['status']=='order cancelled'){
						$commission = 0;
		}elseif($excel['restaurant_manager_status'] == "Accepted"){ $commission = 
						$netordervalue*($excel['commission']/100); }else{ $commission = "0"; }


	    if($excel['status']=='order cancelled'&& $excel['restaurant_manager_status'] == "0"){
						$penalty = ($excel['penalty']);
					}elseif($excel['restaurant_manager_status'] == "Rejected"){
						$penalty = ($excel['penalty']);
					}else{
						$penalty = 0; 
					}
					
	   
	     if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled' ){
						$reimb =  0;
					}elseif($excel['restaurant_manager_status'] == "Rejected" ){
						$reimb = 0;
					}else{
						
						if($excel['order_type']!="I'll pickup")
						{
							$reimb = $excel['reimb'];
							
						}
						else{
						    $reimb = 0;
						} 
					}


		if($excel['payment_mode'] ==0) 
		{ 
		   $paymentmode="Paid Online";
	    }else{
              
           $paymentmode="Collect Cash";
         }


        if($excel['payment_mode'] ==1 && ($excel['status']=='order cancelled' || $excel['status']=='Rejected')) 
		{ 
		   $payment= 0;
				   
		}else
	    {
			 $payment=$excel['total_cost'];
		}
					

	 //    if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
		// 		 	$excel['restaurant_manager_status'] == "Accepted"){
		// 				$netamount = 0;
		// }else{
		// 		$netamount = $commission + $penalty + $reimb; ; 
		// 	}

		if($excel['payment_mode'] ==1) 
		{ 
			$netamount = 0;
					   	
		}else
		{
		    $netamount = $netordervalue1-$commission - $penalty - $reimb; 
		}
					


	 //  if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
		// 		 	$excel['restaurant_manager_status'] == "Accepted"){
		// 				$keepamt = 0;
		// }else{
		// 		$keepamt =  $netamount;
		// 	}


		if($excel['payment_mode'] ==1) 
		{ 
			$keepamt = $payment-$netordervalue1+$commission + $penalty + $reimb;
					   
		}else
	    {
			$keepamt = 0;
				    	 
		}


		// if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
		// 		 	$excel['restaurant_manager_status'] == "Accepted"){
		// 				$givetorest=0;
		// 			}elseif($excel['restaurant_manager_status'] == "Accepted"){
		// 				//echo $order->total_cost - $keepamt;
		// 				$givetorest=$netordervalue+$gstonnetordervalue-$keepamt;
		// 			}else{
		// 				$givetorest="-".$keepamt;
		// 			}	


		$givetorest=$netordervalue1-$commission - $penalty - $reimb;


				if($excel['delivery_partner_status'] == "Rejected"){
					$username=$orders1[0]->firstname;
					$status= "Rejected by $username";
					
					 // }elseif($excel['delivery_partner_status'] == "Accepted"){
						// $status=$excel['status'];
					}else if($excel['restaurant_manager_status'] == "Accepted" && $excel['status'] == "order cancelled" ){
						// $username=$orders1[0]->firstname;
					 //    $status= "Rejected by $username";
						if($excel['order_type']!="I'll pickup")
						{
						    $username=$orders1[0]->firstname;
					        $status= "Rejected by $username";
					    }
					    else{
					    	  $restname=$excel['restaurant_name'];
						      $status="Rejected by $restname";
					    }
					}else if($excel['status'] == "order cancelled"){
						$restname=$excel['restaurant_name'];
						$status="Rejected by $restname";
					}elseif($excel['restaurant_manager_status'] == "Accepted"){
						// echo "Restaurant manager accepted";
						// if($excel['order_type']!="I'll pickup")
						// {
						// $status="Restaurant manager accepted";
					 //    }
					 //    else{
					 //    	//echo "Delivered";
						// 	$status="Delivered";
					 //    }

						if($excel['status'] == "Shipped" || $excel['status'] == "Delivered" )
						{
								$status="Delivered";
						}

					}
					
					else{
						$restname=$excel['restaurant_name'];
						$status="Rejected by $restname";
					}					
					


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,$excel['order_type']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Delivered); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $excel['order_number']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $excel['firstname']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $excel['phone']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $deliveryboy); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $data['toaddress']);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $excel['total_amount']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount,$excel['discount1']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount,$excel['discount2']); 
        //$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,$excel['coupon_discount']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount,$netordervalue);
        //$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount,$gstonnetordervalue); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount,$netordervalue1);
        //$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount,$gstonnetordervalue1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $commission); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $penalty);
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount,$reimb); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount,$paymentmode);
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount,$payment );
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount,$netamount); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount,$keepamt);
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount,$givetorest); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount,$status);
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount,$excel['passcode']);
        
         
        
        
        
 $rowCount++; 
    } 
        
          
         //$rname= $restaurant->restaurant_name;
	 	 $filename=date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).'.xls'; //save our workbook as this file name

    // Instantiate a Writer 
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'" ');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
    //exit();

	$this->load->view($this->config->item('admin_folder').'/restbill',$data,true);
		
		
	}

	function delpartnerexcel($id=false)
	{
		$this->load->model('Deliveryboy_model');
		if($this->input->post('action') == "Go"){
			$data['fromdate'] =  $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			//$data['todate1'] = $_SESSION['todate1'] = date("Y-m-d H:i:s",strtotime($this->input->post('todate')));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));
		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] =  $_SESSION['fromdate'] =  date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));
			// $data['todate1'] = $_SESSION['todate1'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));
			//$data['todate1'] =  $_SESSION['todate1'] = date('Y-m-d H:i:s',strtotime('last day of this month'));


		}
		

		$Deliveryboy= $this->Deliveryboy_model->get_deliveryPartner($id);
		//$orders = $this->Deliveryboy_model->get_deliveryPartnerorders($id);
		
		// $data['name'] = $Deliveryboy->firstname;
		// $data['email'] = $Deliveryboy->email;
		 
	
	$url = $this->uri->segment(4); if(isset($url)){ $idurl = $url; }else{ $idurl = ''; } 
	$this->load->library('excel');
    //Create a new Object
    $objPHPExcel = new PHPExcel();
    // Set the active Excel worksheet to sheet 0
    $objPHPExcel->setActiveSheetIndex(0); 

    $heading=array('Order type','Delivered On','Order number','Customer Name','Customer Mobile','Delivery Boy Name','Pickup Location','Delivery Location','Status','Passcode'); //set title in excel sheet
    
    $rowNumberH = 1; //set in which row title is to be printed
    $colH = 'A'; //set in which column title is to be printed
   
    $objPHPExcel->getActiveSheet()->getStyle($rowNumberH)->getFont()->setBold(true);
    
	for($col = ord('A'); $col <= ord('Z'); $col++){ //set column dimension 
		 $objPHPExcel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
	}
    foreach($heading as $h){ 

        $objPHPExcel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
        if($this->auth->check_access('Admin') && !isset($url)){ 
         $objPHPExcel->getActiveSheet()->setCellValue('K'.$rowNumberH,'Order value(Rs)');
         $objPHPExcel->getActiveSheet()->setCellValue('L'.$rowNumberH,'Convenience Charge');
         $objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumberH,'Discount(%)');
         $objPHPExcel->getActiveSheet()->setCellValue('N'.$rowNumberH,'Discount(Rs)');
         $objPHPExcel->getActiveSheet()->setCellValue('O'.$rowNumberH,'Vocher Discount');
         $objPHPExcel->getActiveSheet()->setCellValue('P'.$rowNumberH,'Net Order Value');
         // $objPHPExcel->getActiveSheet()->setCellValue('U'.$rowNumberH,'GST on Net Order Value ');
         $objPHPExcel->getActiveSheet()->setCellValue('Q'.$rowNumberH,'Net Order Value fulfilled');
         // $objPHPExcel->getActiveSheet()->setCellValue('W'.$rowNumberH,'GST on Net Order Value fulfilled');
         $objPHPExcel->getActiveSheet()->setCellValue('R'.$rowNumberH,'Commission');
         $objPHPExcel->getActiveSheet()->setCellValue('S'.$rowNumberH,'Penalty');
         $objPHPExcel->getActiveSheet()->setCellValue('T'.$rowNumberH,'Reimbursement of delivery charges');
         $objPHPExcel->getActiveSheet()->setCellValue('U'.$rowNumberH,'Payment Mode');
         $objPHPExcel->getActiveSheet()->setCellValue('V'.$rowNumberH,'Customer Payment');
         $objPHPExcel->getActiveSheet()->setCellValue('W'.$rowNumberH,'Net amount');
         $objPHPExcel->getActiveSheet()->setCellValue('X'.$rowNumberH,'Keep amount for eatsapp');
         $objPHPExcel->getActiveSheet()->setCellValue('Y'.$rowNumberH,'Give to Restaurant');
         $objPHPExcel->getActiveSheet()->setCellValue('Z'.$rowNumberH,'Give to Customer');

      }

        $colH++;    
    }

     $userdata = $this->session->userdata('admin');
    if($this->auth->check_access('Deliver manager')){
			
			if($this->auth->check_access('Deliver manager')){
				$delivery_partner = $userdata['id'];
			}elseif(isset($id) && $id != ""){
				$delivery_partner =  $id;
			}else{
				$delivery_partner = 0;
			}
			
			// $export_excel = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.*,e.firstname,e.phone,f.name FROM `orders` a, restaurant b, order_type d, admin c,customers e,delivery_boy f WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id and a.`delivered_by` = f.id
			// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.delivery_partner = '".$delivery_partner."' and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on < '".$data['todate']."') order by ordered_on desc")->result_array();
			$export_excel = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.*,e.firstname,e.phone FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id and a.order_type!=3
			and d.ordertype_id =a.order_type and b.restaurant_manager = c.id and a.delivery_partner = '".$delivery_partner."' and a.status IN ('Shipped','Rejected','order cancelled') and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on < '".$data['todate']."') order by ordered_on desc")->result_array();
			
		}else{
			
			$where = '';
			if(isset($id) && $id != ""){
				$where.=" and a.delivery_partner = '".$id."'";
			}
			if(isset($data['restaurant']) && $data['restaurant'] != ""){
				$where.=" and a.restaurant_id = '".$data['restaurant']."'";
			}
			
			// $export_excel = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,e.firstname,e.phone,f.name FROM `orders` a, restaurant b, order_type d, admin c,customers e,delivery_boy f WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id and a.`delivered_by` = f.id
			// and d.ordertype_id =a.order_type and b.restaurant_manager = c.id   and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on < '".$data['todate']."') ".$where."
			//  order by ordered_on desc")->result_array();

			 $export_excel = $this->db->query("SELECT a.*,d.order_type,d.ordertype_id,b.restaurant_name,e.firstname,e.phone FROM `orders` a, restaurant b, order_type d, admin c,customers e WHERE  a.`restaurant_id` = b.restaurant_id and a.`customer_id` = e.id and a.status!='Payment pending'
			and d.ordertype_id =a.order_type and b.restaurant_manager = c.id   and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on < '".$data['todate']."') ".$where."
			 order by ordered_on desc")->result_array();
			
			
		}

   
    $rowCount = 2; // set the starting row from which the data should be printed
    foreach($export_excel as $excel)
    {  
        $Delivered=date("Y-m-d", strtotime($excel['ordered_on']));
        $charges = $this->Order_model->GetChargesForOrder($excel['ordered_on']);
        $deliverycharge2 = $charges['deliverycharge'];

    	$orders1 = $this->Order_model->get_previousorders1($excel['delivery_partner']);
    	$deliverycharge = $this->Order_model->DelPartnerDeliveryCharge($excel['distance'],$excel['id']);
    	//$deliverycharge1=$deliverycharge['rate'];
		$data['restaurant'] = $this->Restaurant_model->get_restaurant($excel['restaurant_id']);

		$data['fromaddress'] = $data['restaurant']->restaurant_address;

		if($excel['delivered_by'] == "0"){
			$deliveryboy="No Deliveryboy";
		}else{
						
			$deliveryboy=$excel['delivered_name'];
		}
         if($excel['order_type'] == 1 && $excel['pitstop_id ']!= ""){
					$pitstop = $this->Pitstop_model->get_pitstop($excel['pitstop_id']);
					$data['toaddress'] = $pitstop->address;
				}else{
					$data['toaddress'] = $excel['delivery_location'];
				}

        
        $netordervalue=$excel['netordervalue']+$excel['coupon_discount'];
		$gstonnetordervalue=$excel['tax'];

		if($excel['order_type'] !="I'll pickup"){
						
		$delcharge=$deliverycharge2;
		}else{
						
			$delcharge=0;
			} 
		
		if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'){
						$netordervalue1 = 0;
		}elseif($excel['restaurant_manager_status'] == "Accepted"){ $netordervalue1=$netordervalue ; }else{ $netordervalue1 = "0"; }

		if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'){
		 				$gstonnetordervalue1 = 0;
		 }elseif($excel['restaurant_manager_status'] == "Accepted"){ $gstonnetordervalue1=$gstonnetordervalue; }else{ $gstonnetordervalue1 = "0"; }

         
        if($excel['delivery_partner_status'] == "Rejected" || $excel['status']=='order cancelled'){
						$commission = 0;
		}elseif($excel['restaurant_manager_status'] == "Accepted"){ $commission = 
						$netordervalue*($excel['commission']/100); }else{ $commission = "0"; }

		
		// if($excel['delivery_partner_status'] == "Rejected"){
		// 				$penalty = 0;
		// }elseif($excel['restaurant_manager_status'] == "Accepted"){ $penalty="0"; }else{ $penalty = ($excel['penalty']);  }

		if($excel['status']=='order cancelled'&& $excel['restaurant_manager_status'] == "0"){
						$penalty = ($excel['penalty']);
					}elseif($excel['restaurant_manager_status'] == "Rejected"){
						$penalty = ($excel['penalty']);
					}else{
						$penalty = 0; 
					}
					
		 
		if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled' ){
						$reimb =  0;
					}elseif($excel['restaurant_manager_status'] == "Rejected" ){
						$reimb = 0;
					}else{
						
						if($excel['order_type']!="I'll pickup")
						{
							$reimb = $excel['reimb'];
							
						}
						else{
						    $reimb = 0;
						} 
					}


	  if($excel['payment_mode'] ==0) 
		{ 
			$paymentmode="Paid Online";
		}else{
              
             $paymentmode="Collect Cash";
	    }

	    if($excel['payment_mode'] ==1 && ($excel['status']=='order cancelled' || $excel['status']=='Rejected')) 
		{ 
					   

			$payment= 0;
					   
	   }else
      {
				    	
		$payment=$excel['total_cost'];
	  }
					
			


       if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
				 	$excel['restaurant_manager_status'] == "Accepted"){
						$netamount = 0;
			}else{
					$netamount = $commission + $penalty + $reimb; ; 
				}

		if($excel['delivery_partner_status'] == "Rejected" || $excel['status']=='order cancelled'&& 
				 	$excel['restaurant_manager_status'] == "Accepted"){
						$keepamt = 0;
					}elseif($excel['restaurant_manager_status'] == "Rejected" || $excel['status']=='order cancelled' )
					{
						$keepamt =  $netamount;
					}
					else{
						$keepamt =  $netamount+$delcharge-$excel['coupon_discount'];
					}


		if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'&& 
				 	$excel['restaurant_manager_status'] == "Accepted"){
						$givetorest=0;
		}elseif($excel['restaurant_manager_status'] == "Accepted"){
						
						$givetorest=$netordervalue+$gstonnetordervalue-$keepamt-$excel['coupon_discount']+$delcharge;
                       
		}else{
						$givetorest="-".$keepamt;
						
			}		

		// if($excel['restaurant_manager_status']== "Rejected"){
		// 			 $givetocust=$netordervalue+$gstonnetordervalue+$delcharge-$excel['coupon_discount'];
				      
		// 			}elseif($excel['delivery_partner_status']== "Rejected"){
		// 				$givetocust=$netordervalue+$gstonnetordervalue+$delcharge-$excel['coupon_discount'];
						
		// 			}elseif($excel['status']=='order cancelled'){
		// 				$givetocust=$netordervalue+$gstonnetordervalue+$delcharge-$excel['coupon_discount'];
				      
		// 			}else{
		// 				$givetocust=0;
						
		// 			}


		if($excel['payment_mode'] ==0 && ($excel['restaurant_manager_status'] == "Rejected" || 
			$excel['status']=='order cancelled')){
				$givetocust=$netordervalue+$gstonnetordervalue+$delcharge-$excel['coupon_discount'];
			     
				
				}else{
					$givetocust=0;
				}
		

		// if($excel['delivery_partner_status'] == "Rejected"){
		// 				$delivery  =  0;
						
		// 			}elseif($excel['restaurant_manager_status'] == "Rejected"){
		// 				$delivery  = 0;
						
		// 			}else{
		// 				$delivery = $deliverycharge['rate']; 
		// 			}

		if($excel['delivery_partner_status'] == "Rejected" ||$excel['status']=='order cancelled'){
						$delivery  =  0;
						
					}elseif($excel['restaurant_manager_status'] == "Rejected"){
						$delivery  = 0;
						
					}else{
						//$delivery = $deliverycharge['rate']; 
						$delivery = $excel['delpartner_delivery_charge']; 
					}
					
					

	     $km=isset($excel['distance']) ?  $excel['distance'] : '';
	
	 //   if($excel['delivery_partner_status'] == "Accepted"){
		// 				$penalty1=0;
		// }else{
		// 				$penalty1=$excel['penalty'];
		// 	}

	     if($excel['delivery_partner_status'] == "Rejected" ||  
		   $excel['status']=='order cancelled'&&$excel['restaurant_manager_status'] == "Accepted"){
			$penalty1 = ($excel['del_partner_penalty']);
		}else{
			$penalty1 = 0; 
			}


	   if($excel['delivery_partner_status'] == "Accepted" || $excel['status'] == "Order Placed"){
						//$netamount1 = $deliverycharge['rate'];
						$netamount1 = $excel['delpartner_delivery_charge'];
		}else{
						
						$netamount1 = $excel['delpartner_delivery_charge'] -$penalty1;
			}

	    if($excel['delivery_partner_status'] == "Rejected"){ 
						// echo "$order->status";
				 	$username=$orders1[0]->firstname;
				 	$status="Rejected by $username";
					}elseif($excel['delivery_partner_status'] == "Accepted"){
						$status=$excel['status'];
					}else if($excel['restaurant_manager_status'] == "Accepted" &&$excel['status'] == "order cancelled" ){
						// $username=$orders1[0]->firstname;
				 	// $status="Rejected by $username";
						if($excel['order_type']!="I'll pickup")
						{
						    $username=$orders1[0]->firstname;
					            $status= "Rejected by $username";
					    }
					    else{
					    	 $restaurantname=$excel['restaurant_name'];
						$status="Rejected by $restaurantname";
					    }
					}elseif($excel['restaurant_manager_status'] == "Accepted"){
						$status=$excel['status'];
					}
					else if($excel['restaurant_manager_status'] == "Rejected"){
						$restaurantname=$excel['restaurant_name'];
						$status="Rejected by $restaurantname";
					}
					else if($excel['status'] == "order cancelled"){
						
						$restaurantname=$excel['restaurant_name'];
						$status="Rejected by $restaurantname";
					}else{
						$status="Not acted yet";
						//echo "Not acted yet";
						//echo "$order->status ";
					} 

		

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $excel['order_type']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Delivered); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $excel['order_number']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $excel['firstname']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $excel['phone']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $deliveryboy);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $data['fromaddress']);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $data['toaddress']);
        // $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount,$delivery);
        // $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount,$km); 
        // $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount,$penalty1);
        // $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount,$netamount1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount,$status);
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount,$excel['passcode']);
         if($this->auth->check_access('Admin') && !isset($url)){ 
         $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount,$excel['total_amount']);
         $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount,$delcharge);
         $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount,$excel['discount1']);
         $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount,$excel['discount2']);
         $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount,$excel['coupon_discount']);
         $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount,$netordervalue);
         //$objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount,$gstonnetordervalue);
         $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount,$netordervalue1);
         //$objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount,$gstonnetordervalue1);
         $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount,$commission);
         $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount,$penalty);
         $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount,$reimb); 
         $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount,$paymentmode);
         $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount,$payment);
         $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount,$netamount);
         $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount,$keepamt);
         $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount,$givetorest);
         $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount,$givetocust); 
        }
        
        
         $rowCount++; 
    } 
        
         if($this->auth->check_access('Admin') && isset($url)){ 
         $filename = $Deliveryboy->firstname;
         $fnamee = $filename.date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".xls";
         }
        if($this->auth->check_access('Admin') && !isset($url)){ 
          $fnamee = date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".xls";
	 	 
          }
          if(!isset($url)){ 
          $fnamee = date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".xls";
	 	 
          }
    // Instantiate a Writer 
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$fnamee.'" ');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
    //exit();

	$this->load->view($this->config->item('admin_folder').'/restbill',$data,true);
		
		
	}
	function delpartnerbill($id){
		if($this->input->post('action') == "Go"){
			$data['fromdate'] =  $_SESSION['fromdate'] = date("Y-m-d H:i:s",strtotime($this->input->post('fromdate')));
			$data['todate'] = $_SESSION['todate'] = date("Y-m-d",strtotime($this->input->post('todate')));
			// $data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime($this->input->post('todate').' +1 day'));
		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] =  $_SESSION['fromdate'] =  date('Y-m-d H:i:s',strtotime('first day of last month'));
			$data['todate'] = $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of last month'));

		}else{
			$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d H:i:s',strtotime('first day of this month'));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d H:i:s',strtotime('last day of this month'));

		}
        
		$data['date'] = date("Y-m-d");
		$Deliveryboy       = $this->Deliveryboy_model->get_deliveryPartner($id);
		$orders = $this->Deliveryboy_model->get_deliveryPartnerorders($id);
		if($orders == 0){ $data['deliveries'] = 0;}else{ $data['deliveries'] = count($orders); }
		$data['name'] = $Deliveryboy->firstname;
        $data['email'] = $Deliveryboy->email;

        $sql1 = $this->db->query("SELECT SUM(b.rate) FROM `orders` a,delpartner_charges b WHERE b.fromKm <= a.distance and b.toKm >= a.distance and a.delivery_partner_status!='Rejected' and a.status!='Rejected ' and a.delivery_partner=b.delpartner_id and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <= '".$data['todate']."') and a.delivery_partner = '".$id."' order by ordered_on desc");
        


		if($sql1->num_rows() > 0){
			$res	= $sql1->result_array();
			$data['delivery'] = $res[0]['SUM(b.rate)'];
           
			
		}else{
			$data['delivery'] = '';
			
		}

        $sql2 = $this->db->query("SELECT SUM(a.penalty) FROM `orders` a WHERE a.`delivery_partner_status`!='Accepted' and (a.ordered_on >= '".$data['fromdate']."' and a.ordered_on <= '".$data['todate']."') and a.delivery_partner = '".$id."' order by ordered_on desc  ");

		if($sql2->num_rows() > 0){
			$res	= $sql2->result_array();
			$data['penalty'] = $res[0]['SUM(a.penalty)'];
           
			
		}else{
			$data['penalty'] = '';
			
		}

		$data['netdeliverycharge'] = $data['delivery'] - $data['penalty'];
		


		$sql = $this->db->query("select * from charges where id = 1");
		if($sql->num_rows() > 0){
			$res	= $sql->result_array();
			$data['servicetax1'] = $res[0]['servicetax'];
			$data['rate'] = $res[0]['deliverycharge'];
		}else{
			$data['servicetax1'] = '';
			$data['rate'] = '';
		}
		$data['delivery_charge'] = $data['rate'] * $data['deliveries'];
		$data['servicetax'] = (($data['delivery_charge'] * $data['servicetax1'])/100);
		$data['total']	=$data['delivery_charge']+ $data['servicetax'];
		$html = $this->load->view($this->config->item('admin_folder').'/delpartnertbill',$data, true);
		
		$filename = $Deliveryboy->firstname;
		 // if($type == "pdf"){
		 	 $fnamee = $filename.date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".pdf";
		 	// $fnamee = date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".pdf";
			//$fnamee = rand()."purplkite0105201731052017.pdf";
			$filename  = "bills/".$fnamee;
		// }
		// else{
		// 	$fnamee = $filename.date('Y-m-d',strtotime($_SESSION['fromdate'])).date('Y-m-d',strtotime($_SESSION['todate'])).".xls";
		// 	//$fnamee =  rand()."purplkite0105201731052017.xls";
		// 	 $filename  = "bills/".$fnamee;
			 
		// 	 } 
			 
		fopen($filename,"w");
		chmod($fnamee,0777);
		$this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
		$this->m_pdf->pdf->Output($filename, "F");
		
		redirect("https://eatsapp.in/login/".$filename);
		//redirect("http://localhost/grazzyweb/".$filename);
	}
	
	function getOrderDetails(){
		$html="";
		$data = $this->input->post('data');
		$restaurant = $this->Restaurant_model->get_restaurant($data['restaurant_id']);
		$orders = $this->Order_model->get_orders($data['id']);
		if($data['ordertype_id'] == 3){
			 $customer_details = $this->Customer_model->get_customer($data['customer_id']);
			 $cname = $customer_details->firstname." ".$customer_details->lastname;
			 $cphone = $customer_details->phone;

			 $dname1 = $customer_details->firstname." ".$customer_details->lastname;
			 $dphone = $customer_details->phone;

		}else{
			 $customer_details = $this->Customer_model->get_customer($data['customer_id']);
			 $cname = $customer_details->firstname." ".$customer_details->lastname;
			 $cphone = $customer_details->phone;
      $name = $customer_details->firstname." ".$customer_details->lastname;

			$deliveryboy_details = $this->Customer_model->get_deliveryboy($data['delivered_by']);
			$dname = isset($deliveryboy_details->name) ? $deliveryboy_details->name : "Not Assigned Yet";
			$dphone = isset($deliveryboy_details->phone) ? $deliveryboy_details->phone : "";

		}

				
    $t1=date("H:i:s", strtotime($data['keep_ready'])) . "\n";
    $t2=$data['preparation_time'];
   
		$html.="<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal'>&times;</button>
					<h4 class='modal-title'>Order Number: ".$data['order_number']."</h4>
				  </div>
				  <div class='modal-body' class='form-horizontal'>
					<div class='form-group'>
          <table class='table table-bordered'>
          <tr>
          <th>Restaurant Name:</th><td>".$restaurant->restaurant_name."</td>
          </tr>
          <tr>
          <th>Restaurant Contact Number:</th><td>".$restaurant->restaurant_phone."</td>
          </tr>
          <tr>
						<th>Restaurant Location:</th><td>".$restaurant->restaurant_address."</td>
            </tr>
            <tr>
						<th>Customer Name:</th><td>".$customer_details->firstname." ".$customer_details->lastname."</td>
            </tr>
            <tr>
						<th>Customer Contact Number:</th><td>".$cphone."</td>
            </tr>
            <tr>
            <th>Delivery Location:</th><td>".$data['delivery_location']."</td>
            </tr>
            <tr>
            <th>Delivery Boy Name:</th><td>".$dname."</td>
            </tr>
            <tr>
						<th>Delivery Boy Contact Number:</th><td>".$dphone."</td>
            </tr>
            <tr>
						<th>Passcode:</th><td>".$data['passcode']."</td>
            </tr>
             <tr>
             

				<th>Scheduled Pickup Time:</th><td>".date("H:i:s", strtotime($data['keep_ready']))."</td>
            </tr>
            <tr>
						<th>Scheduled Delivery Time:</th><td>".$data['delivered_on']."</td>
            </tr>

            </table>
					</div>
				</div>";
		echo $html;
	}


    function GetMenudetails(){
		$data = $this->input->post('data');
		
		$menus = $this->Order_model->GetMenudetails($data);
		
		$html="";
		if($menus != 0){
			if($data['ordertype_id'] == 3){
				
				
			 $customer_details = $this->Customer_model->get_customer($data['customer_id']);
			 $name = $customer_details->firstname." ".$customer_details->lastname;
			 $phone = $customer_details->phone;
			 $email = $customer_details->email;
			  
			}else{
				
				$deliveryboy_details = $this->Customer_model->get_deliveryboy($data['delivered_by']);
				$name = isset($deliveryboy_details->name) ? $deliveryboy_details->name : "Not assigned yet";
				$phone = isset($deliveryboy_details->phone) ? $deliveryboy_details->phone : "";
				$email = isset($deliveryboy_details->email) ? $deliveryboy_details->email : "";
				
				
				// &ensp;</strong>(".$deliverypartnername.")
			}
			$html.="<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal'>&times;</button>
					<h4 class='modal-title'>Order number: ".$data['order_number']."</h4>
				  </div>
				  <div class='modal-body' class='form-horizontal'>
					<div class='form-group'>
						<label><strong>"; if($data['ordertype_id'] == 3){ $html.="Customer name";}else{$html.="Delivery boy";} $html.=":&ensp;</strong>".$name." </label></br>

						<label><strong>Mobile No:&ensp;</strong>".$phone."</label></br>
						
						
						

					</div>
					<table class='table table-bordered'>
					<thead>
						<tr><th>Item name</th><th>Customization</th><tr>
					</thead>
					<tbody>";
			foreach($menus as $menu){
					// $html.="<tr><td>".$menu->menu."</td><td>".$menu->cost."</td><td>".$menu->contents."</td></td>";
				$html.="<tr><td>".$menu->menu."</td><td>".$menu->contents."</td></td>";

			}
			$html.="</tbody>
				</table>
				</div>
			  <div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			  </div>";
		}
		echo $html;
	}

	function ChangeRestMangerStatus($status,$id){
		$statuss = $this->Order_model->ChangeRestMangerStatus($status,$id);
		if($statuss && $statuss == 1){
			$data['order'] = $this->Order_model->get_order($id);
			if($data['order']->order_type != 3){
				$data['restaurant'] = $this->Restaurant_model->get_restaurant($data['order']->restaurant_id);
				$data['customer'] = $this->Customer_model->get_customer($data['order']->customer_id);
				$data['fromaddress'] = $data['restaurant']->restaurant_address;
				$data['fromcity'] = $data['restaurant']->restaurant_branch;
				if($data['order']->order_type == 1 && $data['order']->pitstop_id != ""){
					$pitstop = $this->Pitstop_model->get_pitstop($data['order']->pitstop_id);
					$data['toaddress'] = $pitstop->address;
					$data['tocity'] = $pitstop->city;
				}else{
					$data['toaddress'] = $data['order']->delivery_location;
					$data['tocity'] = $data['restaurant']->restaurant_branch;
				}
			
				
				$result = $this->Roadrunner_model->CheckServicability($data);
				//echo $result;exit;
				$roadrunner = json_decode($result);
			    //print_r($roadrunner);exit;
				// if($roadrunner->status->code ==  200){
				//  // if($roadrunner->message ==  200){
				// 	// if($roadrunner['status'] ==  200){
				// 	$sql = $this->db->query("update orders set delivery_partner = '123', delivery_partner_status = 'Accepted' where id='".$id."'");
				// 	echo '<script>alert("Order assigned success. delivered by roadrunner.")</script>';
				// }
			}
		}
 



		redirect('admin/orders/dashboard', 'refresh');

	}

	function ChangeDelPartnerStatus($status,$id){
		$status = $this->Order_model->ChangeDelPartnerStatus($status,$id);
		if($status){
			 redirect('admin/orders/delpartnerorders', 'refresh');
		}
	}
	function Review($type){

		$html="";
		$userdata = $this->session->userdata('admin');
		$data = $this->input->post('data');
		if($type ==  2){
			$title = "Review Customer";
			$customer_details = $this->Customer_model->get_customer($data['customer_id']);
			$name = $customer_details->firstname;
			$id= $data['id'];
		}elseif($type == 3 || $type == 5){
			$title ="Review Delivery boy";
			$deliveryboy_details = $this->Customer_model->get_deliveryboy($data['delivered_by']);
			$name = isset($deliveryboy_details->name) ? $deliveryboy_details->name : "";
			$id= $data['id'];
		}elseif($type == 4){
			$title = "Review Restaurant";
			$name = $data['restaurant_name'];
			$id= $data['restaurant_id'];
		}

		$html.="<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal'>&times;</button>
				<h4 class='modal-title'>".$title."</h4>
			  </div>

					<form id='review' class='form-horizontal' method='post'  action='InserReview'>
			  <div class='modal-body' class='form-horizontal'>
						<input type='hidden' name='order_number' value='".$data['order_number']."'>
						<input type='hidden' name='feedbacktype' value='".$type."'>
						<input type='hidden' name='feedbackfrom' value='".$userdata['id']."'>
						<div class='form-group col-sm-12 col-xs-12'>
							<label for='review' class='col-xs-12 col-sm-3'>Feedback to</label>
							<div class='col-sm-8 col-xs-12'>
							<input type='text' name='' class='form-control' value='".$name."' readonly>
							<input type='hidden' name='feedbackto' class='form-control' value='".$id."' readonly>
							</div>
						</div>
						<div class='form-group col-sm-12 col-xs-12'>
							<label for='review' class='col-xs-12 col-sm-3'>Review</label>
							<div class='col-sm-8 col-xs-12'>
								<textarea class='form-control'  id='Comments'  name='comments'></textarea>
							</div>
						</div>
						<h4 style='color: #fff;'>OR</h4>

							<label class='col-xs-12 col-sm-3'>Rating</label>
							<div class='col-sm-8 col-xs-12'>
								<input type='text' name='ratings' id='ratings' value='' class='form-control' >
							</div>
						</div>
						<div class='pop-btn'>

						</div>

			</div>
		  <div class='modal-footer'>
			<input type='submit' class='btn btn-danger' value='Review'>&nbsp;<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
		  </div></form>";
		$html.="
			<script>
			 $('#rating-input').rating({
			  min: 0,
			  max: 5,
			  step: 1,
			  size: 'xs'
			});
			</script>";
		echo $html;
	}

	function InserReview(){
		$data = $this->input->post();
		$this->Order_model->InserReview($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
    function delete($id)
    {
        $category   = $this->Restaurant_model->get_restaurant($id);
        //if the category does not exist, redirect them to the customer list with an error
        if ($category)
        {

            $this->Restaurant_model->delete($id);

            $this->session->set_flashdata('message', "The restaurant has been deleted.");
            redirect($this->config->item('admin_folder').'/restaurant');
        }
        else
        {
            $this->session->set_flashdata('error', lang('error_not_found'));
        }
    }

	function pitstops_autocomplete()
	{
		$name	= trim($this->input->post('name'));
		$limit	= $this->input->post('limit');

		if(empty($name))
		{
			echo json_encode(array());
		}
		else
		{
			$results	= $this->Restaurant_model->pitstops_autocomplete($name, $limit);

			$return		= array();

			foreach($results as $r)
			{
				$return[$r->pitstop_id]	= $r->pitstop_name;
			}
			echo json_encode($return);
		}

	}

	public function ShowReviewDetails($id){
		$this->load->model('Restaurant_model');
		$customerreview = $this->Order_model->GetCustomerReview($id);
		$customerreviewavg= isset($customerreview['avg'][0]->avg) ? $customerreview['avg'][0]->avg : 0;
		$delpartnerreview = $this->Order_model->GetDelPartnerReview($id);
		$delpartnerreviewavg = isset($delpartnerreview['avg'][0]->avg) ? $delpartnerreview['avg'][0]->avg :0;

		$delboyreview = $this->Order_model->GetDelBoyReview($id);
		$delboyreviewavg = isset($delboyreview['avg'][0]->avg) ? $delboyreview['avg'][0]->avg :0;
		$restaurant       = $this->Restaurant_model->get_restaurant($id);
		echo  "<div class='modal-header'>
		  <button type='button' class='close' data-dismiss='modal'>&times;</button>
		  <h4 class='modal-title'>Rating & reviews of ".$restaurant->restaurant_name."</h4>
		</div>
		<div class='modal-body'>
		<div class=''><strong>Ratings By customer:</strong> ".$customerreviewavg."</div>";
		// echo  "<div class=''><strong>Ratings By delivery partner:</strong> ".$delpartnerreviewavg."</div>";
	echo  "<div class=''><strong>Ratings By delivery boy:</strong> ".$delboyreviewavg."</div>";
		echo "<table class='table table-bordered'>
			<thead><tr><th>Date</th><th>Order_Number</th><th>Feedbacktype</th><th>Feedback</th><th>Stars</th><th>from</th></tr></thead>
			<tbody>";
			// if($delpartnerreview['data']){
			// 	foreach($delpartnerreview['data'] as $customer){
			// 		echo "<tr><td>".$customer->date."</td><td>".$customer->order_number."</td><td>".$customer->feedbacktype."</td><td>".$customer->comments."</td><td>".$customer->ratings."</td><td>".$customer->firstname."</td></tr>";
			// 	}
			// }
			if($customerreview['data']){
				foreach($customerreview['data'] as $customer1){
					echo "<tr><td>".$customer1->date."</td><td>".$customer1->order_number."</td><td>".$customer1->feedbacktype."</td><td>".$customer1->comments."</td><td>".$customer1->ratings."</td><td>".$customer1->firstname."</td></tr>";
				}
			}

			if($delboyreview['data']){
				foreach($delboyreview['data'] as $customer1){
					echo "<tr><td>".$customer1->date."</td><td>".$customer1->order_number."</td><td>".$customer1->feedbacktype."</td><td>".$customer1->comments."</td><td>".$customer1->ratings."</td><td>".$customer1->name."</td></tr>";
				}
			}
			// if(isset($delboyreview['data']) && $delboyreview['data']){
			// 	foreach($delboyreview['data'] as $customer1){
			// 		echo "<tr><td>".$customer1->date."</td><td>".$customer1->comments."</td><td>".$customer1->ratings."</td><td>".$customer1->name."</td></tr>";
			// 	}
			// }
		echo "</tbody>
		</table></div>";
	}

	public function ShowReviewDetailstodelpartner($id){
		$restaurantreview = $this->Order_model->GetRestaurantreview($id);
		$delpartnerreviewavg = isset($restaurantreview['avg'][0]->avg) ? $restaurantreview['avg'][0]->avg : 0;
		$admin       = $this->Order_model->get_admin($id);
		echo  "<div class='modal-header'>
		  <button type='button' class='close' data-dismiss='modal'>&times;</button>
		  <h4 class='modal-title'>Rating & reviews of ".$admin[0]->firstname."</h4>
		</div>
		<div class='modal-body'>";
		echo  "<div class=''><strong>Ratings By restaurant:</strong> ".$delpartnerreviewavg."</div>";
		echo "<table class='table table-bordered'>
			<thead><tr><th>Date</th><th>Feedback</th><th>Stars</th><th>from</th></tr></thead>
			<tbody>";
			if($restaurantreview != 0 && $restaurantreview['data']){
				foreach($restaurantreview['data'] as $customer){
					echo "<tr><td>".$customer->date."</td><td>".$customer->comments."</td><td>".$customer->ratings."</td><td>".$customer->restaurant_name."</td></tr>";
				}
			}else{
				echo "No data found";
			}

		echo "</tbody>
		</table></div>";
	}

	public function RequestBill(){
		$data['orders'] = "";
        $this->view($this->config->item('admin_folder').'/requestbill',$data);
	}

	public function GenerateBillMail()
	{
		if($this->input->post('action') == "Go"){
			$data['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			$data['todate'] = date("Y-m-d",strtotime($this->input->post('todate')));
		}elseif($this->input->post('action') == "PreviousMonth"){
			$data['fromdate'] =  date('Y-m-d',strtotime('first day of last month'));
			$data['todate'] =  date('Y-m-d',strtotime('last day of last month'));
		}else{
			$data['fromdate'] =  date('Y-m-d',strtotime('first day of this month'));
			$data['todate'] =  date('Y-m-d',strtotime('last day of this month'));
		}
		if($data['fromdate'] > date("Y-m-d",strtotime('today')) || $data['todate'] > date("Y-m-d",strtotime('today'))){
			echo "<script>alert('Select proper dates'); location.href='http://grazzy.way2gps.com/index.php/admin/orders/RequestBill';</script>";
		}
		$data['orders'] = $this->Order_model->get_previousorders($data);
		$userdata = $this->session->userdata('admin');
		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.gmail.com',
			'smtp_port' => 465,
			'smtp_user' => 'suggest.eatsapp@gmail.com',
			'smtp_pass' => 'devang123',
			'mailtype'  => 'html',
			'charset'   => 'utf-8',
			'newline'    => "\r\n"
		);
		$this->load->library('email',$config);
		$this->email->from('order@eatsapp.in', 'EatsApp');
		$this->email->to($userdata['email']);

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');



		$html= $this->load->view($this->config->item('admin_folder').'/bill',$data,true);

        $filename  = "bill.pdf";

        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
		$this->m_pdf->pdf->Output($filename, "F");
		$this->email->attach($filename);

		 //Send mail
         if($this->email->send())
		 {
			echo "<script> alert('Your bill will be emailed to registred email id.');location.href='http://grazzy.way2gps.com/index.php/admin/orders/RequestBill';</script>";
		//	echo "Your bill will be emailed to registred email id.";
         }
		 else
		 {
			// print_r($this->email->print_debugger());
			echo "Error in sending Email.";
         }
	}
	
	// public function SalesChart(){

	// 	if($this->input->post('action') == "Go"){
	// 		$data['fromdate'] = $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
	// 		 $data['todate'] = $_SESSION['todate'] = date("Y-m-d",strtotime($this->input->post('todate')));
			
	// 	}elseif($this->input->post('action') == "Previous Month"){
	// 		//$data['fromdate'] = $_SESSION['fromdate'] = date('F',strtotime("-1 Months"));
	// 		//echo $data['fromdate'];exit;
	// 		//$data['todate'] = $_SESSION['todate'] = date('y:F:d',strtotime("-1 Months"));
	// 		//echo date('y:F:d',strtotime("-1 Months"));exit;
	// 		$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of last month'));
	// 		$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of last month'));


	// 	}elseif($this->input->post('action') == "Three Months"){
	// 		$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('-3 months',strtotime('first day of this month')));
	// 		$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));


	// 	}elseif($this->input->post('action') == "Six Months"){
	// 		$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('-6 months',strtotime('first day of this month')));
	// 		$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));


	// 	}else{
	// 		$data['fromdate'] =  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
	// 		$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));
	// 		//$data['fromdate'] = $_SESSION['fromdate'] = date('F',strtotime("now"));
	// 		//echo $data['fromdate'];exit;
	// 		  // echo "select date(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d,admin e where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY date(ordered_on)";exit;

			
	// 	}

 //        $this->view($this->config->item('admin_folder').'/saleschart',$data);
 //       }  


	public function SalesChart(){

		if($this->input->post('action') == "Go"){
			$data['fromdate1'] = $_SESSION['fromdate'] = date("Y-m-d",strtotime($this->input->post('fromdate')));
			 $data['todate1'] = $_SESSION['todate'] = date("Y-m-d",strtotime($this->input->post('todate')));
			
		}elseif($this->input->post('action') == "Two Months"){
			//$data['fromdate'] = $_SESSION['fromdate'] = date('F',strtotime("-1 Months"));
			//echo $data['fromdate'];exit;
			//$data['todate'] = $_SESSION['todate'] = date('y:F:d',strtotime("-1 Months"));
			//echo date('y:F:d',strtotime("-1 Months"));exit;
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('-2 months',strtotime('first day of this month')));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));


		}elseif($this->input->post('action') == "Three Months"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('-3 months',strtotime('first day of this month')));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));


		}elseif($this->input->post('action') == "four Months"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('-4 months',strtotime('first day of this month')));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));


		}
		elseif($this->input->post('action') == "Six Months"){
			$data['fromdate'] = $_SESSION['fromdate'] = date('Y-m-d',strtotime('-6 months',strtotime('first day of this month')));
			$data['todate'] =  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));


		}else{
			//echo "test";exit;
			$data['fromdate1']=$_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			$data['todate1']=$_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));
			//$data['orders'] = $this->Order_model->getdata1();

			//print_r($data['orders']);
		// $this->view($this->config->item('admin_folder').'/restaurantdatshboard',$data);
			// $userdata = $this->session->userdata('admin');
			//  $_SESSION['fromdate'] = date('Y-m-d',strtotime('first day of this month'));
			//  $_SESSION['todate'] = date('Y-m-d',strtotime('last day of this month'));
			//  $data['formdate']=$this->db->query("select date(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.delivery_partner_status='Accepted'  and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY date(ordered_on)");
			//$data['fromdate'] = $_SESSION['fromdate'] = date('F',strtotime("now"));
			//echo $data['fromdate'];exit;
			  // echo "select date(c.ordered_on) AS day ,SUM(a.cost) AS daily_total from order_items a,restaurant_menu b,orders c,restaurant d,admin e where c.restaurant_id=b.restaurant_id and c.id=a.order_id and b.menu_id=a.menu_id and c.restaurant_id=d.restaurant_id and d.restaurant_manager = '".$userdata['id']."'  and c.ordered_on >= '".$_SESSION['fromdate']."' and c.ordered_on <= '".$_SESSION['todate']."'  GROUP BY date(ordered_on)";exit;

			
		}

        $this->view($this->config->item('admin_folder').'/saleschart',$data);
       }  

     public function data() 
        { 
          //if($this->input->post('action') == "Go"){
          echo json_encode($this->Order_model->getdata());
           //}
  
        } 

        public function data1() 
        { 
  
          echo json_encode($this->Order_model->getdata1());

  
        } 
	
	public function renew(){
		$userdata = $this->session->userdata('admin');

		$sql = $this->db->query("update admin set RenewalAppliedStatus = 1 where id='".$userdata['id']."'");
		$message = "".$userdata['firstname']."(Email: ".$userdata['email'].") has requested for renewal)";
		$sql = $this->db->query("insert into notification_message (message) value('".$message."')");
		echo "<script>alert('Notification sent. admin will contact you soon');location.reload();</script>";
		 redirect($this->config->item('admin_folder').'/orders/dashboard');
	}

	function GetMenudetails1(){
		$data = $this->input->post('data');
		
		$menus = $this->Order_model->GetMenudetails1($data);
		
		$html="";
		if($menus != 0){
			if($data['ordertype_id'] == 3){
			 $customer_details = $this->Customer_model->get_customer($data['customer_id']);
			 $name = $customer_details->firstname." ".$customer_details->lastname;
			 $phone = $customer_details->phone;
			 $email = $customer_details->email;
			  $email = $customer_details->email;
			}else{
				
				$deliveryboy_details = $this->Customer_model->get_deliveryboy($data['delivered_by']);
				$name = isset($deliveryboy_details->name) ? $deliveryboy_details->name : "Not assigned yet";
				$phone = isset($deliveryboy_details->phone) ? $deliveryboy_details->phone : "";
				$email = isset($deliveryboy_details->email) ? $deliveryboy_details->email : "";
				
			}
			$html.="<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal'>&times;</button>
					<h4 class='modal-title'>Order Number: ".$data['order_number']."</h4>
				  </div>
				  <div class='modal-body' class='form-horizontal'>
					<div class='form-group'>
						<label><strong>"; if($data['ordertype_id'] == 3){ $html.="Customer name";}else{$html.="Delivery boy";} $html.=": </strong>".$name."</label></br>
						<label><strong>Mobile No: </strong>".$phone."</label></br>
						
						

					</div>
					<table class='table table-bordered'>
					<thead>
						<tr><th>RestName</th><th>Order Type</th><th>Scheduled Pickup Time</th><th>Scheduled Delivery Time</th><th>Actual Pickup Time</th><th>Actual Delivery Time</th><tr>
					</thead>
					<tbody>";
			foreach($menus as $menu){
					if($data['ordertype_id'] == 3){ 
					$html.="<tr><td>".$menu->restaurant_name."</td><td>".$menu->order_type."</td><td>".$menu->keep_ready."</td><td>".$menu->keep_ready."</td><td>".$menu->actualpickup_time."</td><td>".$menu->actualdelivery_time."</td></td>";
                }else{
					$html.="<tr><td>".$menu->restaurant_name."</td><td>".$menu->order_type."</td><td>".$menu->keep_ready."</td><td>".$menu->delivered_on."</td><td>".$menu->actualpickup_time."</td><td>".$menu->actualdelivery_time."</td></td>";
				}

			}
			$html.="</tbody>
				</table>
				</div>
			  <div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			  </div>";
		}
		echo $html;
	}


}
