<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Document;
use App\Models\User;
use App\Validators\DocumentValidator;

class DocumentController
{
    public function __construct(
        private Document $documentModel,
        private User $userModel,
        private Request $request,
        private DocumentValidator $validator
    ) {}

    public function create(): void
    {
        require_auth();

        $allUsers = $this->userModel->allForSelect();

        if (!$this->request->isPost()) {
            View::render('documents/form', $this->formView($allUsers));
            return;
        }

        if (!$this->request->verifyCsrf()) {
            View::render('documents/form', $this->formView($allUsers, [
                'errors' => ['Сессия истекла, попробуйте еще раз'],
            ]));
            return;
        }

        $data = $this->formData();

        $errors = $this->validator->validate($data);

        if ($errors !== []) {
            View::render('documents/form', $this->formView($allUsers, [
                'errors' => $errors,
                'doc'    => $data,
            ]));
            return;
        }

        $this->documentModel->create($data);
        redirect('/');
    }

    public function update(): void
    {
        require_auth();

        $allUsers = $this->userModel->allForSelect();

        if (!$this->request->isPost()) {
            $id = $this->request->getInt('id');

            $doc = $this->documentModel->find($id);
            if ($doc === null) {
                redirect('/');
            }

            View::render('documents/form', $this->formView($allUsers, [
                'doc'         => $doc,
                'formTitle'   => 'Редактирование документа',
                'formAction'  => '/documents/edit',
                'submitLabel' => 'Изменить',
            ]));
            return;
        }

        if (!$this->request->verifyCsrf()) {
            View::render('documents/form', $this->formView($allUsers, [
                'errors' => ['Сессия истекла, попробуйте еще раз'],
            ]));
            return;
        }

        $id = $this->request->postInt('id');

        $doc = $this->documentModel->find($id);
        if ($doc === null) {
            redirect('/');
        }

        $data = $this->formData();

        $errors = $this->validator->validate($data);

        if ($errors !== []) {
            View::render('documents/form', $this->formView($allUsers, [
                'errors'      => $errors,
                'doc'         => array_merge($doc, $data),
                'formTitle'   => 'Редактирование документа',
                'formAction'  => '/documents/edit',
                'submitLabel' => 'Изменить',
            ]));
            return;
        }

        $this->documentModel->update($id, $data);
        redirect('/');
    }

    public function delete(): void
    {
        require_auth();

        $id = $this->request->getInt('id');
        if ($id > 0) {
            $this->documentModel->delete($id);
        }

        redirect('/');
    }

    private function formView(array $allUsers, array $overrides = []): array
    {
        return array_merge([
            'errors'      => [],
            'doc'         => [],
            'allUsers'    => $allUsers,
            'formTitle'   => 'Добавление документа',
            'formAction'  => '/documents/create',
            'submitLabel' => 'Добавить',
            'csrfField'   => $this->request->csrfField(),
        ], $overrides);
    }

    private function formData(): array
    {
        return [
            'user_id'     => $this->request->postInt('user_id'),
            'name'        => $this->request->postString('name'),
            'description' => $this->request->postString('description'),
            'doc_type'    => $this->request->postString('doc_type'),
        ];
    }
}
