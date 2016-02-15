<?php

$user = Utils::getUsersByGetId();
$user->setStatus(Utils::getUrlParam('status'));
if (array_key_exists('username', $_POST)) {
    $user->setUsername($_POST['username']);
}

$dao = new UsersDao();
$dao->save($user);
Flash::addFlash('status changed successfully.');

Utils::redirect('detail', array('id' => $user->getId()));
