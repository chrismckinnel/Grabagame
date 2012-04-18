<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{% block title %}Home{% endblock %}</title>
    <meta content="Online sports court booking system" http-equiv="description" />
    <meta content="sport booking, court booking, booking system, booking" http-equiv="keywords" />
    <link href="{{ asset('bundles/grabagame/bookingbundle/css/styles.css) }}" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript" src="{{ asset('bundles/grabagame/bookingbundle/js/jquery-1.2.6.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundles/grabagame/bookingbundle/js/jquery.flow.1.2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundles/grabagame/bookingbundle/js/jquery.easing.1.3.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundles/grabagame/bookingbundle/js/ajaxrequest.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundles/grabagame/bookingbundle/js/suggestions.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundles/grabagame/bookingbundle/js/jflow.js') }}"></script>
</head>
<body>
<div id="clouds">
    <div id="wrapper">
        <div id="background_texture">
            <div id="header">
                <div id="logo"><a href="index.php"><img src="{{ asset('bundles/grabagame/bookingbundle/images/grabagame.gif') }}" alt="logo" /></a></div>
                <div id="header_right">
                    <div id="hidden-controller"> 
                        <span class="jFlowControl">Menu1</span>
                    </div>
                    <div id="header-slides-holder" style="visibility:hidden;"> 
                        <div id="header-slides">
                            <div class="slide"> <img src="images/image_1.jpg" width="208" height="171" class="header-picture" alt="slogan icon 1"/>
                                <div class="header-about-main"> <img class="header-heading" src="images/title_header.png" alt="Lorem ipsum dolor"/>
                                    <p class="header-description">Grab a Game provides sports clubs with a hosted on-line court booking system.    <br /><br />It is simple and easy to use, a breeze to set up and best of all it's <a href="#">very affordable</a></strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="menu_container">
                <ul>
                    <li> <a href="{{ path('_home') }}" class="m1">Home</a> </li>
                    <li> <a href="{{ path('_about') }}" class="m2">About Us</a> </li>
                    <li> <a href="{{ path('_booking') }}" class="m3">Booking</a> </li>
                </ul>
                <div id="search">
                    <form action="" method="post">
                        <a href="{{ path('_search') }}"><img class="button" src="{{ asset('bundles/grabagame/bookingbundle/images/button_login.gif') }}" /></a>
                    </form>
                </div>
            </div>
            <!--end back ground texture-->
            <div class="three_boxes">
                <!--postit 1-->
                <div class="post-it" style="margin-right:10px;">
                    <div class="pin"><img src="images/icon_pin_blue.png" alt="pin" /></div>
                    <div class="title"><img src="images/title_1.gif" alt="title1" /></div>
                    <div class="post-it_text">Try <a href="#">logging in as our Demo User</a> to see what all the fuss is about!<br /><br />We also offer a <strong>fully functional 30 day free trial</strong>.    </div>
                    <div class="image"><img src="images/icon_new.png" alt="new" /></div>
                    <div class="button"><a href="#"><img src="images/button_learn_more.gif" alt="Learn More" width="96" height="27" border="0" /></a></div>
                </div>
                <!--END postit 1-->
                <!--postit 2-->
                <div class="post-it" style="margin-right:10px;">
                    <div class="pin"><img src="images/icon_pin_red.png" alt="pin" /></div>
                    <div class="title"><img src="images/title_2.png" alt="title2" /></div>
                    <div class="post-it_text">New Zealand squash clubs need to embrace technology and I figure the best way to get the ball rolling is to offer Grab a Game for free.</div>
                    <div class="image"><img src="images/icon_paper.gif" alt="paper" /></div>
                    <div class="button"><a href="#"><img src="images/button_learn_more.gif" alt="Learn More" width="96" height="27" border="0" /></a></div>
                </div>
                <!--END postit 2-->
                <!--postit 3-->
                <div class="post-it" id="facebook">
                    <div class="pin"><img src="images/icon_pin_light_blue.png" alt="pin" /></div>
                    <div class="title"><img src="images/title_3.gif" alt="title3" /></div>
                    <div class="post-it_text">Stay connected with the latest updates by following us on Twitter / Facebook!<br /><a target="_new" href="http://twitter.com/grabagame"><img src="images/twitter.png" alt="Twitter" /></a><a target="_new" href="http://www.facebook.com/profile.php?id=100000438650584#/profile.php?id=100000438650584&v=wall"><img id="test" src="images/facebook.png" alt="Facebook" /></a></div>
                </div>
                <!--END postit 3-->
            </div>
            <!--end three boxes-->
            <!--start main paper-->
            <div id="paper_container">
                <div class="top"><img src="images/content_top.png" alt="top" /></div>
                <div id="main_content">
                    <div class="left">
                        <h1>On-line court booking for sports clubs</h1>

                        <ul id="features">
                        <strong>Having an online booking system will:</strong><br />
                        <li>Save your members time and money</li>
                        <li>Utilise your courts more efficiently </li>
                        <li>Avoid confusion for your members over bookings and cancellations</li>
                        <hr / class="features">
                        <strong>Our booking system has:</strong><br />
                        <li>An easy to remember username/password authentication system</li>
                        <li>A unique check-in system to make sure players are turning up at the club for their booked games</li>
                        <li>A "forgot your password" feature</li>
                        <li>Room for corporate sponsorship on the login page</li>
                        <hr / class="features">
                        <strong>Your members will be able to:</strong><br />
                        <li>Book courts from home, work or anywhere with an internet connection</li>
                        <li>Hide their names from bookings for privacy reasons</li>
                        <li>Cancel bookings from anywhere at any time</li>
                        <hr / class="features">
                        <strong>Club administrator users will be able to:</strong><br />
                        <li>Use the event booking feature which enables them to book courts in blocks for the likes of tournaments or interclub.</li>
                        <li>Post notices to the club via the notice board feature</li>
                        <li>Access a bookings breakdown of previous months</li>
                            <ul>
                                <li>Top users</li>
                                <li>Top no-show users (who isn't turning up for their games)</li>
                                <li>Total bookings for current or previous months</li>
                                <li>Most popular court</li>
                            </ul>
                        </li>
                        <li>Generate a list of email addresses in an excel spreadsheet for the likes of mail merges</li>
                        <li>View old bookings for your records</li>
                    </ul>
                    </div>
                    <div class="right">
                        <!--start news-->
                        <div class="quick_poll">
                            <div class="text">
                                <p align="center"><img src="images/title_quick_poll.gif" alt="Quick Poll" border="0" /></p>
                                <?php
                                include('includes/db_connect.php');

                                $query = "SELECT * FROM quick_poll WHERE active = 1";
                                $result = mysql_query($query) or die(mysql_error());
                                
                                while ($rows = mysql_fetch_assoc($result)) {
                    
                                    $ip_query = "SELECT * FROM quick_poll_ips WHERE ip_address = '" . $_SERVER['REMOTE_ADDR'] . "'";
                                    $ip_result = mysql_query($ip_query) or die(mysql_error());

                                    if (mysql_num_rows($ip_result) == 0) {

                                        $option_query = "SELECT * FROM options WHERE poll_id = '" . $rows['poll_id'] . "'";
                                        $option_result = mysql_query($option_query) or die(mysql_error());
                                        ?>
                                        <form name="quick_poll">
                                        <p id="poll_paragraph"><strong><?php echo $rows['question']; ?></strong><br /><br />
                                        <?php
                                        while ($option_rows = mysql_fetch_assoc($option_result)) {
                                            ?>
                                            <input type="radio" name="quick_poll" id="qp_<?php echo $option_rows['option_id']; ?>" value="<?php echo $option_rows['option_id']; ?>" onclick="submit_quickpoll(<?php echo $option_rows['option_id']; ?>, <?php echo $option_rows['poll_id']; ?>)"/><label for="qp_<?php echo $option_rows['option_id']; ?>"><?php echo $option_rows['option_text']; ?></label><br />
                                            <?php
                                        }
                                        ?>
                                        </p></form>
                                        <?php
                                    } else {

                                        $ip_query = "SELECT * FROM quick_poll_ips WHERE ip_address = '" . $_SERVER['REMOTE_ADDR'] . "'";
                                        $ip_result = mysql_query($ip_query) or die(mysql_error());

                                        $row = mysql_fetch_assoc($ip_result);
                                        $your_vote = $row['option_id'];
                                        $poll_id = $row['poll_id'];

                                        $question_query = "SELECT question FROM quick_poll WHERE poll_id = '" . $poll_id . "'";
                                        $question_result = mysql_query($question_query) or die(mysql_error());

                                        $row = mysql_fetch_assoc($question_result);
                                        $question = $row['question'];

                                        echo "<strong>Thanks for your input, check out the results:</strong><br />";

                                        echo $question . "<br /><br />";

                                        $votes_query = "SELECT * FROM options WHERE poll_id = '" . $poll_id . "' ORDER BY num_of_votes DESC";
                                        $votes_result = mysql_query($votes_query) or die(mysql_error());

                                        while ($votes_rows = mysql_fetch_assoc($votes_result)) {
                                            if ($votes_rows['option_id'] == $your_vote) {
                                                echo "<strong>" . $votes_rows['option_text'] . " (" . $votes_rows['num_of_votes'] . ") - Your vote</strong><br />";
                                            } else {
                                                echo $votes_rows['option_text'] . " (" . $votes_rows['num_of_votes'] . ") <br />";
                                            }
                                        }
                                    }

                                }

                                ?>
                            </div>
                        </div>

                        <div class="news_bubble">
                            <div class="text">
                                <p align="center"><img src="images/title_suggestions.gif" alt="Latest News" border="0" /></p>
                                <p id="suggestion_paragraph"><strong>We would love to hear your great ideas!</strong><br />
                                <textarea name="suggestion" id="suggestion" rows="2" cols="28"></textarea>
                                <input type="button" name="submit_suggestion" value="Suggest it!" onclick="submit_suggestion()" />
                                </p>
                            </div>
                        </div>
                        <!--end news-->
                        <!--start contact-->
                        <div class="contact_box">
                            <div class="text">
                                <p><img src="images/title_contact.gif" alt="Latest News" border="0" /></p>
                                <p>&nbsp;</p>
                                <p>
                                    <img src="images/icon_phone.gif" alt="Phone" width="18" height="18" align="top" class="icon" /> +64 21 242 0561 <br />
                                    <img src="images/icon_phone.gif" alt="Phone" width="18" height="18" align="top" class="icon" /> +64 27 469 9442 <br />
                                    <img src="images/icon_door.gif" alt="Address" width="18" height="18" align="top" class="icon" /> 29 / 3 Jackson Street, Te Anau, New Zealand<br />
                                    <img src="images/icon_email.gif" alt="Email" width="18" height="18" align="top" class="icon" /><a href="mailto:chris@grabagame.co.nz">chris@grabagame.co.nz</a></p>
                            </div>
                        </div>
                        <!--end contact-->
                    </div>
                    <hr class="clear" />
                </div>
                <div class="paper_bottom"><img src="images/content_bottom.png" alt="bottom" /></div>
            </div>
            <!-- end main paper-->
            <div id="footer">
                <div class="left">&copy; Grab a Game 2009</div>
                <div class="right"><a href="#">About Us</a></div>
            </div>
        <!--end wrapper-->
    </div>
</div>
</div>
<!--end cloud-->
</body>
</html>
