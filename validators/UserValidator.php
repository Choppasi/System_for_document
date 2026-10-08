<?php

namespace App\Validators;

use App\Models\User;

class UserValidator
{
    public function __construct(private User $userModel) {}

    public function validate(array $data, int $excludeID = 0): array
    {
        $errors = [];

        if ($data['fio'] === '') {
            $errors[] = 'Укажите контактное лицо (ФИО)';
        }

        if ($data['email'] === '') {
            $errors[] = 'Укажите E-mail';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный формат E-mail';
        }

        if ($data['login'] === '') {
            $errors[] = 'Укажите логин';
        }

        if ($data['password'] === '') {
            $errors[] = 'Укажите пароль';
        }

        if ($data['email'] !== '' && $this->userModel->emailExists($data['email'], $excludeID)) {
            $errors[] = 'E-mail уже занят';
        }

        if ($data['login'] !== '' && $this->userModel->loginExists($data['login'], $excludeID)) {
            $errors[] = 'Логин уже занят';
        }

        return $errors;
    }
}
