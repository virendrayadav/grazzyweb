<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Api extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
		$this->load->database();
		$this->load->model('api_model');
        
        $this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
    }
	
	public function customercheck_post(){
		 $data = [
            'firstname' => $this->post('firstname'),
            'phone' => $this->post('phone')
        ];

		$message = $this->api_model->customercheck($data);
		
        $this->set_response($message, REST_Controller::HTTP_OK);
	}
	
    public function customer_get()
    {
		
		$users = $this->api_model->getUsers();
		//print_r($users); exit;
     

        $id = $this->get('id');

        // If the id parameter doesn't exist return all the users

        if ($id === NULL)
        {
            // Check if the users data store contains users (in case the database result returns NULL)
            if ($users)
            {
                // Set the response and exit
                $this->response($users, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No users were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular user.

        $id = (int) $id;

        // Validate the id.
        if ($id <= 0)
        {
            // Invalid id, set the response and exit.
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Get the user from the array, using the id as key for retreival.
        // Usually a model is to be used for this.

        $user = NULL;

        if (!empty($users))
        {
            foreach ($users as $key => $value)
            {
                if (isset($value['id']) && $value['id'] == $id)
                {
                    $user = $value;
					
                }
            }
        }

        if (!empty($user))
        {
            $this->set_response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'User could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
	
	 public function restaurants_get($id)
    {
		
		$restaurants = $this->api_model->getRestaurants($id);
		if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }

       
        if (!empty($restaurants))
        {
            $this->set_response($restaurants, REST_Controller::HTTP_OK); 
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'restaurants  could not be found for the pitstop'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
		
        
    }
	
	 public function customeraddress_get($id)
    { 
      
		$address = $this->api_model->getAddress($id);
		
        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); 
        }

       
        if (!empty($address))
        {
            $this->set_response($address, REST_Controller::HTTP_OK); 
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'User address could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

	
    public function adduserslocation_post()
    {
		$data = [
            'customer_id' => $this->post('customer_id'),
            'latitude' => $this->post('latitude'),
            'langitude' => $this->post('langitude'),
        ];
       $userslocation =  $this->api_model->adduserslocation($data);
        

        $this->set_response($userslocation, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function users_delete()
    {
        $id = (int) $this->get('id');

        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

}
