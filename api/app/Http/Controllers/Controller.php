<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\UserAccessToken;
use App\Models\UserRole;

include_once('../config/symbini.php');
$_ENV['DEFAULT_TITLE'] = $DEFAULT_TITLE;
$_ENV['PORTAL_GUID'] = $PORTAL_GUID;
$_ENV['SECURITY_KEY'] = $SECURITY_KEY;
$_ENV['DEFAULT_TITLE'] = $DEFAULT_TITLE;
$_ENV['ADMIN_EMAIL'] = $ADMIN_EMAIL;
$_ENV['CLIENT_ROOT'] = $CLIENT_ROOT;
$_ENV['SYMBIOTA_VERSION'] = $CODE_VERSION;
$_ENV['API_VERSION'] = '2.0';


class Controller extends BaseController{
	/**
	 * @OA\Info(
	 *   title="Symbiota API",
	 *   version="2.0",
	 *   @OA\Contact(
	 *     email="symbiota@asu.edu",
	 *     name="Symbiota Support Hub Team"
	 *   )
	 * )
	 */

	/**
	 * @OA\Server(url="../")
	 */

	protected $userArr = null;

	public function __construct(){
	}

	/**
	 * Authorization of writable actions or sensitive locality requests
	 *
	 * Input: security token
	 * Return: user object with role/permission settings
	 */
	protected function authenticate(Request $request): bool{
		$status = false;
		$this->validate($request, [
			'apiToken' => 'required'
		]);
		//TODO: convert to an actual user object
		$this->userArr = array();
		if($uid = UserAccessToken::where('token', $request->input('apiToken'))->value('uid')){
			$status = true;
			//Check user security tokens
			$this->userArr['uid'] = $uid;
			$result = UserRole::where('uid', $uid)->groupBy('role')->get(['role', UserRole::raw('GROUP_CONCAT(tablePK) as pks')])->toArray();
			foreach($result as $roleArr){
				$this->userArr['roles'][$roleArr['role']] = explode(',', $roleArr['pks']);
			}
		}
		return $status;
	}

	protected function isAuthorized(string $role, int $roleKey = null): bool{
		if(!$role || !$this->userArr) return false;
		if(array_key_exists($role, $this->userArr['roles'])){
			if($roleKey){
				if(in_array($roleKey, $this->userArr['roles'][$role])) return true;
			}
			else{
				//Role key does not have to be verified if null
				return true;
			}
		}
		return false;
	}
}
