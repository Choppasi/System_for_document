<?php
// UserController: index (list), create, edit, delete actions for users


class UserController
{
    public static function validate(array $data, int $excludeID = 0): array
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

        if ($data['email'] !== '' && User::emailExists($data['email'], $excludeID)){
             $errors[] = 'E-mail уже занят';
        }

        if ($data['login'] !== '' && User::loginExists($data['login'], $excludeID)){
            $errors[] = 'Логин уже занят';
        }

        return $errors;

    }

    public static function create(): void
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

        $errors = self::validate($data);

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

        User::create($data);
        redirect('index.php');
    }

    public static function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            
            $id = (int)($_GET['id'] ?? 0);

            $user = User::find($id);
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
        

        $user = User::find($id);

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

        $errors = self::validate($data, $id);

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

        User::update($id, $data);
        redirect('index.php');
    }


    

}