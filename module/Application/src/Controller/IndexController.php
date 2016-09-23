<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Teste;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
//		$teste = new Teste();

//		$teste->id = 'dsklajdalsdj';
//	
//		var_dump($teste->id);
//		var_dump($teste->tabela);
//		$teste->selecionarPorId(1);
		//$teste->nome = 'João Teste dsadasd';
		//$teste->email = 'ratojampa@yahoo.com.br';
		//$teste->salvar();
//		$teste->excluir(true);

		

		var_dump(\Application\Model\Conexao::getInstance()->colunaExiste('teste', 'idw'));

        return new ViewModel();
    }
}
