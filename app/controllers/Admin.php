<?php
use micro\orm\DAO;
class Admin extends \BaseController {

	public function index() {
		$data = new stdClass();
		$data->total = new stdClass();
		$data->today = new stdClass();

		$data->total->users = DAO::count("Utilisateur");
		$data->total->disques = DAO::count("Disque");
		$data->total->tarifs = DAO::count("Tarif");
		$data->total->services = DAO::count("Service");

		$data->today->users = DAO::count("Utilisateur", "createdAt = NOW()");
		$data->today->disques = DAO::count("Disque", "createdAt = NOW()");

        $this->loadView('admin/index.html', ["data" => $data]);
	}
}