<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Occurrence;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MediaController extends Controller{

	private $rulesInsert = [
		'apiToken' => 'required',
		'originalUrl' => 'required',
		'format' => 'required',
		'occid' => 'integer|exists:omoccurrences,occid',
		'tid' => 'integer|exists:taxa,tid',
		'photographerUid' => 'integer|exists:users,uid'
	];

	private $rulesUpdate = [];

	/**
	 * Media controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
		$this->rulesUpdate = $this->rulesInsert;
		unset($this->rulesUpdate['originalUrl']);
		unset($this->rulesUpdate['format']);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/media",
	 *	 operationId="showAllMedia",
	 *	 tags={""},
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of media records",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllMedia(){
		return response()->json(Media::skip(0)->take(100)->get());
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/media/{identifier}",
	 *	 operationId="showOneMedia",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="primary key or record GUID (UUID) associated with target media object",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns single media record",
	 *		 @OA\JsonContent(type="application/json")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Media identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneMedia($id){
		return response()->json(Media::find($this->getImgid($id)));
	}

	/**
	 * @OA\Post(
	 *	 path="/api/v2/media",
	 *	 operationId="insertMedia",
	 *	 summary="Creates a new Media record",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="apiToken",
	 *		 in="query",
	 *		 description="API security token to authenticate post action",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\RequestBody(
	 *		required=true,
	 *		description="Media object to create",
     *		@OA\MediaType(
     *			mediaType="application/json",
     *			@OA\Schema(
	 *				required={"format", "originalUrl"},
     *				@OA\Property(
     *					property="format",
     *					type="string",
	 *					description="Media Type (MIME type)",
	 *					maxLength=45
     *				),
     *				@OA\Property(
     *					property="originalUrl",
     *					type="string",
	 *					description="URL returning original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="mediumUrl",
     *					type="string",
	 *					description="URL returning medium sized image of original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="thumbnailUrl",
     *					type="string",
	 *					description="URL returning thumbnail derivative of original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="archiveUrl",
     *					type="string",
	 *					description="URL returning large archival image (e.g. DNG, TIFF, etc), if web accessible ",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="referenceUrl",
     *					type="string",
	 *					description="URL returning original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="occid",
     *					type="integer",
	 *					description="Primary Key of occurrence record that media object is associated with. Should be left null if record is not associated with an occurrence.",
     *				),
     *				@OA\Property(
     *					property="tid",
     *					type="integer",
	 *					description="Primary Key of taxon record (e.g. scientific name reference) associated with media object. Should be left NULL if record is associated with an occurrence, since media will inherit occurrence identification. Required if media is not associated with an occurrence record (e.g. field image)",
     *				),
     *				@OA\Property(
     *					property="photographer",
     *					type="string",
	 *					description="Verbatim name of author/photographer media object. Leave NULL if photographerUid is supplied",
	 *					maxLength=100
     *				),
     *				@OA\Property(
     *					property="photographerUid",
     *					type="integer",
	 *					description="User Primary Key of photographer, who must have a user account register in the portal",
     *				),
     *				@OA\Property(
     *					property="imageType",
     *					type="string",
	 *					description="Term name of image type (e.g. stillImage)",
	 *					maxLength=45
     *				),
     *				@OA\Property(
     *					property="caption",
     *					type="string",
	 *					description="Consist, free-form text describing content of media object",
	 *					maxLength=100
     *				),
     *				@OA\Property(
     *					property="copyright",
     *					type="string",
	 *					description="Copyright Owner",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="rights",
     *					type="string",
	 *					description="A URI pointing to structured information about rights held in and over the resource (e.g. https://creativecommons.org/publicdomain/zero/1.0/, https://creativecommons.org/licenses/by/4.0/legalcode)",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="accessRights",
     *					type="string",
	 *					description="Information about who can access the resource or an indication of its security status",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="locality",
     *					type="string",
	 *					description="Short description of locality. Leave null if locality is available via the occurrence record",
	 *					maxLength=250
     *				),
     *				@OA\Property(
     *					property="notes",
     *					type="string",
	 *					description="General notes",
	 *					maxLength=350
     *				),
     *				@OA\Property(
     *					property="sourceIdentifier",
     *					type="string",
	 *					description="The source identifier, if media was harvested from an external resource",
	 *					maxLength=150
     *				),
     *				@OA\Property(
     *					property="hashFunction",
     *					type="string",
	 *					description="Cryptographic hash function used to compute the value given in the Hash Value",
	 *					maxLength=45
     *				),
     *				@OA\Property(
     *					property="hashValue",
     *					type="string",
	 *					description="Value computed by a hash function applied to the media that will be delivered at the access point",
	 *					maxLength=45
     *				),
     *			),
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="201",
	 *		 description="Returns UUID recordID (GUID)",
	 *		 @OA\JsonContent(
	 *			oneOf={
	 *				@OA\Schema(@OA\Property(property="status", type="boolean"),@OA\Property(property="recordID", type="string")),
	 *				@OA\Schema(@OA\Property(property="status", type="boolean"))
	 *			},
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	public function insert(Request $request){
		if($user = $this->authenticate($request)){
			$request->validate($this->rulesInsert);

			$inputArr = $request->all();
			$this->adjustInputData($inputArr);
			$media = Media::create($inputArr);
			return response()->json($media, 201);
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	/**
	 * @OA\Patch(
	 *	 path="/api/v2/media/{identifier}",
	 *	 operationId="updateMedia",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="primary key or record GUID (UUID) associated with target media object",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="apiToken",
	 *		 in="query",
	 *		 description="API security token to authenticate post action",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\RequestBody(
	 *		required=true,
	 *		description="Media object to create",
     *		@OA\MediaType(
     *			mediaType="application/json",
     *			@OA\Schema(
	 *				required={"format", "originalUrl"},
     *				@OA\Property(
     *					property="format",
     *					type="string",
	 *					description="Media Type (MIME type)",
	 *					maxLength=45
     *				),
     *				@OA\Property(
     *					property="originalUrl",
     *					type="string",
	 *					description="URL returning original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="mediumUrl",
     *					type="string",
	 *					description="URL returning medium sized image of original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="thumbnailUrl",
     *					type="string",
	 *					description="URL returning thumbnail derivative of original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="archiveUrl",
     *					type="string",
	 *					description="URL returning large archival image (e.g. DNG, TIFF, etc), if web accessible ",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="referenceUrl",
     *					type="string",
	 *					description="URL returning original large image; image should be a web-ready JPG",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="occid",
     *					type="integer",
	 *					description="Primary Key of occurrence record that media object is associated with. Should be left null if record is not associated with an occurrence.",
     *				),
     *				@OA\Property(
     *					property="tid",
     *					type="integer",
	 *					description="Primary Key of taxon record (e.g. scientific name reference) associated with media object. Should be left NULL if record is associated with an occurrence, since media will inherit occurrence identification. Required if media is not associated with an occurrence record (e.g. field image)",
     *				),
     *				@OA\Property(
     *					property="photographer",
     *					type="string",
	 *					description="Verbatim name of author/photographer media object. Leave NULL if photographerUid is supplied",
	 *					maxLength=100
     *				),
     *				@OA\Property(
     *					property="photographerUid",
     *					type="integer",
	 *					description="User Primary Key of photographer, who must have a user account register in the portal",
     *				),
     *				@OA\Property(
     *					property="imageType",
     *					type="string",
	 *					description="Term name of image type (e.g. stillImage)",
	 *					maxLength=45
     *				),
     *				@OA\Property(
     *					property="caption",
     *					type="string",
	 *					description="Consist, free-form text describing content of media object",
	 *					maxLength=100
     *				),
     *				@OA\Property(
     *					property="copyright",
     *					type="string",
	 *					description="Copyright Owner",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="rights",
     *					type="string",
	 *					description="A URI pointing to structured information about rights held in and over the resource (e.g. https://creativecommons.org/publicdomain/zero/1.0/, https://creativecommons.org/licenses/by/4.0/legalcode)",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="accessRights",
     *					type="string",
	 *					description="Information about who can access the resource or an indication of its security status",
	 *					maxLength=255
     *				),
     *				@OA\Property(
     *					property="locality",
     *					type="string",
	 *					description="Short description of locality. Leave null if locality is available via the occurrence record",
	 *					maxLength=250
     *				),
     *				@OA\Property(
     *					property="notes",
     *					type="string",
	 *					description="General notes",
	 *					maxLength=350
     *				),
     *				@OA\Property(
     *					property="sourceIdentifier",
     *					type="string",
	 *					description="The source identifier, if media was harvested from an external resource",
	 *					maxLength=150
     *				),
     *				@OA\Property(
     *					property="hashFunction",
     *					type="string",
	 *					description="Cryptographic hash function used to compute the value given in the Hash Value",
	 *					maxLength=45
     *				),
     *				@OA\Property(
     *					property="hashValue",
     *					type="string",
	 *					description="Value computed by a hash function applied to the media that will be delivered at the access point",
	 *					maxLength=45
     *				),
     *			),
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="201",
	 *		 description="",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	public function update($id, Request $request){
		if($user = $this->authenticate($request)){
			$media = Media::findOrFail($id);
			$this->validate($request, $this->rulesUpdate);

			$inputArr = $request->all();
			$this->adjustInputData($inputArr);
			$media->update($inputArr);
			return response()->json($media, 200);
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	/**
	 * @OA\Delete(
	 *	 path="/api/v2/media/{identifier}",
	 *	 operationId="deleteMedia",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="primary key or record GUID (UUID) associated with target media object",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="apiToken",
	 *		 in="query",
	 *		 description="API security token to authenticate post action",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Record deleted successfully",
	 *		 @OA\JsonContent(
	 *			@OA\Schema(@OA\Property(property="status", type="boolean"))
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Media identifier is required.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	public function delete($id, Request $request){
		if($user = $this->authenticate($request)){
			Media::findOrFail($id)->delete();
			return response('Media object deleted successfully', 200);
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	private function adjustInputData(&$data){
		if(!empty($data['mediumUrl'])){
			//remap mediumUrl to url field
			$data['url'] = $data['mediumUrl'];
			unset($data['mediumUrl']);
		}
	}

	private function getImgid($id){
		if(!is_numeric($id)){
			$imgId = Media::where('recordID', $id)->value('imgid');
			if(is_numeric($imgId)) $id = $imgId;
		}
		return $id;
	}
}