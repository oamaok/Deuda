<form class="form-signin" action="login" name="login" method="post">
    <h2 class="form-signin-heading">Please login</h2>
    <input type="text" name="username" value="<?= $fields["username"] ?>"
           class="form-control top" placeholder="Username" required autofocus>
    <input type="password" name="password" class="form-control bottom" placeholder="Password" required>
    <label class="checkbox">
        <input type="checkbox" name="remember" value="remember-me">
        <span>Remember me</span>
    </label>
    <div class="register-errors"><p><?= $error ?></p></div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
    <a href="register" class="pull-right" style="margin-top: 10px">Don't have an account?</a>
</form>