# IW MegaMenu Divi 1.0.0

Mega menús para Divi con columnas de enlaces o una columna de enlaces acompañada de contenido de Divi Library.

## Implementación actual: Maicito

Implementado para **Maicito**, en `nuevomaicito.ingeweb.com.co`, con Divi 4.27.5 y su tema hijo personalizado. La apariencia reproduce su cabecera flotante, las tarjetas azules y el resaltado celeste de los enlaces.

- **Líneas de negocio:** cuatro columnas con sus categorías e hijos.
- **Nosotros y Blog:** título del elemento principal sobre sus enlaces, con espacio a la derecha para un diseño de Divi Library.
- Apertura mediante hover o foco, cierre con Escape y tolerancia al cruzar el espacio entre el enlace y el panel.
- Los paneles cerrados quedan fuera de la interacción para evitar que sus enlaces activen otro mega menú.

**Pendiente: verificar cómo se ve y funciona en un sitio con Divi estándar, sin las personalizaciones de Maicito.** Esta versión no está validada como solución visual independiente del tema hijo.

## Instalación y uso

1. Instala y activa la carpeta `iw-megamenu-divi` o el [ZIP instalable](../../dist/iw-megamenu-divi-v1.0.0.zip).
2. En **Apariencia → Menús**, abre un elemento principal y busca **IW MegaMenu Divi**.
3. Marca **Activar mega menú** y selecciona **Columnas de enlaces** o **Enlaces a la izquierda + Divi Library**.
4. En modo mixto, selecciona un diseño publicado de la biblioteca y el ancho de enlaces: 25 %, 33 %, 40 % o 50 %.
5. Guarda el menú y comprueba el resultado en la cabecera pública.

En el diseño por columnas, cada hijo con descendientes forma una columna. El modo mixto agrupa los enlaces a la izquierda y repite el título del padre como enlace superior. La sección visual se edita desde **Divi → Biblioteca Divi**; se guarda su ID en el elemento del menú y se renderiza en el servidor, sin pegar shortcodes manualmente.

El plugin excluye diseños con referencias circulares de biblioteca. No incluyas el mismo menú principal dentro del diseño insertado. En móvil se ocultan el título repetido y el contenido adicional de biblioteca, conservando los enlaces.

## Dependencias de la cabecera de Maicito

El CSS y JavaScript actuales usan el contenedor `#mod-header` y los menús `.et-menu` de Divi. El CSS conserva también la referencia al menú `#menu-main-menu` para igualar la especificidad de los estilos del tema. El walker se aplica a menús con clase `et-menu` que no tengan otro walker personalizado.

Los colores dependen de variables definidas por el tema: `--color-primary`, `--color-secondary`, `--color-white`, `--color-body` y `--color-gray-opaque`. Esta versión no define una paleta alternativa si esas variables faltan.

La cabecera de Maicito aporta el posicionamiento flotante, el manejo del desbordamiento y parte de los estilos generales del menú. Al migrar desde la versión que vivía en el tema hijo, deben retirarse sus reglas de mega menú y la carga del JavaScript anterior para evitar duplicados.

La configuración se almacena en el metadato `_iw_megamenu` de cada elemento. La instalación del código por sí sola no copia los menús, diseños de biblioteca ni ajustes de Maicito. Desactivar el plugin conserva esos datos.

## Validación y trabajo pendiente

Se comprobó la sintaxis PHP, la publicación de los tres paneles en Maicito, la estructura de listas y la renderización e inserción de una sección Divi independiente. La apariencia se ha ajustado con las capturas y pruebas del sitio de Maicito; no se ha realizado una validación visual automatizada completa.

Para evaluar una instalación estándar de Divi:

- Probar una cabecera de Theme Builder con un módulo Menú y comprobar la necesidad de añadir `#mod-header` y las reglas de posicionamiento.
- Revisar la cabecera nativa de Divi, que usa una estructura distinta y no está cubierta por los selectores actuales.
- Definir las variables de color o preparar valores predeterminados para un uso independiente.
- Verificar anchos, altura del panel, hover, navegación con teclado, cabecera sticky y presentación móvil.
- Comprobar los módulos utilizados en cada diseño de biblioteca y la carga de sus estilos y scripts.
- Verificar Divi 5 por separado; no se ha probado.
