<?php
/*
------------------
Language: Español (Spanish)
------------------
*/

include_once('checklist.es.php');

$LANG['MANAGE_CHECKLIST'] = 'Administrar lista de verificación';
$LANG['NO_PERMISSIONS'] = 'No tienes permiso para crear una lista. Por favor contacte a un administrador.';
$LANG['ERR_DELETING_CHECKLIST'] = 'ERROR al eliminar la lista';
$LANG['ERR_ADDING_CHILD'] = 'ERROR al agregar el enlace de la lista infantil';
$LANG['CHECK_PARSED_SUCCESS'] = '¡Lista analizada correctamente!';
$LANG['TARGET_PROJ'] = 'Proyecto de destino';
$LANG['TARGET_CHECKLIST'] = 'Lista de objetivos';
$LANG['PARENT_CHECKLIST'] = 'Lista de los padres';
$LANG['SELECTPROJECT'] = 'Selecciona un proyecto';
$LANG['RETURNCHECK'] = 'Volver al Listado de Comprobación';
$LANG['ADMIN'] = 'Admin';
$LANG['DESCRIPTION'] = 'Descripción';
$LANG['RELATEDCHECK'] = 'Listados de Comprobación Relacionados';
$LANG['ERROR'] = 'ERROR';
$LANG['ADDIMGVOUCHER'] = 'Agregar Imágen Voucher';
$LANG['NOEDITOR'] = 'Nadie ha sido asignado explícitamente como un editor';
$LANG['ADDNEWUSER'] = 'Agregar Usuario Nuevo';
$LANG['SELECTUSER'] = 'Seleccionar Usuario';
$LANG['INVENTORYPROJECTS'] = 'Asignaciones a Proyecto de Inventario';
$LANG['REMOVEPROJECTCONFIRM'] = '&#191Estás seguro de que quieres desvincular el proyecto?';
$LANG['REMOVEPROJECT'] = 'Desvincular lista del proyecto';
$LANG['CHECKNOTASSIGNED'] = 'Listado de Comprobación no ha sido asignado a ningún Proyecto de Inventario';
$LANG['LINKTOPROJECT'] = 'Enlace a un Proyecto';
$LANG['SUBMIT'] = 'Enviar';
$LANG['PERMREMOVECHECK'] = 'Retirar permanentemente Listado de Comprobación';
$LANG['REMOVEUSERCHECK'] = 'Antes de que una lista de comprobación se pueda eliminar, todos los editores (excepto a sí mismo) y las asignaciones del proyecto de inventario deben ser eliminados.
							Las asignaciones del proyecto de inventario sólo pueden ser eliminados por el administrador activo del proyecto o un administrador del sistema. ';
$LANG['WARNINGNOUN'] = 'ADVERTENCIA: La acción no se puede deshacer.';
$LANG['EDITOR_DELETE'] = '¿Está seguro de que desea eliminar este editor:';
$LANG['PROJECT_DELETE'] = '¿Está seguro de que desea desvincular esta lista de verificación de';
$LANG['CONFIRMDELETE'] = '¿Está seguro de que desea eliminar permanentemente el Listado de Comprobación? ¡Esta acción no se puede deshacer!';
$LANG['CREATECHECKDET'] = 'Crear Nueva Lista';
$LANG['EDITCHECKDET'] = 'Editar detalles del Listado de Comprobación';
$LANG['CHECKNAME'] = 'Nombre del Listado de Comprobación';
$LANG['CHECKTYPE'] = 'Tipo de Listado de Comprobación';
$LANG['EXCLUDESPP'] = 'Lista de Especies Excluidas';
$LANG['RARETHREAT'] = 'Listado de especies raras, amenazadas, protegidas';
$LANG['GENCHECK'] = 'Listado de Comprobación General';
$LANG['EXTSERVICE'] = 'Servicio Externo (por ejemplo, iNaturalist) para asociar con esta listado';
$LANG['EXTSERVICEID'] = 'ID de Proyecto de Servicio Externo';
$LANG['EXTSERVICETAXON'] = 'Filtro de Taxones de Servicio Externo [opcional]';
$LANG['LOC'] = 'Localidad';
$LANG['PARCHECK'] = 'Listado de Comprobación Padre';
$LANG['SELECTPARCHECK'] = 'Seleccionar un Listado de Comprobación Padre';
$LANG['SPECIES'] = 'Especies:';
$LANG['LATCENT'] = 'Latitud';
$LANG['LONGCENT'] = 'Longitud';
$LANG['POINTRAD'] = 'Punto Radio (metros)';
$LANG['POLYFOOT'] = 'Huella de Polígono';
$LANG['POLYGON_DEFINED'] = 'Polígono definida<br/>Haga clic en globo para ver / editar';
$LANG['POLYGON_NOT_DEFINED'] = 'Polígono definida<br/>Haga clic en globo para crear';
$LANG['CREATE_EDIT_POLYGON'] = 'Crear/Editar Polígono';
$LANG['POLYGON_READY'] = 'Polígono cambiado y listas para guardar';
$LANG['DEFAULTDISPLAY'] = 'Ajustes de Vista por Defecto';
$LANG['SHOWDETAILS'] = 'Mostrar Detalles';
$LANG['DEFAULT_SORT'] = 'Secuencia de Clasificación por Defecto';
$LANG['ACCESS'] = 'Acceso';
$LANG['PRIVATE'] = 'Privado (cualquiera con enlace puede ver)';
$LANG['PRIVATE_STRICT'] = 'Estricta privada (solo las editoras pueden ver)';
$LANG['PUBLIC'] = 'Público';
$LANG['ADDIMGVOUC'] = 'Agregar Imágen Voucher y Vínculo a Listado de Comprobación';
$LANG['FORMADDVOUCH'] = 'Este formulario le permitirá añadir un voucher de imágen vinculado a este listado de comprobación.<br/>
						Si no está ya presente, el nombre científico se añadirá al listado de comprobación.';
$LANG['SELECTVOUCPROJ'] = 'Seleccione el proyecto al que desea añadir el voucher.';
$LANG['IDNOTSET'] = ' Identificador de listado de Comprobación no se ha establecido';
$LANG['NOADMINPERM'] = ' Usted no tiene permiso administrativo para este listado de comprobación';
$LANG['CURREDIT'] = 'Editores Actuales';
$LANG['ASSIGNED_BY'] = 'Asignado por';
$LANG['REMOVEEDITPRIVCONFIRM'] = '¿Está seguro de que desea eliminar los derechos de edición para este usuario?';
$LANG['MASSUPDATEED'] = 'Editar Detalles del Listado de Comprobación';
$LANG['ADDEDITOR'] = 'Agregar Editor';
$LANG['DELETECHECK'] = 'Borrar Listado de Comprobación';
$LANG['EDITCHECKLIST'] = 'Editar Lista';
$LANG['SAVE_EDITS'] = 'Someter Cambios';
$LANG['ADDCHECKLIST'] = 'Crear Lista Nueva';
$LANG['DELETETHISU'] = 'Borrar este usuario';
$LANG['ERROR_LOWER'] = 'Error';

$LANG['DROP_ICON_FOR_EDITOR'] = 'Soltar Icono para el Editor';
$LANG['DROP_ICON_FOR_DELETE_PROJECT'] = 'Soltar Icono para Dliminar Proyecto';
$LANG['SUBMIT_BUTTON'] = 'Botón Enviar';

?>
