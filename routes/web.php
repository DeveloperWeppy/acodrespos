<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//comandos
Route::get('storage-link', function () {
    Artisan::call('storage:link');
});

Route::get('/', 'FrontEndController@index')->name('front');
Route::get('/qrorder/{id?}', 'QRController@showOrder')->name('qrorder');
Route::get('/orderstatus/{restorant}/{id?}', 'QRController@orders')->name('orderstatus');
Route::get('/qrsms', 'QRController@qrsms')->name('qrsms');

Route::get('/'.config('settings.url_route').'/{alias}', 'FrontEndController@restorant')->name('vendor');
Route::get('/city/{city}', 'FrontEndController@showStores')->name('show.stores');
Route::get('/lang', 'FrontEndController@langswitch')->name('lang.switch');

Route::post('/search/location', 'FrontEndController@getCurrentLocation')->name('search.location');

Route::post('/geozonedelivery/store', 'GeoZoneDeliveryController@store')->name('geozone.store');
Route::post('/geozonedelivery/updated/{id?}', 'GeoZoneDeliveryController@updated')->name('geozone.updated');

Route::get('/geozonedelivery/destroy/{id?}', 'GeoZoneDeliveryController@destroy')->name('geozone.destroy');
Route::get('/geozonedelivery/get', 'GeoZoneDeliveryController@get')->name('geozone.get');
Route::get('/geozonedelivery/getgeo', 'GeoZoneDeliveryController@getgeo')->name('geozone.getgeo');

Auth::routes(['register' => config('app.isft')]);

Route::get('/pdf/{id?}/{tipo?}', 'PdfController@get')->name('pdf');

Route::get('/selectpay/{order}', 'PaymentController@selectPaymentGateway')->name('selectpay');
Route::get('/selectedpaymentt/{order}/{payment}', 'PaymentController@selectedPaymentGateway')->name('selectedpaymentt');

//route of pqr
Route::get('/diligenciar-solicitud', 'PqrsController@index')->name('pqrs.index');
Route::post('pqrs-guardar', 'PqrsController@store')->name('pqrs.store');
Route::get('centro-ayuda/confirmacion-solicitud/{consecutive_case}', 'PqrsController@confirmacion')->name('pqrs.confirmacion');
Route::post('/pqrs-acceso/validar', 'PqrsController@validate_acces')->name('pqrs.validate_acces');
Route::get('/pqrs-acceso/validacion/{consecutive_case}', 'PqrsController@validacion')->name('pqrs.validateaccespqr');
/* Route::get('/pqrs-acceso/validacion', function () {
    return view('pqrs.includes.validateacces');
})->name('pqrs.'); */

    Route::get('/detalle-solicitud/{consecutive_case}', 'PqrsController@detalle')->middleware('validateAccessPqr')->name('pqrs.detalle');
    Route::get('/detalle-solicitud-radicada/{consecutive_case}', 'PqrsController@detalle_radicada')->middleware('okaccespqr')->name('pqrs.detalle_radicada');

