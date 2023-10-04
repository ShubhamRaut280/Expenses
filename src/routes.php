<?php

use Simcify\Router;
use Simcify\Exceptions\Handler;
use Simcify\Middleware\Authenticate;
use Simcify\Middleware\RedirectIfAuthenticated;
use Pecee\Http\Middleware\BaseCsrfVerifier;

/**
 * ,------,
 * | NOTE | CSRF Tokens are checked on all PUT, POST and GET requests. It
 * '------' should be passed in a hidden field named "csrf-token" or a header
 *          (in the case of AJAX without credentials) called "X-CSRF-TOKEN"
 *  */
 Router::csrfVerifier(new BaseCsrfVerifier());

// Router::group(array(
//     'prefix' => '/' . env('URL_PREFIX')
// ), function() {
    
    Router::group(array(
        'middleware' => Simcify\Middleware\Authenticate::class
    ), function() {
        
        // Overview
        Router::get('/', 'Overview@get');
        Router::post('/overview/reports', 'Overview@getreports');
        Router::post('/account/create', 'Overview@createaccount');
        Router::post('/account/update', 'Overview@updateaccount');
        Router::post('/account/update/view', 'Overview@updateaccountview');
        Router::post('/account/delete', 'Overview@deleteaccount');
        
        // Budget
        Router::get('/budget', 'Budget@get');
        Router::post('/budget/adjust', 'Budget@adjust');
        
        // Expenses
        Router::get('/expenses', 'Expenses@get');
        Router::post('/expense/add', 'Expenses@add');
        Router::post('/expense/update', 'Expenses@update');
        Router::post('/expense/update/view', 'Expenses@updateview');
        Router::post('/expense/delete', 'Expenses@delete');
        
        // Income
        Router::get('/income', 'Income@get');
        Router::post('/income/add', 'Income@add');
        Router::post('/income/update', 'Income@update');
        Router::post('/income/update/view', 'Income@updateview');
        Router::post('/income/delete', 'Income@delete');
        
        // Settings
        Router::get('settings', 'Settings@get');
        Router::post('/settings/update/profile', 'Settings@updateprofile');
        Router::post('/settings/update/company', 'Settings@updatecompany');
        Router::post('/settings/update/system', 'Settings@updatesystem');
        Router::post('/settings/update/reminders', 'Settings@updatereminders');
        Router::post('/settings/update/password', 'Settings@updatepassword');
        Router::post('/settings/category/add', 'Settings@addcategory');
        Router::post('/settings/category/update', 'Settings@updatecategory');
        Router::post('/settings/category/update/view', 'Settings@updatecategoryview');
        Router::post('/settings/category/delete', 'Settings@deletecategory');
        
        // Users
        Router::get('/users', 'Users@get');
        Router::post('/users/create', 'Users@create');
        Router::post('/users/update', 'Users@update');
        Router::post('/users/update/view', 'Users@updateview');
        Router::post('/users/delete', 'Users@delete');
        
        // Signout
        Router::get('/signout', 'Auth@signout');

        // update
        Router::get('/update', 'Update@get');
        Router::post('/update/scan', 'Update@scan');
        
    });
    
    Router::group(array(
        'middleware' => Simcify\Middleware\RedirectIfAuthenticated::class
    ), function() {
        
        /**
         * No login Required for these pages
         **/
        Router::get('/signin', 'Auth@get');
        Router::post('/signin/authenticate', 'Auth@signin');
        Router::post('/forgot', 'Auth@forgot');
        Router::post('/signup', 'Auth@signup');
        Router::post('/reset', 'Auth@reset');
        Router::get('/reset/{token}', 'Auth@resetpage', array(
            'as' => 'token'
        ));
        
    });
    
    Router::get('/404', function() {
        response()->httpCode(404);
        echo view();
    });
    
// });