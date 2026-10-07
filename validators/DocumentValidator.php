<?php

class DocumentValidator
{
    public function __construct(private User $userModel) {}

    public function validate(array $data): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors[] = 'Укажите наименование';
        }

        if (!in_array($data['doc_type'], ['Excel', 'Word', 'TXT'], true)) {
            $errors[] = 'Выберите правильный тип документа';
        }

        if ($data['user_id'] <= 0 || $this->userModel->findID($data['user_id']) === null) {
            $errors[] = 'Пользователь не найден';
        }

        return $errors;
    }
}
