<?php if(!defined('BASEPATH')) exit('No direct script allowed access');
class Api_model extends CI_Model
{
	
	public function customercheck($data){
		$sql = "select id from customers where firstname='".$data['firstname']."' and phone ='".$data['phone']."'";
		$query = $this->db->query($sql);
		if($query->num_rows() == 0){
			$sql = "insert into customers (firstname, phone) values('".$data['firstname']."','".$data['phone']."')";	
			$query = $this->db->query($sql);
			$id = $this->db->insert_id();
			
		}else{
			$id = $query->result();
		}
		return $id;
	}
	
	public function getUsers(){
		
		$threadmsg = $this->db->query("select * from customers");

			if($threadmsg->num_rows()>0){

				return $threadmsg->result_array();

			}else{
			
				return false;
				
			}
			
		
	}
	
	public function getRestaurants($id){
		
		$threadmsg = $this->db->query("select a.* from restaurant a, pitstops b, pitstop_restaurants c where 
		a.restaurant_id = c.restaurants_id and b.pitstop_id=c.pitstop_id and b.pitstop_id=".$id);

			if($threadmsg->num_rows()>0){
				$result = array();
				$i=0;
				foreach($threadmsg->result_array() as $row){ 
					$result[$i]['restaurant_id'] = $row['restaurant_id'];
					$result[$i]['restaurant_name'] = $row['restaurant_name'];
					$result[$i]['image'] = 'uploads/images/thumbnails/'.$row['image']; 
				$i++;
				}
				return $result;
			}else{
			
				return false;
				
			}
			
		
	}
	
	public function getAddress($id){
		
		$addresses = $this->db->where('customer_id', $id)->get('customers_address_bank')->result_array();
		
        // unserialize the field data
        if($addresses)
        {
            foreach($addresses as $add)
            {
                $addr[] = unserialize($add['field_data']);
            }
			return $addr;
        }
        else{
			return false;
		}
		
	}
	
	 function saveAddress($data)
    {
        $data['field_data'] = serialize($data['field_data']);
       
        if(!empty($data['id']))
        {
            $this->db->where('id', $data['id']);
            $this->db->update('customers_address_bank', $data);
            return $data['id'];
        } else {
            $this->db->insert('customers_address_bank', $data);
            return $this->db->insert_id();
        }
    }
	
	public function pitstopsuser($data){
		$sql = "SELECT * FROM `pitstops` WHERE `latitude` > '".$data['southwest_lat']."' and `latitude` < '".$data['northeast_lat']."'
		and `langitude` > '".$data['southwest_lng']."' and`langitude` < '".$data['northeast_lng']."'";
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[$row['pitstop_id']]['pitstop_id'] = $row['pitstop_id'];
				$result[$row['pitstop_id']]['latitude'] = $row['latitude'];
				$result[$row['pitstop_id']]['langitude'] = $row['langitude'];
				$sql1 = "SELECT * FROM `pitstop_restaurants` a, restaurant b where a.pitstop_id ='".$row['pitstop_id']."' and a.restaurants_id = b.restaurant_id";
				$query1 = $this->db->query($sql1);
				if($query1->num_rows()>0){
					$j=0;
					foreach($query->result_array() as $row){ 
						$result[$row['pitstop_id']][$j]['restaurant_name'] = $row['restaurant_name'];
						$result[$row['pitstop_id']][$j]['image'] = 'uploads/images/thumbnails/'.$row['image'];
					$j++;
					}
				}
			$i++;
			}
		}
	}
	
	public function restaurantuser($data){
		$sql = "SELECT *,( 3959 * acos( cos( radians('".$data['latitude']."') ) * cos( radians( restaurant_latitude ) ) * cos( radians( restaurant_langitude ) - radians('".$data['langitude']."') ) + sin( radians('".$data['latitude']."') ) * sin( radians( restaurant_latitude ) ) ) ) AS distance FROM restaurant HAVING distance < 2";
		
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			return $query->result_array();
		}
	}
	
	public function adduserslocation($data){
		$sql = "insert into customer_locations (customer_id,latitude,langitude) values('".$data['customer_id']."','".$data['latitude']."','".$data['langitude']."')";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query){
			return true;
		}
	}
	
	public function orderlist($id){
		$sql = "SELECT * FROM `gc_orders`a, gc_order_items b WHERE a.`customer_id` = ".$id." and a.id = b.order_id order by a.ordered_on desc";
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			$result = array();
			$i=0;
			foreach($query->result_array() as $row){ 
				$result[$row['order_number']][$i]['id'] = $row['order_id'];
				$result[$row['order_number']][$i]['ordered_on'] = $row['ordered_on'];
				$result[$row['order_number']][$i]['menu_id'] = $row['product_id'];
				$result[$row['order_number']][$i]['quantity'] = $row['quantity'];
				$result[$row['order_number']][$i]['price'] = $row['subtotal'];
			$i++;
			}
			return $result;
		}else{
			return false;
		}
	}
	
	public function updateprofile($data){
		$sql = "update customers set firstname='".$data['firstname']."',email='".$data['email']."',dob='".$data['dob']."',gender='".$data['gender']."' where id='".$data['id']."'";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query){
			return true;
		}
	}
	
	public function getMenus($id){
		$sql ="SELECT DISTINCT b.category_id, c.name FROM `restaurant_menu` a, menu_categories b, categories c where a.restaurant_id = '".$id."' and a.menu_id = b.menu_category and b.category_id = c.id";
		$query = $this->db->query($sql);
		$result = array();
		if($query->num_rows()>0){
			$data = $query->result_array();
			$i=0;
			foreach($data as $menu){
				$result[$i]['category_id'] = $menu['category_id'];
				$result[$i]['category'] = $menu['name'];
				$sql1 ="SELECT * FROM `restaurant_menu` a, menu_categories b, categories c where a.restaurant_id = '".$id."' and b.category_id='".$menu['category_id']."' and a.menu_id = b.menu_category and b.category_id = c.id";
				$query1 = $this->db->query($sql1);
				if($query1->num_rows()>0){
					$data1 = $query1->result_array();
					$j=0;
					foreach($data1 as $mn){
						$result[$i]['menus'][$j]['menu_id'] = $mn['menu_id'];
						$result[$i]['menus'][$j]['menu'] = $mn['menu'];
						$result[$i]['menus'][$j]['price'] = $mn['price'];
						$result[$i]['menus'][$j]['image'] = 'uploads/images/thumbnails/'.$mn['image'];
						$result[$i]['menus'][$j]['type'] = $mn['type'];
					$j++;
					}
				}
			$i++;
			}
		}
		
		return $result;
	}
	
	public function restaurantforlocation($data){
		
	}
	
	public function restaurantSuggest($data){
		
		$sql =$this->db->query("insert into  restaurant_suggest (id,restaurant_name,restaurant_phone,restaurant_email) values('".$data['id']."','".$data['restaurant_name']."','".$data['restaurant_phone']."','".$data['restaurant_email']."')");
		
		if($sql){
			return true;
		}else{
			return false;
		}		
			
	}
	public function pitstopSuggest($data){
		
		$sql =$this->db->query("insert into  pitstop_suggest (id,restaurant_address) values('".$data['id']."','".$data['restaurant_address']."')");
		
		if($sql){
			return true;
		}
			
	}
	
	public function displayProfile($data){
		
		$sql=$this->db->query("select id,profile_image from customers where id='".$data['id']."'");
        $i=0;
		if($sql->num_rows()>0){
			$result[$i] = true;
			//echo $this->db->last_query(); exit;
			foreach($sql->result_array() as $row){
         	
                $profile_image_path=$row['profile_image'];
			    $profile_image_path=$this->config->base_url()."uploads/".$profile_image_path;
			    $result['data'][$i]['profile_image']=$profile_image_path;
				
			};
		}else{
			$result[0] = false;
		}
		return $result;	
			
	}
	
	public function profilePictureUpdate($data){
		
		$sql=$this->db->query("UPDATE customers SET 
		profile_image='".$data['profile_image']."' where id='".$data['id']."'");
	    if($sql==true){
			$result[0] = true;
		//	$result['data'] = $sql;
		}else{
			$result[0] = false;
		}
		return $result;
		
	}
	
	public function validateCoupon($data){
		
		$sql=$this->db->query("select * from coupons where coupon_code='".$data['coupon_code']."' and used =0");
       
		if($sql->num_rows()>0){
			$data = $sql->result_array();
			
			$result['id'] = $data[0]['id'];
			$result['cost'] = $data[0]['cost'];
			
		}else{
				$result['id'] = 0;
				
		}
			return $result;
		
	}
	
	public function addFeedback($data){
	
		$sql =$this->db->query("insert into  feedback (customer_id,user_feedback) values('".$data['customer_id']."','".$data['user_feedback']."')");
		if($sql){
			$result[0] = true;
		}else{
			$result[0] = false;
		}
		return $result;
	}
	
	 public function orderInsert($data){
		  
		  $sql=$this->db->query("insert into orders (id,order_number,customer_id,shipping,tax,coupon_discount,coupon_id,order_type,shipping_lat,shipping_lang)
		   values ('".$data['id']."','".$data['order_number']."','".$data['customer_id']."','".$data['shipping']."','".$data['tax']."','".$data['coupon_discount']."','".$data['coupon_id']."','".$data['order_type']."',
		   '".$data['shipping_lat']."','".$data['shipping_lang']."') ");
		   $sql2=$this->db->query("insert into order_items (menu_id,quantity) values ('".$data['menu_id']."','".$data['quantity']."') ");
		  
		  
	  }
	
}