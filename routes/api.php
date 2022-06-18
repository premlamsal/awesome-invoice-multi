<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});





//List Customers
Route::get('customers', 'CustomerController@index');


//Create new Customer
Route::post('customer', 'CustomerController@store');

//List single customer
Route::get('customer/{id}', 'CustomerController@show');

//Update customer
Route::put('customer', 'CustomerController@update');

//Delete customer
Route::delete('customer/{id}', 'CustomerController@destroy');

//Search Customer
Route::post('customers/search', 'CustomerController@searchCustomers');

//Get Customer Transactions
Route::get('customer/transactions/{id}', 'CustomerTransactionController@index');

//Create new Customer payent
Route::post('customer/add-payment', 'CustomerPaymentController@store');

//Create new Customer payent
Route::get('customer/payments/{customer_id}', 'CustomerController@getPayments');

//get Customer payment 
Route::get('customer/payment/{payment_id}', 'CustomerPaymentController@show');

//Delete customer payment
Route::delete('customer/delete-payment/{payment_id}', 'CustomerPaymentController@destroy');

//update customer payment
Route::post('customer/update-payment/', 'CustomerPaymentController@update');




//List supplier
Route::get('suppliers', 'SupplierController@index');

//Create new supplier
Route::post('supplier', 'SupplierController@store');

//List single supplier
Route::get('supplier/{id}', 'SupplierController@show');

//Update supplier
Route::put('supplier', 'SupplierController@update');

//Delete supplier
Route::delete('supplier/{id}', 'SupplierController@destroy');

//Search Suppliers
Route::post('suppliers/search', 'SupplierController@searchSuppliers');


//Get Supplier Transactions
Route::get('supplier/transactions/{id}', 'SupplierTransactionController@index');

//Create new Supplier payent
Route::post('supplier/add-payment', 'SupplierPaymentController@store');

//Create new Supplier payent
Route::get('supplier/payments/{supplier_id}', 'SupplierController@getPayments');

//get Supplier payment 
Route::get('supplier/payment/{payment_id}', 'SupplierPaymentController@show');

//Delete supplier payment
Route::delete('supplier/delete-payment/{payment_id}', 'SupplierPaymentController@destroy');

//Delete supplier payment
Route::post('supplier/update-payment/', 'SupplierPaymentController@update');





//List product
Route::get('products', 'ProductController@index');

//Create new product
Route::post('product', 'ProductController@store');

//List single product
Route::get('product/{id}', 'ProductController@show');

//Update product
Route::put('product', 'ProductController@update');

//Delete product
Route::delete('product/{id}', 'ProductController@destroy');

//search products
Route::post('products/search', 'ProductController@searchProducts');



// //List single productDetail
// Route::get('productDetail/{id}','ProductDetailController@show');



//retriving settings
Route::get('settings', 'SettingController@index');

//updating settings
Route::put('settings', 'SettingController@update');



//List invoices
Route::get('invoices', 'InvoiceController@index');

//List single invoice
Route::get('invoice/{id}', 'InvoiceController@show');

//Create new invoice
Route::post('invoice', 'InvoiceController@store');

//Update invoice
Route::put('invoice', 'InvoiceController@update');

//return invoice
// Route::put('invoice/return','InvoiceController@returnInvoice');

//Delete invoice
Route::delete('invoice/{id}', 'InvoiceController@destroy');


//search invoices
Route::post('invoices/search', 'InvoiceController@searchInvoices');


//update status of invoice i.e To Pay or Paid 
Route::put('changeInvoiceStatus', 'InvoiceController@changeInvoiceStatus');



//List Units
Route::get('units', 'UnitController@index');

//List single Unit
Route::get('units/{id}', 'UnitController@show');

//Add Unit
Route::post('unit', 'UnitController@store');

//Update Unit
Route::put('unit', 'UnitController@update');

//Delete Unit
Route::delete('unit/{id}', 'UnitController@destroy');

//Search units
Route::post('units/search', 'UnitController@searchUnits');








//List Categories
Route::get('categories', 'CategoryController@index');

//List single Category
Route::get('categories/{id}', 'CategoryController@show');

//Add Category
Route::post('category', 'CategoryController@store');

//Update Category
Route::put('category', 'CategoryController@update');

//Delete Category
Route::delete('Category/{id}', 'CategoryController@destroy');

//Search Category
Route::post('categories/search', 'CategoryController@searchCategories');



//List purchase
Route::get('purchases', 'PurchaseController@index');

//List single purchase
Route::get('purchase/{id}', 'PurchaseController@show');

//Create new purchase
Route::post('purchase', 'PurchaseController@store');

//Update purchase
Route::put('purchase', 'PurchaseController@update');

//return purchase
// Route::put('purchase/return','PurchaseController@returnPurchase');


//update status of invoice i.e To Pay or Paid 
Route::put('changePurchaseStatus', 'PurchaseController@changePurchaseStatus');


//Delete purchase
Route::delete('purchase/{id}', 'PurchaseController@destroy');


//search purchases
Route::post('purchases/search', 'PurchaseController@searchPurchases');



