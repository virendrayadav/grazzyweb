<?php
Class Menu_model extends CI_Model
{
	function GetMenus($id){
		$sql = "SELECT * FROM `restaurant_menu` where restaurant_id=$id and `delete` = 0 order by menu_id DESC";
		
		$query = $this->db->query($sql);
		if($query->num_rows() ==''){
			$result =0; 		
		}else{
			$result = $query->result();
		} 
		
		return $result;
	}
	
	function GetMenu($id){
		$result	= $this->db->get_where('restaurant_menu', array('menu_id'=>$id,'delete'=>0))->row();
		//echo $this->db->last_query(); exit;
		if(!$result)
		{
			return false;
		}
		$result->categories			= $this->get_menu_categories($result->menu_id);

		return $result;
	}
	
	
	
	function InsertMenus($menus,$id){
		foreach($menus as $men){
			foreach($men as $menu){
				//print_r($menu);exit;
				$categories = explode(",", $menu['Category']);
	
				$sql =$this->db->query("INSERT INTO `restaurant_menu`(`restaurant_id`, `code`, `menu`, `description`, `price`, `type`, `itemPreparation_time`, `enabled`
				) VALUES ('".$id."','".$menu['Code']."','".$menu['Menu']."','".$this->db->escape_str($menu['Description'])."','".$menu['Price']."',
				'".$menu['Type']."','".$menu['Item Preparation Time']."','".$menu['Enabled']."')");
				$menu_category = $this->db->insert_id();
				if(count($categories) > 0){
					foreach($categories as $cat){
						//print_r($cat);exit;
						$sql =$this->db->query("INSERT INTO `menu_categories`(category_id,menu_category) VALUES ('".$cat."','".$menu_category."')");
					}
				}
			}
		}
	}

	// function InsertCustomisation($menus,$id){
       
	// 	foreach($menus as $men){
			
	// 			$menu_category = $this->db->insert_id();
				
	// 			$values=$men;
				
 //                $array1=array();
				

	// 			for($i=0;$i<count($men);$i++)
	// 			{
	// 				$data1=array();
	// 			// $data1[0]['name']=$men[$i+1]['name1'];
	// 			// $data1[0]['weight']=$men[$i+1]['weight'];
	// 			// $data1[0]['price']=$men[$i+1]['price'];
				
 //               for($j=0;$j<count($men);$j++)
	// 			{
	// 			$data1[$j]['name']=$men[$j+1]['name1'];
	// 			$data1[$j]['weight']=$men[$j+1]['weight'];
	// 			$data1[$j]['price']=$men[$j+1]['price'];

 //                }

				
	// 			$array1[$i]['type']=$men[$i+1]['type'];
	// 			$array1[$i]['name']=$men[$i+1]['name'];
	// 			// $k=0;
	// 			// //print_r($men[$i+4]['name']);exit;
	// 			//  if($array1[$i]['name']==$men[$i+$k]['name'])
	// 			//  {
 //    //                   $array1[$i]['name']=$men[$i+1]['name'];
	// 			//  }
	// 			//  $k++;
	// 			$array1[$i]['values']=$data1;

				

	// 			}
	// 			$array2=array($array1);
				
                 
 //      $sql=$this->db->query("update `restaurant_menu` set `customisation`= '".serialize($array2[0])."'  where `menu_id`='".$id."' ");

      

 //                $menu_category = $this->db->insert_id();
	// 			if(count($categories) > 0){
	// 				foreach($categories as $cat){
	// 					$sql =$this->db->query("INSERT INTO `menu_categories`(category_id,menu_category) VALUES ('".$cat."','".$menu_category."')");
	// 				}
	// 			}
	// 		//}
	// 	}
	// }


	function InsertCustomisation($menus,$id){
       
		foreach($menus as $men){
			
				$menu_category = $this->db->insert_id();
				
				$values=$men;
				
                $array1=array();
				

				for($i=0;$i<count($men);$i++)
				{
					$data1=array();
				// $data1[0]['name']=$men[$i+1]['name1'];
				// $data1[0]['weight']=$men[$i+1]['weight'];
				// $data1[0]['price']=$men[$i+1]['price'];
				
    //           for($j=0;$j<=$i;$j++)
				// {
				$data1[$i]['name']=$men[$i+1]['name1'];
				$data1[$i]['weight']=$men[$i+1]['weight'];
				$data1[$i]['price']=$men[$i+1]['price'];

				$data1[$i+1]['name']=$men[$i+1]['name2'];
				$data1[$i+1]['weight']=$men[$i+1]['weight1'];
				$data1[$i+1]['price']=$men[$i+1]['price1'];

				$data1[$i+2]['name']=$men[$i+1]['name3'];
				$data1[$i+2]['weight']=$men[$i+1]['weight2'];
				$data1[$i+2]['price']=$men[$i+1]['price2'];


				$data1[$i+3]['name']=$men[$i+1]['name4'];
				$data1[$i+3]['weight']=$men[$i+1]['weight3'];
				$data1[$i+3]['price']=$men[$i+1]['price3'];

				$data1[$i+4]['name']=$men[$i+1]['name5'];
				$data1[$i+4]['weight']=$men[$i+1]['weight4'];
				$data1[$i+4]['price']=$men[$i+1]['price4'];

				$data1[$i+5]['name']=$men[$i+1]['name6'];
				$data1[$i+5]['weight']=$men[$i+1]['weight5'];
				$data1[$i+5]['price']=$men[$i+1]['price5'];

				$data1[$i+6]['name']=$men[$i+1]['name7'];
				$data1[$i+6]['weight']=$men[$i+1]['weight6'];
				$data1[$i+6]['price']=$men[$i+1]['price6'];


				$data1[$i+7]['name']=$men[$i+1]['name8'];
				$data1[$i+7]['weight']=$men[$i+1]['weight7'];
				$data1[$i+7]['price']=$men[$i+1]['price7'];


				$data1[$i+8]['name']=$men[$i+1]['name9'];
				$data1[$i+8]['weight']=$men[$i+1]['weight8'];
				$data1[$i+8]['price']=$men[$i+1]['price8'];

				
               // }

				
				$array1[$i]['type']=$men[$i+1]['type'];
				$array1[$i]['name']=$men[$i+1]['name'];
				$array1[$i]['values']=$data1;
				
				
}
				$array2=array($array1);
				
                 
      $sql=$this->db->query("update `restaurant_menu` set `customisation`= '".$this->db->escape_str(serialize($array2[0]))."'  where `menu_id`='".$id."' ");

      

                
				
			
		}
	}




// function InsertCustomisation1($menus,$id,$res_id){
// 		foreach($menus as $men){
// 			foreach($men as $menu){
// 				//print_r($menu);exit;
// 				//$categories = explode(":", $menu['Category']);
				
// 				$sql =$this->db->query("INSERT INTO `customization`(`menus_id`,`restaurant_id`, `type`, `name`, `name1`, `weight`, `price`
// 				) VALUES ('".$id."','".$res_id."','".$menu['type']."','".$menu['name']."','".$menu['name1']."','".$menu['weight']."',
// 				'".$menu['price']."')");
// 				//$menu_category = $this->db->insert_id();
// 				// if(count($categories) > 0){
// 				// 	foreach($categories as $cat){
// 				// 		$sql =$this->db->query("INSERT INTO `menu_categories`(category_id,menu_category) VALUES ('".$cat."','".$menu_category."')");
// 				// 	}
// 				// }
// 			}
// 		}
// 	}

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
		$data['delete'] = 1;
        $this->db->where('menu_id', $id);
		$this->db->where('restaurant_id', $res_id);
        //$this->db->delete('restaurant_menu');
		 $this->db->update('restaurant_menu',$data);
       
    }
	
	function MenuStatusChange($data){
		
		 if ($data['restaurant_id'] && $data['menu_id'])
        {
			$this->db->where('menu_id', $data['menu_id']);
            $this->db->where('restaurant_id', $data['restaurant_id']);
            $this->db->update('restaurant_menu', $data);
        }
	}
}