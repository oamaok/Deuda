<?php

function getSidebarClass($page)
{
    if($page == Deuda::getActivePage())
        return "active";
    return "";
}

?>
<div class="col-sm-3 col-md-2 sidebar">
    <ul class="nav nav-sidebar">
        <li class="<?= getSidebarClass("index") ?>"><a href="">Overview</a></li>
        <li class="<?= getSidebarClass("groups") ?>"><a href="groups/">Groups</a></li>
        <li class="<?= getSidebarClass("debts") ?>"><a href="debts/">My Debts</a></li>
        <li class="<?= getSidebarClass("profile") ?>"><a href="profile/">Profile</a></li>
    </ul>
</div>