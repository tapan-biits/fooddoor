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

      public function create_user(Request $req)
      {
        $a=$this->user_services->create_user($req->json()->all());

        return $a;
      }
      
      public function create_user_validate(Request $req)
      {
        return $this->user_services->create_user_validate($req->json()->all());
      
      }

      public function login(Request $request)
      {
        
       
        return $this->user_services->login_new($request->json()->all());
      
      }

      

      public function login_validate(Request $req)
      {
        return $this->user_services->login_validate($req->json()->all());
      }
      public function get_usertypes()
      {
        return $this->user_services->get_usertypes();
      }

      public function get_user($a)
      {
        return $this->user_services->get_user($a);
      }
      
       

      
}
