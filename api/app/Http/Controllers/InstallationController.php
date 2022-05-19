<?php
namespace App\Http\Controllers;

use App\PortalIndex;
use Illuminate\Http\Request;

class InstallationController extends Controller{
	/**
	 * Installation controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/installation",
	 *	 operationId="/api/v2/installation",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Pagination parameter: maximum number of records per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=100)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="offset",
	 *		 in="query",
	 *		 description="Pagination parameter: page number",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=0)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of installations registered within system",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllPortals(Request $request){
		$this->validate($request, [
			'limit' => 'integer',
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$fullCnt = PortalIndex::count();
		$result = PortalIndex::skip($offset)->take($limit)->get();

		$eor = false;
		$retObj = [
			"offset" => (int)$offset,
			"limit" => (int)$limit,
			"endOfRecords" => $eor,
			"count" => $fullCnt,
			"results" => $result
		];
		return response()->json($retObj);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/installation/{identifier}",
	 *	 operationId="/api/v2/installation/identifier",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Installation ID or GUID associated with target installation",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns metabase on installation registered within system with matching ID",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Installation identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOnePortal($id, Request $request){
		$portalObj = null;
		if(is_numeric($id)) $portalObj = PortalIndex::find($id);
		else $portalObj = PortalIndex::where('guid',$id)->first();
		if(!$portalObj->count()) $portalObj = ["status"=>false,"error"=>"Unable to locate installation based on identifier"];
		return response()->json($portalObj);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/installation/ping",
	 *	 operationId="/api/v2/installation/ping",
	 *	 tags={""},
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns installation metadata",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function pingPortal(Request $request){
		$portalObj = null;
		if(isset($_ENV['DEFAULT_TITLE']) && isset($_ENV['PORTAL_GUID'])){
			$portalObj['status'] = true;
			$portalObj['portalName'] = $_ENV['DEFAULT_TITLE'];
			$portalObj['guid'] = $_ENV['PORTAL_GUID'];
			$portalObj['managerEmail'] = $_ENV['ADMIN_EMAIL'];
			$portalObj['urlRoot'] = $this->getServerDomain().$_ENV['CLIENT_ROOT'];
			$portalObj['symbiotaVersion'] = $_ENV['SYMBIOTA_VERSION'];
		}
		else{
			$portalObj['status'] = false;
			if(!isset($_ENV['DEFAULT_TITLE'])) $portalObj['error'][] = 'Portal title is NULL';
			if(!isset($_ENV['PORTAL_GUID'])) $portalObj['error'][] = 'Portal GUID is NULL';
			$portalObj['status'] = false;
		}
		return response()->json($portalObj);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/installation/{identifier}/touch",
	 *	 operationId="/api/v2/installation/identifier/touch",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Identifier of the remote installation",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="endpoint",
	 *		 in="query",
	 *		 description="Url to Symbiota root of remote installation",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns metabase remote installation, if successfully registered",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Identifier of remote installation is required.",
	 *	 ),
	 * )
	 */
	public function portalHandshake($id, Request $request){
		$responseArr = array();
		$portalObj = PortalIndex::where('guid',$id)->get();
		if($portalObj->count()){
			$responseArr['status'] = true;
			$responseArr['message'] = 'Portal previously registered';
		}
		elseif($id == $_ENV['PORTAL_GUID']){
			//Make sure touch isn't referring to self
			$responseArr['status'] = false;
			$responseArr['error'] = 'Registration failed: handshake is referencing self';
		}
		elseif($request->has('endpoint')){
			//Remote installation not yet in system, thus add and then process list from remote
			if($baseUrl = $request->input('endpoint')){
				//Insert portal
				$urlPing = $baseUrl.'/api/v2/installation/ping';
				if($remote = $this->getAPIResponce($urlPing)){
					if($id == $remote['guid']){
						//Shake back just to makes sure remote knows about self
						$remoteTouch = $baseUrl.'/api/v2/installation/'.$_ENV['PORTAL_GUID'].'/touch?endpoint='.htmlentities($this->getServerDomain().$_ENV['CLIENT_ROOT']);
						$this->getAPIResponce($remoteTouch, true);
						try {
							//Register remote
							$portalObj = PortalIndex::create($remote);
							$responseArr['status'] = true;
							$responseArr['message'] = 'Remote portal registered successfully';
							//Register all portals listed within remote, if not alreay registered
							$urlInstallation = $baseUrl.'/api/v2/installation';
							if($remoteInstallationArr = $this->getAPIResponce($urlInstallation)){
								$currentRegistered = 0;
								$newRegistration = 0;
								foreach($remoteInstallationArr['results'] as $portal){
									if(PortalIndex::where('guid',$portal['guid'])->count()) $currentRegistered++;
									elseif($portal['guid'] != $_ENV['PORTAL_GUID']){
										//Add remote
										PortalIndex::create($portal);
										//Touch remote installation but don't wait for a response because propagation across a large network can take awhile
										$urlTouch = $portal['urlRoot'].'/api/v2/installation/'.$_ENV['PORTAL_GUID'].'/touch?endpoint='.htmlentities($this->getServerDomain().$_ENV['CLIENT_ROOT']);
										$this->getAPIResponce($urlTouch, true);
										$newRegistration++;
									}
								}
								$responseArr['Current registered remotes obtained from remote library'] = $currentRegistered;
								$responseArr['Additional new registrations obtained from remote library'] = $newRegistration;
							}
							else $responseArr['error'] = 'Unable to obtain remote installation listing: '.$urlInstallation;
						} catch(\Illuminate\Database\QueryException $ex){
							$responseArr['status'] = false;
							$responseArr['error'] = 'Registration failed: Unable insert database record: '.$ex->getMessage();
						}
					}
					else{
						$responseArr['status'] = false;
						$responseArr['error'] = 'Registration failed: Supplied and returned remote GUIDs not matching ('.$id.' != '.$remote['guid'].')  ';
					}
				}
				else{
					$responseArr['status'] = false;
					$responseArr['error'] = 'Registration failed: Unable to obtain data from endpoint: '.$urlPing;
				}
			}
		}
		else{
			$responseArr['status'] = false;
			$responseArr['error'] = 'Registration failed: Unable to obtain portal endpoint';
		}
		$responseArr['results'] = $portalObj;
		return response()->json($responseArr);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/installation/{identifier}/occurrence",
	 *	 operationId="/api/v2/installation/identifier/occurrence",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Identifier of the remote installation",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Pagination parameter: maximum number of records per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=100)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="offset",
	 *		 in="query",
	 *		 description="Pagination parameter: page number",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=0)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of occurrences associated with an installations",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Identifier of remote installation is required.",
	 *	 ),
	 * )
	 */
	public function showOccurrences($id, Request $request){
		$this->validate($request, [
			'verification' => ['integer'],
			'limit' => ['integer', 'max:1000'],
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$portalObj = null;
		if(is_numeric($id)) $portalObj = PortalIndex::find($id);
		else $portalObj = PortalIndex::where('guid',$id)->first();

		$retObj = [];
		if($portalObj){
			$conditions = [];
			if($request->has('verification')){
				if($request->verification) $conditions[] = ['verification',1];
				else $conditions[] = ['verification',0];
			}
			$fullCnt = $portalObj->portalOccurrences()->where($conditions)->count();
			$result = $portalObj->portalOccurrences()->where($conditions)->skip($offset)->take($limit)->get();
			$eor = false;
			$retObj = [
				"offset" => (int)$offset,
				"limit" => (int)$limit,
				"endOfRecords" => $eor,
				"count" => $fullCnt,
				"results" => $result
			];
		}
		else $retObj = ["status"=>false,"error"=>"Unable to locate installation based on identifier"];
		return response()->json($retObj);
	}

	public function create(Request $request){
		/*
		$portalIndex = PortalIndex::create($request->all());

		return response()->json($portalIndex, 201);
		*/
	}

	public function update($id, Request $request){
		/*
		$portalIndex = PortalIndex::findOrFail($id);
		$portalIndex->update($request->all());

		return response()->json($portalIndex, 200);
		*/
	}

	public function delete($id){
		/*
		PortalIndex::findOrFail($id)->delete();
		return response('Portal Index deleted successfully', 200);
		*/
	}

	//Helper functions
	private function getAPIResponce($url, $asyc = false){
		$resJson = false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if($asyc) curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
		$resJson = curl_exec($ch);
		if(!$resJson){
			$this->errorMessage = 'FATAL CURL ERROR: '.curl_error($ch).' (#'.curl_errno($ch).')';
			return false;
			//$header = curl_getinfo($ch);
		}
		curl_close($ch);
		return json_decode($resJson,true);
	}

	private function getServerDomain(){
		$domain = "http://";
		if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $domain = "https://";
		$domain .= $_SERVER["SERVER_NAME"];
		if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER['SERVER_PORT'] != 443) $domain .= ':'.$_SERVER["SERVER_PORT"];
		return $domain;
	}
}