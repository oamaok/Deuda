<?php

$user = User::model()->findByPk($userId);

?>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h1><?= $user->getFullName() ?></h1>
</div>