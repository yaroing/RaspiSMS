<?php
	/**
	 * Page des commandes
	 */
	class commands extends Controller
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
		 * Cette fonction retourne toutes les commandes, sous forme d'un tableau permettant l'administration de ces commandess
		 * @return void;
		 */
		public function showAll()
		{
			//Creation de l'object de base de données
			global $db;
			
			//Recupération des commandes
			$commands = $db->getFromTableWhere('commands');

			$this->render('commands', array(
				'commands' => $commands,
			));
			
		}

		/**
		 * Cette fonction va supprimer une liste de commands
		 * @param int... $ids : Les id des commandes à supprimer
		 * @return boolean;
		 */
		public function delete(...$ids)
		{
			if (!internalTools::verifyCSRF())
			{
				$_SESSION['errormessage'] = 'Jeton CSRF invalide !';
				header('Location: ' . $this->generateUrl('commands', 'showAll'));
				return false;
			}

			//Create de l'object de base de données
			global $db;
			
			$db->deleteCommandsIn($ids);
			header('Location: ' . $this->generateUrl('commands'));		
			return true;
		}

		/**
		 * Cette fonction retourne la page d'ajout d'une commande
		 */
		public function add()
		{
			$this->render('addCommand');
		}

		/**
		 * Cette fonction retourne la page d'édition des commandes
		 * @param int... $ids : Les id des commandes à editer
		 */
		public function edit(...$ids)
		{
			global $db;

			$commands = $db->getCommandsIn($ids);
			$this->render('editCommands', array(
				'commands' => $commands,
			));
		}

		/**
		 * Cette fonction insert une nouvelle commande
		 * @param string $_POST['name'] : Le nom de la commande
		 * @param string $_POST['script'] : Le script a appeler
		 * @param boolean $_POST['admin'] : Si la commande necessite les droits d'admin (par défaut non)
		 * @return boolean;
		 */
		public function create()
		{
			if (!internalTools::verifyCSRF())
			{
				$_SESSION['errormessage'] = 'Jeton CSRF invalide !';
				header('Location: ' . $this->generateUrl('commands', 'showAll'));
				return true;
			}

			global $db;

			$nom = $_POST['name'];
			$script = $_POST['script'];
			$admin = (isset($_POST['admin']) ? $_POST['admin'] : false);

			if (!$db->insertIntoTable('commands', ['name' => $nom, 'script' => $script, 'admin' => $admin]))
			{
				$_SESSION['errormessage'] = 'Impossible créer cette commande.';
				header('Location: ' . $this->generateUrl('commands', 'add'));
				return false;
			}

			$db->insertIntoTable('events', ['type' => 'COMMAND_ADD', 'text' => 'Ajout commande : ' . $nom . ' => ' . $script]);
			
			$_SESSION['successmessage'] = 'La commande a bien été créée.';
			header('Location: ' . $this->generateUrl('commands', 'showAll'));
			return true;

		}

		/**
		 * Cette fonction met à jour une commande
		 * @param array $_POST['commands'] : Un tableau des commandes avec leur nouvelle valeurs
		 * @return boolean;
		 */
		public function update()
		{
			if (!internalTools::verifyCSRF())
			{
				$_SESSION['errormessage'] = 'Jeton CSRF invalide !';
				header('Location: ' . $this->generateUrl('commands', 'showAll'));
				return false;
			}

			global $db;
			
			$errors = array(); //On initialise le tableau qui contiendra les erreurs rencontrés

			//Pour chaque commande reçu, on boucle en récupérant son id (la clef), et la commande elle-même (la value)
			foreach ($_POST['commands'] as $id => $command)
			{
				$db->updateTableWhere('commands', $command, ['id' => $id]);
			}

			$_SESSION['successmessage'] = 'Toutes les commandes ont été modifiées avec succès.';
			header('Location: ' . $this->generateUrl('commands', 'showAll'));
		}
	}
