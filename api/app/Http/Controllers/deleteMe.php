/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence",
	 *	 operationId="/api/v2/occurrence",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="collid",
	 *		 in="query",
	 *		 description="collid(s) - collection identifier(s) in portal",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="catalogNumber",
	 *		 in="query",
	 *		 description="catalogNumber",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="occurrenceID",
	 *		 in="query",
	 *		 description="occurrenceID",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="family",
	 *		 in="query",
	 *		 description="family",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="sciname",
	 *		 in="query",
	 *		 description="Scientific Name - binomen only without authorship",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordedBy",
	 *		 in="query",
	 *		 description="Collector/observer of occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordedByLastName",
	 *		 in="query",
	 *		 description="Last name of collector/observer of occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordNumber",
	 *		 in="query",
	 *		 description="Personal number of the collector or observer of the occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="eventDate",
	 *		 in="query",
	 *		 description="Date as YYYY, YYYY-MM or YYYY-MM-DD that the occurrence was collected or observed, or earliest date if a range was provided",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="eventDate2",
	 *		 in="query",
	 *		 description="Last date as YYYY, YYYY-MM or YYYY-MM-DD that the occurrence was collected or observed. Used when a date range is provided",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="decimalLatitude",
	 *		 in="query",
	 *		 description="Latitude as a decimal",
	 *		 required=false,
	 *		 @OA\Schema(type="number")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="decimalLongitude",
	 *		 in="query",
	 *		 description="Longitude as a decimal",
	 *		 required=false,
	 *		 @OA\Schema(type="number")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="country",
	 *		 in="query",
	 *		 description="country",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="stateProvince",
	 *		 in="query",
	 *		 description="State, Province, or second level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="county",
	 *		 in="query",
	 *		 description="County, parish, or third level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="datasetID",
	 *		 in="query",
	 *		 description="dataset ID within portal",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Controls the number of results per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=100)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="offset",
	 *		 in="query",
	 *		 description="Determines the starting point for the search results. A limit of 100 and offset of 200, will display 100 records starting the 200th record.",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=0)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of occurrences",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */