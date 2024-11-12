<script src="<?= $CLIENT_ROOT ?>/js/jquery.slides.js"></script>

<div style="float:right;clear:right;width:400px;">
    <?php
    $ssId = 1;
    $numSlides = 10;
    $width = 350;
    $dayInterval = 7;
    //This value needs to be set to a valid checklist ID
    $clid = 157;
    $imageType = "both";
    $numDays = 240;

    ini_set('max_execution_time', 120); //300 seconds = 5 minutes
    include_once('classes/PluginsManager.php');
    $pluginManager = new PluginsManager();
    echo $pluginManager->createSlideShow($ssId,$numSlides,$width,$numDays,$imageType,$clid,$dayInterval);
    ?>
</div>