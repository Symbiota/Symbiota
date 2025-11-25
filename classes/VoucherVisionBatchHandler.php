<?php

include_once($GLOBALS['SERVER_ROOT'] . '/config/dbconnection.php');

class VoucherVisionBatchHandler {
    
    private $vvMapping = [
        'SLTPvM_default' => [
            'additionalText' => 'occurrenceremarks',
            'collectedBy' => 'recordedby',
            'collectionDate' => 'eventdate',
            'collectionDateEnd' => 'eventdate2',
            'collectorNumber' => 'recordnumber',
            'continent' => 'continent',
            'country' => 'country',
            'county' => 'county',
            'cultivated' => 'cultivationstatus',
            'decimalLatitude' => 'decimallatitude',
            'decimalLongitude' => 'decimallongitude',
            'habitat' => 'habitat',
            'identifiedBy' => 'identifiedby',
            'identifiedConfidence' => 'identificationqualifier',
            'identifiedDate' => 'dateidentified',
            'identifiedRemarks' => 'identificationremarks',
            'locality' => 'locality',
            'maximumElevationInMeters' => 'maximumelevationinmeters',
            'minimumElevationInMeters' => 'minimumelevationinmeters',
            'scientificName' => 'sciname',
            'scientificNameAuthorship' => 'scientificnameauthorship',
            'specimenDescription' => 'verbatimattributes',
            'stateProvince' => 'stateprovince',
            'verbatimCollectionDate' => 'verbatimeventdate',
            'verbatimCoordinates' => 'verbatimcoordinates'
        ],
        'OSC_Symbiota' => [
            'collector' => 'recordedby',
            'associatedCollectors' => 'associatedcollectors',
            'collectorNumber' => 'recordnumber',
            'verbatimCollectionDate' => 'verbatimeventdate',
            'collectionDate' => 'eventdate',
            'scientificName' => 'sciname',
            'scientificNameAuthorship' => 'scientificnameauthorship',
            'family' => 'family',
            'identifiedBy' => 'identifiedby',
            'identifiedConfidence' => 'identificationqualifier',
            'identifiedDate' => 'dateidentified',
            'identifiedRemarks' => 'identificationremarks',
            'continent' => 'continent',
            'country' => 'country',
            'stateProvince' => 'stateprovince',
            'county' => 'county',
            'locality' => 'locality',
            'decimalLatitude' => 'decimallatitude',
            'decimalLongitude' => 'decimallongitude',
            'verbatimCoordinates' => 'verbatimcoordinates',
            'datum' => 'geodeticdatum',
            'verbatimElevation' => 'verbatimelevation',
            'cultivated' => 'cultivationstatus',
            'habitat' => 'habitat',
            'specimenDescription' => 'verbatimattributes',
            'associatedSpecies' => 'associatedtaxa',
            'additionalText' => 'occurrenceremarks'
        ]
    ];
    
