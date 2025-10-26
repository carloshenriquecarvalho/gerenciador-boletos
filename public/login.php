<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="../src/actions/processa_login.php" method="POST">
        <fieldset>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
 
            <button type="submit" class="login-button">Entrar</button>
        </fieldset>
    </form>
</body>
</html>