

/* eslint-disable no-unused-vars */
const globalThis = require('globalthis/polyfill')();
import 'core-js/stable/url-search-params';
// Our own imports
import 'xibo-interactive-control/dist/xibo-interactive-control.min.js';
import './xibo-calendar-render';
import './xibo-countdown-render';
import './xibo-finance-render';
import './xibo-image-render';
import './xibo-legacy-template-render';
import './xibo-layout-animate.js';
import './xibo-layout-scaler';
import './xibo-menuboard-render';
import './xibo-metro-render';
import './xibo-substitutes-parser';
import './xibo-text-render';
import './xibo-text-scaler';
import './xibo-dataset-render';
import './xibo-webpage-render';
import './xibo-worldclock-render';
import './xibo-elements-render';
import './editor-render';

window.PlayerHelper = require('../../ui/src/helpers/player-helper.js');
window.XiboPlayer = require('./xibo-player.js');

window.jQuery = window.$ = require('jquery');
require('babel-polyfill');
window.moment = require('moment');
require('moment-timezone');
window.Handlebars = require('handlebars/dist/handlebars.min.js');
require('./handlebars-helpers.js');

// Include HLS.js
window.Hls = require('hls.js');

// Include PDFjs
window.pdfjsLib = require('pdfjs-dist/es5/build/pdf.js');

// Include common helpers/transformer
window.transformer = require('../../ui/src/helpers/transformer.js');
window.ArrayHelper = require('../../ui/src/helpers/array.js');
window.DateFormatHelper = require('../../ui/src/helpers/date-format-helper.js');

// Plugins
require('../vendor/jquery-cycle-2.1.6.min.js');
require('../vendor/jquery.marquee.min.js');
