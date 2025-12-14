<?php

const C2L_PATH = '/../view/LoginC/L_index.php';
const C2F_PATH = '/../view/F/F_index.php';

const C2B_PATH = '/../view/B/B_index.php';

class LogC {

    public function index(): void{



        require __DIR__ . C2L_PATH;





    }

    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?c=logC&a=index');
            exit;
        }
    }


    public function login()
    {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $user   = User::findByEmail($email);
        $errors = [];

        if (!$user || !$user->isActive()) {
            $errors[] = 'Invalid credentials.';
        } else {
            // Demo only: plain compare
            if ($user->getPasswordHash() !== $password) {
                $errors[] = 'Invalid credentials.';
            }
        }

        if (!empty($errors)) {
            $page = 'auth';
            $authErrors = $errors;



            require __DIR__ . C2L_PATH;



            return;
        }

        $_SESSION['user_id']  = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role']     = $user->getRole();

        if ($user->getRole() === 'back') {
            header('Location: index.php?c=chatA&a=index');
        } else {
            header('Location: index.php?c=chatC&a=index');
        }
        exit;

    }
    public function logout()
    {
        session_destroy();
        header('Location: index.php?c=logC&a=index');
        exit;
    }


}