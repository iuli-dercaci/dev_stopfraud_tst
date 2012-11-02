<?php
/**
 *
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 10/31/12
 * @time 9:59 PM
 * phpexcelreader http://sourceforge.net/projects/phpexcelreader/
 */
include_once '../Classes/AbstractController.php';
include_once '../lib/PHPExcel/PHPExcel.php';
include_once '../lib/PHPExcel/PHPExcel/IOFactory.php';

class DataController extends AbstractController
{
    /**
     * class constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * upload form
     */
    public function indexAction(){
        $viewParams = array(
            'controller' => 'Data',
            'action'     => 'index',
        );
        $data = array('regions' => $this->_getListOfRegions());
        $this->_view->setParams($viewParams)->render($data);
    }

    /**
     * upload process
     */
    public function addAction(){



        $data = array(
            'result'  => false,
            'message' => 'Upload was failed'
        );
        if (isset($_POST['submit']) && isset($_FILES['uploadedfile']['tmp_name'])){
            $data = array(
                'message' => 'Successful upload',
                'result' => $this->_getExcelData($_FILES['uploadedfile']['tmp_name'])
            );
        }
        $viewParams = array(
            'controller' => 'Data',
            'action'  => 'add'
        );
        $this->_view->setParams($viewParams)->render($data);

        /*
         * saving uploaded file
        $target_path = $this->_config['paths']['root'] . $this->_config['paths']['upload'] . '/' . basename($_FILES['uploadedfile']['name']);

        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
            echo "The file ".  basename( $_FILES['uploadedfile']['name']).
                " has been uploaded";
        } else{
            echo "There was an error uploading the file, please try again!";
        }
        */
    }

    /**
     * data from excel file
     * @param $file
     * @return array
     */
    private function _getExcelData($file){
        $PHPExcel = PHPExcel_IOFactory::load($file);
        return $PHPExcel->getActiveSheet()->toArray();
    }

    /**
     * list of regions
     * @return array
     */
    private function _getListOfRegions(){

        $result = array();

        $db = $this->helper('database')->db;
        $stmt = $db->query('SELECT ParentID, Caption_eng FROM ut_ref_phonezone');
        $stmt->setFetchMode(PDO::FETCH_OBJ);

        while($row = $stmt->fetch()){
            $result[] = array(
                'value' => $row->ParentID,
                'text'  => $row->Caption_eng
            );
        }
        return $result;
    }
}
