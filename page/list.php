<?php

$status = Utils::getUrlParam('status');
//UsersValidator::validateStatus($status);

$dao = new UsersDao();
$search = new UsersSearchCriteria();
$search->setStatus($status);

// data for template
$title = Utils::capitalize($status) . 'Users';
$user = $dao->find($search);
