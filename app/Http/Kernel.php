<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,


    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'role.user' => [
            \App\Http\Middleware\Auth\Role\RouteMiddleware::class,
            \App\Http\Middleware\Auth\Role\RoleMiddleware::class
        ]
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        //AUTH
        'data.login' => \App\Http\Middleware\Auth\DataLogin::class,
        'data.change.password' => \App\Http\Middleware\Auth\ChangePassword::class,
        //FORMS
        'enterprise.parameterization' => \App\Http\Middleware\CRUD\EnterpriseParameterization\EnterpriseParameterization::class,
        'office.parameterization' => \App\Http\Middleware\CRUD\OfficeParameterization\OfficeParameterization::class,
        'user.parameterization' => \App\Http\Middleware\CRUD\UserParameterization\UserParameterization::class,
        'role.parameterization' => \App\Http\Middleware\CRUD\RoleParameterization\RoleParameterization::class,
        'client.parameterization' => \App\Http\Middleware\CRUD\ClientParameterization\ClientParameterization::class,
        'service.parameterization' => \App\Http\Middleware\CRUD\ServiceParameterization\ServiceParameterization::class,
        'field.parameterization' => \App\Http\Middleware\CRUD\FieldParameterization\FieldParameterization::class,
        'supplier.parameterization' => \App\Http\Middleware\CRUD\SupplierParameterization\SupplierParameterization::class,
        'warehouse.parameterization' => \App\Http\Middleware\CRUD\WarehouseParameterization\WarehouseParameterization::class,
        'measure.parameterization' => \App\Http\Middleware\CRUD\MeasureParameterization\MeasureParameterization::class,
        'brand.parameterization' => \App\Http\Middleware\CRUD\BrandParameterization\BrandParameterization::class,
        'category.parameterization' => \App\Http\Middleware\CRUD\CategoryParameterization\CategoryParameterization::class,
        'inventory.parameterization' => \App\Http\Middleware\CRUD\InventoryParameterization\InventoryParameterization::class,
        'product.parameterization' => \App\Http\Middleware\CRUD\ProductParameterization\ProductParameterization::class,
        'employee.parameterization' => \App\Http\Middleware\CRUD\EmployeeParameterization\EmployeeParameterization::class,
        'invoice.parameterization' => \App\Http\Middleware\CRUD\InvoiceParameterization\InvoiceParameterization::class,
        'tax.parameterization' => \App\Http\Middleware\CRUD\TaxParameterization\TaxParameterization::class,
        'purchase.order.parameterization' => \App\Http\Middleware\CRUD\PurchaseOrderParameterization\PurchaseOrderParameterization::class,
        'libretto.activity.parameterization' => \App\Http\Middleware\CRUD\LibrettoActivityParameterization\LibrettoActivityParameterization::class,
        'tax.value.parameterization' => \App\Http\Middleware\CRUD\TaxValueParameterization\TaxValueParameterization::class,
        'payment.parameterization' => \App\Http\Middleware\CRUD\PaymentParameterization\PaymentParameterization::class,
        'payment.method.parameterization' => \App\Http\Middleware\CRUD\PaymentMethodParameterization\PaymentMethodParameterization::class,
        'charge.parameterization' => \App\Http\Middleware\CRUD\ChargeParameterization\ChargeParameterization::class,
        //INVOKES
        'validate.name.invoke' => \App\Http\Middleware\Invokes\ValidateNameParameter::class,
    ];
}

