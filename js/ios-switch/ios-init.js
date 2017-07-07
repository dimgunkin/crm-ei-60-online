
var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

elems.forEach(function(html) {
    var switchery = new Switchery(html);

});

// Colored switches
var blue = document.querySelector('.js-switch-blue');
var switchery = new Switchery(blue, { color: '#41b7f1' });

var pink = document.querySelector('.js-switch-pink');
var switchery = new Switchery(pink, { color: '#ff7791' });

var teal = document.querySelector('.js-switch-teal');
var switchery = new Switchery(teal, { color: '#3cc8ad' });

var red = document.querySelector('.js-switch-red');
var switchery = new Switchery(red, { color: '#db5554' });

var yellow = document.querySelector('.js-switch-yellow');
var switchery = new Switchery(yellow, { color: '#fec200' });


// Цвета чекбоксов ios7 для страницы информации о заявке
var production = document.querySelector('.js-switch-app-production');
var switchery = new Switchery(production, { color: '#f2f2f2' });

var ready = document.querySelector('.js-switch-app-ready');
var switchery = new Switchery(ready, { color: '#e7f9e1' });

var suspended = document.querySelector('.js-switch-app-suspended');
var switchery = new Switchery(suspended, { color: '#f8d9ac' });

var stopped = document.querySelector('.js-switch-app-stopped');
var switchery = new Switchery(stopped, { color: '#fce2dc' });

var partiallyshipped = document.querySelector('.js-switch-app-partially-shipped');
var switchery = new Switchery(partiallyshipped, { color: '#c0daf2' });

var shipped = document.querySelector('.js-switch-app-shipped');
var switchery = new Switchery(shipped, { color: '#e8cff8' });