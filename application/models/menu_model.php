<?php
Class Menu_model extends CI_Model
{
	function GetMenus($id){
		$sql = "SELECT * FROM `restaurant_menu` where restaurant_id=$id";
		$query = $this->db->query($sql);
		if($query->num_rows() ==''){
			$result =0; 		
		}else{
			$result = $query->result();
		} 
		
		return $result;
	}
	
	function GetMenu($id){
		$result	= $this->db->get_where('restaurant_menu', array('menu_id'=>$id))->row();
		//echo $this->db->last_query(); exit;
		if(!$result)
		{
			return false;
		}
		$result->categories			= $this->get_menu_categories($result->menu_id);

		return $result;
	}
	
	function save($menu, $categories=false)
	{
		
		 if($menu['menu_id'] == ""){$menu['menu_id'] = false;}
		if ($menu['menu_id'])
		{
			$this->db->where('menu_id', $menu['menu_id']);
			$this->db->update('restaurant_menu', $menu);

			$id	= $menu['menu_id'];
		}
		else
		{
			$this->db->insert('restaurant_menu', $menu);
			$id	= $this->db->insert_id();
		}
		
		if($categories !== false)
		{
			
			if($menu['menu_id'])
			{
				//get all the categories that the product is in
				$cats	= $this->get_menu_categories($menu['menu_id']);
				
				//generate cat_id array
				$ids	= array();
				foreach($cats as $c)
				{
					$ids[]	= $c->id;
				}

				//eliminate categories that products are no longer in
				foreach($ids as $c)
				{
					if(!in_array($c, $categories))
					{
						$this->db->delete('menu_categories', array('menu_category'=>$id,'category_id'=>$c));
					}
				}
				
				//add products to new categories
				foreach($categories as $c)
				{
					if(!in_array($c, $ids))
					{
						$this->db->insert('menu_categories', array('menu_category'=>$id,'category_id'=>$c));
					}
				}
			}
			else
			{
				//new product add them all
				foreach($categories as $c)
				{
					$this->db->insert('menu_categories', array('menu_category'=>$id,'category_id'=>$c));
				}
			}
		}
		
		
		//return the product id
		return $id;
	}
	
	function get_menu_categories($id)
	{
		return $this->db->where('menu_category', $id)->join('categories', 'category_id = categories.id')->get('menu_categories')->result();
	}
	
	  function delete($id,$res_id)
    {
        $this->db->where('menu_id', $id);
		$this->db->where('restaurant_id', $res_id);
        $this->db->delete('restaurant_menu');
       
    }
}