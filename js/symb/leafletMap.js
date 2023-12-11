function getObservationSvg(opts = {color: "#7A8BE7", size: 24, className:""}) {
   const default_ops = {color: "#7A8BE7", size: 24};
   opts = {...default_ops, ...opts};
   const half = opts.size/2;

   return L.divIcon({
      html: `
<svg
width="${opts.size}"
height="${opts.size}"
viewBox="-10 -10 ${opts.size + 20} ${opts.size + 20}"
version="1.1"
preserveAspectRatio="none"
xmlns="http://www.w3.org/2000/svg"
>
<polygon class="${opts.className}" points="${half},0 0,${opts.size} ${opts.size},${opts.size}" style="fill:${opts.color};stroke:black;stroke-width:3" />
</svg>`,
      className: "",
      observation: true,
      iconSize: [opts.size, opts.size],
      iconAnchor: [half, half],
   });
}

class LeafletMap {
   //DEFAULTS
   DEFAULT_MAP_OPTIONS = {
      center: [43.64701, -79.39425],
      zoom: 15,
   };

   DEFAULT_SHAPE_OPTIONS = {
      color: '#000',
      opacity: 0.85,
      fillOpacity: 0.55
   };

   DEFAULT_DRAW_OPTIONS = {
      polyline: false,
      control: true,
      circlemarker: false,
      marker: false,
      multiDraw: false,
      drawColor: this.DEFAULT_SHAPE_OPTIONS 
   };
   
   /* To Hold Reference to Leaflet Map */
   mapLayer;

   /* Active_Shape Type
    * type: String represenation of shape 
    * layer: leaflet layer
    * center: { lat: float, lng float }
    */
   activeShape;

   //List of drawn shapes
   shapes = [];

   /* Reference Leaflet Feature Group for all drawn items*/
   drawLayer;

