<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-layout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-layout" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <table id="route" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $entry_store; ?></td>
                <td class="text-left"><?php echo $entry_route; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $route_row = 0; ?>
              <?php foreach ($layout_routes as $layout_route) { ?>
              
              <?php if (($layout_route['route'] == 'product/category') || ($layout_route['route'] == 'product/manufacturer') || ($layout_route['route'] == 'product/special') || ($layout_route['route'] == 'product/search')) { ?>
              <style>.category_above_products {display:block !important}</style>
              <?php } ?>
              
              <tr id="route-row<?php echo $route_row; ?>">
                <td class="text-left"><select name="layout_route[<?php echo $route_row; ?>][store_id]" class="form-control">
                    <option value="0"><?php echo $text_default; ?></option>
                    <?php foreach ($stores as $store) { ?>
                    <?php if ($store['store_id'] == $layout_route['store_id']) { ?>
                    <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
                <td class="text-left"><input type="text" name="layout_route[<?php echo $route_row; ?>][route]" value="<?php echo $layout_route['route']; ?>" placeholder="<?php echo $entry_route; ?>" class="form-control" /></td>
                <td class="text-left"><button type="button" onclick="$('#route-row<?php echo $route_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $route_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2"></td>
                <td class="text-left"><button type="button" onclick="addRoute();" data-toggle="tooltip" title="<?php echo $button_route_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </tfoot>
          </table>
          
          <?php $module_row = 0; ?>
          
          <div id="module" class="well">
            
            <div class="row">
            	<div class="col-sm-12">
              <h4>Top</h4>
              <div class="well well-white">
              <div class="row top">
              <div class="col-sm-12">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'top') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-12">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="top"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('top');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
              </div>
            </div>
            
            
            <div class="row">

            <div class="col-sm-3">
            <h4>Column Left</h4>
              <div class="well well-white">
              <div class="row column_left">
              <div class="col-sm-12">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'column_left') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-12">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="column_left"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('column_left');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
            </div>
            
            
            <div class="col-sm-6">
              <h4>Content Top</h4>
              <div class="well well-white">
              <div class="row content_top">
              <div class="col-sm-12">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'content_top') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-12">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="content_top"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('content_top');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
              
              
              <div class="category_above_products" style="display:none">
			  <h4>Above Product List</h4>
              <div class="well well-white">
              <div class="row cat_top">
              <div class="col-sm-12">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'cat_top') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-12">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="cat_top"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('cat_top');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
              </div>
            
            
            
              <h4>Content Bottom</h4>
              <div class="well well-white">
              <div class="row content_bottom">
              <div class="col-sm-12">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'content_bottom') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-12">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="content_bottom"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('content_bottom');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
              
            </div>
            
            <div class="col-sm-3">
              <h4>Column Right</h4>
              <div class="well well-white">
              <div class="row column_right">
              <div class="col-sm-12">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'column_right') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-12">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="column_right"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('column_right');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
            </div>
            </div>
            
            <div class="row">
            	<div class="col-sm-12">
              <h4>Bottom (50% width)</h4>
              <div class="well well-white">
              <div class="row bottom_half">
              <div class="col-sm-6">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <div class="col-sm-6">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'bottom_half') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-6">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="bottom_half"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('bottom_half');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
              </div>
            </div>
            
            
            <div class="row">
             <div class="col-sm-12">
              <h4>Bottom</h4>
              <div class="well well-white">
              <div class="row bottom">
              <div class="col-sm-12">
              <table class="heading" style="width:100%">
                <td><?php echo $entry_module; ?></td>
                <td width="98">Sort order</td>
              </table>
              </div>
              <?php foreach ($layout_modules as $layout_module) if ($layout_module['position'] == 'bottom') { ?>
              <div id="module-row<?php echo $module_row; ?>" class="col-sm-12">
              <div class="well module">
                <table style="width:100%">
                <td>
                <select name="layout_module[<?php echo $module_row; ?>][code]" class="form-control">
                    <?php foreach ($extensions as $extension) { ?>
                    <?php if (!$extension['module']) { ?>
                    <?php if ($extension['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $extension['code']; ?>" selected="selected"><?php echo $extension['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $extension['code']; ?>"><?php echo $extension['name']; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                    <optgroup label="<?php echo $extension['name']; ?>">
                    <?php foreach ($extension['module'] as $module) { ?>
                    <?php if ($module['code'] == $layout_module['code']) { ?>
                    <option value="<?php echo $module['code']; ?>" selected="selected"><?php echo $module['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $module['code']; ?>"><?php echo $module['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                    </optgroup>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="layout_module[<?php echo $module_row; ?>][position]" value="bottom"  />
                 </td>
                <td width="50" style="padding-left:10px">
               <input type="text" name="layout_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $layout_module['sort_order']; ?>"  class="form-control" />
               </td>
               <td align="right" width="48">
                <button type="button" onclick="$('#module-row<?php echo $module_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </table>
              </div>
              </div>
              <?php $module_row++; ?>
              <?php } ?>
              </div>
              <button type="button" onclick="addModule('bottom');" class="btn btn-primary btn-block"><?php echo $button_module_add; ?></button>
              </div>
              </div>
            </div>
            
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
var route_row = <?php echo $route_row; ?>;

function addRoute() {
	html  = '<tr id="route-row' + route_row + '">';
	html += '  <td class="text-left"><select name="layout_route[' + route_row + '][store_id]" class="form-control">';
	html += '  <option value="0"><?php echo $text_default; ?></option>';
	<?php foreach ($stores as $store) { ?>
	html += '<option value="<?php echo $store['store_id']; ?>"><?php echo addslashes($store['name']); ?></option>';
	<?php } ?>   
	html += '  </select></td>';
	html += '  <td class="text-left"><input type="text" name="layout_route[' + route_row + '][route]" value="" placeholder="<?php echo $entry_route; ?>" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#route-row' + route_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	$('#route tbody').append(html);
	route_row++;
}

var module_row = <?php echo $module_row; ?>;
function addModule(position) {
	if (position == 'bottom_half') {
	html  = '<div id="module-row' + module_row + '" class="col-sm-6">';
	} else {
	html  = '<div id="module-row' + module_row + '" class="col-sm-12">';	
	}
	html += '<div class="well module">';
	html += '<table style="width:100%">';
	html += '  <td><select name="layout_module[' + module_row + '][code]" class="form-control">';
	<?php foreach ($extensions as $extension) { ?>    
	<?php if (!$extension['module']) { ?>
	html += '    <option value="<?php echo $extension['code']; ?>"><?php echo addslashes($extension['name']); ?></option>';
	<?php } else { ?>
	html += '    <optgroup label="<?php echo addslashes($extension['name']); ?>">';
	<?php foreach ($extension['module'] as $module) { ?>
	html += '      <option value="<?php echo $module['code']; ?>"><?php echo addslashes($module['name']); ?></option>';
	<?php } ?>
	html += '    </optgroup>';
	<?php } ?>
	<?php } ?>
    html += '</select><input type="hidden" name="layout_module[' + module_row + '][position]" value="' + position + '" /></td>';
	html += '<td width="50" style="padding-left:10px">';
	html += '<input type="text" name="layout_module[' + module_row + '][sort_order]" value=""  class="form-control" />';
	html += '</td>';
	html += '<td align="right" width="48">';
	html += '<button type="button" onclick="$(\'#module-row' + module_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>';
	html += '</td>';
	html += '</table>';
	html += '</div>';
	html += '</div>';
	$('.row.' + position).append(html);
	module_row++;
}
//--></script>
<style>
.well h4 {
	font-size:16px;
}
.well-white {
	background:#ffffff;
	padding:15px;
}
.well-white .well.module {
	margin-bottom:10px;
	padding:8px;
}
.well-white .heading {
	font-size:11px;
	border-bottom:1px solid #eeeeee;
	margin-bottom:10px;
	font-weight:bold;
}
.well-white .heading td {
	padding-bottom:3px;
}
</style>
</div>
<?php echo $footer; ?>