//List stocks
Route::get('stocks', 'StockController@index');

//List single stock
Route::get('stock/{id}', 'StockController@show');

//Create new stock
Route::post('stock', 'StockController@store');

//adjust stock
Route::post('adjust_stock', 'StockController@adjustStock');

//Update stock
Route::put('stock', 'StockController@update');

//check quantity in stock
Route::post('checkQuantityInStock', 'StockController@checkQuantityInStock');



//List single stockDetail
Route::post('stock/history', 'StockController@stockHistory');



//Delete stock
Route::delete('stock/{id}', 'StockController@destroy');


//search stock
Route::post('stock/search', 'StockController@searchPurchases');


//List single stockDetail
Route::get('stockDetail/{id}', 'StockDetailController@show');



//get stock adjustment for single stock
Route::get('stock_adjustments/{id}', 'StockController@showStockAdjustments');



//List Users
Route::get('users', 'UserController@index');

//create User
Route::post('user', 'UserController@store');

//List single user
Route::get('user/{id}', 'UserController@show');

//delete single user
Route::delete('user/{id}', 'UserController@destroy');

//update single user
Route::put('user', 'UserController@update');

//Search user
Route::post('users/search', 'UserController@searchUsers');





//dashboard 
Route::get('dashInfo', 'DashboardController@dashInfo');

//dashboard 
Route::get('sales/chart/{before_month}', 'DashboardController@salesChart');



//check user has store or not
Route::get('store/check', 'CheckController@checkUserForStore');

//check user has permissions or not
Route::get('permissions/check', 'CheckController@checkPermissions');


//fetch stores of login user
Route::get('stores', 'CheckController@stores');

Route::get('store', 'StoreController@show');

Route::put('store', 'StoreController@update');


//roles

Route::get('roles', 'RoleController@index');

Route::post('role', 'RoleController@store');

Route::put('role', 'RoleController@update');

Route::get('role/{id}', 'RoleController@show');

Route::delete('role/{id}', 'RoleController@destroy');


//permissions

Route::get('permissions', 'PermissionController@index');

Route::post('permission', 'PermissionController@store');

Route::put('permission', 'PermissionController@update');

Route::get('permission/{id}', 'PermissionController@show');

Route::delete('permission/{id}', 'PermissionController@destroy');



//accounts

//List account
Route::get('accounts', 'AccountController@index');

//Create new account
Route::post('account', 'AccountController@store');

//List single account
Route::get('account/{id}', 'AccountController@show');

//Update account
Route::put('account', 'AccountController@update');

//Delete account
Route::delete('account/{id}', 'AccountController@destroy');

//search accounts
Route::post('accounts/search', 'AccountController@searchAccounts');


//Get Account Transactions
Route::get('account/transactions/{id}', 'AccountController@getAccountTransactions');


//get all transactions
Route::get('transactions', 'TransactionController@index');

Route::post('transaction/add', 'TransactionController@store');

Route::get('transaction/{id}', 'TransactionController@show');

//Update transaction
Route::post('transaction', 'TransactionController@update');

//Delete transaction
Route::delete('transaction/delete/{id}', 'TransactionController@destroy');


///////........RETURN INVOICE......../////////


//List return_invoices
Route::get('return_invoices', 'ReturnInvoiceController@index');

//List single return_invoice
Route::get('return_invoice/{id}', 'ReturnInvoiceController@show');

//Create new return_invoice
Route::post('return_invoice', 'ReturnInvoiceController@store');

//Update return_invoice
Route::put('return_invoice', 'ReturnInvoiceController@update');

//return return_invoice
// Route::put('return_invoice/return','ReturnInvoiceController@returnReturnInvoice');

//Delete return_invoice
Route::delete('return_invoice/{id}', 'ReturnInvoiceController@destroy');


//search return_invoices
Route::post('return_invoices/search', 'ReturnInvoiceController@searchReturnInvoices');


//update status of return_invoice i.e To Pay or Paid 
Route::put('changeReturnInvoiceStatus', 'ReturnInvoiceController@changeReturnInvoiceStatus');



///////........RETURN INVOICE......../////////



///////........RETURN PURCHASE......../////////


//List return_purchases
Route::get('return_purchases', 'ReturnPurchaseController@index');

//List single return_purchase
Route::get('return_purchase/{id}', 'ReturnPurchaseController@show');

//Create new return_purchase
Route::post('return_purchase', 'ReturnPurchaseController@store');

//Update return_purchase
Route::put('return_purchase', 'ReturnPurchaseController@update');

//return return_purchase
// Route::put('return_purchase/return','ReturnPurchaseController@returnReturnPurchase');

//Delete return_purchase
Route::delete('return_purchase/{id}', 'ReturnPurchaseController@destroy');


//search return_purchases
Route::post('return_purchases/search', 'ReturnPurchaseController@searchReturnPurchases');


//update status of return_purchase i.e To Pay or Paid 
Route::put('changeReturnPurchaseStatus', 'ReturnPurchaseController@changeReturnPurchaseStatus');



///////........RETURN PURCHASE......../////////

