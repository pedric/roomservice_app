<!-- Add user form -->
<div class="row">
  <div class="eight columns offset-by-two column">
    
    <?php

    // Page shows hotel info, if admin a update form is shown. Form holds value but can be changed.
    $hotel = $_SESSION['hotel_id'];

    // Set placeholders
    $placeholders = new Room();

    $placeholders->thisHotelInfo( $hotel );

    $name = ucfirst($placeholders->getName());
    $phone = $placeholders->getPhone();
    $mail = $placeholders->getMail();
    $start_date = substr($placeholders->getStartDate(), 0, 10);

    $role = new User($hotel);

    $user = $_SESSION['username'];

    ?>

    <div class="row">
        <h4><?= $name; ?></h4>
        <p>
        <i class="fa fa-envelope" aria-hidden="true"></i> <?= $mail; ?><br />
        <i class="fa fa-phone" aria-hidden="true"></i> <?= $phone; ?><br />
        Registered <?= $start_date; ?>
        </p>
    </div>

    <?php if ( $role->isAdmin( $user ) ) { ?>

    <form id="add-user-form" action="#" method="get">

      <h4>Keep the info up to date.</h4>

      <div class="row">
        <label for="editHotelMailInput">E-mail</label>
        <input id="editHotelMailInput" class="u-full-width" name="editHotelMailInput" type="email" value="<?= $mail; ?>">
      </div>

      <div class="row">
        <label for="editHotelPhoneInput">Phone</label>
        <input id="editHotelPhoneInput" class="u-full-width" name="editHotelPhoneInput" type="text" value="<?= $phone; ?>">
      </div>

      <div class="row">
        <input id="submitEditHotel" class="u-full-width button-primary" name="submitEditHotel" type="submit" value="Update info">
      </div>

    </form>

    <?php } ?>

  </div>
</div>