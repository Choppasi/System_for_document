<?php

class UserController
{
    public function __construct(private User $userModel) {}

    public function validate(array $data, int $excludeID = 0): array
    {
        $errors = [];

        if ($data['fio'] === ''){
            $errors[] = 'Укажите контактное лицо (ФИО)';
        }

        if ($data['email'] === ''){
            $errors[] = 'Укажите E-mail';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            $errors[] = 'Некорректный формат E-mail';
        }

        if ($data['login'] === ''){
            $errors[] = 'Укажите логин';
        }

        if ($data['password'] === ''){
            $errors[] = 'Укажите пароль';
        }

        if ($data['email'] !== '' && $this->userModel->emailExists($data['email'], $excludeID)){
            $errors[] = 'E-mail уже занят';
        }

        if ($data['login'] !== '' && $this->userModel->loginExists($data['login'], $excludeID)){
            $errors[] = 'Логин уже занят';
        }

        return $errors;
    }

    public function create(): void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            View::render('users/form', [
                'errors' => [],
                'user' => [],
                'formTitle' => 'Добавление пользователя',
                'formAction' => 'user_create.php',
                'submitLabel' => 'Добавить',
            ]);
            return;
        }

        $data = [
            'fio' => trim($_POST['fio'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'login' => trim($_POST['login'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
        ];

        $errors = $this->validate($data);

        if($errors !== []){
            View::render('users/form', [
                'errors'      => $errors,
                'user'        => [],
                'formTitle'   => 'Добавление пользователя',
                'formAction'  => 'user_create.php',
                'submitLabel' => 'Добавить',
            ]);
            return;
        }

        // хэшируем пароль только после валидации
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->userModel->create($data);
        redirect('index.php');
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){

            $id = (int)($_GET['id'] ?? 0);

            $user = $this->userModel->findID($id);
            if ($user === null){
                redirect('index.php');
            }

            View::render('users/form', [
            'errors'      => [],
            'user'        => $user,
            'formTitle'   => 'Редактирование пользователя',
            'formAction'  => 'user_edit.php',
            'submitLabel' => 'Изменить',
            ]);
            return;

        };

        $id = (int)($_POST['id'] ?? 0);

        $user = $this->userModel->findID($id);

        if ($user === null) {
            redirect('index.php');
        }

        $data = [
            'fio' => trim($_POST['fio'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'login' => trim($_POST['login'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
        ];

        $errors = $this->validate($data, $id);

        if ($errors !== []){
            View::render('users/form', [
                'errors'      => $errors,
                'user'        => array_merge($user, $data),
                'formTitle'   => 'Редактирование пользователя',
                'formAction'  => 'user_edit.php',
                'submitLabel' => 'Изменить',
            ]);

            return;
        }

        // если пароль не меняли (в поле тот же хэш) — оставляем его как есть
        if ($data['password'] !== $user['password']) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);
        redirect('index.php');
    }
}
