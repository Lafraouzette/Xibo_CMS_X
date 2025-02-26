

// --- Add NPM Packages - JS ----
import './public_path';

// leaflet
require('leaflet');
require('leaflet-draw');
require('leaflet-search');

window.L = require('leaflet');
window.leafletPip = require('@mapbox/leaflet-pip');

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: '/dist/assets/marker-icon-2x.png',
  iconUrl: '/dist//assets/marker-icon.png',
  shadowUrl: '/dist/assets/marker-shadow.png',
});

require('leaflet.markercluster');
require('leaflet-easyprint');
require('leaflet-fullscreen');

// Style
require('leaflet/dist/leaflet.css');
require('leaflet-draw/dist/leaflet.draw-src.css');
require('leaflet-search/dist/leaflet-search.src.css');
require('leaflet.markercluster/dist/MarkerCluster.css');
require('leaflet.markercluster/dist/MarkerCluster.Default.css');
require('leaflet-fullscreen/dist/leaflet.fullscreen.css');
