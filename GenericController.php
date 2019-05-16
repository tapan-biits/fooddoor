<?php

namespace App\Http\Controllers;
use App\Providers\UserServices\UserServices;
use Illuminate\Http\Request;

class GenericController extends Controller
{
    protected $user_services;

     public function __construct(UserServices $services) 
     {

        $this->user_services = $services;
      }

      

      public function store_details(Request $request,$a,$b)
      {
        $req=$request->post();
       
        return $this->user_services->store_details($a,$b,$req);
      
      }

           

      //graph
      public function get_all_specific_nodes($type)
      {

        $a=$this->user_services->get_all_specific_nodes($type);


        return json_decode($a,true);
      
      }

      public function get_node_val($type,$id)
      {

        $a=$this->user_services->get_node_val($type,$id);

        return json_decode($a,true);
      
      }

      public function create_specific_node(Request $req)
      {
        
        $a=$this->user_services->create_specific_node($req->getContent());

        return json_decode($a,true);
      
      }

public function create_specific_node_new(Request $req)
      {
        
        $a=$this->user_services->create_specific_node_new($req->json()->all());

        return json_decode($a,true);
      
      }

      public function demo()
      {
        return "demo hi";
      }

//new code
      public function create_user(Request $req)
      {
        $a=$this->user_services->create_user($req->json()->all());

        return $a;
      }
      
      public function login(Request $request)
      {
        
       
        return $this->user_services->login_new($request->json()->all());
      
      }

      public function create_user_validate(Request $req)
      {
        return $this->user_services->create_user_validate($req->json()->all());
      
      }

      public function login_validate(Request $req)
      {
        return $this->user_services->login_validate($req->json()->all());
      }
      public function get_usertypes()
      {
        return $this->user_services->get_usertypes();
      }
      
}
