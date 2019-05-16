<?php

/*dev*/
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

		public function store_details($a,$b,$c)
 	{

		 if (DB::table($a)->where($b,$c[$b])->exists()) 
		{
			
            $out = DB::table($a)->where($b, '=',$c[$b])->get();
             return response()->json(["status"=>false,"message"=>"User already exists"]);

			
		}
		else 
		{
			if(!empty($c['userpassword']))
				$c['userpassword']=Hash::make($c['userpassword']);
			$c['created_at']=date("Y-m-d H:i:s");
			//var_dump($c);die;
		
		 try {
     
		       if($id=DB::table($a)->insertGetId($c))
					{
					
					if($a=='users')
					{
						$role_id = DB::table('role')->where('RoleName', '=','manager')->get();
					DB::table('user_roles')->insert(['user_id'=>$id,'role_id'=>$role_id[0]->RoleId]);
				}
					 return response()->json(["status"=>true]);
					}
		    } 
		    catch (\Illuminate\Database\QueryException $e) 
		    {
		        return response()->json(["status"=>false,"message"=>$e->getMessage()]);
		    
		    } 
		    catch (\Exception $e) 
		    {
		       return response()->json(["status"=>false,"message"=>$e->getMessage()]);
		    }
						
			
		}
 	 }

 	 public function login($a,$b,$c)
 	 {
 	 	
 	 	$out = DB::table($a)->where($b, '=',$c[$b])->get();
 	 	
 	 	if(!empty($out[0]) && Hash::check($c['userpassword'],$out[0]->userpassword)  && $out[0]->usertype==$c['usertype']) 
 	 	{
		    return response()->json(["login_status"=>true]);
		} 
		else 
		{
		   return response()->json(["login_status"=>false]);
		}
 	 }

		//new code
		public function create_user($data)
		{
			$client = new Client();
			$data_out=array();
			foreach ($data as $key => $value) {
				if($key=="name"||$key=="phone"||$key=="addr"||$key=="email"||$key=="usertype"||$key=="password")
				$data_out[$key]=$data[$key];
			}
			
			$data_out['password']=Hash::make($data['password']);
			$data_out['created_at']=date("Y-m-d H:i:s");
			$data_out['otp']=rand(1000,9999);
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

		public function create_user_validate($data)
		{
			$client = new Client();
			$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"MATCH (j:User {phone: '".$data['phone']."'})RETURN j.otp,j.phone"],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
 	 	    $content = json_decode($res->getBody()->getContents());
 	 	    
 	 	    if($content->data[0][0]==$data['otp'] && $content->data[0][1]==$data['phone'])
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
 	 	$client = new Client();
 	 	$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"MATCH (j:User {phone: '".$data['phone']."'})RETURN j"],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
 	 	$content = json_decode($res->getBody()->getContents());
 	 	//var_dump(!empty($content->data));die;
        if(!empty($content->data))
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
 	 	$client = new Client();
 	 	$res = $client->request('POST', 'http://localhost:7474/db/data/cypher', [
	            'json' => [
				        "query"=>"MATCH (j:User {phone: '".$data['phone']."'})RETURN j.otp,j.phone"],
		        	 'headers' => [
		    'Authorization'     => 'Basic bmVvNGo6YWRtaW4='
		    ]
	        ]);
 	 	$content = json_decode($res->getBody()->getContents());
 	 	
        if(!empty($content->data) && $content->data[0][0]==$data['otp'])
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

 	 

 	
 	 
}