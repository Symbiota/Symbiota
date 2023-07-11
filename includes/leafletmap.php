
<!-- Importing Leaflet and Leaflet Styles--> 
<link 
   rel="stylesheet" 
   href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
   integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
   crossorigin="" 
/>
<script 
   src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
   integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
   crossorigin="">
</script>

<!-- Importing Leaflet Draw Plugin --> 
<link 
   rel="stylesheet" 
   href="<?php echo $CLIENT_ROOT?>/js/leaflet-draw/dist/leaflet.draw.css"
/>
<script 
   src="<?php echo $CLIENT_ROOT?>/js/leaflet-draw/dist/leaflet.draw.js"
   type="text/javascript">
</script>

<!-- Importing Leaflet Draw Drag --> 
<script 
   src= <?php echo $CLIENT_ROOT."/js/leaflet-draw-drag/dist/Leaflet.draw.drag.js" ?>
   type="text/javascript">
</script>

<!-- Importing Cluster Plugin --> 
<script 
   src= <?php echo $CLIENT_ROOT."/js/Leaflet.markercluster-1.4.1/dist/Leaflet.markercluster.js" ?>
   type="text/javascript">
</script>

<link 
   rel="stylesheet" 
   href="<?php echo $CLIENT_ROOT?>/js/Leaflet.markercluster-1.4.1/dist/MarkerCluster.css"
/>
<link 
   rel="stylesheet" 
   href="<?php echo $CLIENT_ROOT?>/js/Leaflet.markercluster-1.4.1/dist/MarkerCluster.Default.css"
/>

<!-- Importing Leaflet Related Functions--> 
<script 
   src=<?php echo $CLIENT_ROOT."/js/symb/LeafletMap.js" ?>
   type="text/javascript">
</script>

<!-- Leaflet Specific Styling --> 
<style> 
   .leaflet-control-layers-expanded .leaflet-control-layers-list {
      font-size: 1.5rem;
   }
</style>
