<?php

class Login extends Base_Controller {

	function __construct()
	{
		parent::__construct();
		$this->lang->load('login');
	}

	function index()
	{

		//we check if they are logged in, generally this would be done in the constructor, but we want to allow customers to log out still
		//or still be able to either retrieve their password or anything else this controller may be extended to do
		$redirect	= $this->auth->is_logged_in(false, false);
		//if they are logged in, we send them back to the dashboard by default, if they are not logging in
		if ($redirect)
		{
			//redirect($this->config->item('admin_folder').'/customers');
			redirect($this->config->item('admin_folder'));
		}
		
		
		$this->load->helper('form');
		$data['redirect']	= $this->session->flashdata('redirect');
		$submitted 			= $this->input->post('submitted');
		if ($submitted)
		{
			$username	= $this->input->post('username');
			$password	= $this->input->post('password');
			$remember   = $this->input->post('remember');
			$redirect	= $this->input->post('redirect');
			$login		= $this->auth->login_admin($username, $password, $remember);
			//print_r($login);exit;
			
			if ($login)
			{
				if ($redirect == '')
				{
					$userdata = $this->session->userdata('admin');
					//print_r($userdata['enabled']);exit;
					if($this->auth->check_access('Restaurant manager')){
						if($userdata['enabled']==1)
						{
                     

						$date = date('Y-m-d');
						// $sql = $this->db->query("select * from admin where  username='".$username."'");
						 $sql = $this->db->query("select * from admin a,restaurant b where  a.username='".$username."' and  b.restaurant_manager=a.id and b.enabled=1  ");
						
						//print_r("select * from admin where NextRenewalDate > '".$date."' and username='".$username."'"); exit;
                    	if($sql->num_rows() > 0){
							$redirect = $this->config->item('admin_folder').'/orders/dashboard';
						}else{
							$this->auth->logout();
							//$this->session->set_flashdata('error', 'Your renewal date expired');
							$this->session->set_flashdata('error','It appears that your access to the Portal has been restricted by the Administrator. Please write to contact@eatsapp.in or contact on +919820076457');
							redirect($this->config->item('admin_folder').'/login');
						}
					 }else{

                        
						$date = date('Y-m-d');
						// $sql = $this->db->query("select * from admin where  username='".$username."'");
						$sql = $this->db->query("select * from admin a,restaurant b where  a.username='".$username."' and  b.restaurant_manager=a.id and b.enabled=1  ");
						//print_r("select * from admin where NextRenewalDate > '".$date."' and username='".$username."'"); exit;

						if($sql->num_rows() > 0){
							$redirect = $this->config->item('admin_folder').'/orders/dashboard';
						}else{
							$this->auth->logout();
							//$this->session->set_flashdata('error', 'Your renewal date expired');
							$this->session->set_flashdata('error','It appears that your access to the Portal has been restricted by the Administrator. Please write to contact@eatsapp.in or contact on +919820076457');
							redirect($this->config->item('admin_folder').'/login');
						}

					 }
						
					}elseif($this->auth->check_access('Admin')){
						$redirect = $this->config->item('admin_folder').'/dashboard';
					}else{
						
						$date = date('Y-m-d');
						
						// $sql = $this->db->query("select * from admin where NextRenewalDate > '".$date."' and username='".$username."' ");

						$sql = $this->db->query("select * from admin where username='".$username."' and enabled=1 ");

						
						if($sql->num_rows() > 0){
							$redirect = $this->config->item('admin_folder').'/orders/delpartnerorders';
						}else{
							$this->auth->logout();
							// $this->session->set_flashdata('error', 'Your renewal date expired');
							$this->session->set_flashdata('error', 'It appears that your access to the Portal has been restricted by the Administrator. Please write to contact@eatsapp.in or contact on +919820076457');
							redirect($this->config->item('admin_folder').'/login');
						}
						
					}
				}
				redirect($redirect);
			}
			else
			{
				//this adds the redirect back to flash data if they provide an incorrect credentials
				$this->session->set_flashdata('redirect', $redirect);
				$this->session->set_flashdata('error', lang('error_authentication_failed'));
				redirect($this->config->item('admin_folder').'/login');
			}
		}
		$this->load->view($this->config->item('admin_folder').'/login', $data);
	}
	
	function logout()
	{
		$this->auth->logout();
		
		//when someone logs out, automatically redirect them to the login page.
		$this->session->set_flashdata('message', lang('message_logged_out'));
		redirect($this->config->item('admin_folder').'/login');
	}
	
	function forgot_password()
	{
		$submitted = $this->input->post('submitted');
		if ($submitted)
		{
			$this->load->helper('string');
			$email = $this->input->post('email');
			
			$reset = $this->Customer_model->reset_password($email);
			
			if ($reset)
			{						
				$this->session->set_flashdata('message',"Password mailed");
			}
			else
			{
				$this->session->set_flashdata('error', "Mail id is not registered");
			}
			redirect('admin/forgot_password');
		}
		
		// load other page content 
		
		$this->load->view($this->config->item('admin_folder').'/forgot_password', $data);
	}

}
