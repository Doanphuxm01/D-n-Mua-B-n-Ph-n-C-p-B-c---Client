<?php
/**
 * Created by PhpStorm.
 * User: ngannv
 * Date: 9/13/15
 * Time: 12:40 AM
 */

namespace App\Elibs;


use App\Http\Models\ProjectPermission;
use App\Http\Models\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class Debug
{
    const DEBUG_ON = 1;
    private $dbInfo = '';


    static function show($obj, $label = '', $color = '#ffcebb')
    {
        echo "<pre style='border: 1px solid red;margin:5px;padding:5px;background-color:$color !important;max-height: 800px;overflow: auto'>";
        $debug = debug_backtrace();
        echo "<h2>$label</h2>";
        echo ($debug[0]['file'] . ':' . $debug[0]['line']) . '<br/>';
        print_r($obj);
        echo "</pre>";
    }

    static function getDbInfo()
    {
        return DB::getQueryLog();
        if ($logDB) {
            $li = '';
            foreach ($logDB as $key => $val) {
                $li .= '<li> Time: ' . $val['time'] . 's';
                $li .= '<span class="db-sql">' . $val['query'] . '</span>';

                $li .= '</li>';
            }

            return $li;
        }

        return '';

    }

    static function DebugPermission()
    {

        self::show(["role" => Role::getPermissionByGroup()]);
        self::show(["project" => ProjectPermission::getAccessListProjectId()]);
        self::show(["belongGroupManage" => Role::isBelongGroupManage()]);
        self::show(["permissionGroup" => Role::getCurrentPermissionGroup()]);
        self::show(["isRoot" => Role::isRoot()]);
    }

    static function pushNotification($msg=''){
        $ch = curl_init();
        // Set the URL
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot1146618205:AAGywiAB8XX3uIXwo1QsAucbVeTgv9RqxJk/sendMessage?chat_id=@kaynpro&text=' . urlencode($msg));
        // Removes the headers from the output
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // Return the output instead of displaying it directly
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Execute the curl session
        curl_exec($ch);
        // Close the curl session
        curl_close($ch);
        // Return the output as a variable
        return true;
    }
}
