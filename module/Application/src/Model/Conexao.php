<?php

	namespace Application\Model;
	
	use Zend\Db\Adapter\Adapter;
	use Zend\Db\Sql\Sql;
	use Zend\Db\Metadata\Metadata;

	class Conexao {
	
		public $adapter;
		public $sql;

		private function __construct(){}

		public static function getInstance() {
			$conexao = new Conexao();
			if( is_null($conexao->adapter) ) {
				$conexao->adapter = new Adapter([
				    'driver'   => 'Mysqli',
				    'database' => 'teste',
				    'username' => 'root',
				    'password' => '123',
				]);
			}

			if( is_null($conexao->sql) ) {
				$conexao->sql = new Sql($conexao->adapter);
			}	
			return $conexao;
		}

		/* 
			Método para executarSql de um mesmo lugar
			o indice do array deve ser a operação: (select,insert, update ou delete) e o valor deve ser outro array com os elementos que a operação exige: select => (where, order, limit, offset), insert => (values), update => (set, where), delete => (delete)
		*/
		public function executarSql(string $tabela, array $sql) {
			switch( key($sql) ) {
				case 'select' :
					$objectSql = $this->sql->select()
								 ->from($tabela)
								 ->where($sql['select']['condicao']);
					if( $this->colunaExiste($tabela, 'excluido') ) {
						$objectSql->where('excluido = 0');
					}					

					if( !empty($sql['select']['order']) ) {
						$objectSql->order($sql['select']['order']);
					}
					if( !empty($sql['select']['limit']) ) {
						$objectSql->limit($sql['select']['limit']);
					}
					if( !empty($sql['select']['offset']) ) {
						$objectSql->offset($sql['select']['offset']);
					}
								 
				break;
				case 'update' :
					$objectSql = $this->sql->update()->table($tabela)->set($sql['update']['set'])->where($sql['update']['condicao']);
				break;
				case 'insert' :
					$objectSql = $this->sql->insert()->into($tabela)->values($sql['insert']['values']);
				break;
				case 'delete' :
					if( $sql['delete']['logicamente'] && $this->colunaExiste($tabela, 'excluido') ) {
						$objectSql = $this->sql->update()->table($tabela)->set('excluido = 1')->where($sql['delete']['condicao']);
					} else {
						$objectSql = $this->sql->delete()->from($tabela)->where($sql['delete']['condicao']);
					}
				break;
			}
			echo $this->sql->buildSqlString($objectSql);	
			return $this->adapter->query($this->sql->buildSqlString($objectSql), $this->adapter::QUERY_MODE_EXECUTE);
		}	

		public function colunaExiste(string $tabela, string $nomeColuna) {
			$metadata = new Metadata($this->adapter);
			$tabela = $metadata->getTable($tabela);
			$colunas = $tabela->getColumns();
			return (bool) count(array_filter($colunas, function($coluna) use ($nomeColuna) { return $coluna->getName() == $nomeColuna; }));	
		}
	
	}
