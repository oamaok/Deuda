<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= Config::SITE_BASE ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Teemu Pääkkönen">
    <link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    <title><?= Deuda::$pageTitle ?></title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
<?= Deuda::getNavigation() ?>
<div class="container-fluid">
    <div class="row">
        <?= Deuda::getSidebar() ?>
        <?= Deuda::$outputBuffer ?>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
