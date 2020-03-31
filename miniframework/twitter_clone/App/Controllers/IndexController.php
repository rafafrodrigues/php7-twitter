<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';

		$this->render('index');
	}

	public function inscreverse() {
		$this->view->usuario = array(
           'nome' => '',
           'email' => '',
           'senha' => '',
	  	);

		$this->view->erroCadastro = false;

		$this->render('inscreverse');
	}

    //registrar ação após direcionar no form e no script Route.php
	public function registrar() {
      //var_dump($_POST);

	  //instanciar objeto
	  $usuario = Container::getModel('Usuarios');

	  $usuario->__set('nome', $_POST['nome']);	
	  $usuario->__set('email', $_POST['email']);	
	  //$usuario->__set('senha', $_POST['senha']);	
	  $usuario->__set('senha', md5($_POST['senha']));

	  //var_dump($usuario);
	  
	  if ($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {
	  		$usuario->salvar();

	  		$this->render('cadastro');
	  	
	  } else {
	  	//recuperar dados informados no formulário
	  	$this->view->usuario = array(
           'nome' => $_POST['nome'],
           'email' => $_POST['email'],
           'senha' => $_POST['senha'],
	  	);

	  	$this->view->erroCadastro = true;
	  	
	  	$this->render('inscreverse');
	  }
	  
	}
}


?>