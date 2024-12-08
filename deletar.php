<?php
require 'connection.php';

$connection = new Connection();
$id = $_GET['id'] ?? null;

if ($id) {

    // Inicia uma transação
    $transacao = $connection->getConnection();
    $transacao->beginTransaction();

    try {
        // exclui as associações de cores do usuario
        $stmt = $transacao->prepare("DELETE FROM user_colors WHERE user_id = ?");
        $stmt->execute([$id]);

        // exclui o usuário
        $stmt = $transacao->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        // comita a transação
        $transacao->commit();

        // volta pro index.php
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        // reverte a transação em caso de erro
        $transacao->rollBack();

        // mostra erro e volta pro index.php
        echo "<script>alert('Erro ao excluir o usuário: {$e->getMessage()}');</script>";
        header("Location: index.php");
        exit;
    }
} else {
    die("ID inválido!");
}
