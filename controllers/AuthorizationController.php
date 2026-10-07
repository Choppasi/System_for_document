<?php

class AuthorizationController
{
    public function __construct(
        private User $userModel,
        private Request $request
    ) {}

    public function login(): void
    {
        $data = [
            'errors'      => [],
            'login'       => '',
            'formTitle'   => 'Авторизация',
            'formAction'  => '/login',
            'submitLabel' => 'Войти',
            'csrfField'   => $this->request->csrfField(),
        ];

        if (!$this->request->isPost()) {
            View::render('authorization/login', $data);
            return;
        }

        if (!$this->request->verifyCsrf()) {
            $data['errors'] = ['Сессия истекла, попробуйте еще раз'];
            View::render('authorization/login', $data);
            return;
        }

        $login    = $this->request->postString('login');
        $password = (string)$this->request->post('password', '');
        $data['login'] = $login;

        if ($login === '' || $password === '') {
            $data['errors'] = ['Пожалуйста, заполните все поля'];
            View::render('authorization/login', $data);
            return;
        }

        $user = $this->userModel->findLogin($login);

        if (!$user || !password_verify($password, $user['password'])) {
            $data['errors'] = ['Неверный логин или пароль'];
            View::render('authorization/login', $data);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['user_fio'] = $user['fio'];

        redirect('/');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('/login');
    }
}
