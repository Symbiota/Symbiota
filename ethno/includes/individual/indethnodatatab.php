<div id="ethnodatatab">
	<?php
	foreach($ethnoDataArr as $dataId => $dataArr){
		$dataPersonelStr = '';
		$dataSemanticsStr = '';
		$dataTypologyStr = '';
		$dataUseStr = '';
		$dataPartsStr = '';
		$dataPersonelArr = $dataArr["personnelArr"];
		$dataSemanticsArr = $dataArr["semanticTags"];
		$dataUseArr = $dataArr["useTags"];
		$dataPartsArr = $dataArr["partsTags"];
		foreach($ethnoPersonnelArr as $id => $pArr){
			if(in_array($id,$dataPersonelArr)){
				$dataPersonelStr .= $pArr['title'].' '.$pArr['firstname'].' '.$pArr['lastname'].'; ';
			}
		}
		if($dataPersonelStr) $dataPersonelStr = substr($dataPersonelStr,0,-2);
		foreach($ethnoNameSemanticTagArr as $id => $stArr){
			if(in_array($id,$dataSemanticsArr)){
				$dataSemanticsStr .= $stArr['ptag'].'; ';
			}
			unset($stArr['ptag']);
			unset($stArr['pdesc']);
			if($stArr){
				foreach($stArr as $cid => $cArr){
					if(in_array($cid,$dataSemanticsArr)){
						$dataSemanticsStr .= $cArr['ctag'].'; ';
					}
				}
			}
		}
		if($dataSemanticsStr) $dataSemanticsStr = substr($dataSemanticsStr,0,-2);
		foreach($ethnoUsePartsUsedTagArr as $id => $text){
			if(in_array($id,$dataPartsArr)){
				$dataPartsStr .= $text.'; ';
			}
		}
		if($dataPartsStr) $dataPartsStr = substr($dataPartsStr,0,-2);
		foreach($ethnoUseUseTagArr as $id => $uArr){
			$tempStr = '';
			$header = $uArr['header'];
			unset($uArr['header']);
			foreach($uArr as $uid => $text){
				if(in_array($uid,$dataUseArr)){
					if(!$tempStr) $tempStr = '<b>'.$header.':</b> ';
					$tempStr .= $text.'; ';
				}
			}
			if($tempStr) $dataUseStr .= $tempStr;
		}
		if($dataUseStr) $dataUseStr = substr($dataUseStr,0,-2);
		if($dataArr["typology"]==='opaque') $dataTypologyStr = 'Opaque';
		elseif($dataArr["typology"]==='transparent') $dataTypologyStr = 'Transparent';
		elseif($dataArr["typology"]==='modifiedopaque') $dataTypologyStr = 'Modified opaque';
		elseif($dataArr["typology"]==='modifiedtransparent') $dataTypologyStr = 'Modified transparent';
		?>
		<div style="margin-top:10px">
			<?php
			if($dataArr["reftitle"]){
				?>
				<div style="font-size:13px;">
					<b>Reference title:</b>
					<?php echo $dataArr["reftitle"]; ?>
				</div>
				<?php
			}
			if($dataArr["refpages"]){
				?>
				<div style="font-size:13px;">
					<b>Reference pages:</b>
					<?php echo $dataArr["refpages"]; ?>
				</div>
				<?php
			}
			if($dataArr["dataeventstr"]){
				?>
				<div style="font-size:13px;">
					<b>Elicitation event:</b>
					<?php echo $dataArr["dataeventstr"]; ?>
				</div>
				<?php
			}
			if($dataPersonelStr){
				?>
				<div style="font-size:13px;">
					<b>Consultants:</b>
					<?php echo $dataPersonelStr; ?>
				</div>
				<?php
			}
			if($dataArr["verbatimVernacularName"]){
				?>
				<div style="font-size:13px;">
					<b>Verbatim vernacular name:</b>
					<?php echo $dataArr["verbatimVernacularName"]; ?>
				</div>
				<?php
			}
			if($dataArr["annotatedVernacularName"]){
				?>
				<div style="font-size:13px;">
					<b>Annotated vernacular name:</b>
					<?php echo $dataArr["annotatedVernacularName"]; ?>
				</div>
				<?php
			}
			if($dataSemanticsStr){
				?>
				<div style="font-size:13px;">
					<b>Semantic tags:</b>
					<?php echo $dataSemanticsStr; ?>
				</div>
				<?php
			}
			if($dataArr["verbatimLanguage"]){
				?>
				<div style="font-size:13px;">
					<b>Verbatim language:</b>
					<?php echo $dataArr["verbatimLanguage"]; ?>
				</div>
				<?php
			}
			if($dataArr["langName"]){
				?>
				<div style="font-size:13px;">
					<b>Glottolog language:</b>
					<?php echo $dataArr["langName"]; ?>
				</div>
				<?php
			}
			if($dataArr["otherVerbatimVernacularName"]){
				?>
				<div style="font-size:13px;">
					<b>Other verbatim vernacular name:</b>
					<?php echo $dataArr["otherVerbatimVernacularName"]; ?>
				</div>
				<?php
			}
			if($dataArr["otherLangName"]){
				?>
				<div style="font-size:13px;">
					<b>Other verbatim vernacular name Glottolog language:</b>
					<?php echo $dataArr["otherLangName"]; ?>
				</div>
				<?php
			}
			if($dataArr["verbatimParse"]){
				?>
				<div style="font-size:13px;">
					<b>Verbatim parse:</b>
					<?php echo $dataArr["verbatimParse"]; ?>
				</div>
				<?php
			}
			if($dataArr["annotatedParse"]){
				?>
				<div style="font-size:13px;">
					<b>Annotated parse:</b>
					<?php echo $dataArr["annotatedParse"]; ?>
				</div>
				<?php
			}
			if($dataArr["verbatimGloss"]){
				?>
				<div style="font-size:13px;">
					<b>Verbatim gloss:</b>
					<?php echo $dataArr["verbatimGloss"]; ?>
				</div>
				<?php
			}
			if($dataArr["annotatedGloss"]){
				?>
				<div style="font-size:13px;">
					<b>Annotated gloss:</b>
					<?php echo $dataArr["annotatedGloss"]; ?>
				</div>
				<?php
			}
			if($dataTypologyStr){
				?>
				<div style="font-size:13px;">
					<b>Typology:</b>
					<?php echo $dataTypologyStr; ?>
				</div>
				<?php
			}
			if($dataArr["translation"]){
				?>
				<div style="font-size:13px;">
					<b>Translation:</b>
					<?php echo $dataArr["translation"]; ?>
				</div>
				<?php
			}
			if($dataArr["taxonomicDescription"]){
				?>
				<div style="font-size:13px;">
					<b>Taxonomic description:</b>
					<?php echo $dataArr["taxonomicDescription"]; ?>
				</div>
				<?php
			}
			if($dataArr["nameDiscussion"]){
				?>
				<div style="font-size:13px;">
					<b>Consultant comments on name:</b>
					<?php echo $dataArr["nameDiscussion"]; ?>
				</div>
				<?php
			}
			if($dataPartsStr){
				?>
				<div style="font-size:13px;">
					<b>Parts used:</b>
					<?php echo $dataPartsStr; ?>
				</div>
				<?php
			}
			if($dataUseStr){
				?>
				<div style="font-size:13px;">
					<b>Uses:</b>
					<?php echo $dataUseStr; ?>
				</div>
				<?php
			}
			if($dataArr["consultantComments"]){
				?>
				<div style="font-size:13px;">
					<b>Consultant comments:</b>
					<?php echo $dataArr["consultantComments"]; ?>
				</div>
				<?php
			}
			if($dataArr["useDiscussion"]){
				?>
				<div style="font-size:13px;">
					<b>Consultant comments on use:</b>
					<?php echo $dataArr["useDiscussion"]; ?>
				</div>
				<?php
			}
			?>
		</div>
		<hr/>
		<?php
	}
	?>
</div>
