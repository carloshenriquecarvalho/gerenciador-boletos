<?php

class Usuario 
{
    private ?int $id_usuario;
    private string $email;
    private string $nome;

    public function __construct(string $email, string $nome, ?int $id_usuario)
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->id_usuario = $id_usuario;
    }

    //setting getters and setters
    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }
}

?>