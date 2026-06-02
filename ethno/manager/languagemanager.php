<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$ethnoManager = new EthnoProjectManager();
$ethnoManager->setCollid($collid);
$langArr = $ethnoManager->getLanguageArr();
?>
<script src="../../js/symb/shared.js" type="text/javascript"></script>
<script>
    $(function() {
        var dialogArr = new Array("langname","fileurl","filetopic");
        var dialogStr = "";
        for(i=0;i<dialogArr.length;i++){
            dialogStr = dialogArr[i]+"info";
            $( "#"+dialogStr+"dialog" ).dialog({
                autoOpen: false,
                modal: true,
                position: { my: "left top", at: "right bottom", of: "#"+dialogStr }
            });

            $( "#"+dialogStr ).click(function() {
                $( "#"+this.id+"dialog" ).dialog( "open" );
            });
        }

    });

    $(document).ready(function() {
        function split( val ) {
            return val.split( /,\s*/ );
        }

        $( "#addLanguageName" )
            .bind( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                    $( this ).data( "autocomplete" ).menu.active ) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function( request, response ) {
                    $.getJSON( "rpc/autofilllanguagename.php", {
                        name: request.term
                    }, response );
                },
                search: function() {
                    var term = this.value;
                    if ( term.length < 3 ) {
                        return false;
                    }
                },
                focus: function() {
                    return false;
                },
                select: function( event, ui ) {
                    var terms = split( this.value );
                    terms.pop();
                    terms.push( ui.item.value );
                    document.getElementById('addLanguageID').value = ui.item.id;
                    this.value = terms.join( ", " );
                    return false;
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                        alert("Language must be selected from list.");
                    }
                }
            },{});
    });

    function verifyLinkLanguage(){
        var langID = document.getElementById("addLanguageID").value;
        if(langID){
            document.getElementById("langformaction").value = 'Link language';
            document.langlistform.submit();
        }
        else{
            alert("Please enter a language name.");
        }
    }

    function verifyRemoveLanguage(f){
        var valid = false;
        for(var i=0;i<f.length;i++){
            if((f.elements[i].name == "cplid[]") && (f.elements[i].checked == true)){
                valid = true;
            }
        }
        if(valid){
            document.getElementById("langformaction").value = 'Remove Language';
            document.langlistform.submit();
        }
        else{
            alert('Please select at least one language to remove.');
        }
    }

    function openCommunityEditor(comid){
        var urlStr = 'editcommunity.php?comid='+comid;
        newWindow = window.open(urlStr,'addnewpopup','toolbar=1,status=1,scrollbars=1,width=1250,height=900,left=20,top=20');
        if (newWindow.opener == null) newWindow.opener = self;
    }
</script>
<div style="float:right;">
    <a href="editlanguage.php?collid=<?php echo $collid; ?>" title="Add Language to project">  <img src="../../images/add.png" /></a>
</div>
<form name="langlistform" id="langlistform" action="index.php" method="post" onsubmit="">
    <div id="addLanguageDiv" style="display:<?php echo ($langArr?'none':'block'); ?>;width:1000px;margin-left:auto;margin-right:auto;">
        <fieldset style="padding:10px;">
            <legend><b>Add Language</b></legend>
            <table style="width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td style="width:200px;font-size:14px;">
                        Language name:
                        <a id="langnameinfo" href="#" onclick="return false" title="More information about Institution Code">
                            <img src="../../images/qmark_big.png" style="width:15px;" />
                        </a>
                    </td>
                    <td style="width:500px;">
                        <input type="text" id="addLanguageName" name="addLanguageName" size="43" value="" title="" />
                        <div id="langnameinfodialog">

                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width:200px;font-size:14px;">
                        File URL:
                        <a id="fileurlinfo" href="#" onclick="return false" title="More information about Institution Code">
                            <img src="../../images/qmark_big.png" style="width:15px;" />
                        </a>
                    </td>
                    <td style="width:500px;">
                        <input type="text" id="addLanguageFileUrl" name="addLanguageFileUrl" size="60" value="" title="" />
                        <div id="fileurlinfodialog">

                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width:200px;font-size:14px;">
                        File Topic:
                        <a id="filetopicinfo" href="#" onclick="return false" title="More information about Institution Code">
                            <img src="../../images/qmark_big.png" style="width:15px;" />
                        </a>
                    </td>
                    <td style="width:500px;">
                        <select id="addLanguageFileTopic" name="addLanguageFileTopic" onchange="">
                            <option value='' >--------</option>
                            <option value='Orthography'>Orthography</option>
                            <option value='Contact languages'>Contact languages</option>
                            <option value='Language history and phylogeny'>Language history and phylogeny</option>
                            <option value='Language divisions'>Language divisions</option>
                            <option value='Phonetics and phonology'>Phonetics and phonology</option>
                            <option value='Morphology'>Morphology</option>
                            <option value='Syntax'>Syntax</option>
                            <option value='Other'>Other</option>
                        </select>
                        <div id="filetopicinfodialog">

                        </div>
                    </td>
                </tr>
            </table>
            <div style="clear:both;float:right;">
                <button type="button" onclick="verifyLinkLanguage();"><b>Add language</b></button>
            </div>
        </fieldset>
    </div>

    <div id="languageListDiv">
        <div style="min-height:200px;clear:both">
            <?php
            if($langArr){
                ?>
                <table class="styledtable" style="width:850px;font-family:Arial;font-size:12px;margin-left:auto;margin-right:auto;">
                    <tr>
                        <th style="width:20px;"></th>
                        <th style="width:40px;">Language Name</th>
                        <th style="width:40px;">Glottocode</th>
                        <th style="width:40px;">ISO 639-3</th>
                        <th style="width:500px;">File</th>
                        <th style="width:200px;">File topic</th>
                    </tr>
                    <?php
                    foreach($langArr as $cplId => $lArr){
                        echo '<tr>';
                        echo '<td style="width:20px;"><input name="cplid[]" type="checkbox" value="'.$cplId.'" /></td>'."\n";
                        echo '<td style="width:40px;"><a href="http://glottolog.org/resource/languoid/id/'.$lArr['glottocode'].'" target="_blank">'.$lArr['langname'].'</a></td>'."\n";
                        echo '<td style="width:40px;">'.$lArr['glottocode'].'</td>'."\n";
                        echo '<td style="width:40px;">'.$lArr['isocode'].'</td>'."\n";
                        echo '<td style="width:500px;"><a href="'.$lArr['fileurl'].'" target="_blank">'.$lArr['fileurl'].'</a></td>'."\n";
                        echo '<td style="width:200px;">'.$lArr['filetopic'].'</td>'."\n";
                        echo '</tr>';
                    }
                    ?>
                </table>
                <div style="margin:15px;float:right;">
                    <button type="button" onclick='verifyRemoveLanguage(this.form);'><b>Remove Selected</b></button>
                </div>
                <div style="clear:both;width:100%;height:5px;"></div>
                <?php
            }
            else{
                echo '<div style="margin-top:10px;font-weight:bold;font-size:120%;">There are no languages added to this project.</div>';
            }
            ?>
        </div>
    </div>
    <input id="langformaction" name="action" type="hidden" value="" />
    <input id="addLanguageID" name="addLanguageID" type="hidden" value="" />
    <input type="hidden" name="tabindex" value="2" />
    <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
</form>
