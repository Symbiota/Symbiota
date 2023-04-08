<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageExplorer.php');

    $imageExplorer = new ImageExplorer();
    $imgArr = $imageExplorer->getImages($_POST);

                echo '<div style="clear:both;">';
                echo '<input type="hidden" id="imgCnt" value="'.$imgArr['cnt'].'" />';

                unset($imgArr['cnt']);

                foreach($imgArr as $imgArr){
                    $imgId = $imgArr['imgid'];
                    $imgUrl = $imgArr['url'];
                    $imgTn = $imgArr['thumbnailurl'];
                    if($imgTn){
                        $imgUrl = $imgTn;
                        if($imageDomain && substr($imgTn,0,1)=='/'){
                            $imgUrl = $imageDomain.$imgTn;
                        }
                    }
                    elseif($imageDomain && substr($imgUrl,0,1)=='/'){
                        $imgUrl = $imageDomain.$imgUrl;
                    }
    ?>

                    <div class="tndiv" style="margin-top: 15px; margin-bottom: 15px">
                        <div class="tnimg">
                            <a href="imgdetails.php?imgid=<?php echo htmlspecialchars($imgId, HTML_SPECIAL_CHARS_FLAGS); ?>">
                                <img src="<?php echo $imgUrl; ?>" />
                            </a>
                        </div>
                        <div>
                            <?php echo "<a href=\"../collections/editor/occurrenceeditor.php?q_customtypeone=EQUALS&occid=". htmlspecialchars($imgArr['occid'], HTML_SPECIAL_CHARS_FLAGS) .  "\">". htmlspecialchars($imgArr['instcode'], HTML_SPECIAL_CHARS_FLAGS) . " - " . htmlspecialchars($imgArr['catalognumber'], HTML_SPECIAL_CHARS_FLAGS) .  "<br />" . htmlspecialchars($imgArr['sciname'], HTML_SPECIAL_CHARS_FLAGS) . "<br />" . htmlspecialchars($imgArr['stateprovince'], HTML_SPECIAL_CHARS_FLAGS); ?>
                            </a>
                        </div>
                    </div>
                <?php
                }
                echo "</div>";
?>