<?php echo $header; ?>
<?php if ($contrast_color) { ?>
<style>
.slide-preview-wrapper .btn-contrast, 
.slide-preview-wrapper a.btn-contrast, 
.slide-preview-wrapper .btn-contrast-outline:hover {
	background-color:<?php echo $contrast_color; ?>;
}
.slide-preview-wrapper .btn-contrast-outline {
	border-color:<?php echo $contrast_color; ?>;
	color:<?php echo $contrast_color; ?>;
}
</style>
<?php } ?>
<?php echo $column_left; ?>
<div id="content" class="<?php if ($fullwidth) echo 'fullwidth'; ?>">
 <div class="page-header">
  <div class="container-fluid">
  <div class="pull-right">
  <?php if ($has_module_id) { ?>
  <a class="btn btn-success" onclick="$('#save').val('stay');$('#form-layerslider').submit();"><?php echo $button_save_stay; ?></a>
  <?php } ?>
  <button type="submit" form="form-layerslider" class="btn btn-primary"><?php echo $button_save; ?></button>
  <a href="<?php echo $cancel; ?>" class="btn btn-default"><?php echo $button_cancel; ?></a>
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
    
    <!-- Notification messages -->
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
    
     <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-layerslider" class="">
        <input type="hidden" name="save" id="save" value="0">
          
        <!-- Tab Headings -->
        <ul class="nav nav-tabs master" role="tablist">
        <li role="presentation" <?php if (!$has_module_id) { ?>class="active"<?php } ?>><a href="#home" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> <?php echo $text_general_settings; ?></a></li>
        <li role="presentation"><a href="#fonts" role="tab" data-toggle="tab"><i class="fa fa-font"></i> <?php echo $text_google_fonts; ?></a></li>
        <li role="presentation" <?php if ($has_module_id) { ?>class="active"<?php } ?>><a href="#slides" role="tab" data-toggle="tab"><i class="fa fa-bars"></i> <?php echo $text_slides; ?></a></li>
        </ul>
        
        <!-- Tab content -->
        <div class="tab-content">
        <!-- General Settings -->
        <div role="tabpanel" class="tab-pane <?php if (!$has_module_id) { echo 'active'; } ?>" id="home">
        
        <div class="form-horizontal">
        
        <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10 pull-left-2">
              <h3 class="first"><?php echo $h3_module_settings; ?></h3>
            </div>
          </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
                <input name="name" id="input-name" class="form-control" value="<?php echo $name; ?>" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $text_preview_language; ?></label>
            <div class="col-sm-10">
              <select name="lang" id="input-lang" class="form-control">
               <?php foreach ($languages as $language) { ?>
                <?php if ($lang == $language['language_id']) { ?>
				<option value="<?php echo $language['language_id']; ?>" selected="selected"><?php echo $language['name']; ?></option>
                <?php } else { ?>
				<option value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
                <?php } ?>
               <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10 pull-left-2">
              <h3><?php echo $h3_slideshow_sizing; ?></h3>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_width; ?></label>
            <div class="col-sm-10">
                <input name="width" id="input-width" class="form-control" value="<?php echo $width; ?>" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_height; ?></label>
            <div class="col-sm-10">
                <input name="height" id="input-height" class="form-control" value="<?php echo $height; ?>" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_minheight; ?></label>
            <div class="col-sm-10">
                <input name="minheight" id="input-minheight" class="form-control" value="<?php echo $minheight; ?>" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_fullwidth; ?></label>
            <div class="col-sm-10">
                <select name="fullwidth" id="input-fullwidth" class="form-control">
                <?php if ($fullwidth) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-margin_bottom"><?php echo $entry_margin_bottom; ?></label>
            <div class="col-sm-10">
                <input name="margin_bottom" id="input-margin_bottom" class="form-control" value="<?php echo $margin_bottom; ?>" />
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10 pull-left-2">
              <h3><?php echo $h3_slide_navigation; ?></h3>
            </div>
          </div>
          
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $text_slide_transition; ?></label>
            <div class="col-sm-10">
                <select name="slide_transition" class="form-control">
                
                <?php if ($slide_transition == 'basic') { ?>
				<option value="basic" selected="selected">Basic</option>
                <?php } else { ?>
				<option value="basic">Basic</option>
                <?php } ?>
                
                <?php if ($slide_transition == 'fade') { ?>
				<option value="fade" selected="selected">Fade</option>
                <?php } else { ?>
				<option value="fade">Fade</option>
                <?php } ?>
                
                <?php if ($slide_transition == 'wave') { ?>
				<option value="wave" selected="selected">Wave</option>
                <?php } else { ?>
				<option value="wave">Wave</option>
                <?php } ?>
                
                <?php if ($slide_transition == 'flow') { ?>
				<option value="flow" selected="selected">Flow</option>
                <?php } else { ?>
				<option value="flow">Flow</option>
                <?php } ?>
                
                <?php if ($slide_transition == 'stack') { ?>
				<option value="stack" selected="selected">Stack</option>
                <?php } else { ?>
				<option value="stack">Stack</option>
                <?php } ?>
                
                <?php if ($slide_transition == 'scale') { ?>
				<option value="scale" selected="selected">Scale</option>
                <?php } else { ?>
				<option value="scale">Scale</option>
                <?php } ?>
                
                </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_speed; ?></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="speed" value="<?php echo isset($speed) ? $speed : '20'; ?>" />
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_loop; ?></label>
            <div class="col-sm-10">
                <select name="loop" class="form-control">
                <?php if ($loop) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $text_nav_buttons; ?></label>
            <div class="col-sm-10">
              <select name="nav_buttons" class="form-control">
                
                <?php if ($nav_buttons == '0') { ?>
				<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
				<option value="0"><?php echo $text_disabled; ?></option>
                <?php } ?>
                
                <?php if ($nav_buttons == 'simple-arrows') { ?>
				<option value="simple-arrows" selected="selected"><?php echo $text_simple_arrows; ?></option>
                <?php } else { ?>
				<option value="simple-arrows"><?php echo $text_simple_arrows; ?></option>
                <?php } ?>
                
                <?php if ($nav_buttons == 'circle-arrows') { ?>
				<option value="circle-arrows" selected="selected"><?php echo $text_circle_arrows; ?></option>
                <?php } else { ?>
				<option value="circle-arrows"><?php echo $text_circle_arrows; ?></option>
                <?php } ?>
                
              </select>
            </div>
          </div>
          
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $text_nav_bullets; ?></label>
            <div class="col-sm-10">
              <select name="nav_bullets" class="form-control">
                
                <?php if ($nav_bullets) { ?>
				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
				<option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>

              </select>
            </div>
          </div>
          
          
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $text_nav_timer_bar; ?></label>
            <div class="col-sm-10">
              <select name="nav_timer_bar" class="form-control">
                
                <?php if ($nav_timer_bar) { ?>
				<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
				<option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
                
              </select>
            </div>
          </div>
          
          
          
          
          
          </div>
          
        </div>
        
        <!-- Google Fonts -->
        <div role="tabpanel" class="tab-pane" id="fonts">
        
                <?php echo $fonts_help_block; ?><br />

               
                
        <table id="fonts" class="g_fonts">
            <thead>
              <tr>
                <td class="text-left"><?php echo $text_import; ?></td>
                <td class="text-left"><?php echo $text_name; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $g_font_row = 1; ?>
              <?php foreach ($g_fonts as $g_font) { ?>
              <tr id="g_font-row<?php echo $g_font_row; ?>">
             
                <td class="text-left">
                <input type="text" class="form-control" name="g_fonts[<?php echo $g_font_row; ?>][import]" value="<?php echo isset($g_font['import']) ? $g_font['import'] : ''; ?>" />
                </td>
                
                <td class="text-left">
                <input type="text" class="form-control" name="g_fonts[<?php echo $g_font_row; ?>][name]" value="<?php echo isset($g_font['name']) ? $g_font['name'] : ''; ?>" />
                </td>

                <td class="text-right action"><button type="button" onclick="$('#g_font-row<?php echo $g_font_row; ?>').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $g_font_row++; ?>
			<?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2"></td>
                <td class="text-left action"><button type="button" onclick="addFont();" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </tfoot>
          </table>
        </div>
        
        <!-- Slides -->
        
        <div role="tabpanel" class="tab-pane <?php if ($has_module_id) { echo 'active'; } ?>" id="slides">
         
      		<ul class="nav nav-pills" id="section">
                <?php $section_row = 1; ?>
                <?php foreach ($sections as $section) { ?>
        		<li><a href="#tab-section-<?php echo $section_row; ?>" data-toggle="tab"><i class="fa fa-times-circle" onclick="$('a[href=\'#tab-section-<?php echo $section_row; ?>\']').parent().remove(); $('#tab-section-<?php echo $section_row; ?>').remove(); $('#section a:first').tab('show');"></i><i class="fa fa-image"></i> <?php echo $tab_section . ' ' . $section_row; ?></a></li>
        		<?php $section_row++; ?>
        		<?php } ?>
        		<li id="section-add"><a onclick="addSlide();"><?php echo $text_add_section; ?> <i class="fa fa-long-arrow-right"></i></a></li> 
        	</ul>
        
      	
        <div class="row">
      	<div class="col-sm-12">
        
        <div class="tab-content first">
        
        <?php $section_row = 1; ?>
       
      	<?php foreach ($sections as $section) { ?>
           
		<div class="tab-pane" id="tab-section-<?php echo $section_row; ?>">
		<div class="tab-content">
          
          <h2><?php echo $text_slide_settings; ?></h2>
          <div class="well">
          <div class="row">
          
          <div class="form-group col-sm-4">
          <label><?php echo $text_sort_order; ?></label>
       	  <input type="text" class="form-control" name="sections[<?php echo $section_row; ?>][sort_order]" value="<?php echo isset($section['sort_order']) ? $section['sort_order'] : $section_row; ?>" />
          </div> <!-- form-group ends -->
          
          <div class="form-group col-sm-4">
          <label><?php echo $text_slide_duration; ?></label>
       	  <input type="text" class="form-control" name="sections[<?php echo $section_row; ?>][duration]" id="duration<?php echo $section_row; ?>" value="<?php echo isset($section['duration']) ? $section['duration'] : '6'; ?>" />
          </div> <!-- form-group ends -->
          
          <div class="form-group col-sm-4">
          <label><?php echo $text_slide_kenburn; ?></label>
        <select name="sections[<?php echo $section_row; ?>][slide_kenburn]" class="form-control">
        <?php foreach ($slide_kenburns as $key => $slide_kenburn) { ?>
        <option value="<?php echo $key; ?>" 
        <?php echo ($section['slide_kenburn'] == $key) ? 'selected="selected"' : ''; ?>>
        <?php echo $slide_kenburn; ?></option>
        <?php } ?>
        </select>
          </div> <!-- form-group ends -->
          
          </div> <!-- row ends -->
          
          
          <div class="row">
          
          <div class="form-group col-sm-4">
          <label><?php echo $text_bg_color; ?></label>
          <div class="input-group colorpicker-element" id="bg_color_<?php echo $section_row; ?>">
          <input class="form-control" name="sections[<?php echo $section_row; ?>][bg_color]" value="<?php echo isset($section['bg_color']) ? $section['bg_color'] : false; ?>" /><span class="input-group-addon"><i></i></span>
          </div>
          </div> <!-- form-group ends -->
          
          <div class="form-group col-sm-4">
          <label><?php echo $text_link_target; ?></label>
       	  <input type="text" class="form-control" name="sections[<?php echo $section_row; ?>][link]" value="<?php echo isset($section['link']) ? $section['link'] : ''; ?>" />
          </div> <!-- form-group ends -->
          
          <div class="form-group col-sm-4">
          <label><?php echo $text_button_target; ?></label>
          
          <select name="sections[<?php echo $section_row; ?>][link_new_window]" class="form-control">
        <?php if ($section['link_new_window']) { ?>
		<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
        <option value="0"><?php echo $text_disabled; ?></option>
        <?php } else { ?>
        <option value="1"><?php echo $text_enabled; ?></option>
        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
        <?php } ?>
        </select>
          </div> <!-- form-group ends -->
          
          </div> <!-- row ends -->
          
          </div> <!-- well ends -->
          

          
          <h2><?php echo $text_slide_preview; ?></h2>
          
          <div class="slide-preview-wrapper">

          <div class="slide-preview s<?php echo $section_row; ?>" style="
          background-image:url('<?php echo isset($section['thumb_image']) ? $base_url . 'image/' . $section['thumb_image'] : ''; ?>');
          background-color:<?php echo isset($section['bg_color']) ? $section['bg_color'] : ''; ?>;
          width:<?php echo isset($width) ? $width : '1140'; ?>px;
          ">
          
          <div class="slide-preview-inner" style="width:<?php echo isset($width) ? $width : '1140'; ?>px;height:<?php echo isset($height) ? $height : '500'; ?>px">
          
          
          <?php $group_row = 0; ?>
            <?php if (isset($section['groups'])) { ?>
                        
            <?php foreach($section['groups'] as $group){ ?>
            
            <!-- Text layer -->
            
           <div class="preview-layer-holder s<?php echo $section_row; ?> g<?php echo $group_row; ?>" style="
           top:<?php echo isset($section['groups'][$group_row]['top'][$lang]) ? $section['groups'][$group_row]['top'][$lang] : ''; ?>px;
           left:<?php echo isset($section['groups'][$group_row]['left'][$lang]) ? $section['groups'][$group_row]['left'][$lang] : ''; ?>px;
           ">
           
            <div class="preview_single_layer s<?php echo $section_row; ?> g<?php echo $group_row; ?> layer-edit-s<?php echo $section_row; ?>-g<?php echo $group_row; ?> <?php if ($section['groups'][$group_row]['type'] == 'button') { echo $section['groups'][$group_row]['button_class'];} ?>" onclick="layerNavigate(<?php echo $section_row; ?>, <?php echo $group_row; ?>);" style="
            
            z-index:<?php echo isset($section['groups'][$group_row]['sort_order']) ? $section['groups'][$group_row]['sort_order'] : 'auto'; ?>;
            
            <?php if ($section['groups'][$group_row]['type'] == 'text') { ?>
            font-family:<?php echo isset($section['groups'][$group_row]['font']) ? $section['groups'][$group_row]['font'] : ''; ?>;
            font-weight:<?php echo isset($section['groups'][$group_row]['fontweight']) ? $section['groups'][$group_row]['fontweight'] : ''; ?>;
            font-size:<?php echo isset($section['groups'][$group_row]['fontsize']) ? $section['groups'][$group_row]['fontsize'] : ''; ?>;
            color:<?php echo isset($section['groups'][$group_row]['color']) ? $section['groups'][$group_row]['color'] : ''; ?>;
            background-color:<?php echo isset($section['groups'][$group_row]['bg']) ? $section['groups'][$group_row]['bg'] : ''; ?>;
            padding:<?php echo isset($section['groups'][$group_row]['padding']) ? $section['groups'][$group_row]['padding'] : ''; ?>;
            border-radius:<?php echo isset($section['groups'][$group_row]['radius']) ? $section['groups'][$group_row]['radius'] : ''; ?>;
            <?php echo isset($section['groups'][$group_row]['customcss']) ? $section['groups'][$group_row]['customcss'] : ''; ?>
            <?php } ?>
            ">
            
            <?php if ($section['groups'][$group_row]['type'] == 'text') { ?>
            <?php echo isset($section['groups'][$group_row]['description'][$lang]) ? html_entity_decode($section['groups'][$group_row]['description'][$lang], ENT_QUOTES, 'UTF-8') : ''; ?>
            <?php } ?>
            
            <?php if ($section['groups'][$group_row]['type'] == 'button') { ?>
            <span><?php echo isset($section['groups'][$group_row]['description'][$lang]) ? html_entity_decode($section['groups'][$group_row]['description'][$lang], ENT_QUOTES, 'UTF-8') : ''; ?></span>
            <?php } ?>
            
            <?php if ($section['groups'][$group_row]['type'] == 'image') { ?>
            <img src="<?php echo (!empty($section['groups'][$group_row]['image'][$lang])) ? $base_url . 'image/' . $section['groups'][$group_row]['image'][$lang] : $placeholder; ?>" alt="" title="" />
            <?php } ?>
            
            
            </div>
            
           </div>
                        
            <?php $group_row++; ?>
            <?php } ?> <!-- foreach groups ends -->
            <?php } ?>
            
              
            </div> <!-- Slide preview inner ends -->
          </div> <!-- Slide preview ends -->
          </div> <!-- Slide preview wrapper ends -->
          
          <div class="slide-toolbar">
          <div class="row">
          <div class="col-sm-6">
          <div>
       	  <a id="thumb-image<?php echo $section_row; ?>" data-toggle="image" class="btn btn-default"><i class="fa fa-arrows-alt"></i> <?php echo $text_change_slide_bg; ?></a>
        <input type="hidden" name="sections[<?php echo $section_row; ?>][thumb_image]" value="<?php echo isset($section['thumb_image']) ? $section['thumb_image'] : ''; ?>" id="input-image<?php echo $section_row; ?>" />
          </div>
          </div>
          
          <div class="col-sm-6 text-right">
          <a class="btn btn-default" onclick="addLayer(<?php echo $section_row; ?>, 'text');"><i class="fa fa-font"></i> <?php echo $text_add_text_layer; ?></a>
          <a class="btn btn-default" onclick="addLayer(<?php echo $section_row; ?>, 'button');"><i class="fa fa-hand-o-up"></i> <?php echo $text_add_button_layer; ?></a>
          <a class="btn btn-default" onclick="addLayer(<?php echo $section_row; ?>, 'image');"><i class="fa fa-image"></i> <?php echo $text_add_image_layer; ?></a>
          </div>
          </div> <!-- row ends -->
          </div>
          
          
          
		<div class="layer-wrapper">
        
        
        
          <div id="groups-<?php echo $section_row; ?>" class="layer-holder">
          
          <div class="layer-table-header">
          <div class="layer-table-cell icons">
          <b class="pull-right"><span><?php echo $text_layer_sort_order; ?></span><span><?php echo $text_layer_parallax; ?></span></b>
          <b><?php echo $text_layer_list; ?></b>
          </div>
          <div class="layer-table-cell slider"><b><?php echo $text_layer_in; ?></b><b class="pull-right"><?php echo $text_layer_out; ?></b></div>
          </div>
          
          <?php $group_row = 0; ?>
           <?php if (isset($section['groups'])) { ?>


          	<?php foreach($section['groups'] as $group){ ?>
			
            <div id="group-row-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" class="single-layer row<?php echo $group_row; ?>">
            
            <div class="main">
          
          <div class="panel panel-default ">
  			
            <div class="panel-heading"><h3 class="panel-title"><?php echo $text_layer_settings; ?></h3></div>
  			
            <div class="panel-body">
            <input type="hidden" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][type]" value="<?php echo isset($section['groups'][$group_row]['type']) ? $section['groups'][$group_row]['type'] : 'text'; ?>" />	
            

            
                <div id="language-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>">
                  <ul class="nav nav-tabs" id="language<?php echo $section_row; ?>">
                    <?php foreach ($languages as $language) { ?>
                    <li><a href="#tab-section-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $language['language_id']; ?>" data-toggle="tab">
                    <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></a></li>
                    <?php } ?>
                  </ul>
                 </div>
               <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane fade" id="tab-section-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $language['language_id']; ?>">
                  
                  <h4><?php echo $text_layer_content; ?></h4>
                  <div class="row">
                  <div class="col-sm-12">
                  
                  <?php if ($section['groups'][$group_row]['type'] == 'text') { ?>
                  <div class="form-group">
                  <textarea class="form-control custom-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][description][<?php echo $language['language_id']; ?>]" id="description-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $language['language_id']; ?>"><?php echo isset($section['groups'][$group_row]['description'][$language['language_id']]) ? $section['groups'][$group_row]['description'][$language['language_id']] : ''; ?></textarea>
                 </div>
                 <?php } ?>
                 
                 <?php if ($section['groups'][$group_row]['type'] == 'button') { ?>
                  <div class="form-group">
                  <label><?php echo $text_button_text; ?></label>
                  <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][description][<?php echo $language['language_id']; ?>]" id="button_text-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $language['language_id']; ?>" value="<?php echo isset($section['groups'][$group_row]['description'][$language['language_id']]) ? $section['groups'][$group_row]['description'][$language['language_id']] : ''; ?>" />
                 </div>
                 <?php } ?>
                 
                 <?php if ($section['groups'][$group_row]['type'] == 'image') { ?>
                 <div class="well">
                 <div class="image_holder">
                  <a href="" id="thumb-image-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $language['language_id']; ?>" data-toggle="image" class="img-thumbnail">
                <img src="<?php echo (!empty($section['groups'][$group_row]['image'][$language['language_id']])) ? $base_url . 'image/' . $section['groups'][$group_row]['image'][$language['language_id']] : $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                <input type="hidden" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][image][<?php echo $language['language_id']; ?>]" value="<?php echo isset($section['groups'][$group_row]['image'][$language['language_id']]) ? $section['groups'][$group_row]['image'][$language['language_id']] : ''; ?>" id="input-image-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $language['language_id']; ?>" />
                </div>
                </div>
                <?php } ?>
                
                </div>
                </div> <!-- Row ends -->
                
                <h4><?php echo $text_layer_position; ?></h4>
                <div class="row border_bottom">
            	<div class="form-group col-sm-6">
                <label><?php echo $text_offset_left; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][left][<?php echo $language['language_id']; ?>]" id="left-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-l<?php echo $language['language_id']; ?>" value="<?php echo isset($section['groups'][$group_row]['left'][$language['language_id']]) ? $section['groups'][$group_row]['left'][$language['language_id']] : ''; ?>" />	
            	</div>
                
            	<div class="form-group col-sm-6">
                <label><?php echo $text_offset_top; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][top][<?php echo $language['language_id']; ?>]" id="top-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-l<?php echo $language['language_id']; ?>" value="<?php echo isset($section['groups'][$group_row]['top'][$language['language_id']]) ? $section['groups'][$group_row]['top'][$language['language_id']] : ''; ?>" />	
            	</div>
            	</div> <!-- Row ends -->
                 
                </div> <!-- tab-pane ends -->
                <?php } ?> <!-- Foreach language ends -->
                </div>
                
                <?php if ($section['groups'][$group_row]['type'] == 'image') { ?>
                <h4><?php echo $text_heading_minheight; ?></h4>
                <div class="row border_bottom">
            	<div class="form-group col-sm-12">
                <label><?php echo $text_layer_minheight; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][minheight]" value="<?php echo isset($section['groups'][$group_row]['minheight']) ? $section['groups'][$group_row]['minheight'] : '0'; ?>" />	
            	</div>
                </div>
                <?php } ?>
            
			<?php if ($section['groups'][$group_row]['type'] == 'text' || $section['groups'][$group_row]['type'] == 'button') { ?>
              <h4><?php echo $text_layer_style; ?></h4>
              
              <?php if ($section['groups'][$group_row]['type'] == 'button') { ?>
              <div class="row">
              <div class="form-group col-sm-12">
                <label><?php echo $text_button_class; ?></label>
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][button_class]" id="button_class-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" class="form-control">
                <?php foreach ($button_classes as $key => $button_class) { ?>
                <option value="<?php echo $key; ?>" 
                <?php echo ($section['groups'][$group_row]['button_class'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php echo $button_class; ?></option>
                <?php } ?>
                </select>
                </div>
              </div>
              <div class="row">
              <div class="form-group col-sm-6">
                <label><?php echo $text_button_href; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][button_href]" id="button_href-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" value="<?php echo isset($section['groups'][$group_row]['button_href']) ? $section['groups'][$group_row]['button_href'] : ''; ?>" />
                </div>
                <div class="form-group col-sm-6">
                <label><?php echo $text_button_target; ?></label>
                
                
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][button_target]" class="form-control">
                <?php if ($section['groups'][$group_row]['button_target']) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
                </select>
                
                </div>
              </div>
              
              <?php } ?>
              
              
              <?php if ($section['groups'][$group_row]['type'] == 'text') { ?>
              
              <div class="row">
                <div class="form-group col-sm-6">
                <label><?php echo $text_font_family; ?></label>
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][font]" id="font-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" class="form-control">
                <option disabled style="font-weight:bold"><?php echo $text_system_fonts; ?></option>
                <?php foreach ($system_fonts as $key => $system_font) { ?>
                <option value="<?php echo $key; ?>" 
                <?php echo ($section['groups'][$group_row]['font'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php echo $system_font; ?></option>
                <?php } ?>
                <option disabled style="font-weight:bold"><?php echo $text_google_fonts; ?></option>
                <?php foreach ($g_fonts as $g_font) { ?>
                <option value="<?php echo $g_font['name']; ?>" <?php if ($section['groups'][$group_row]['font'] == $g_font['name']) { echo 'selected="selected"'; } ?>><?php echo $g_font['name']; ?></option>
                <?php } ?>
                <?php foreach ($basel_fonts as $basel_font) { ?>
                <option value="<?php echo $basel_font['name']; ?>" <?php if ($section['groups'][$group_row]['font'] == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
                <?php } ?>
              </select>
                </div>
                
                <div class="form-group col-sm-6">
                <label><?php echo $text_font_weight; ?></label>
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][fontweight]" id="font-weight-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" class="form-control">
                <?php foreach ($fontweights as $key => $fontweight) { ?>
                <option value="<?php echo $key; ?>" 
                <?php echo ($section['groups'][$group_row]['fontweight'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php echo $fontweight; ?></option>
                <?php } ?>
                </select>
                </div>
                                
                </div> <!-- Row ends -->
                
                
                
                <div class="row">
                
                <div class="form-group col-sm-6">
                <label><?php echo $text_font_size; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][fontsize]" id="fontsize-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" value="<?php echo isset($section['groups'][$group_row]['fontsize']) ? $section['groups'][$group_row]['fontsize'] : ''; ?>" />
            	</div>

                <div class="form-group col-sm-6">
                <label><?php echo $text_color; ?></label>
                <div class="input-group" id="color-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>">
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][color]" value="<?php echo isset($section['groups'][$group_row]['color']) ? $section['groups'][$group_row]['color'] : ''; ?>" /><span class="input-group-addon"><i></i></span>
                </div>
            	</div>

               </div> <!-- Row ends -->
               
               
               <div class="row">
               
               <div class="form-group col-sm-6">
                <label><?php echo $text_background; ?></label>
                <div class="input-group" id="bg-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>">
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][bg]" value="<?php echo isset($section['groups'][$group_row]['bg']) ? $section['groups'][$group_row]['bg'] : ''; ?>" /><span class="input-group-addon"><i></i></span>
                </div>
            	</div>

                <div class="form-group col-sm-6">
                <label><?php echo $text_padding; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][padding]" id="padding-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" value="<?php echo isset($section['groups'][$group_row]['padding']) ? $section['groups'][$group_row]['padding'] : ''; ?>" />
            	</div>
                
               </div> <!-- Row ends -->
               
               <div class="row border_bottom">

                <div class="form-group col-sm-6">
                <label><?php echo $text_border_radius; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][radius]" id="radius-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" value="<?php echo isset($section['groups'][$group_row]['radius']) ? $section['groups'][$group_row]['radius'] : ''; ?>" />
            	</div>
                
                <div class="form-group col-sm-6">
                <label><?php echo $text_custom_css; ?></label>
                <textarea class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][customcss]" id="customcss-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>"><?php echo isset($section['groups'][$group_row]['customcss']) ? $section['groups'][$group_row]['customcss'] : ''; ?></textarea>
            	</div>
			
               </div> <!-- Row ends -->
               <?php } ?> <!-- layer type text ends -->
               <?php } ?>
               
               <h4><?php echo $text_animation_in; ?></h4>
               
               <div class="row">
                
                <div class="form-group col-sm-6">
                <label><?php echo $text_effect; ?></label>
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][transitionin]" id="transitionin-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" class="form-control">
                <?php foreach ($transitions as $key => $transition) { ?>
                <option value="<?php echo $key; ?>" 
                <?php echo ($section['groups'][$group_row]['transitionin'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php echo $transition; ?></option>
                <?php } ?>
                </select>
                </div>
                
                <div class="form-group col-sm-6">
                <label><?php echo $text_easing; ?></label>
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][easingin]" id="ease-in-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" class="form-control">
                <?php foreach ($easings as $key => $easing) { ?>
                <option value="<?php echo $key; ?>" 
                <?php echo ($section['groups'][$group_row]['easingin'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php echo $easing; ?></option>
                <?php } ?>
                </select>
                </div>
                
               </div> <!-- Row ends -->
               
               <div class="row border_bottom">
               
               <div class="form-group col-sm-6">
                <label><?php echo $text_duration; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][durationin]" id="durationin-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" value="<?php echo isset($section['groups'][$group_row]['durationin']) ? $section['groups'][$group_row]['durationin'] : ''; ?>" />
            	</div>
                
               </div> <!-- Row ends -->
               
               
               
               <h4><?php echo $text_animation_out; ?></h4>
               
               <div class="row">
                
                <div class="form-group col-sm-6">
                <label><?php echo $text_effect; ?></label>
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][transitionout]" class="form-control">
                <?php foreach ($transitions as $key => $transition) { ?>
                <option value="<?php echo $key; ?>" 
                <?php echo ($section['groups'][$group_row]['transitionout'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php echo $transition; ?></option>
                <?php } ?>
                </select>
                </div>
                
                <div class="form-group col-sm-6">
                <label><?php echo $text_easing; ?></label>
                <select name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][easingout]" class="form-control">
                <?php foreach ($easings as $key => $easing) { ?>
                <option value="<?php echo $key; ?>" 
                <?php echo ($section['groups'][$group_row]['easingout'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php echo $easing; ?></option>
                <?php } ?>
                </select>
                </div>
                
               </div> <!-- Row ends -->
               
               <div class="row">
               
               <div class="form-group col-sm-6">
                <label><?php echo $text_duration; ?></label>
                <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][durationout]" value="<?php echo isset($section['groups'][$group_row]['durationout']) ? $section['groups'][$group_row]['durationout'] : ''; ?>" />
            	</div>
                
               </div> <!-- Row ends -->
                
               
               </div>
               </div> 
               </div>
               
               <div class="layer-list">
               <div class="layer-bar">
               
               <div class="layer-table-cell icons">
               <a class="icon selector" onclick="layerNavigate(<?php echo $section_row; ?>, <?php echo $group_row; ?>);"></a>
               <div class="icon <?php echo $section['groups'][$group_row]['type']; ?>"></div>
               <a class="icon remove" onclick="removeLayer(<?php echo $section_row; ?>, <?php echo $group_row; ?>);"><i class="fa fa-times-circle"></i></a>
               <div class="sort_holder">
               <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][sort_order]" value="<?php echo (!empty($section['groups'][$group_row]['sort_order'])) ? $section['groups'][$group_row]['sort_order'] : '0'; ?>" id="sort_order<?php echo $section_row; ?><?php echo $group_row; ?>" />
               <input class="form-control" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][p_index]" value="<?php echo (!empty($section['groups'][$group_row]['p_index'])) ? $section['groups'][$group_row]['p_index'] : '0'; ?>" id="p_index<?php echo $section_row; ?><?php echo $group_row; ?>" />
               </div>
               
               </div>
               
               
                
                <div class="layer-table-cell field first">
                <input class="form-control" data-index="0" style="width:60px;display:inline-block" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][start]" id="start-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" value="<?php echo (!empty($section['groups'][$group_row]['start'])) ? $section['groups'][$group_row]['start'] : '0'; ?>" />
                </div>
                
                <div class="layer-table-cell slider">
                <div id="slider-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>"></div>
                </div>
                
                <div class="layer-table-cell field last">
               <input class="form-control" data-index="1" style="width:60px;display:inline-block" name="sections[<?php echo $section_row; ?>][groups][<?php echo $group_row; ?>][end]" id="end-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>" value="<?php echo (!empty($section['groups'][$group_row]['end'])) ? $section['groups'][$group_row]['end'] : '6000'; ?>" />
               </div>
               
               
               
               </div> <!-- layer-bar ends -->
               </div>
               
          		<?php $group_row++; ?>
                
                </div> <!-- class single-layer ends -->
               <?php } ?> <!-- foreach groups ends -->
                <?php } ?>

          	</div> <!-- id groups- ends -->
           </div> <!-- layer-wrapper ends -->
            
         </div> <!-- tab-content ends -->
      	<?php $section_row++; ?>
      	</div>
      	
        
        <?php } ?> <!-- foreach sections ends -->
      
       </div>
      </div> 
    </div>
  </div> 
