<div id="edit-alternatives">
  <div class="container">
    <div id="close-button-wrapper"><img src="images/close.png" onclick="closeThis()" alt="Close button" /></div>
    <p>Fill in complete information to add a new room,</strong> (all fields are required).</p>
    
    <!-- Form to add new room -->
    <form id="add-room-form" action="#" method="get">
      <div class="row">
        <label for="addRoomNumberInput">Room number</label>
        <input id="addRoomNumberInput" class="u-full-width" name="addRoomNumberInput" type="text">
      </div>

      <div class="row">
        <label for="addNoBedsInput">Number of beds</label>
        <input id="addNoBedsInput" class="u-full-width" name="addNoBedsInput" type="text">
      </div>

      <div class="row">
        <input type="radio" name="type" value="budget" checked> Budget<br />
        <input type="radio" name="type" value="standard"> Standard<br />
        <input type="radio" name="type" value="luxury"> Luxury
      </div>
      <div class="row">
        <input id="submitAddRoom" class="u-full-width button-primary" name="submitAddRoom" type="submit" value="Add room">
      </div>
    </form>

  </div>
</div>