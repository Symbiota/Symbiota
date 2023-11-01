<?php
$name = $catEl['name'] ?? '';
if($catEl['acronym']) $name .= ' ('.$catEl['acronym'].')';
$catIcon = $catEl['icon'];
// @TODO decide if the unsets are necessary
$idStr = $catId; // deleteMe $this->collArrIndex.'-'.$catid;
?>
<section class="gridlike-form-row bottom-breathing-room-relative">
    <?php
    if($displayIcons){
        ?>
        <div class="<?php echo ($catIcon?'cat-icon-div':''); ?>">
        <?php
        if($catIcon){
            $catIcon = (substr($catIcon,0,6)=='images'?$CLIENT_ROOT:'').$catIcon;
            echo '<img src="'.$catIcon.'" style="border:0px;width:30px;height:30px;" />';
        }
        ?>
    </div>
    <?php
    }
    ?>
    <div>
        <?php
        $catSelected = false;
        if(!$catSelArr && !$collSelArr) $catSelected = true;
        elseif(in_array($catid, $catSelArr)) $catSelected = true;
        $ariaLabel = $name . '(' . $collTypeLabel . ')' . '-' . $uniqGrouping;
        echo '<input aria-label="' . $ariaLabel . '"   data-role="none" id="cat-' . $idStr . '-' . $collTypeLabel . '-' . $uniqGrouping . '-Input" name="cat[]" value="' . $catid.'" type="checkbox" onclick="selectAllCat(this,\'cat-' . $idStr . '\')" ' . ($catSelected?'checked':'') . ' />';
        echo $name . "(" . $collTypeLabel . ")";
        ?>
    </div>
<?php