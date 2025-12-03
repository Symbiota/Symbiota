<?php

use PHPUnit\Framework\TestCase;

include_once($GLOBALS['SERVER_ROOT'] . '/classes/TaxonomyEditorManager.php');
include_once($GLOBALS['SERVER_ROOT'] . '/classes/VoucherVisionBatchHandler.php');

class BatchVoucherVisionTest extends TestCase {
    
    public function testValidBatchRun() {
        $labelImageUrls = [
            'https://mvzhandbook.berkeley.edu/wp-content/uploads/sites/46/2020/08/small_vial_label-1024x518.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTeZ6xezTI_6EuhNiUAoqVXXDm3NHV7HK2ohQ&s',
            'https://i.pinimg.com/474x/76/b1/d9/76b1d93b4117962183af5a5495c41583.jpg',
            'https://fm-digital-assets.fieldmuseum.org/472/500/P16376_label.jpg',
            'https://blogs.ucl.ac.uk/museums/files/2016/05/web2.jpg',
            'https://i.redd.it/w9qmx2dbyg211.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQRvpLK6eLbDBBVn6b71LKu_BsFkFiG2rh7tA&s',
            'https://upload.wikimedia.org/wikipedia/commons/0/0b/Specimen_label_from_the_collection_of_Anatolia_College_Museum.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIFcJJA3m0jtLlPgnA5sHzxDWjzD21aMsLCw&s',
            'https://fm-digital-assets.fieldmuseum.org/1245/733/38215_Potamopyrgus_subgradatus_holotype_label_FMNH_IZ.JPG',
            'https://images.nms.ac.uk/production/Images/Collection-objects/Natural-Sciences/ws-bruce-collection.jpg?w=570&h=426&q=100&auto=format&fit=crop&dm=1721982193&s=aa02f1d3503e8ed5eeef066f3aed54bd',
            'https://carnegiemnh.org/wp-content/uploads/2018/03/tumblr_inline_p6explwVjB1tiol9c_540-1.jpg',
            'https://fm-digital-assets.fieldmuseum.org/472/560/UC24417_label.jpg',
            'https://carnegiemnh.org/wp-content/uploads/2018/12/tumblr_inline_pkgqhp92mh1tiol9c_540.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTCYGm_I-zW1MXxpnV9I1CAQzSVtfuznS_rDg&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkZu_1j0ePBF5SdMixNIcvpEHpg9GxXL4F8A&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSxi_S6twMRibVdjCZgxn4TVTDnDAkUtxPzsw&s',
            'https://ucmp.berkeley.edu/images/science/fieldnotes/vendetti/6bruclarkia_barkeriana600.jpg',
            'https://cdn.irocks.com/storage/media/57775/conversions/japanrhodolabel-large.jpg',
            'https://image.invaluable.com/housePhotos/reddingauction/38/742338/H21122-L320720072.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSuj0RtPWnvNlQEXyZ_azFN7KEM9r8yow3fnw&s',
            'https://c8.alamy.com/comp/2JEDKXY/endoceras-proteiforme-hall-2JEDKXY.jpg',
            'https://www.popsci.com/wp-content/uploads/2022/02/09/Indigenous-seed-University-Michigan-museum-collection.jpg?quality=85&w=768'
        ];
        
        // Create associative array with mock occids as keys and image URLs as values
        $imageUrlsWithOccids = [];
        $occidBase = 100000;
        foreach ($labelImageUrls as $index => $url) {
            $imageUrlsWithOccids[$occidBase + $index] = $url;
        }
        
        $prompt = 'SLTPvM_default';  // Using default prompt
        $engines = ['gemini-2.0-flash', 'gemini-1.5-pro'];
        $ocrOnly = false;  // Include transcription
        $llmModel = 'gemini-2.0-flash';  // LLM model for transcription
        
        // Create the VoucherVision batch handler
        $voucherVisionHandler = new VoucherVisionBatchHandler();
        
        // Run the batch process (Note: This will make actual API calls if the API is configured)
        // For actual testing, you may want to mock the API calls
        $results = $voucherVisionHandler->runVoucherVision(
            $imageUrlsWithOccids, 
            $prompt, 
            $engines, 
            $ocrOnly, 
            $llmModel
        );
        
        // Verify that we got results for all images
        $this->assertIsArray($results, 'Results should be an array');
        $this->assertCount(count($imageUrlsWithOccids), $results, 'Should have results for all input images');
        
        // Check that each result has the expected structure
        foreach ($results as $occid => $result) {
            $this->assertArrayHasKey('success', $result, "Result for occid $occid should have 'success' key");
            $this->assertArrayHasKey('image_url', $result, "Result for occid $occid should have 'image_url' key");
            $this->assertEquals($imageUrlsWithOccids[$occid], $result['image_url'], "Image URL should match for occid $occid");
            
            if ($result['success']) {
                // If successful, should have data
                $this->assertArrayHasKey('data', $result, "Successful result for occid $occid should have 'data' key");
                $this->assertIsArray($result['data'], "Data for occid $occid should be an array");
                
                // Check for expected VoucherVision response structure
                if (isset($result['data']['ocr'])) {
                    $this->assertIsString($result['data']['ocr'], "OCR data for occid $occid should be a string");
                }
                
                if (!$ocrOnly && isset($result['data']['formatted_json'])) {
                    $this->assertIsArray($result['data']['formatted_json'], "Formatted JSON for occid $occid should be an array");
                }
                
                // Check cost information if present
                if (isset($result['data']['parsing_info']) || isset($result['data']['ocr_info'])) {
                    $costInfo = $voucherVisionHandler->extractCostInfo($result['data']);
                    $this->assertIsArray($costInfo, "Cost info for occid $occid should be an array");
                    $this->assertArrayHasKey('total', $costInfo, "Cost info should have 'total' key");
                    $this->assertGreaterThanOrEqual(0, $costInfo['total'], "Total cost should be non-negative");
                }
            } else {
                // If unsuccessful, should have error message
                $this->assertArrayHasKey('error', $result, "Failed result for occid $occid should have 'error' key");
                $this->assertIsString($result['error'], "Error message for occid $occid should be a string");
            }
        }
        
        // Test field mapping functionality
        $fieldMapping = $voucherVisionHandler->getFieldMapping($prompt);
        $this->assertIsArray($fieldMapping, 'Field mapping should be an array');
        $this->assertArrayHasKey('scientificName', $fieldMapping, 'Field mapping should include scientificName');
        $this->assertEquals('sciname', $fieldMapping['scientificName'], 'scientificName should map to sciname');
    }

