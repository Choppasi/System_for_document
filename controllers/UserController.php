<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\User;
use App\Validators\UserValidator;

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
            View::render('users/form', $this->formView());
            return;
        }

        if (!$this->request->verifyCsrf()) {
            View::render('users/form', $this->formView([
                'errors' => ['Сессия истекла, попробуйте еще раз'],
            ]));
            return;
        }

        $data = $this->formData();

        $errors = $this->validator->validate($data);

        if($errors !== []){
            View::render('users/form', $this->formView([
                'errors' => $errors,
            ]));
            return;
        }

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

            View::render('users/form', $this->formView([
                'user'      => $user,
                'formTitle' => 'Редактирование пользователя',
                'formAction' => '/users/edit',
                'submitLabel' => 'Изменить',
            ]));
            return;

        };

        if (!$this->request->verifyCsrf()) {
            View::render('users/form', $this->formView([
                'errors' => ['Сессия истекла, попробуйте еще раз'],
            ]));
            return;
        }

        $id = $this->request->postInt('id');

        $user = $this->userModel->findID($id);

        if ($user === null) {
            redirect('/');
        }

        $data = $this->formData();

        $errors = $this->validator->validate($data, $id);

        if ($errors !== []){
            View::render('users/form', $this->formView([
                'errors'      => $errors,
                'user'        => array_merge($user, $data),
                'formTitle'   => 'Редактирование пользователя',
                'formAction'  => '/users/edit',
                'submitLabel' => 'Изменить',
            ]));

            return;
        }

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

    private function formView(array $overrides = []): array
    {
        return array_merge([
            'errors'      => [],
            'user'        => [],
            'formTitle'   => 'Добавление пользователя',
            'formAction'  => '/users/create',
            'submitLabel' => 'Добавить',
            'csrfField'   => $this->request->csrfField(),
        ], $overrides);
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
