<?php
class ProfileController extends BaseController {
  public function index() { $this->render('profile', 'Profile'); }
  // later: update(), password(), preferences()
}
