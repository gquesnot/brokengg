import './bootstrap';

import Alpine from 'alpinejs';
import Clipboard from "@ryangjchandler/alpine-clipboard"
// import $ from "jquery";
// import select2 from 'select2';
//
// window.$ = $;

Alpine.plugin(Clipboard)
window.Alpine = Alpine;

Alpine.start();
