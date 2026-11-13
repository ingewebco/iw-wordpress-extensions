=== IW MegaMenu Divi ===
Contributors: IngeWeb.co
Version: 1.0.0
Requires PHP: 7.4
Requires at least: 6.0

Mega menús para Divi 4 en la cabecera #mod-header.

IMPLEMENTACIÓN Y COMPATIBILIDAD
Implementado para Maicito (nuevomaicito.ingeweb.com.co), con Divi 4.27.5
y su tema hijo personalizado. Pendiente verificar cómo se ve y funciona
en un sitio con Divi estándar, sin las personalizaciones de Maicito.
Depende del contenedor #mod-header y de las variables de color del tema.
Consulta README.md para las dependencias y comprobaciones pendientes.

CONFIGURACIÓN
1. Apariencia → Menús → Main Menu.
2. Abre un elemento principal y busca IW MegaMenu Divi.
3. Activa el mega menú y elige Columnas o Enlaces + Divi Library.
4. Para diseño mixto, selecciona un diseño publicado en Divi Library y el ancho de enlaces.
5. Guarda el menú. Edita el diseño visual desde Divi → Biblioteca Divi.

Cada hijo con descendientes forma una columna en el diseño Columnas. En el diseño
mixto los enlaces se agrupan a la izquierda y el diseño Divi ocupa el resto.
El plugin reutiliza las variables de color del tema, abre por hover o teclado,
y mantiene el panel abierto durante el cruce de la cabecera. Escape lo cierra.
El contenido de biblioteca se renderiza en el servidor. En móvil se oculta el
contenido adicional y se mantienen los enlaces. Evita incluir dentro del diseño
el mismo menú principal para no crear navegación recursiva.

INSTALACIÓN ACTUAL
Líneas de negocio: cuatro columnas.
Nosotros y Blog: modo mixto, pendiente de elegir el diseño de biblioteca.

Los datos se guardan como metadatos _iw_megamenu del ítem. Desactivar el plugin
conserva esos datos y devuelve el menú a su presentación normal del tema.
