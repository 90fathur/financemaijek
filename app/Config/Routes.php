<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', static function () {
    return redirect()->to(base_url('dashboard'));
});

// Authentication routes
$routes->get('auth/login', 'AuthController::login');
$routes->post('auth/process-login', 'AuthController::processLogin');
$routes->get('auth/logout', 'AuthController::logout');

// Protected application routes
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Transactions
    $routes->get('transactions', 'TransactionController::index');
    $routes->post('transactions/store', 'TransactionController::store');
    $routes->post('transactions/update/(:num)', 'TransactionController::update/$1');
    $routes->get('transactions/delete/(:num)', 'TransactionController::delete/$1');
    $routes->get('transactions/export-csv', 'TransactionController::exportCsv');

    // Investors & Investments
    $routes->get('investors', 'InvestorController::index');
    $routes->post('investors/store', 'InvestorController::store');
    $routes->get('investors/detail/(:num)', 'InvestorController::detail/$1');
    $routes->post('investors/update/(:num)', 'InvestorController::update/$1');
    $routes->post('investors/add-capital/(:num)', 'InvestorController::addCapital/$1');
    $routes->post('investors/distribute-dividends', 'InvestorController::distributeDividends');
    $routes->get('investors/delete/(:num)', 'InvestorController::delete/$1');

    // Categories (Dynamic)
    $routes->get('categories', 'CategoryController::index');
    $routes->post('categories/store', 'CategoryController::store');
    $routes->post('categories/update/(:num)', 'CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'CategoryController::delete/$1');

    // Kas & Bank Accounts
    $routes->get('kas-accounts', 'KasAccountController::index');
    $routes->post('kas-accounts/store', 'KasAccountController::store');
    $routes->post('kas-accounts/update/(:num)', 'KasAccountController::update/$1');
    $routes->get('kas-accounts/recalculate/(:num)', 'KasAccountController::recalculate/$1');
    $routes->get('kas-accounts/delete/(:num)', 'KasAccountController::delete/$1');

    // Reports
    $routes->get('reports', 'ReportController::index');
    $routes->get('reports/print', 'ReportController::print');

    // Dynamic WhatsApp Group Integration
    $routes->get('settings/whatsapp', 'WhatsAppSettingController::index');
    $routes->post('settings/whatsapp/save', 'WhatsAppSettingController::save');
    $routes->post('settings/whatsapp/test-send', 'WhatsAppSettingController::testSend');
    $routes->post('settings/whatsapp/send-daily', 'WhatsAppSettingController::sendDailySummary');
});
