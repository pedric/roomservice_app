<?php

// Check if user param in url exists and if validates that it is setup for reset password
$validation = false;

if ( isset($_REQUEST['user']) ) { 
  
  $user = $_REQUEST['user'];

  $obj = new User();

  if ( $obj->validateSetPassword( $user ) ) {

    $validation = true;
  
  }
}

// Echo form if user is set up to set new password
if ( isset($_REQUEST['user']) && $validation ) {

?>

<!-- Logo -->
<div class="row">
  <div class="eight columns offset-by-two column">
    <div class="row">
      <img class="logo" src="images/logo_black.png" alt="Clean Rooms logo">
    </div>
  </div>
</div>

<!-- Info -->
<div class="row">
  <div class="eight columns offset-by-two column">
    <p>
      Choose a password below to start using Clean Rooms.
    </p>
  </div>
</div>

<!-- Register form -->
<div class="row">
  <div class="eight columns offset-by-two column">
    
    <p><?= $user;?></p>
    <form action="#" method="post">
      
      <div class="row">
        <input id="newMailEmailInput" class="u-full-width" name="newMailEmailInput" type="hidden" value="<?= $user; ?>">
      </div>

      <div class="row">
        <label for="newMailPasswordInput">Password <span class="thin">(min 4 charachters)</span></label>
        <input id="newMailPasswordInput" class="u-full-width" name="newMailPasswordInput" type="password">
      </div>

      <div class="row">
        <input id="submitNewUserFromMail" class="u-full-width button-primary" name="submitNewUserFromMail" type="submit" value="Join Clean Rooms">
      </div>

    </form>

  </div>
</div>

<?php 

// Echo error-page
} else { 

?>

<!-- Logo -->
<div class="row">
  <div class="eight columns offset-by-two column">
    <div class="row">
      <img class="logo" src="images/logo_black.png" alt="Clean Rooms logo">
    </div>
  </div>
</div>

<!-- Info -->
<div class="row">
  <div class="eight columns offset-by-two column">
    <h4>Error.</h4>
    <p>
      This user is not setup to reset password. Contact an administrator at your organisation or proceed to <a href="index.php">login</a>.
    </p>
  </div>
</div>

<?php } ?>









