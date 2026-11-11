# IW WhatsApp Button 1.2.0

Un solo plugin con dos botones y configuración en **Ajustes → WhatsApp Button**.

- **Flotante:** aparece en todo WordPress, abajo a la derecha, con ícono blanco de Font Awesome y fondo circular verde WhatsApp. Funciona sin WooCommerce y usa el mensaje inicial configurable.
- **Comprar por WhatsApp:** aparece junto a Añadir al carrito en el formulario de producto de WooCommerce. Envía nombre, precio mostrado, cantidad, atributos seleccionados y enlace del producto. Actualiza el precio al seleccionar una variación y lo restablece al limpiar la selección.

Ambos botones usan el mismo teléfono con código de país. El número de versiones anteriores se conserva; dejarlo vacío oculta ambos botones. El mensaje configurable se aplica al flotante; el de compra se construye a partir del producto.

## Instalación

Sube `wc-whatsapp-button-v1.2.0.zip` desde Plugins → Añadir plugin → Subir plugin y acepta reemplazar la versión anterior. Mantiene la carpeta y archivo principal originales. Activa el plugin y configura el número en Ajustes → WhatsApp Button.

## Requisitos y alcance

WordPress 5.8+, PHP 7.4+. WooCommerce es opcional. El tema debe ejecutar `wp_footer()` para el flotante y el hook `woocommerce_after_add_to_cart_button` en el formulario de compra para el botón de producto. Los constructores que sustituyen ese formulario deben conservar dicho hook.

El enlace de producto funciona sin JavaScript con datos básicos y cantidad 1; JavaScript actualiza cantidad y variaciones al hacer clic. La integración usa el jQuery de WordPress y el HTML de precios que proporciona WooCommerce. No añade productos al carrito ni procesa pedidos o pagos.

## Validación

Sintaxis PHP 7.4 y JavaScript verificadas. Pruebas de DOM aisladas: precio base, precio de variación, reinicio de selección, cantidad, atributos y codificación de mensajes/enlaces. ZIP verificado. Pendiente validación en una instalación WordPress/WooCommerce real.

## Cambios 1.2.0

Se recuperan las funciones de compra de la versión original y se mantienen el flotante general y los ajustes independientes de WooCommerce de 1.1.0. Las llamadas a WooCommerce se comprueban antes de usarlas.

## Ícono

Font Awesome Free 6, WhatsApp (Brands), por Fonticons, Inc. SVG bajo CC BY 4.0. Consulta `assets/LICENSE-fontawesome.txt`.
