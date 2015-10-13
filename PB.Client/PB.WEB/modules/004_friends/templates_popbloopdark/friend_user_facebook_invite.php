<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_12">
    <p>
      <input type="button"
        onclick="sendRequestToRecipients(); return false;"
        value="Send Request to Users Directly"
      />
      <input type="text" value="User ID" name="user_ids" />
      </p>
    <p>
    <input type="button"
      onclick="sendRequestViaMultiFriendSelector(); return false;"
      value="Send Request to Many Users with MFS"
    />
    </p>
    
    <script>
      function sendRequestToRecipients() {
        var user_ids = document.getElementsByName("user_ids")[0].value;
        FB.ui({method: 'apprequests',
          message: 'My Great Request',
          to: user_ids
        }, requestCallback);
      }

      function sendRequestViaMultiFriendSelector() {
        FB.ui({method: 'apprequests',
          message: 'Hi, lets join <a href="<?php echo $this->basepath; ?>">PopBloop</a>, get new friends and start hangout with me!'
        }, requestCallback);
      }
      
      function requestCallback(response) {
        // Handle callback here
        if(response && response.request) {
             // Here, requests have been sent, facebook gives you the request and the array of recipients
             //console.log(response);
             // location.href='<?php echo $this->basepath; ?>';
             alert('Invitation sent.');
        } else {
             // No requests sent, you can do what you want (like...nothing, and stay on the page).
             // alert('The invitation does not sent. Oh why? Please...');
        }
      }
    </script>
  
  </div>
</div>

