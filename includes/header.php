<?php
include_once($SERVER_ROOT.'/content/lang/header.'.$LANG_TAG.'.php');
?>
<script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/js/symb/base.js?ver=171023"></script>
<script type="text/javascript">
        //Uncomment following line to support toggling of database content containing DIVs with lang classes in form of: <div class="lang en">Content in English</div><div class="lang es">Conten$
        setLanguageDiv();
</script>
<table id="maintable" cellspacing="0">
        <tr>
            	<td id="header" colspan="3">
                        <div style="margin-top:5px">
                            
                                <div id="right_navbarlinks">
                                        <?php
                                        if($USER_DISPLAY_NAME){
                                                ?>
                                                <span style="">
                                                        <?php echo (isset($LANG['H_WELCOME'])?$LANG['H_WELCOME']:'Welcome').' '.$USER_DISPLAY_NAME; ?>!
                                                </span>
                                                <span style="margin-left:5px;">
                                                        <a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php"><?php echo (isset($LANG['H_MY_PROFILE'])?$LANG['H_MY_PROFILE']:'My Profile')?></a>
                                                </span>
                                                <span style="margin-left:5px;">
                                                        <a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout"><?php echo (isset($LANG['H_LOGOUT'])?$LANG['H_LOGOUT']:'Logout')?></a>
                                                </span>
                                                <?php
                                        }
                                        else{
                                             	?>
                                                <span style="">
                                                        <a href="<?php echo $CLIENT_ROOT."/profile/index.php?refurl=".$_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING']; ?>"><?php echo (isset($LANG['H_LOGIN'])?$LANG['H_LOGIN']:'Login')?></a>
                                                </span>
                                                <span style="margin-left:5px;">
                                                        <a href="<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php"><?php echo (isset($LANG['H_NEW_ACCOUNT'])?$LANG['H_NEW_ACCOUNT']:'New Accoun')?></a>
                                                </span>
                                                <?php
                                        }
                                        ?>
                                        <span style="margin-left:5px;">
                                        	<a href='<?php echo $CLIENT_ROOT; ?>/sitemap.php'><?php echo (isset($LANG['H_SITEMAP'])?$LANG['H_SITEMAP']:'Sitemap'); ?></a>
                                        </span>
                                        <span style="margin-left:5px;margin-right:5px;">
                                                <select onchange="setLanguage(this)">
                                                        <option value="en">English</option>
                                                        <option value="es" <?php echo ($LANG_TAG=='es'?'SELECTED':''); ?>>Espa&ntilde;ol</option>
                                                        <option value="pt" <?php echo ($LANG_TAG=='pt'?'SELECTED':''); ?>>Português do Brasil</option>
                                                        <option value="ko" <?php echo ($LANG_TAG=='ko'?'SELECTED':''); ?>>Korean</option>
                                                </select>
                                                <?php
                                                if($IS_ADMIN){
                                                        echo '<a href="'.$CLIENT_ROOT.'/content/lang/admin/langmanager.php?refurl='.$_SERVER['SCRIPT_NAME'].'"><img src="'.$CLIENT_ROOT.'/images/edit.png" style="width:12px" /></a>';
                                                }
                                                ?>
                                        </span>
                                </div>
                                
                                
                        <div style="clear:both;">
							<img src="/ecdysis/images/ecdysis.png" style="max-height:130px;max-width:95%" />
						</div>
						<div style="font-size:24; color:#ff7417;">
							<?php echo (isset($LANG['H_SUBTITLE'])?$LANG['H_SUBTITLE']:'A portal for live-data arthropod collections')?>
						</div> 
                                
                                
                                

 						<div id="top_navbar">
                                <ul id="hor_dropdown">
                                        <li>
                                            	<a href="<?php echo $CLIENT_ROOT; ?>/index.php" ><?php echo (isset($LANG['H_HOME'])?$LANG['H_HOME']:'Home'); ?></a>
                                        </li>
                                        <li>
                                            	<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php"><?php echo (isset($LANG['H_SEARCH'])?$LANG['H_SEARCH']:'Search'); ?></a>

                                        </li>
                                        <li>
                                                <a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank"><?php echo (isset($LANG['H_MAP'])?$LANG['H_MAP']:'Map Search'); ?></a>
                                        </li>
					<!--
                                        <li>
                                                <a href="<?php echo $CLIENT_ROOT; ?>/imagelib/index.php" ><?php echo (isset($LANG['H_IMAGE_BROWSER'])?$LANG['H_IMAGE_BROWSER']:'Image Browser'); ?></a>
                                        </li>
					-->
                                        <li>
						<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/search.php" ><?php echo (isset($LANG['H_IMAGE_SEARCH'])?$LANG['H_IMAGE_SEARCH']:'Search Images'); ?></a>
                                        </li>
                                        <li>

                                        </li>
                                </ul>
                        </div>
			<div style="clear:both;height:1px;">
				</div>
                </td>
        </tr>
	<tr>
            	<td id='middlecenter'  colspan="3">

