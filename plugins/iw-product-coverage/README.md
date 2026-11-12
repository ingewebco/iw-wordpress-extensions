# IW Product Coverage 1.0.0

Cobertura regional para productos WooCommerce, contenido condicional en Divi y envío de la región seleccionada a Contact Form 7.

- [Descargar ZIP instalable](../../dist/iw-product-coverage-v1.0.0.zip)
- [Guía completa de instalación y uso](README.txt)

Añada `[hidden region-disponible]` al formulario CF7 y `Región seleccionada: [region-disponible]` al correo.

```text
[iw_cobertura]
[contact-form-7 id="123" title="Consulta de producto"]
[/iw_cobertura]
```

Para controlar otro módulo, fila o sección de Divi, asigne al destino el ID CSS `consulta-producto` y coloque fuera de él:

```text
[iw_cobertura destino="#consulta-producto"]
```

Regiones iniciales: Cesar, Guajira, Magdalena, Bolívar y Santander. Se administran desde **Productos → Regiones disponibles** y se asignan en el editor del producto.

La versión tiene comprobaciones de sintaxis y pruebas DOM del selector. Está pendiente la validación de instalación y envío real en WordPress, WooCommerce, Divi 4 y CF7.
