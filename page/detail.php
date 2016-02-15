<?php
// data for template
$user = Utils::getUsersByGetId();
$tooLate = $user->getStatus() == User::STATUS_PENDING && $user->getDueOn() < new DateTime();
