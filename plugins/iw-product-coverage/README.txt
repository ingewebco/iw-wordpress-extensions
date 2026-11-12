IW Product Coverage 1.0.0 — IngeWeb

INSTALACIÓN
Instalar el ZIP en Plugins > Añadir nuevo > Subir plugin y activar.
Requiere WooCommerce para gestionar los productos. CF7 es opcional.
Productos > Regiones disponibles permite añadir, renombrar y eliminar regiones.
En el editor clásico del producto, la caja lateral Región disponible permite
marcar varias regiones y añadir nuevas. Desmarcar no elimina la región global.
Se crean una sola vez: Cesar, Guajira, Magdalena, Bolívar y Santander.

DIVI: FORMULARIO ENVUELTO
En un módulo Código o Texto que procese shortcodes:
[iw_cobertura]
[contact-form-7 id="123" title="Consulta de producto"]
[/iw_cobertura]

DIVI: MÓDULO, FILA O SECCIÓN EXTERNA
Asigne al destino el ID CSS consulta-producto (sin #).
Coloque en otro módulo Código:
[iw_cobertura destino="#consulta-producto"]
El selector debe estar fuera del destino. Un selector por destino; no anidar
contenedores de cobertura. Evitar formularios duplicados con IDs HTML iguales.
Para evitar un destello antes de cargar JavaScript puede añadir en su CSS:
#consulta-producto { display:none; }
#consulta-producto.iwpc-controlled:not([hidden]) { display:block; }
Use el display adecuado si su destino originalmente utiliza flex o grid.
Sin JavaScript el contenido protegido permanece oculto en modo envolvente;
el modo destino requiere ese CSS previo para ocultarse también sin JavaScript.

CONTACT FORM 7 (NECESARIO PARA VALIDACIÓN EN SERVIDOR)
Añada en la pestaña Formulario del CF7:
[hidden region-disponible]
Y en la pestaña Correo:
Región seleccionada: [region-disponible]
Mantenga sus campos titulo-producto, codigo-producto y url-producto existentes.
El plugin no modifica el mecanismo que ya los rellena.
El campo oculto explícito activa la validación del servidor: firma del producto,
publicación y asociación vigente de la región. El nombre enviado se obtiene de
la taxonomía; no se confía en el nombre enviado por el navegador.
Dedique este CF7 a consultas con cobertura: si lo utiliza fuera del contenedor,
la validación impedirá enviar por faltar una selección válida.
Sin ese campo explícito, JavaScript puede rellenar el valor, pero no se garantiza
la validación de cobertura ante una petición manipulada. Inclúyalo siempre.

ATRIBUTOS
producto_id="123" (necesario fuera de una ficha de producto)
etiqueta="Selecciona tu región"
placeholder="Seleccione una región"
sin_cobertura="Este producto no tiene cobertura disponible."
destino="#consulta-producto" (opcional; sustituye al contenido envuelto)

COMPORTAMIENTO
Sin regiones = sin cobertura; no se asignan automáticamente a los productos.
Se conserva lo escrito al cambiar región. La cobertura aplica al producto
completo, no a variaciones. Los textos se configuran en cada shortcode.
La selección controla presentación y validación CF7, no restringe el acceso a
contenido privado ni condiciona compras/envíos de WooCommerce.
No procesa [iw_cobertura] dentro del editor de campos de CF7 en esta versión.
Borrar el plugin conserva las regiones y asociaciones para no perder datos.

VALIDACIÓN
Sintaxis PHP 7.4 comprobada con php-parser; JavaScript con node --check.
Pruebas DOM con jsdom: modos envuelto y externo, sin cobertura, cambio de
región, conservación de datos y vuelta a la opción inicial: correctas.
Requiere prueba de instalación y envío en WordPress + WooCommerce + Divi 4 +
CF7 antes de producción. No se ha probado en un sitio real desde este entorno.
