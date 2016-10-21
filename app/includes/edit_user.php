<!-- Add user form -->
<div class="row">
  <div class="eight columns offset-by-two column">
    
    <?php

    // Page shows profile and mood image including form to update info
    // Set placeholders
    $user = $_SESSION['username'];
    $hotel = $_SESSION["hotel"];
    $placeholders = new User($hotel);

    $placeholders->thisUserInfo( $user );

    $mail = $placeholders->getUser();
    $firstname = ucfirst($placeholders->getFirstname());
    $lastname = ucfirst($placeholders->getLastname());
    $phone = $placeholders->getPhone();
    $active = $placeholders->getActive();
    $role = $placeholders->getRole();
    $image = $placeholders->getImage();
    $start_date = substr($placeholders->getStartDate(), 0, 10);

    ?>

    <div class="row">
        <h4><?php if(isset($firstname)) { echo "Hi " . $firstname . "!"; } ?><span class="hello">
          <strong>How are you today?</strong>
        </span></h4>
    </div>

    <!-- Icons -->
    <div style="width: 95%;margin: 0 auto 25px;max-width: 350px;">
      <div class="mood-icon-container">
        <a href="?mood=sad"><img class="mood-icon" src="images/sad.png" alt="Smiley"></a>
      </div>
      <div class="mood-icon-container">
        <a href="?mood=whistles"><img class="mood-icon" src="images/whistles.png" alt="Smiley"></a>
      </div>
      <div class="mood-icon-container">
        <a href="?mood=happy"><img class="mood-icon" src="images/happy.png" alt="Smiley"></a>
      </div>
    </div>

    <!-- info -->
    <div class="row">
        <h4>You.</h4>
        <p><i class="fa fa-user" aria-hidden="true"></i> <?= $firstname . " " . $lastname; ?> (<?= $role; ?>)<br />
        <i class="fa fa-envelope" aria-hidden="true"></i> <?= $mail; ?><br />
        <i class="fa fa-phone" aria-hidden="true"></i> <?= $phone; ?><br />
        User since <?= $start_date; ?></p>
    </div>

    <!-- form -->
    <form id="add-user-form" action="#" method="get">
    <h4>Keep your profile up to date.</h4>
      <div class="row">
        <label for="editProfileFirstnameInput">Firstname</label>
        <input id="editProfileFirstnameInput" class="u-full-width" name="editProfileFirstnameInput" type="text" value="<?= $firstname; ?>">
      </div>

      <div class="row">
        <label for="editProfileLastnameInput">Lastname</label>
        <input id="editProfileLastnameInput" class="u-full-width" name="editProfileLastnameInput" type="text" value="<?= $lastname; ?>">
      </div>

      <div class="row">
        <label for="editProfilePhoneInput">Phone</label>
        <input id="editProfilePhoneInput" class="u-full-width" name="editProfilePhoneInput" type="text" value="<?= $phone; ?>">
      </div>

      <div class="row">
        <input id="submitEditProfile" class="u-full-width button-primary" name="submitEditProfile" type="submit" value="Update profile">
      </div>
    </form>

    <!-- Change password form -->
        <form id="change-password-form" action="#" method="post">
          <h4>Change password.</h4>
          <div class="row">
            <label for="changePasswordInput">New password<span class="thin">(min 4 charachters)</span></label>
            <input id="changePasswordInput" class="u-full-width" name="changePasswordInput" type="password">
          </div>

          <div class="row">
            <input id="changePasswordSubmit" class="u-full-width button-primary" name="changePasswordSubmit" type="submit" value="Change password">
          </div>
        
        </form>

  </div>
</div>