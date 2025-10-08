<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Sanitizer;
use App\Support\Session;
use App\Support\Validator;
use PDO;

final class ContactController extends Controller
{
    public function submit(): void
    {
        Session::ensureStarted();

        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::verify(is_string($token) ? $token : null)) {
            Flash::set('contact_error', 'Session expired. Please try again.');
            $this->redirect('/contact');
        }

        $sanitized = Sanitizer::clean($_POST, [
            'name' => 'string',
            'email' => 'email',
            'message' => 'text',
        ]);

        $errors = Validator::validate($sanitized, [
            'name' => ['required' => true, 'max' => 120],
            'email' => ['required' => true, 'email' => true, 'max' => 150],
            'message' => ['required' => true, 'max' => 2000],
        ]);

        if ($errors) {
            Flash::set('contact_error', reset($errors));
            $this->redirect('/contact');
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO contact_messages (name, email, message, ip_address, user_agent, status)
             VALUES (:name, :email, :message, :ip, :agent, :status)'
        );

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ipBinary = $ip ? @inet_pton($ip) : null;

        $stmt->execute([
            'name' => $sanitized['name'],
            'email' => $sanitized['email'],
            'message' => $sanitized['message'],
            'ip' => $ipBinary,
            'agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
            'status' => 'new',
        ]);

        Flash::set('contact_success', 'Thanks for reaching out—we’ll be in touch shortly.');
        $this->redirect('/contact');
    }
}
