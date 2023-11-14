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
                foreach($catEl as $collid => $nestedCatEl){
                    include('./singleSubcollectionDetails.php');
                    // $collCnt++;
                }
                ?>
            </section>
        </fieldset>
    </div>
</section>