<?php
$titulo = "Login";
include_once '../src/includes/header_generic.php';
?>
    <form action="../src/actions/processa_login.php" method="POST">
        <fieldset>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
 
            <button type="submit" class="login-button">Entrar</button>
        </fieldset>
    </form>


    <?php
    include_once '../src/includes/footer_generic.php';
    
    ?>