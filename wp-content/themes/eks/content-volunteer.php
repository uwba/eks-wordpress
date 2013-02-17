<?php global $current_user, $user_ID; //var_dump($current_user);?>
<div class="yui3-u-1-12"><input type="checkbox" name="volunteers[]" value="<?php echo $user_ID?>" /></div>
<div class="yui3-u-11-12">
<a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo ($current_user->data->display_name ? $current_user->data->display_name : $current_user->data->name); ?></a>
</div>