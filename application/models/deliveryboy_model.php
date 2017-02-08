<?php
Class Deliveryboy_model extends CI_Model
{

    var $CI;

    function __construct()
    {
        parent::__construct();

        $this->CI =& get_instance();
        $this->CI->load->database(); 
        $this->CI->load->helper('url');
    }
	function get_deliverypartner_list(){
		$sql = $this->db->query("select * from admin where access='Deliver manager'");
		if($sql->num_rows() > 0){
			$result	= $sql->result();
		}else{
			$result = 0;
		}
		return $result;
	}
    function get_lists()
    {
		$userdata = $this->session->userdata('admin');
		$this->db->order_by('id', 'ASC');
		$this->db->where('delivery_partner', $userdata['id']);
		$this->db->where('enabled', 1);
        $result = $this->db->get('delivery_boy');
		//echo $this->db->last_query(); exit;
        return $result->result();
    }
    
	function get_deliveryboys($parent)
	{
		
		$this->db->order_by('id', 'ASC');
		$this->db->where('id', $parent);
		$this->db->where('enabled', 1);
		$result = $this->db->get('delivery_boy')->result();
		//echo $this->db->last_query(); exit;
		$return	= array();
		foreach($result as $page)
		{

			$return[]				= $page;
		}
		
		return $return;
	}
	
	 function get_deliveryboy($id)
    {
        $result = $this->db->get_where('delivery_boy', array('id'=>$id));
        return $result->row();
    }
    function get_deliveryPartner($id)
    {
        $result = $this->db->get_where('admin', array('id'=>$id,'access'=>'Deliver manager'));
        return $result->row();
    }
    
    function save($delivery_boy)
    {
        if ($delivery_boy['id'])
        {
            $this->db->where('id', $delivery_boy['id']);
            $this->db->update('delivery_boy', $delivery_boy);
            return $delivery_boy['id'];
        }
        else
        {
            $this->db->insert('delivery_boy', $delivery_boy);
            return $this->db->insert_id();
        }
    }
    
    function deactivate($id)
    {
        $customer   = array('id'=>$id, 'active'=>0);
        $this->save_customer($customer);
    }
    
    function DeleteDeliveryBoy($id)
    {
       
        $this->db->where('id', $id);
        $this->db->delete('delivery_boy');
        
        
    }
	
	function GetReviewDelPartner($id,$type){
		$sql = $this->db->query("select a.*,b.firstname from feedback a, admin b where a.feedbackfrom=b.id and a.feedbacktype='".$type."' and a.feedbackto='".$id."'");
		if($sql->num_rows() > 0){
			$result['data']	= $sql->result();
			$sql1 = $this->db->query("select AVG(ratings) as avg from feedback where feedbacktype='".$type."' and feedbackto='".$id."'");
			$result['avg']	= $sql1->result();
		}else{
			$result = 0;
		}
		return $result;
	}
	
	function get_ListValues($id){
		$sql = $this->db->query("select * from delpartner_charges where delpartner_id='".$id."'");
		if($sql->num_rows() > 0){
			$result['data']	= $sql->result();
			
		}else{
			$result = 0;
		}
		return $result;
	}
	
	function SaveCharges($data,$id){
		
		$sql = $this->db->query("delete from delpartner_charges where delpartner_id='".$id."'");
		if($sql){
			foreach($data as $datas){
				$this->db->insert('delpartner_charges', $datas);
			}
		}
	}
	function ChangeStatus($id,$status){
		$sql = $this->db->query("update admin set enabled='".$status."' where id='".$id."'");
		if($sql){ return true; }
	}
	
	
}