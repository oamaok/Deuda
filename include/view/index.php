<?php

$userPayments = array();
$paymentsFrom = Payment::getPaymentsFromUser(Session::getUser());
$paymentsTo = Payment::getPaymentsToUser(Session::getUser());

$userPayments = array_merge($paymentsFrom, $paymentsTo);

$debts = array();
foreach($paymentsFrom as $payment)
{
    if(!isset($debts[$payment->to]))
        $debts[$payment->to] = 0;
    $debts[$payment->to] += $payment->amount;
}

foreach($paymentsTo as $payment)
{
    if(!isset($debts[$payment->from]))
        $debts[$payment->from] = 0;
    $debts[$payment->from] -= $payment->amount;
}
$totalBalance = 0;
$debtTableContents = "";
foreach($debts as $user => $amount)
{
    $totalBalance += $amount;
    $debtTableContents .= "<tr>";
    $debtTableContents .= "<td>" . User::model()->findByPk($user)->getFullName() . "</td>";
    if($amount < 0)
        $debtTableContents .= "<td class='debt-negative'>";
    else
        $debtTableContents .= "<td class='debt-positive'>";
    $debtTableContents .= sprintf("<b>%0.2f€</b>", $amount) . "</td>";
    $debtTableContents .= "<td><a href=\"history/" . $user . "\">View history</a></div>";
    $debtTableContents .= "</tr>";
}

if($totalBalance < 0)
    $debtTotalInfo = sprintf("In total, you owe people <b class='debt-negative'>%0.2f€</b>.", abs($totalBalance));
else
    $debtTotalInfo = sprintf("In total, people owe you <b class='debt-positive'>%0.2f€</b>.", $totalBalance);

usort($userPayments, function($a, $b){
    return strtotime($a->createDate) < strtotime($b->createDate);
});

$paymentsTableContents = "";

foreach($userPayments as $payment)
{
    $paymentsTableContents .= "<tr>";
    $paymentsTableContents .= "<td>" . User::model()->findByPk($payment->from)->getFullName() . "</td>";
    $paymentsTableContents .= "<td>" . User::model()->findByPk($payment->to)->getFullName() . "</td>";
    $paymentsTableContents .= "<td>" . sprintf("%0.2f€", $payment->amount) . "</td>";
    $time = strtotime($payment->createDate);
    $fullDate = date("d.m.Y H:i", $time);
    $displayTime = $fullDate;

    // check if the event happened today
    if(date('Ymd') == date('Ymd', $time))
    {
        $displayTime = "Today " . date("H:i", $time);
    }
    // check if the event happened yesterday
    elseif(date('Ymd', time() - 24 * 60 * 60) == date('Ymd', $time))
    {
        $displayTime = "Yesterday " . date("H:i", $time);
    }
    // check if the event happened within a week
    elseif(time() - 7 * 24 * 60 * 60 < $time)
    {
        $displayTime = "Last " . date("l", $time) . " " . date("H:i", $time);
    }
    $paymentsTableContents .= "<td title=\"$fullDate\">$displayTime</td>";
    $paymentsTableContents .= "</tr>";
}



?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">Dashboard</h1>
    <h2 class="sub-header">Your balance</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>User</th>
                <th>Sum</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?= $debtTableContents ?>
            </tbody>
        </table>
    </div>
    <span class="debt-total-info"><?= $debtTotalInfo ?></span>
    <h2 class="sub-header">Recent payments</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?= $paymentsTableContents ?>
            </tbody>
        </table>
    </div>
</div>