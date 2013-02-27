<?php
// Template Name: Coordinator: Email all
?>
<?php
wp_enqueue_style('yui', 'http://yui.yahooapis.com/3.8.1/build/cssgrids/grids-min.css');

/* form proccessing */
wp_enqueue_script('json-form', '/wp-content/plugins/volunteer/js/jquery.form.js', array('jquery'));
?>
<div id="main">
    <div class="section-head">
        <h1><?php _e('Email Volunteers', APP_TD); ?></h1>
    </div>
    <?php
    if (!empty($_POST)) {
        $u = wp_get_current_user();
        $errors = array();
        if (!isset($_POST['subject']) || empty($_POST['subject'])) {
            $errors[] = 'The email subject is empty.';
        }
        if (!isset($_POST['message']) || empty($_POST['message'])) {
            $errors[] = 'The email message is empty.';
        }
        if (!isset($_POST['volunteers']) || !count($_POST['volunteers'])) {
            $errors[] = 'Please select at least one volunteer to email.';
        }

        if (!count($errors)) {
            // Validate volunteers - if user hack form
            $volunteers = get_volunteers();
            $volunteers_ids = array();
            foreach ($volunteers as $volunteer) {
                $volunteers_ids[] = $volunteer->post_author;
            }

            global $current_user;
            get_currentuserinfo();
            $name = $current_user->user_login;
            $email = 'noreply@' . $_SERVER["HTTP_HOST"];
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $headers = "From: \"{$name}\"<{$email}>\r\n";

            foreach ($volunteers as $volunteer) {
                if (in_array($volunteer->post_author, $_POST['volunteers'])) {
                    set_current_user($volunteer->post_author);
                    get_currentuserinfo();
                    $to = "\"{$current_user->display_name}\"<{$current_user->user_email}>";
                    $message = str_replace('!user', $current_user->display_name, $message);
                    if (!wp_mail($to, $subject, $message, $headers)) {
                        $errors[] = htmlentities("Message to {$to} is failed");
                    }
                }
            }
            ?>
            <div class="notice success"><span>The emails were sent.</span></div>
            <?php
        } else {
            ?>
            <div class="notice error"><span><?php echo implode(' ', $errors) ?></span></div>
            <?php
        }

        wp_set_current_user($u->ID);
    }
    ?>
    <div class="categories-list">
        <?php
        $volunteers = get_volunteers();
        if (count($volunteers) > 0) {
            ?>
            <div id="result"></div>

            <form method="post" action="/email-all" class="email-form" id="email-form">
                <div class="yui3-g">  
                    <div class="yui3-u-1">
                        <p>Select the volunteers and enter your message below.</p>
                        <?php
                        $u = wp_get_current_user();

                        foreach ($volunteers as $post) {

                            if ($post->post_author) {
                                wp_set_current_user($post->post_author);

                                // Modified from page-coordinator-volunteers.php
                                $volunteer_tax_sites = get_volunteer_tax_sites($user_ID);
                                $tax_site_ids = array_keys($volunteer_tax_sites);
                                $role = $volunteer_tax_sites[$tax_site_ids[0]][0];
                                $tax_site = get_post($tax_site_ids[0]);
                                $tax_site_link = '<a href="/listings/' . $tax_site->post_name . '">' . $tax_site->post_title . '</a>';

                                $items[] = array(
                                    'checkbox' => '<input type="checkbox" name="volunteers[]" value="' . $user_ID . '" />',
                                    'name' => '<a href="' . get_permalink($post->ID) . '" rel="bookmark">' . $current_user->data->user_nicename . '</a>',
                                    'tax_site' => $tax_site_link,
                                    'role' => $role
                                );
                            }
                        }
                        echo OutputArrayToTable($items, array('&nbsp;', 'Volunteer', 'Tax Site', 'Role'));
                        set_current_user($u->ID);
                        ?>
                        <p><input type="button" id="select_all" value="Select All" class="btn-small" /></p>
                    </div>
                    <div class="yui3-u-1" style="padding-top:10px">
                        <label>Email Subject:
                            <input type="text" id="subject" name="subject" style="width:100%"/>
                        </label>
                        <label>Email Message:
                            <textarea id="message" name="message" style="width:100%;height:100px"></textarea>
                        </label>
                        <p>&nbsp</p>
                        <input type="submit" value="Send Email"/>
                    </div>
                </div> 
            </form>
        <?php } else { ?><p>You do not yet have any volunteers assigned to your Tax Sites.</p><?php } ?>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#select_all').toggle(function() {
                $('input:checkbox').attr('checked', true);
                return false;
            }, function() {
                $('input:checkbox').attr('checked', false);
                return false;
            });
        });
    </script>

</div>
<?php get_sidebar(); ?>
