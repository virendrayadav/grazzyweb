<?php
Class Pitstop_model extends CI_Model
{

    function get_pitstops_tiered($admin = false)
    {
        if(!$admin)
        $this->db->where('enabled', 1);
		$this->db->where('delete', 0);
        $this->db->order_by('pitstop_name', 'ASC');
        $pitstops = $this->db->get('pitstops')->result();
      
        return $pitstops;
    }
	
	function CheckConnection($id){
		$sql = $this->db->query("select * from pitstop_restaurants where pitstop_id='".$id."'");
		
		if($sql->num_rows() > 0){ return "<span style='color:green;font-weight:bold'>Yes</span>"; }else{
			return "<span style='color:red;font-weight:bold'>No</span>";
		}
	}
    function InsertPitstops($pitstops){
		
		foreach($pitstops as $men){
			foreach($men as $pitstop){
				$sql =$this->db->query("INSERT INTO `pitstops`(`pitstop_name`, `latitude`, `langitude`, `enabled`,`city`) 
				VALUES ('".$pitstop['Delivery Point name']."','".$pitstop['Latitude']."','".$pitstop['Longitude']."','".$pitstop['Enabled']."','".$pitstop['City']."')");
			}
		}
	}
	
	function ChangeStatus($data){
		if ($data['pitstop_id'])
        {
			$this->db->where('pitstop_id', $data['pitstop_id']);
            $this->db->update('pitstops', $data);
			//echo $this->db->last_query(); exit;
			return true;
        }
		
	}
	
    function get_pitstop($id,$related_restaurants=true)
    {
		
		$result	= $this->db->get_where('pitstops', array('pitstop_id'=>$id))->row();
		if(!$result)
		{
			return false;
		}
		
		$sql	=  "select * from pitstop_restaurants a, restaurant b where a.pitstop_id = ".$id." and a.restaurants_id =b.restaurant_id";
		$query = $this->db->query($sql);
		
		if($query->num_rows() > 0){
			$result->related_restaurants	= $query->result();
		}
		else
		{
			$result->related_restaurants	= array();
		}
		return $result;
    }
    
    function get_category_products_admin($id)
    {
        $this->db->order_by('sequence', 'ASC');
        $result = $this->db->get_where('category_products', array('category_id'=>$id));
        $result = $result->result();
        
        $contents   = array();
        foreach ($result as $product)
        {
            $result2    = $this->db->get_where('products', array('id'=>$product->product_id));
            $result2    = $result2->row();
            
            $contents[] = $result2; 
        }
        
        return $contents;
    }
    
   
    
    function save($pitstop,$restaurants)
    {
		
        if ($pitstop['pitstop_id'])
        {
            $this->db->where('pitstop_id', $pitstop['pitstop_id']);
            $this->db->update('pitstops', $pitstop);
            
            $id= $pitstop['pitstop_id'];
        }
        else
        {
            $this->db->insert('pitstops', $pitstop);
            $id = $this->db->insert_id();
        }
		
		if(count($restaurants) >= 0){
			 $this->db->where('pitstop_id', $id);
			$this->db->delete('pitstop_restaurants');
			foreach($restaurants as $restaurant){
				$pitstop_restaurants = array('pitstop_id'=> $id,'restaurants_id'=>$restaurant);
				$this->db->insert('pitstop_restaurants', $pitstop_restaurants);
				
			}
		}
    }
    
    function delete($id)
    {

        $this->db->where('pitstop_id', $id);
        $this->db->delete('pitstops');
		// $data['delete'] = 1 ;
  //       $this->db->where('pitstop_id', $id);
  //       $this->db->update('pitstops',$data);
		//echo $this->db->last_query(); exit;
       // $sql = $this->db->query('update pitstops set `delete`=1 where pitstop_id="'.$id.'"');
        //delete references to this category in the product to category table
       // $this->db->where('category_id', $id);
        //$this->db->delete('category_products');
    }
	
	function restaurants_autocomplete($name, $limit)
	{
		return	$this->db->like('restaurant_name', $name)->get('restaurant', $limit)->result();
	}

// 	public function getpitstop($id)
// {

// 		// $sql = $this->db->query("select a.*,b.city from pitstopcity a,pitstop b where pitstop_id='".$id."' ");
// 		// if($sql->num_rows() > 0){
// 		// 	$result	= $sql->result();
// 		// }else{
// 		// 	$result = 0;
// 		// }
// 		// return $result;
      
//       $query=$this->db->get('pitstopcity',$id);
//       if($query->num_rows()>0){
//       return $query->result();
// }
// }


function get_class()
    {
       $class=$this->db->get('pitstopcity');
       return $class->result_array();
    }

// public function getpitstop($id)
//     {
		
// 		$result	= $this->db->get_where('pitstopcity', array('Id'=>$id))->row();
// 		if(!$result)
// 		{
// 			return false;
// 		}
		
		
// 		return $result;
//     }
}