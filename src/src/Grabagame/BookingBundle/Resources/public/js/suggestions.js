    function submit_suggestion() {
      var suggestion_paragraph = document.getElementById("suggestion_paragraph");
      var suggestion = document.getElementById("suggestion").value;

      if (suggestion == "") {
        alert("Oops, you forgot to write your suggestion!");
        return false;
      }

      AjaxRequest.post( 
        {
          'url':'submit_suggestion.php'
          ,'parameters':{ 'suggestion':suggestion 
          ,'onLoading':loading_suggestion() }
          ,'onSuccess':function(req){ suggestion_paragraph.innerHTML = req.responseText; }
        }
      );
    }
 
    function submit_quickpoll(option_id, poll_id) {
      var poll_paragraph = document.getElementById("poll_paragraph");

      AjaxRequest.post( 
        {
          'url':'submit_vote.php'
          ,'parameters':{ 'option_id':option_id, 'poll_id':poll_id
          ,'onLoading':loading_poll() }
          ,'onSuccess':function(req){ poll_paragraph.innerHTML = req.responseText; }
        }
      );
    }

    function loading_suggestion() {
      var suggestion_paragraph = document.getElementById("suggestion_paragraph");
      suggestion_paragraph.innerHTML = "<br />Submitting...<br /><br /><img src='images/loading.gif' />";
    }

    function loading_poll() {
      var poll_paragraph = document.getElementById("poll_paragraph");
      poll_paragraph.innerHTML = "<br />Submitting...<br /><br /><img src='images/loading.gif' />";
    }
