<?php
final class UsersMapper {

    private function __construct() {
    }
    public static function map(User $Users, array $properties) {
        if (array_key_exists('id', $properties)) {
            $Users->setId($properties['id']);
        }
        if (array_key_exists('username', $properties)) {
            $Users->setUsername($properties['username']);
        }
         if (array_key_exists('email', $properties)) {
            $Users->setEmail($properties['email']);
        }
         if (array_key_exists('password', $properties)) {
            $Users->setPassword($properties['password']);
        }
       
    }

    private static function createDateTime($input) {
        return DateTime::createFromFormat('Y-n-j H:i:s', $input);
    }

}