   constructor(map_id, map_options=this.DEFAULT_MAP_OPTIONS) {

      this.mapLayer = L.map(map_id, map_options);

      const terrainLayer = L.tileLayer('https://{s}.google.com/vt?lyrs=p&x={x}&y={y}&z={z}', {
         attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
         subdomains:['mt0','mt1','mt2','mt3'],
         maxZoom: 20, 
         tileSize: 256,
      }).addTo(this.mapLayer);

      const satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
         attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
         subdomains:['mt1','mt2','mt3'],
         maxZoom: 20, 
         tileSize: 256,
      });

      if(map_options.layer_control !== false) {
         L.control.layers({
            "Terrain": terrainLayer,
            "Satellite": satelliteLayer
         }).addTo(this.mapLayer);
      }

      if(map_options.scale !== false) {
         L.control.scale().addTo(this.mapLayer);
      }

      this.mapLayer._onResize();
   }

   clearMap() {
      this.drawLayer.clearLayers();
      this.activeShape = null;
      this.shapes = [];
   }

   enableDrawing(drawOptions = this.DEFAULT_DRAW_OPTIONS, onDrawChange) {

      var drawnItems = new L.FeatureGroup();
      this.drawLayer = drawnItems;
      this.mapLayer.addLayer(drawnItems);

      //Jank workaround for leaflet-draw api
      const setDrawColor = (drawOption) => {
         if(drawOptions[drawOption] === false)
            return;

         if(!drawOptions[drawOption]) drawOptions[drawOption] = { };

         if(!drawOptions[drawOption].shapeOptions)
            drawOptions[drawOption].shapeOptions = drawOptions.drawColor;
      }

      if(drawOptions.drawColor) {
         //Setting Styles for Pre Rendered
         L.Path.mergeOptions(drawOptions.drawColor);
         //Set Color for All Manual Draws 
         setDrawColor("polyline");
         setDrawColor("polygon");
         setDrawColor("rectangle");
         setDrawColor("circle");
      }
      if(drawOptions.map_mode_strict) {
         if(drawOptions.mode !== "polygon") drawOptions.polygon = false;
         if(drawOptions.mode !== "circle") drawOptions.circle = false;
         if(drawOptions.mode !== "rectangle") drawOptions.rectangle= false;
         if(drawOptions.mode !== "polyline") drawOptions.polyline = false;
      }

      if(drawOptions.control || drawOptions.control === undefined) {
         var drawControl = new L.Control.Draw({
            position: 'topright',
            draw: drawOptions,
            edit: {
               featureGroup: drawnItems,
            }
         });

         this.mapLayer.addControl(drawControl);

         //Event saved edit 
         this.mapLayer.on('draw:edited', function(e) {
            if(!e.layers || !e.layers._layers) return;
            ///Some Extra steps to get at the layer
            const layer = e.layers._layers;
            const keys = Object.keys(layer);
            let type = this.activeShape.type;

            if(keys.length > 0) {
               const edited_shape = getShapeCoords(type, layer[keys[0]]);
               this.activeShape = edited_shape;
               this.shapes = this.shapes.map(s=> s.layer._leaflet_id === edited_shape.layer._leaflet_id? edited_shape: s);
            }

            if(onDrawChange) onDrawChange(this.activeShape);
         }.bind(this))

         //Event saved delete 
         this.mapLayer.on('draw:deleted', function(e) {
            const ids = Object.keys(e.layers._layers);
            this.shapes = this.shapes.filter(s => !ids.includes(`${s.layer._leaflet_id}`))
            this.activeShape = null;
            if(onDrawChange) onDrawChange(this.activeShape);
         }.bind(this))

         //Fires on New Draw
         this.mapLayer.on('draw:created', function (e) {
            if(!drawOptions || !drawOptions.multiDraw) {
               this.drawLayer.clearLayers();
            }

            const id = this.shapes.length;

            const layer = e.layer;
            this.drawLayer.addLayer(layer);

            this.activeShape = getShapeCoords(e.layerType, e.layer);
            this.activeShape.id = id;
            this.shapes.push(this.activeShape);

            if(onDrawChange) onDrawChange(this.activeShape);
         }.bind(this))
      }
         switch(drawOptions.mode) {
            case "polygon": 
               document.querySelector(".leaflet-draw-draw-polygon").click();
            break;
            case "rectangle": 
               document.querySelector(".leaflet-draw-draw-rectangle").click();
            break;
            case "marker": 
               document.querySelector(".leaflet-draw-draw-marker").click();
            break;
            case "circle":
               document.querySelector(".leaflet-draw-draw-circle").click();
            break;
         }

   }

   drawShape(shape) {
      const id = this.shapes.length;
      switch(shape.type) {
         case "polygon":
            const poly = L.polygon(shape.latlngs);
            this.activeShape = getShapeCoords(shape.type, poly);
            poly.addTo(this.drawLayer);
            break;
         case "rectangle":
            const rec = L.rectangle([
               [shape.upperLat, shape.rightLng],
               [shape.lowerLat, shape.leftLng]
            ]);
            this.activeShape = getShapeCoords(shape.type, rec);
            rec.addTo(this.drawLayer)
            break;
         case "circle":
            const circ = L.circle(shape.latlng, shape.radius);
            this.activeShape = getShapeCoords(shape.type, circ);
            circ.addTo(this.drawLayer);
            break;
         default:
            throw Error(`Can't draw ${shape.type}`)
      }

      this.activeShape.id = id;
      this.shapes.push(this.activeShape);

      this.mapLayer.fitBounds(this.activeShape.layer.getBounds());
   }

}

function getShapeCoords(layerType, layer) {
   if(!layer)
      return null;

   let shape ={
      type: layerType,
      layer: layer,
   };

   const SIG_FIGS = 6;

   switch(layerType) {
      case "polygon":
         let latlngs = layer._latlngs[0].map(coord=> [coord.lat, coord.lng]);
         latlngs.push(latlngs[0]);

         let polygon = latlngs.map(coord => 
            (`${coord[0].toFixed(SIG_FIGS)} ${coord[1].toFixed(SIG_FIGS)}`));

         shape.latlngs = latlngs;
         shape.wkt = "POLYGON ((" + polygon.join(',') + "))";
         shape.center = layer.getBounds().getCenter();
         break;
      case "rectangle":
         const northEast = layer._bounds._northEast;
         shape.upperLat =  northEast.lat;
         shape.rightLng =  northEast.lng;

         const southWest = layer._bounds._southWest;
         shape.lowerLat =southWest.lat;
         shape.leftLng = southWest.lng;

         shape.center = layer.getBounds().getCenter();
         break;
      case "circle":
         shape.radius = layer._mRadius;
         shape.center = {
            lat: layer._latlng.lat,
            lng: layer._latlng.lng
         };
         break;
      default:
         throw Error("Couldn't parse this shape type");
   }

   return shape;
}
