<?php

class DocumentController
{
    public static function validate(array $data, array $allUsers): array
    {
        $errors = [];

        if($data['name'] ===''){
            $errors[] = 'Укажите наименование';
        }

        if (!in_array($data['doc_type'], ['Excel', 'Word', 'TXT'], true)) {
            $errors[] = 'Выберите правильный тип документа';
        }

        $userExists = false;
        foreach ($allUsers as $user) {
            if ((int)$user['id'] === (int)$data['user_id']){
                $userExists = true;
                break;
            }
        }

        if (!$userExists){
            $errors[] = 'Пользователь не найден';
        }

        return $errors;
    }

    public static function create(): void
    {
        $allUsers = User::allForSelect();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            View::render('documents/form', [
                'errors'      => [],
                'doc'         => [],
                'allUsers'    => $allUsers,
                'formTitle'   => 'Добавление документа',
                'formAction'  => 'document_create.php',
                'submitLabel' => 'Добавить',
            ]);
            return;
        }

        $data = [
            'user_id'     => (int)($_POST['user_id'] ?? 0),
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'doc_type'    => trim($_POST['doc_type'] ?? ''),
        ];

        $errors = self::validate($data, $allUsers);

        if ($errors !== []) {
            View::render('documents/form', [
                'errors'      => $errors,
                'doc'         => $data,
                'allUsers'    => $allUsers,
                'formTitle'   => 'Добавление документа',
                'formAction'  => 'document_create.php',
                'submitLabel' => 'Добавить',
            ]);
            return;
        }

        Document::create($data);
        redirect('index.php');
    }

    public static function update(): void
    {
        $allUsers = User::allForSelect();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $id = (int)($_GET['id'] ?? 0);

            $doc = Document::find($id);
            if ($doc === null) {
                redirect('index.php');
            }

            View::render('documents/form', [
                'errors'      => [],
                'doc'         => $doc,
                'allUsers'    => $allUsers,
                'formTitle'   => 'Редактирование документа',
                'formAction'  => 'document_edit.php',
                'submitLabel' => 'Изменить',
            ]);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        $doc = Document::find($id);
        if ($doc === null) {
            redirect('index.php');
        }

        $data = [
            'user_id'     => (int)($_POST['user_id'] ?? 0),
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'doc_type'    => trim($_POST['doc_type'] ?? ''),
        ];

        $errors = self::validate($data, $allUsers);

        if ($errors !== []) {
            View::render('documents/form', [
                'errors'      => $errors,
                'doc'         => array_merge($doc, $data),
                'allUsers'    => $allUsers,
                'formTitle'   => 'Редактирование документа',
                'formAction'  => 'document_edit.php',
                'submitLabel' => 'Изменить',
            ]);
            return;
        }

        Document::update($id, $data);
        redirect('index.php');
    }

}