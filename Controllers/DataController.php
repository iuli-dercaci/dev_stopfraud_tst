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
        $viewParams = array();
        if (   isset($_POST['submit'])
            && isset($_FILES['uploadedfile']['tmp_name'])
            && $_FILES['uploadedfile']['error'] == 0
        ){
            $region = $_POST['regions'];
            $structure_id = $this->_getCountryId($region);
            $parsed_data = array();
            $file_data = $this->_getExcelData($_FILES['uploadedfile']['tmp_name']);
            foreach ($file_data as $line) {
                $line_data = array();
                if ((int)$line[0]){// line has id
                    $line_data['ParentID'] = $structure_id;
                    $zones_pattern = '/(?<z1>\d)\D?(?<r3>\d{2})\D*(?<c5>\d*)/';
                    if (false != ($numbers = preg_match($zones_pattern, $line[1], $m))){
                        $line_data['z1'] = $m['z1'];
                        $line_data['r3'] = $m['r3'];
                        $line_data['c5'] = $m['c5'];
                    }
                    $line_data['Full_Prefix'] = preg_replace('/\D/', '', $line[1]);
                    $line_data['ZoneID'] = $region;
                    $line_data['Region1_rus'] = $line_data['Region1_rom'] = $line_data['Region1_eng'] = $line[4];
                    $line_data['City_rus'] = $line_data['City_rom'] = $line_data['City_eng'] = $line[2];
                    $line_data['Name_rus'] = $line_data['Name_rom'] = $line_data['Name_eng'] = $line[3];
                    $line_data['TypeID'] = $this->_getPhoneType($line[5]);
                }
                if (!empty($line_data)){
                    $parsed_data[] = $line_data;
                }
            }
            $this->_insertIntoDb($parsed_data);
            $data = array(
                'message' => 'Successful upload',
                'result' => 'Added ' . count($parsed_data) . ' lines'
            );
        } else {
            $data = array('message' => 'file upload error, code: ' . $_FILES['uploadedfile']['error']);
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

    private function _insertIntoDb($data){
        $db = $this->helper('database')->db;
        foreach($data as $line){
            $columns = rtrim(join(', ', array_keys($line)), ',');
            $values  = rtrim(':' . join(', :', array_keys($line)), ', :');
            $stmt = $db->prepare('INSERT INTO ut_ref_phonecode (' . $columns . ') VALUES (' . $values . ')');
            $stmt->execute($line);
        }
    }

    private function _getPhoneType($name){
        $name = trim(strtolower($name));
        $result = 'Unknown';
        if(isset($this->_config['phone_types'][$name])){
            $result = $this->_config['phone_types'][$name];
        }
        return $result;
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
        $stmt = $db->query('SELECT ref_phonezoneID, Caption_eng FROM ut_ref_phonezone');
        $stmt->setFetchMode(PDO::FETCH_OBJ);

        while($row = $stmt->fetch()){
            $result[] = array(
                'value' => $row->ref_phonezoneID,
                'text'  => $row->Caption_eng
            );
        }
        return $result;
    }

    private function _getCountryId($zoneId){

        $db = $this->helper('database')->db;
        $stmt = $db->query('SELECT ParentID FROM ut_structure WHERE FolderName REGEXP "(Zone ' . (int)$zoneId . ').*" LIMIT 1');
        $stmt->setFetchMode(PDO::FETCH_OBJ);

        return $stmt->fetch()->ParentID;
    }
}
