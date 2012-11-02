<?php
/**
 *
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 10/29/12
 * @time 11:09 PM
 */
include_once '../Classes/AbstractController.php';

class IndexController extends AbstractController
{
    //landing page
    public function indexAction(){

        $viewParams = array(
            'controller' => 'Index',
            'action' => 'index'
        );
        $this->_view->setParams($viewParams)->render(array('name' => 'Stas'));
    }


}
