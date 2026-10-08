<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Document;
use App\Models\User;

class HomeController
{
    public function __construct(
        private User $userModel,
        private Document $documentModel,
        private Request $request
    ) {}

    public function index(): void
    {
        require_auth();

        $search = $this->request->getString('search');
        $page = max(1, $this->request->getInt('page', 1));

        $docSearch = $this->request->getString('doc_search');
        $dpage = max(1, $this->request->getInt('dpage', 1));
        $userId = $this->request->getInt('user_id');

        $total = $this->userModel->count($search);
        $totalPages = max(1, (int) ceil($total / 5));
        $page = min($page, $totalPages);
        $users = $this->userModel->all($search, $page, 5);

        $docTotal = $this->documentModel->count($docSearch, $userId);
        $docTotalPages = max(1, (int) ceil($docTotal / 5));
        $dpage = min($dpage, $docTotalPages);
        $documents = $this->documentModel->all($docSearch, $dpage, 5, $userId);

        $filteredUser = $userId > 0 ? $this->userModel->findID($userId) : null;

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/users/index.php';
        require __DIR__ . '/../views/documents/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