</div>
          

      
     </form>
   </div>
  </div>
 </div>
<br />

<!-- Google Fonts -->
<script type="text/javascript"><!--
var deleted_layers = 0;

var g_font_row = <?php echo $g_font_row; ?>;
function addFont() {
	html  = '<tr id="g_font-row' + g_font_row + '">';
	html += '<td class="text-left">';
	html += '<input type="text" name="g_fonts[' + g_font_row + '][import]" class="form-control"/>';	
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" name="g_fonts[' + g_font_row + '][name]" class="form-control"/>';	
	html += '</td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#g_font-row' + g_font_row  + '\').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	$('#fonts tbody').append(html);
	g_font_row++;
}
//--></script>


<script type="text/javascript"><!--
var section_row = <?php echo $section_row; ?>;

function addSlide() {	
	group_row = 0;
   	html  = '<div class="tab-pane" id="tab-section-' + section_row + '">';
	html += '<div class="tab-content">';
	html += '<h2><?php echo $text_slide_settings; ?></h2>';
	html += '<div class="well">';
	
	html += '<div class="row">';
	html += '<div class="form-group col-sm-4">';
	html += '<label><?php echo $text_sort_order; ?></label>';
    html += '<input type="text" class="form-control" name="sections[' + section_row + '][sort_order]" value="' + section_row + '" />';
	html += '</div>';
	html += '<div class="form-group col-sm-4">';
	html += '<label><?php echo $text_slide_duration; ?></label>';
    html += '<input type="text" class="form-control" id="duration' + section_row + '" name="sections[' + section_row + '][duration]" value="6" />';
	html += '</div>';

	html += '<div class="form-group col-sm-4">';
	html += '<label><?php echo $text_slide_kenburn; ?></label>';
	html += '<select name="sections[' + section_row + '][slide_kenburn]" class="form-control">';
	<?php foreach ($slide_kenburns as $key => $slide_kenburn) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $slide_kenburn; ?></option>';
    <?php } ?>
	html += '</select>';
	html += '</div>';
	html += '</div>';
	
	html += '<div class="row">';
	
	html += '<div class="form-group col-sm-4">';
	html += '<label><?php echo $text_bg_color; ?></label>';
	html += '<div class="input-group colorpicker-element" id="bg_color_' + section_row + '">';
    html += '<input class="form-control" name="sections[' + section_row + '][bg_color]" /><span class="input-group-addon"><i></i></span>';
	html += '</div>';
	html += '</div>';
	
		  
	html += '<div class="form-group col-sm-4">';
	html += '<label><?php echo $text_link_target; ?></label>';
    html += '<input type="text" class="form-control" name="sections[' + section_row + '][link]" />';
	html += '</div>';
	html += '<div class="form-group col-sm-4">';
	html += '<label><?php echo $text_button_target; ?></label>';
	html += '<select class="form-control" name="sections[' + section_row + '][link_new_window]">';
	html += '<option value="1"><?php echo $text_enabled; ?></option>';
	html += '<option value="0" selected="selected"><?php echo $text_disabled; ?></option>';
	html += '</select>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '<h2><?php echo $text_slide_preview; ?></h2>';
	html += '<div class="slide-preview-wrapper">';
	html += '<div class="slide-preview s' + section_row + '">';
	html += '<div class="slide-preview-inner" style="width:' + $('#input-width').val() + 'px; height:' + $('#input-height').val() + 'px; ">';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '<div class="slide-toolbar">';
	html += '<div class="row">';
	html += '<div class="col-sm-6">';
	html += '<div>';
	html += '<a id="thumb-image' + section_row + '" data-toggle="image" class="btn btn-default"><i class="fa fa-arrows-alt"></i> <?php echo $text_change_slide_bg; ?></a>';
	html += '<input type="hidden" id="input-image' + section_row + '" name="sections[' + section_row + '][thumb_image]" />';
	html += '</div>';
	html += '</div>';
	html += '<div class="col-sm-6 text-right">';
	html += '<a class="btn btn-default" onclick="addLayer(' + section_row + ', \'text\''+');"><i class="fa fa-font"></i> <?php echo $text_add_text_layer; ?></a> ';
	html += '<a class="btn btn-default" onclick="addLayer(' + section_row + ', \'button\''+');"><i class="fa fa-hand-o-up"></i> <?php echo $text_add_button_layer; ?></a> ';
	html += '<a class="btn btn-default" onclick="addLayer(' + section_row + ', \'image\''+');"><i class="fa fa-image"></i> <?php echo $text_add_image_layer; ?></a>';
    html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '<div class="layer-wrapper">';
	html += '<div id="groups-' + section_row + '" class="layer-holder">';
	html += '<div class="layer-table-header">';
	html += '<div class="layer-table-cell icons"><b class="pull-right"><span><?php echo $text_layer_sort_order; ?></span><span><?php echo $text_layer_parallax; ?></span></b><b><?php echo $text_layer_list; ?></b></div>';
	html += '<div class="layer-table-cell slider"><b><?php echo $text_layer_in; ?></b><span class="pull-right"><b><?php echo $text_layer_out; ?></b></span></div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	
	$('.tab-content.first').append(html);
		
	$('#section-add').before('<li><a href="#tab-section-' + section_row + '" data-toggle="tab"><i class="fa fa-times-circle" onclick="$(\'a[href=\\\'#tab-section-' + section_row + '\\\']\').parent().remove(); $(\'#tab-section-' + section_row + '\').remove(); $(\'#section a:first\').tab(\'show\');"></i> </i><i class="fa fa-image"></i> <?php echo $tab_section; ?> ' + section_row + '</a></li>');


	$('#section a[href=\'#tab-section-' + section_row + '\']').tab('show');
	
	addSlideScripts(section_row);
	
	section_row++;
}
//--></script>

