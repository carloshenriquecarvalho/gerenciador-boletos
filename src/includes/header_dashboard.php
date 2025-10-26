<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo) ? htmlspecialchars($titulo) : 'Titulo padrao' ?></title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <header>
        <img src="./img/logo_trial.png" alt="logo de boletos verde com vermelho">
        <nav>
            <ul>
                <li><a href="../src/actions/logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>
    <main>
