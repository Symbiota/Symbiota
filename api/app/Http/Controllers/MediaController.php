<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
	/**
	 * Media controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

	}

	public function showAllMedia()
	{
		return response()->json(Media::skip(0)->take(100)->get());
	}

	public function showOneMedia($id)
	{
		return response()->json(Media::find($id));
	}

	public function create(Request $request)
	{
		$media = Media::create($request->all());

		return response()->json($media, 201);
	}

	public function update($id, Request $request)
	{
		$media = Media::findOrFail($id);
		$media->update($request->all());

		return response()->json($media, 200);
	}

	public function delete($id)
	{
		Media::findOrFail($id)->delete();
		return response('Media object deleted successfully', 200);
	}
}