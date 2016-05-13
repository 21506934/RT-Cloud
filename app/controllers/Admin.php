<?php
use micro\orm\DAO;
use micro\js\Jquery;
class Admin extends \BaseController {

	public function initialize() {
		// On execute la fonction parente
		parent::initialize();

		// Si l'utilisateur connecté n'est pas admin, on lui affiche un message d'erreur et on arrete la page
		if((Auth::getUser() == NULL) || ((int)Auth::getUser()->getAdmin() != 1)) {
			$this->loadView("main/vInfo",array("message"=>"Accès à une ressource non autorisée","type"=>"danger","dismissable"=>true,"timerInterval"=>0,"visible"=>true));
			die();
		}

		// On change le breadcrumb
		$breadcrumb = "Administration";
		Jquery::setHtml('.breadcrumb', '<li><a href="'.$GLOBALS['config']['siteUrl'].'"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;Accueil</a></li><li><a href="#">&nbsp;'.$breadcrumb.'</a></li>');
		echo Jquery::compile();
	}

	public function index() {
		$data = new stdClass();
		$data->total = new stdClass();
		$data->today = new stdClass();

		$data->total->users = DAO::count("Utilisateur");
		$data->total->disques = DAO::count("Disque");
		$data->total->tarifs = DAO::count("Tarif");
		$data->total->services = DAO::count("Service");

		$data->today->users = DAO::count("Utilisateur", "DAY(createdAt) = DAY(NOW())");
		$data->today->disques = DAO::count("Disque", "DAY(createdAt) = DAY(NOW())");

		$this->loadView('admin/index.html', ["data" => $data]);
	}

	public function users() {
		$users = DAO::getAll('Utilisateur');

		foreach ($users as $user) {
			DAO::getOneToMany($user, 'disques');   
			$user->nbDisques = count($user->getDisques());
			$user->montant = 0;
			foreach ($user->getDisques() as $disque) {
				DAO::getOneToMany($disque, 'disqueTarifs');
				if ($disque->getTarif() != NULL) {
					$user->montant += $disque->getTarif()->getPrix();
				}
			}
		}

		$this->loadView('admin/users.html', ["users" => $users]);
	}

	public function disques($idUtilisateur = NULL) {

		$cond = ($idUtilisateur) ? "id = $idUtilisateur" : "";
		$users = DAO::getAll('Utilisateur', $cond);

		foreach ($users as $user) {
			DAO::getOneToMany($user, 'disques');   

			foreach ($user->getDisques() as $disque) {
				// Utilisation d'exception pour verifier que le disque existe bien, sinon ses variables à 0.
				try {
					$disque->percentUsed = round(($disque->getSize()/$disque->getQuota())*100);
				} catch (Exception $e) {
					$disque->percentUsed = 0;
				}
			}
		}

		$this->loadView('admin/disques.html', ["users" => $users]);

	}
}