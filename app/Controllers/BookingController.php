<?php
class BookingController extends BaseController {
  public function index() { $this->render('bookings', 'My Bookings'); }
  // later: store(), cancel($id)
  // Note: bookings are triggered via modals, not a separate page
}
