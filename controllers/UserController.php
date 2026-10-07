<?php

class UserController
{
    public function __construct(
        private User $userModel,
        private Request $request,
        private UserValidator $validator
    ) {}

    public function create(): void
    {
        require_auth();

        if (!$this->request->isPost()) {
            View::render('users/form', [
                'errors' => [],
                'user' => [],
                'formTitle' => 'Добавление пользователя',
                'formAction' => '/users/create',
                'submitLabel' => 'Добавить',
            ]);
            return;
        }

        $data = $this->formData();

        $errors = $this->validator->validate($data);

        if($errors !== []){
            View::render('users/form', [
                'errors'      => $errors,
                'user'        => [],
                'formTitle'   => 'Добавление пользователя',
                'formAction'  => '/users/create',
                'submitLabel' => 'Добавить',
            ]);
            return;
        }

        // хэшируем пароль только после валидации
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->userModel->create($data);
        redirect('/');
    }

    public function update(): void
    {
        require_auth();

        if (!$this->request->isPost()) {

            $id = $this->request->getInt('id');

            $user = $this->userModel->findID($id);
            if ($user === null){
                redirect('/');
            }

            View::render('users/form', [
            'errors'      => [],
            'user'        => $user,
            'formTitle'   => 'Редактирование пользователя',
            'formAction'  => '/users/edit',
            'submitLabel' => 'Изменить',
            ]);
            return;

        };

        $id = $this->request->postInt('id');

        $user = $this->userModel->findID($id);

        if ($user === null) {
            redirect('/');
        }

        $data = $this->formData();

        $errors = $this->validator->validate($data, $id);

        if ($errors !== []){
            View::render('users/form', [
                'errors'      => $errors,
                'user'        => array_merge($user, $data),
                'formTitle'   => 'Редактирование пользователя',
                'formAction'  => '/users/edit',
                'submitLabel' => 'Изменить',
            ]);

            return;
        }

        // если пароль не меняли (в поле тот же хэш) — оставляем его как есть
        if ($data['password'] !== $user['password']) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);
        redirect('/');
    }

    public function delete(): void
    {
        require_auth();

        $id = $this->request->getInt('id');
        if ($id > 0) {
            $this->userModel->delete($id);
        }

        redirect('/');
    }

    private function formData(): array
    {
        return [
            'fio'      => $this->request->postString('fio'),
            'city'     => $this->request->postString('city'),
            'phone'    => $this->request->postString('phone'),
            'email'    => $this->request->postString('email'),
            'login'    => $this->request->postString('login'),
            'password' => $this->request->postString('password'),
        ];
    }
}
