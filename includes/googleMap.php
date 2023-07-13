<!-- Importing google api --> 
<script 
   src="//maps.googleapis.com/maps/api/js?v=3.exp&libraries=drawing<?php echo (isset($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY? '&key=' . $GOOGLE_MAP_KEY: ''); ?>">
</script>

<!-- Importing google map api js helper class and functions --> 
<script 
   src="<?php echo $CLIENT_ROOT; ?>/js/symb/googleMap.js" 
   type="text/javascript">
</script>
