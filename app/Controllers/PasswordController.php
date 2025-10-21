<?php
class PasswordController extends BaseController {

  public function request() {
    $this->render('forgot', 'Reset Password', 'bare');
  }

  public function sent() {
    $email = $_GET['email'] ?? '';
    $this->render('forgot_done', 'Email Sent', 'bare', ['email' => $email]);
  }
}
