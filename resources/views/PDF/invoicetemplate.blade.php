<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Título de tu PDF</title>
        <link rel="stylesheet" href="{{ asset('css/invoice-template.css') }}">
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
            <table class="line-mark-table">
                <tr>
                    <td class="left-client-data">
                        <table >
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Cliente</strong></p></td>
                                <td><p>{{$client->business_name ? $client->business_name : $client->names.' '.$client->surnames}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Fecha de creación</strong></p></td>
                                <td><p>{{$invoice->created_at}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Ciudad</strong></p></td>
                                <td><p>{{$client->city->name}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>C.C o NIT</strong></p></td>
                                <td><p>{{$client->identification}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Email</strong></p></td>
                                <td><p>{{$client->email}}</p></td>
                            </tr>
                            
                        </table>
                    </td>
                    <td class="left-client-data">
                        <table cellspacing="0">
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Vendedor</strong></p></td>
                                <td><p>{{$client->userCreate->third->business_name ? 
                                        $client->userCreate->third->business_name :
                                        $client->userCreate->third->names . ' ' .  $client->userCreate->third->surnames}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Fecha del evento</strong></p></td>
                                <td><p>No se sabe</p></td>
                            </tr>
                            <tr class="information-rows-client"class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Celular</strong></p></td>
                                <td><p>{{$client->mobile}}</p></td>
                            </tr>
                            <tr class="information-rows-client">
                                <td class="left-client-data-information"><p><strong>Dirección principal</strong></p></td>
                                <td><p>{{$client->address}}</p></td>
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
            {{-- Productos contratados --}}
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
                        <p><strong>+Adicionales</strong></p>
                        <p><strong>BASE GRAVABLE</strong></p>
                        <p><strong>+IVA</strong></p>
                        <p><strong>NETO COTIZACION </strong></p>
                    </td>
                    <td class="right-client-data-invoice-value table-padding-purchase">
                        <p>${{$productsPurchase['total_product']}}</p>
                        <p>$0,00</p>
                        <p>$0,00</p>
                        <p>${{$productsPurchase['total_tax_product']}}</p>
                        <p>$0,00</p>
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