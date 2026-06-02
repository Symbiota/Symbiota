<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
include_once($SERVER_ROOT.'/classes/ReferenceManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$ethnoManager = new EthnoProjectManager();
$refManager = new ReferenceManager();
$ethnoManager->setCollid($collid);
$referenceArr = $ethnoManager->getReferenceArr();
?>
<script src="../../js/symb/shared.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        function split( val ) {
            return val.split( /,\s*/ );
        }

        $( "#linkReferenceTitle" )
            .bind( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                    $( this ).data( "autocomplete" ).menu.active ) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function( request, response ) {
                    $.getJSON( "rpc/autofillreferencetitle.php", {
                        title: request.term
                    }, response );
                },
                search: function() {
                    var term = this.value;
                    if ( term.length < 4 ) {
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
                    document.getElementById('linkReferenceID').value = ui.item.id;
                    this.value = terms.join( ", " );
                    return false;
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                        document.getElementById('linkReferenceID').value = "";
                        alert("Reference must be selected from list. If the reference is not listed, please create a new reference record in the box below..");
                    }
                }
            },{});

        $( "#linkReferenceAuthor" )
            .bind( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                    $( this ).data( "autocomplete" ).menu.active ) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function( request, response ) {
                    $.getJSON( "rpc/autofillreferenceauthor.php", {
                        author: request.term
                    }, response );
                },
                search: function() {
                    var term = this.value;
                    if ( term.length < 4 ) {
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
                    document.getElementById('linkReferenceID').value = ui.item.id;
                    this.value = terms.join( ", " );
                    return false;
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                        document.getElementById('linkReferenceID').value = "";
                        alert("Reference must be selected from list. If the reference is not listed, please create a new reference record in the box below..");
                    }
                }
            },{});
    });

    function verifyRemoveReference(f){
        var valid = false;
        for(var i=0;i<f.length;i++){
            if((f.elements[i].name == "crlid[]") && (f.elements[i].checked == true)){
                valid = true;
            }
        }
        if(valid){
            document.getElementById("performaction").value = 'Remove Reference';
            document.reflistform.submit();
        }
        else{
            alert('Please select at least one reference to remove.');
        }
    }

    function verifyNewRefForm(f){
        if(document.getElementById("newreftitle").value === ""){
            alert("Please enter the title of the reference.");
            return false;
        }
        if(document.getElementById("newreftype").selectedIndex < 2){
            alert("Please select the type of reference.");
            return false;
        }
        return true;
    }

    function verifyLinkReference(){
        var refID = document.getElementById("linkReferenceID").value;
        if(refID){
            document.getElementById("linkrefformaction").value = 'Link Reference';
            document.linkrefform.submit();
        }
        else{
            alert("Please enter a reference title.");
        }
    }
</script>
<div style="float:right;">
    <a href="#" title="Add Reference to project" onclick="toggle('addReferenceDiv');">  <img src="../../images/add.png" /></a>
</div>
<div id="addReferenceDiv" style="display:none;">
    <form name="linkrefform" id="linkrefform" action="index.php" method="post" onsubmit="">
        <fieldset style="padding:10px;">
            <legend><b>Link Reference</b></legend>
            <div style="clear:both;padding-top:4px;float:left;">
                <div style="">
                    <b>Reference Title: </b>
                </div>
                <div style="margin-left:145px;margin-top:-14px;">
                    <textarea name="linkReferenceTitle" id="linkReferenceTitle" rows="10" style="width:380px;height:40px;resize:vertical;" ></textarea>
                </div>
            </div>
            <div style="clear:both;padding-top:4px;float:left;">
                <div style="">
                    <b>Reference Author: </b>
                </div>
                <div style="margin-left:150px;margin-top:-14px;">
                    <input style="width:500px;" type="text" id="linkReferenceAuthor" name="linkReferenceAuthor" value="" title="" />
                </div>
            </div>
            <div style="clear:both;float:right;">
                <button type="button" onclick="verifyLinkReference();"><b>Link Reference</b></button>
            </div>
        </fieldset>
        <input id="linkrefformaction" name="action" type="hidden" value="" />
        <input id="linkReferenceID" name="linkReferenceID" type="hidden" value="" />
        <input type="hidden" name="tabindex" value="2" />
        <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
    </form>
    <form name="addrefform" id="addrefform" action="index.php" method="post" onsubmit="return verifyNewRefForm(this.form);">
        <fieldset style="padding:10px;">
            <legend><b>Add New Reference</b></legend>
            <div style="clear:both;padding-top:4px;float:left;">
                <div style="">
                    <b>Title: </b>
                </div>
                <div style="margin-left:50px;margin-top:-14px;">
                    <textarea name="newreftitle" id="newreftitle" rows="10" style="width:380px;height:40px;resize:vertical;" ></textarea>
                </div>
            </div>
            <div style="clear:both;padding-top:6px;float:left;">
                <b>Reference Type: </b>
                <select name="newreftype" id="newreftype" style="width:400px;">
                    <option value="">Select Reference Type</option>
                    <option value="">------------------------------------------</option>
                    <?php
                    $typeArr = $refManager->getRefTypeArr();
                    foreach($typeArr as $k => $v){
                        echo '<option value="'.$k.'">'.$v.'</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="clear:both;float:right;">
                <button type="submit"><b>Create Reference</b></button>
            </div>
        </fieldset>
        <input name="action" type="hidden" value="Create Reference" />
        <input name="ispublished" type="hidden" value="1" />
        <input type="hidden" name="tabindex" value="2" />
        <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
    </form>
</div>
<div>
    <div style="min-height:200px;clear:both">
        <form name="reflistform" id="reflistform" action="index.php" method="post" onsubmit="">
            <?php
            if($referenceArr){
                ?>
                <table class="styledtable" style="width:430px;font-family:Arial;font-size:12px;margin-left:auto;margin-right:auto;">
                    <tr>
                        <th style="width:20px;"></th>
                        <th style="width:550px;">Reference Title</th>
                    </tr>
                    <?php
                    foreach($referenceArr as $crlId => $lArr){
                        echo '<tr>';
                        echo '<td style="width:20px;"><input name="crlid[]" type="checkbox" value="'.$crlId.'" /></td>'."\n";
                        echo '<td style="width:550px;"><a href="../../references/refdetails.php?refid='.$lArr['refid'].'" target="_blank">'.$lArr['title'].'</a></td>'."\n";
                        echo '</tr>';
                    }
                    ?>
                </table>
                <div style="margin:15px;float:right;">
                    <button type="button" onclick='verifyRemoveReference(this.form);'><b>Remove Selected</b></button>
                </div>
                <div style="clear:both;width:100%;height:5px;"></div>
                <?php
            }
            else{
                echo '<div style="margin-top:10px;font-weight:bold;font-size:120%;">There are no references added to this project.</div>';
            }
            ?>
            <input id="performaction" name="action" type="hidden" value="" />
            <input type="hidden" name="tabindex" value="0" />
            <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
        </form>
    </div>
</div>
