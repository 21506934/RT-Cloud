<?php
use micro\js\Jquery;
/**
 * Gestion des users
 * @author jcheron
 * @version 1.1
 * @package nas.controllers
 */
class Users extends \_DefaultController {

	public function __construct(){
		parent::__construct();
		$this->title="Utilisateurs";
		$this->model="Utilisateur";
	}

	public function initialize() {
		$breadcrumb = "Utilisateurs";
		Jquery::setHtml('.breadcrumb', '<li><a href="'.$GLOBALS['config']['siteUrl'].'"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;Accueil</a></li><li><a href="#">&nbsp;'.$breadcrumb.'</a></li>');
		echo Jquery::compile();
	}

	public function frm($id=NULL){
		$user=$this->getInstance($id);
		$disabled="";
		$this->loadView("user/vAdd.html",array("user"=>$user,"disabled"=>$disabled));
	}

	/* (non-PHPdoc)
	 * @see _DefaultController::setValuesToObject()
	 */
	protected function setValuesToObject(&$object) {
		parent::setValuesToObject($object);
		$object->setAdmin(isset($_POST["admin"]));
	}

	public function tickets(){
		$this->forward("tickets");
	}
}