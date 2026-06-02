<table id="maintable" cellspacing="0">
    <tr style="width:100%;">
        <td class="header" colspan="3">
            <div style="clear:both;width:100%;background-color:black;">
                <div style="clear:both;width:967px;margin-right:auto;margin-left:auto;">
                    <img style="" src="<?php echo $clientRoot; ?>/images/layout/demcaheader.jpg" />
                </div>
            </div>
            <div id="top_navbar">
                <div id="right_navbarlinks">
                    <?php
                    if($userDisplayName){
                        ?>
                        <span style="">
							Welcome <?php echo $userDisplayName; ?>!
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $clientRoot; ?>/profile/viewprofile.php">My Profile</a>
						</span>
                        <span style="margin-left:5px;">
							<a href="<?php echo $clientRoot; ?>/profile/index.php?submit=logout">Logout</a>
						</span>
                        <?php
                    }
                    else{
                        ?>
                        <span style="">
							<a href="<?php echo $clientRoot."/profile/index.php?refurl=".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>">
								Log in
							</a>
						</span>
                        <?php
                    }
                    ?>
                    <span style="margin-left:5px;margin-right:5px;">
						<a href='<?php echo $clientRoot; ?>/sitemap.php'>Sitemap</a>
					</span>

                </div>
                <ul id="hor_dropdown">
                    <li>
                        <a href="<?php echo $clientRoot; ?>/index.php" >Home</a>
                    </li>
                    <li>
                        <a href="<?php echo $clientRoot; ?>/collections/index.php" >Search</a>
                        <ul>
                            <li>
                                <a href="<?php echo $clientRoot; ?>/collections/index.php" >Search collections</a>
                            </li>
                            <li>
                                <a href="<?php echo $clientRoot; ?>/collections/map/mapinterface.php" target="_blank">Map search</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo $clientRoot; ?>/ethno/eaf/index.php" >Multimedia</a>
                    </li>
                    <li>
                        <a href="#" >Images</a>
                        <ul>
                            <li>
                                <a href="<?php echo $clientRoot; ?>/imagelib/index.php" >Browse images</a>
                            </li>
                            <li>
                                <a href="<?php echo $clientRoot; ?>/imagelib/search.php" >Search images</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo $clientRoot; ?>/projects/index.php" >Checklists</a>
                        <ul>
                            <li>
                                <a href="<?php echo $clientRoot; ?>/checklists/checklist.php?cl=1" >Flora of Sierra Nororiental de Puebla</a>
                            </li>

                        </ul>
                    </li>
                    <li>
                        <a href="#" >Tools</a>
                        <ul>
                            <li>
                                <a href="<?php echo $clientRoot; ?>/checklists/dynamicmap.php?interface=checklist" >Dynamic checklist</a>
                            </li>
                            <li>
                                <a href="<?php echo $clientRoot; ?>/checklists/dynamicmap.php?interface=key" >Dynamic key</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo $clientRoot; ?>/userguide.php" >User guide</a>
                    </li>
                    <li>
                        <a href="<?php echo $clientRoot; ?>/resources.php" >Resources</a>
                    </li>
                    <li>
                        <a href="<?php echo $clientRoot; ?>/acknowledgements.php" >Acknowledgements</a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    <tr style="width:100%;">
        <td class='middlecenter'  colspan="3">
