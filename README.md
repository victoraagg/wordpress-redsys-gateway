# wordpress-redsys-gateway
Botones de pago de WordPress - RedSys sin necesidad de tener instalado WooCommerce

## Modo de uso del shortcode
- Crear nuevo botón en el admin y asignar precio
![admin wp](https://bthebrand.es/uploads/redsys-example-1.png)
- Colocar el shortcode con el ID del botón creado, la cantidad y el ID de pedido que corresponda
```php
[redsysbutton desc="Descripcion del producto" id="00" qty="1" order="ORDER-0000"]
```