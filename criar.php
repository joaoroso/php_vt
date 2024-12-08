<?php
require 'connection.php';

$connection = new Connection();

// Obtem a lista de cores disponíveis
$colors = $connection->query("SELECT * FROM colors");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; 
    $email = $_POST['email'];
    $colorIds = $_POST['color_ids'];

    
    if (!empty($name) && !empty($email)) {
        // insere novo usuário
        $stmt = $connection->getConnection()->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);

        // obtem o ID do usuário criado por ultimo
        $userId = $connection->getConnection()->lastInsertId();

        // vincula cores ao usuário
        if (!empty($colorIds)) {
            $stmt = $connection->getConnection()->prepare("INSERT INTO user_colors (user_id, color_id) VALUES (?, ?)");
            foreach ($colorIds as $colorId) {
                $stmt->execute([$userId, $colorId]);
            }
        }

        // volta para o index.php
        header("Location: index.php");
        exit;
    } else {
        $error = "Preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Criar Usuário</h1>

    <!-- mostra se existir algum erro -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- formulario com os dadosp para preencher-->
    <form method="POST">

        <!-- controle que pede o NOME -->
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <!-- controle que pede o EMAIL -->
        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- controle que pede as CORES -->
        <div class="mb-3">
            <label>Cores</label>
            <select id="color_ids" name="color_ids[]" class="form-select" multiple>
                
                <!-- loop para adicionar todas as cores ao controle -->
                <?php foreach ($colors as $color): ?>
                    <option value="<?= $color->id ?>"><?= htmlspecialchars($color->name) ?></option>
                <?php endforeach; ?>

            </select>
            <small class="form-text text-muted">Segure Ctrl para selecionar várias cores.</small>
        </div>

        <button type="submit" class="btn btn-success">Criar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>
</body>
</html>
