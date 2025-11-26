<?php

include_once('../../config/symbini.php');
include_once($GLOBALS['SERVER_ROOT'] . '/classes/VoucherVisionBatchHandler.php');

header('Content-Type: text/html; charset=' . $CHARSET);

$labelImageUrls = [
    'https://mvzhandbook.berkeley.edu/wp-content/uploads/sites/46/2020/08/small_vial_label-1024x518.jpg',
    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTeZ6xezTI_6EuhNiUAoqVXXDm3NHV7HK2ohQ&s',
    'https://i.pinimg.com/474x/76/b1/d9/76b1d93b4117962183af5a5495c41583.jpg',
    'https://fm-digital-assets.fieldmuseum.org/472/500/P16376_label.jpg',
    // 'https://blogs.ucl.ac.uk/museums/files/2016/05/web2.jpg',
    // 'https://i.redd.it/w9qmx2dbyg211.jpg',
    // 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQRvpLK6eLbDBBVn6b71LKu_BsFkFiG2rh7tA&s',
    // 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Specimen_label_from_the_collection_of_Anatolia_College_Museum.jpg',
    // 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIFcJJA3m0jtLlPgnA5sHzxDWjzD21aMsLCw&s',
    // 'https://fm-digital-assets.fieldmuseum.org/1245/733/38215_Potamopyrgus_subgradatus_holotype_label_FMNH_IZ.JPG',
    // 'https://images.nms.ac.uk/production/Images/Collection-objects/Natural-Sciences/ws-bruce-collection.jpg?w=570&h=426&q=100&auto=format&fit=crop&dm=1721982193&s=aa02f1d3503e8ed5eeef066f3aed54bd',
    // 'https://carnegiemnh.org/wp-content/uploads/2018/03/tumblr_inline_p6explwVjB1tiol9c_540-1.jpg',
    // 'https://fm-digital-assets.fieldmuseum.org/472/560/UC24417_label.jpg',
    // 'https://carnegiemnh.org/wp-content/uploads/2018/12/tumblr_inline_pkgqhp92mh1tiol9c_540.jpg',
    // 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTCYGm_I-zW1MXxpnV9I1CAQzSVtfuznS_rDg&s',
    // 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkZu_1j0ePBF5SdMixNIcvpEHpg9GxXL4F8A&s',
    // 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSxi_S6twMRibVdjCZgxn4TVTDnDAkUtxPzsw&s',
    // 'https://ucmp.berkeley.edu/images/science/fieldnotes/vendetti/6bruclarkia_barkeriana600.jpg',
    // 'https://cdn.irocks.com/storage/media/57775/conversions/japanrhodolabel-large.jpg',
    // 'https://image.invaluable.com/housePhotos/reddingauction/38/742338/H21122-L320720072.jpg',
    // 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSuj0RtPWnvNlQEXyZ_azFN7KEM9r8yow3fnw&s',
    // 'https://c8.alamy.com/comp/2JEDKXY/endoceras-proteiforme-hall-2JEDKXY.jpg',
    // 'https://www.popsci.com/wp-content/uploads/2022/02/09/Indigenous-seed-University-Michigan-museum-collection.jpg?quality=85&w=768'
];

// Create associative array with mock occids as keys and image URLs as values
$imageUrlsWithOccids = [];
$occidBase = 100000;
foreach ($labelImageUrls as $index => $url) {
    $imageUrlsWithOccids[$occidBase + $index] = $url;
}

$prompt = 'OSC_Symbiota';
$engines = ['gemini-2.0-flash'];
$ocrOnly = false;
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

var_dump($results);
