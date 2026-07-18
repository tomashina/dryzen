<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          
         	<div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($status)) { ?>
            <label><input type="radio" name="status" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="status" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" name="status" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="status" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>
            
            <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_contrast; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if ($contrast) { ?>
            <label><input type="radio" name="contrast" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="contrast" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" name="contrast" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="contrast" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>            
            
            <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $text_use_block_title; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($use_title)) { ?>
            <label><input type="radio" class="title_select" name="use_title" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="title_select" name="use_title" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" class="title_select" name="use_title" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="title_select" name="use_title" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>
            
            <div class="form-group title_field" style="display:<?php if (!empty($use_title)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $text_block_pre_line; ?></label>
            <div class="col-sm-10">
            <?php foreach ($languages as $language) { ?>
            <div class="input-group">
            <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
            <input type="text" name="title_pl[<?php echo $language['language_id']; ?>]" value="<?php echo isset($title_pl[$language['language_id']]) ? $title_pl[$language['language_id']] : ''; ?>" class="form-control" />
            </div>
            <?php } ?>
            </div>
            </div>
            
            <div class="form-group title_field" style="display:<?php if (!empty($use_title)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $text_block_title; ?></label>
            <div class="col-sm-10">
            <?php foreach ($languages as $language) { ?>
            <div class="input-group">
            <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
            <input type="text" name="title_m[<?php echo $language['language_id']; ?>]" value="<?php echo isset($title_m[$language['language_id']]) ? $title_m[$language['language_id']] : ''; ?>" class="form-control" />
            </div>
            <?php } ?>
            </div>
            </div>
            
            <div class="form-group title_field" style="display:<?php if (!empty($use_title)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $text_block_sub_line; ?></label>
            <div class="col-sm-10">
            <?php foreach ($languages as $language) { ?>
            <div class="input-group">
            <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
            <textarea type="text" name="title_b[<?php echo $language['language_id']; ?>]" class="form-control"><?php echo isset($title_b[$language['language_id']]) ? $title_b[$language['language_id']] : ''; ?></textarea>
            </div>
            <?php } ?>
            </div>
            </div>
    

          <div class="form-group clearfix">
            <label class="col-sm-2 control-label" for="input-name"><span data-toggle="tooltip" title="<?php echo $entry_tabs_help; ?>"><?php echo $entry_tabs; ?></span></label>
            <div class="col-sm-10">
            <div class="well well-sm" style="height: 150px; overflow: auto;margin-bottom:10px;">
            <?php foreach ($tabs as $keytab => $tab) { ?>
            <?php if (isset($selected_tabs['tabs']) && in_array($keytab, $selected_tabs['tabs'])) { ?>
             <label><input type="checkbox" name="selected_tabs[tabs][]" value="<?php echo $keytab; ?>" checked="checked" />
             <?php echo $tab['tab_title']; ?></label><br />
               <?php } else { ?>
             <label><input type="checkbox" name="selected_tabs[tabs][]" value="<?php echo $keytab; ?>" />
             <?php echo $tab['tab_title']; ?></label><br />
            <?php } ?>
           <?php } ?>
           </div>           
            <a href="<?php echo $groups_url; ?>" target="_blank"><?php echo $groups_text; ?></a>
            </div>
          </div>
          
          
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $entry_tabstyle_help; ?>"><?php echo $entry_tabstyle; ?></span></label>
            <div class="col-sm-10">
              <select name="tabstyle" class="form-control">
                
                <?php if ($tabstyle == '') { ?>
                <option value="0" selected="selected"><?php echo $text_tabs_default; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_tabs_default; ?></option>
                <?php } ?>
                
                <?php if ($tabstyle == 'nav-tabs-sm text-center') { ?>
                <option value="nav-tabs-sm text-center" selected="selected"><?php echo $text_tabs_floating; ?></option>
                <?php } else { ?>
                <option value="nav-tabs-sm text-center"><?php echo $text_tabs_floating; ?></option>
                <?php } ?>
                
                <?php if ($tabstyle == 'nav-tabs-lg text-center') { ?>
                <option value="nav-tabs-lg text-center" selected="selected"><?php echo $text_tabs_floating_lg; ?></option>
                <?php } else { ?>
                <option value="nav-tabs-lg text-center"><?php echo $text_tabs_floating_lg; ?></option>
                <?php } ?>
                

              </select>
            </div>
          </div>
          
          
           <div class="form-group">
            <label class="col-sm-2 control-label" for="input-limit"><span data-toggle="tooltip" title="<?php echo $entry_limit_help; ?>"><?php echo $entry_limit; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="limit" value="<?php echo $limit; ?>"  id="input-limit" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-image_width"><?php echo $entry_width; ?></label>
            <div class="col-sm-10">
              <input type="text" name="image_width" value="<?php echo $image_width; ?>" id="input-image_width" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-image_height"><?php echo $entry_height; ?></label>
            <div class="col-sm-10">
              <input type="text" name="image_height" value="<?php echo $image_height; ?>" id="input-image_height" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-columns"><?php echo $entry_columns; ?></label>
            <div class="col-sm-10">
              <select name="columns" id="input-columns" class="form-control">
               
                
                <?php if ($columns == '5') { ?>
                <option value="5" selected="selected"><?php echo $text_grid5; ?></option>
                <?php } else { ?>
                <option value="5"><?php echo $text_grid5; ?></option>
                <?php } ?>
                
                <?php if ($columns == '4') { ?>
                <option value="4" selected="selected"><?php echo $text_grid4; ?></option>
                <?php } else { ?>
                <option value="4"><?php echo $text_grid4; ?></option>
                <?php } ?>
                
                <?php if ($columns == '3') { ?>
                <option value="3" selected="selected"><?php echo $text_grid3; ?></option>
                <?php } else { ?>
                <option value="3"><?php echo $text_grid3; ?></option>
                <?php } ?>
                
                <?php if ($columns == '2') { ?>
                <option value="2" selected="selected"><?php echo $text_grid2; ?></option>
                <?php } else { ?>
                <option value="2"><?php echo $text_grid2; ?></option>
                <?php } ?>
                
                <?php if ($columns == '1') { ?>
                <option value="1" selected="selected"><?php echo $text_grid1; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_grid1; ?></option>
                <?php } ?>
                
                <?php if ($columns == 'list') { ?>
                <option value="list" selected="selected"><?php echo $text_gridz; ?></option>
                <?php } else { ?>
                <option value="list"><?php echo $text_gridz; ?></option>
                <?php } ?>
                
              </select>
            </div>
          </div>
          
          
          	<div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_carousel; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($carousel)) { ?>
            <label><input type="radio" class="carousel_select" name="carousel" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="carousel_select" name="carousel" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" class="carousel_select" name="carousel" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="carousel_select" name="carousel" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>
          
           <div class="form-group carousel_field" style="display:<?php if (!empty($carousel)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label" for="input-carousel"><?php echo $entry_rows; ?></label>
            <div class="col-sm-10">
              <input type="text" name="rows" value="<?php echo $rows; ?>"  id="input-rows" class="form-control" />
            </div>
          </div>
          
          
          	<div class="form-group carousel_field" style="display:<?php if (!empty($carousel)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $entry_carousel_a; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($carousel_a)) { ?>
            <label><input type="radio" name="carousel_a" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="carousel_a" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" name="carousel_a" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="carousel_a" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>
          
            <div class="form-group carousel_field" style="display:<?php if (!empty($carousel)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $entry_carousel_b; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($carousel_b)) { ?>
            <label><input type="radio" name="carousel_b" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="carousel_b" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" name="carousel_b" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="carousel_b" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>
          

          
          <div class="form-group">
            <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $entry_countdown_h; ?>"><?php echo $entry_countdown; ?></span></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($countdown)) { ?>
            <label><input type="radio" name="countdown" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="countdown" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" name="countdown" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" name="countdown" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>
            
            
            <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $text_use_button; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($use_button)) { ?>
            <label><input type="radio" class="button_select" name="use_button" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="button_select" name="use_button" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" class="button_select" name="use_button" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="button_select" name="use_button" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>

            <div class="form-group button_field" style="display:<?php if (!empty($use_button)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $entry_link_title; ?></label>
                <div class="col-sm-10">
                <?php foreach ($languages as $language) { ?>
                <div class="input-group">
                <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                 <input type="text" name="link_title[<?php echo $language['language_id']; ?>]" value="<?php echo isset($link_title[$language['language_id']]) ? $link_title[$language['language_id']] : ''; ?>" class="form-control"/>
                 </div>
                 <?php } ?>
                </div>
            </div>
            
            <div class="form-group button_field" style="display:<?php if (!empty($use_button)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $entry_link_href; ?></label>
            <div class="col-sm-10">
              <input type="text" name="link_href" value="<?php echo $link_href; ?>" class="form-control" />
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $text_use_margin; ?></label>
            <div class="col-sm-10 toggle-btn">
            <?php if (!empty($use_margin)) { ?>
            <label><input type="radio" class="margin_select" name="use_margin" value="0" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="margin_select" name="use_margin" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
            <?php } else { ?>
            <label><input type="radio" class="margin_select" name="use_margin" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
            <label><input type="radio" class="margin_select" name="use_margin" value="1" /><span><?php echo $text_enabled; ?></span></label>
            <?php } ?>
            </div>                   
            </div>
          
          <div class="form-group margin_field" style="display:<?php if (!empty($use_margin)) { echo 'block'; } else { echo 'none'; } ?>">
            <label class="col-sm-2 control-label"><?php echo $text_margin; ?></label>
            <div class="col-sm-10">
              <input type="text" name="margin" value="<?php echo $margin; ?>" class="form-control" />
            </div>
          </div>
          
        </form>
      </div>
    </div>
  </div>

<script type="text/javascript">
$('.title_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('.title_field').css('display', 'block');
	} else {
		$('.title_field').css('display', 'none');
	}
});
$('.carousel_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('.carousel_field').css('display', 'block');
	} else {
		$('.carousel_field').css('display', 'none');
	}
});
$('.button_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('.button_field').css('display', 'block');
	} else {
		$('.button_field').css('display', 'none');
	}
});
$('.margin_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('.margin_field').css('display', 'block');
	} else {
		$('.margin_field').css('display', 'none');
	}
});
</script>
<style>
.toggle-btn {
	font-size:0;
}
.toggle-btn label {
	margin-bottom:0px;
}
.toggle-btn input[type="radio"] {
	display:none;
}
.toggle-btn span {
	font-size:12px;
	background:#f5f5f5;
	font-weight:normal;
	cursor:pointer;
	padding:8px 12px;
	display:inline-block;
	background:#fafafa;
   color:#666666;
    -webkit-box-shadow: inset 0 1px 4px rgba(41, 41, 41, 0.15);
    -moz-box-shadow: inset 0 1px 4px 0 rgba(41, 41, 41, 0.15);
    box-shadow: inset 0 1px 4px rgba(41, 41, 41, 0.15);
	-webkit-text-shadow:1px 1px 0 #ffffff;
	-moz-text-shadow:1px 1px 0 #ffffff;
	text-shadow:1px 1px 0 #ffffff;
}
.toggle-btn label:first-child span {
	border-radius:3px 0 0 3px
}
.toggle-btn label:last-child span {
	border-radius:0 3px 3px 0;
}
.toggle-btn input[type="radio"]:checked + span {
   background:#1e91cf;
   color:#ffffff;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    -moz-box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
	-webkit-text-shadow:1px 1px 0 rgba(0, 0, 0, 0.3);
	-moz-text-shadow:1px 1px 0 rgba(0, 0, 0, 0.3);
	text-shadow:1px 1px 0 rgba(0, 0, 0, 0.3);
}
.toggle-btn label:first-child input[type="radio"]:checked + span {
   background:#9f9f9f;
}
.title_field, .carousel_field, .button_field, .margin_field {
	background:#fafafa;
}
</style>
</div>
<?php echo $footer; ?>