Route::group(['middleware' => ['auth','impersonate']], function () {
    Route::group(['middleware' => ['ChangePassword']], function () {
        Route::get('/home/{lang?}', 'HomeController@index')->name('home')->middleware(['isOwnerOnPro','verifiedSetup']);

        Route::post('/home/graficos', 'HomeController@graficos')->name('home.graficos');

        Route::resource('user', 'UserController', ['except' => ['show']]);
        Route::post('/user/push', 'UserController@checkPushNotificationId');

        Route::name('admin.')->group(function () {
            Route::get('syncV1UsersToAuth0', 'SettingsController@syncV1UsersToAuth0')->name('syncV1UsersToAuth0');
            Route::get('encuesta', 'EncuestaOrdenController@index')->name('encuesta.index');
            Route::post('encuesta/crear', 'EncuestaOrdenController@store')->name('encuesta.store');
            Route::post('encuesta/actualizar', 'EncuestaOrdenController@update')->name('encuesta.update');
            Route::get('encuesta/delete/{id}', 'EncuestaOrdenController@destroy')->name('encuesta.destroy');
            Route::get('dontsyncV1UsersToAuth0', 'SettingsController@dontsyncV1UsersToAuth0')->name('dontsyncV1UsersToAuth0');
            Route::resource(config('settings.url_route_plural'), 'RestorantController',[
                'names' => [
                    'index' => 'restaurants.index',
                    'store' => 'restaurants.store',
                    'edit' => 'restaurants.edit',
                    'create' => 'restaurants.create',
                    'destroy' => 'restaurants.destroy',
                    'update' => 'restaurants.update',
                    'show' => 'restaurants.show'
                ]
            ]);
            Route::put('restaurants_app_update/{restaurant}', 'RestorantController@updateApps')->name('restaurant.updateApps');

            Route::get('restaurants_add_new_shift/{restaurant}', 'RestorantController@addnewshift')->name('restaurant.addshift');

            Route::get('restaurants/loginas/{restaurant}', 'RestorantController@loginas')->name('restaurants.loginas');
            Route::get('stopimpersonate', 'RestorantController@stopImpersonate')->name('restaurants.stopImpersonate');
            

            Route::get('removedemodata', 'RestorantController@removedemo')->name('restaurants.removedemo');
            Route::get('sitemap','SettingsController@regenerateSitemap')->name('regenerate.sitemap');

            // Landing page settings 
            Route::get('landing', 'SettingsController@landing')->name('landing');
            Route::prefix('landing')->name('landing.')->group(function () {
                Route::get('posts/{type}', 'CRUD\PostsController@index')->name('posts');
                Route::get('posts/{type}/create', 'CRUD\PostsController@create')->name('posts.create');
                Route::post('posts/{type}', 'CRUD\PostsController@store')->name('posts.store');
            
                Route::get('posts/edit/{post}', 'CRUD\PostsController@edit')->name('posts.edit');
                Route::put('posts/{post}', 'CRUD\PostsController@update')->name('posts.update');
                Route::get('posts/del/{post}', 'CRUD\PostsController@destroy')->name('posts.delete');

                Route::resource('features', 'FeaturesController');
                Route::get('/features/del/{feature}', 'FeaturesController@destroy')->name('features.delete');

                Route::resource('testimonials', 'TestimonialsController');
                Route::get('/testimonials/del/{testimonial}', 'TestimonialsController@destroy')->name('testimonials.delete');

                Route::resource('processes', 'ProcessController');
                Route::get('/processes/del/{process}', 'ProcessController@destroy')->name('processes.delete');
            });

        

            Route::resource('allergens', 'CRUD\AllergensController');
            Route::get('/allergens/del/{allergen}', 'CRUD\AllergensController@destroy')->name('allergens.delete');

            Route::name('restaurant.')->group(function () {

                //Remove restaurant
                Route::get('removerestaurant/{restaurant}', 'RestorantController@remove')->name('remove');

                // Tables
                Route::get('tables', 'TablesController@index')->name('tables.index')->middleware('isOwnerOnPro');
                Route::get('tables/{table}/edit', 'TablesController@edit')->name('tables.edit');
                Route::get('tables/create', 'TablesController@create')->name('tables.create');
                Route::post('tables', 'TablesController@store')->name('tables.store');
                Route::put('tables/{table}', 'TablesController@update')->name('tables.update');
                Route::get('tables/del/{table}', 'TablesController@destroy')->name('tables.delete');

                // Delivery areas
                Route::get('simpledelivery', 'SimpleDeliveryController@index')->name('simpledelivery.index')->middleware('isOwnerOnPro');
                Route::get('simpledelivery/{delivery}/edit', 'SimpleDeliveryController@edit')->name('simpledelivery.edit');
                Route::get('simpledelivery/create', 'SimpleDeliveryController@create')->name('simpledelivery.create');
                Route::post('simpledelivery', 'SimpleDeliveryController@store')->name('simpledelivery.store');
                Route::put('simpledelivery/{delivery}', 'SimpleDeliveryController@update')->name('simpledelivery.update');
                Route::get('simpledelivery/del/{delivery}', 'SimpleDeliveryController@destroy')->name('simpledelivery.delete');


                

                // Areas
                Route::resource('restoareas', 'RestoareasController');
                Route::get('restoareas/del/{restoarea}', 'RestoareasController@destroy')->name('restoareas.delete');

                // Areas
                Route::resource('visits', 'VisitsController');
                Route::get('visits/del/{visit}', 'VisitsController@destroy')->name('visits.delete');

                //Coupons
                Route::get('coupons', 'CouponsController@index')->name('coupons.index');
                Route::get('coupons/{coupon}/edit', 'CouponsController@edit')->name('coupons.edit');
                Route::get('coupons/create', 'CouponsController@create')->name('coupons.create');
                Route::post('coupons', 'CouponsController@store')->name('coupons.store');
                Route::put('coupons/{coupon}', 'CouponsController@update')->name('coupons.update');
                Route::get('coupons/del/{coupon}', 'CouponsController@destroy')->name('coupons.delete');
                Route::get('coupons/createDiscount', 'CouponsController@createDiscount')->name('coupons.createDiscount');
                Route::post('coupons/storeDiscount', 'CouponsController@storeDiscount')->name('coupons.storeDiscount');
                Route::get('coupons/{coupon}/editDiscount', 'CouponsController@editDiscount')->name('coupons.editDiscount');
                Route::post('coupons/{coupon}/updateDiscount', 'CouponsController@updateDiscount')->name('coupons.updateDiscount');
                Route::get('coupons/deleteDiscount/{coupon}', 'CouponsController@destroyDiscount')->name('coupons.deleteDiscount');
            

                //Banners
                Route::get('banners', 'BannersController@index')->name('banners.index');
                Route::get('banners/{banner}/edit', 'BannersController@edit')->name('banners.edit');
                Route::get('banners/create', 'BannersController@create')->name('banners.create');
                Route::post('banners', 'BannersController@store')->name('banners.store');
                Route::put('banners/{banner}', 'BannersController@update')->name('banners.update');
                Route::get('banners/del/{banner}', 'BannersController@destroy')->name('banners.delete');

                //Language menu
                Route::post('storenewlanguage', 'RestorantController@storeNewLanguage')->name('storenewlanguage');
            });
        });
        //------------------------------------------------------------------------------------//
        #routes for settings banks account
        Route::prefix('configuracioncuenta')->name('configuracioncuenta.')->group(function () {
            Route::post('guardar', 'ConfigCuentasBancariasController@store')->name('store');
            Route::get('/del/{id}', 'ConfigCuentasBancariasController@destroy')->name('delete');
            Route::post('obtener', 'ConfigCuentasBancariasController@geInfoCuentas')->name('obtener');
        });

        #routes for file PQR
        Route::prefix('pqrs')->name('pqrs.')->group(function () {
            Route::get('/', 'PqrsController@index_admin')->name('index_admin');
            Route::get('/detalle-pqr/{id}', 'PqrsController@show')->name('show');
            Route::post('/updateestadopqr', 'PqrsController@updateStatus')->name('updateStatus');
            Route::post('/store-respuesta', 'PqrsController@storeRespuesta')->name('storeRespuesta');
        });

        #routes for file LOGS
        Route::prefix('auditoria')->name('logs.')->group(function () {
            Route::get('/', 'LogController@index')->name('index');
        });

        #routes for config of reservations
        Route::prefix('reservas')->name('reservation.')->group(function () {
            Route::get('/', 'ConfigReservationController@index')->name('index');
            Route::post('obtener-mesas', 'ConfigReservationController@geInfoMesas')->name('obtener');
            Route::post('guardar', 'ConfigReservationController@store')->name('store');
            Route::post('storeConfig', 'ConfigReservationController@storeConfig')->name('storeConfig');
            Route::post('getInfoConfig', 'ConfigReservationController@getInfoConfig')->name('getInfoConfig');
            Route::get('create', 'ConfigReservationController@create')->name('create');
            Route::post('store', 'ConfigReservationController@store')->name('store');
            Route::post('storePendiente', 'ConfigReservationController@storePendiente')->name('storePendiente');
            Route::post('storeUpdate', 'ConfigReservationController@storeUpdate')->name('storeUpdate');
            Route::post('getOcupation', 'ConfigReservationController@getOcupation')->name('getOcupation');
            Route::post('getTables', 'ConfigReservationController@getTables')->name('getTables');
            Route::get('edit/{id}', 'ConfigReservationController@edit')->name('edit');
            Route::post('inactiveReservation', 'ConfigReservationController@inactiveReservation')->name('desabilitarReserva');
            Route::post('configRestaurant', 'ConfigReservationController@configRestaurant')->name('configRestaurant');
            Route::get('getHours', 'ConfigReservationController@getHours')->name('getHours');
            Route::get('editsolicitud/{id}', 'ConfigReservationController@editsolicitud')->name('editsolicitud');
            
        });

        #routes for reason of reservations
        Route::prefix('reservas/motivos/')->name('reservationreason.')->group(function () {
            Route::post('guardar', 'ReservationReasonController@store')->name('store');
            Route::get('obtener', 'ReservationReasonController@cargarMotivos')->name('obtener');
            Route::get('del/{id}', 'ReservationReasonController@destroy')->name('delete');
            Route::post('getMotivos', 'ReservationReasonController@getMotivos')->name('getMotivos');
        });
        // --------------------------------------------------------------------------------- //

        Route::resource('cities', 'CitiesController');
        Route::get('/cities/del/{city}', 'CitiesController@destroy')->name('cities.delete');

        Route::post('/updateres/location/{restaurant}', 'RestorantController@updateLocation');
        Route::post('/updateres/radius/{restaurant}', 'RestorantController@updateRadius');
        Route::post('/updateres/delivery/{restaurant}', 'RestorantController@updateDeliveryArea');
        Route::post('/import/restaurants', 'RestorantController@import')->name('import.restaurants');
        Route::get('/restaurant/{restaurant}/activate', 'RestorantController@activateRestaurant')->name('restaurant.activate');
        Route::post('/restaurant/workinghours', 'RestorantController@workingHours')->name('restaurant.workinghours');
        Route::get('restaurants/working_hours/remove/{hours}','RestorantController@workingHoursremove')->name('restaurant.workinghoursremove');
        Route::post('/restaurant/address','RestorantController@getCoordinatesForAddress')->name('restaurant.coordinatesForAddress');

        Route::prefix('finances')->name('finances.')->group(function () {
            Route::get('admin', 'FinanceController@adminFinances')->name('admin');
            Route::get('owner', 'FinanceController@ownerFinances')->name('owner');
        });

        Route::prefix('stripe')->name('stripe.')->group(function () {
            Route::get('connect', 'FinanceController@connect')->name('connect');
        });

        Route::resource('reviews', 'ReviewsController');
        Route::get('/reviewsdelete/{rating}', 'ReviewsController@destroy')->name('reviews.destroyget');

        Route::resource('drivers', 'DriverController');
        Route::get('/driver/{driver}/activate', 'DriverController@activateDriver')->name('driver.activate');
        Route::get('/nearest_driver/','DriverController@getNearestDrivers')->name('drivers.nearest');
        Route::get('/driver/eloquent', 'DriverController@eloquent')->name('driver.eloquent');



        Route::resource('clients', 'ClientController');

        Route::resource('orders', 'OrderController');
        Route::get('/listclients/{tipo?}', 'ClientController@listclients')->name('clients.list');
        Route::get('/notificacion/{index?}', 'OrderController@notificacion')->name('order.notificacion');
        Route::post('/rating/{order}', 'OrderController@rateOrder')->name('rate.order');
        Route::post('/orders/status', 'OrderController@statusitemorder')->name('itemcart.status');
        Route::get('/orders/status2/{id}', 'OrderController@statusitemorder2')->name('itemcart.status2');
        Route::get('/check/rating/{order}', 'OrderController@checkOrderRating')->name('check.rating');

        Route::get('ordertracingapi/{order}', 'OrderController@orderLocationAPI');
        Route::get('liveapi', 'OrderController@liveapi');
        Route::get('driverlocations', 'DriverController@driverlocations');
        Route::get('restaurantslocations', 'RestorantController@restaurantslocations');

        Route::get('live', 'OrderController@live')->name('live')->middleware('isOwnerOnPro');
        Route::get('/updatestatus/{alias}/{order}/{motivo?}', ['as' => 'update.status', 'uses'=>'OrderController@updateStatus']);

        Route::resource('settings', 'SettingsController');
        Route::get('apps','AppsController@index')->name('apps.index');
        Route::get('appremove/{alias}','AppsController@remove')->name('apps.remove');
        Route::post('apps','AppsController@store')->name('apps.store');
        Route::get('cloudupdate', 'SettingsController@cloudupdate')->name('settings.cloudupdate');
        Route::get('systemstatus', 'SettingsController@systemstatus')->name('systemstatus');
        Route::get('translatemenu', 'SettingsController@translateMenu')->name('translatemenu');

        Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
        Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
        Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

        Route::resource('items', 'ItemsController')->middleware('isOwnerOnPro');
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('reorder/{up}', 'ItemsController@reorderCategories')->name('reorder');
            Route::get('list/{restorant}', 'ItemsController@indexAdmin')->name('admin');

            // Options
            Route::get('options/{item}', 'Items\OptionsController@index')->name('options.index');
            Route::get('options/{option}/edit', 'Items\OptionsController@edit')->name('options.edit');
            Route::get('options/{item}/create', 'Items\OptionsController@create')->name('options.create');
            Route::post('options/{item}', 'Items\OptionsController@store')->name('options.store');
            Route::put('options/{option}', 'Items\OptionsController@update')->name('options.update');
            Route::get('options/del/{option}', 'Items\OptionsController@destroy')->name('options.delete');

            // Variants
            Route::get('variants/{item}', 'Items\VariantsController@index')->name('variants.index');
            Route::get('variants/{variant}/edit', 'Items\VariantsController@edit')->name('variants.edit');
            Route::get('variants/{item}/create', 'Items\VariantsController@create')->name('variants.create');
            Route::post('variants/{item}', 'Items\VariantsController@store')->name('variants.store');
            Route::put('variants/{variant}', 'Items\VariantsController@update')->name('variants.update');

            Route::get('variants/del/{variant}', 'Items\VariantsController@destroy')->name('variants.delete');
        });

        Route::post('/import/items', 'ItemsController@import')->name('import.items');
        Route::post('/item/change/{item}', 'ItemsController@change');
        Route::post('/{item}/extras', 'ItemsController@storeExtras')->name('extras.store');
        Route::post('/{item}/extras/edit', 'ItemsController@editExtras')->name('extras.edit');
        Route::delete('/{item}/extras/{extras}', 'ItemsController@deleteExtras')->name('extras.destroy');

        Route::resource('categories', 'CategoriesController');
        Route::post('/areakitchen', 'CategoriesController@storeareakitchen')->name('categories.storeareakitchen');

        Route::resource('addresses', 'AddressControler');
        Route::get('/new/address/autocomplete', 'AddressControler@newAddressAutocomplete');
        Route::post('/new/address/details', 'AddressControler@newAdressPlaceDetails');
        Route::post('/address/delivery', 'AddressControler@AddressInDeliveryArea');

        Route::post('/change/{page}', 'PagesController@change')->name('changes');

        Route::post('ckeditor/image_upload', 'CKEditorController@upload')->name('upload');
        Route::get('/payment', 'PaymentController@view')->name('payment.view');

        if (config('app.isft')) {
            Route::get('/cart-checkout', 'CartController@cart')->middleware('verifiedphone')->name('cart.checkout');
        }

        Route::resource('plans', 'PlansController');
        Route::get('/plan', 'PlansController@current')->name('plans.current');
        Route::post('/subscribe/plan', 'PlansController@subscribe')->name('plans.subscribe');
        Route::get('/subscribe/cancel', 'PlansController@cancelStripeSubscription')->name('plans.cancel');
        Route::get('/subscribe/plan3d/{plan}/{user}', 'PlansController@subscribe3dStripe')->name('plans.subscribe_3d_stripe');
        Route::post('/subscribe/update', 'PlansController@adminupdate')->name('update.plan');

        Route::get('qr', 'QRController@index')->name('qr');

        
        Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
        Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

        Route::get('/share/menu', 'RestorantController@shareMenu')->name('share.menu');
        Route::get('/downloadqr', 'RestorantController@downloadQR')->name('download.menu');
    });
    //Route::get('/cambiar-clave', 'HomeController@changepasswordd')->name('changepasswordd');
    Route::get('/cambiar-clave', function () {
        return view('auth.changepassword');
    })->name('changepasswordd');
    Route::post('/cambiar-clave/update', 'UserController@passwordfirst')->name('passwordfirst.update');
});

