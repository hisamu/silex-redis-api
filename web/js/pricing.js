;(function($){
/*
    This is a simple placeholder wich looks for [acme-price] elements 
    and searches the price through the API.
*/
var api = '/api/';

var loadPriceElements = function() {
    var elements = $('[acme-price]'),
        ids = [],
        url = api + 'price/';

    if (elements.length < 1) {
        return null;
    }

    if (elements.length > 1) {
        elements.each(function(){
            ids.push($(this).attr('acme-price'));
        });
    } else {
        ids.push(elements.first().attr('acme-price'));
    }

    $.getJSON(url + '/' + ids.join('/'), function(products){
        if (elements.length > 1) {
            for (var prod in products) {
                elements.filter('[acme-price="'+prod+'"]').text(products[prod].price);
            }
        } else {
            elements.first().text(products.price);
        }
    });
}

loadPriceElements();

}(jQuery));