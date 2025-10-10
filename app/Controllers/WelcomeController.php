<?php
class WelcomeController extends BaseController {
  public function index() { $this->render('welcome', 'Welcome', 'bare'); }
}
