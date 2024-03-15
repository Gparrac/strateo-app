<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Título de tu PDF</title>
        <link rel="stylesheet" href="{{ asset('css/invoice.css') }}">
    </head>

    <body>
        <div class="header">
            <table>
                <tr>
                    <td class="company-logo">
                        <img src="{{ $dataPDF['path_logo'] }}" alt="Logo de la empresa">
                    </td>
                    <td class="company-info">
                        {{$dataPDF['header']}}
                    </td>
                </tr>
            </table>
        </div>
        <div class="client-data">
            <table>
                <tr>
                    <td class="left-client-data">
                        <!-- Contenido del div izquierdo -->
                        <p>Contenido del lado izquierdo</p>
                        <p>Contenido del lado izquierdo</p>
                        <p>Contenido del lado izquierdo</p>
                        <p>Contenido del lado izquierdo</p>
                        <p>Contenido del lado izquierdo</p>
                        <p>Contenido del lado izquierdo</p>
                        <p>Contenido del lado izquierdo</p>
                        <p>Contenido del lado izquierdo</p>
                    </td>
                    <td class="right-client-data">
                        <!-- Contenido del div derecho (número grande) -->
                        <h1># 45 </h1>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            {{-- Productos contratados --}}
            <div class="products-invoice-table">
                <h3>Productos Contratados, Eventos y Productos adicionales</h3>
                <table>
                    <thead>
                        <tr class="content-table-font-size">
                            <th class="product-name left-aligned">Nombre del Producto</th>
                            <th class="left-aligned">Cantidad</th>
                            <th class="left-aligned">Precio</th>
                            <th class="left-aligned">Descuento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="content-table-font-size">
                            <td>Producto 1</td>
                            <td>10</td>
                            <td>$100</td>
                            <td>5%</td>
                        </tr>
                        <tr class="content-table-font-size">
                            <td>Producto 2</td>
                            <td>5</td>
                            <td>$50</td>
                            <td>0%</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr class="content-table-font-size-tax">
                            <th class="left-aligned">Impuestos asociados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="content-table-font-size-tax">
                            <td>Impuesto 1</td>
                            <td>10%</td>
                            <td>100000</td>
                        </tr>
                        <tr class="content-table-font-size-tax">
                            <td>Impuesto 1</td>
                            <td>10%</td>
                            <td>100000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Eventos --}}
            <div class="products-invoice-table">
                <h3>Productos Contratados, Eventos y Productos adicionales</h3>
                <table>
                    <thead>
                        <tr class="content-table-font-size">
                            <th class="product-name left-aligned">Nombre del Producto</th>
                            <th class="left-aligned">Cantidad</th>
                            <th class="left-aligned">Precio</th>
                            <th class="left-aligned">Descuento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="content-table-font-size">
                            <td>Producto 1</td>
                            <td>10</td>
                            <td>$100</td>
                            <td>5%</td>
                        </tr>
                        <tr class="content-table-font-size">
                            <td>Producto 2</td>
                            <td>5</td>
                            <td>$50</td>
                            <td>0%</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr class="content-table-font-size-tax">
                            <th class="left-aligned">Impuestos asociados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="content-table-font-size-tax">
                            <td>Impuesto 1</td>
                            <td>10%</td>
                            <td>100000</td>
                        </tr>
                        <tr class="content-table-font-size-tax">
                            <td>Impuesto 1</td>
                            <td>10%</td>
                            <td>100000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Productos adicionales --}}
            <div class="products-invoice-table">
                <h3>Productos Contratados, Eventos y Productos adicionales</h3>
                <table>
                    <thead>
                        <tr class="content-table-font-size">
                            <th class="product-name left-aligned">Nombre del Producto</th>
                            <th class="left-aligned">Cantidad</th>
                            <th class="left-aligned">Precio</th>
                            <th class="left-aligned">Descuento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="content-table-font-size">
                            <td>Producto 1</td>
                            <td>10</td>
                            <td>$100</td>
                            <td>5%</td>
                        </tr>
                        <tr class="content-table-font-size">
                            <td>Producto 2</td>
                            <td>5</td>
                            <td>$50</td>
                            <td>0%</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr class="content-table-font-size-tax">
                            <th class="left-aligned">Impuestos asociados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="content-table-font-size-tax">
                            <td>Impuesto 1</td>
                            <td>10%</td>
                            <td>100000</td>
                        </tr>
                        <tr class="content-table-font-size-tax">
                            <td>Impuesto 1</td>
                            <td>10%</td>
                            <td>100000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="client-data">
            <table>
                <tr>
                    <td class="left-client-data">
                        <!-- Contenido del div izquierdo -->
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Facere eum nulla atque earum pariatur culpa corporis officia laudantium veritatis sunt sed exercitationem odit praesentium ratione expedita veniam, quia nobis recusandae.</p>
                    </td>
                    <td class="right-client-data">
                        <!-- Contenido del div derecho (número grande) -->
                        <p>Costos Totales </p>
                        <p>Impuestos totales</p>
                        <p>Descuentos totales y extras</p>
                        <p>Productos adicionales</p>
                    </td>
                </tr>
            </table>
        </div>
        <footer>
            {{$dataPDF['footer']}}
        </footer>
    </body>
</html>