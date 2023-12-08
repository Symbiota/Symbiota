<section class="gridlike-form-row bottom-breathing-room-relative">
    <?php
    if($displayIcons){
        ?>
        <div class="cat-icon-div">
            <?php
            if(array_key_exists('icon', $nestedCatEl)){
                $isInLocalFileSys = isset($nestedCatEl["icon"]) && substr($nestedCatEl["icon"],0,6)=='images';
                $prefix = $isInLocalFileSys ? $CLIENT_ROOT : '';
                $cIcon = isset($nestedCatEl["icon"]) ? $prefix . $nestedCatEl["icon"]: '#';
                ?>
                <a href = '<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>'>
                    <img src="<?php echo htmlspecialchars($cIcon, HTML_SPECIAL_CHARS_FLAGS); ?>" style="border:0px;width:30px;height:30px;" alt='Icon associated with collection <?php echo isset($nestedCatEl["collname"]) ? substr($nestedCatEl["collname"],0, 20) : substr($idStr,0, 20) ?>' />
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
        $codeStr = '(';
        if(array_key_exists('instcode', $nestedCatEl)){
            $codeStr = ' (' . $nestedCatEl['instcode'];
        }
        if(array_key_exists('collcode', $nestedCatEl)){
            $codeStr .= '-' . $nestedCatEl['collcode'];
        } 
        $codeStr .= ')';
        echo '<input  data-chip="Collection: ' . $codeStr . '" aria-label="select collection ' . $collid . '" id="coll-' . $collid . '-' . $idStr . '" data-role="none" name="db[]" value="'.$collid.'" type="checkbox" class="cat-'.$idStr.'" onclick="unselectCat(\'cat-' . $idStr . '-Input\')" '.($catSelected || !$collSelArr || in_array($collid, $collSelArr)?'checked':'').' />';
        ?>
    </div>
    <div>
        <div class="collectiontitle">
            <?php
            $colName = $nestedCatEl["collname"] ?? 'Unknown Name';
            echo '<div class="collectionname">' . $colName . '</div><div class="collectioncode">' . $codeStr . '</div>';
            ?>
            <a href='<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>' target="_blank">
                <?php echo (isset($LANG['MORE_INFO']) ? $LANG['MORE_INFO'] : 'more info...'); ?>
            </a>
        </div>
    </div>
</section>