<script type="text/javascript"><!--
function addLayer(section_row, layer_type) {
	group_row = ($('#groups-' + section_row + ' .single-layer').length) + (deleted_layers);
	sort_order = $('#groups-' + section_row + ' .single-layer').length + 1;

	html  = '<div id="group-row-s' + section_row + '-g' + group_row + '" class="single-layer row' + group_row + '">';
	html += '<div class="main">';
	html += '<div class="panel panel-default">';
	html += '<div class="panel-heading"><h3 class="panel-title"><?php echo $text_layer_settings; ?></h3></div>';
	html += '<div class="panel-body">';
	html += '<input type="hidden" name="sections[' + section_row + '][groups][' + group_row + '][type]" value="' + layer_type + '"/>';
	html += '<div id="language-s' + section_row + '-g' + group_row + '">';
	html += '<ul class="nav nav-tabs" id="language' + section_row + '">';
	<?php foreach ($languages as $language) { ?>
	html += '<li><a href="#tab-section-s' + section_row + '-g' + group_row + '-<?php echo $language['language_id']; ?>" data-toggle="tab">';
	html += '<img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />'
	html += '</a></li>';
	<?php } ?>
    html += '</ul>';
	html += '</div>';
	html += '<div class="tab-content">';
	<?php foreach ($languages as $language) { ?>
	html += '<div class="tab-pane" id="tab-section-s' + section_row + '-g' + group_row + '-<?php echo $language['language_id']; ?>">';
	html += '<h4><?php echo $text_layer_content; ?></h4>';
	html += '<div class="row">';
	html += '<div class="col-sm-12">';
	if (layer_type == 'text') { 
	html += '<div class="form-group">';
	html += '<textarea class="form-control custom-control" name="sections[' + section_row + '][groups][' + group_row + '][description][<?php echo $language['language_id']; ?>]" id="description-s' + section_row + '-g' + group_row + '-<?php echo $language['language_id']; ?>" >Layer Caption</textarea>';
	html += '</div>';
	}
	if (layer_type == 'button') { 
	html += '<div class="form-group">';
	html += '<label><?php echo $text_button_text; ?></label>';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][description][<?php echo $language['language_id']; ?>]" id="button_text-s' + section_row + '-g' + group_row + '-<?php echo $language['language_id']; ?>" value="button text" />';
	html += '</div>';
	}
	if (layer_type == 'image') { 
	html += '<div class="well">';
	html += '<div class="image_holder">';
	html += '<a href="" id="thumb-image-s' + section_row + '-g' + group_row + '-<?php echo $language['language_id']; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>';
	html += '<input type="hidden" name="sections[' + section_row + '][groups][' + group_row + '][image][<?php echo $language['language_id']; ?>]" id="input-image-s' + section_row + '-g' + group_row + '-<?php echo $language['language_id']; ?>" value="cache/no_image-100x100.png" />';
	html += '</div>';
	html += '</div>';
	} 
	html += '</div>';
	html += '</div>';
	html += '<h4><?php echo $text_layer_position; ?></h4>';
	html += '<div class="row border_bottom">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_offset_left; ?></label>';
	html += '<input class="form-control" type="text" name="sections[' + section_row + '][groups][' + group_row + '][left][<?php echo $language['language_id']; ?>]" id="left-s' + section_row + '-g' + group_row + '-l<?php echo $language['language_id']; ?>" value="50" />';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_offset_top; ?></label>';
	html += '<input class="form-control" type="text" name="sections[' + section_row + '][groups][' + group_row + '][top][<?php echo $language['language_id']; ?>]" id="top-s' + section_row + '-g' + group_row + '-l<?php echo $language['language_id']; ?>" value="50" />';
	html += '</div>';
	html += '</div>';
	html += '</div>';

	<?php } ?>
	html += '</div>';
    
	if (layer_type == 'text' || layer_type == 'button') {
	html += '<h4><?php echo $text_layer_style; ?></h4>';
	if (layer_type == 'button') {
	html += '<div class="row">';
	html += '<div class="form-group col-sm-12">';
	html += '<label><?php echo $text_button_class; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][button_class]" id="button_class-s' + section_row + '-g' + group_row + '" class="form-control">';
	<?php foreach ($button_classes as $key => $button_class) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $button_class; ?></option>';
    <?php } ?>
	html += '</select>';
	html += '</div>';
	html += '</div>';
	html += '<div class="row">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_button_href; ?></label>';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][button_href]" />';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_button_target; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][button_target]" class="form-control">';
    html += '<option value="0"><?php echo $text_disabled; ?></option>';
	html += '<option value="1"><?php echo $text_enabled; ?></option>';
	html += '</select>';
	html += '</div>';
	html += '</div>';
	}
	
	if (layer_type == 'text') {
	html += '<div class="row">';
	
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_font_family; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][font]" id="font-s' + section_row + '-g' + group_row + '" class="form-control">';
	html += '<option disabled style="font-weight:bold"><?php echo $text_system_fonts; ?></option>';
	<?php foreach ($system_fonts as $key => $system_font) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $system_font; ?></option>';
    <?php } ?>
	html += '<option disabled style="font-weight:bold"><?php echo $text_google_fonts; ?></option>';
	<?php foreach ($g_fonts as $g_font) { ?>
    html += '<option value="<?php echo addslashes($g_font['name']); ?>">';
	html += '<?php echo addslashes($g_font['name']); ?>';
	html += '</option>';     
    <?php } ?>
	<?php foreach ($basel_fonts as $basel_font) { ?>
	html += '<option value="<?php echo addslashes($basel_font['name']); ?>">';
	html += '<?php echo addslashes($basel_font['name']); ?>';
	html += '</option>';
	<?php } ?>		
	html += '</select>';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_font_weight; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][fontweight]" id="font-weight-s' + section_row + '-g' + group_row + '" class="form-control">';
	<?php foreach ($fontweights as $key => $fontweight) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $fontweight; ?></option>';
    <?php } ?>
	html += '</select>';
	html += '</div>';
	html += '</div>';

	html += '<div class="row">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_font_size; ?></label>';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][fontsize]" id="fontsize-s' + section_row + '-g' + group_row + '" value="24px" />';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_color; ?></label>';
	html += '<div class="input-group" id="color-s' + section_row + '-g' + group_row + '">';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][color]" value="#222222" /><span class="input-group-addon"><i></i></span>';
	html += '</div>';
	html += '</div>';
    html += '</div>';
	
	html += '<div class="row">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_background; ?></label>';
	html += '<div class="input-group" id="bg-s' + section_row + '-g' + group_row + '">';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][bg]" value="" /><span class="input-group-addon"><i></i></span>';
	html += '</div>';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_padding; ?></label>';
	html += '<input class="form-control" id="padding-s' + section_row + '-g' + group_row + '" name="sections[' + section_row + '][groups][' + group_row + '][padding]" value="10px 15px 10px 15px" />';
	html += '</div>';
    html += '</div>';

	html += '<div class="row border_bottom">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_border_radius; ?></label>';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][radius]" id="radius-s' + section_row + '-g' + group_row + '" value="3px 3px 3px 3px" />';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_custom_css; ?></label>';
	html += '<textarea class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][customcss]"/></texarea>';
	html += '</div>';
    html += '</div>';
	}
	}
	
	if (layer_type == 'image') {
	html += '<h4><?php echo $text_heading_minheight; ?></h4>';
	html += '<div class="row border_bottom">';
	html += '<div class="form-group col-sm-12">';
	html += '<label><?php echo $text_layer_minheight; ?></label>';
	html += '<input class="form-control" type="text" name="sections[' + section_row + '][groups][' + group_row + '][minheight]"  value="0" />';
	html += '</div>';
	html += '</div>';
	}

	html += '<h4><?php echo $text_animation_in; ?></h4>';
	html += '<div class="row">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_effect; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][transitionin]" id="transitionin-s' + section_row + '-g' + group_row + '" class="form-control">';
	<?php foreach ($transitions as $key => $transition) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $transition; ?></option>';
    <?php } ?>
	html += '</select>';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_easing; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][easingin]" id="easingin-s' + section_row + '-g' + group_row + '" class="form-control">';
	<?php foreach ($easings as $key => $easing) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $easing; ?></option>';
    <?php } ?>
	html += '</select>';
	html += '</div>';
	html += '</div>';
	
	html += '<div class="row border_bottom">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_duration; ?></label>';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][durationin]" id="durationin-s' + section_row + '-g' + group_row + '" value="500" />';
	html += '</div>';
	html += '</div>';

	html += '<h4><?php echo $text_animation_out; ?></h4>';
	html += '<div class="row">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_effect; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][transitionout]" id="transitionout-s' + section_row + '-g' + group_row + '" class="form-control">';
	<?php foreach ($transitions as $key => $transition) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $transition; ?></option>';
    <?php } ?>
	html += '</select>';
	html += '</div>';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_easing; ?></label>';
	html += '<select name="sections[' + section_row + '][groups][' + group_row + '][easingout]" id="easingout-s' + section_row + '-g' + group_row + '" class="form-control">';
	<?php foreach ($easings as $key => $easing) { ?>
    html += '<option value="<?php echo $key; ?>"><?php echo $easing; ?></option>';
    <?php } ?>
	html += '</select>';
	html += '</div>';
	html += '</div>';
	
	html += '<div class="row">';
	html += '<div class="form-group col-sm-6">';
	html += '<label><?php echo $text_duration; ?></label>';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][durationout]" id="durationout-s' + section_row + '-g' + group_row + '" value="500" />';
	html += '</div>';
	html += '</div>';
	
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '<div class="layer-list">';
	html += '<div class="layer-bar">';
	html += '<div class="layer-table-cell icons">';
	html += '<a class="icon selector" onclick="layerNavigate(' + section_row + ', ' + group_row + ');"></a>';
	html += '<div class="icon ' + layer_type + '"></div>';
	html += '<a class="icon remove" onclick="removeLayer(' + section_row + ',' + group_row + ');"><i class="fa fa-times-circle"></i></a>';		   
	
	html += '<div class="sort_holder">';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][sort_order]" value="' + sort_order + '" id="sort_order' + section_row + group_row + '" /> ';
	html += '<input class="form-control" name="sections[' + section_row + '][groups][' + group_row + '][p_index]" value="0" id="p_index' + section_row + group_row + '" />';
	html += '</div>';	
	
	html += '</div>';
	html += '<div class="layer-table-cell field first">';
	html += '<input class="form-control" type="text" data-index="0" name="sections[' + section_row + '][groups][' + group_row + '][start]" id="start-s' + section_row + '-g' + group_row + '" value="500" />';
	html += '</div>';
	html += '<div class="layer-table-cell slider">';
	html += '<div id="slider-s' + section_row + '-g' + group_row + '"></div>';
	html += '</div>';
	html += '<div class="layer-table-cell field last">';
	html += '<input class="form-control" type="text" data-index="1" name="sections[' + section_row + '][groups][' + group_row + '][end]" id="end-s' + section_row + '-g' + group_row + '" value="' + ( ($('#duration' + section_row + '').val() * 1000) - 500) + '" />';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';

	$('#groups-' + section_row ).append(html);
	
	$('#language-s' + section_row + '-g' + group_row + ' li:first-child a').tab('show');
	$('#groups-' + section_row + ' .single-layer').removeClass("active");
	$('#group-row-s' + section_row + '-g' + group_row + '').addClass("active");
	
	if (layer_type == 'text') {
	preview_layer = '<div class="preview-layer-holder s' + section_row + ' g' + group_row + '" style="top:50px;left:50px;z-index:' + sort_order + ';"><div class="preview_single_layer s' + section_row + ' g' + group_row + ' layer-edit-s' + section_row + '-g' + group_row +'" style="font-size:24px;color:#222222;padding:10px 15px 10px 15px;border-radius:3px 3px 3px 3px;" onclick="layerNavigate(' + section_row + ',' + group_row + ');">Layer Caption</div></div>';
	}
	
	if (layer_type == 'button') {
	preview_layer = '<div class="preview-layer-holder s' + section_row + ' g' + group_row + '" style="top:50px;left:50px;z-index:' + sort_order + ';"><div class="preview_single_layer s' + section_row + ' g' + group_row + ' layer-edit-s' + section_row + '-g' + group_row +' btn btn-link" style="" onclick="layerNavigate(' + section_row + ',' + group_row + ');"><span>button text</span></div></div>';
	}
	
	if (layer_type == 'image') {
	preview_layer = '<div class="preview-layer-holder s' + section_row + ' g' + group_row + '" style="top:50px;left:50px;z-index:' + sort_order + ';"><div class="preview_single_layer s' + section_row + ' g' + group_row + ' layer-edit-s' + section_row + '-g' + group_row +'" style="" onclick="layerNavigate(' + section_row + ',' + group_row + ');"><img src="<?php echo $placeholder; ?>" /></div></div>';
	}
	
	$('#tab-section-' + section_row + ' .slide-preview-inner').append(preview_layer);
	
	addScripts(section_row, group_row);
	
	group_row++;

}
function addScripts(section_row, group_row){
// Update image
$('#input-image-s' + section_row + '-g' + group_row + '-<?php echo $lang; ?>').change( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + ' img').attr("src", ("<?php echo $base_url; ?>" + 'image/' + $(this).val()));
});
// Make layers draggable
$('.preview-layer-holder.s' + section_row + '.g' + group_row + '').draggable({
stop: function() { 
$('#left-s' + section_row + '-g' + group_row + '-l<?php echo $lang; ?>').val(Math.round( $(this).position().left ));
$('#top-s' + section_row + '-g' + group_row + '-l<?php echo $lang; ?>').val(Math.round( $(this).position().top ));
}
});
// Change layer position
$('#left-s' + section_row + '-g' + group_row + '-l<?php echo $lang; ?>').change( function(){
$('.preview-layer-holder.s' + section_row + '.g' + group_row + '').css("left", $(this).val() + "px");
});
$('#top-s' + section_row + '-g' + group_row + '-l<?php echo $lang; ?>').change( function(){
$('.preview-layer-holder.s' + section_row + '.g' + group_row + '').css("left", $(this).val() + "px");
});
// Change z-index
$('#sort_order' + section_row + group_row + '').change( function(){
$('.preview-layer-holder.s' + section_row + '.g' + group_row + '').css("z-index", $(this).val());
});
// Change text
$('#description-s' + section_row + '-g' + group_row + '-<?php echo $lang; ?>').keyup( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').html( $(this).val() );
});
$('#button_text-s' + section_row + '-g' + group_row + '-<?php echo $lang; ?>').keyup( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + ' span').html( $(this).val() );
});
// Change font-family
$('#font-s' + section_row + '-g' + group_row + '').change( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').css("font-family", $(this).val());
});
// Font Size
$('#fontsize-s' + section_row + '-g' + group_row + '').change( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').css("font-size", $(this).val()).css("height", "auto");
});
// Font Weight
$('#font-weight-s' + section_row + '-g' + group_row + '').change( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').css("font-weight", $(this).val());
});
// Color picker
$('#color-s' + section_row + '-g' + group_row + '').colorpicker({
sliders: {
saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
}).on('changeColor.colorpicker', function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').css("color", $('#color-s' + section_row + '-g' + group_row + ' input').val());
});
// Bg Color Picker
$('#bg-s' + section_row + '-g' + group_row + '').colorpicker({
sliders: {
saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
}).on('changeColor.colorpicker', function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').css("background-color", $('#bg-s' + section_row + '-g' + group_row + ' input').val());
});
// Padding
$('#padding-s' + section_row + '-g' + group_row + '').change( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').css("padding", $(this).val()).css("height", "auto");
});
// Border radius
$('#radius-s' + section_row + '-g' + group_row + '').change( function(){
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').css("border-radius", $(this).val());
});
// Timer slider
$('#slider-s' + section_row + '-g' + group_row + '').slider({
range: true,
min: 0,
max: ( ($('#duration' + section_row + '').val() * 1000)),
step: 1,
values: [ 500 , ( ($('#duration' + section_row + '').val() * 1000) - 500) ],
slide: function(event, ui) {
$('#start-s' + section_row + '-g' + group_row + '').val(ui.values[0]);
$('#end-s' + section_row + '-g' + group_row + '').val(ui.values[1]);
}
});
$('#start-s' + section_row + '-g' + group_row + '').change(function() {
var $this = $(this);
$('#slider-s' + section_row + '-g' + group_row + '').slider("values", $this.data("index"), $this.val());
});
$('#end-s' + section_row + '-g' + group_row + '').change(function() {
var $this = $(this);
$('#slider-s' + section_row + '-g' + group_row + '').slider("values", $this.data("index"), $this.val());
});
// Change button class
$('#button_class-s' + section_row + '-g' + group_row + '').change(function() {
var $this = $(this);
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').alterClass( 'btn-*' );
$('.preview_single_layer.s' + section_row + '.g' + group_row + '').addClass( $this.val() );
});
// Fix Bootstrap Tooltip
$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
}


