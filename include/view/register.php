<?php

$errorMessage = "";

foreach($errorMessages as $message)
{
    $errorMessage .= "<p>$message</p>";
}

?>
<form class="form-signin" action="register" method="post">
    <h2 class="form-signin-heading">Register</h2>
    <input type="text" name="username" value="<?= $fields["username"] ?>"
           class="form-control top" placeholder="Username" required autofocus>
    <input type="text" name="firstName" value="<?= $fields["firstName"] ?>"
           class="form-control middle has-error" placeholder="First name" required>
    <input type="text" name="lastName" value="<?= $fields["lastName"] ?>"
           class="form-control middle" placeholder="Last name" required>
    <input type="password" name="password"
           class="form-control bottom" placeholder="Password" required>

    <div class="register-errors"><?= $errorMessage ?></div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>

    <a href="login" class="pull-right" style="margin-top: 10px;">Already have an account?</a>
</form>