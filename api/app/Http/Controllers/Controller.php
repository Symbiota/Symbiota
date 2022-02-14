<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

include_once('../config/symbini.php');
$_ENV['DEFAULT_TITLE'] = $DEFAULT_TITLE;
$_ENV['PORTAL_GUID'] = $PORTAL_GUID;
$_ENV['DEFAULT_TITLE'] = $DEFAULT_TITLE;
$_ENV['ADMIN_EMAIL'] = $ADMIN_EMAIL;
$_ENV['CLIENT_ROOT'] = $CLIENT_ROOT;
$_ENV['SYMBIOTA_VERSION'] = $CODE_VERSION;

class Controller extends BaseController
{
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
	 * @OA\Server(url="portal")
	 */
}
