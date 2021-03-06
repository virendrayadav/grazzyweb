<?php

class Pitstop extends Admin_Controller { 
    
    function __construct()
    {       
        parent::__construct();
        
        $this->auth->check_access('Admin', true);
        $this->lang->load('category');
		$this->lang->load('product');
        $this->load->model('Pitstop_model');
    }
    
	function Deleteall(){
		// foreach($_POST['DeleteOptions'] as $key=>$DeleteOption){
			$sql = $this->db->query('delete from pitstops');
			//$sql = $this->db->query('update pitstops set delete=1 where pitstop_id="'.$key.'"');
		//}
		redirect("admin/pitstop");
	}
	
	public function ChangeStatus($id=false,$status=false){
		$enabled = $this->input->post('enabled');
		$data['pitstop_id'] = false == $this->input->post('pitid') ? $id : $this->input->post('pitid');
		$data['enabled'] = isset($enabled) ? $enabled : 1;
		$data['deactivatefrom'] = date('Y-m-d',strtotime($this->input->post('FromDate')));
		$data['deactivateto'] = date('Y-m-d',strtotime($this->input->post('ToDate')));
		$result = $this->Pitstop_model->ChangeStatus($data);
		if($result){
			redirect("admin/pitstop");
		}
	}
	
    function index()
    {       
		$data['page_title'] = 'Pitstops';
        $data['pitstops'] = $this->Pitstop_model->get_pitstops_tiered(true);
		
        $this->view($this->config->item('admin_folder').'/pitstops', $data);
    }
   
