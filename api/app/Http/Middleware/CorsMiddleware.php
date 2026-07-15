<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CorsMiddleware{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next){
		$origin = $request->headers->get('Origin');

		// Restricted origins for write methods
		$writeAllowedOrigins = [
			//https://lichenportal.org  - in future we may add all urls within portalindex table
		];

		$allowedOrigin = '';
		if ($request->isMethod('GET')) {
			//Open all GET requests to all
			$allowedOrigin = '*';
		} else {
			// Limit write methods to only origins defined above
			$allowedOrigin = null;
			if(in_array($origin, $writeAllowedOrigins)) $allowedOrigin = $origin;
		}

		$response = $next($request);

		// Only apply CORS headers if allowed
		if ($allowedOrigin !== null) {
			$response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
			$response->headers->set('Access-Control-Allow-Credentials', 'true');

			$response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
			$response->headers->set('Access-Control-Max-Age', '86400');

			// Allowed methods depend on the scenario
			if ($request->isMethod('GET')) {
				$response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
			} else {
				$response->headers->set('Access-Control-Allow-Methods', 'POST, PUT, DELETE, OPTIONS');
			}
		}

		return $response;
	}
}
?>