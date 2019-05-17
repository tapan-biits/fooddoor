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

   
      public function create_user(Request $request)
      {
         $req=$request->post();
        
       if(!empty($request->file('profile_pic')))
       {
        $file = $request->file('profile_pic');
        $file_name = time() . $file->getClientOriginalName();                      

        $file_path = 'customer_uploads/';

        $file->move($file_path, $file_name);
        $data='customer_uploads/'.$file_name;
        $result=$this->user_services->create_user([asset($data),$req]);
    }
    else
        $result=$this->user_services->create_user(["NA",$req]);

            return $result;
       
      }

      public function create_user_test(Request $req)
      {
        $a=$this->user_services->create_user_test($req->json()->all());

        return $a;
      }
      
      
      public function create_user_validate(Request $request)
      {
        if((bool)$request->post()['verify'])
        {

        $result=$this->user_services->verify_customer($request->post());
       return $result;
        
        
        }
        else 
            return response()->json(["verification_status"=>false,"message"=>"Incorrect form parameters"]);
      
      }

      public function login(Request $request)
      {
        
       
        return $this->user_services->login($request->post());
      
      }

      

      public function login_validate(Request $request)
      {
       if((bool)$request->post()['verify'])
        {

        $res=$this->user_services->verify_login_customer($request->post());
       
        return $res;
        
        }
        else
            return response()->json(["login_verification_status"=>false,"message"=>"incorrect form parameters"]);
      }
      public function get_usertypes()
      {
        var_dump("hga");die;
        return $this->user_services->get_usertypes();
      }

      // public function get_user($a)
      // {
      //   $output=$this->user_services->get_user($a);
      
      //   return response()->json(["response"=>$output]);
      // }
      
      //  public function update_user(Request $req,$phone)
      // {
       
      //   return $this->user_services->update_user($req->json()->all(),$phone);
      // }

      
}
