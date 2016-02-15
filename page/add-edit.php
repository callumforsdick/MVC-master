<?php
$errors = array();
$user = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $user = Utils::getUsersByGetId();
} else {
    // set defaults
    $user = new User();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    Utils::redirect('detail', array('id' => $user->getId()));
} elseif (array_key_exists('save', $_POST)) {
    // for security reasons, do not map the whole $_POST['Users']
    $data = array(
        'username' =>$_POST['Users']['username'],#FILTER_SANITIZE_STRING
        'email' => $_POST['Users']['email'],
        'password' => $_POST['Users']['password'],
    );
        
    // map
    UsersMapper::map($user, $data);
    // validate
    $errors = UsersValidator::validate($user);
    // validate
    if (empty($errors)) {
        // save
        $dao = new UsersDao();
        $user = $dao->save($user);
        Flash::addFlash('Changes saved successfully.');
        // redirect
        Utils::redirect('list');
    }
}
?>