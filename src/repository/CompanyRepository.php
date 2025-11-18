<?php
namespace App\repository;

use App\model\User;
use PDO;
use PDOException;

class CompanyRepository
{
    private ?PDO $conn = null;
    public function __construct(PDO $pdo)
    {
        $this->conn = $pdo;
    }

    private const string SQL_GET_ALL_COMPANIES = "select * from gerenciador_boletos.empresa where id_usuario = :id_usuario";
    private const string SQL_CREATE_COMPANY = "insert into gerenciador_boletos.empresa(id_usuario, nome_empresa) values(:id_usuario,:nome)";
    private const string SQL_UPDATE_COMPANY_NAME = "update gerenciador_boletos.empresa set nome_empresa = :nome where id_empresa = :id_empresa";
    private const string SQL_DELETE_COMPANY_FROM_TABLE = 'DELETE FROM gerenciador_boletos.empresa WHERE id_empresa = :id_company';

    public function createCompany(string $nome, int $id_user): bool
    {
        try {
            $stmt = $this->conn->prepare(self::SQL_CREATE_COMPANY);
            $stmt->execute([
                ':id_usuario' => $id_user,
                ':nome' => $nome
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao criar company: " . $e->getMessage());
            return false;
        }
    }
    public function getCompanies(int $id_usuario): array
    {
        try {
            $stmt = $this->conn->prepare(self::SQL_GET_ALL_COMPANIES);
            $stmt->execute( [
                ':id_usuario' => $id_usuario,
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar company: " . $e->getMessage());
            return [];
        }
    }

    public function updateCompanyName(string $newName, int $id_company): bool
    {
        try {
            $stmt = $this->conn->prepare(self::SQL_UPDATE_COMPANY_NAME);
            $stmt->execute([
                ':nome' => $newName,
                ':id_empresa' => $id_company,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar company: " . $e->getMessage());
            return false;
        }

    }

    public function deleteCompany(int $id_company): bool
    {
        try {
            $stmt = $this->conn->prepare(self::SQL_DELETE_COMPANY_FROM_TABLE);
            $stmt->execute([
                ':id_company' => $id_company,
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao excluir company: " . $e->getMessage());
            return false;
        }
    }
}