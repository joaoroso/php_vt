<?php

// arquivo php que contém as instruções de conexão com o banco de dados
require 'connection.php';

// faz a conexão com o banco
$connection = new Connection();

// Adicionar cor - obtem 'color_name' e 'rgb_code' conforme informado no formulário html
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['color_name'], $_POST['rgb_code'])) {
    $colorName = $_POST['color_name'];
    $rgbCode = $_POST['rgb_code'];

    if (!empty($colorName) && preg_match('/^#[0-9A-Fa-f]{6}$/', $rgbCode)) {
        $stmt = $connection->getConnection()->prepare("INSERT INTO colors (name, rgb) VALUES (?, ?)");
        $stmt->execute([$colorName, $rgbCode]);
        header("Location: cores.php");
        exit;
    } else {
        $error = "Nome ou código RGB inválido.";
    }
}

// Excluir cor
if (isset($_GET['delete_id'])) {
    $colorId = $_GET['delete_id'];
    $stmt = $connection->getConnection()->prepare("SELECT COUNT(*) FROM user_colors WHERE color_id = ?");
    $stmt->execute([$colorId]);
    $linkedUsers = $stmt->fetchColumn();

    if ($linkedUsers > 0) {
        $error = "Não é possível excluir uma cor vinculada a usuários.";
    } else {
        $stmt = $connection->getConnection()->prepare("DELETE FROM colors WHERE id = ?");
        $stmt->execute([$colorId]);
        header("Location: cores.php");
        exit;
    }
}

// Listar cores
$colors = $connection->query("SELECT * FROM colors");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Cores</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="color_name" class="form-label">Nome da Cor</label>
            <input type="text" class="form-control" id="color_name" name="color_name" required>
        </div>
        <div>&nbsp;</div>
        <div>
            <label for="rgb_code" class="form-label">Código RGB</label>
            <input type="text" class="form-control" id="rgb_code" name="rgb_code" placeholder="#RRGGBB" required
                   oninput="updatePreview()">
            <div id="color_preview" style="width: 50px; height: 50px; margin-top: 10px; border: 1px solid #ccc;"></div>
        </div>
        <button type="submit" class="btn btn-success">Adicionar Cor</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>

    <h2 class="mt-5">Lista de Cores</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Cor</th>
            <th>Ação</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($colors as $color): ?>
            <tr>
                <td><?= htmlspecialchars($color->id) ?></td>
                <td><?= htmlspecialchars($color->name) ?></td>
                <td>
                    <div style="width: 50px; height: 50px; background: <?= htmlspecialchars($color->rgb) ?>; border: 1px solid #ccc;"></div>
                </td>
                <td>
                    <a href="cores.php?delete_id=<?= $color->id ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Tem certeza que deseja excluir esta cor?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- mostra preview das cores-->
<script>
    function updatePreview() {
        const rgbInput = document.getElementById('rgb_code');
        const preview = document.getElementById('color_preview');
        preview.style.background = rgbInput.value;
    }
</script>

</body>
</html>
