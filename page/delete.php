<?php

$user = Utils::getUsersByGetId();

$dao = new UsersDao();
$dao->delete($user->getId());
Flash::addFlash('User deleted successfully.');

Utils::redirect('list', array('status' => $user->getStatus()));
