<?php if ($use_custom_links) { ?>
<?php foreach ($basel_links as $basel_link) { ?>
<li class="static-link"><a class="anim-underline" href="<?php echo $basel_link['target']; ?>"><?php echo $basel_link['text']; ?></a></li>
<?php } ?>
<?php } else { ?>
<li class="static-link"><a class="anim-underline"  href="<?php echo $account; ?>" title="<?php echo $text_account; ?>"><?php echo $text_account; ?></a></li>
<li class="static-link is_wishlist"><a class="anim-underline wishlist-total" href="<?php echo $wishlist; ?>" title="<?php echo $text_wishlist; ?>"><span><?php echo $text_wishlist; ?></span></a></li>
<li class="static-link"><a class="anim-underline"  href="<?php echo $shopping_cart; ?>" title="<?php echo $text_shopping_cart; ?>"><?php echo $text_shopping_cart; ?></a></li>
<li class="static-link"><a class="anim-underline"  href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>"><?php echo $text_checkout; ?></a></li>
<?php } ?>