<?php

namespace App\Providers;
use App\Http\Models\Cate;
use App\Http\Models\BaseModel;
use Illuminate\Support\ServiceProvider;
use DB;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Load cac file template tuong ung trong tung module
        if(is_dir(__DIR__ . '/FrontEnd' )){
            $this->loadViewsFrom(__DIR__ . '/FrontEnd/');
        }

        $html = '';
        $listCate = [];
        $listObj = Cate::getAllProductCate();
        if ($listObj != "NULL") {
            $listCate = Cate::buildTreeMenuLeft($listObj['items'], 0);
            // $html .= '<div class="col-md-6"><ol class="dd-list">'.Cate::buildMenuLeftMPG($listCate).'</ol></div>';
            $html .= 
            '<ul class="dd-list menu">'
            .Cate::buildMenuLeftMPG($listCate).
            
            '</ul>';
            $tpl['listCate'] = $listCate;
        }
        $tpl['html'] = $html;
        return view()->share('tpl', $tpl);

        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
