/* Integración opcional: precio, cantidad y variación del formulario del producto. */
(function ($) {
    'use strict';

    function plainText(html) {
        var element = document.createElement('div');
        element.innerHTML = html || '';
        return element.textContent.trim();
    }

    function buildMessage(button) {
        var form = button.closest('form.cart');
        var quantity = form ? form.querySelector('input.qty') : null;
        var price = button.getAttribute('data-product-price') || '';
        var variation = form ? $(form).data('iwWhatsappVariation') : null;
        // WooCommerce proporciona el HTML del precio de la variación seleccionada.
        if (variation && variation.price_html) {
            price = plainText(variation.price_html);
        }
        var attributes = [];
        if (form) {
            form.querySelectorAll('.variations select').forEach(function (select) {
                if (!select.value) {
                    return;
                }
                var row = select.closest('tr');
                var label = row ? row.querySelector('label') : null;
                var selected = select.options[select.selectedIndex];
                attributes.push('- ' + (label ? label.textContent.trim() : select.name) + ': ' + selected.textContent.trim());
            });
        }
        return 'Hola, estoy interesado en este producto:\n\n' +
            'Producto: ' + button.getAttribute('data-product-name') + '\n' +
            'Precio: ' + price + '\n' +
            (attributes.length ? 'Variación:\n' + attributes.join('\n') + '\n' : '') +
            'Cantidad: ' + (quantity && quantity.value ? quantity.value : '1') + '\n\n' +
            button.getAttribute('data-product-url');
    }

    $(document).on('found_variation', 'form.variations_form', function (event, variation) {
        $(this).data('iwWhatsappVariation', variation);
    }).on('reset_data hide_variation', 'form.variations_form', function () {
        $(this).removeData('iwWhatsappVariation');
    }).on('click', '.iw-whatsapp-product', function () {
        // Se actualiza el enlace antes de la navegación nativa; no se requiere window.open.
        this.href = 'https://wa.me/' + this.getAttribute('data-phone') + '?text=' + encodeURIComponent(buildMessage(this));
    });
})(jQuery);