if (config('app.isqrsaas')) {
    Route::get('/cart-checkout', 'CartController@cart')->name('cart.checkout');
    Route::get('/guest-orders', 'OrderController@guestOrders')->name('guest.orders');
    Route::post('/whatsapp/store', 'OrderController@storeWhatsappOrder')->name('whatsapp.store');
}

Route::post('coupons/apply', 'CouponsController@apply')->name('coupons.apply');

Route::get('/handleOrderPaymentStripe/{order}', 'PaymentController@handleOrderPaymentStripe')->name('handle.order.payment.stripe');

Route::get('/get/rlocation/{restaurant}', 'RestorantController@getLocation');
Route::get('/footer-pages', 'PagesController@getPages');
Route::get('/cart-getContent', 'CartController@getContent')->name('cart.getContent');
Route::get('/cart-getContent-POS', 'CartController@getContentPOS')->name('cart.getContentPOS');
Route::post('/cart-add', 'CartController@add')->name('cart.add');
Route::post('/cart-remove', 'CartController@remove')->name('cart.remove');
Route::get('/cart-update', 'CartController@update')->name('cart.update');
Route::get('/cartinc/{item}/{orderId?}', 'CartController@increase')->name('cart.increase');
Route::get('/cartdec/{item}/{orderId?}', 'CartController@decrease')->name('cart.decrease');
Route::post('/updataObser', 'CartController@updateCartObser')->name('cart.updataObser');

