<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Http\Request;
use App\Http\Response;
use App\Models\User;
use App\UserRepository;
use App\Validator;
use PDOException;

final class ProfileController
{
    public function __construct(private readonly UserRepository $users, private readonly Auth $auth)
    {
    }

    public function show(Request $request): Response
    {
        $user = $this->auth->currentUser();
        if ($user === null) {
            flash('Please log in to continue.', 'error');
            return Response::redirect(url('/login'));
        }
        return Response::html(render('profile', ['user' => $user->toArray(), 'authEnabled' => true, 'errors' => take_validation_errors(), 'old' => take_old_input()]));
    }

    public function update(Request $request): Response
    {
        verify_csrf();
        $user = $this->auth->currentUser();
        if ($user === null) {
            return Response::redirect(url('/login'));
        }
        $name = $request->inputString('name');
        $email = strtolower($request->inputString('email'));
        $validator = (new Validator())->required('name', $name, 'Name')->maxLength('name', $name, 100, 'Name')->email('email', $email);
        if (!$validator->valid()) {
            $_SESSION['_validation_errors'] = $validator->errors();
            $_SESSION['_old_input'] = ['name' => $name, 'email' => $email];
            return Response::redirect(url('/profile'));
        }
        try {
            $this->users->updateProfile($user->id, $name, $email);
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23505') {
                throw $exception;
            }
            $_SESSION['_validation_errors'] = ['email' => 'That email address is already registered.'];
            $_SESSION['_old_input'] = ['name' => $name, 'email' => $email];
            return Response::redirect(url('/profile'));
        }
        flash('Profile updated.');
        return Response::redirect(url('/profile'));
    }

    public function delete(Request $request): Response
    {
        verify_csrf();
        $user = $this->auth->currentUser();
        if ($user === null) {
            return Response::redirect(url('/login'));
        }
        $this->users->delete($user->id);
        $this->auth->logout();
        flash('Your account has been deleted.');
        return Response::redirect(url('/'));
    }
}
