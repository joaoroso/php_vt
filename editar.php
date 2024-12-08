<?php
require 'connection.php';

$connection = new Connection();

// Obter ID do usuário
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Obter dados do usuário
$stmt = $connection->getConnection()->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_OBJ);

// se usuario inválido volta pra index.php
if (!$user) {
    header("Location: index.php");
    exit;
}

// Atualizar informações do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (!empty($name) && !empty($email)) {
        $stmt = $connection->getConnection()->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
        header("Location: index.php");
        exit;
    } else {
        $error = "Preencha todos os campos obrigatórios.";
    }
}

// gerencia a vinculação e desvinculação de cores
if (isset($_POST['link_color'])) {
    $colorId = $_POST['color_id'];
    $stmt = $connection->getConnection()->prepare("INSERT INTO user_colors (user_id, color_id) VALUES (?, ?)");
    $stmt->execute([$id, $colorId]);
    header("Location: editar.php?id=$id");
    exit;
}

if (isset($_GET['unlink_color'])) {
    $colorId = $_GET['unlink_color'];
    $stmt = $connection->getConnection()->prepare("DELETE FROM user_colors WHERE user_id = ? AND color_id = ?");
    $stmt->execute([$id, $colorId]);
    header("Location: editar.php?id=$id");
    exit;
}

// obter cores vinculadas e disponíveis
$linkedColors = $connection->query("SELECT uc.color_id, c.name, c.rgb FROM user_colors uc JOIN colors c ON uc.color_id = c.id WHERE uc.user_id = $id") ?: [];
$availableColors = $connection->query("SELECT * FROM colors WHERE id NOT IN (SELECT color_id FROM user_colors WHERE user_id = $id)") ?: [];
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Editar Usuário</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user->name) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user->email) ?>" required>
        </div>
        <button type="submit" name="update_user" class="btn btn-primary">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>

    <h3 class="mt-5">Cores Vinculadas</h3>
    <?php if (!empty($linkedColors)): ?>
        <?php foreach ($linkedColors as $color): ?>
            <div class="d-flex align-items-center mb-2">
                <div style="width: 25px; height: 25px; background: <?= htmlspecialchars($color->rgb) ?>; margin-right: 10px; border: 1px solid #ccc;"></div>
                <?= htmlspecialchars($color->name) ?>
                <a href="editar.php?id=<?= $id ?>&unlink_color=<?= $color->color_id ?>" class="btn btn-danger btn-sm ms-3">Desvincular</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nenhuma cor vinculada.</p>
    <?php endif; ?>

    <h3 class="mt-5">Vincular Nova Cor</h3>
    <form method="POST">
        <div class="mb-3">
            <select name="color_id" class="form-select">
                <?php foreach ($availableColors as $color): ?>
                    <option value="<?= $color->id ?>"><?= htmlspecialchars($color->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="link_color" class="btn btn-success">Vincular Cor</button>
    </form>
</div>
</body>
</html>