	function ImportPitstops()
	{
			$target_file =  basename($_FILES["pitstopfile"]["name"]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$uploadOk = 0;
			if($imageFileType == "csv"){
				$uploadOk = 1;
			}
			if ($uploadOk == 1) {
				
				if (move_uploaded_file($_FILES["pitstopfile"]["tmp_name"], "uploads/" . basename($_FILES["pitstopfile"]["name"]))) {
						$this->load->library('csvreader');
						$result =   $this->csvreader->parse_file("uploads/".$_FILES["pitstopfile"]["name"]);//path to csv file
						
						$data['pitstops'] =  $result;
						$this->Pitstop_model->InsertPitstops($data);
						unlink("uploads/".$_FILES["pitstopfile"]["name"]); 
						redirect('admin/pitstop/index', 'refresh');
						
				}
			
			}
		
	}
    function form($id = false)
    {
        
        $config['upload_path']      = 'uploads/images/full';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']         = $this->config->item('size_limit');
        $config['max_width']        = '1024';
        $config['max_height']       = '768';
        $config['encrypt_name']     = true;
		$data['related_restaurants']	= array();
        $this->load->library('upload', $config);
        
        
        $this->pitstop_id  = $id;
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $data['getpitstop'] = $this->Pitstop_model->get_class();
        //$data['pitstops']     = $this->Pitstop_model->get_pitstops_tiered();
        $data['page_title']     = lang('category_form');
        
        //default values are empty if the customer is new
		
        $data['pitstop_id']             = '';
        $data['pitstop_name']           = '';

        $data['latitude']           = '';
        $data['langitude']      = '';
		// $data['address']      = '';
		$data['city']      = '';
        $data['enabled']        = '';
       
        
        if ($id)
        {   
            //$pitstop1 = $this->Pitstop_model->getpitstop($id);
		
            $pitstop       = $this->Pitstop_model->get_pitstop($id);
            //print_r($pitstop);exit;         

            //if the category does not exist, redirect them to the category list with an error
            if (!$pitstop)
            {
                //$this->session->set_flashdata('error', lang('error_not_found'));
                redirect($this->config->item('admin_folder').'/pitstops');
            }
            
            
			
            $data['pitstop_id']             = $pitstop->pitstop_id;
            $data['pitstop_name']           = $pitstop->pitstop_name;
            $data['latitude']           = $pitstop->latitude;
            $data['langitude']    = $pitstop->langitude;
			// $data['address']    = $pitstop->address;
			$data['city']    = $pitstop->city;
            //print_r($data['city']);exit;
            $data['enabled']        = $pitstop->enabled;
			if(!$this->input->post('submit'))
			{
				$data['related_restaurants']	= $pitstop->related_restaurants;
			}
			if(!is_array($data['related_restaurants']))
			{
				$data['related_restaurants']	= array();
			}
            
        }
        
        $this->form_validation->set_rules('pitstop_name', 'lang:pitstop_name', 'trim|required|max_length[64]');
        $this->form_validation->set_rules('latitude', 'lang:latitude', 'trim');
        $this->form_validation->set_rules('langitude', 'lang:langitude', 'trim');
        $this->form_validation->set_rules('enabled', 'lang:enabled', 'trim|numeric');
        
        if($this->input->post('submit'))
		{
			$data['related_restaurants']	= $this->input->post('related_restaurants');
		}
        // validate the form
        if ($this->form_validation->run() == FALSE)
        {
            $this->view($this->config->item('admin_folder').'/pitstop_form', $data);
        }
        else
        {
            
            
            $save['pitstop_id']             = $id;
			
            $save['pitstop_name']           = $this->input->post('pitstop_name');
            $save['latitude']    = 	$this->input->post('latitude');
            $save['langitude']        = $this->input->post('langitude');
            // $save['address']        = $this->input->post('address');
		    $save['city']        = $this->input->post('city');
            $save['enabled']        = $this->input->post('enabled');
		
			if($this->input->post('related_restaurants'))
			{
				$related_restaurants = $this->input->post('related_restaurants');
			}
			else
			{
				$related_restaurants = array();
			}
			
            $pitstop_id    = $this->Pitstop_model->save($save,$related_restaurants);
       
            $this->session->set_flashdata('message', 'pitstop saved');
            
            //go back to the category list
            redirect($this->config->item('admin_folder').'/pitstop');
        }
    }

    function delete($id)
    {
        
        $pitstop   = $this->Pitstop_model->get_pitstop($id);
        //if the category does not exist, redirect them to the customer list with an error
        if ($pitstop)
        {
            $this->Pitstop_model->delete($id);
            
            $this->session->set_flashdata('message', "The pitstop has been deleted.");
            redirect($this->config->item('admin_folder').'/pitstop');
        }
        else
        {
            $this->session->set_flashdata('error', lang('error_not_found'));
        }
    }
	
	function restaurants_autocomplete()
	{
		$name	= trim($this->input->post('name'));
		$limit	= $this->input->post('limit');
		
		if(empty($name))
		{
			echo json_encode(array());
		}
		else
		{
			$results	= $this->Pitstop_model->restaurants_autocomplete($name, $limit);
			
			$return		= array();
			
			foreach($results as $r)
			{
				$return[$r->restaurant_id]	= $r->restaurant_name;
			}
			echo json_encode($return);
		}
		
	}

	function get_pitstop_list()
	{
		
	$this->load->library('excel');
    //Create a new Object
    $objPHPExcel = new PHPExcel();
    // Set the active Excel worksheet to sheet 0
    $objPHPExcel->setActiveSheetIndex(0); 

    $heading=array('PitstopName','City','Lattitude','Longitude','Enabled','Restaurant_name'); 
    //set title in excel sheet
    $rowNumberH = 1; //set in which row title is to be printed
    $colH = 'A'; //set in which column title is to be printed
    
    $objPHPExcel->getActiveSheet()->getStyle($rowNumberH)->getFont()->setBold(true);
    
	for($col = ord('A'); $col <= ord('F'); $col++){ //set column dimension 
		 $objPHPExcel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
	}
    foreach($heading as $h){ 

        $objPHPExcel->getActiveSheet()->setCellValue($colH.$rowNumberH,$h);
        $colH++;    
    }

    $export_excel = $this->db->query("select * from pitstops a,restaurant b ,pitstop_restaurants c where a.pitstop_id=c.pitstop_id and c.restaurants_id=b.restaurant_id  and a.delete=0 ")->result_array();

     $rowCount = 2; // set the starting row from which the data should be printed
    foreach($export_excel as $excel)
    {  

        // $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $excel['pitstop_id']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $excel['pitstop_name']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $excel['city']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $excel['latitude']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $excel['langitude']);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $excel['enabled']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $excel['restaurant_name']); 
       $rowCount++; 
    } 

    // Instantiate a Writer 
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="PitstopConnectedInformationOnly.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
    //exit();

	
		
		
	}	
}