    /**
     * Run VoucherVision OCR and transcription on a batch of images
     *
     * @param array $imageUrls Associative array with occids as keys and image URLs as values
     * @param string $prompt The prompt to use for transcription
     * @param array $engines Array of OCR engines to use
     * @param bool $ocrOnly Whether to only perform OCR without transcription
     * @param string $llmModel The LLM model to use for transcription
     * @return array Results from VoucherVision API calls
     */
    public function runVoucherVision($imageUrls, $prompt, $engines, $ocrOnly, $llmModel) {
        $results = [];
        
        foreach ($imageUrls as $occid => $imageUrl) {
            try {
                // Construct the data object for the API call
                $vvData = [
                    'image_url' => $imageUrl,
                    'engines' => $engines,
                    'prompt' => $prompt . '.yaml',
                    'llm_model' => $llmModel,
                    'ocr_only' => $ocrOnly
                ];
                
                // Make the API call
                $response = $this->makeVoucherVisionRequest($vvData);
                
                if ($response !== false) {
                    $results[$occid] = [
                        'success' => true,
                        // 'data' => $response,
                        'formatted_json' => $response['formatted_json'] ?? null,
                        'image_url' => $imageUrl
                    ];
                } else {
                    $results[$occid] = [
                        'success' => false,
                        'error' => 'Failed to get response from VoucherVision API',
                        'image_url' => $imageUrl
                    ];
                }
            } catch (Exception $e) {
                $results[$occid] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'image_url' => $imageUrl
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Construct the RPC URL for VoucherVision API calls
     *
     * @return string The constructed RPC URL
     */
    protected function constructRpcUrl() { // @TODO decide whether this is necessary
        // Determine the correct path to the RPC endpoint
        $serverRoot = $GLOBALS['SERVER_ROOT'] ?? '';
        $rpcUrl = !empty($serverRoot) ? ($serverRoot . '/collections/editor/rpc/voucherVisionGo.php') : 'collections/editor/rpc/voucherVisionGo.php';
        
        // For testing or local development, we might need to construct full URL
        if (!filter_var($rpcUrl, FILTER_VALIDATE_URL)) {
            // Construct base URL from server variables if available
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $protocol . '://' . $host;
            
            // Add path from document root if available
            if (isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['SCRIPT_NAME'])) {
                // Get the web root path relative to document root
                $scriptName = $_SERVER['SCRIPT_NAME'];
                
                // If $rpcUrl starts with $serverRoot (absolute path), we need to convert it to relative
                if (!empty($serverRoot) && strpos($rpcUrl, $serverRoot) === 0) {
                    // Remove the server root part to get relative path
                    $relativePath = substr($rpcUrl, strlen($serverRoot));
                    // Find the web root by looking for where the server root maps to in the URL
                    $webRoot = '';
                    if (strpos($scriptName, '/') !== false) {
                        $pathParts = explode('/', trim($scriptName, '/'));
                        // Find the Symbiota part in the path
                        for ($i = 0; $i < count($pathParts); $i++) {
                            if ($pathParts[$i] === 'Symbiota' || $pathParts[$i] === basename($serverRoot)) {
                                $webRoot = '/' . implode('/', array_slice($pathParts, 0, $i + 1));
                                break;
                            }
                        }
                        if (empty($webRoot)) {
                            // Fallback: use first path component
                            $webRoot = '/' . $pathParts[0];
                        }
                    }
                    $rpcUrl = $baseUrl . $webRoot . $relativePath;
                } else {
                    // $rpcUrl is already relative
                    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                    $rpcUrl = $baseUrl . $scriptDir . '/' . $rpcUrl;
                }
            } else {
                // Fallback: treat as relative path
                if (!empty($serverRoot) && strpos($rpcUrl, $serverRoot) === 0) {
                    $relativePath = substr($rpcUrl, strlen($serverRoot));
                    $rpcUrl = $baseUrl . $relativePath;
                } else {
                    $rpcUrl = $baseUrl . '/' . $rpcUrl;
                }
            }
        }
        
        return $rpcUrl;
    }
    
    /**
     * Make a request to the VoucherVision API via the rpc endpoint
     *
     * @param array $vvData Data to send to the API
     * @return array|false Decoded JSON response or false on failure
     */
    private function makeVoucherVisionRequest($vvData) {
        // Get the RPC URL using the dedicated method
        $rpcUrl = $this->constructRpcUrl();
        
        // Initialize cURL
        $curl = curl_init();
        
        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $rpcUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($vvData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 300, // 5 minute timeout for long API calls
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false, // For development/testing // @TODO change for prod?
        ]);
        
        // Execute the request
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        
        curl_close($curl);
        
        // Check for cURL errors
        if ($curlError) {
            error_log("API error in VoucherVisionBatchHandler: " . $curlError);
            return false;
        }
        
        // Check for HTTP errors
        if ($httpCode !== 200) {
            error_log("HTTP Error in VoucherVisionBatchHandler: HTTP {$httpCode}");
            return false;
        }
        
        // Decode and return the JSON response
        $decodedResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error in VoucherVisionBatchHandler: " . json_last_error_msg());
            return false;
        }
        
        return $decodedResponse;
    }
    
    /**
     * Get the field mapping for a specific prompt
     *
     * @param string $prompt The prompt name
     * @return array|null The mapping array or null if not found
     */
    public function getFieldMapping($prompt) {
        return $this->vvMapping[$prompt] ?? null;
    }
    
    /**
     * Extract and format cost information from VoucherVision response
     *
     * @param array $response VoucherVision API response
     * @return array Cost breakdown
     */
    public function extractCostInfo($response) {
        $cost = [
            'ocr' => 0,
            'transcription' => 0,
            'total' => 0
        ];
        
        // Get transcription cost
        if (isset($response['parsing_info'])) {
            $cost['transcription'] = ($response['parsing_info']['cost_in'] ?? 0) + 
                                   ($response['parsing_info']['cost_out'] ?? 0);
        }
        
        // Get OCR cost
        if (isset($response['ocr_info']) && is_array($response['ocr_info'])) {
            foreach ($response['ocr_info'] as $ocrData) {
                $cost['ocr'] += ($ocrData['cost_in'] ?? 0) + ($ocrData['cost_out'] ?? 0);
            }
        }
        
        // Calculate total cost
        $cost['total'] = $cost['transcription'] + $cost['ocr'];
        
        return $cost;
    }
}

?>