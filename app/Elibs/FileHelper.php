<?php
/**
 * Created by ngankt2@gmail.com
 * Website: https://techhandle.net
 */


namespace App\Elibs;


use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FileHelper
{
    const EXCEL_EXTENSIONS = [
        'xls', 'xlsx', 'csv'
    ];
    const WORD_EXTENSIONS = [
        'rtf', 'odt', 'doc', 'docx'
    ];
    const POWERPOIN_EXTENSIONS = [
        'ppt', 'pptx'
    ];
    const IMAGE_EXTENSIONS = [
        'png', 'jpg', 'jpeg'
    ];
    const DOCUMENT_EXTENSIONS = [
        'txt', 'html', 'xhtml', 'htm'
    ];

    static function getFileTypeIcon($file)
    {
        $ext = Helper::getFileExtension($file);
        $ext = trim($ext,'.');
        $icon = 'icon-file-text2';
        if (in_array($ext, self::EXCEL_EXTENSIONS)) {
            $icon = "icon-file-excel text-success";
        } elseif (in_array($ext, self::WORD_EXTENSIONS)) {
            $icon = "icon-file-word text-primary";
        } elseif (in_array($ext, ['pdf'])) {
            $icon = "icon-file-pdf text-warning";
        } elseif (in_array($ext, self::IMAGE_EXTENSIONS)) {
            $icon = "icon-file-picture2 text-success-800";
        }

        return "<i class='" . $icon . "'></i>";
    }
    static function readFileExcel($file){
        /*$reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);*/
        $fileType = IOFactory::identify($file);
        $reader = IOFactory::createReader($fileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
            $cells = [];
            foreach ($cellIterator as $cell) {
                $InvDate = $cell->getValue();
                $cell_getValue = '';
                if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell) || (is_numeric($InvDate) && strlen($InvDate) == 5)) {
                    $_InvDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($InvDate);
                    $_InvDate = $_InvDate->format('Y-m-d H:i:s');
                    if(isset($_InvDate)){
                        $_InvDateAr = explode(' ',$_InvDate);
                        $_InvDateArr = explode('-',$_InvDateAr[0]);
                        $cell_getValue = $_InvDateArr[2].'-'.$_InvDateArr[1].'-'.$_InvDateArr[0];
                    }
                }
                if($cell_getValue){
                    $cells[] = $cell_getValue;
                }else{
                    $cells[] = $cell->getValue();
                }
            }
            $rows[] = $cells;
        }
        return $rows;
    }
    static function readFileExcelFromString($file,$data)
    {
        /*$reader = new Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($file);*/
        $fileType = IOFactory::identify($file);
        $reader = IOFactory::createReader($fileType);
        $reader->setReadDataOnly(true);

        $temp = tempnam(sys_get_temp_dir(), 'TMP_');
        file_put_contents($temp,$data);
        if($data)
        {
            $spreadsheet = $reader->load($temp);

        }
        else{
            return [];
        }


        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
            $cells = [];
            foreach ($cellIterator as $cell) {
                $InvDate = $cell->getValue();
                $cell_getValue = '';
                if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell) || (is_numeric($InvDate) && strlen($InvDate) == 5)) {
                    $_InvDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($InvDate);
                    $_InvDate = $_InvDate->format('Y-m-d H:i:s');
                    if(isset($_InvDate)){
                        $_InvDateAr = explode(' ',$_InvDate);
                        $_InvDateArr = explode('-',$_InvDateAr[0]);
                        $cell_getValue = $_InvDateArr[2].'-'.$_InvDateArr[1].'-'.$_InvDateArr[0];
                    }
                }
                if($cell_getValue){
                    $cells[] = $cell_getValue;
                }else{
                    $cells[] = $cell->getValue();
                }
            }
            $rows[] = $cells;
        }
        unlink($temp);
        return $rows;
    }

    static function ExportFileExcel($data=false,$filename = "export_data.xlsx"){
        if(!$data || is_string($data)){
            return false;
        }
/// Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

// Set document properties
        $spreadsheet->getProperties()->setCreator('TEXO')
            ->setLastModifiedBy('TEXO')
            ->setTitle('Office 2007 XLSX Document')
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Customer file');

// Add some data
// Header
        try {
            $spreadsheet->getActiveSheet()->fromArray(array_keys(current($data)), null, 'A1');
        } catch (Exception $e) {
        }
// Data
        try {
            $spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
        } catch (Exception $e) {
        }

// Rename worksheet
        try {
            $spreadsheet->getActiveSheet()->setTitle('sheet');
        } catch (Exception $e) {
        }

// Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
//        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
//        $writer->save("export_customer_page_".@$_REQUEST['page'].".xlsx");
//        return public_link("export_customer_page_".@$_REQUEST['page'].".xlsx");
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}