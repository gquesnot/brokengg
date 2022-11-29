import './bootstrap';

import Alpine from 'alpinejs';

import Clipboard from "@ryangjchandler/alpine-clipboard/src/index.js";
// import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import  Lol  from './lol/lol';

import Tooltip from "@ryangjchandler/alpine-tooltip/src/index.js";
import 'reflect-metadata';
import 'es6-shim';
// import $ from "jquery";
// import select2 from 'select2';


//window.$ = $;


Alpine.plugin(Tooltip);
Alpine.plugin(Clipboard)

window.Alpine = Alpine;
Alpine.data('lol_class', (participants:any, items:any, version:any, participant_id:any,) => ({
    lol: new Lol(participants, items, version, participant_id)
}));
Alpine.start();
