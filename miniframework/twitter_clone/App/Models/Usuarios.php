<?php 

  namespace App\Models;

  //importar a classe para realizar conexão com o BD com a abstração do script Model.php dentro do diretório Model 
  use MF\Model\Model;

  class Usuarios extends Model {
     private $id;
     private $nome;
     private $email;
     private $senha;

     public function __get($atributo) {
     	return $this->$atributo;
     }

     public function __set($atributo, $valor) {
     	$this->$atributo = $valor;
     }

     //método salvar
     public function salvar() {
     	$query = "insert into usuarios(nome, email, senha) values(:nome, :email, :senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        return $this;
     }

     //método para validar se um cadastro já foi feito para evitar duplicidade
     public function validarCadastro() {
     	$valido = true;

        if (strlen($this->__get('nome')) < 3) {
        	$valido = false;
        }

        if (strlen($this->__get('email')) < 3) {
        	$valido = false;
        }

        if (strlen($this->__get('senha')) < 3) {
        	$valido = false;
        }

     	return $valido;
     }

     //método para recuperar um usuário por e-mail
     public function getUsuarioPorEmail() {
     	$query = "select nome, email from usuarios where email = :email";
     	$stmt = $this->db->prepare($query);
     	$stmt->bindValue('email', $this->__get('email'));
     	$stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
     }

     public function autenticar() {
     	$query = "select id, nome, email from usuarios where email = :email and senha = :senha";
     	$stmt = $this->db->prepare($query);
     	$stmt->bindValue(':email', $this->__get('email'));
     	$stmt->bindValue(':senha', $this->__get('senha'));
     	$stmt->execute();

     	$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
        //return $usuario;

     	if ($usuario['id'] != '' && $usuario['nome'] != '') {
     		$this->__set('id', $usuario['id']);
     		$this->__set('nome', $usuario['nome']);
     	}
     	return $this;
     }

     //pesquisar usuários no banco
     public function getAll() {
     	$query = "select 
     	         u.id, u.nome, u.email,
     	         (
                    select count(*)
                    from
                      usuarios_seguidores as us
                    where
                      us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id   
     	         ) as seguindo_sn 
     	         from 
     	         usuarios as u
     	         where
     	         u.nome like :nome and u.id != :id_usuario";
     	$stmt = $this->db->prepare($query);
     	$stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
     	$stmt->bindValue(':id_usuario', $this->__get('id'));
     	$stmt->execute();

     	return $stmt->fetchAll(\PDO::FETCH_ASSOC);
     }

     public function seguirUsuario($id_usuario_seguindo) {
        //echo "seguir";
        $query = "insert into usuarios_seguidores(id_usuario, id_usuario_seguindo)values(:id_usuario, :id_usuario_seguindo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
     }

     public function deixarSeguirUsuario($id_usuario_seguindo) {
        //echo "deixar de seguir";
        $query = "delete from usuarios_seguidores where id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
     }

     public function getInfoUsuario() {
     	$query = "select nome from usuarios where id = :id_usuario";
     	$stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
     }

     public function getTotalTweets() {
     	$query = "select count(*) as total_tweets from tweets where id_usuario = :id_usuario";
     	$stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
     }

     public function getTotalSeguindo() {
     	$query = "select count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
     	$stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
     }

     public function getTotalSeguidores() {
     	$query = "select count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
     	$stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
     }

  }

?>