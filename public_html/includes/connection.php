<?php
// Arquivo de conexão com o banco de dados
// Carrega as credenciais do arquivo config.php

// Prevenir acesso direto
if (!defined('SYSTEM_LOADED')) {
    die('Acesso direto não permitido');
}

// Incluir arquivo de configuração
require_once dirname(dirname(__DIR__)) . '/config/config.php';

/**
 * Classe para gerenciar conexão com o banco de dados
 */
class Database {
    private $connection;
    private static $instance = null;

    /**
     * Construtor privado - padrão Singleton
     */
    private function __construct() {
        try {
            $this->connection = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Alterado para mostrar o erro durante o desenvolvimento
            die('Erro de conexão: ' . $e->getMessage());
        }
    }

    /**
     * Obtém uma instância única da conexão (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtém a conexão PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Executa uma consulta SQL com parâmetros
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Alterado para mostrar o erro durante o desenvolvimento
            echo 'Erro na consulta: ' . $e->getMessage();
            echo '<br>SQL: ' . $sql;
            die();
        }
    }
}
?>