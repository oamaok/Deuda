<?php

$userGroups = Session::getUser()->getGroups();

$recentPayments = array();
foreach($userGroups as $group)
{
    /* @var $group DebtGroup */

    $recentPayments = array_merge($recentPayments, $group->getPayments());
}

usort($recentPayments, function($a, $b){
    return strtotime($a->createDate) < strtotime($b->createDate);
});

$tableContents = "";

foreach($recentPayments as $payment)
{
    $tableContents .= "<tr>";
    $tableContents .= "<td>" . $payment->id . "</td>";
    $tableContents .= "<td>" . DebtGroup::model()->findByPk($payment->group)->name . "</td>";
    $tableContents .= "<td>" . User::model()->findByPk($payment->from)->getFullName() . "</td>";
    $tableContents .= "<td>" . User::model()->findByPk($payment->to)->getFullName() . "</td>";
    $tableContents .= "<td>" . sprintf("%0.2f€", $payment->amount) . "</td>";
    $tableContents .= "<td>" . $payment->createDate . "</td>";
    $tableContents .= "</tr>";
}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li class="active"><a href="#">Overview</a></li>
                <li><a href="#">Groups</a></li>
                <li><a href="#">My Debts</a></li>
                <li><a href="#">Profile</a></li>
            </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h1 class="page-header">Dashboard</h1>
            <h2 class="sub-header">Your debts</h2>
            <h2 class="sub-header">Recent payments</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Group</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?= $tableContents ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>