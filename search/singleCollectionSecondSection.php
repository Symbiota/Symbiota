<section id="cat-<?php echo $idStr ?>" class="gridlike-form-row bottom-breathing-room-relative">
    <div>
        <fieldset>
            <legend>
                <?php 
                echo $name; 
                $specimenLegendTxt = isset($LANG['SPECIMEN']) ? $LANG['SPECIMEN'] : "Specimen";
                $observationLegendTxt = isset($LANG['OBSERVATION']) ? $LANG['OBSERVATION'] : "Observation";
                $isObservation = $collTypeLabel === "Observation";
                $outputTxt = $specimenLegendTxt;
                if($isObservation) $outputTxt = $observationLegendTxt;
                ?> 
                (<?php echo $outputTxt ?>)
            </legend>
            <section class="gridlike-form">
                <?php
                foreach($catEl as $collid => $collName2){
                    ?>
                    <section class="gridlike-form-row bottom-breathing-room-relative">
                        <?php
                        if($displayIcons){
                            ?>
                            <div class="cat-icon-div">
                                <?php
                                if($collName2["icon"]){
                                    $cIcon = (substr($collName2["icon"],0,6)=='images'?$CLIENT_ROOT:'').$collName2["icon"];
                                    ?>
                                    <a href = '<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'>
                                        <img src="<?php echo htmlspecialchars($cIcon, HTML_SPECIAL_CHARS_FLAGS); ?>" style="border:0px;width:30px;height:30px;" alt='Icon associated with collection <?php echo isset($collName2["collname"]) ? substr($collName2["collname"],0, 20) : substr($idStr,0, 20) ?>' />
                                    </a>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div>
                            <?php
                            echo '<input aria-label="select collection ' . $collid . '" id="coll-' . $collid . '-' . $idStr . '" data-role="none" name="db[]" value="'.$collid.'" type="checkbox" class="cat-'.$idStr.'" onclick="unselectCat(\'cat-'.$idStr.'-Input\')" '.($catSelected || !$collSelArr || in_array($collid, $collSelArr)?'checked':'').' />';
                            ?>
                        </div>
                        <div>
                            <div class="collectiontitle">
                                <?php
                                $codeStr = '(';
                                if(array_key_exists('instcode', $collName2)){
                                    $codeStr = ' (' . $collName2['instcode'];
                                }
                                if(array_key_exists('collcode', $collName2)){
                                    $codeStr .= '-'.$collName2['collcode'];
                                } 
                                $codeStr .= ')';
                                $colName = $collName2["collname"] ?? 'Unknown Name';
                                echo '<div class="collectionname">' . $colName . '</div><div class="collectioncode">' . $codeStr . '</div>';
                                ?>
                                <a href='<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>' target="_blank">
                                    <?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info...'); ?>
                                </a>
                            </div>
                        </div>
                    </section>
                    <?php
                    $collCnt++;
                }
                ?>
            </section>
        </fieldset>
    </div>
</section>