$(document).ready(function(){
  /*
  Here we create a content slider using the jquery jFlow plugin.
  Instructions are in the HELP file and also included in HTML comments below.
  Link to the jFlow plugin can be found in help file.
  */

  // We do this so that on long loading pages (eg: google maps) the content doesn't overflow/flicker down
  // over the content.
  $('#header-slides-holder').css('visibility','visible');

  $.ajaxSetup({
    cache: false
  });

  $( "#datepicker" ).datepicker({
    altField: "#alt_date_field",
    altFormat: "mm/dd/yy",
    dateFormat: "DD, d MM, yy",
    showOn: "button",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true,
    autoSize: true,
    minDate: 0, 
    maxDate: "+14D",
    onSelect: function() { $("#date").submit() }
  });

  $("#datepicker").attr('disabled', 'disabled');
  $("#ajax_book, #result, #message, #cancel_booking, #cancel_result, #cancel_message").hide();

  //grab the increment
  var increment = $("#make_booking input[name=increment]").val() * 60;

  //Click any cell in the booking table
  $("#timetable td a").click(function (event) {

    var td_offset = $(this).offset();
    var td_width = $(this).width();
    var booking_id = event.target.id;
    
    $("#make_booking input[name=booking_id]").val(booking_id);

    if ($(this).hasClass("you")) {

      $("#ajax_book").width(td_width);
      if ($("#ajax_book").is(":visible")) {
        $("#ajax_book").fadeOut(function(){
          $("#result, #cancel_booking").hide();
          $("#booking").show();
        });
      } else {
        $("#result, #booking").hide();
        $("#cancel_booking").show();
        $("#ajax_book").height(130).fadeIn(function(){}).offset({ top: td_offset.top - 132, left: td_offset.left });
      }
      $("#cancel_booking_form input[name=booking_id]").val(booking_id);
    } else if ($(this).hasClass("booked")) {
      //do nothing
    } else {

      //check_conflicts();
      var id = booking_id.split("-");
      var timestamp = parseInt(id[0]);
      var court = parseInt(id[1]);
      var radios = $("#make_booking input[name=duration]").get().reverse();
      alert(radios.length);

      for (i = 0; i < radios.length; i++) {
        radios[i].disabled = false;
        if ((i + 1) == radios.length) {
          radios[i].checked = true;
        }
      }

      var collision = 0;

      for (i = 0; i < radios.length; i++) {
        if ($("#" + timestamp + "-" + court).hasClass("booked")) {
          //disable radios
          for (j = 0; j < (radios.length -i); j++) {
            radios[j].disabled = true;
          }
          break;
        }
        timestamp = timestamp + increment;
      }

      $("#ajax_book").width(td_width);
      if ($("#ajax_book").is(":visible")) {
        $("#ajax_book").fadeOut(function(){
          $("#cancel_booking, #cancel_message, #result").hide();
          $("#booking").show();
        });
      } else {
        $("#result, #cancel_booking, #cancel_message").hide();
        $("#booking").show();
        $("#ajax_book").height(200).fadeIn(function(){}).offset({ top: td_offset.top - 202, left: td_offset.left });
      }
    }
    return false;
  });

  $("#make_booking").submit(function(){
    var ajax_load = "<img src='images/loading.gif' alt='loading...' /> Working...";
    var loadUrl = "book.php";
    var values = $('#make_booking').serialize();
    $("#booking").hide();

    $("#result").html(ajax_load).load(loadUrl, values);
    $("#message").fadeIn(function() {
      var str = $("#result").text();
      var ids = str.split(",");

      for (i = 0; i < ids.length; i++) {
        $("#" + ids[i]).addClass('booked you');
      }

      $("#ajax_book").delay(2000).fadeOut(function(){
        $("#message").hide();
        $("#booking").show();
      });
    });
    return false;
  });

  $("#cancel_booking_form").submit(function(){
    var ajax_load = "<img src='images/loading.gif' alt='loading...' /> Working...";
    var values = $('#cancel_booking_form').serialize();
    var loadUrl = "cancel.php";

    $("#cancel_result").html(ajax_load).load(loadUrl, values);

    $("#cancel_booking").hide();
    $("#cancel_message").fadeIn(function() {

      var str = $("#cancel_result").text();
      var ids = str.split(",");

      for (i = 0; i < ids.length; i++) {
        $("#" + ids[i]).removeClass('booked you');
      }

      $("#ajax_book").delay(2000).fadeOut(function(){
        $("#cancel_message").hide();
        $("#booking").show();
      });
    });
    return false;
  });


});