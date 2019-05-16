<?php

namespace App\Providers\UserServices;
use Illuminate\Support\Facades\DB;
use Schema;
use App;
use GuzzleHttp\Client;
use stdClass;
use Hash;
use Date;

class UserServices 
{

	public function create_user($data)
		{
		   $content=$this->get_user($data['phone']);

 	 	   if(empty($content->data[0][0]->data))
 	 	   {
			
				$data_out=array();
				foreach ($data as $key => $value) {
					if($key=="name"||$key=="phone"||$key=="addr"||$key=="email"||$key=="usertype"||$key=="password")
					$data_out[$key]=$data[$key];
				}
				
				$data_out['password']=Hash::make($data['password']);
				$data_out['created_at']=date("Y-m-d H:i:s");
				$data_out['otp']=rand(1000,9999);
				$client=new Client();
				$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
		            'json' => [
					        "query"=>"CREATE (n:User { props } ) RETURN n",
					        "params"=>json_decode(json_encode(['props'=>$data_out])),	         
					     ],
			    'headers' => [
			    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
			    ]
		        ]);
		        $content = json_decode($res->getBody()->getContents());
		        return response()->json(["status"=>true,"phone"=>$content->data[0][0]->data->phone,"otp"=>$content->data[0][0]->data->otp]);
	        }
	        else
	        	return response()->json(["status"=>false,"msg"=>"User already registered"]);

	        
		}


		public function create_user_validate($data)
		{
			

 	 	    $content=$this->get_user($data['phone']);
 	 	    $client=new Client();
 	 	    if($content->data[0][0]->data->otp==$data['otp'] && $content->data[0][0]->data->phone==$data['phone'])
 	 	    {
 	 	    	$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"MATCH (p:User {phone: '".$data['phone']."'})
						SET p.registered ='". date("Y-m-d H:i:s")."'
						RETURN p"],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
 	 	    	
 	 	    return response()->json(["login_validation_status"=>true]);
 	 	}
 	 	else
 	 		return response()->json(["login_validation_status"=>false]);
		}

		
 	 public function login_new($data)
 	 {
 	 	
 	 	$content=$this->get_user($data['phone']);
 	 	$client=new Client();
        if(!empty($content->data[0][0]->data))
        {
        	if(Hash::check($data['password'],$content->data[0][0]->data->password))
        	{
        		$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"MATCH (p:User {phone: '".$data['phone']."'})
						SET p.otp ='". rand(1000,9999)."'
						RETURN p.otp,p.phone"],
		        	 'headers' => [
								    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
								    ]
	        ]);
 	 	$content = json_decode($res->getBody()->getContents());

        	return response()->json(["otp"=>$content->data[0][0],"phone"=>$content->data[0][1]]);
        }
        else
        	return response()->json(["login_status"=>false]);
        }
        else
        	return response()->json(["login_status"=>false,"msg"=>"User not registered!!"]);
        
 	 }

 	 public function login_validate($data)
 	 {
 	 	
 	 	$content=$this->get_user($data['phone']);
        if(!empty($content->data[0][0]->data) && $content->data[0][0]->data->otp==$data['otp'])
        	return response()->json(["login_status"=>"success"]);
        else
        	return response()->json(["login_status"=>"failed"]);
 	 }

 	 public function get_usertypes()
 	 {
 	 	$client = new Client();
 	 	$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"MATCH (j:UserType)RETURN j"],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
 	 	$content = json_decode($res->getBody()->getContents());
 	 	$data_out=array();
 	 	foreach ($content->data as $key => $value) {
 	 		$data_out[$key]['id']=$value[0]->data->id;
 	 		$data_out[$key]['name']=$value[0]->data->name;
 	 		
 	 	}
 	 	return response()->json(["usertype_response"=>$data_out]);
 	 }

 	 
 	 public function get_user($phone)
      {
      	$client = new Client();
 	 	$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"MATCH (j:User {phone: '".$phone."'})RETURN j"],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
 	 	$content = json_decode($res->getBody()->getContents());
        return $content;
      }

      	
 	 
}