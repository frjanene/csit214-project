<?php
class MembershipController extends BaseController {
  public function index() { $this->render('memberships', 'Memberships'); }
  // later: plans(), subscribe(), cancel()
}
