<?php global $current_user, $user_ID; 

    $volunteer_tax_sites = get_volunteer_tax_sites($user_ID);
    $tax_site_ids = array_keys($volunteer_tax_sites);
    $role = $volunteer_tax_sites[$tax_site_ids[0]][0];
    $tax_site = get_post($tax_site_ids[0]);   
    $tax_site_link = '<a href="/listings/' . $tax_site->post_name . '">' . $tax_site->post_title . '</a>'

?>
<div class="yui3-u-1-12"><input type="checkbox" name="volunteers[]" value="<?php echo $user_ID?>" /></div>
<div class="yui3-u-1-4">
    <a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $current_user->data->user_nicename; ?></a>
</div>
<div class="yui3-u-1-3">
    <?php echo $tax_site_link ?>
</div>
<div class="yui3-u-1-3">
    <?php echo $role; ?>
</div>