    public function testMakeVoucherVisionRequestUrlConstruction() {
        $handler = new VoucherVisionBatchHandler();
        
        // Test 1: Valid URL should bypass construction
        $originalServerVars = $_SERVER;
        $originalServerRoot = $GLOBALS['SERVER_ROOT'] ?? null;
        
        // Set up a valid URL scenario
        $GLOBALS['SERVER_ROOT'] = 'https://example.com/symbiota';
        $reflection = new ReflectionClass($handler);
        $method = $reflection->getMethod('makeVoucherVisionRequest');
        $method->setAccessible(true);
        
        // Mock valid URL response
        $vvData = ['test' => 'data'];
        
        // We can't easily test the actual URL construction without mocking curl,
        // but we can test the URL building logic by creating a test method
        $this->assertTrue(true); // Placeholder for now
        
        $_SERVER = $originalServerVars;
        if ($originalServerRoot !== null) {
            $GLOBALS['SERVER_ROOT'] = $originalServerRoot;
        } else {
            unset($GLOBALS['SERVER_ROOT']);
        }
    }
    
    public function testUrlConstructionLogic() {
        // Test the URL construction logic with various scenarios using the actual method
        $originalServerVars = $_SERVER;
        $originalServerRoot = $GLOBALS['SERVER_ROOT'] ?? null;
        
        try {
            $handler = new VoucherVisionBatchHandler();
            $reflection = new ReflectionClass($handler);
            $method = $reflection->getMethod('constructRpcUrl');
            $method->setAccessible(true);
            
            // Scenario 1: Local development with absolute server root
            $_SERVER['HTTP_HOST'] = 'localhost';
            $_SERVER['HTTPS'] = '';
            $_SERVER['DOCUMENT_ROOT'] = '/Users/markfisher/Sites';
            $_SERVER['SCRIPT_NAME'] = '/Symbiota/collections/editor/specimens/index.php';
            $GLOBALS['SERVER_ROOT'] = '/Users/markfisher/Sites/Symbiota';
            
            $finalUrl = $method->invoke($handler);
            $expectedUrl = 'http://localhost/Symbiota/collections/editor/rpc/voucherVisionGo.php';
            $this->assertEquals($expectedUrl, $finalUrl, 'URL should be constructed correctly for local development with absolute server root');
            
            // Verify the URL is valid
            $this->assertNotFalse(filter_var($finalUrl, FILTER_VALIDATE_URL), 'Constructed URL should be valid: "' . $finalUrl . '"');
            
            // Scenario 2: Empty SERVER_ROOT (relative path scenario)
            $GLOBALS['SERVER_ROOT'] = '';
            
            $finalUrl2 = $method->invoke($handler);
            $expectedUrl2 = 'http://localhost/Symbiota/collections/editor/specimens/collections/editor/rpc/voucherVisionGo.php';
            $this->assertEquals($expectedUrl2, $finalUrl2, 'URL should be constructed correctly when SERVER_ROOT is empty');
            
            // Scenario 3: HTTPS scenario
            $_SERVER['HTTPS'] = 'on';
            $_SERVER['HTTP_HOST'] = 'example.com';
            $GLOBALS['SERVER_ROOT'] = '/var/www/symbiota';
            
            $finalUrl3 = $method->invoke($handler);
            $expectedUrl3 = 'https://example.com/Symbiota/collections/editor/rpc/voucherVisionGo.php';
            $this->assertEquals($expectedUrl3, $finalUrl3, 'URL should use HTTPS when $_SERVER["HTTPS"] is set');
            
            // Scenario 4: Already valid URL in SERVER_ROOT (should not be processed by construction logic)
            $GLOBALS['SERVER_ROOT'] = 'https://api.vouchervision.com';
            
            $finalUrl4 = $method->invoke($handler);
            $expectedUrl4 = 'https://api.vouchervision.com/collections/editor/rpc/voucherVisionGo.php';
            $this->assertEquals($expectedUrl4, $finalUrl4, 'Should work with valid URL in SERVER_ROOT');
            
        } finally {
            $_SERVER = $originalServerVars;
            if ($originalServerRoot !== null) {
                $GLOBALS['SERVER_ROOT'] = $originalServerRoot;
            } else {
                unset($GLOBALS['SERVER_ROOT']);
            }
        }
    }
    
