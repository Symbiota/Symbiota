<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$ethnoManager = new EthnoProjectManager();
$ethnoManager->setCollid($collid);
$personnelArr = $ethnoManager->getPersonnelArr();
$roleArr = $ethnoManager->getPersonnelRoleArr();
?>
<script src="../../js/symb/shared.js" type="text/javascript"></script>
<script>
    function openPersonnelEditor(cplid,perid){
        var urlStr = 'editpersonnel.php?cplid='+cplid+'&perid='+perid+'<?php echo '&collid='.$collid; ?>';
        newWindow = window.open(urlStr,'addnewpopup','toolbar=1,status=1,scrollbars=1,width=1350,height=900,left=20,top=20');
        if (newWindow.opener == null) newWindow.opener = self;
    }

    function verifyRemovePersonnel(f){
        var valid = false;
        for(var i=0;i<f.length;i++){
            if((f.elements[i].name == "cplid[]") && (f.elements[i].checked == true)){
                valid = true;
            }
        }
        if(valid){
            document.getElementById("performaction").value = 'Remove Personnel';
            document.perlistform.submit();
        }
        else{
            alert('Please select at least one person to remove.');
        }
    }
</script>
<div style="float:right;">
    <a href="editpersonnel.php?collid=<?php echo $collid; ?>" title="Add Personnel to project">  <img src="../../images/add.png" /></a>
</div>
<div>
    <div style="min-height:200px;clear:both">
        <form name="perlistform" id="perlistform" action="index.php" method="post" onsubmit="">
            <?php
            if($personnelArr){
                ?>
                <table class="styledtable" style="width:770px;font-family:Arial;font-size:12px;margin-left:auto;margin-right:auto;">
                    <tr>
                        <th style="width:20px;"></th>
                        <th style="width:200px;">Full Name</th>
                        <th style="width:150px;">Birth Community</th>
                        <th style="width:40px;">Project Code</th>
                        <th style="width:350px;">Roles</th>
                    </tr>
                <?php
                foreach($personnelArr as $cplId => $pArr){
                    $roles = $pArr['rolearr'];
                    $rolesStrArr = array();
                    $rolesStr = '';
                    if(is_array($roles)){
                        foreach($roleArr as $id => $role){
                            if(in_array($id,$roles)){
                                $rolesStrArr[] = $role;
                            }
                        }
                        $rolesStr = implode(", ",$rolesStrArr);
                        if(isset($pArr['rolecomments'])) $rolesStr .= ': '.$pArr['rolecomments'];
                    }
                    echo '<tr>';
                    echo '<td style="width:20px;"><input name="cplid[]" type="checkbox" value="'.$cplId.'" /></td>'."\n";
                    //echo '<td style="width:200px;"><a href="#" onclick="openPersonnelEditor('.$cplId.','.$pArr['pid'].'); return false;">'.$pArr['name'].'</a></td>'."\n";
                    echo '<td style="width:200px;"><a href="editpersonnel.php?cplid='.$cplId.'&collid='.$collid.'&perid='.$pArr['pid'].'">'.$pArr['name'].'</a></td>'."\n";
                    echo '<td style="width:150px;">'.$pArr['birthCommunity'].'</td>'."\n";
                    echo '<td style="width:40px;">'.$pArr['code'].'</td>'."\n";
                    echo '<td style="width:350px;">'.$rolesStr.'</td>'."\n";
                    echo '</tr>';
                }
                ?>
                </table>
                <div style="margin:15px;float:right;">
                    <button type="button" onclick='verifyRemovePersonnel(this.form);'><b>Remove Selected</b></button>
                </div>
                <div style="clear:both;width:100%;height:5px;"></div>
                <?php
            }
            else{
                echo '<div style="margin-top:10px;font-weight:bold;font-size:120%;">There are no personnel added to this project.</div>';
            }
            ?>
            <input id="performaction" name="action" type="hidden" value="" />
            <input type="hidden" name="tabindex" value="0" />
            <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
        </form>
    </div>
</div>
