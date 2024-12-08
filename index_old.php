<?php

require 'connection.php';

$connection = new Connection();

$users = $connection->query("SELECT * FROM users");

echo "
<html>
<head>


  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH' crossorigin='anonymous'>
  <style>
    table {
        width: 80%;
    }
    th {
        background-color: #e0e0a0;
    }
    td, th {
        border: 1px solid; 
        height: 40px;
        padding: 15px;
    }
    table {
        border: 1px solid;
        margin: 35px;
    }
    div.criar{
        padding: 30px 0px 0px 35px;
    }

    .conjunto {
        display: flex;
    }

    .caixa{
        display: inline-block;
        width: 25px;
        height: 25px;
        border: 1px solid rgba(50,50,50, 0.4)
    }
    .vermelho{
        background: #ff0000;
    }
    .verde{
        background: #00ff00;
    }
    .azul{
        background: #0000ff;
    }
    .branco{
        background: #ffffff;
    }


  </style>
</head>
<body>

<div class='criar'>
    <h1>Lista de Usuários</h1>
    <a href='criar.php' class='btn btn-primary'>Adicionar Usuário</a>
</div>
<table border='1'>


    <tr>
        <th width=5%>ID</th>    
        <th>Nome</th>    
        <th>Email</th>
        <th>Cores</th>
        <th width=15%>Ação</th>    
    </tr>



";

foreach ($users as $user) {

    echo sprintf(
        "<tr>
                      <td>%s</td>
                      <td>%s</td>
                      <td>%s</td>
                      <td>
                        <div class='caixa vermelho'>&nbsp;</div>
                        <div class='caixa verde'>&nbsp;</div>
                        <div class='caixa azul'>&nbsp;</div>
                        <div class='caixa branco'>&nbsp;</div>
                        
                        
                      </td>
                      <td>
                           <a href='edit.php?id=<?= $user->id ?>' class='btn btn-warning btn-sm'>Editar</a>
                           <a href='deletar.php?id=$user->id' class='btn btn-danger btn-sm' onclick='return confirm('Tem certeza que deseja excluir?')'>Excluir</a>
                      </td>
                   </tr>",
        $user->id,
        $user->name,
        $user->email,

    );
}

echo "</table>
</body>
</html>

";

?>