/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything

require('bootstrap');
require('select2');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function () {
    $('[data-toggle="popover"]').popover();
    $('select').select2();
    $('.toast').toast({delay: 2500});
    $('.toast').toast('show');
});

// script for update image into CRUD
// listen the change input poster

let box = document.querySelector('.watch-js');
let img = document.getElementById('img');
let srcImage = document.getElementById('avatar');

if (box.value !== null) {
    if (img !== null) {
        img.setAttribute('src', box.value);
    }
    if (srcImage !== null) {
        srcImage.setAttribute('src', '/images/' + box.value);
    }
}

box.addEventListener('change', function () {
    document.getElementById('img').setAttribute('src', box.value);
})
