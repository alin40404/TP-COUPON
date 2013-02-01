<?php
//PHPExcel封装类
/** Include path **/
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.LIB_PATH."ORG/phpExcel/");
 
/** PHPExcel */
include "PHPExcel.php";
 
/** PHPExcel_Writer_Excel2007 */
include "PHPExcel/Writer/Excel2007.php";
class excel
{
    private static  $_instance = null;
    
    public $objActSheet = null;
    
    public $objPHPExcel = null;
    
    private $_objWriter = null;
    
    private function __construct($sheetTitle = null)
    {
        // Create new PHPExcel object
        $this->objPHPExcel = new PHPExcel();

        // Set properties
        $this->objPHPExcel->getProperties()->setCreator("360mt");
        $this->objPHPExcel->getProperties()->setLastModifiedBy("360mt");
        $this->objPHPExcel->getProperties()->setTitle("360媒体");
        $this->objPHPExcel->getProperties()->setSubject("360媒体");
        $this->objPHPExcel->getProperties()->setDescription("中国最大的户外媒体在线交易平台.");

        // Add some data
        $this->objPHPExcel->setActiveSheetIndex(0);
        $this->objActSheet = $this->objPHPExcel->getActiveSheet();
        //设置当前活动sheet的名称
        $this->objActSheet->setTitle($sheetTitle);
        
        $this->_objWriter = new PHPExcel_Writer_Excel2007($this->objPHPExcel);
    }
    
    /**
     * 获取唯一实例
     *
     * @param string $sheetTitle        表格名称
     * @return excel
     */
    public static function getInstance($sheetTitle = null)
    {
        if(self::$_instance !== null){
            return self::$_instance;
        }
        self::$_instance = new self($sheetTitle);
        return self::$_instance;
    }
    
    /**
     * 初始化单元格样式
     *
     * @param string    $cell   单元格
     * @return Object
     */
    public function setStyle($cell)
    {
        //设置宽度
//        $this->objActSheet->getColumnDimension('B')->setAutoSize(true);
//        $this->objActSheet->getColumnDimension('A')->setAutoSize(true);

        $objStyleA1 = $this->objActSheet->getStyle($cell);
        $objStyleA1
        ->getNumberFormat()
        ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        //设置字体
        $objFontA1 = $objStyleA1->getFont();
        $objFontA1->setName('Courier New');
        $objFontA1->setSize(10);
        $objFontA1->setBold(true);
        $objFontA1->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $objFontA1->getColor()->setARGB('00000000');

        //设置对齐方式
        $objAlignA1 = $objStyleA1->getAlignment();
        $objAlignA1->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objAlignA1->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //设置边框
        $objBorderA1 = $objStyleA1->getBorders();
        $objBorderA1->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objBorderA1->getTop()->getColor()->setARGB('FFFF0000'); // color
        $objBorderA1->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objBorderA1->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objBorderA1->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //设置填充颜色
        $objFillA1 = $objStyleA1->getFill();
        $objFillA1->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objFillA1->getStartColor()->setARGB('D8ECFE00');
        return $objStyleA1;
    }
    
    /**
     * 输出到浏览器
     *
     * @param string $outputFileName
     * @return void
     */
    public function output($outputFileName)
    {
        //ob_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream; charset=utf-8");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outputFileName.'"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $this->_objWriter->save('php://output');
        exit(0);
    }
    
    public function __destruct()
    {
        $this->objActSheet = null;
    
        $this->objPHPExcel = null;
    
        $this->_objWriter = null;
    }
}