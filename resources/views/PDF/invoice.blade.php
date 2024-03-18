<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Título de tu PDF</title>
        <link rel="stylesheet" href="{{ public_path('css/invoice.css') }}">
    </head>

    <body>
         <div class="header">
            <table>
                <tr>
                    <td class="company-logo">
                        {{-- <img src="{{ $dataPDF['path_logo'] }}" alt="Logo de la empresa"> --}}
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
                        <p><strong>Nombre: </strong> {{$client['names'] ? $client['names'].' '. $client['surnames'] : $client['business_name']}}</p>
                        <p><strong>Tipo de Documento: </strong> {{$client['type_document']}}</p>
                        <p><strong>Identificación: </strong> {{$client['identification']}}</p>
                        <p><strong>Dirección: </strong> {{$client['address']}}</p>
                        <p><strong>Correo: </strong> {{$client['email']}}</p>
                    </td>
                    <td class="right-client-data">
                        <!-- Contenido del div derecho (número grande) -->
                        <h2>Cotizacion: N°{{$invoice->id}}</h2>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            {{-- Productos contratados --}}
            <div class="products-invoice-table">
                <h3>{{ $titlePDF }}</h3>
                @foreach ( $products as $product )
                    <table>
                        <thead>
                            <tr class="content-table-font-size">
                                <th class="product-name left-aligned">Nombre del Producto</th>
                                <th class="left-aligned">Cantidad</th>
                                <th class="left-aligned">Precio</th>
                                <th class="left-aligned">Descuento</th>
                                <th class="right-aligned">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="content-table-font-size">
                                <td>{{$product->product->name}}</td>
                                <td>{{$product->amount}}</td>
                                <td>${{$product->fcost}}</td>
                                <td>${{$product->fdiscount}}</td>
                                <td class="right-aligned">${{$product->total_format}}</td>
                            </tr>
                        </tbody>
                    </table>
                    @if (count($product->taxes) > 0)
                        <table>
                            <thead>
                                <tr class="content-table-font-size-tax">
                                    <th class="left-aligned">Impuestos asociados</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->taxes as $tax)
                                    <tr class="content-table-font-size-tax">
                                        <td>{{$tax->acronym}}</td>
                                        <td>%{{$tax->pivot->percent}}</td>
                                        <td>${{$tax->total_tax}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                <br>
                @endforeach
            </div>
            @if ($furtherProducts && count($furtherProducts) > 0)

            <div class="products-invoice-table">
                <h3>Productos adicionales</h3>
                @foreach ( $furtherProducts as $product )
                    <table>
                        <thead>
                            <tr class="content-table-font-size">
                                <th class="product-name left-aligned">Nombre del Producto</th>
                                <th class="left-aligned">Cantidad</th>
                                <th class="left-aligned">Precio</th>
                                <th class="left-aligned">Descuento</th>
                                <th class="left-aligned">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="content-table-font-size">
                                <td>{{$product->product->name}}</td>
                                <td>{{$product->amount}}</td>
                                <td>${{$product->fcost}}</td>
                                <td>${{$product->fdiscount}}</td>
                                <td>${{$product->total_format}}</td>
                            </tr>
                        </tbody>
                    </table>
                    @if (count($product->taxes) > 0)
                        <table>
                            <thead>
                                <tr class="content-table-font-size-tax">
                                    <th class="left-aligned">Impuestos asociados</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->taxes as $tax)
                                    <tr class="content-table-font-size-tax">
                                        <td>{{$tax->acronym}}</td>
                                        <td>%{{$tax->pivot->percent}}</td>
                                        <td>${{$tax->total_tax}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                <br>
                @endforeach
            </div>
        </div>


            @endif
        <div class="client-data">
            <table>
                <tr class="content-table-font-size-tax">
                    <td class="left-client-data-invoice">
                        <strong class="subtitle">Observaciones</strong>
                        {{$invoice->note}}
                    </td>
                    <td class="right-client-data-invoice">
                        <p><strong>Total productos: </strong></p>
                        @if (count($product->taxes) > 0)
                        <p><strong>Total Adicionales: </strong></p>
                        @endif
                        <p><strong>Impuestos Totales: </strong></p>
                        <p><strong>Total: </strong></p>
                    </td>
                    <td class="right-client-data-invoice-value">
                        <p>${{$productsPurchase['total_product']}}</p>
                        @if (count($product->taxes) > 0)
                        <p><strong>{{$furtherProductsPurchase['total_product']}}</strong></p>
                        @endif
                        <p>${{$productsPurchase['total_tax_product']}}</p>
                        <p>${{$productsPurchase['total_purchase']}}</p>
                    </td>
                </tr>
            </table>
        </div>
        <footer>
            {{$dataPDF['footer']}}
        </footer>
    </body>
</html>
