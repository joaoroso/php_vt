<?php

class Connection {

    private $databaseFile;
    private $connection;

    public function __construct()
    {
        // caminho do banco de dados
        $this->databaseFile = realpath(__DIR__ . "/database/db.sqlite");
        
        // efetua a conexão com o banco
        $this->connect();
    }

    // efetua a conexão com o banco SQLite
    private function connect()
    {
        return $this->connection = new PDO("sqlite:{$this->databaseFile}");
    }

    // se já exitir uma conexão, retorna a existente, senão efetua conexão
    public function getConnection()
    {
        return $this->connection ?: $this->connection = $this->connect();
    }

    // recebe um query e retorna o resultado
    public function query($query)
    {
        $result      = $this->getConnection()->query($query);
        $result->setFetchMode(PDO::FETCH_INTO, new stdClass);
        return $result;
    }
}