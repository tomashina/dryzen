<div class="mz-content-top container-fluid clearfix">
  <?php if(!empty($title)){  ?>
  <h1 class="mz-content-title"><?php echo $title ?></h1>
  <?php } ?>
  <?php if(!empty($buttons)){ ?>
  <div class="pull-right">
    <?php foreach($buttons as $button){ ?>
      <?php if($button['form_target_id']){ ?>
      <button id="<?php echo $button['id'] ?>" type="submit" form="<?php echo $button['form_target_id'] ?>" <?php echo !empty($button['formaction'])?"formaction='{$button['formaction']}'":'' ?> <?php echo !empty($button['alert'])?"onclick=\"confirm('{$button['alert']}')?$(this).submit() : false;\"":'' ?> data-toggle="tooltip" title="<?php echo $button['tooltip'] ?>" class="btn <?php echo $button['class'] ?>"><i class="fa <?php echo $button['icon'] ?>"></i> <?php echo $button['name'] ?></button>
      <?php } elseif($button['href']) { ?>
      <a id="<?php echo $button['id'] ?>" href="<?php echo $button['href'] ?>" target="<?php echo $button['target'] ?>" data-toggle="tooltip" title="<?php echo $button['tooltip'] ?>" class="btn <?php echo $button['class'] ?>"><i class="fa <?php echo $button['icon'] ?>"></i> <?php echo $button['name'] ?></a>
      <?php } else { ?>
      <button id="<?php echo $button['id'] ?>" type="button"  data-toggle="tooltip" title="<?php echo $button['tooltip'] ?>" class="btn <?php echo $button['class'] ?>"><i class="fa <?php echo $button['icon'] ?>"></i> <?php echo $button['name'] ?></button>
      <?php } ?>
    <?php } ?>
  </div>
  <?php } ?>
</div>
<?php if(!empty($menu)){ ?>
<ul class="mz-content-nav nav nav-tabs">
  <?php foreach ($menu as $link){ ?>
    <?php if($link['href']){ ?>
    <li <?php if($link['id'] == $menu_active) echo 'class="active"' ?>><a href="<?php echo $link['href'] ?>"><?php echo $link['name'] ?> <i class="fa fa-external-link"></i></a></li>
    <?php } else { ?>
    <li <?php if($link['id'] == $menu_active) echo 'class="active"' ?>><a href="#<?php echo $link['id'] ?>" data-toggle="tab"><?php echo $link['name'] ?></a></li>
    <?php } ?>
  <?php } ?>
</ul>
<?php } ?>
<script>
$(document).ready(function(){
    // Alert to save unsave data
    <?php if(!empty($form_target_id)){ ?>

    var mz_form_submitting = false;
    var mz_form_changed = false;

    // Form submit
    $('form[id="<?php echo $form_target_id ?>"]').on('submit', function(){
        mz_form_submitting = true;
    });

    $('form[id="<?php echo $form_target_id ?>"] input, form[id="<?php echo $form_target_id ?>"] select').on('change', function(){
        mz_form_changed = true;
    });

    // Alert unsaved data before to leave form
    window.addEventListener('beforeunload', function(e){
        if (mz_form_submitting || !mz_form_changed) {
            return undefined;
        }

        var confirmationMessage = "<?php echo $text_alert_unsaved_form ?>";

        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
    
    <?php } ?>
});

</script>