<div id="edit-alternatives">
  <div class="container">
    <div id="close-button-wrapper"><img src="images/close.png" onclick="closeThis()" alt="Close button" /></div>
    <p><strong>A e-mail with login-instructions will be sent</strong> to the e-mailaddress entered below. <strong>Employees</strong> can only read content, handle the <q>To do</q> and their own profile info. <strong>Administrators</strong> can handle room- and users.</p>
    
    <!-- Form to add new user -->
    <form id="add-user-form" action="#" method="get">
      <div class="row">
        <label for="addUserEmailInput">New users e-mail</label>
        <input id="addUserEmailInput" class="u-full-width" name="addUserEmailInput" type="email">
      </div>
      <div class="row">
        <input type="radio" name="role" value="employee" checked> Employee<br>
        <input type="radio" name="role" value="administrator"> Administrator
      </div>
      <div class="row">
        <input id="submitAddUser" class="u-full-width button-primary" name="submitAddUser" type="submit" value="Add new user">
      </div>
    </form>

  </div>
</div>