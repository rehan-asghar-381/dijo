<?php

use App\Models\Restaurant;
use App\Models\DataSetting;
use App\CentralLogics\Helpers;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Models\SubscriptionTransaction;
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

Route::get('/', 'HomeController@index')->name('home');

Route::get('/cleareverything', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";

    // $cleardebugbar = Artisan::call('debugbar:clear');
    // echo "Debug Bar cleared<br>";
});
//login

Route::get('login/{tab}', 'LoginController@login')->name('login');
Route::post('login_submit', 'LoginController@submit')->name('login_post')->middleware('actch');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::get('/reload-captcha', 'LoginController@reloadCaptcha')->name('reload-captcha');
Route::get('/reset-password', 'LoginController@reset_password_request')->name('reset-password');
Route::post('/vendor-reset-password', 'LoginController@vendor_reset_password_request')->name('vendor-reset-password');
Route::get('/password-reset', 'LoginController@reset_password')->name('change-password');
Route::post('verify-otp', 'LoginController@verify_token')->name('verify-otp');
Route::post('reset-password-submit', 'LoginController@reset_password_submit')->name('reset-password-submit');
Route::get('otp-resent', 'LoginController@otp_resent')->name('otp_resent');



Route::get('lang/{locale}', 'HomeController@lang')->name('lang');
Route::get('terms-and-conditions', 'HomeController@terms_and_conditions')->name('terms-and-conditions');
Route::get('about-us', 'HomeController@about_us')->name('about-us');
Route::get('blogs', 'BlogController@blogs')->name('blogs');
Route::get('blog/{slug}', 'BlogController@post_detail')->name('blog-detail');
Route::match(['get', 'post'],'contact-us', 'HomeController@contact_us')->name('contact-us');
Route::get('privacy-policy', 'HomeController@privacy_policy')->name('privacy-policy');
Route::post('newsletter/subscribe', 'NewsletterController@newsLetterSubscribe')->name('newsletter.subscribe');

Route::get('refund-policy', 'HomeController@refund_policy')->name('refund-policy');
Route::get('shipping-policy', 'HomeController@shipping_policy')->name('shipping-policy');
Route::get('cancellation-policy', 'HomeController@cancellation_policy')->name('cancellation-policy');

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthenticated.']);
    return response()->json([
        'errors' => $errors,
    ], 401);
})->name('authentication-failed');

Route::group(['prefix' => 'payment-mobile'], function () {
    Route::get('/', 'PaymentController@payment')->name('payment-mobile');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});

// SSLCOMMERZ Start

Route::post('pay-ssl', 'SslCommerzPaymentController@index')->name('pay-ssl');
Route::post('/success', 'SslCommerzPaymentController@success');
Route::post('/fail', 'SslCommerzPaymentController@fail');
Route::post('/cancel', 'SslCommerzPaymentController@cancel');
Route::post('/ipn', 'SslCommerzPaymentController@ipn');
//SSLCOMMERZ END

/*paypal*/
/*Route::get('/paypal', function (){return view('paypal-test');})->name('paypal');*/
// Route::post('pay-paypal', 'PaypalPaymentController@payWithpaypal')->name('pay-paypal');
// Route::get('paypal-status', 'PaypalPaymentController@getPaymentStatus')->name('paypal-status');

Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
    Route::get('pay', 'PaypalPaymentController@payment')->name('pay');
    Route::any('callback', 'PaypalPaymentController@callback')->name('callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;
    Route::any('cancel',  'PaypalPaymentController@cancel')->name('cancel')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;
});
/*paypal*/


Route::get('pay-stripe', 'StripePaymentController@payment_process_3d')->name('pay-stripe');
Route::get('pay-stripe/success/{order_id}/{transaction_ref}', 'StripePaymentController@success')->name('pay-stripe.success');
Route::get('pay-stripe/fail', 'StripePaymentController@fail')->name('pay-stripe.fail');

// Get Route For Show Payment Form
Route::get('paywithrazorpay', 'RazorPayController@payWithRazorpay')->name('paywithrazorpay');
Route::post('payment-razor/{order_id}', 'RazorPayController@payment')->name('payment-razor');

/*Route::fallback(function () {
return redirect('/admin/auth/login');
});*/

Route::get('payment-success', 'PaymentController@success')->name('payment-success');
Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');

//senang pay
Route::match(['get', 'post'], '/return-senang-pay', 'SenangPayController@return_senang_pay')->name('return-senang-pay');

