<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Deuda</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="pull-right"><a href="logout">Logout</a></li>
            </ul>
            <span class="logged-as">Logged in as <b><?= Session::getUser()->getFullName() ?></b></span>

            <form class="navbar-form navbar-right form-group has-feedback">
                <input type="text" class="form-control navbar-search" placeholder="Search users and groups">
                <span class="glyphicon glyphicon-search form-control-feedback"></span>
            </form>
        </div>
    </div>
</div>