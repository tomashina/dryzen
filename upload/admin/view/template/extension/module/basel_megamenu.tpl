<?php echo $header; ?><?php echo $column_left;?>
<div id="content">
    <div class="page-header">
    <div class="container-fluid">
    
    <div class="pull-right">
    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-default"><i class="fa fa-reply"></i></a>
	</div>

    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
    </ul>
    </div>
    </div>
    <div class="container-fluid" id="megamenu">
        
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        
        <?php if ($success) {  ?>
        <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        
        <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bars"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
    	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        

        <div id="content">
            <div class="row">
                <div class="background clearfix">
                    <?php if(isset($moduleid) && $moduleid){?>
                    <div class="left col-md-5 col-xs-12 col-sm-6 ">
                        <?php echo $nestable_list; ?>
                        <div class="well">
                        <div class="row">
                        <div class="col-sm-6">
                        <a id="nestable-menu">
                        <button type="button" data-action="expand-all" class="btn btn-link"><?php echo $text_expand_all; ?></button>
                        <button type="button" data-action="collapse-all" class="btn btn-link"><?php echo $text_collapse_all; ?></button>
                        </a>
                        </div>
                        <div class="col-sm-6 text-right">
                        <a href="<?php echo $action; ?>&action=create" class="btn btn-sm btn-primary" ><i class="fa fa-plus"></i>&nbsp; <?php echo $text_creat_new_item; ?></a>
                        </div>
                        </div>
                        <div class="time">
                        <div id='sortDBfeedback'></div>
                        </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($action_type == 'create' || $action_type == 'edit') { ?>
                    <div class="right col-md-7 col-xs-12 col-sm-6">
                    
                    <h2>
                    <div class="buttons pull-right">
                                    <button type="submit" name="button-back" class="btn btn-default" value="" title="Configuration">Cancel</button>
                                    <?php if($action_type == 'create') { ?>
                                    <button type="submit" name="button-create" class="btn btn-primary" value="">Save</button>
                                    <?php } elseif ($action_type == 'edit') { ?>
                                    <button type="submit" name="button-edit" class="btn btn-primary" value="">Save</button>
                                    <?php } else { ?>
                                    <button type="submit" name="button-save" class="btn btn-primary" value="">Save</button>
                                    <?php } ?>
                            </div>
                    
                    <?php if($action_type == 'edit') { ?>
                            <?php echo $text_edit_item; ?><?php echo $_GET['edit']; ?>)
                            <input type="hidden" name="id" value="<?php echo $_GET['edit']; ?>" />
                            <?php } else { ?>
                            <?php echo $text_creat_new_item; ?>
                            <?php } ?>
                    
                    
                    </h2>
                    
                            
                            <input type="hidden" name="status" value="<?php echo $status; ?>">
                            
                            <div class="input clearfix">
                                    <p><?php echo $text_name; ?><span style="color:#F30">*</span></p>
                                    <?php
                                    $i = 0;
                                    foreach ($languages as $language) { $i++; ?>
                                         <input type="text" name="name[<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_description_name; ?>" id="input-head-name-<?php echo $language['language_id']; ?>" value="<?php echo isset($name[$language['language_id']]) ? $name[$language['language_id']] : ''; ?>" class="form-control <?php echo ($i>1) ? ' hide ' : ' first-name'; ?>" />
                                         <?php
                                         ?>
                                    <?php } ?>
                                    <select  class="form-control lang-select" id="language">
                                    <?php foreach ($languages as $language) { ?>
                                        
                                            <option value="<?php echo $language['language_id']; ?>"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></option>
                                        
                                    <?php } ?>
                                    </select>
                            </div>
                            
                            
                            
                            
                            <div class="input clearfix">
                            <p><?php echo $text_class_menu ?></p>
                                    <div class="list-language">
                                            <input type="text" class="form-control" name="class_menu" value="<?php echo $class_menu; ?>">
                                    </div>
                            </div>
                            
                            <div class="input clearfix">
                            <p><?php echo $entry_display_mobile_module; ?></p>
                            <select name="disp_mobile_item" class="form-control">
                                <?php
                                if ($disp_mobile_item) { ?>
                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="0"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                        	</div>
                            
                            <div class="input clearfix">
                                    <p>Link Target</p>
                                    <input type="text" class="form-control" value="<?php echo $link; ?>" name="link">
                            </div>
                           
                            
                            <div class="input clearfix">
                                    <p><?php echo $text_link_in_new_window; ?></p>
                                    <select class="form-control" name="new_window">
                                            <?php if($new_window == 1) { ?>
                                            <option value="0"><?php echo $text_disabled; ?></option>
                                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                            <?php } else { ?>
                                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                            <option value="1"><?php echo $text_enabled; ?></option>
                                            <?php } ?>
                                    </select>
                            </div>
                            
                            <div class="input clearfix">
                                    <p><?php echo $text_icon_font ?></p>
                                    <div class="list-language">
                                            <input type="text" class="form-control" name="icon_font" value="<?php echo $icon_font; ?>">
                                    </div>
                                    <span class="helper">Example: <i>fa fa-desktop</i> or <i>icon-heart</i> &nbsp;-&nbsp; 
                                    <a href="http://fontawesome.io/cheatsheet/" target="_blank">FontAwesome Icons</a> &nbsp;|&nbsp; 
                                    <a class="icon_list">Basel Icons</a>
                                    </span>
                            </div>
                            
                            
                            <div class="input clearfix">
                                <p><?php echo $text_label; ?></p>
                                <?php
                                $i = 0;
                                foreach ($languages as $language) { $i++; ?>
                                     <input type="text" name="description[<?php echo $language['language_id']; ?>]" placeholder="<?php echo $text_label; ?>" id="input-head-des-<?php echo $language['language_id']; ?>" value="<?php echo isset($description[$language['language_id']]) ? $description[$language['language_id']] : ''; ?>" class="form-control <?php echo ($i>1) ? ' hide ' : ' first-name'; ?>" /> 
                                     <?php
                                     ?>
                                <?php } ?>
                                <select  class="form-control lang-select" id="des_language">
                                  <?php foreach ($languages as $language) { ?>
                                    
                                            <option value="<?php echo $language['language_id']; ?>"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></option>
                                        
                                    <?php } ?>
                                </select>
                                <span class="helper">Example:<br /><i>&lt;i class=&quot;menu-tag sale&quot;&gt;SALE&lt;/i&gt;<br />&lt;i class=&quot;menu-tag new&quot;&gt;NEW&lt;/i&gt;</i></span>
                            </div>

                            <input type="hidden" name="item_type" id="item_type" value="<?php echo $item_type; ?>">

                            <?php if ($item_type) { ?>
                                <h4 class="button_parent_config active"><?php echo $text_parent_config; ?></h4>
                                <span class="h4_helper">(<?php echo $text_parent_item; ?>)</span>
                                <div id="text_parent_config" class="collapse in" aria-expanded="true">
                            <?php } else { ?>
                                <h4 class="button_parent_config"><?php echo $text_parent_config; ?></h4>
                                <span class="h4_helper">(<?php echo $text_parent_item; ?>)</span>
                                <div id="text_parent_config" class="collapse">
                            <?php } ?>
                            
                            
                            
                            <div class="input clearfix">
                            <p><?php echo $text_submenu_width; ?></p>
                            <input type="text" class="form-control" name="submenu_width" value="<?php echo $submenu_width; ?>">
                            <span class="helper">Enter: <b>full</b> to cteate a full width drop down</span>
                            </div>
                            
                            <div class="input clearfix">
                                    <p>Background Image</p>
                                    <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $src_icon; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>"  /></a>
                                    <input type="hidden" name="icon" value="<?php echo $icon; ?>" id="input-image" />
                            </div>
                            

                            <div class="input clearfix">
                                    <p><?php echo $text_position; ?></p>
                                    <select name="position" class="form-control">
                                    <?php if ($position == 'left top') { ?>
                                    <option value="left top" selected="selected">left top</option>
                                    <?php } else { ?>
                                    <option value="left top">left top</option>
                                    <?php } ?>
                                    <?php if ($position == 'left center') { ?>
                                    <option value="left center" selected="selected">left center</option>
                                    <?php } else { ?>
                                    <option value="left center">left center</option>
                                    <?php } ?>
                                    <?php if ($position == 'left bottom') { ?>
                                    <option value="left bottom" selected="selected">left bottom</option>
                                    <?php } else { ?>
                                    <option value="left bottom">left bottom</option>
                                    <?php } ?>
                                    <?php if ($position == 'right top') { ?>
                                    <option value="right top" selected="selected">right top</option>
                                    <?php } else { ?>
                                    <option value="right top">right top</option>
                                    <?php } ?>
                                    <?php if ($position == 'right center') { ?>
                                    <option value="right center" selected="selected">right center</option>
                                    <?php } else { ?>
                                    <option value="right center">right center</option>
                                    <?php } ?>
                                    <?php if ($position == 'right bottom') { ?>
                                    <option value="right bottom" selected="selected">right bottom</option>
                                    <?php } else { ?>
                                    <option value="right bottom">right bottom</option>
                                    <?php } ?>
                                    <?php if ($position == 'center top') { ?>
                                    <option value="center top" selected="selected">center top</option>
                                    <?php } else { ?>
                                    <option value="center top">center top</option>
                                    <?php } ?>
                                    <?php if ($position == 'center center') { ?>
                                    <option value="center center" selected="selected">center center</option>
                                    <?php } else { ?>
                                    <option value="center center">center center</option>
                                    <?php } ?>
                                    <?php if ($position == 'center bottom') { ?>
                                    <option value="center bottom" selected="selected">center bottom</option>
                                    <?php } else { ?>
                                    <option value="center bottom">center bottom</option>
                                    <?php } ?>
                                    </select>
                            </div>
                        

                        
                            </div>
                            
                            <?php if ($item_type) { ?>
                                <h4 class="button_content_config"><?php echo $text_content_config; ?></h4>
                                <span class="h4_helper">(<?php echo $text_content_item; ?>)</span>
                                <div id="text_content_config" class="collapse">
                            <?php } else { ?>
                                <h4 class="button_content_config active"><?php echo $text_content_config; ?></h4>
                                <span class="h4_helper">(<?php echo $text_content_item; ?>)</span>
                                <div id="text_content_config" class="collapse in" aria-expanded="true">
                            <?php } ?>
                            
                            <div class="input clearfix">
                            <p>Show Item Name</p>
                            <div class="list-language">
                                <select name="show_title" class="form-control">
                                <?php if ($show_title) { ?>
                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="0"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                            </div>
                            </div>
                        
                            <div class="input clearfix">
                            <p><?php echo $text_content_width; ?></p>
                            <select name="content_width" class="form-control">
                            <?php for($i=1; $i<13; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if($i == $content_width) { echo 'selected="selected"'; } ?>><?php echo $i; ?>/12</option>
                            <?php } ?>
                            </select>
                            </div>                           
                            
                            <div class="input clearfix">
                                    <p><?php echo $text_content_type; ?></p>
                                    <select name="content_type" class="form-control">
                                            <?php if($content_type != '0') { ?>
                                            <option value="0">HTML</option>
                                            <?php } else { ?>
                                            <option value="0" selected="selected">HTML</option>
                                            <?php } ?>
                                            <?php if($content_type != '1') { ?>
                                            <option value="1">Product</option>
                                            <?php } else { ?>
                                            <option value="1" selected="selected">Product</option>
                                            <?php } ?>
                                            <?php if($content_type != '2') { ?>
                                            <option value="2">Categories</option>
                                            <?php } else { ?>
                                            <option value="2" selected="selected">Categories</option>
                                            <?php } ?>
                                            <?php if($content_type != '4') { ?>
                                            <option value="4">Image</option>
                                            <?php } else { ?>
                                            <option value="4" selected="selected">Image</option>
                                            <?php } ?>

                                    </select>
                            </div>
                            
                    <!-------------- HTML ---------------->
                    <!------------------------------------>
                    
                    <div id="content_type0"<?php if($content_type != '0') { ?> style="display:none"<?php } ?> class="content_type content_type_html">
                    <legend>HTML</legend>
                    <div class="tab-pane">
                    <ul id="language" class="nav nav-tabs">
                    <?php foreach ($languages as $language) { ?>
                    <li>
                    <a data-toggle="tab" href="#content_html_<?php echo $language['language_id']; ?>">
                    <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?>
                    </a>
                    </li>
                    <?php } ?>
                    </ul>
                    <div class="tab-content">
                    <?php foreach ($languages as $language) { $lang_id = $language['language_id']; ?>
                    <div id="content_html_<?php echo $language['language_id']; ?>" class="content_html tab-pane">
                    <textarea name="content[html][text][<?php echo $language['language_id']; ?>]" id="content_html_text_<?php echo $language['language_id']; ?>" class="form-control content-block"><?php if(isset($content['html']['text'][$lang_id])) { echo $content['html']['text'][$lang_id]; } ?></textarea>
                    <a onclick="enable_editor('#content_html_text_<?php echo $language['language_id']; ?>');">Enable HTML Editor</a>
                    </div>
                    <?php } ?>
                    </div>
                    </div>
                    </div>

                    <!------------- Product -------------->
                    <!------------------------------------>
                    
                    <div id="content_type1"<?php if($content_type != '1') { ?> style="display:none"<?php } ?> class="content_type">
                    <legend>Product</legend>
                    <div class="input clearfix">
                    <p>Product:<br><span style="font-size:11px;color:#808080">(Autocomplete)</span></p>
                    <input type="hidden" name="content[product][id]" value="<?php echo (isset($content['product']['id'])) ? $content['product']['id'] : ''; ?>" />
                    <input type="text" id="product_autocomplete" class="form-control" name="content[product][name]" value="<?php echo (isset($content['product']['name'])) ? $content['product']['name'] : ''; ?>">
                    </div>
                    
                    <div class="input clearfix">
                    <p>Image Width (px)</p>
                    <input type="text" class="form-control" name="content[product][img_w]" value="<?php echo (isset($content['product']['img_w'])) ? $content['product']['img_w'] : '262'; ?>">
                    </div>
                    
                    <div class="input clearfix">
                    <p>Image Height (px)</p>
                    <input type="text" class="form-control" name="content[product][img_h]" value="<?php echo (isset($content['product']['img_h'])) ? $content['product']['img_h'] : '334'; ?>">
                    </div>
                    
                    </div>
                    
                    
                    <!-------------- Image --------------->
                    <!------------------------------------>
                    <div id="content_type4"<?php if($content_type != '4') { ?> style="display:none"<?php } ?> class="content_type">
                    <legend>Image</legend>
                    <div class="input clearfix">
                    <p>Image:</p>
                    <a href="" id="thumb-image-content" data-toggle="image" class="img-thumbnail"><img src="<?php echo (isset($content['image']['image_link'])) ? $content['image']['image_link'] : $src_image_default; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>"  /></a>
                    <input type="hidden" name="content[image][link]" value="<?php echo (isset($content['image']['link'])) ? $content['image']['link'] : $image_default; ?>" id="input-image-content" />
                    </div>
                    </div>
                    
                    
                    <!----------- Categories ------------->
                    <!------------------------------------>
                    <div id="content_type2"<?php if($content_type != '2') { ?> style="display:none"<?php } ?> class="content_type">
                    <legend>Categories</legend>
                    <div class="input clearfix">
                    <p>Add categories<br><span style="font-size:11px;color:#808080">(Autocomplete)</span></p>
                    <input type="text" id="categories_autocomplete" class="form-control" value="">
                    </div>
                    <div class="input clearfix">
                    <p>Sort categories</p>
                    <div class="cf nestable-lists">
                    <div class="dd" id="sort_categories">
                    <ol class="dd-list">
                    <?php echo $list_categories; ?>
                    </ol>
                    </div>
                    <input type="hidden" id="sort_categories_data" name="content[categories][categories]" value="<?php echo !is_array($content['categories']['categories']) ? $content['categories']['categories'] : '' ; ?>" />
                    </div>
                    </div>
                    <div class="input clearfix">
                    <p>Columns</p>
                    <select name="content[categories][columns]" class="form-control">
                    <?php if($content['categories']['columns'] != '1') { ?>
                    <option value="1">1</option>
                    <?php } else { ?>
                    <option value="1" selected="selected">1</option>
                    <?php } ?>
                    <?php if($content['categories']['columns'] != '2') { ?>
                    <option value="2">2</option>
                    <?php } else { ?>
                    <option value="2" selected="selected">2</option>
                    <?php } ?>
                    <?php if($content['categories']['columns'] != '3') { ?>
                    <option value="3">3</option>
                    <?php } else { ?>
                    <option value="3" selected="selected">3</option>
                    <?php } ?>
                    <?php if($content['categories']['columns'] != '4') { ?>
                    <option value="4">4</option>
                    <?php } else { ?>
                    <option value="4" selected="selected">4</option>
                    <?php } ?>
                    <?php if($content['categories']['columns'] != '5') { ?>
                    <option value="5">5</option>
                    <?php } else { ?>
                    <option value="5" selected="selected">5</option>
                    <?php } ?>
                    <?php if($content['categories']['columns'] != '6') { ?>
                    <option value="6">6</option>
                    <?php } else { ?>
                    <option value="6" selected="selected">6</option>
                    <?php } ?>
                    </select>
                    </div>
                    <div class="input clearfix" id="submenu-type">
                    <p>Submenu type</p>
                    <select name="content[categories][submenu]" class="form-control">
                    <?php if($content['categories']['submenu'] != '1') { ?>
                    <option value="1">Hover</option>
                    <?php } else { ?>
                    <option value="1" selected="selected">Hover</option>
                    <?php } ?>
                    <?php if($content['categories']['submenu'] != '2') { ?>
                    <option value="2">Visible</option>
                    <?php } else { ?>
                    <option value="2" selected="selected">Visible</option>
                    <?php } ?>
                    </select>
                    </div>
                    <div class="input clearfix" <?php if($content['categories']['submenu'] != '2') { echo 'style="display:none"'; } ?> id="submenu-columns">
                    <p>Submenu columns</p>
                    <select name="content[categories][submenu_columns]" class="form-control">
                    <?php if($content['categories']['submenu_columns'] != '1') { ?>
                    <option value="1">1</option>
                    <?php } else { ?>
                    <option value="1" selected="selected">1</option>
                    <?php } ?>
                    <?php if($content['categories']['submenu_columns'] != '2') { ?>
                    <option value="2">2</option>
                    <?php } else { ?>
                    <option value="2" selected="selected">2</option>
                    <?php } ?>
                    <?php if($content['categories']['submenu_columns'] != '3') { ?>
                    <option value="3">3</option>
                    <?php } else { ?>
                    <option value="3" selected="selected">3</option>
                    <?php } ?>
                    <?php if($content['categories']['submenu_columns'] != '4') { ?>
                    <option value="4">4</option>
                    <?php } else { ?>
                    <option value="4" selected="selected">4</option>
                    <?php } ?>
                    <?php if($content['categories']['submenu_columns'] != '5') { ?>
                    <option value="5">5</option>
                    <?php } else { ?>
                    <option value="5" selected="selected">5</option>
                    <?php } ?>
                    <?php if($content['categories']['submenu_columns'] != '6') { ?>
                    <option value="6">6</option>
                    <?php } else { ?>
                    <option value="6" selected="selected">6</option>
                    <?php } ?>
                    </select>
                    </div>
                    </div>
                                        
                      
                
                    </div>
                    </div>
                    <?php } else { ?>
                    
                    
                    <div class="right col-md-7 col-xs-12 col-sm-6">
                    <h2><?php echo $text_basic_configuration; ?>
                    <div class="buttons pull-right">
                    <button type="submit" name="button-save" class="btn btn-primary" value="" title="Save">Save</button>
                    </div>
                    </h2>

<input type="hidden" name="moduleid" value="<?php echo isset($_GET['module_id']) && !empty($_GET['module_id']) ? $_GET['module_id'] : $moduleid; ?>" />


<?php  if(!isset($_GET['module_id']) || empty($_GET['module_id'])){ ?>
    <div class="input clearfix">
            <p>Clone Items From Existing Module</p>
            <select name="import_module" class="form-control">
                    <?php if ($modules) { ?>
                        <?php foreach ($modules as $module) { ?>
                         <option value="<?php echo $module['module_id']; ?>"><?php echo $module['name']; ?></option>
                         <?php } ?>
                         <option value="0">Create new empty menu</option>
                    <?php } else { ?>
                    <option value="0">No existing modules found</option>
                    <?php } ?>
            </select>
    </div>
<?php } ?>

<div class="input clearfix">
        <p>Module Name</p>
        <input type="text" name="name" value="<?php echo $name; ?>"  id="input-name" class="form-control" />

</div>

<div class="input clearfix">
        <p><?php echo $text_status; ?></p>
        <select name="status" class="form-control">
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
<?php } ?>
</div>
</div>
</div>
<!-- End Content -->
</form>
</div>
</div>
</div>
<script type="text/javascript">
$('#language a:first').tab('show');
if($("input[name='use_cache']:radio:checked").val() == '0')
{
        $('#input-cache_time_form').hide();
}else
{
        $('#input-cache_time_form').show();
}
$("input[name='use_cache']").change(function(){
        val = $(this).val();
        if(val ==0)
        {
                $('#input-cache_time_form').hide();
        }else
        {
                $('#input-cache_time_form').show();
        }
});
$(document).ready(function() {
        $(".button_parent_config").click(function(){
            if($(this).hasClass('active')) {
			}
			else
                $(this).addClass('active'),
            	$("#text_parent_config").collapse('show'),
				$("#text_content_config").collapse('hide'),
				$(".button_content_config").removeClass('active'),
				$("#item_type").val('1');
        });

        $(".button_content_config").click(function(){
            if($(this).hasClass('active')) {
			} else
                $(this).addClass('active');
            	$("#text_parent_config").collapse('hide'),
				$("#text_content_config").collapse('show'),
				$(".button_parent_config").removeClass('active'),
				$("#item_type").val('0');
        });

        $('#nestable-menu').on('click', function(e)
        {
            var target = $(e.target),
                    action = target.data('action');
            if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
            }
        });

        $('#language').change(function(){
            var that = $(this), opt_select = $('option:selected', that).val() , _input = $('#input-head-name-'+opt_select);
            $('[id^="input-head-name-"]').addClass('hide');
            _input.removeClass('hide');
        });

        $('#head_name_language').change(function(){
            var that = $(this), opt_select = $('option:selected', that).val() , _input = $('#input-headname-'+opt_select);
            $('[id^="input-headname-"]').addClass('hide');
            _input.removeClass('hide');
        });

        $('#des_language').change(function(){
            var that = $(this), opt_select = $('option:selected', that).val() , _input = $('#input-head-des-'+opt_select);
            $('[id^="input-head-des-"]').addClass('hide');
            _input.removeClass('hide');
        });

        $('#navigation_language').change(function(){
            var that = $(this), opt_select = $('option:selected', that).val() , _input = $('#input-text-navigation-'+opt_select);
            $('[id^="input-text-navigation-"]').addClass('hide');
            _input.removeClass('hide');
        });

        $('#home_text_language').change(function(){
            var that = $(this), opt_select = $('option:selected', that).val() , _input = $('#input-home-text-'+opt_select);
            $('[id^="input-home-text-"]').addClass('hide');
            _input.removeClass('hide');
        });

        $("select[name=content_type]").change(function () {
                $("select[name=content_type] option:selected").each(function() {
                        $(".content_type").hide();
                        $("#content_type" + $(this).val()).show();
                });
        });
        $("#submenu-type").change(function () {
                $("#submenu-type option:selected").each(function() {
                        if($(this).val() == 2) {
                                $("#submenu-columns").show();
                        } else {
                                $("#submenu-columns").hide();
                        }
                });
        });
		$("#orientation_select").change(function () {
                $("#orientation_select option:selected").each(function() {
                        if($(this).val() == 1) {
                                $("#orientation_limit").show();
                        } else {
                                $("#orientation_limit").hide();
                        }
                });
        });
        $('li','.content_type_html').first().addClass('active');
        $('.tab-pane','.content_type_html .tab-content').first().addClass('active');
        

        $('#product_autocomplete').autocomplete({
                delay: 500,
                source: function(request, response) {
                        $.ajax({
                                url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $_GET['token']; ?>&filter_name=' + encodeURIComponent(request) ,
                                dataType: 'json',
                                success: function(json) {
                                        json.unshift({
                                                'product_id':  0,
                                                'name':  'None'
                                        });
                                        response($.map(json, function(item) {
                                                return {
                                                 label: item.name,
                                                 value: item.product_id
                                                }
                                        }));
                                }
                        });
                },
                
                select: function(event) {

                        $('#product_autocomplete').val(event.label);
                        $('input[name=\'content[product][id]\']').val(event.value);
                        return false;
                },
                focus: function(event) {
                        return false;
                }
                
        });

        


        $('#categories_autocomplete').autocomplete({
                delay: 500,
                source: function(request, response) {
                        $.ajax({
                                url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $_GET['token']; ?>&filter_name=' +  request,
                                dataType: 'json',
                                success: function(json) {
                                        json.unshift({
                                                'category_id':  0,
                                                'name':  'None'
                                        });
                                        response($.map(json, function(item) {
                                                return {
                                                        label: item.name,
                                                        value: item.category_id
                                                }
                                        }));
                                }
                        });
                },
                select: function(event) {
                        if(event.value > 0) {
                            $("#sort_categories > .dd-list").append('<li class="dd-item" data-id="' + event.value + '" data-name="' + event.label + '"><a class="fa fa-times"></a><div class="dd-handle">' + event.label + '</div></li>');
                        }
                        updateOutput2($('#sort_categories').data('output', $('#sort_categories_data')));
                        $( "#sort_categories .fa-times" ).on( "click", function() {
                            $(this).parent().remove();
                            updateOutput2($('#sort_categories').data('output', $('#sort_categories_data')));
                        });
                        return false;
                },
                focus: function(event) {
                        return false;
                }
        });

        function lagXHRobjekt() {
                var XHRobjekt = null;

                try {
                        ajaxRequest = new XMLHttpRequest(); // Firefox, Opera, ...
                } catch(err1) {
                        try {
                                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP"); // Noen IE v.
                        } catch(err2) {
                                try {
                                        ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP"); // Noen IE v.
                                } catch(err3) {
                                        ajaxRequest = false;
                                }
                        }
                }
                return ajaxRequest;
        }


        function menu_updatesort(jsonstring) {
                mittXHRobjekt = lagXHRobjekt();

                if (mittXHRobjekt) {
                        mittXHRobjekt.onreadystatechange = function() {
                                if(ajaxRequest.readyState == 4){
                                        var ajaxDisplay = document.getElementById('sortDBfeedback');
                                        ajaxDisplay.innerHTML = ajaxRequest.responseText;
                                }
                        }
						ajaxRequest.open("GET", "index.php?route=extension/module/basel_megamenu&token=<?php echo $_GET['token']; ?>&jsonstring=" + encodeURIComponent(jsonstring), true);
                                
                        ajaxRequest.setRequestHeader("Content-Type", "application/json");
                        ajaxRequest.send(null);
                }
        }

        var updateOutput = function(e)
        {
                var list   = e.length ? e : $(e.target),
                        output = list.data('output');

                if (window.JSON) {
                        menu_updatesort(window.JSON.stringify(list.nestable('serialize')));
                } else {
                        alert('JSON browser support required for this demo.');
                }
        };

        $('#nestable').nestable({
                group: 1,
                maxDepth: 2
        }).on('change', updateOutput);

        updateOutput($('#nestable').data('output', $('#nestable-output')));

        var updateOutput2 = function(e)
        {
                var list   = e.length ? e : $(e.target),
                        output = list.data('output');
                if (window.JSON && typeof(output)!= 'undefined' ) {
                        output.val(window.JSON.stringify(list.nestable('serialize')));
                }
        };
        $('#sort_categories').nestable({
               group: 1,
                maxDepth: 3
        }).on('change', updateOutput2);

                updateOutput2($('#sort_categories').data('output', $('#sort_categories_data')));

       $( "#sort_categories .fa-times" ).on( "click", function() {
               $(this).parent().remove();
               updateOutput2($('#sort_categories').data('output', $('#sort_categories_data')));
       });
        
		
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
			html += '        <h4 class="modal-title">Icon List Preview</h4>';
			html += '      </div>';
			html += '      <div class="modal-body"><iframe src="view/javascript/basel/icons_list/icon_list.html" width="1240" height="560" frameborder="0" allowtransparency="true"></iframe></div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';
			$('body').append(html);
			$('#modal-icons').modal('show');
		}
	});
});

        
});
</script>
<script type="text/javascript">
function enable_editor(textarea) {
	$(textarea).summernote({
			disableDragAndDrop: true,
			emptyPara: '',
			height: 300,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline','italic', 'clear']],
				['fontname', ['fontname']],
				['color', ['color']],
				['para', ['ul', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'image', 'video']],
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
										
										$(textarea).summernote('insertImage', $(this).attr('href'));
																	
										$('#modal-image').modal('hide');
									});
								}});}});
				return button.render();
		}}});
}
</script>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php echo $footer; ?>