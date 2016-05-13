<?php
use micro\orm\DAO;
use micro\js\Jquery;
class Disques extends \_DefaultController {

	public function __construct(){
		parent::__construct();
		$this->title="Disques";
		$this->model="Disque";
	}

	public function initialize() {
		// On execute la fonction parente
		parent::initialize();

		// On change le breadcrumb
		$breadcrumb = "Disques";
		Jquery::setHtml('.breadcrumb', '<li><a href="'.$GLOBALS['config']['siteUrl'].'"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;Accueil</a></li><li><a href="#">&nbsp;'.$breadcrumb.'</a></li>');
		echo Jquery::compile();
	}	

	public function index() {
		// Si l'utilisateur est connecté, on appelle la fonction parente pour afficher la page
		if (Auth::getUser() != NULL) {
			parent::index();
		// Sinon message d'erreur
		} else {
			$this->loadView("main/vInfo",array("message"=>"Vous devez être connecté pour voir cette page","type"=>"danger","dismissable"=>true,"timerInterval"=>0,"visible"=>true));
			die();
		}
	}

	public function create() {
		$this->frm();
	}

	public function frm($id=NULL){
		$disque=$this->getInstance($id);
		if($disque->getUtilisateur() != NULL) {
			$idUtilisateur = $disque->getUtilisateur()->getId();
		} else {
			$idUtilisateur = Auth::getUser()->getId();
            $disque = new stdClass();
			$disque->id = 0;
			$disque->nom = "";
		}
		$this->loadView("disques/vAdd.html",array("disque"=>$disque, 'idUtilisateur'=>$idUtilisateur));
	}

	public function update(){
		// Si un ID et un nom sont passés en paramètres, il s'agit de mettre à jour un disque ***
		if($_POST["id"] && $_POST['nom']) {
			// On recupère le chemin ABSOLU du dossier (disque) grace à l'ancien nom du disque disque et au variable globale
			$oldfolder = DAO::getOne('Disque', $_POST['id'])->getNom();
			$basepath = (dirname(dirname(__DIR__))."/files/".$GLOBALS['config']['cloud']['prefix'].Auth::getUser()->getLogin().'/');
			$actualpath = $basepath.$oldfolder;
			$newpath = $basepath.$_POST['nom'];
			// Ensuite une exception classique pour tester si tout s'est bien passé !
			try {
				rename($actualpath, $newpath);
			} catch (Exception $e) {
				die("Erreur pour renommer le dossier");   
			}
		// *** Sinon, il s'agit de créer un disque
		} else {
			if ($_POST['nom']) {
				// On recupère le chemin ABSOLU du dossier (disque) comme au dessus
				$basepath = (dirname(dirname(__DIR__))."/files/".$GLOBALS['config']['cloud']['prefix'].Auth::getUser()->getLogin().'/');
				$newpath = $basepath.$_POST['nom'];
				// Ensuite une exception classique pour tester si la création a fonctionné !
				try {
					mkdir($newpath);
				} catch (Exception $e) {
					die("Erreur de créer le dossier");   
				}
			}
		}
		// On appelle ensuite la fonction update du DefaultController pour mettre à jour les paramètres en base de données.
		parent::update();
	}

	// Réecriture de la fonction parente
	// On "set" l'objet utilisateur dans l'objet disque afin de pouvoir utiliser la fonction toString de Disque
	protected function setValuesToObject(&$object) {
		parent::setValuesToObject($object);
		if(isset($_POST["idUtilisateur"])) {
			$user = DAO::getOne('Utilisateur', $_POST["idUtilisateur"]);
			$object->setUtilisateur($user);
		}
	}
}