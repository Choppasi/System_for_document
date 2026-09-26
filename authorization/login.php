<?php


require __DIR__ . '/../core/init.php';

class AuthorizationController
{

    public static function login(): void
    {
        $data = [
            'errors' => [],
            'login' => '',
            'formTitle' => 'Авторизация',
            'formAction' => 'login.php',
            'submitLabel' => 'Войти',
        ];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            View::render('authorization/login', $data);
            return;
        }

        if (!csrf_verify()){
            $data['errors'] = ['Сессия истекла, попробуйте еще раз'];
            View::render('authorization/login', $data);
            return;
        }

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $data['login'] = $login;

        if ($login === '' || $password === ''){
            $data['errors'] = ['Пожалуйста, заполните все поля'];
            View::render('authorization/login', $data);
            return;

        }

        $user = User::findLogin($login);

        if (!$user || !password_verify($password, $user['password'])){
            $data['errors'] = ['Неверный логин или пароль'];
            View::render('authorization/login', $data);
            return;
        }

        

        session_regenerate_id(true);
        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['user_fio'] = $user['fio'];

        redirect('/index.php');


    }
}

AuthorizationController::login();