Route::post('/order', 'OrderController@store')->name('order.store');

Route::resource('pages', 'PagesController');
Route::get('/blog/{slug}', 'PagesController@blog')->name('blog');

Route::get('/login/google', 'Auth\LoginController@googleRedirectToProvider')->name('google.login');
Route::get('/login/google/redirect', 'Auth\LoginController@googleHandleProviderCallback');

Route::get('/login/facebook', 'Auth\LoginController@facebookRedirectToProvider')->name('facebook.login');
Route::get('/login/facebook/redirect', 'Auth\LoginController@facebookHandleProviderCallback');

Route::get('/new/'.config('settings.url_route').'/register', 'RestorantController@showRegisterRestaurant')->name('newrestaurant.register');
Route::post('/new/restaurant/register/store', 'RestorantController@storeRegisterRestaurant')->name('newrestaurant.store');


Route::get('phone/verify', 'PhoneVerificationController@show')->name('phoneverification.notice');
Route::post('phone/verify', 'PhoneVerificationController@verify')->name('phoneverification.verify');

Route::get('/get/rlocation/{restorant}', 'RestorantController@getLocation');
Route::get('/items/variants/{variant}/extras', 'Items\VariantsController@extras')->name('items.variants.extras');


//Languages routes
$availableLanguagesENV = ENV('FRONT_LANGUAGES', 'EN,English,IT,Italian,FR,French,DE,German,ES,Spanish,RU,Russian,PT,Portuguese,TR,Turkish,ar,Arabic');
$exploded = explode(',', $availableLanguagesENV);
if (count($exploded) > 3) {

    $mode="qrsaasMode";
    if(config('settings.landing_to_use')!="system"){
        if(config('settings.landing_to_use')=="whatsapp"){
            $mode="whatsappMode";
        }else if(config('settings.landing_to_use')=="pos"){
            $mode="posMode";
        }
    }else{
        if(config('settings.is_whatsapp_ordering_mode')){
            $mode="whatsappMode";
        }
        if(config('settings.is_pos_cloud_mode')){
            $mode="posMode";
        }
    }
    if(config('app.isft')){
        $mode="index";
    }

    for ($i = 0; $i < count($exploded); $i += 2) {
        
        Route::get('/'.strtolower($exploded[$i]), 'FrontEndController@'.$mode)->name('lang.'.strtolower($exploded[$i]));
    }
}

Route::get('register/visit/{restaurant_id}', 'VisitsController@register')->name('register.visit');
Route::post('register/visit', 'VisitsController@registerstore')->name('register.visit.store');

//Call Waiter
Route::post('call/waiter/', 'RestorantController@callWaiter')->name('call.waiter');

//Register driver
Route::get('new/driver/register', 'DriverController@register')->name('driver.register');
Route::post('new/driver/register/store', 'DriverController@registerStore')->name('driver.register.store');

Route::get('order/success', 'OrderController@success')->name('order.success');
Route::get('order/successwhatsapp/{order}', 'OrderController@silentWhatsAppRedirect')->name('order.successwhatsapp');

Route::get('order/cancel', 'OrderController@cancel')->name('order.cancel');

Route::post('/fb-order', 'OrderController@fbOrderMsg')->name('fb.order');

Route::get('onboarding', 'FrontEndController@onboarding')->name('sd.onboarding');

Route::get('/{alias}', 'FrontEndController@restorant')->where('alias', '.*');

