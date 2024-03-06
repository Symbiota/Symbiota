<?php
?>
    <section class="gridlike-form-row bottom-breathing-room-relative">
        <div class="collection-checkbox">
            <?php
            $codeStr = '(';
            if(array_key_exists('instcode', $cArr)){
                $codeStr = ' (' . $cArr['instcode'];
            }
            if(array_key_exists('collcode', $cArr)){
                $codeStr .= '-' . $cArr['collcode'];
            } 
            $codeStr .= ')';
            echo '<input data-chip="Collection: ' . $codeStr . '" aria-label="select collection ' . $collid . '" data-role="none" id="collection-' . $collid . '" name="db[]" value="' . $collid . '" type="checkbox" onclick="uncheckAll()" '.(!$collSelArr || in_array($collid, $collSelArr)?'checked':'').' />';
            ?>
        </div>
        <div>
            <div class="collectiontitle">
                <?php
                $codeStr = '('.$cArr['instcode'];
                if($cArr['collcode']) $codeStr .= '-'.$cArr['collcode'];
                $codeStr .= ')';
                echo '<div class="collectionname">'.$cArr["collname"].'</div> <div class="collectioncode">'.$codeStr.'</div> ';
                ?>
                <a href = '<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>' target="_blank">
                    <?php echo (isset($LANG['MORE_INFO'])?$LANG['MORE_INFO']:'more info...'); ?>
                </a>
            </div>
        </div>
    </section>
<?php
?>