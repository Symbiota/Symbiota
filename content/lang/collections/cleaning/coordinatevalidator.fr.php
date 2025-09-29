<?php
/*
------------------
Language: Français (French)
------------------
*/

$LANG['HOME'] = 'Accueil';
$LANG['COLLECTION_MANAGEMENT'] = 'Gestion des Collections';

$LANG['COOR_VALIDATOR'] = 'Validateur de Coordonnées';
$LANG['RECOMMEND_USE_GEOGRAPHIC_CLEANER'] = "<b>*Remarque*</b>: Il est recommandé d'utiliser les outils de nettoyage 
    géographique avant de valider les coordonnées.
    Cela garantira que vos unités politiques correspondent à celles du thésaurus géographique.";
$LANG['TOOL_DESCRIPTION'] = 'Cliquer sur le bouton «Valider toutes les coordonnées» parcourra tous les enregistrements géoréférencés non validés afin de 
    vérifier que les coordonnées correspondent bien aux unités géographiques définies, telles que définies par les polygones géographiques stockés dans le 
    thésaurus géographique. Cliquez sur le numéro dans la colonne «Enregistrements douteux» (disponible après validation) pour afficher les 
    enregistrements présentant le problème. Pour plus d\'informations sur cet outil, consultez la page 
    <a href="https://docs.symbiota.org/Collection_Manager_Guide/Data_Cleaning/coordinate_validator/" target="_blank">Documentation Symbiota</a>.';
$LANG['COORDINATES_OUTSIDE_COUNTY_LIMITS'] = "Les coordonnées se situent en dehors des limites de l'unité géographique";
$LANG['WRONG_COUNTY_ENTERED'] = "Une unité géographique incorrecte a été saisie";
$LANG['COUNTY_MISSPELLED'] = "L'unité géographique est mal orthographiée";
$LANG['VALIDATION_COUNT_LIMIT'] = "La validation des coordonnées est limitée à 50.000 enregistrements à la fois, mais peut être exécutée plusieurs fois.";
$LANG['LAST_VER_DATE'] = "Date de la dernière vérification";
$LANG['RECORDS_TOOK'] = 'enregistrements pris';
$LANG['SEC'] = 'secondes';
$LANG['SPEC_RANK_OF'] = 'Enregistrement avec rang de';
$LANG['CHECKED_BY'] = 'vérifié par';
$LANG['NOTHING_TO_DISPLAY'] = 'Rien à afficher';
$LANG['POPULATE_COUNTRY'] = 'Indiquer le pays si manquant; peut être déduit des coordonnées';
$LANG['POPULATE_STATE_PROVINCE'] = "Indiquer l'état/la province si manquant; peut être déduit des coordonnées";
$LANG['POPULATE_COUNTY'] = "Indiquer le comté si manquant; peut être déduit des coordonnées";
$LANG['RE-VALIDATE_ALL_COORDINATES'] = 'Revalider toutes les coordonnées';
$LANG['VALIDATE_ALL_COORDINATES'] = 'Valider toutes les coordonnées';
$LANG['RANKING_STATISTICS'] = 'Statistiques de classement';

$LANG['RANKING'] = 'Classement';
$LANG['STATUS'] = 'Statut';
$LANG['COUNT'] = 'Nombre';
$LANG['RE-VERIFY'] = 'Revérifier';
$LANG['UNVERIFIED'] = 'non vérifié';

$LANG['UNVERIFIED_BY_COUNTRY'] = 'Enregistrements non vérifiés classés par pays';
$LANG['COUNTRY'] = 'Pays';
$LANG['VIEW_SPECIMENS'] = 'Voir les spécimens';
$LANG['NOT_AUTHORIZED'] = "Vous n'êtes pas autorisé à accéder à cette page";

$LANG['COUNTRY_DOES_NOT_MATCH_COORDS'] = 'Les coordonnées du pays ne correspondent pas';
$LANG['STATE_PROVINCE_DOES_NOT_MATCH_COORDS'] = "Les coordonnées de l'État/de la province ne correspondent pas";
$LANG['COUNTY_DOES_NOT_MATCH_COORDS'] = 'Les coordonnées du comté ne correspondent pas';
$LANG['UNVERIFIABLE_NO_POLYGON'] = 'Échec de la validation des coordonnées selon le thésaurus géographique';
$LANG['HAS_POLYGON_FAILED_TO_VERIFY'] = 'Échec de la validation des coordonnées malgré un polygone de recherche connu';
$LANG['NOT_AUTHORIZED'] = "Vous n'êtes pas autorisé à accéder à cette page";
$LANG['INVALID_RANK'] = 'Classement de validation des coordonnées invalide';

$LANG['COUNTRY_POPULATED'] = 'Valeurs du pays renseignées';
$LANG['STATE_PROVINCE_POPULATED'] = "Valeurs de l'État/Province renseignées";
$LANG['COUNTY_POPULATED'] = 'Valeurs du comté renseignées';
?>