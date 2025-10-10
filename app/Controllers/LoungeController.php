<?php
class LoungeController extends BaseController {
  public function index() { $this->render('find_lounges', 'Find Lounges'); }
  // later: list(), details($id), search(), etc.
}