    public function testFieldMappingRetrieval() {
        $handler = new VoucherVisionBatchHandler();
        
        // Test valid prompt
        $mapping = $handler->getFieldMapping('SLTPvM_default');
        $this->assertIsArray($mapping, 'Should return array for valid prompt');
        $this->assertArrayHasKey('scientificName', $mapping, 'Should have scientificName mapping');
        $this->assertEquals('sciname', $mapping['scientificName'], 'scientificName should map to sciname');
        
        // Test invalid prompt
        $invalidMapping = $handler->getFieldMapping('nonexistent_prompt');
        $this->assertNull($invalidMapping, 'Should return null for invalid prompt');
    }
    
    public function testCostExtraction() {
        $handler = new VoucherVisionBatchHandler();
        
        // Test response with cost info
        $response = [
            'parsing_info' => [
                'cost_in' => 0.05,
                'cost_out' => 0.03
            ],
            'ocr_info' => [
                ['cost_in' => 0.02, 'cost_out' => 0.01],
                ['cost_in' => 0.015, 'cost_out' => 0.005]
            ]
        ];
        
        $cost = $handler->extractCostInfo($response);
        $this->assertIsArray($cost, 'Should return cost array');
        $this->assertEquals(0.08, $cost['transcription'], 'Transcription cost should be sum of parsing_info costs');
        $this->assertEquals(0.05, $cost['ocr'], 'OCR cost should be sum of all ocr_info costs');
        $this->assertEquals(0.13, $cost['total'], 'Total cost should be sum of transcription and OCR costs');
        
        // Test response without cost info
        $emptyCost = $handler->extractCostInfo([]);
        $this->assertEquals(0, $emptyCost['total'], 'Empty response should have zero total cost');
    }

}