function addSlideScripts(section_row){
// Change layer background image
$('#input-image' + section_row).change( function(){
$('.slide-preview.s' + section_row).css('background-image', 'url("<?php echo $base_url; ?>/image/' + $(this).val() + '")');
});
// Change layer background color
$('#bg_color_' + section_row).colorpicker({
sliders: {
saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
}).on('changeColor.colorpicker', function(){
$('.slide-preview.s' + section_row).css("background-color", $('#bg_color_' + section_row +' input').val());
});


// Fix Bootstrap Tooltip
$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
}
	
function removeLayer(section_row, group_row){
if ($('#group-row-s' + section_row + '-g' + group_row).hasClass('active')) {
$('#group-row-s' + section_row + '-g' + group_row).remove();
$('.preview-layer-holder.s' + section_row + '.g' + group_row).remove();
$('#groups-' + section_row + ' .single-layer:first').addClass('active');
} else {
$('#group-row-s' + section_row + '-g' + group_row).remove();
$('.preview-layer-holder.s' + section_row + '.g' + group_row).remove();
}
deleted_layers++;
}

function layerNavigate(section_row, group_row) {
$('#groups-' + section_row + ' .single-layer').removeClass("active");
$('#group-row-s' + section_row + '-g' + group_row + '').addClass("active");
}
//--></script> 


<!-- Scripts -->
<script type="text/javascript"><!--
$('#section li:first-child a').tab('show');
//--></script>

<script type="text/javascript"><!--
$('#input-fullwidth').change( function(){
if ($(this).val() == "1") {
$('#content').addClass('fullwidth');
} else {
$('#content').removeClass('fullwidth');
}
});
<?php $section_row = 1; ?>
<?php foreach ($sections as $section) { ?>
$('#language<?php echo $section_row; ?> li:first-child a').tab('show');
$('#groups-<?php echo $section_row; ?> .single-layer:first').addClass('active');
// Change layer background image
$("#input-image<?php echo $section_row; ?>").change( function(){
$('.slide-preview.s<?php echo $section_row; ?>').css('background-image', 'url("<?php echo $base_url; ?>/image/' + $(this).val() + '")');
});
// Change layer background color
$('#bg_color_<?php echo $section_row; ?>').colorpicker({
sliders: {
saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
}).on('changeColor.colorpicker', function(){
$('.slide-preview.s<?php echo $section_row; ?>').css("background-color", $('#bg_color_<?php echo $section_row; ?> input').val());
});
<?php $group_row = 0; ?>
<?php foreach($section['groups'] as $group){ ?>
// Make layers draggable
$(".preview-layer-holder.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>").draggable({
    stop: function() { 
    $('#left-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-l<?php echo $lang; ?>').val(Math.round( $(this).position().left ));
	$('#top-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-l<?php echo $lang; ?>').val(Math.round( $(this).position().top ));
    }
});
// Change layer position
$('#left-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-l<?php echo $lang; ?>').change( function(){
$('.preview-layer-holder.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("left", $(this).val() + "px");
});
$('#top-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-l<?php echo $lang; ?>').change( function(){
$('.preview-layer-holder.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("top", $(this).val() + "px");
});
// Change image
$('#input-image-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $lang; ?>').change( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?> img').attr("src", ("<?php echo $base_url; ?>" + 'image/' + $(this).val()));
});
// Change text
$('#description-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $lang; ?>').keyup( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').html( $(this).val() );
});
$('#button_text-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>-<?php echo $lang; ?>').keyup( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?> span').html( $(this).val() );
});
// Change z-index
$('#sort_order<?php echo $section_row; ?><?php echo $group_row; ?>').change( function(){
$('.preview-layer-holder.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("z-index", $(this).val());
});
// Change font
$('#font-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').change( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("font-family", $(this).val());
});
// Color picker
$('#color-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').colorpicker({
sliders: {
saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
}).on('changeColor.colorpicker', function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("color", $('#color-s<?php echo $section_row; ?>-g<?php echo $group_row; ?> input').val());
});
// Bg Color Picker
$('#bg-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').colorpicker({
sliders: {
saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
}).on('changeColor.colorpicker', function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("background-color", $('#bg-s<?php echo $section_row; ?>-g<?php echo $group_row; ?> input').val());
});
// Font Size
$('#fontsize-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').change( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("font-size", $(this).val()).css("height", "auto");
});
// Font Weight
$('#font-weight-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').change( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("font-weight", $(this).val());
});
// Padding
$('#padding-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').change( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("padding", $(this).val()).css("height", "auto");
});
// Border radius
$('#radius-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').change( function(){
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').css("border-radius", $(this).val());
});
// Range Slider


$("#slider-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>").slider({
range: true,
min: 0,
max: ($('#duration<?php echo $section_row; ?>').val() * 1000),
step: 1,
values: [ <?php echo $section['groups'][$group_row]['start']; ?> , <?php echo $section['groups'][$group_row]['end']; ?> ],
slide: function(event, ui) {
$('#start-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').val(ui.values[0]);
$('#end-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>').val(ui.values[1]);
}
});
$("#start-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>").change(function() {
var $this = $(this);
$("#slider-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>").slider("values", $this.data("index"), $this.val());
});
$("#end-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>").change(function() {
var $this = $(this);
$("#slider-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>").slider("values", $this.data("index"), $this.val());
});
//Change button class
$("#button_class-s<?php echo $section_row; ?>-g<?php echo $group_row; ?>").change(function() {
var $this = $(this);
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').alterClass( 'btn-*' );
$('.preview_single_layer.s<?php echo $section_row; ?>.g<?php echo $group_row; ?>').addClass( $this.val() );
});

<?php $group_row++; ?>
<?php } ?>

<?php $section_row++; ?>
<?php } ?> 
//--></script>
<?php echo $footer; ?>