# IW WhatsApp Button 1.1.0

Botón flotante para cualquier sitio WordPress. No requiere WooCommerce, jQuery ni un CDN.

## Instalación y configuración

1. Sube `dist/wc-whatsapp-button-v1.1.0.zip` desde Plugins → Añadir plugin → Subir plugin.
2. Si ya tienes la versión anterior, acepta reemplazarla. La carpeta y archivo principal se mantienen para actualizar el mismo plugin.
3. Activa el plugin y abre Ajustes → WhatsApp Button.
4. Indica el teléfono con código de país (por ejemplo, `573001234567`) y el mensaje inicial.

El botón aparece en todo el sitio, abajo a la derecha, con fondo circular verde `#25D366` e ícono blanco de WhatsApp de Font Awesome Free, incluido localmente como SVG. El tema debe ejecutar el hook estándar `wp_footer()`. Un teléfono vacío oculta el botón.

## Cambios 1.1.0

- Se elimina la dependencia y la ubicación exclusiva de WooCommerce.
- Se reemplaza el botón de compra por un acceso de contacto general en todo el sitio.
- La configuración pasa a Ajustes y conserva el teléfono anterior (`wc_whatsapp_phone`).
- Se añade mensaje inicial configurable y codificación correcta del enlace.
- Botón de 60 px, enfoque visible de teclado y respeto de áreas seguras móviles.
- No extrae precios, cantidades ni variaciones de productos.

## Requisitos

WordPress 5.8 o posterior y PHP 7.4 o posterior. Sintaxis validada para PHP 7.4 y paquete ZIP verificado. Pendiente prueba funcional en una instalación WordPress real.

## Ícono

Font Awesome Free 6, WhatsApp (Brands), por Fonticons, Inc. SVG bajo CC BY 4.0. Consulta `assets/LICENSE-fontawesome.txt`.
