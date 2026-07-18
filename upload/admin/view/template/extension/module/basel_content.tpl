<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"">
      <?php if ($save_and_stay) { ?>
      <a class="btn btn-success" onclick="$('#save').val('stay');$('#form_basel_content').submit();" data-toggle="tooltip" title="<?php echo $button_save_stay; ?>"><i class="fa fa-check"></i></a>
      <?php } ?>
    <button type="submit" form="form_basel_content" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
        </div>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    
    <div class="panel-wrapper">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_basel_content" class="form-horizontal">
    <input type="hidden" name="save" id="save" value="0">
    <div class="panel-header">
    <ul class="main-tabs list-unstyled">
    <li class="active"><a href="#tab-content" data-toggle="tab"><i class="fa fa-pencil"></i> <?php echo $text_tab_content; ?></a></li><!--
    --><li><a href="#tab-templates" data-toggle="tab"><i class="fa fa-download"></i> <?php echo $text_tab_template; ?></a></li>
    </ul>
    </div>
    
    <div class="tab-content">
    
    <div class="tab-pane active" id="tab-content">
    <div class="main-content">
    
    <div class="left-side">
    <legend><?php echo $text_module_settings; ?></legend>
    
    <div class="form-group">
    <label class="col-sm-3 control-label" for="input-name"><?php echo $entry_name; ?></label>
    <div class="col-sm-9">
    <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
    <?php if ($error_name) { ?>
    <div class="text-danger"><?php echo $error_name; ?></div>
    <?php } ?>
    </div>
    </div>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_status; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if ($status) { ?>
    <label><input type="radio" name="status" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="status" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" name="status" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="status" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="sample">
    <h4 class="sample-heading"><?php echo $text_layout_example; ?></h4>
    
    <div class="page"><small class="browser"><?php echo $text_page; ?></small>
    	<div class="block"><small><?php echo $text_block; ?></small>
        	<div class="content">
    			<div class="column left"><small><?php echo $text_column; ?></small></div>
                <div class="column right"><small><?php echo $text_column; ?></small></div>
                <small><?php echo $text_content; ?></small>
    		</div>
    	</div>
    </div>
    
    </div>
    
    <legend><?php echo $text_block_settings; ?></legend>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_use_block_title; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($b_setting['title'])) { ?>
    <label><input type="radio" class="title_select" name="b_setting[title]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="title_select" name="b_setting[title]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" class="title_select" name="b_setting[title]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="title_select" name="b_setting[title]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group title_field" style="display:<?php if (!empty($b_setting['title'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_block_pre_line; ?></label>
    <div class="col-sm-9">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
    <input type="text" name="b_setting[title_pl][<?php echo $language['language_id']; ?>]" value="<?php echo isset($b_setting['title_pl'][$language['language_id']]) ? $b_setting['title_pl'][$language['language_id']] : ''; ?>" class="form-control" />
    </div>
    <?php } ?>
    </div>
    </div>
    
    <div class="form-group title_field" style="display:<?php if (!empty($b_setting['title'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_block_title; ?></label>
    <div class="col-sm-9">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
    <input type="text" name="b_setting[title_m][<?php echo $language['language_id']; ?>]" value="<?php echo isset($b_setting['title_m'][$language['language_id']]) ? $b_setting['title_m'][$language['language_id']] : ''; ?>" class="form-control" />
    </div>
    <?php } ?>
    </div>
    </div>
    
    <div class="form-group title_field" style="display:<?php if (!empty($b_setting['title'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_block_sub_line; ?></label>
    <div class="col-sm-9">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
    <textarea type="text" name="b_setting[title_b][<?php echo $language['language_id']; ?>]" class="form-control"><?php echo isset($b_setting['title_b'][$language['language_id']]) ? $b_setting['title_b'][$language['language_id']] : ''; ?></textarea>
    </div>
    <?php } ?>
    </div>
    </div>
    
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_block_margin; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($b_setting['custom_m'])) { ?>
    <label><input type="radio" class="margin_select" name="b_setting[custom_m]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="margin_select" name="b_setting[custom_m]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" class="margin_select" name="b_setting[custom_m]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="margin_select" name="b_setting[custom_m]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group" id="custom_margin_field" style="display:<?php if (!empty($b_setting['custom_m'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_margin; ?></label>
    <div class="col-sm-9">
    <input type="text" name="b_setting[mt]" value="<?php echo isset($b_setting['mt']) ? $b_setting['mt'] : ''; ?>" placeholder="<?php echo $text_top; ?>" class="form-control inline" /> px&nbsp;&nbsp;&nbsp;
    <input type="text" name="b_setting[mr]" value="<?php echo isset($b_setting['mr']) ? $b_setting['mr'] : ''; ?>" placeholder="<?php echo $text_right; ?>" class="form-control inline" /> px&nbsp;&nbsp;&nbsp;
    <input type="text" name="b_setting[mb]" value="<?php echo isset($b_setting['mb']) ? $b_setting['mb'] : ''; ?>" placeholder="<?php echo $text_bottom; ?>" class="form-control inline" /> px&nbsp;&nbsp;&nbsp;
    <input type="text" name="b_setting[ml]" value="<?php echo isset($b_setting['ml']) ? $b_setting['ml'] : ''; ?>" placeholder="<?php echo $text_left; ?>" class="form-control inline" /> px&nbsp;&nbsp;&nbsp;
    </div>
    </div>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_full_width_background; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($b_setting['fw'])) { ?>
    <label><input type="radio" name="b_setting[fw]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="b_setting[fw]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" name="b_setting[fw]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="b_setting[fw]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_use_background_color; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($b_setting['block_bg'])) { ?>
    <label><input type="radio" class="bg_color_select" name="b_setting[block_bg]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="bg_color_select" name="b_setting[block_bg]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" class="bg_color_select" name="b_setting[block_bg]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="bg_color_select" name="b_setting[block_bg]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group" id="background_color_field" style="display:<?php if (!empty($b_setting['block_bg'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_background_color; ?></label>
    <div class="col-sm-9">
    <div class="input-group color_field">
    <span class="input-group-addon"><i></i></span><input type="text" name="b_setting[bg_color]" value="<?php echo isset($b_setting['bg_color']) ? $b_setting['bg_color'] : ''; ?>" class="form-control" />
    </div>
    </div>
    </div>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_use_background_image; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($b_setting['block_bgi'])) { ?>
    <label><input type="radio" class="bg_image_select" name="b_setting[block_bgi]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="bg_image_select" name="b_setting[block_bgi]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" class="bg_image_select" name="b_setting[block_bgi]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="bg_image_select" name="b_setting[block_bgi]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group background_image_field" style="display:<?php if (!empty($b_setting['block_bgi'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_background_image; ?></label>
    <div class="col-sm-9"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $image; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
    <input type="hidden" name="bg_image" value="<?php echo isset($bg_image) ? $bg_image : ''; ?>" id="input-image" />
    </div>
    </div>
    
    <div class="form-group background_image_field" id="background_color_field" style="display:<?php if (!empty($b_setting['block_bgi'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_background_parallax; ?></label>
    <div class="col-sm-9">
    <select name="b_setting[bg_par]" class="form-control">
    <?php if ($b_setting['bg_par'] == "0") { ?>
    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
    <?php } else { ?>
    <option value="0"><?php echo $text_disabled; ?></option>
    <?php } ?>
    <?php if (!empty($b_setting['bg_par'] == "1")) { ?>
    <option value="1" selected="selected">1</option>
    <?php } else { ?>
    <option value="1">1</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "2") { ?>
    <option value="2" selected="selected">2</option>
    <?php } else { ?>
    <option value="2">2</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "3") { ?>
    <option value="3" selected="selected">3</option>
    <?php } else { ?>
    <option value="3">3</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "4") { ?>
    <option value="4" selected="selected">4</option>
    <?php } else { ?>
    <option value="4">4</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "5") { ?>
    <option value="5" selected="selected">5</option>
    <?php } else { ?>
    <option value="5">5</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "6") { ?>
    <option value="6" selected="selected">6</option>
    <?php } else { ?>
    <option value="6">6</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "7") { ?>
    <option value="7" selected="selected">7</option>
    <?php } else { ?>
    <option value="7">7</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "8") { ?>
    <option value="8" selected="selected">8</option>
    <?php } else { ?>
    <option value="8">8</option>
    <?php } ?>
    <?php if ($b_setting['bg_par'] == "9") { ?>
    <option value="9" selected="selected">9</option>
    <?php } else { ?>
    <option value="9">9</option>
    <?php } ?>
    </select>
    </div>
    </div>
    
    <div class="form-group background_image_field" style="display:<?php if (!empty($b_setting['block_bgi'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_background_position; ?></label>
    <div class="col-sm-9">
    <select name="b_setting[bg_pos]" class="form-control">
    <?php if ($b_setting['bg_pos'] == 'left top') { ?>
    <option value="left top" selected="selected">left top</option>
    <?php } else { ?>
    <option value="left top">left top</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'left center') { ?>
    <option value="left center" selected="selected">left center</option>
    <?php } else { ?>
    <option value="left center">left center</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'left bottom') { ?>
    <option value="left bottom" selected="selected">left bottom</option>
    <?php } else { ?>
    <option value="left bottom">left bottom</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'right top') { ?>
    <option value="right top" selected="selected">right top</option>
    <?php } else { ?>
    <option value="right top">right top</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'right center') { ?>
    <option value="right center" selected="selected">right center</option>
    <?php } else { ?>
    <option value="right center">right center</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'right bottom') { ?>
    <option value="right bottom" selected="selected">right bottom</option>
    <?php } else { ?>
    <option value="right bottom">right bottom</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'center top') { ?>
    <option value="center top" selected="selected">center top</option>
    <?php } else { ?>
    <option value="center top">center top</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'center center') { ?>
    <option value="center center" selected="selected">center center</option>
    <?php } else { ?>
    <option value="center center">center center</option>
    <?php } ?>
    <?php if ($b_setting['bg_pos'] == 'center bottom') { ?>
    <option value="center bottom" selected="selected">center bottom</option>
    <?php } else { ?>
    <option value="center bottom">center bottom</option>
    <?php } ?>
    </select>
    </div>
    </div>
    
    <div class="form-group background_image_field" style="display:<?php if (!empty($b_setting['block_bgi'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_background_repeat; ?></label>
    <div class="col-sm-9">
    <select name="b_setting[bg_repeat]" class="form-control">
    <?php if ($b_setting['bg_repeat'] == 'no-repeat') { ?>
    <option value="no-repeat" selected="selected">no-repeat</option>
    <?php } else { ?>
    <option value="no-repeat">no-repeat</option>
    <?php } ?>
    <?php if ($b_setting['bg_repeat'] == 'repeat') { ?>
    <option value="repeat" selected="selected">repeat</option>
    <?php } else { ?>
    <option value="repeat">repeat</option>
    <?php } ?>
    <?php if ($b_setting['bg_repeat'] == 'repeat-x') { ?>
    <option value="repeat-x" selected="selected">repeat-x</option>
    <?php } else { ?>
    <option value="repeat-x">repeat-x</option>
    <?php } ?> 
    <?php if ($b_setting['bg_repeat'] == 'repeat-y') { ?>
    <option value="repeat-y" selected="selected">repeat-y</option>
    <?php } else { ?>
    <option value="repeat-y">repeat-y</option>
    <?php } ?> 
    </select>
    </div>
    </div>
    
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_use_background_video; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($b_setting['block_bgv'])) { ?>
    <label><input type="radio" class="bg_video_select" name="b_setting[block_bgv]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="bg_video_select" name="b_setting[block_bgv]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" class="bg_video_select" name="b_setting[block_bgv]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="bg_video_select" name="b_setting[block_bgv]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group" id="background_video_field" style="display:<?php if (!empty($b_setting['block_bgv'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_background_video; ?></label>
    <div class="col-sm-9">
    <input type="text" name="b_setting[bg_video]" value="<?php echo isset($b_setting['bg_video']) ? $b_setting['bg_video'] : ''; ?>" class="form-control" />
    </div>
    </div>
    
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_use_css; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($b_setting['block_css'])) { ?>
    <label><input type="radio" class="b_css_select" name="b_setting[block_css]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="b_css_select" name="b_setting[block_css]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" class="b_css_select" name="b_setting[block_css]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="b_css_select" name="b_setting[block_css]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group" id="block_css_field" style="display:<?php if (!empty($b_setting['block_css'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_css; ?></label>
    <div class="col-sm-9">
    <textarea name="b_setting[css]" class="form-control" style="height:105px;"><?php echo isset($b_setting['css']) ? $b_setting['css'] : ''; ?></textarea>
    </div>
    </div>
    
    
    
    <legend><?php echo $text_content_settings; ?></legend>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_full_width_content; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($c_setting['fw'])) { ?>
    <label><input type="radio" name="c_setting[fw]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="c_setting[fw]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" name="c_setting[fw]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="c_setting[fw]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_use_css; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($c_setting['block_css'])) { ?>
    <label><input type="radio" class="c_css_select" name="c_setting[block_css]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="c_css_select" name="c_setting[block_css]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" class="c_css_select" name="c_setting[block_css]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" class="c_css_select" name="c_setting[block_css]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group" id="content_css_field" style="display:<?php if (!empty($c_setting['block_css'])) { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_css; ?></label>
    <div class="col-sm-9">
    <textarea name="c_setting[css]" class="form-control" style="height:105px;"><?php echo isset($c_setting['css']) ? $c_setting['css'] : ''; ?></textarea>
    </div>
    </div>
    
    <legend><?php echo $text_columns_settings; ?></legend>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_zero_margin; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($c_setting['nm'])) { ?>
    <label><input type="radio" name="c_setting[nm]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="c_setting[nm]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" name="c_setting[nm]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="c_setting[nm]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_equal_height; ?></label>
    <div class="col-sm-9 toggle-btn">
    <?php if (!empty($c_setting['eh'])) { ?>
    <label><input type="radio" name="c_setting[eh]" value="0" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="c_setting[eh]" value="1" checked="checked" /><span><?php echo $text_enabled; ?></span></label>
    <?php } else { ?>
    <label><input type="radio" name="c_setting[eh]" value="0" checked="checked" /><span><?php echo $text_disabled; ?></span></label>
    <label><input type="radio" name="c_setting[eh]" value="1" /><span><?php echo $text_enabled; ?></span></label>
    <?php } ?>
    </div>                   
    </div>
    
    </div> <!-- .left-side -->
    
    
    <div class="right-side">
    <legend><?php echo $text_content_columns; ?></legend>
    
    
    
    <ul class="list-unstyled" id="column_tabs">
    <?php $column_row = 1; ?>
    <?php foreach ($columns as $column) { ?>
    <li><a href="#tab-column-<?php echo $column_row; ?>" data-toggle="tab"><?php echo $text_column; ?> <?php echo $column_row; ?> <i class="fa fa-minus-circle" onclick="$('a[href=\'#tab-column-<?php echo $column_row; ?>\']').parent().remove(); $('#tab-column-<?php echo $column_row; ?>').remove(); $('#column_tabs a:first').tab('show');"></i></a></li>
    <?php $column_row++; ?>
    <?php } ?>
    <li id="column-add" style="cursor:pointer"><a onclick="addColumn();"><i class="fa fa-plus-circle"></i> <?php echo $text_add_column; ?></a></li> 
    </ul>
    

    <div class="tab-content column-holder">
    
    <?php $column_row = 1; ?>
    
    <?php foreach ($columns as $column) { ?>
    <div class="tab-pane" id="tab-column-<?php echo $column_row; ?>">
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_column_width; ?></label>
    <div class="col-sm-9">
    <select name="columns[<?php echo $column_row; ?>][w]" class="form-control" onchange="set_width($(this).val(),'<?php echo $column_row; ?>')">
    <?php foreach ($column_widths as $key => $column_width) { ?>
    <option value="<?php echo $key; ?>"<?php echo ($column['w'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $column_width; ?></option>
    <?php } ?>
    </select>
    </div>
    </div> <!-- form-group ends -->
    
    
    <div class="form-group" id="custom-width-<?php echo $column_row; ?>" style="display:<?php if (($column['w']) == 'custom') { echo 'block'; } else { echo 'none'; } ?>">
    <label class="col-sm-3 control-label"><?php echo $text_width_per_device; ?></label>
    <div class="col-sm-9">
    <i class="fa fa-2x fa-mobile"></i>&nbsp;
    <select name="columns[<?php echo $column_row; ?>][w_sm]" class="form-control inline">
    <?php foreach ($sm_widths as $key => $sm_width) { ?>
    <option value="<?php echo $key; ?>"<?php echo ($column['w_sm'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $sm_width; ?></option>
    <?php } ?>
    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    <i class="fa fa-2x fa-tablet"></i>&nbsp;
    <select name="columns[<?php echo $column_row; ?>][w_md]" class="form-control inline">
    <?php foreach ($md_widths as $key => $md_width) { ?>
    <option value="<?php echo $key; ?>"<?php echo ($column['w_md'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $md_width; ?></option>
    <?php } ?>
    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    <i class="fa fa-2x fa-desktop"></i>&nbsp;
    <select name="columns[<?php echo $column_row; ?>][w_lg]" class="form-control inline">
    <?php foreach ($lg_widths as $key => $lg_width) { ?>
    <option value="<?php echo $key; ?>"<?php echo ($column['w_lg'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $lg_width; ?></option>
    <?php } ?>
    </select>      
    </div>
    </div> <!-- form-group ends -->
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_type; ?></label>
    <div class="col-sm-9">
    <select name="columns[<?php echo $column_row; ?>][type]" class="form-control" onchange="set_type($(this).val(),'<?php echo $column_row; ?>')" id="type-select-<?php echo $column_row; ?>">
    <?php if ($column['type'] == 'html') { ?>
    <option value="html" selected="selected"><?php echo $text_html; ?></option>
    <?php } else { ?>
    <option value="html"><?php echo $text_html; ?></option>
    <?php } ?> 
    <?php if ($column['type'] == 'img') { ?>
    <option value="img" selected="selected"><?php echo $text_banner; ?></option>
    <?php } else { ?>
    <option value="img"><?php echo $text_banner; ?></option>
    <?php } ?>
    <?php if ($column['type'] == 'tm') { ?>
    <option value="tm" selected="selected"><?php echo $text_testimonial; ?></option>
    <?php } else { ?>
    <option value="tm"><?php echo $text_testimonial; ?></option>
    <?php } ?>
    </select>
    </div>
    </div> <!-- form-group ends -->
    
    <div id="data-holder-<?php echo $column_row; ?>">
    
    <?php if ($column['type'] == 'html') { ?>
    <legend><?php echo $text_title_html; ?></legend>
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_position; ?></label>
    <div class="col-sm-9"> 
    <select name="columns[<?php echo $column_row; ?>][data7]" class="form-control">
    <?php foreach ($overlay_positions as $key => $overlay_position) { ?>
    <option value="<?php echo $key; ?>"<?php echo ($column['data7'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $overlay_position; ?></option>
    <?php } ?>
    </select>
    </div>
    </div> <!-- form-group ends -->
    
    <div class="tab-pane">
    <div class="col-sm-offset-3 language-tabs-holder">
    <ul class="nav nav-tabs" id="tabs-<?php echo $column_row; ?>">
    <?php foreach ($languages as $language) { ?>
    <li><a href="#tab-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
    <?php } ?>
    </ul>
    </div>
    <div class="tab-content">
    <?php foreach ($languages as $language) { ?>
    <div class="tab-pane" id="tab-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>">
    <div class="form-group">
      <label class="col-sm-3 control-label"><?php echo $text_html_content; ?><br />
      <a id="enable_editor-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>-1" class="editor-link-<?php echo $column_row; ?>-1" onclick="enable_editor('<?php echo $column_row; ?>', '<?php echo $language['language_id']; ?>', '1')"><small><?php echo $text_enable_editor; ?></small></a><br />
      <a class="icon_list"><small><?php echo $text_view_icons; ?></small></a><br />
      <a class="shortcode_list"><small><?php echo $text_view_shortcodes; ?></small></a></label>
      <div class="col-sm-9">
        <textarea name="columns[<?php echo $column_row; ?>][data1][<?php echo $language['language_id']; ?>]" class="form-control content-block template-reciever-<?php echo $column_row; ?>-1" id="textarea-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>-1"><?php echo isset($column['data1'][$language['language_id']]) ? $column['data1'][$language['language_id']] : ''; ?></textarea>
      </div>
    </div>
    </div>
    <?php } ?>
    </div>
    </div>
    <?php } ?>
    
    
    <?php if ($column['type'] == 'tm') { ?>
    <legend><?php echo $text_title_testimonial; ?></legend>
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_limit; ?></label>
    <div class="col-sm-9"> 
    <input type="text" class="form-control" name="columns[<?php echo $column_row; ?>][data1]" value="<?php echo isset($column['data1']) ? $column['data1'] : '3'; ?>" />
    </div>
    </div> <!-- form-group ends -->
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_tm_columns; ?></label>
    <div class="col-sm-9"> 
    <select name="columns[<?php echo $column_row; ?>][data7]" class="form-control">
    <?php if ($column['data7'] == '1') { ?>
    <option value="1" selected="selected">1</option>
    <?php } else { ?>
    <option value="1">1</option>
    <?php } ?> 
    <?php if ($column['data7'] == '2') { ?>
    <option value="2" selected="selected">2</option>
    <?php } else { ?>
    <option value="2">2</option>
    <?php } ?>
    <?php if ($column['data7'] == '3') { ?>
    <option value="3" selected="selected">3</option>
    <?php } else { ?>
    <option value="3">3</option>
    <?php } ?>
    </select>
    </div>
    </div> <!-- form-group ends -->
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_tm_style; ?></label>
    <div class="col-sm-9"> 
    <select name="columns[<?php echo $column_row; ?>][data8]" class="form-control">
    <?php if ($column['data8'] == 'plain') { ?>
    <option value="plain" selected="selected"><?php echo $text_tm_style_plain; ?></option>
    <?php } else { ?>
    <option value="plain"><?php echo $text_tm_style_plain; ?></option>
    <?php } ?>
    <?php if ($column['data8'] == 'light plain') { ?>
    <option value="light plain" selected="selected"><?php echo $text_tm_style_plain_light; ?></option>
    <?php } else { ?>
    <option value="light plain"><?php echo $text_tm_style_plain_light; ?></option>
    <?php } ?> 
    <?php if ($column['data8'] == 'block') { ?>
    <option value="block" selected="selected"><?php echo $text_tm_style_block; ?></option>
    <?php } else { ?>
    <option value="block"><?php echo $text_tm_style_block; ?></option>
    <?php } ?> 
    </select>
    </div>
    </div> <!-- form-group ends -->
    
    <?php } ?>
    
    
    
    <?php if ($column['type'] == 'img') { ?>
    <legend><?php echo $text_title_banner; ?></legend>

    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_banner; ?></label>
    <div class="col-sm-9"> 
    <a id="thumb-image<?php echo $column_row; ?>" data-toggle="image" class="img-thumbnail">
    <img src="<?php echo !empty($column['image']) ? $column['image'] : $placeholder; ?>" data-placeholder="<?php echo $placeholder; ?>" /></a>
    <input type="hidden" name="columns[<?php echo $column_row; ?>][data2]" value="<?php echo isset($column['data2']) ? $column['data2'] : ''; ?>" id="input-image<?php echo $column_row; ?>" />
    </div>
    </div> <!-- form-group ends -->
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_link_target; ?></label>
    <div class="col-sm-9"> 
    <input type="text" class="form-control" name="columns[<?php echo $column_row; ?>][data5]" value="<?php echo isset($column['data5']) ? $column['data5'] : ''; ?>" />
    </div>
    </div> <!-- form-group ends -->
    
    <legend class="sub"><?php echo $text_banner_overlay; ?></legend>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_position_banner; ?></label>
    <div class="col-sm-9"> 
    <select name="columns[<?php echo $column_row; ?>][data7]" class="form-control">
    <?php foreach ($overlay_positions as $key => $overlay_position) { ?>
    <option value="<?php echo $key; ?>"<?php echo ($column['data7'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $overlay_position; ?></option>
    <?php } ?>
    </select>
    </div>
    </div> <!-- form-group ends -->
    
    <div class="tab-pane">
    <div class="col-sm-offset-3 language-tabs-holder">
    <ul class="nav nav-tabs" id="tabs-<?php echo $column_row; ?>">
    <?php foreach ($languages as $language) { ?>
    <li><a href="#tab-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
    <?php } ?>
    </ul>
    </div>
    <div class="tab-content">
    <?php foreach ($languages as $language) { ?>
    <div class="tab-pane" id="tab-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>">
    <div class="form-group">
      <label class="col-sm-3 control-label"><?php echo $text_banner_overlay; ?><br /><a class="overlay_list"><small><?php echo $text_view_overlays; ?></small></a></label>
      <div class="col-sm-9">
        <textarea name="columns[<?php echo $column_row; ?>][data1][<?php echo $language['language_id']; ?>]" class="form-control content-block template-reciever-<?php echo $column_row; ?>-1"><?php echo isset($column['data1'][$language['language_id']]) ? $column['data1'][$language['language_id']] : ''; ?></textarea>
        
      </div>
    </div>
    </div>
    <?php } ?>
    
    </div>
    </div>
    
    <a class="btn btn-primary btn-block banner-btn-<?php echo $column_row; ?>" style="margin:20px 20px 10px 20px;display:<?php if (!empty($column['data4'])) { echo "none"; } else { echo "block"; } ?>" onclick="add_second_banner('<?php echo $column_row; ?>');"><?php echo $text_btn_add_banner; ?></a>
  
    
    <div class="banner2-holder-<?php echo $column_row; ?>">
    <?php if (!empty($column['data4'])) { ?>
    <legend><?php echo $text_title_banner2; ?> <a class="remove_second_banner" onclick="remove_second_banner('<?php echo $column_row; ?>');">[<?php echo $text_remove_banner; ?>]</a></legend>
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_banner; ?></label>
    <div class="col-sm-9"> 
    <a id="thumb-image2<?php echo $column_row; ?>" data-toggle="image" class="img-thumbnail">
    <img src="<?php echo !empty($column['image2']) ? $column['image2'] : $placeholder; ?>" data-placeholder="<?php echo $placeholder; ?>" /></a>
    <input type="hidden" name="columns[<?php echo $column_row; ?>][data4]" value="<?php echo isset($column['data4']) ? $column['data4'] : ''; ?>" id="input-image2<?php echo $column_row; ?>" />
    </div>
    </div> <!-- form-group ends -->
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_link_target; ?></label>
    <div class="col-sm-9"> 
    <input type="text" class="form-control" name="columns[<?php echo $column_row; ?>][data6]" value="<?php echo isset($column['data6']) ? $column['data6'] : ''; ?>" />
    </div>
    </div> <!-- form-group ends -->
    
    <legend class="sub"><?php echo $text_banner_overlay; ?></legend>
    
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_position_banner; ?></label>
    <div class="col-sm-9"> 
    <select name="columns[<?php echo $column_row; ?>][data8]" class="form-control">
    <?php foreach ($overlay_positions as $key => $overlay_position) { ?>
    <option value="<?php echo $key; ?>"<?php echo ($column['data8'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $overlay_position; ?></option>
    <?php } ?>
    </select>
    </div>
    </div> <!-- form-group ends -->
    
    <div class="tab-pane">
    <div class="col-sm-offset-3 language-tabs-holder">
    <ul class="nav nav-tabs" id="tabs2-<?php echo $column_row; ?>">
    <?php foreach ($languages as $language) { ?>
    <li><a href="#tab2-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
    <?php } ?>
    </ul>
    </div>
    <div class="tab-content">
    <?php foreach ($languages as $language) { ?>
    <div class="tab-pane" id="tab2-<?php echo $column_row; ?>-<?php echo $language['language_id']; ?>">
    <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $text_banner_overlay; ?><br /><a class="overlay_list"><small><?php echo $text_view_overlays; ?></small></a></label>
    <div class="col-sm-9">
    <textarea name="columns[<?php echo $column_row; ?>][data3][<?php echo $language['language_id']; ?>]" class="form-control content-block template-reciever-<?php echo $column_row; ?>-2"><?php echo isset($column['data3'][$language['language_id']]) ? $column['data3'][$language['language_id']] : ''; ?></textarea>
    
    </div>
    </div>
    </div>
    <?php } ?>
    </div>
    </div>
    <?php } ?>
    </div>
    <?php } ?>
    
    

    </div>
    </div> <!-- tab-pane ends -->
    <?php $column_row++; ?>
    <?php } ?> <!-- foreach columns ends -->
    </div> <!-- columns holder ends -->
    </div> <!-- .right-side -->
    </div> <!-- .main-content -->
    </div> <!-- #tab-content -->
    
    <div class="tab-pane" id="tab-templates">
    <div class="template-content">
    
    <table class="table table-bordered table-hover">
    
    <thead>
    <td style="width:100%"><?php echo $text_template; ?></td>
    <td class="text-right"><?php echo $text_action; ?></td>
    </thead>
    
    <?php foreach ($templates as $template) { ?>
    <tr>
    <td class="name" style="width:100%"><?php echo $template['name']; ?></td>
    <td style="white-space:nowrap;">
    <a class="btn btn-primary" data-toggle="tooltip" title="<?php echo $text_preview; ?>" onclick="template_preview(<?php echo $template['template_id']; ?>, '<?php echo $template['name']; ?>');"><i class="fa fa-eye"></i></a>
    <a class="btn btn-success" data-toggle="tooltip" title="<?php echo $text_import; ?>" onclick="confirm('<?php echo $text_confirm; ?>') ? location.href='<?php echo $template['import_url']; ?>' : false;"><i class="fa fa-download"></i></a>
    </td>
    </tr>
    <?php } ?>
 
    </table>
    
    </div> <!-- .template-content -->
    </div> <!-- #tab-templates -->
    
    </div>

    </form>
    </div> <!-- .panel-wrapper -->

   </div>



<script type="text/javascript"><!--
// Add Column
var column_row = <?php echo $column_row; ?>;
function addColumn() {	
html  = '<div class="tab-pane" id="tab-column-' + column_row + '">';
html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_column_width; ?></label>';
html += '<div class="col-sm-9">';
html += '<select name="columns[' + column_row + '][w]" onchange="set_width($(this).val(),' + column_row + ');" class="form-control">';
<?php foreach ($column_widths as $key => $column_width) { ?>
html += '<option value="<?php echo $key; ?>"><?php echo $column_width; ?></option>';
<?php } ?>
html += '</select>';
html += '</div>';
html += '</div>';

html += '<div class="form-group" id="custom-width-' + column_row + '" style="display:none;">';
html += '<label class="col-sm-3 control-label"><?php echo $text_width_per_device; ?></label>';
html += '<div class="col-sm-9">';
html += '<i class="fa fa-2x fa-mobile"></i>&nbsp;';
html += '<select name="columns[' + column_row + '][w_sm]" class="form-control inline">';
<?php foreach ($sm_widths as $key => $sm_width) { ?>
html += '<option value="<?php echo $key; ?>"><?php echo $sm_width; ?></option>';
<?php } ?>
html += '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
html += '<i class="fa fa-2x fa-tablet"></i>&nbsp;';
html += '<select name="columns[' + column_row + '][w_md]" class="form-control inline">';
<?php foreach ($md_widths as $key => $md_width) { ?>
html += '<option value="<?php echo $key; ?>"><?php echo $md_width; ?></option>';
<?php } ?>
html += '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
html += '<i class="fa fa-2x fa-desktop"></i>&nbsp;';
html += '<select name="columns[' + column_row + '][w_lg]" class="form-control inline">';
<?php foreach ($lg_widths as $key => $lg_width) { ?>
html += '<option value="<?php echo $key; ?>"><?php echo $lg_width; ?></option>';
<?php } ?>
html += '</select>';
html += '</div>';
html += '</div>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_type; ?></label>';
html += '<div class="col-sm-9">';
html += '<select name="columns[' + column_row + '][type]" onchange="set_type($(this).val(),' + column_row + ');" id="type-select-' + column_row + '" class="form-control">';
html += '<option><?php echo $text_select_type; ?></option>';
html += '<option value="html"><?php echo $text_html; ?></option>';
html += '<option value="img"><?php echo $text_banner; ?></option>';
html += '<option value="tm"><?php echo $text_testimonial; ?></option>';
html += '</select>';
html += '</div>';
html += '</div>';

html += '<div id="data-holder-' + column_row + '">';
html += '</div>';

html += '</div>';        

$('.tab-content.column-holder').append(html);
	
$('#column-add').before('<li><a href="#tab-column-' + column_row + '" data-toggle="tab"> <?php echo $text_column; ?> ' + column_row + ' <i class="fa fa-minus-circle" onclick="$(\'a[href=\\\'#tab-column-' + column_row + '\\\']\').parent().remove(); $(\'#tab-column-' + column_row + '\').remove(); $(\'#column_tabs a:first\').tab(\'show\');"></i></a></li>');

$('#column_tabs a[href=\'#tab-column-' + column_row + '\']').tab('show');
// Fix Bootstrap Tooltip
$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
column_row++;
}
//--></script>

<script type="text/javascript"><!--
// Set content type
var set_type = function(type, column_row) {

if( type == 'html' ) {
html = '<legend><?php echo $text_title_html; ?></legend>';
html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_overlay_position; ?></label>';
html += '<div class="col-sm-9">';
html += '<select name="columns[' + column_row + '][data7]" class="form-control">';
<?php foreach ($overlay_positions as $key => $overlay_position) { ?>
html += '<option value="<?php echo $key; ?>"><?php echo $overlay_position; ?></option>';
<?php } ?>
html += '</select>';
html += '</div>';
html += '</div>';
html += '<div class="tab-pane">';
html += '<div class="col-sm-offset-3 language-tabs-holder">';
html += '<ul class="nav nav-tabs" id="tabs-' + column_row + '">';
<?php foreach ($languages as $language) { ?>
html += '<li><a href="#tab-' + column_row + '<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>';
<?php } ?>
html += '</ul>';
html += '</div>';
html += '<div class="tab-content">';
<?php foreach ($languages as $language) { ?>
html += '<div class="tab-pane" id="tab-' + column_row + '<?php echo $language['language_id']; ?>">';
html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_html_content; ?><br /><a id="enable_editor-' + column_row + '-<?php echo $language['language_id']; ?>-1"  class="editor-link-' + column_row + '-1" onclick="enable_editor(' + column_row + ', <?php echo $language['language_id']; ?>,1);"><small><?php echo $text_enable_editor; ?></small></a><br /><a class="icon_list"><small><?php echo $text_view_icons; ?></small></a><br><a class="shortcode_list"><small><?php echo $text_view_shortcodes; ?></small></a></label>';
html += '<div class="col-sm-9">';
html += '<textarea name="columns[' + column_row + '][data1][<?php echo $language['language_id']; ?>]" id="textarea-' + column_row + '-<?php echo $language['language_id']; ?>-1" class="form-control content-block template-reciever-' + column_row + '-1"></textarea>';
html += '</div>';
html += '</div>';
html += '</div>';
<?php } ?>
html += '</div>';
html += '</div>';
}

if( type == 'img' ) {
html = '<legend><?php echo $text_title_banner; ?></legend>';
html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_banner; ?></label>';
html += '<div class="col-sm-9">';
html += '<a href="" id="thumb-image' + column_row + '" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" data-placeholder="<?php echo $placeholder; ?>" /></a>';
html += '<input type="hidden" name="columns[' + column_row + '][data2]" value="" id="input-image' + column_row + '" />';
html += '</div>';
html += '</div>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_link_target; ?></label>';
html += '<div class="col-sm-9">';
html += '<input type="text" class="form-control" name="columns[' + column_row + '][data5]"/>';
html += '</div>';
html += '</div>';

html += '<legend class="sub"><?php echo $text_banner_overlay; ?></legend>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_position_banner; ?></label>';
html += '<div class="col-sm-9">';
html += '<select name="columns[' + column_row + '][data7]" class="form-control">';
<?php foreach ($overlay_positions as $key => $overlay_position) { ?>
html += '<option value="<?php echo $key; ?>"><?php echo $overlay_position; ?></option>';
<?php } ?>
html += '</select>';
html += '</div>';
html += '</div>';
			
html += '<div class="tab-pane">';
html += '<div class="col-sm-offset-3 language-tabs-holder">';
html += '<ul class="nav nav-tabs" id="tabs-' + column_row + '">';
<?php foreach ($languages as $language) { ?>
html += '<li><a href="#tab-' + column_row + '<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>';
<?php } ?>
html += '</ul>';
html += '</div>';
html += '<div class="tab-content">';
<?php foreach ($languages as $language) { ?>
html += '<div class="tab-pane" id="tab-' + column_row + '<?php echo $language['language_id']; ?>">';
html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_banner_overlay; ?><br /><a class="overlay_list"><small><?php echo $text_view_overlays; ?></small></a></label>';
html += '<div class="col-sm-9">';
html += '<textarea name="columns[' + column_row + '][data1][<?php echo $language['language_id']; ?>]" id="textarea-' + column_row + '-<?php echo $language['language_id']; ?>-1" class="form-control content-block template-reciever-' + column_row + '-1"></textarea>';
html += '</div>';
html += '</div>';
html += '</div>';
<?php } ?>
html += '</div>';
html += '</div>';

html += '<a style="margin:20px 20px 10px 20px" class="btn btn-primary btn-block banner-btn-' + column_row + '" onclick="add_second_banner(' + column_row + ');"><?php echo $text_btn_add_banner; ?></a>';

html += '<div class="banner2-holder-' + column_row + '"';
html += '</div>';
}

if( type == 'tm' ) {
html = '<legend><?php echo $text_title_testimonial; ?></legend>';
html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_limit; ?></label>';
html += '<div class="col-sm-9">';
html += '<input type="text" class="form-control" name="columns[' + column_row + '][data1]" value="3"/>';
html += '</div>';
html += '</div>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_tm_columns; ?></label>';
html += '<div class="col-sm-9">';
html += '<select name="columns[' + column_row + '][data7]" class="form-control">';
html += '<option value="1">1</option>';
html += '<option value="2">2</option>';
html += '<option value="3">3</option>';
html += '</select>';
html += '</div>';
html += '</div>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_tm_style; ?></label>';
html += '<div class="col-sm-9">';
html += '<select name="columns[' + column_row + '][data8]" class="form-control">';
html += '<option value="plain"><?php echo $text_tm_style_plain; ?></option>';
html += '<option value="light plain"><?php echo $text_tm_style_plain_light; ?></option>';
html += '<option value="block"><?php echo $text_tm_style_block; ?></option>';
html += '</select>';
html += '</div>';
html += '</div>';
}

$('#data-holder-' + column_row + '').html(html);
$('#tabs-' + column_row + ' a:first').tab('show');
// Fix Bootstrap Tooltip
$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
}
//--></script>
<script type="text/javascript"><!--
// Set content type
var set_template = function(template, column_row, number) {
		<?php foreach ($languages as $language) { ?>
		$('#textarea-' + column_row + '-<?php echo $language['language_id']; ?>-' + number + '').summernote('destroy');
		<?php } ?>
		$('.editor-link-' + column_row + '-' + number + '').text('<?php echo $text_enable_editor; ?>').removeClass('active');
		$('.template-reciever-' + column_row + '-' + number + '').val(template);
	
}
// Set column width
var set_width = function(width,column_row) {
	if (width == 'custom') {
		$('#custom-width-' + column_row + '').css('display', 'block');
	} else {
		$('#custom-width-' + column_row + '').css('display', 'none');
	}
}
var enable_editor = function(column_row, lang_id, number) {
	if ( $('#enable_editor-' + column_row + '-' + lang_id + '-' + number + '').hasClass('active') ) {
		$('#enable_editor-' + column_row + '-' + lang_id + '-' + number + '').text('<?php echo $text_enable_editor; ?>').removeClass('active');
		$('#textarea-' + column_row + '-' + lang_id + '-' + number + '').summernote('destroy');
		
	} else {
		$('#enable_editor-' + column_row + '-' + lang_id + '-' + number + '').text('<?php echo $text_disable_editor; ?>').addClass('active');

		$('#textarea-' + column_row + '-' + lang_id + '-' + number + '').summernote({
			disableDragAndDrop: true,
			styleWithSpan: false,
			height: 260,
			fontSizes: ['12', '13', '14', '16', '18', '20', '22', '24', '26','28','30','32', '34', '48' , '64', '42'],
			toolbar: [
				['style', ['style']],
				['font', [ "underline","italic", "bold", "clear"]],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['paragraph']],
				['insert', ['link', 'image']],
				['view', ['fullscreen', 'codeview']]
			],
			buttons: {
    			image: function() {
					var ui = $.summernote.ui;
					// create button
					var button = ui.button({
						contents: '<i class="fa fa-image" />',
						tooltip: $.summernote.lang[$.summernote.options.lang].image.image,
						click: function () {
							$('#modal-image').remove();
							$.ajax({
								url: 'index.php?route=common/filemanager&token=' + getURLVar('token'),
								dataType: 'html',
								beforeSend: function() {
									$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
									$('#button-image').prop('disabled', true);
								},
								complete: function() {
									$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
									$('#button-image').prop('disabled', false);
								},
								success: function(html) {
									$('body').append('<div id="modal-image" class="modal">' + html + '</div>');
									
									$('#modal-image').modal('show');
									
									$('#modal-image').delegate('a.thumbnail', 'click', function(e) {
										e.preventDefault();
										
										$('#textarea-' + column_row + '-' + lang_id + '-' + number + '').summernote('insertImage', $(this).attr('href'));
																	
										$('#modal-image').modal('hide');
									});
								}});}});
				return button.render();
		}}});
	}
}
var add_second_banner = function(column_row) {
html = 	'<legend><?php echo $text_title_banner2; ?>'; 
html += '<a class="remove_second_banner" onclick="remove_second_banner(' + column_row + ');">[<?php echo $text_remove_banner; ?>]</a>';
html += '</legend>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_banner; ?></label>';
html += '<div class="col-sm-9">';
html += '<a href="" id="thumb-image2' + column_row + '" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" data-placeholder="<?php echo $placeholder; ?>" /></a>';
html += '<input type="hidden" name="columns[' + column_row + '][data4]" value="" id="input-image2' + column_row + '" />';
html += '</div>';
html += '</div>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_link_target; ?></label>';
html += '<div class="col-sm-9">';
html += '<input type="text" class="form-control" name="columns[' + column_row + '][data6]"/>';
html += '</div>';
html += '</div>';

html += '<legend class="sub"><?php echo $text_banner_overlay; ?></legend>';

html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_position_banner; ?></label>';
html += '<div class="col-sm-9">';
html += '<select name="columns[' + column_row + '][data8]" class="form-control">';
<?php foreach ($overlay_positions as $key => $overlay_position) { ?>
html += '<option value="<?php echo $key; ?>"><?php echo $overlay_position; ?></option>';
<?php } ?>
html += '</select>';
html += '</div>';
html += '</div>';

html += '<div class="tab-pane">';
html += '<div class="col-sm-offset-3 language-tabs-holder">';
html += '<ul class="nav nav-tabs" id="tabs2-' + column_row + '">';
<?php foreach ($languages as $language) { ?>
html += '<li><a href="#tab2-' + column_row + '<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>';
<?php } ?>
html += '</ul>';
html += '</div>';
html += '<div class="tab-content">';
<?php foreach ($languages as $language) { ?>
html += '<div class="tab-pane" id="tab2-' + column_row + '<?php echo $language['language_id']; ?>">';
html += '<div class="form-group">';
html += '<label class="col-sm-3 control-label"><?php echo $text_banner_overlay; ?><br /><a class="overlay_list"><small><?php echo $text_view_overlays; ?></small></a></label>';
html += '<div class="col-sm-9">';
html += '<textarea name="columns[' + column_row + '][data3][<?php echo $language['language_id']; ?>]" id="textarea-' + column_row + '-<?php echo $language['language_id']; ?>-2" class="form-control content-block template-reciever-' + column_row + '-2"></textarea>';
html += '</div>';
html += '</div>';
html += '</div>';
<?php } ?>
html += '</div>';
html += '</div>';

$('.banner2-holder-' + column_row + '').html(html);
$('#tabs2-' + column_row + ' a:first').tab('show');
$('.banner-btn-' + column_row + '').css('display', 'none');
// Fix Bootstrap Tooltip
$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
}
var remove_second_banner = function(column_row) {
$('.banner2-holder-' + column_row + '').html('');
$('.banner-btn-' + column_row + '').css('display', 'block');
}
//--></script>
<script type="text/javascript">
/* HTML templates popup */
var template_preview = function(template, name) {
	$.ajax({
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-' + template + '" class="modal content">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title"><?php echo $text_preview_template; ?>: ' + name + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body"><img src="view/javascript/basel/content_templates/' + template + '/preview.png"</div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';
			$('body').append(html);
			$('#modal-' + template + '').modal('show');
		}
	});
}
$(document).delegate('.icon_list', 'click', function(e) {
	e.preventDefault();
	$.ajax({
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-icons" class="modal content">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title"><?php echo $text_icons_list; ?></h4>';
			html += '      </div>';
			html += '      <div class="modal-body" style="padding:30px 0;"><iframe src="view/javascript/basel/icons_list/icon_list.html" width="1240" height="560" frameborder="0" allowtransparency="true"></iframe></div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';
			$('body').append(html);
			$('#modal-icons').modal('show');
		}
	});
});
$(document).delegate('.shortcode_list', 'click', function(e) {
	e.preventDefault();
	$.ajax({
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-shortcode" class="modal content">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">Shortcodes</h4>';
			html += '      </div>';
			html += '      <div class="modal-body"><iframe src="view/javascript/basel/shortcode_list/shortcode_list.html" width="1240" height="560" frameborder="0" allowtransparency="true"></iframe></div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';
			$('body').append(html);
			$('#modal-shortcode').modal('show');
		}
	});
});
$(document).delegate('.overlay_list', 'click', function(e) {
	e.preventDefault();
	$.ajax({
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-overlay" class="modal content">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">Banner overlay examples</h4>';
			html += '      </div>';
			html += '      <div class="modal-body"><iframe src="view/javascript/basel/overlay_list/overlay_list.html" width="1240" height="560" frameborder="0" allowtransparency="true"></iframe></div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';
			$('body').append(html);
			$('#modal-overlay').modal('show');
		}
	});
});
//--></script>
<script type="text/javascript"><!--
// Import ready made templates
$('.margin_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('#custom_margin_field').css('display', 'block');
	} else {
		$('#custom_margin_field').css('display', 'none');
	}
});
$('.bg_color_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('#background_color_field').css('display', 'block');
	} else {
		$('#background_color_field').css('display', 'none');
	}
});
$('.color_field').colorpicker({
sliders: {
saturation: {
	maxLeft: 150,
	maxTop: 150},
	hue: { maxTop: 150},
	alpha: { maxTop: 150}
	}
});

$('.bg_image_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('.background_image_field').css('display', 'block');
	} else {
		$('.background_image_field').css('display', 'none');
	}
});
$('.bg_video_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('#background_video_field').css('display', 'block');
	} else {
		$('#background_video_field').css('display', 'none');
	}
});
$('.title_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('.title_field').css('display', 'block');
	} else {
		$('.title_field').css('display', 'none');
	}
});
$('.c_css_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('#content_css_field').css('display', 'block');
	} else {
		$('#content_css_field').css('display', 'none');
	}
});
$('.b_css_select').on('change', function() {
  	if ($(this).val() == '1') {
		$('#block_css_field').css('display', 'block');
	} else {
		$('#block_css_field').css('display', 'none');
	}
});
//--></script>
<script type="text/javascript"><!--
$('#column_tabs li:first-child a').tab('show');
var column_row = 1;
<?php foreach ($columns as $column) { ?>
$('#tabs-' + column_row + ' li:first-child a').tab('show');
$('#tabs2-' + column_row + ' li:first-child a').tab('show');
column_row++;
<?php } ?>
//--></script> 
</div>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php echo $footer; ?>