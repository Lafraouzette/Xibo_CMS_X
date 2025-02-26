

// --- Add NPM Packages - JS ----
import './public_path';

// Masonry
window.Masonry = require('masonry-layout');

// images loaded
const imagesLoaded = require('imagesloaded');
// provide jQuery argument
imagesLoaded.makeJQueryPlugin( window.$ );

// moveable
window.Moveable = require('moveable/dist/moveable.min.js');
window.Selecto = require('selecto/dist/selecto.min.js');
