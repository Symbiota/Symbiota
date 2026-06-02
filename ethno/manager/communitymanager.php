<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$ethnoManager = new EthnoProjectManager();
$ethnoManager->setCollid($collid);
$commArr = $ethnoManager->getCommunityArr();
?>
<script src="../../js/symb/shared.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        function split( val ) {
            return val.split( /,\s*/ );
        }

        /*$( "#addCommunityName" )
            .bind( "keydown", function( event ) {
                if ( event.keyCode === $.ui.keyCode.TAB &&
                    $( this ).data( "autocomplete" ).menu.active ) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function( request, response ) {
                    $.getJSON( "rpc/autofillcommunityname.php", {
                        name: request.term
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
                    document.getElementById('addCommID').value = ui.item.id;
                    this.value = terms.join( ", " );
                    return false;
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                        alert("Community must be selected from list. Please add the community first, if it does not exist in database.");
                    }
                }
            },{});*/
    });

    function verifyLinkCommunity(){
        var comID = document.getElementById("addCommID").value;
        if(comID){
            document.getElementById("comformaction").value = 'Link community';
            document.commlistform.submit();
        }
        else{
            alert("Please enter a community name.");
        }
    }

    function verifyRemoveCommunity(f){
        var valid = false;
        for(var i=0;i<f.length;i++){
            if((f.elements[i].name == "cplid[]") && (f.elements[i].checked == true)){
                valid = true;
            }
        }
        if(valid){
            document.getElementById("comformaction").value = 'Remove Community';
            document.commlistform.submit();
        }
        else{
            alert('Please select at least one community to remove.');
        }
    }

    function openCommunityEditor(comid){
        var urlStr = 'editcommunity.php?comid='+comid+'<?php echo '&collid='.$collid; ?>';
        newWindow = window.open(urlStr,'addnewpopup','toolbar=1,status=1,scrollbars=1,width=1350,height=900,left=20,top=20');
        if (newWindow.opener == null) newWindow.opener = self;
    }
</script>
<div style="float:right;">
    <a href="editcommunity.php?collid=<?php echo $collid; ?>" title="Add Community to project">  <img src="../../images/add.png" /></a>
</div>
<form name="commlistform" id="commlistform" action="index.php" method="post" onsubmit="">
    <div>
        <div style="min-height:200px;clear:both">
            <?php
            if($commArr){
                ?>
                <table class="styledtable" style="width:430px;font-family:Arial;font-size:12px;margin-left:auto;margin-right:auto;">
                    <tr>
                        <th style="width:20px;"></th>
                        <th style="width:400px;">Community Name</th>
                    </tr>
                    <?php
                    foreach($commArr as $cplId => $lArr){
                        echo '<tr>';
                        echo '<td style="width:20px;"><input name="cplid[]" type="checkbox" value="'.$cplId.'" /></td>'."\n";
                        //echo '<td style="width:400px;"><a href="#" onclick="openCommunityEditor('.$lArr['comid'].'); return false;">'.$lArr['communityname'].'</a></td>'."\n";
                        echo '<td style="width:200px;"><a href="editcommunity.php?collid='.$collid.'&comid='.$lArr['comid'].'" target="_blank">'.$lArr['communityname'].'</a></td>'."\n";
                        echo '</tr>';
                    }
                    ?>
                </table>
                <div style="margin:15px;float:right;">
                    <button type="button" onclick='verifyRemoveCommunity(this.form);'><b>Remove Selected</b></button>
                </div>
                <div style="clear:both;width:100%;height:5px;"></div>
                <?php
            }
            else{
                echo '<div style="margin-top:10px;font-weight:bold;font-size:120%;">There are no communities added to this project.</div>';
            }
            ?>
        </div>
    </div>
    <input id="comformaction" name="action" type="hidden" value="" />
    <input type="hidden" name="tabindex" value="1" />
    <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
</form>
