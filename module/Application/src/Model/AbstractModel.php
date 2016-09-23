<?php 
	
	namespace Application\Model;

	use Zend\Filter\Word\UnderscoreToCamelCase;

	class AbstractModel {

		protected $tabela;

		public function __construct($camposValores = null) {
			if( empty($this->tabela) ) {
				throw new \Exception('O atributo \'tabela\' está vazio');
			}

			if( !empty($camposValores) ) {
				if( is_int($camposValores) ) {
					$this->selecionarPorId($camposValores);
					return;
				}

				if( is_object($camposValores) ) {
					$camposValores = get_object_vars($camposValores);
				}
			
				foreach( $camposValores as $campo => $valor ) {
					$this->$campo = $valor;	
				}
			}
		}

		public function __get($atributo) {
			$nomeMetodo = 'get'.(new UnderscoreToCamelCase)->filter($atributo);
			if( method_exists($this, $nomeMetodo) ) {
				return $this->{$nomeMetodo}();
			}

			if(property_exists($this, $atributo)) {
				return $this->$atributo;
			}
		}

		public function __set($atributo, $valor) {
			$nomeMetodo = 'set'.(new UnderscoreToCamelCase)->filter($atributo);
			if( method_exists($this, $nomeMetodo) ) {
				$this->{$nomeMetodo}($valor);
				return;
			}			

			if(property_exists($this, $atributo)) {
				$this->$atributo = $valor;
				return;
			}	
		}

		public function listar($condicao = null, $order = null, $limit = null, $offset = null) {
			$resultSet = Conexao::getInstance()->executarSql($this->tabela, ['select' => ['condicao' => $condicao, 'order' => $order, 'limit' => $limit, 'offset' => $offset]]);
			$resultados = [];
			foreach( $resultSet as $r ) {
				$nomeClasse = get_class($this);
				$object = new $nomeClasse($r);	
				$resultados[] = $object;
			}
			return $resultados;
		}

		public function selecionarPorId(int $id) {
			$lista = $this->listar('id = '.$id);
			if( !empty($lista) )
				$this->__construct($lista[0]);
		}

		public function salvar() {	
			if( !empty($this->id) ) {
				$resultSet = Conexao::getInstance()->executarSql($this->tabela, ['update' => ['condicao' => 'id = '.$this->id, 'set' => $this->getColunas()]]);
				return $resultSet->getAffectedRows();
			} else {
				$resultSet = Conexao::getInstance()->executarSql($this->tabela, ['insert' => ['values' => $this->getColunas()]]);
				return $resultSet->getGeneratedValue();
			}
		}

		public function excluir($logicamente = false) {
			if( !empty($this->id) ) {
				$resultSet = Conexao::getInstance()->executarSql($this->tabela, ['delete' => ['condicao' => 'id = '.$this->id, 'logicamente' => $logicamente]]);
			}
		}

		public function getColunas($for = null) {
			$excluidos = ['tabela'];
			if( $for == 'update' ) {
				$excluidos[] = 'id';
			}
			return array_filter(get_object_vars($this), function($coluna) use ($excluidos){ return !in_array($coluna, $excluidos); }, ARRAY_FILTER_USE_KEY);
		}

	}

?>
