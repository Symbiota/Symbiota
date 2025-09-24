<?php global $LANG;
/*
------------------
Language: Français (French)
------------------
*/

$LANG['BATCH_DUPLICATE_HARVESTER'] = 'Moissonneuse de Données en Double';
$LANG['MUST_BATCH_LINK_DUPLICATES'] = 'Pour que cet outil puisse trouver les doublons de géoréférences, vos spécimens doivent déjà 
    être liés en tant que doublons à d\'autres spécimens du portail. Il est recommandé d\'exécuter la procédure «Lier les doublons 
    de spécimens par lots» dans les Outils de Regroupement des Doublons avant d\'utiliser la moissonneuse de données en double.';
$LANG['DUPLICATE_SEARCH_CRITERIA'] = 'Critères de recherche des doublons';
$LANG['MISSING_LAT_LNG'] = 'Afficher uniquement les spécimens de ma collection sans latitude ni longitude';
$LANG['HIDE_EXACT_MATCHES'] = 'Afficher uniquement les doublons dont les géoréférences sont différentes de celles du spécimen cible';
$LANG['NO_DUPLICATES'] = 'Aucun cluster en double ne correspond à cette recherche.';
$LANG['FILTER_COLLECTIONS'] = 'Filtrer les collections.';
$LANG['COPY_DUPLICATE_DATA'] = 'Copier les données en double.';
$LANG['COPY_DUPLICATE_DATA_EXPLANATION'] = 'Cliquer sur le bouton ci-dessus remplacera les données de géoréférencement de l\'enregistrement cible (gris foncé) par celles de l\'enregistrement en double vérifié. Les champs suivants seront remplacés: decimalLatitude, decimalLongitude, geodeticDatum, footprintWKT, coordinateUncertaintyInMeters, georeferencedBy, georeferenceRemarks, georeferenceSources, georeferenceProtocol, georeferenceVerificationStatus.';
$LANG['ENABLE_AUTO_CHECK'] = 'Sélectionner automatiquement les doublons avec une seule option (Remarque: les doublons de votre collection ne seront pas sélectionnés automatiquement)';
$LANG['SEARCH_TO_SEE_DUPLICATES'] = 'Saisir des valeurs de recherche pour afficher les doublons potentiels';

?>
