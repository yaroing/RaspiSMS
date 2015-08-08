<?php
	/**
	 * Page des SMS reçus
	 */
	class receiveds extends Controller
	{
		/**
		 * Cette fonction est appelée avant toute les autres : 
		 * Elle vérifie que l'utilisateur est bien connecté
		 * @return void;
		 */
		public function before()
		{
			internalTools::verifyConnect();
		}

		/**
		 * Cette fonction est alias de showAll()
		 */	
		public function byDefault()
		{
			$this->showAll();
		}
		
		/**
		 * Cette fonction retourne tous les SMS envoyés, sous forme d'un tableau
		 * @param int $page : La page à consulter. Par défaut 0
		 * @return void;
		 */
		public function showAll($page = 0)
		{
			//Creation de l'object de base de données
			global $db;
			
			$page = (int)($page < 0 ? $page = 0 : $page);
			$limit = 25;
			$offset = $limit * $page;
			

			//Récupération des SMS envoyés triés par date, du plus récent au plus ancien, par paquets de $limit, en ignorant les $offset premiers
			$receiveds = $db->getFromTableWhere('receiveds', [], 'at', true, $limit, $offset);

			return $this->render('receiveds', array(
				'receiveds' => $receiveds,
				'page' => $page,
				'limit' => $limit,
				'nbResults' => count($receiveds),
			));
		}
	}
