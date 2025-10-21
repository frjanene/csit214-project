<?php
require_once __DIR__ . '/BaseController.php';

class PasswordController extends BaseController
{
  // GET /?r=forgot
  public function request()
  {
    // Render the request form (no backend action yet)
    $this->render('forgot', 'Reset Password', 'bare');
  }

  // GET /?r=forgot_done&email=...
  public function sent()
  {
    // Read and lightly sanitize the email from querystring
    $email = trim($_GET['email'] ?? '');

    // Keep it simple: if invalid, show empty (UI already explains possibilities)
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email = '';
    }

    // Render the confirmation page, echoing the email (views escape output)
    $this->render('forgot_done', 'Email Sent', 'bare', ['email' => $email]);
  }
}
