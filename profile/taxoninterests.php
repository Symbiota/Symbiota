<script src="../../js/jquery.js?ver=131123" type="text/javascript"></script>
<script src="../../js/jquery-ui.js?ver=131123" type="text/javascript"></script>

<?php
/* Copyright � 2012 President and Fellows of Harvard College
 *
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of Version 2 of the GNU General Public License
* as published by the Free Software Foundation.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* Author: David B. Lowery
*/

include_once('../config/symbini.php');
include_once('fp/FPNetworkFactory.php');
include_once('fp/FPConfig.php');
include_once('fp/common/AnnotationGenerator.php');
@include_once($SERVER_ROOT.'/content/lang/profile/taxoninterests.'.$LANG_TAG.'.php');

// check that the client helper has been installed
$file = 'fp/FPNetworkFactory.php';
$includePaths = explode(PATH_SEPARATOR, get_include_path());
$fileExists = false;

foreach ($includePaths as $p) {
    $fullname = $p . DIRECTORY_SEPARATOR . $file;
    if (is_file($fullname)) {
        $fileExists = true;
        break;
    }
}

if (!$fileExists) {
    echo (isset($LANG['NEED_HELPER'])?$LANG['NEED_HELPER']:'FilteredPush Support has been enabled in this Symbiota installation, but FilteredPush helper code is not installed').'<br>';
    echo '<strong>$file '.(isset($LANG['NOT_FOUND'])?$LANG['NEED_HELPER']:'not found').'.</strong>';
} else {

        $endpoint = FPNetworkFactory::getSparqlEndpoint();

        // returns query result formatted as html
        $results = json_decode($endpoint->getAnnotations($_GET));

        $annotations = array();
        $responses = array();

        foreach ($results as $result) {
            if (isset($result->describesObject)) {
                $responses[] = $result;
            } else {
                $annotations[] = $result;
            }
        }

        foreach ($annotations as $annotation) {
            echo "<h1>" . $_GET['scientificName'] . " - (" . $annotation->scientificNameAuthorship . ")</h1>";
            echo '<p><a href="response.php?uri=' . htmlspecialchars($annotation->uri, HTML_SPECIAL_CHARS_FLAGS) . '" onclick="window.open(this.href, \'popupwindow\', \'width=500,height=400\'); return false;">' . htmlspecialchars((isset($LANG['RESPOND'])?$LANG['RESPOND']:'Respond'), HTML_SPECIAL_CHARS_FLAGS) . '</a><br />';
            echo "<a href=\"" . htmlspecialchars($CLIENTHELPER_ENDPOINT, HTML_SPECIAL_CHARS_FLAGS) . "/clientHelper/getAnnotation/?uri=" . htmlspecialchars($annotation->uri, HTML_SPECIAL_CHARS_FLAGS) . "\" target=\"_blank\">View</a><br /></p>";
            ?>
            <table>
                <tr>
                    <td><b><?php echo (isset($LANG['CREATED_BY'])?$LANG['CREATED_BY']:'Created By'); ?>:</b></td>
                    <td style="padding-right:35px;"><? echo $annotation->createdBy ?></td>
                    <td><b><?php echo (isset($LANG['CREATED_ON'])?$LANG['CREATED_BY']:'Created On'); ?>:</b></td>
                    <td><? echo $annotation->date ?></td>
                </tr>
                <tr>
                    <td><b><?php echo (isset($LANG['COLL_CODE'])?$LANG['COLL_CODE']:'Collection Code'); ?>:</b></td>
                    <td style="padding-right:35px;"><? echo $_GET['collectioncode'] ?></td>
                    <td><b><?php echo (isset($LANG['CAT_NUM'])?$LANG['CAT_NUM']:'Catalog Number'); ?>:</b></td>
                    <td><? echo $_GET['catalognumber'] ?></td>
                </tr>
                <tr>
                    <td><b><?php echo (isset($LANG['INST_CODE'])?$LANG['INST_CODE']:'Institution Code'); ?>:</b></td>
                    <td style="padding-right:35px;"><? echo $_GET['institutioncode'] ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><b><?php echo (isset($LANG['DATE_IDED'])?$LANG['DATE_IDED']:'Date Identified'); ?>:</b></td>
                    <td style="padding-right:35px;"><? echo $annotation->dateIdentified ?></td>
                    <td><b><?php echo (isset($LANG['IDED_BY'])?$LANG['IDED_BY']:'Identified By'); ?>:</b></td>
                    <td><? echo $annotation->identifiedBy ?></td>
                </tr>
            </table>
            <?php

            foreach ($responses as $response) {
                if ($response->describesObject == $annotation->uri) {
                    ?>
                    <ul>
                        <li>
                            <table>
                                <tr>
                                    <td><b><?php echo (isset($LANG['CREATED_BY'])?$LANG['CREATED_BY']:'Created By'); ?>:</b></td>
                                    <td style="padding-right:35px;"><? echo $response->createdBy ?></td>
                                    <td><b><?php echo (isset($LANG['CREATED_ON'])?$LANG['CREATED_BY']:'Created On'); ?></b></td>
                                    <td><? echo $response->date ?></td>
                                </tr>
                                <tr>
                                    <td><b><?php echo (isset($LANG['OPINION'])?$LANG['OPINION']:'Opinion'); ?>:</b></td>
                                    <td style="padding-right:35px;"><? echo $response->opinionText ?></td>
                                    <td><b><?php echo (isset($LANG['POLARITY'])?$LANG['POLARITY']:'Polarity'); ?>:</b></td>
                                    <td><? echo $response->polarity ?></td>
                                </tr>
                            </table>
                        </li>
                    </ul>
                <?php
                }
            }
        }
}
?>