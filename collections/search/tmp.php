if($matSampleTypeArr = $collManager->getMaterialSampleTypeArr()){
								?>
								<div class="select-container">
									<label for="materialsampletype"><?= $LANG['MATERIAL_SAMPLE_TYPE'] ?></label>
									<select name="materialsampletype" id="materialsampletype">
										<option value="">---------------</option>
										<option value="all-ms"><?= $LANG['ALL_MATERIAL_SAMPLE'] ?></option>
										<?php
										foreach($matSampleTypeArr as $matSampeType){
											echo '<option value="' . $matSampeType . '">' . $matSampeType . '</option>';
										}
										?>
									</select>
								</div>
								<?php
							}