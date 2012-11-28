<?php global $current_user, $user_ID; //var_dump($current_user);?>
<div class="info">
<a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo ($current_user->data->display_name ? $current_user->data->display_name : $current_user->data->name); ?></a>
</div>
<div class="action"><input type="checkbox" name="volunteers[]" value="<?php echo $user_ID?>" /></div>