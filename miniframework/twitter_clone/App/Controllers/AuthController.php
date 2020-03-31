<?php

namespace App\Controllers;

//importar os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {
	public function autenticar() {
		//print_r($_POST);
		$usuario = Container::getModel('Usuarios');

		$usuario->__set('email', $_POST['email']);
		//$usuario->__set('senha', $_POST['senha']);
		$usuario->__set('senha', md5($_POST['senha']));

		//$retorno = $usuario->autenticar();
		//print_r($retorno);

        $usuario->autenticar();
        if ($usuario->__get('id') != '' && $usuario->__get('nome') != '') {
        	//echo "Autenticado";
        	session_start();

        	$_SESSION['id'] = $usuario->__get('id');
        	$_SESSION['nome'] = $usuario->__get('nome');

        	header('Location: /timeline');
        } else {
        	//echo "Erro na autenticação";
        	header('Location: /?login=erro');
        }
        
	}

	public function sair() {
		session_start();
		session_destroy();
		header('Location: /');
	}
}

?>