<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hola?</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice-templates.css') }}">
</head>

<body class="px-20">
    @php
        use App\Http\Utils\PriceFormat;
    @endphp
    <img src="{{public_path('images/joyFactory.png')}}"a alt="" class="icon">
    <div class="w-100" >
        <h1 class="text-center w-60 mx-auto" style="margin: 0 auto;">
            {{ $dataPDF['header'] }}
            {{$dataPDF['type_document'] . ': ' . $dataPDF['identification'] . 'IMPUESTO SOBRE EL VALOR DE LA VENTA IVA' }}<br>
            {{'Actividad Económica' . '####'. ' Tarifa Renta 0.80%'}}
            {{$dataPDF['address'] . ' - '. $dataPDF['third']['city']['name'] }}<br>
            {{'Teléfono: ' . $dataPDF['third']['mobile']}}<br>
            No Somos Autorretenedores - No somos Grandes Contribuyentes
            Régimen IVA: IMPUESTO SOBRE LAS VENTAS - IVA Actividad ICA: 304 9.60 x mil

        </h1>
        <!-- start invoice data section -->

        <table class="w-100 border-2 my-20">
            <tr>
                <!-- observations -->
                <td class="w-70 py-10 pl-10">
                    <table class="w-100">
                        <tr>
                            <td colspan="4" class="text-md2 text-bold">
                                Datos de la venta
                            </td>
                        </tr>
                        <tr>
                            <td class="w-20 text-bold ">Cliente</td>
                            <td class="w-30 text-right pr-40">{{ $invoice['client']['third']['fullname'] }}</td>
                            <td class="w-20 text-bold ">Fecha</td>
                            <td class="w-30 text-right pr-40">{{ $invoice['date'] }} </td>
                        </tr>
                        <tr>
                            <td class="w-20 text-bold ">Celular</td>
                            <td class="w-30 text-right pr-40">{{ $invoice['client']['third']['mobile'] }} </td>
                            <td class="w-20 text-bold ">ciudad</td>
                            <td class="w-30 text-right pr-40">{{ $invoice['client']['third']['city']['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="w-20 text-bold ">C.C o NIT</td>
                            <td class="w-30 text-right pr-40">{{ $invoice['client']['third']['identification'] }}</td>
                            <td class="w-20 text-bold ">Dirección principal</td>
                            <td class="w-30 text-right pr-40">{{ $invoice['client']['third']['address'] }} </td>
                        </tr>
                        <tr>
                            <td class="w-20 text-bold ">Vendedor</td>
                            <td class="w-30 text-right pr-40">{{ $invoice['seller']['third']['fullname'] }}</td>
                        </tr>
                        @if ($invoice['sale_type']['id'] == 'E')
                            <tr>
                                <td colspan="4" class="text-md2 text-bold">
                                    Datos del evento
                                </td>
                            </tr>
                            <tr>
                                <td class="w-20 text-bold ">Inicio</td>
                                <td class="w-30 text-right pr-40">{{ $planment['start_date'] }}</td>
                                <td class="w-20 text-bold ">Finalización</td>
                                <td class="w-30 text-right pr-40">{{ $planment['start_date'] }} </td>
                            </tr>
                            <tr>
                                <td class="w-20 text-bold ">Abono</td>
                                <td class="w-30 text-right pr-40">{{ $planment['pay_off'] }} </td>
                            </tr>
                        @endif
                    </table>

                </td>
                <td class="w-30 text-center border-2">
                    <h5 class="text-lg">cotización:</h5>

                    <p class="text-xl">#{{$invoice['id']}}</p>
                </td>
            </tr>
        </table>
        <!-- end invoice data section -->


        <!-- start further products section -->
        <div class="my-20">
            @if ($products && count($products) > 0)
                <h4 class="text-center py-10">{{ $titlePDF }}</h4>
                <table class="w-100">
                    <thead>
                        <tr>
                            <th class="text-left">Producto</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio (u)</th>
                            <th class="text-right">Descuento</th>
                            <th class="text-right">Impuestos</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="w-30 text-left">{{ $product['product']['id']. ' - '. $product['product']['name'] }}</td>
                                <td class="w-20 text-right">{{ $product['amount'] }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['cost']) }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['discount']) }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['total_tax_product']) }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="my-20">
            @if ($furtherProducts && count($furtherProducts) > 0)
                <!-- end further products section -->
                <h4 class="text-center py-10">Productos adicionales</h4>
                <!-- start further products section -->
                <table class="w-100">
                    <thead>
                        <tr>
                            <th class="text-left">Producto</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio (u)</th>
                            <th class="text-right">Descuento</th>
                            <th class="text-right">Impuestos</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($furtherProducts as $product)
                            <tr>
                                <td class="w-30 text-left">{{ $product['product']['id']. ' - '. $product['product']['name'] }}</td>
                                <td class="w-20 text-right">{{ $product['amount'] }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['cost']) }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['discount']) }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['total_tax_product']) }}</td>
                                <td class="w-20 text-right">{{ PriceFormat::getNumber($product['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- end further products section -->

        <!-- start taxes section -->
        <div class="my-20">
            @if ($headTaxes && count($headTaxes) > 0)
                <h4 class="text-center py-10">Impuestos</h4>
                @foreach ($headTaxes as $index => $tax)
                    <div class="w-100 py-20">
                        <h5>{{ $tax['name'] }} ({{ $tax['acronym'] }})</h5>

                        <table class="w-100">
                            <thead>
                                <tr class="content-table-font-size">
                                    <th class="text-left">Producto</th>
                                    <th class="text-right">Porcentaje</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productTaxes[$index] as $product)
                                    <tr>
                                        <td class="w-50 text-left">{{$product['id'] . ' - ' . $product['name'] }}</td>
                                        <td class="w-20 text-right">{{ $product['percent'] }}</td>
                                        <td class="w-30 text-right">
                                            {{ PriceFormat::getNumber($product['total']) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                @endforeach
            @endif
            <!-- end taxes section -->
        </div>
        <!-- total amount section -->
        <table class="w-100 mt-10 border-2">
            <tr>
                <!-- observations -->
                <td class="w-70 pl-10 py-10">
                    <h5 class="">Observaciones</h5>
                    <p >{{$invoice['note'] ?? 'No presenta.'}}</p>
                </td>
                <td class="w-30 align-top py-10 border-2">
                    <table class="w-100 ">
                        <tr>
                            <!-- label -->
                            <td class="w-40 text-bold pl-10">- Subtotal</td>
                            <!-- content -->
                            <td class="w-60 text-right pr-10">{{PriceFormat::getNumber($productsPurchase['total_product'])}}</td>
                        </tr>
                        <tr>
                            <!-- label -->
                            <td class="w-40 text-bold pl-10">- Impuestos </td>
                            <!-- content -->
                            <td class="w-60 text-right pr-10">{{PriceFormat::getNumber($productsPurchase['total_tax_product'])}}</td>
                        </tr>
                        @foreach ($invoice['taxes'] as $tax)
                        <tr>
                            <!-- label -->
                            <td class="w-40 text-bold pl-10">- {{$tax['acronym']}}</td>
                            <!-- content -->
                            <td class="w-60 text-right pr-10">{{PriceFormat::getNumber($tax['total'])}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <!-- label -->
                            <td class="w-40 text-bold pl-10">- Total neto</td>
                            <!-- content -->
                            <td class="w-60 text-right pr-10">{{PriceFormat::getNumber($productsPurchase['total_purchase'])}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- end total amount section -->

    </div>
    <footer>{{ $dataPDF['footer'] }}</footer>
</body>

</html>
