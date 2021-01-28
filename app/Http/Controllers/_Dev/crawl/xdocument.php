<?php

namespace App\Http\Controllers\_Dev;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\SearchHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\BaseModel;
use App\Http\Models\Book;
use App\Http\Models\Post;
use App\Http\Models\Project;
use App\Http\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Models\Cate;
use App\Http\Models\Document;
use App\Elibs\Pager;

require_once app_path('Elibs/simple_html_dom.php');

/**
 * Class vnews
 * @package App\Http\Controllers\_Dev
 * Cralw trang thông tin của vnexpress
 */
class xdocument extends Controller
{
    function chinhphu_gov()
    {

        $link = 'http://vanban.chinhphu.vn/portal/page/portal/chinhphu/hethongvanban?class_id=1&mode=view&org_group_id=0&type_group_id=0&category_id=2';
        $link = 'http://vanban.chinhphu.vn/portal/page/portal/chinhphu/hethongvanban?class_id=1&mode=view&org_group_id=0&type_group_id=0&category_id=2';
        $link = 'http://vanban.chinhphu.vn/portal/page/portal/chinhphu/hethongvanban?class_id=1&mode=view&org_group_id=0&type_group_id=0&category_id=3';
        $link = 'http://vanban.chinhphu.vn/portal/page/portal/chinhphu/hethongvanban?class_id=1&mode=view&org_group_id=0&type_group_id=0&category_id=4';
        $link = 'http://vanban.chinhphu.vn/portal/page/portal/chinhphu/hethongvanban?class_id=1&mode=view&org_group_id=0&type_group_id=0&category_id=20';

        for ($index = 1; $index <= 35; $index++) {
            // $link = 'http://vanban.chinhphu.vn/portal/page/portal/chinhphu/hethongvanban?class_id=1&mode=view&org_group_id=0&type_group_id=0&_page=2&category_id=' . $index;
            $link = 'http://vanban.chinhphu.vn/portal/page/portal/chinhphu/hethongvanban?class_id=1&mode=view&org_group_id=0&type_group_id=0&_page=3&category_id=' . $index;            $html = Helper::getUrlContent($link, 'D0N=8cf8745f0cfb55a7fa0b7507a6414781; _ga=GA1.2.1717978598.1529813496; _gid=GA1.2.379641568.1529813496; __asc=7c37a0ea1642ffd71c5337234d9; __auc=7c37a0ea1642ffd71c5337234d9; JSESSIONID=C2dkbvfLngpB5CQyLXvrXTy00pNjL3TgnpGRGMGw5vdj0GkhhJr4!-611282167; DEV_PORTAL=11.1+en-us+us+AMERICA+6F5C6FD2B544576EE050A8C0BF0933CD+8C436B9D4A126F3844A8D2AE35146256B46BF25A35C276998157DA58A91B4A297DB064DB414EA53E646BEDBEB91D98948A4CF371F92FCA5D64FBD11CD157FE1B6CAD969FE0516ABFD2C0BE0385E6FC78D45710CE99CED953; _gat=1; _gali=_search');
            if ($html) {

                $html = str_get_html($html);
                //Debug::show($html);
                $contentDom = $html->find('table#highlight', 0);
                if ($contentDom) {
                    $lsStatus = Document::getListStatus();
                    $lsStatus = array_keys($lsStatus);
                    $lsSecret = [Document::DOC_SECRET_HIGHT, Document::DOC_SECRET_NORMAL,];
                    $lsEmer = [Document::DOC_EMERGENCY_HIGHT, Document::DOC_EMERGENCY_NORMAL,];
                    $lsType = [Document::DOC_FROM, Document::DOC_TO, Document::DOC_IN];


                    $lsStaff = Staff::select(['_id'])->get()->keyBy('_id')->toArray();
                    $lsStaff = array_keys($lsStaff);

                    $lsProject = Project::select(['_id'])->get()->keyBy('_id')->toArray();
                    $lsProject = array_keys($lsProject);

                    $lsFiles = [
                        'http://datafile.chinhphu.vn/file-remote-v2/DownloadServlet?filePath=vbpq/2018/06/86.signed.pdf',
                        'http://datafile.chinhphu.vn/file-remote-v2/DownloadServlet?filePath=vbpq/2018/06/27BTRE.signed.pdf',
                        'http://datafile.chinhphu.vn/file-remote-v2/DownloadServlet?filePath=vbpq/2018/06/84.signed.pdf',
                        'http://datafile.chinhphu.vn/file-remote-v2/DownloadServlet?filePath=vbpq/2018/06/34-BGTVT.signed.pdf',
                        'http://datafile.chinhphu.vn/file-remote-v2/DownloadServlet?filePath=vbpq/2018/05/77%202018%20ND-CP.signed.pdf',
                        'http://datafile.chinhphu.vn/file-remote-v2/DownloadServlet?filePath=vbpq/2018/05/23.signed.pdf',
                    ];

                    foreach ($contentDom->find('tr') as $item) {
                        $td0 = $item->find('td', 0)->plaintext;
                        $td1 = $item->find('td', 1)->plaintext;
                        $td2 = $item->find('td', 2)->plaintext;


                        $type = $lsType[0];
                        if ($type == Document::DOC_IN) {
                            $lsCompany = Document::getTableCompany()->where('type', Document::TYPE_COMPANY_IN)->select(['_id', 'name'])->get()->keyBy('_id')->toArray();
                        } elseif ($type == Document::DOC_FROM) {
                            $lsCompany = Document::getTableCompany()->where('type', Document::TYPE_COMPANY_FROM)->select(['_id', 'name'])->get()->keyBy('_id')->toArray();
                        } else {
                            $lsCompany = Document::getTableCompany()->where('type', Document::TYPE_COMPANY_TO)->select(['_id', 'name'])->get()->keyBy('_id')->toArray();

                        }
                        $lsCompanyKey = array_keys($lsCompany);


                        shuffle($lsStatus);
                        shuffle($lsSecret);
                        shuffle($lsEmer);
                        shuffle($lsType);
                        shuffle($lsCompanyKey);
                        shuffle($lsFiles);
                        shuffle($lsProject);

                        $saveToDb = [
                            'name'           => trim($td2),
                            'num_page'       => rand(1, 20),
                            'status'         => $lsStatus[0],
                            'doc_secret'     => $lsSecret[0],
                            'doc_emergengy'  => $lsEmer[0],
                            'type'           => $lsType[0],
                            'company_enact'  => $lsCompanyKey[0],
                            'created_by'     => $lsStaff[0],
                            'project_id'     => $lsProject[0],
                            'updated_by'     => $lsStaff[1],
                            'created_at'     => Helper::randMongoDateTime(),
                            'doc_date_enact' => Helper::randMongoDateTime(),
                            'doc_date_come'  => Helper::randMongoDateTime(),
                            'updated_at'     => Helper::randMongoDateTime('30 May 2018', '24 Jun 2018'),
                        ];
                        for ($i = 0; $i < rand(0, count($lsFiles)); $i++) {
                            $saveToDb['files'][] = $lsFiles[$i];
                        }
                        for ($i = 0; $i < rand(1, count($lsCompanyKey)); $i++) {
                            $saveToDb['com_relation'][] = $lsCompanyKey[$i];
                        }
                        if (isset($lsCompany[$saveToDb['company_enact']])) {
                            $saveToDb['num_doc'] = Helper::convertToAlias($lsCompany[$saveToDb['company_enact']]['name'], '_') . '_' . BaseModel::getNextNumberWithYear(Document::table_name);
                        }
                        Debug::show($saveToDb);
                        Document::insert($saveToDb);
                    }
                } else {
                    echo "non";
                }

            }
        }

    }
}
