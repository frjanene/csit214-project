<?php
class AuthController extends BaseController {
  public function index() { $this->render('auth', 'Auth', 'bare'); }
  // later: signin(), signup(), signout()
}
