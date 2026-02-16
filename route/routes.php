<?php
// routes.php
// Get current URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 1. Catch invalid trailing /login paths
$segmentsUrl = explode('/', trim($uri, '/'));

// If second segment is "login" (like /update-product/login or /something/login)
if (isset($segmentsUrl[0]) && $segmentsUrl[0] !== 'api') {
    if (isset($segmentsUrl[1]) && $segmentsUrl[1] === 'login') {
        // Redirect to correct login page
        header("Location: /login");
        exit();
    }
}


$routes = [
    'GET' => [
        //shop//
        '/' => 'Ecom\SelectCountryController@index',

        //Login/Logout
        '/login' => 'Auth\AuthController@index',
        '/logout' => 'Member\MemberController@logout',

        //Dashboard
        '/dashboard' => 'Member\MemberController@dashboard',
        '/sales-stats' => 'Member\MemberController@salesStatistics',
        '/sales-report' => 'Member\MemberController@salesReport',
        '/testMail' => 'Member\MemberController@testMailer',
        '/profile' => 'Member\MemberController@profile',

        //Create New Product
        '/new-product' => 'Product\ProductController@newProduct',

        //List Product
        '/stock-control' => 'Product\ProductController@stockControl',

        //Update Product
        '/update-product/:id' => 'Product\ProductController@updateProduct',

        //Delete Product
        '/delete-product/:id' => 'Product\ProductController@deleteProduct',

        //Category-Brand
        '/category-product' => 'Product\ProductController@productCategory',
        '/brand-product' => 'Product\ProductController@productBrand',
        '/update-category/:id' => 'Product\ProductController@updateCategory',
        '/update-brand/:id' => 'Product\ProductController@updateBrand',
        '/delete-brand/:id' => 'Product\ProductController@deleteBrand',
        '/delete-category/:id' => 'Product\ProductController@deleteCategory',

        //Order
        '/new-order' => 'Order\OrderController@newOrder',
        '/details-buyer' => 'Order\OrderController@detailsBuyer',
        '/update-buyer' => 'Order\OrderController@updateBuyer',
        '/process-order' => 'Order\OrderController@processOrder',
        '/set-order-status/:id/:id/:id' => 'Order\OrderController@statusOrder',
        '/indelivery-order' => 'Order\OrderController@inDeliveryOrder',
        '/completed-order' => 'Order\OrderController@completeOrder',
        '/returned-order' => 'Order\OrderController@returnOrder',
        '/cancelled-order' => 'Order\OrderController@cancelOrder',
        '/database-order' => 'Order\OrderController@database',
        '/search-order' => 'Order\OrderController@searchOrder',
        '/set-order-to-processing/:id' => 'Order\OrderController@moveToProcessing',

        //SHOP
        '/main' => 'Ecom\ecomController@index',
        '/count-cart' => 'Ecom\AddToCartController@countCart',
        '/list-cart' => 'Ecom\AddToCartController@listCart',
        '/product-details/:id' => 'Ecom\ecomController@productDetails',
        '/checkout' => 'Ecom\checkoutController@index',
        '/checkout-step1' => 'Ecom\checkoutController@index2',
        '/proceed-payment' => 'Ecom\checkoutController@proceedPaymentSenangPay',
        '/categories/:id' => 'Ecom\ecomController@detailsCat',
        '/brands/:id' => 'Ecom\ecomController@detailsBrand',
        '/promo-item' => 'Ecom\ecomController@detailsPromo',
        '/order-details/:id' => 'Ecom\ecomController@detailsOrder',
        '/contact' => 'Ecom\ecomController@contact',
        '/about-us' => 'Ecom\ecomController@about',
        '/track-order' => 'Ecom\ecomController@trackOrder',
        '/tracks' => 'Ecom\ecomController@tracking',
        '/cart-delete' => 'Ecom\ecomController@cartDelete',
        '/change-country' => 'Ecom\ecomController@changeCountry',
        '/customer/support-ticket' => 'Ecom\supportController@index',
        '/customer/ticket-details' => 'Ecom\supportController@ticketDetails',
        '/customer/tiket-details' => 'Ecom\supportController@tiketDetails',
        '/blog-annoucement' => 'Ecom\ecomController@blog',

        //thank you page
        '/senangpay-thank-you' => 'Ecom\checkoutController@thankYou',

        //setting
        '/dhl-setting' => 'Setting\DhlController@index',
        '/setting-policy' => 'Setting\SettingController@policy',
        '/setting-terms' => 'Setting\SettingController@terms',
        '/setting-about-us' => 'Setting\SettingController@aboutUs',
        '/delivery-charge' => 'Setting\SettingController@deliveryCharge',
        '/delete-cod-charge/:id' => 'Setting\SettingController@deleteCodCharge',
        '/jt-express' => 'Setting\SettingController@settingJNT',
        '/password' => 'Setting\SettingController@changePassword',
        '/logo-setting' => 'Setting\SettingController@imageSetting',
        '/slider-setting' => 'Setting\SettingController@sliderSetting',
        '/set-logo' => 'Setting\SettingController@setLogo',

        //Announcement Blog
        '/announcement-blog' => 'Setting\SettingController@indexAnnouncement',
        '/update-post/:id' => 'Setting\SettingController@updateAnnouncement',
        '/delete-post/:id' => 'Setting\SettingController@deleteAnnouncement',

        //country
        '/list-country' => 'Setting\countryController@index',
        '/add-new-country' => 'Setting\countryController@addNewCountry',

        //staff
        '/hq-staff' => 'Member\staffController@index',
        '/banned-user/:id' => 'Member\staffController@bannUsers',
        '/unbanned-user/:id' => 'Member\staffController@unbannUsers',
        '/edit-user/:id' => 'Member\staffController@userDetails',
        '/set-user-permission' => 'Member\staffController@userPermission',
        '/delete-user/:id' => 'Member\staffController@userDelete',

        //member
        '/member-verify' => 'Ecom\memberController@verifyMember',
        '/member-logout' => 'Ecom\memberController@logout',


        //support-ticket
        '/support/tickets' => 'SupportTicket\ticketController@mainTickets',
        '/support/tickets-details' => 'SupportTicket\ticketController@replyTickets',
        '/ticket/close-ticket' => 'SupportTicket\ticketController@closeTicket',


        //Policy/Term&Condition,
        '/policies' => 'Ecom\ecomController@policies',
        '/terms-conditions' => 'Ecom\ecomController@terms',

        //hub pickup
        '/hub/dashboard' => 'HUB\PickupHubController@dashboard',


        '/access-denied' => 'accessDeniedController@index',


        //API-APPS
        '/api/login' => 'API\mainController@login',
        '/api/dashboard' => 'API\mainController@dashboard',
        '/api/validate-token' => 'API\mainController@validateToken',
        '/api/dashboard-data' => 'API\mainController@dashboardData',
        '/api/data-profile/:id' => 'API\mainController@profileData',
        '/api/orders' => 'API\mainController@getNewOrder',
        '/api/list-country' => 'API\mainController@listCountry',
        '/api/list-brand' => 'API\mainController@listBrand',
        '/api/list-category' => 'API\mainController@listCategory',

        //senangpay robot
        '/bot-senangpay' => '\Ecom\BotSenangpayCotroller@handleBot',

        //bayarcash
        '/proceed-bayarcash' => 'Ecom\checkoutController@proceedPaymentBayarcash',
        '/proceed-cod' => 'Ecom\checkoutController@proceedCOD',
        '/bayarcash-thank-you' => 'Ecom\checkoutController@thankYouBayarcash'

    ],
    'POST' => [
        '/' => 'Ecom\SelectCountryController@index',
        '/submit' => function () {
            echo "Form submitted!";
        },
        '/profile' => 'Member\MemberController@updateProfile',
        '/new-product' => 'Product\ProductController@newProduct',
        '/update-customer' => 'Order\OrderController@updateCustomer',
        '/update-product/:id' => 'Product\ProductController@updateProduct',
        '/check-slug' => 'Helper\slugSearchController@liveSearch',
        '/update-category/:id' => 'Product\ProductController@updateCategory',
        '/update-brand/:id' => 'Product\ProductController@updateBrand',
        '/category-product' => 'Product\ProductController@productCategory',
        '/brand-product' => 'Product\ProductController@productBrand',
        '/stock-control' => 'Product\ProductController@stockControl',
        '/bulk-delete-category' => 'Product\ProductController@bulkDeleteCategory',
        '/bulk-delete-brand' => 'Product\ProductController@bulkDeleteBrand',
        '/new-order' => 'Order\OrderController@submitCourier',
        '/process-order' => 'Order\OrderController@printAWB',

        //setting
        '/dhl-setting' => 'Setting\DhlController@saveDHL',
        '/setting-policy' => 'Setting\SettingController@updatePolicy',
        '/setting-terms' => 'Setting\SettingController@updateTerms',
        '/setting-about-us' => 'Setting\SettingController@updateAboutUs',
        '/add-new-country' => 'Setting\countryController@saveCountry',
        '/list-country' => 'Setting\countryController@updateCountry',
        '/delivery-charge' => 'Setting\SettingController@saveDeliveryCharge',
        '/save-cod-charge' => 'Setting\SettingController@saveCodCharge',
        '/jt-express' => 'Setting\SettingController@saveJNT',
        '/my-pass' => 'Setting\SettingController@savePassword',
        '/logo-setting' => 'Setting\SettingController@uploadImages',
        '/slider-setting' => 'Setting\SettingController@uploadSlider',

        //Announcement Blog
        '/announcement-blog' => 'Setting\SettingController@saveAnnouncement',
        '/update-post/:id' => 'Setting\SettingController@saveUpdateAnnouncement',

        //ecom
        '/add-to-cart' => 'Ecom\AddToCartController@addCart',
        '/checkout' => 'Ecom\checkoutController@nextCalculate',
        '/senangpay-callback' => 'Ecom\checkoutController@callBackSenangPay',
        '/bayarcash-callback' => 'Ecom\checkoutController@callBackBayarcash',
        '/update-checkout' => 'Ecom\checkoutController@checkoutUpdate',
        '/customer/support-ticket' => 'Ecom\supportController@submittedTicket',
        '/customer/ticket-details' => 'Ecom\supportController@submittedReplyTicket',

        //member
        '/member-auth' => 'Ecom\memberController@submitLoginRegister',
        '/member-verify' => 'Ecom\memberController@processVerify',

        //staff
        '/hq-staff' => 'Member\staffController@saveUsers',

        //support ticket
        '/support/tickets-reply' => 'SupportTicket\ticketController@repliesTickets',

        '/cart' => 'shop\CartController@updateCart',
        '/signup' => 'shop\AuthController@index2',
        '/login' => 'Auth\AuthController@processLogin',
        //'/checkout' => 'shop\CheckoutController@postChechout',
        '/verify_phone' => 'shop\AuthController@submitPhone',
        '/pay' => 'shop\CheckoutController@payChechout',


        //API-APPS
        '/api/login' => 'API\mainController@login',
        '/api/validate-token' => 'API\mainController@validateToken',
        '/api/update-profile/:id' => 'API\mainController@updateProfile',
        '/api/update-password/:id' => 'API\mainController@updatePassword'
    ],
];
