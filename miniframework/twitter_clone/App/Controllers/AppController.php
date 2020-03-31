<?php

namespace App\Controllers;

//importar os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

   public function timeline() {
   	  session_start();

   	  if ($_SESSION['id'] != '' && $_SESSION['nome'] != '') {
   	  	//print_r($_SESSION);
        
        //recuperação dos tweets
        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);
        
        //array
        $tweets = $tweet->getAll();
        /* echo "<pre>";
        print_r($tweets);
        echo "</pre>"; */

        $this->view->tweets = $tweets;

        //verificar usuário logado na timeline
        $usuario = Container::getModel('Usuarios');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo= $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

   	  	$this->render('timeline');
   	  } else {
   	  	header('Location: /?login=erro');
   	  }   	  

   }

   public function tweet() {
      /*
   	  //verificar se usuário está logado
   	  session_start();

   	  if ($_SESSION['id'] != '' && $_SESSION['nome'] != '') {
   	  	//print_r($_POST);
   	  	//Container:: - já traz a conexão com o DB configurada
   	  	$tweet = Container::getModel('Tweet');

   	  	$tweet->__set('tweet', $_POST['tweet']);
   	  	$tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header('Location: /timeline');
   	  } else {
   	  	header('Location: /?login=erro');
   	  }  */

   	  $this->validaAutenticacao();

   	  //Container:: - já traz a conexão com o DB configurada e cria instancia do objeto Tweet
   	  $tweet = Container::getModel('Tweet');

   	  $tweet->__set('tweet', $_POST['tweet']);
   	  $tweet->__set('id_usuario', $_SESSION['id']);

      $tweet->salvar();

      header('Location: /timeline');
   }
   
   public function validaAutenticacao() {
      session_start();

   	  if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
   	  	 header('Location: /?login=erro');
   	  }    	  
   } 

   public function quemSeguir() {
      $this->validaAutenticacao();
      //echo "<br><br><br><br>";
      //print_r($_GET);
      $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';
      //echo $pesquisarPor;

      $usuarios = array();

      if ($pesquisarPor != '') {
      	//Container:: - já traz a conexão com o DB configurada e cria instancia do objeto Usuarios
   	    $usuario = Container::getModel('Usuarios');

   	    $usuario->__set('nome', $pesquisarPor);
   	    $usuario->__set('id', $_SESSION['id']);
   	    $usuarios = $usuario->getAll();
        /*
        echo "<pre>";
   	    var_dump($usuarios);
   	    echo "</pre>"; */
      }

        //verificar usuário logado na quemSeguir
        $usuario = Container::getModel('Usuarios');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo= $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

      $this->view->usuarios = $usuarios;

      $this->render('quemSeguir');
   }

   public function acao() {
   	  $this->validaAutenticacao();

   	  $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
   	  $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

   	  //Container:: - já traz a conexão com o DB configurada e cria instancia do objeto Usuarios
   	  $usuario = Container::getModel('Usuarios');
      $usuario->__set('id', $_SESSION['id']);

      if ($acao == 'seguir') {
      	//método da classe Usuarios
      	$usuario->seguirUsuario($id_usuario_seguindo);
      } else if($acao == 'deixar_de_seguir') {
      	$usuario->deixarSeguirUsuario($id_usuario_seguindo);
      }
      
      header('Location: /quem_seguir');
   }

   public function remover() {
   	  $this->validaAutenticacao();

   	  $delete = isset($_GET['remover']) ? $_GET['remover'] : '';

      $tweet_delete = Container::getModel('Tweet');
      $tweet_delete->__set('id', $_SESSION['id']);
      $tweet_delete->delete();

      echo "Removido com sucesso!";

      header('Location: /timeline');
   }
}

?>

