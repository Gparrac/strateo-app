<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Título de tu PDF</title>
        <link rel="stylesheet" href="{{ public_path('css/invoice-template-v2.css') }}">
    </head>

    <body>
        @php
         use App\Http\Utils\PriceFormat;
        @endphp
        {{-- <div class="header">
            <table>
                <tr>
                    <td class="company-logo">

                    </td>
                    <td class="company-info">
                        {{$dataPDF['header']}}
                    </td>
                </tr>
            </table>
        </div>
        <div class="client-data">
            <table class="line-mark-table">
                <tr>
                    <td class="left-client-data">
                        <table >
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Cliente</strong></p></td>
                                <td><p>{{$invoice['client']['third']['fullname']}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Fecha de creación</strong></p></td>
                                <td><p>{{$invoice->created_at}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Ciudad</strong></p></td>
                                <td><p>{{$invoice['client']['third']['city']['name']}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>C.C o NIT</strong></p></td>
                                <td><p>{{$invoice['client']['third']['identification']}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Email</strong></p></td>
                                <td><p>{{$invoice['client']['third']['email']}}</p></td>
                            </tr>

                        </table>
                    </td>
                    <td class="left-client-data">
                        <table cellspacing="0">
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Vendedor</strong></p></td>
                                <td><p>{{$invoice['seller']['third']['fullname']}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Fecha del evento</strong></p></td>
                                <td><p>{{$invoice['date']}}</p></td>
                            </tr>
                            <tr class="information-rows-client"class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Celular</strong></p></td>
                                <td><p>{{$invoice['client']['third']['mobile']}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Dirección de contacto</strong></p></td>
                                <td><p>{{$invoice['client']['third']['address']}}</p></td>
                            </tr>
                        </table>
                    </td>
                    <td class="right-client-data line-mark-table">
                        <!-- Contenido del div derecho (número grande) -->
                        <h2>Cotizacion: N°{{$invoice->id}}</h2>
                    </td>
                </tr>
            </table>
        </div>
        <div>

            <div class="products-invoice-table">
                <h5>{{ $titlePDF }}</h5>
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
                    @if (count($product->taxes) > 0 )
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
                <h5>Productos adicionales</h5>
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
                                <td>${{$product->cost}}</td>
                                <td>${{$product->discount}}</td>
                                <td>${{$product->total}}</td>
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
            @endif

        </div>
        <div class="client-data">
            <table class="line-mark-table">
                <tr class="content-table-font-size-tax">
                    <td class="left-client-data-invoice line-mark-table table-padding-purchase">
                        <p><strong>Observaciones: </strong> {{$invoice->note}}</p>
                    </td>
                    <td class="right-client-data-invoice table-padding-purchase">
                        <p><strong>SUBTOTAL</strong></p>
                        <p><strong>-Descuentos</strong></p>
                        @if ($furtherProductsPurchase)
                            <p><strong>+Adicionales</strong></p>
                            @if ($productsPurchase['total_tax_product'] && $furtherProductsPurchase['total_tax_product'])
                            <p><strong>+IVA</strong></p>
                            @endif
                        @else
                            @if ($productsPurchase['total_tax_product'])
                                <p><strong>+IVA</strong></p>
                            @endif
                        @endif
                        <p><strong>NETO COTIZACION </strong></p>
                    </td>
                    <td class="right-client-data-invoice-value table-padding-purchase">
                        @if ($furtherProductsPurchase)
                            <p>${{$productsPurchase['total_product']}}</p>
                            <p>${{$furtherProductsPurchase['total_discount'] + $productsPurchase['total_discount']}}</p>
                            <p>${{$furtherProductsPurchase['total_product']}}</p>
                             <p>----- {{PriceFormat::getNumber($testPrice)}}</p>
                            <p>${{$furtherProductsPurchase['total_tax_product'] + $productsPurchase['total_tax_product']}}</p>
                             <p>${{$furtherProductsPurchase['total_purchase'] + $productsPurchase['total_purchase']}}</p>
                        @else
                            <p>${{$productsPurchase['total_product']}}</p>
                            <p>${{$productsPurchase['total_discount']}}</p>
                            <p>${{$productsPurchase['total_tax_product']}}</p>
                            <p>${{$productsPurchase['total_purchase']}}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <footer>
            {{$dataPDF['footer']}}
        </footer> --}}
                    {{--  starting taxes section --}}
                    @if ($headTaxes && count($headTaxes) > 0)
                    <h5>Impuestos</h5>
                    @foreach ($headTaxes as $index => $tax)
                    <div class="w-100">
                        <h6>{{$tax['name']}} ({{$tax['acronym']}})</h6>

                            <table class="w-100">
                                <thead>
                                    <tr class="content-table-font-size">
                                        <th class="product-name left-aligned">Nombre del Producto</th>
                                        <th class="left-aligned">Porcentaje</th>
                                        <th class="left-aligned">total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productTaxes[$index] as $product)
                                    <tr class="content-table-font-size">
                                        <td>{{$product['name']}}</td>
                                        <td>{{$product['percent']}}</td>
                                        <td>{{PriceFormat::getNumber($product['total'])}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    @endforeach
                    @endif
                    {{-- ending up taxes section --}}
    </body>
</html>
