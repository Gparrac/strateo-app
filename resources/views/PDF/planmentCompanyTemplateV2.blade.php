<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orden de planeación - Empresa</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice-templates.css') }}">

</head>

<body class="px-20">
    @php
        use App\Http\Utils\PriceFormat;
    @endphp
    {{-- <img src="{{public_path('images/joyFactory.png')}}"a alt="" class="icon"> --}}
    @if ($dataPDF['path_logo'])
    <img src="{{public_path($dataPDF['path_logo2'])}}" alt="icon" class="icon">


    @endif
    <div class="w-100" >
        <h1 class="text-center w-60 mx-auto" >
            {{ $dataPDF['header'] }}
            {{$dataPDF['type_document'] . ': ' . $dataPDF['identification'] . 'IMPUESTO SOBRE EL VALOR DE LA VENTA IVA' }}<br>
            {{'Actividad Económica ' . $dataPDF['third']['ciiu']['code']. ' Tarifa Renta 0.80%'}}
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
                                <td class="w-30 text-right pr-40">{{ $planment['end_date'] }} </td>
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


        <!-- start  products section -->
        <div class="my-20">
            @if ($products && count($products) > 0)
                <h4 class="text-center py-10">Detalles de planeación</h4>
                <table class="w-100">
                    <thead>
                        <tr>
                            <th class="text-left">Producto</th>
                            <th class="text-center">Presentación</th>
                            <th class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="w-30 text-left">
                                    <span class="block">
                                        {{ $product['id']. ' - '. $product['name'] }}
                                    </span>
                                    <span class="block">
                                        Consecutivo: {{$product['consecutive']}}
                                    </span>
                                </td>

                                <td class="w-30 text-left">
                                    <span class="block">
                                        - {{$product['brand']['name']}}
                                    </span><span class="block">
                                        {{$product['size']}} {{$product['measure']['symbol']}}
                                    </span>
                                </td>


                                <td class="w-30 text-right">
                                  <table class="w-100">
                                    @foreach ($product['events'] as $event)
                                    <tr>
                                      <td class="w-80 text-left">- {{$event['name']}}</td>
                                      <td class="w-20 text-right"> {{$event['amount']}} </th>
                                    </tr>
                                    @endforeach
                                  </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <!-- end  products section -->
                <!-- start  la section -->
                <div class="my-20">
                  @if ($las && count($las) > 0)
                      <h4 class="text-center py-10">Libretro de actividades</h4>
                      <table class="w-100">
                          <thead>
                              <tr>
                                  <th class="text-left">Evento</th>
                                  <th class="text-center">Descripción</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ($las as $la)
                                  <tr>
                                      <td class="w-30 text-left">{{ $la['id']. ' - '. $la['name'] }}</td>
                                      <td class="w-20 text-left">{{ $la['description'] }}</td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>
                  @endif
              </div>
              <!-- end  la section -->
                              <!-- start  la section -->
                              <div class="my-20">
                                @if ($employees && count($employees) > 0)
                                    <h4 class="text-center py-10">Empleados</h4>
                                    <table class="w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Trabajador</th>
                                                <th class="text-center">Cargo</th>
                                                <th class="text-center">Metodo de pago</th>
                                                <th class="text-center">Referencia</th>
                                                <th class="text-right">Salario</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employees as $employee)
                                                <tr>
                                                    <td class="w-30 text-left">
                                                        <span class="block text-lg">{{ $employee['id']. ' - '. $employee['fullname'] }}</span>
                                                        <span class="block">{{$employee['identification']}}</span>

                                                    </td>
                                                    <td class="w-30 text-left">
                                                        @foreach($employee['charges'] as $charge)
                                                        <span>- {{$charge['name']}}</span><br>
                                                        @endforeach
                                                    </td>
                                                    <td class="w-10 text-center">
                                                        {{$employee['payment_method']}}
                                                    </td>
                                                    <td class="w-10 text-center">
                                                        {{$employee['reference']}}
                                                    </td>
                                                    <td class="w-10 text-right">{{ PriceFormat::getNumber($employee['salary']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                            <!-- end  la section -->
        </div>
        <!-- total amount section -->
        <div class="w-100 mt-10 border-2">

                    <h5 class="pt-5 px-5">Observaciones</h5>
                    <p class="pt-5 px-5 pb-10" >{{$invoice['note'] ?? 'No presenta.'}}</p>

        </div>
        <!-- end total amount section -->

    </div>
    <footer>{{ $dataPDF['footer'] }}</footer>
</body>

</html>
