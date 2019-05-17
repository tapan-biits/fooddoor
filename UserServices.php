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
/*neo4j specific code
//new code
		public function create_user($data)
		{

		   $content=$this->get_user($data['phone']);
		  
 	 	   if(empty($content[0][0]->data))
 	 	   {
			
				$data_out=array();
				foreach ($data as $key => $value) {
					if($key=="name"||$key=="phone"||$key=="addr"||$key=="email"||$key=="usertype"||$key=="password")
					$data_out[$key]=$data[$key];
				}
				
				$data_out['password']=Hash::make($data['password']);
				$data_out['created_at']=date("Y-m-d H:i:s");
				$data_out['otp']=rand(1000,9999);
				//var_dump($data_out);die;
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
 	 	    if($content[0][0]->data->otp==$data['otp'] && $content[0][0]->data->phone==$data['phone'])
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
        if(!empty($content[0][0]->data))
        {
        	if(Hash::check($data['password'],$content[0][0]->data->password))
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
        if(!empty($content[0][0]->data) && $content[0][0]->data->otp==$data['otp'])
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
 	 	if(!empty($content->data))
        return $content->data;
    	else 
    	return null; 
      }

      public function update_user($datapost,$phone)
      {
      	
      	$client=new Client();
		$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
            'json' => [
			        "query"=>"MATCH (n:User { phone: '".$phone."' }) SET n = {props}
					RETURN n",
			        "params"=>json_decode(json_encode(['props'=>$datapost])),	         
			     ],
	    'headers' => [
	    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
	    ]
        ]);
 	 	$content = json_decode($res->getBody()->getContents());
 	 	var_dump($content);die;
      	
      } 

      //graph func
	public function get_all_specific_nodes($type)
	{
		 $client = new Client();
        $res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
            'json' => [
			        "query"=>"match (n:$type) RETURN n",
			        "params"=> new stdClass(),		         
			     ],
	        	 'headers' => [
	    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
	    ]
        ]);
       
        return $res->getBody()->getContents();
	}

	public function get_node_val($type,$id)
		{
			 $client = new Client();
	        $res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"match (n:$type{name:'".$id."'}) RETURN n",
				        "params"=> new stdClass(),		         
				     ],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6dGVzdA=='
		    ]
	        ]);
	       
	        return $res->getBody()->getContents();
		}

	public function create_specific_node($data)
		{
			
			 $client = new Client();
			// var_dump($data['usermobile']);die;
	        $res = $client->request('POST', 'http://localhost:7474/db/data/node', [
	            'json' => json_decode($data,true),
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
	        
	        return $res->getBody()->getContents();
		}

		public function create_specific_node_new($data)
		{
			 $client = new Client();
			 $object = new stdClass();
			foreach ($data as $key => $value)
			{
			    $object->$key = $value;
			}
			$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"CREATE (n:Usertest { props } ) RETURN n",
				        "params"=>$object,	         
				     ],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
		}
*/
public function create_user($a)
	{
		
		
		if(DB::table('customer')->where('CustomerPhone',$a[1]["CustomerPhone"])->exists())
		{
			DB::table('customer')
            ->where('CustomerPhone', '=', $a[1]['CustomerPhone'])
            ->update(['CustomerMobileOTP' => rand(1000,9999)]);
            $out = DB::table('customer')->where('CustomerPhone', '=', $a[1]['CustomerPhone'])->get();
             return response()->json(["login_status"=>true,"register_status"=>false,"CustomerId"=>$out[0]->CustomerId,"Customer"=>$out[0],"msg"=>"Already registered"]);
			
		}
		// else if (DB::table('customer')->where('CustomerEmail',$a[1]["CustomerEmail"])->exists()) 
		// {
		// 	DB::table('customer')
  //           ->where('CustomerEmail', '=', $a[1]['CustomerEmail'])
  //           ->update(['CustomerMobileOTP' => rand(1000,9999)]);
  //           $out = DB::table('customer')->where('CustomerEmail', '=', $a[1]['CustomerEmail'])->get();
  //            return response()->json(["login_status"=>true,"register_status"=>false,"CustomerId"=>$out[0]->CustomerId,"Customer"=>$out[0]]);

			
		// }
		else 
		{
			$a[1]["CustomerId"]=uniqid();
			if(!empty($a[1]['customer_photo']))
				$a[1]["CustomerPhotoURL"]=$a[1]['customer_photo'];
			else
				$a[1]["CustomerPhotoURL"]=$a[0];

			unset($a[1]['customer_photo']);

			$a[1]["CustomerCreatedDate"]=date("Y-m-d H:i:s");
			
			$a[1]["CustomerMobileOTP"]=rand(1000,9999);
			$a[1]["CustomerPassword"]=Hash::make($a[1]["CustomerPassword"]);
			
			if(DB::table('customer')->insert($a[1]))
			{
			$cus_res = DB::table('customer')->orderBy('CustomerCreatedDate', 'desc')->first();
			
			 return response()->json(["login_status"=>false,"register_status"=>true,"Customer"=>$cus_res]);
			}
			
			
		}
		
	}

	public function verify_customer($a)
	{
		

		$b=DB::table('customer')
			->where('CustomerPhone', '=', $a['CustomerPhone'])
			->where('CustomerMobileOTP', '=',$a['CustomerMobileOTP'])
			
			->exists();


		if($b){

			DB::table('customer')
            ->where('CustomerPhone', '=', $a['CustomerPhone'])
            ->update(['CustomerRegisteredDate' => date("Y-m-d H:i:s")]);
            $out = DB::table('customer')->where('CustomerPhone', '=', $a['CustomerPhone'])->get();
			//locations
			// $loc = DB::table('customerlocation')->where('customerLocation_customerId', '=', $out[0]->CustomerId)->orderBy('LocDate', 'desc')->exists();
			// if($loc)
			// {
			// 	$loc = DB::table('customerlocation')->where('customerLocation_customerId', '=', $out[0]->CustomerId)->orderBy('LocDate', 'desc')->first();
			// 	$locations=(array)$loc;
			// 	foreach ($locations as $value) {
			// 	unset($value->customerLocationId);
			//     unset($value->customerLocationIsDelete);
			// 	}
			// }
			// else
			// 	$locations=(object)[];
			
			//$out[0]->Address=$locations;
			 return response()->json(["verification_status"=>true,"CustomerId"=>$out[0]->CustomerId,"Customer"=>$out[0]]);
		}
		else
			return response()->json(["verification_status"=>false,"status_msg"=>"Incorrect OTP!!Pls try again"]);
		
		
	}

	public function login($a)
	{
		
		if(!empty($a['CustomerPhone']))
		{
		
				$b=DB::table('customer')
					->where('CustomerPhone', '=', $a['CustomerPhone'])
					
					->where('CustomerRegisteredDate','<>',null)
					->exists();

				$out=DB::table('customer')
					->where('CustomerPhone', '=', $a['CustomerPhone'])
					
					->where('CustomerRegisteredDate','<>',null)
					->get();
					

				if($b && Hash::check($a['CustomerPassword'],$out[0]->CustomerPassword) ){

					DB::table('customer')
		            ->where('CustomerPhone', '=', $a['CustomerPhone'])
		            ->update(['CustomerMobileOTP' => rand(1000,9999)]);
		            $out = DB::table('customer')->where('CustomerPhone', '=', $a['CustomerPhone'])->get();
		             return response()->json(["register_status"=>false,"login_status"=>true,"CustomerId"=>$out[0]->CustomerId,"Customer"=>$out[0]]);
					
				}
				else 
					{
						return response()->json(["register_status"=>false,"login_status"=>true,"status_msg"=>"Phone number not registered!!"]);
						
				
			         }
	     }

	 //    else if(!empty($a['CustomerEmail']))
		// {
		
		// $b=DB::table('customer')
		// 	->where('CustomerEmail', '=', $a['CustomerEmail'])
			
		// 	->where('CustomerRegisteredDate','<>',null)
		// 	->exists();
			

		// 		if($b){

		// 			DB::table('customer')
		//             ->where('CustomerEmail', '=', $a['CustomerEmail'])
		//             ->update(['CustomerMobileOTP' => rand(1000,9999)]);
		//             $out = DB::table('customer')->where('CustomerEmail', '=', $a['CustomerEmail'])->get();
		//              return response()->json(["register_status"=>false,"login_status"=>true,"CustomerId"=>$out[0]->CustomerId,"Customer"=>$out[0]]);
					
		// 		}
		// 		else 
		// 			{
		// 				return response()->json(["register_status"=>false,"login_status"=>false,"status_msg"=>"Email not registered!!"]);
						
				
		// 	         }
	 //     }
	     else{}
	}

	public function verify_login_customer($a)
	{
		

		$b=DB::table('customer')
			->where('CustomerPhone', '=', $a['CustomerPhone'])
			->where('CustomerMobileOTP', '=',$a['CustomerMobileOTP'])
			
			->exists();

		if($b){

		
            $out = DB::table('customer')->where('CustomerPhone', '=', $a['CustomerPhone'])->get();
			
			// $loc = DB::table('customerlocation')->where('customerLocation_customerId', '=', $out[0]->CustomerId)->orderBy('LocDate', 'desc')->exists();
			// if($loc)
			// {
			// 	$loc = DB::table('customerlocation')->where('customerLocation_customerId', '=', $out[0]->CustomerId)->orderBy('LocDate', 'desc')->first();
			// 	$locations=(array)$loc;
			// 	foreach ($locations as $value) {
			// 	unset($value->customerLocationId);
			//     unset($value->customerLocationIsDelete);
			// 	}
			// }
			// else
			// 	$locations=(object)[];
			
			
			
			
			//$out[0]->Address=$locations;
			
			return response()->json(["login_verification_status"=>true,"CustomerId"=>$out[0]->CustomerId,"Customer"=>$out[0]]);
		}
		else 
			return response()->json(["login_verification_status"=>false,"status_msg"=>"Incorrect OTP!!"]);
		
		
	}
	
 	 

	public function get_usertypes()
	{
		$out = DB::table("types")->get();
		return response()->json(["data"=>$out]);
	}
			
 	 
}