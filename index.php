<?php
require 'connection.php';

$connection = new Connection();

// Consulta os usuários
$users = $connection->query("SELECT * FROM users")->fetchAll(PDO::FETCH_OBJ);

// Consulta associações de cores
$colorAssociations = $connection->query("
    SELECT 
        uc.user_id, c.rgb
    FROM 
        user_colors AS uc
        JOIN colors AS c ON uc.color_id = c.id
")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="px-5 md-5 pt-5">
    <h1>Lista de Usuários</h1>
</div>

<!-- tabela com a lista de usuários -->
<table class="table w-75">
    <thead class="table-dark">
        <tr>
            <th width="5%">ID</th>
            <th width="20%">Nome</th>
            <th width="30%">Email</th>
            <th width="20%">Cores</th>
            <th width="15%">Ação</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <th><?= $user->id ?></th>
            <td><?= htmlspecialchars($user->name) ?></td>
            <td><?= htmlspecialchars($user->email) ?></td>
            <td>
                
            <!-- faz um loop das cores associadas e cria uma caixa com a cor-->
                <?php if (isset($colorAssociations[$user->id])): ?>
                    <?php foreach ($colorAssociations[$user->id] as $color): ?>
                        <div class="caixa" style="background: <?= htmlspecialchars($color) ?>"></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span>Sem cores vinculadas</span>
                <?php endif; ?>

            </td>
            <td>
                <a href="editar.php?id=<?= $user->id ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="deletar.php?id=<?= $user->id ?>" class="btn btn-danger btn-sm" 
                   onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- botoes para criar usuários e gerenciar as cores-->
<div class="px-5 md-5 pt-5">
    <a href="criar.php" class="btn btn-primary">Adicionar Usuário</a>
    <a href="cores.php" class="btn btn-secondary">Cadastrar Cores</a>
</div>

</body>
</html>
