<?php
/**
 * Validator for {@link Foc}.
 * @see FocMapper
 */
final class UsersValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Foc} instance.
     * @param FOc $Users
     *  {@link Foc} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(User $Users) {
        $errors = array();
        if (!$Users->getUsername()) {
            $errors[] = new Error('username', 'Empty or invalid username.');
        }
        if (!$Users->getEmail()) {
            $errors[] = new Error('email', 'Empty or invalid email.');
        }
        if (!$Users->getPassword()) {
            $errors[] = new Error('password', 'Empty or invalid password.');
        }
        return $errors;
    }

    /**
     * Validate the given status.
     * @param string $status status to be validated
     * @throws Exception if the status is not known
     */
    public static function validateStatus($status) {
        if (!self::isValidStatus($status)) {
            throw new Exception('Unknown status: ' . $status);
        }
    }

    /**
     * Validate the given priority.
     * @param int $priority priority to be validated
     * @throws Exception if the priority is not known
     */
    public static function validatePriority($priority) {
        if (!self::isValidPriority($priority)) {
            throw new Exception('Unknown priority: ' . $priority);
        }
    }

    private static function isValidStatus($status) {
        return in_array($status, User::allStatuses());
    }

    private static function isValidPriority($priority) {
        return in_array($priority, User::allPriorities());
    }

}
