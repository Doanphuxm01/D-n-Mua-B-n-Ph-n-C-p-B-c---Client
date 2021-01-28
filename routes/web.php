<?php

use App\Elibs\eView;

if (config('debugbar.enabled')) {
    \Illuminate\Support\Facades\DB::connection()->enableQueryLog();
}
if (config('app.env') == 'production') {
    define('__DEV__', false);
} else {
    define('__DEV__', true);
}
// Route::any('/', 'KaynHome\KaynHome@index');
Route::any('_job/{action_name}','_Dev\CrawlDev@index');
Route::any('_job2','_Dev\CrawlDev@_inti_product');
Route::any('/public-api/{api_class?}/{api_func?}', 'API\AppApi@public_api');
Route::group(['middleware' => ['web'], 'prefix' => '/', 'namespace' => 'FrontEnd',], function () {
    Route::any('/', ['as' => 'FeHome', 'uses' => 'FeHome\FeHome@index']);
    Route::group(['middleware' => ['admin'], 'prefix' => 'checkout/', 'before' => ''], function () {
        Route::any('/{action?}', ['as' => 'FeCart', 'uses' => 'FeCart\FeCart@index']);
    });
    Route::group(['prefix' => 'tags/', 'before' => ''], function () {
        Route::any('{alias}.html', ['as' => 'FeProductTags', 'uses' => 'FeProduct\FeProduct@tags'])
        ->where([
            'alias' => '[a-zA-Z0-9_\-]+',
        ]);
    });
    Route::any('{alias}-p{id}.html', ['as' => 'FeProductDetail', 'uses' => 'FeProduct\FeProduct@detail'])
        ->where([
            'alias' => '[a-zA-Z0-9_\-]+',
            'id' => '[a-zA-Z0-9_\-]+',
    ]);
    Route::any('{alias}.html', ['as' => 'FeProductCate', 'uses' => 'FeProduct\FeProduct@cate'])->where(['alias' => '[a-zA-Z0-9_\-]+',]);
    // Route::any('{alias}.html',['as' => 'Fe_PR', 'uses' => 'FeProduct\FeProduct@product'])->where([
    //     'alias' => '[a-zA-Z0-9_\-]+'
    // ]);;
    Route::any('cate',['as' => 'FeCate', 'uses' => 'FeCate\FeCate@_sendCate']);

    // Route::any('', 'KaynHome\KaynHome@index');
});
Route::any('auth/{action_name?}', 'AdminMember\MemberGate@index');
Route::group(['middleware' => ['admin'], 'prefix' => '/admin', 'before' => ''], function () {
//Route::group(['prefix' => 'admin', 'before' => ''], function () {
    Route::get('/', ['as' => 'AdminSystem', 'uses' => 'AdminSystem\MngSystem@index']);
    Route::get('/logs', ['as' => 'AdminSystem', 'uses' => 'AdminSystem\MngSystem@list_log_access']);
    Route::get('/system-role', ['as' => 'AdminSystem', 'uses' => 'AdminSystem\MngSystem@list_role']);
    Route::get('/system-contact', ['as' => 'AdminSystem', 'uses' => 'AdminSystem\MngSystem@contact']);
    Route::get('/demo_sendmail', ['as' => 'AdminSystem', 'uses' => 'AdminSystem\MngSystem@demo_send_mail']);

    #region
    Route::group(['prefix' => 'project', 'before' => ''], function () {
        Route::any('/{action_name?}', ['as' => 'AdminSystem', 'uses' => 'AdminSystem\MngProject@index']);
    });

    Route::group(['prefix' => 'media', 'before' => ''], function () {
        Route::any('/{action_name?}', ['as' => 'AdminContent', 'uses' => 'AdminContent\MngMedia@index']);
    });

    Route::group(['prefix' => 'chuyen-diem', 'before' => ''], function () {
        Route::any('/{action_name?}', ['as' => 'AdminChuyenDiem', 'uses' => 'AdminChuyenDiem\mngChuyenDiem@index']);
    });

    #endregion quản lý nội dung

    Route::any('/msg/{action_name?}', ['as' => 'AdminMsg', 'uses' => 'AdminMsg\MngMsg@index']);
    Route::any('/staff/{action_name?}', ['as' => 'AdminMember', 'uses' => 'AdminMember\MngMember@index']);
    Route::any('/orders-mpg/{action_name?}', ['as' => 'AdminOrderMpg', 'uses' => 'AdminOrder\MngOrderMpg@index']);
    Route::any('/orders-tieudung/{action_name?}', ['as' => 'AdminTransactionTieuDung', 'uses' => 'AdminTransaction\MngOrderTieuDung@index']);
    Route::any('/orders-chietkhau/{action_name?}', ['as' => 'AdminTransactionChietKhau', 'uses' => 'AdminTransaction\MngOrderChietKhau@index']);
    Route::any('/orders-congno/{action_name?}', ['as' => 'AdminTransactionCongNo', 'uses' => 'AdminTransaction\MngOrderCongNo@index']);
    Route::any('/orders-hoahong/{action_name?}', ['as' => 'AdminTransactionHoaHong', 'uses' => 'AdminTransaction\MngOrderHoaHong@index']);
    Route::any('/orders-khodiem/{action_name?}', ['as' => 'AdminTransactionKhoDiem', 'uses' => 'AdminTransaction\MngOrderKhoDiem@index']);
    Route::group(['prefix' => '', 'as' => 'AdminWithdrawal'], function () {
        Route::any('/lich-su-rut-tien-cua-ban/{action_name?}', 'AdminWithdrawal\MngWithdrawalHistory@index');
        Route::any('/rut-tien/{action_name?}', 'AdminWithdrawal\MngWithdrawalRequest@index');
    });

    Route::group(['prefix' => '', 'as' => 'AdminKhoDiem'], function () {
        Route::any('/danh-sach-don-tra-hang/{action_name?}', 'AdminKhoDiem\MngOrderMuaHang@index');
    });

    Route::group(['prefix' => '', 'as' => 'AdminChuyenDiem'], function () {
        Route::any('/chuyen-diem/{action_name?}', 'AdminChuyenDiem\MngChuyenDiem@index');
        Route::any('/kich-hoat-thanh-vien/{action_name?}', 'AdminKichHoatTaiKhoan\MngKichHoatTaiKhoan@index');
    });
    Route::any('/don-hang-cua-toi/{action_name?}', ['as' => 'AdminPurchaseOrder', 'uses' => 'AdminOrder\MngPurchaseOrder@index']);
    Route::any('/mua-diem/{action_name?}', ['as' => 'AdminBuyMpg', 'uses' => 'AdminBuyMpg\MngBuyMPG@index']);
    Route::post('media/do-upload', 'AdminContent\MngMedia@do_upload');


});
