<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="../src/actions/processa_registro.php" method="POST">
        <fieldset>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="senha_texto_puro">Senha:</label>
            <input type="password" name="senha_texto_puro" id="senha_texto_puro" required>

            <label for="re-senha">Repita a senha:</label>
            <input type="password" name="re-senha" id="re-senha" required>

            <button type="submit">Cadastre-se!</button>
        </fieldset>
    </form>
</body>
</html>