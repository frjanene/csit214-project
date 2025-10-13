<?php
class PasswordController extends BaseController {

  // Step 1: request form
  public function request() {
    $this->render('forgot', 'Reset Password', 'bare');
  }

  // Step 2: success page (GET for demo; in real app you’d POST then redirect)
  public function sent() {
    $email = $_GET['email'] ?? '';
    $this->render('forgot_done', 'Email Sent', 'bare', ['email' => $email]);
  }
}
