const DEFAULT_DRAW_OPTIONS = {
   polyline: false,
   circlemarker: false,
   marker: false,
   multi_draw: false,
   draw_color: { color: '#000', opacity: 0.85, fillOpacity: 0.55 }
}

const DEFAULT_MAP_OPTIONS = {
   center: [43.64701, -79.39425],
   zoom: 15,
}

const DEFAULT_SHAPE_OPTIONS = {
   color: '#000',
   opacity: 0.85,
   fillOpacity: 0.55
}

class LeafletMap {
   /* To Hold Reference to Leaflet Map */
   map_layer;

   /* Active_Shape Type
    * type: String represenation of shape 
    * layer: leaflet layer
    * center: { lat: float, lng float }
    */
   active_shape;

   /* Reference Leaflet Feature Group for all drawn items*/
   draw_layer;

   constructor(map_id, map_options=DEFAULT_MAP_OPTIONS) {
      this.map_layer = L.map(map_id, map_options);

      const terrainLayer = L.tileLayer('https://{s}.google.com/vt?lyrs=p&x={x}&y={y}&z={z}', {
         attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
         subdomains:['mt0','mt1','mt2','mt3'],
         maxZoom: 20, 
         tileSize: 256,
      }).addTo(this.map_layer);

      const satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
         attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
         subdomains:['mt1','mt2','mt3'],
         maxZoom: 20, 
         tileSize: 256,
      });

      L.control.layers({
         "Terrain": terrainLayer,
         "Satellite": satelliteLayer
      }).addTo(this.map_layer);

   }

   enableDrawing(draw_options = DEFAULT_DRAW_OPTIONS, onDrawChange) {

      var drawnItems = new L.FeatureGroup();
      this.draw_layer = drawnItems;
      this.map_layer.addLayer(drawnItems);

      //Jank workaround for leaflet-draw api
      const setDrawColor = (draw_option) => {
         if(draw_options[draw_option] === false)
            return;

         if(!draw_options[draw_option]) draw_options[draw_option] = { }

         if(!draw_options[draw_option].shapeOptions)
            draw_options[draw_option].shapeOptions = draw_options.draw_color
      }

      if(draw_options.draw_color) {
         //Setting Styles for Pre Rendered
         L.Path.mergeOptions(draw_options.draw_color)
         //Force to Manually Each Color unfortunately
         setDrawColor("polyline")
         setDrawColor("polygon")
         setDrawColor("rectangle")
         setDrawColor("circle")
      }

      var drawControl = new L.Control.Draw({
         draw: draw_options,
         edit: {
            featureGroup: drawnItems,
         }
      });

      this.map_layer.addControl(drawControl);

      this.map_layer.on('draw:edited', function(e) {
         ///Some Extra steps to get at the layer
         const layer = e.layers._layers;
         const keys = Object.keys(layer);
         let type = this.active_shape.type;

         if(keys.length > 0) this.active_shape = getShapeCoords(type, layer[keys[0]]);

         if(onDrawChange) onDrawChange(this.active_shape);
      }.bind(this))
         
      this.map_layer.on('draw:deleted', function(e) {
         this.active_shape = null;
         if(onDrawChange) onDrawChange(this.active_shape);
      }.bind(this))

      this.map_layer.on('draw:created', function (e) {
         if(!draw_options || !draw_options.multi_draw)
            drawnItems.clearLayers();

         const layer = e.layer;
         drawnItems.addLayer(layer);

         this.active_shape = getShapeCoords(e.layerType, e.layer);

         if(onDrawChange) onDrawChange(this.active_shape);
      }.bind(this))
   }

   drawShape(shape) {
      switch(shape.type) {
         case "polygon":
            const poly = L.polygon(shape.latlngs)
            this.active_shape = getShapeCoords(shape.type, poly);
            poly.addTo(this.draw_layer);
            break;
         case "rectangle":
            const rec = L.rectangle([
               [shape.upperLat, shape.rightLng],
               [shape.lowerLat, shape.leftLng]
            ])
            this.active_shape = getShapeCoords(shape.type, rec);
            rec.addTo(this.draw_layer)
            break;
         case "circle":
            const circ = L.circle(shape.latlng, shape.radius);
            this.active_shape = getShapeCoords(shape.type, circ);
            circ.addTo(this.draw_layer)
         default:
            throw Error(`Can't draw ${shape.type}`)
      } 
   }

}

function getShapeCoords(layerType, layer) {
   if(!layer)
      return null;

   let shape ={
      type: layerType,
      layer: layer,
   }

   const SIG_FIGS = 6;

   switch(layerType) {
      case "polygon":
         let polygon = layer._latlngs[0].map(coord => 
            (`${coord.lat.toFixed(SIG_FIGS)} ${coord.lng.toFixed(SIG_FIGS)}`));
         shape.points = layer._latlngs[0];
         shape.wkt = "POLYGON ((" + polygon.join(',') + "))";
         shape.center = layer.getBounds().getCenter();
         break;
      case "rectangle":
         const northEast = layer._bounds._northEast;
         shape.upperLat =  northEast.lat.toFixed(SIG_FIGS)
         shape.rightLng =  northEast.lng.toFixed(SIG_FIGS)

         const southWest = layer._bounds._southWest;
         shape.lowerLat =southWest.lat.toFixed(SIG_FIGS)
         shape.leftLng = southWest.lng.toFixed(SIG_FIGS)

         shape.center = layer.getBounds().getCenter();
         break;
      case "circle":
         shape.radius = layer._mRadius.toFixed(SIG_FIGS);
         shape.center = {
            lat: layer._latlng.lat.toFixed(SIG_FIGS),
            lng: layer._latlng.lng.toFixed(SIG_FIGS)
         }
         break;
      default:
         throw Error("Couldn't parse this shape type");
   }

   return shape;
}

/*
      L.marker([51.5, -0.09]).addTo(map)
         .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
         .openPopup();    
 */
