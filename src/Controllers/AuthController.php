<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Http\Request;
use App\Http\Response;
use App\RememberMe;
use App\UserRepository;
use App\Validation;
use PDOException;

final class AuthController
{
    public function __construct(private readonly UserRepository $users, private readonly Auth $auth) {}

    public function loginForm(Request $request): Response
    {
        return Response::html(render('login', $this->formData()));
    }

    public function login(Request $request): Response
    {
        verify_csrf();
        $login = strtolower($request->inputString('username'));
        $password = $request->inputString('password');
        $candidate = $this->users->findByLogin($login);
        if ($candidate !== null && \App\Password::verify($password, $candidate['password_hash'])) {
            if (\App\Password::needsRehash($candidate['password_hash'])) {
                $this->users->updatePassword($candidate['user']->id, \App\Password::hash($password));
            }
            $this->auth->login($candidate['user']);
            if ($request->inputString('remember') === '1') {
                (new RememberMe($this->users))->issue($this->users, $candidate['user']->id);
            }
            flash('You are now logged in.');
            return Response::redirect(url('/profile'));
        }
        $validator = \App\Validation::check(['username' => $login, 'password' => $password], [
            'username' => ['required' => true],
            'password' => ['required' => true],
        ]);
        if ($validator->valid()) {
            $validator->add('login', 'The username/email or password was incorrect.');
        } else {
            $validator->add('login', 'The username/email or password was incorrect.');
        }
        return $this->invalid('/login', $validator, ['username' => $login]);
    }

    public function registerForm(Request $request): Response
    {
        return Response::html(render('register', $this->formData()));
    }

    public function register(Request $request): Response
    {
        verify_csrf();
        $old = ['name' => $request->inputString('name'), 'username' => strtolower($request->inputString('username')), 'email' => strtolower($request->inputString('email'))];
        $password = $request->inputString('password');
        $confirm = $request->inputString('confirm_password');
        $validator = \App\Validation::check($old + ['password' => $password, 'confirm_password' => $confirm], [
            'name' => ['required' => true, 'max' => 100, 'label' => 'Name'],
            'username' => ['required' => true, 'username' => true],
            'email' => ['required' => true, 'email' => true],
            'password' => ['required' => true, 'password' => true, 'min_password' => 12],
            'confirm_password' => ['required' => true, 'match' => 'password'],
        ]);
        if (!$validator->valid()) return $this->invalid('/register', $validator, $old);
        try {
            $user = $this->users->create($old['name'], $old['username'], $old['email'], $password);
            $this->auth->login($user);
            flash('Account created. Welcome!');
            return Response::redirect(url('/profile'));
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23505') throw $exception;
            return $this->invalid('/register', \App\Validation::check([], [])->add('email', 'That username or email address is already registered.'), $old);
        }
    }

    public function logout(Request $request): Response
    {
        verify_csrf();
        $this->auth->logout();
        flash('You have been logged out.');
        return Response::redirect(url('/'));
    }

    private function formData(): array
    {
        return ['user' => $this->auth->currentUser(), 'authEnabled' => true, 'errors' => take_validation_errors(), 'old' => take_old_input()];
    }

    private function invalid(string $path, \App\Validator $validator, array $old): Response
    {
        $_SESSION['_validation_errors'] = $validator->errors();
        $_SESSION['_old_input'] = $old;
        return Response::redirect(url($path));
    }
}
