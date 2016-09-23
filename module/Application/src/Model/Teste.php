<?php
	namespace Application\Model;

	class Teste extends AbstractModel {

		protected $id;
		protected $nome;
		protected $email;
		protected $tabela = 'teste';

		public function getId() {
			return $this->id;
		}

		public function setId($id) {
			$this->id = strtoupper($id);
		}	
			
	}