// paymob
Route::post('/paymob-credit', 'PaymobController@credit')->name('paymob-credit');
Route::get('/paymob-callback', 'PaymobController@callback')->name('paymob-callback');

//paystack
// Route::post('/paystack-pay', 'PaystackController@redirectToGateway')->name('paystack-pay');
// Route::get('/paystack-callback', 'PaystackController@handleGatewayCallback')->name('paystack-callback');
// Route::get('/paystack',function (){
//     return view('paystack');
// });
Route::get('/paystack-callback', 'PaystackController@callback')->name('paystack-callback');


// The route that the button calls to initialize payment
Route::post('/flutterwave-pay','FlutterwaveController@initialize')->name('flutterwave_pay');
// The callback url after a payment
Route::get('/rave/callback/{payment_id}', 'FlutterwaveController@callback')->name('flutterwave_callback');


// The callback url after a payment
Route::get('mercadopago/home', 'MercadoPagoController@index')->name('mercadopago.index');
Route::post('mercadopago/make-payment', 'MercadoPagoController@make_payment')->name('mercadopago.make_payment');
Route::get('mercadopago/get-user', 'MercadoPagoController@get_test_user')->name('mercadopago.get-user');

//paytabs
Route::any('/paytabs-payment', 'PaytabsController@payment')->name('paytabs-payment');
Route::any('/paytabs-response', 'PaytabsController@callback_response')->name('paytabs-response');

//bkash
Route::group(['prefix' => 'bkash'], function () {
    // Payment Routes for bKash
    Route::post('get-token', 'BkashPaymentController@getToken')->name('bkash-get-token');
    // Route::post('create-payment', 'BkashPaymentController@createPayment')->name('bkash-create-payment');
    // Route::post('execute-payment', 'BkashPaymentController@executePayment')->name('bkash-execute-payment');
    // Route::get('query-payment', 'BkashPaymentController@queryPayment')->name('bkash-query-payment');
    Route::get('make-payment', 'BkashPaymentController@make_tokenize_payment')->name('bkash-make-payment');
    Route::any('success', 'BkashPaymentController@bkashSuccess')->name('bkash-success');

    // Refund Routes for bKash
    // Route::get('refund', 'BkashRefundController@index')->name('bkash-refund');
    // Route::post('refund', 'BkashRefundController@refund')->name('bkash-refund');
});

// The callback url after a payment PAYTM
Route::get('paytm-payment', 'PaytmController@payment')->name('paytm-payment');
Route::any('paytm-response', 'PaytmController@callback')->name('paytm-response');

// The callback url after a payment LIQPAY
Route::get('liqpay-payment', 'LiqPayController@payment')->name('liqpay-payment');
Route::any('liqpay-callback/{order_id}', 'LiqPayController@callback')->name('liqpay-callback');

Route::get('wallet-payment','WalletPaymentController@make_payment')->name('wallet.payment');

Route::get('/test',function (){

    // Mail::to('tuhin.nahian@gmail.com')->send(new \App\Mail\RestaurantRegistration('pending', 'tttt'));
    // Mail::to('tuhin.nahian@gmail.com')->send(new \App\Mail\VendorSelfRegistration('pending', 'tttt'));

    // Helpers::insert_data_settings_key('admin_login_url', 'login_admin' ,'admin');
    // Helpers::insert_data_settings_key('admin_employee_login_url', 'login_admin_employee' ,'admin_employee');
    // Helpers::insert_data_settings_key('restaurant_login_url', 'login_restaurant' ,'restaurant');
    // Helpers::insert_data_settings_key('restaurant_employee_login_url', 'login_restaurant_employee' ,'restaurant_employee');


return view('errors.404');
    // dd('Hello tester');
});

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
    return response()->json([
        'errors' => $errors
    ], 401);
})->name('authentication-failed');

Route::get('module-test',function (){

});

//Restaurant Registration
Route::group(['prefix' => 'restaurant', 'as' => 'restaurant.'], function () {
    Route::get('apply', 'VendorController@create')->name('create');
    Route::get('back/{restaurant_id}', 'VendorController@back')->name('back');
    Route::post('apply', 'VendorController@store')->name('store');
    Route::post('payment', 'VendorController@payment')->name('payment');
    Route::post('business-plan', 'VendorController@business_plan')->name('business_plan');
});

//Deliveryman Registration
Route::group(['prefix' => 'deliveryman', 'as' => 'deliveryman.'], function () {
    Route::get('apply', 'DeliveryManController@create')->name('create');
    Route::post('apply', 'DeliveryManController@store')->name('store');
});
