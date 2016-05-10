<?php
use micro\controllers\Controller;
use micro\js\Jquery;
use micro\utils\RequestUtils;
use micro\orm\DAO;

class MyDisques extends Controller{
	public function initialize(){
		if(!RequestUtils::isAjax()){
			$this->loadView("main/vHeader.html",array("infoUser"=>Auth::getInfoUser()));
		}
	}
	public function index() {
		echo Jquery::compile();

		$user = Auth::getUser();
		//On recupère les disques de chaque utilisateur
		DAO::getOneToMany($user, "disques");

		// Pour chaque disque on effectue une itération
		foreach ($user->getDisques() as $disque) {
			DAO::getOneToMany($disque, "disqueTarifs");
			// Utilisation d'exception pour verifier que le disque existe bien, sinon ses variables à 0.
			try {
				$disque->sizeUsed = DirectoryUtils::formatBytes($disque->getSize());
				$disque->sizeMax = DirectoryUtils::formatBytes($disque->getQuota());
				$disque->percentUsed = round(($disque->getSize()/$disque->getQuota())*100);
			} catch (Exception $e) {
				$disque->sizeUsed = 0;
				$disque->sizeMax = 0;
				$disque->percentUsed = 0;
			}

			// Positionnement du type de statut pour le progressbar boostrap
			if ($disque->percentUsed <= 10) {
				$disque->statut = "info";
			} else if ($disque->percentUsed > 10 && $disque->percentUsed <= 50) {
				$disque->statut = "success";
			} else if ($disque->percentUsed > 50 && $disque->percentUsed <= 80) {
				$disque->statut = "warning";
			} else {
				$disque->statut = "danger";				
			}
		}

		// On charge la vue et on lui passe l'object $user
		$this->loadView("mydisques/index.html", array("user"=>$user));
	}

	public function finalize(){
		if(!RequestUtils::isAjax()){
			$this->loadView("main/vFooter.html");
		}
	}

}