<?php echo $header; ?><?php echo $column_left; ?>
<!--Main Content block start-->

<div id="content">
  <!--Header Start-->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-language" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i> SAVE</button>
		<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#add-links"><i class="fa fa-plus" aria-hidden="true"></i> Add Links</button>
		<button type="button" class="btn btn-danger" id="delete"><i class="fa fa-trash-o"></i> DELETE</button>
		<a href="https://www.huntbee.com/documentation/docs/seo-broken-link-manager/" target="_blank" class="btn btn-success" data-toggle="tooltip" title="Documentation"><i class="fa fa-book"></i></a>
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
  <!--Header End-->
  <!--Container 1 start -->
  <div class="container-fluid">
    <!--Start - Error / Success Message if any -->
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <!--End - Error / Success Message if any -->
	<div id="msgoutput" style="text-align:center;"></div>
	
    <!--Panel Content Start-->
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
			<?php if ($stores) { ?>
			<div class="pull-right">
			<select id="store">
				<option value="0" <?php echo ($store_id == 0)?'selected':''; ?>>Default Store</option>
				<?php foreach ($stores as $store) { ?>
					<option value="<?php echo $store['store_id']; ?>" <?php echo ($store_id == $store['store_id'])?'selected':''; ?>><?php echo $store['name']; ?></option>
				<?php } ?>
			</select>
			</div>
			<?php } ?>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language" class="form-horizontal">
			<!--Tabs UL Starts-->
			<ul class="nav nav-tabs" id="tabs">
                <li class="active"><a href="#tab-broken" data-toggle="tab"><i class="fa fa-chain-broken" aria-hidden="true"></i> <?php echo $tab_broken; ?></a></li>
				<li><a href="#tab-redirect" data-toggle="tab"><i class="fa fa-share" aria-hidden="true"></i> <?php echo $tab_redirect; ?></a></li>
				<li><a href="#tab-setup" data-toggle="tab"><i class="fa fa-cogs" aria-hidden="true"></i> <?php echo $tab_setup; ?></a></li>
				<li><a href="#tab-keyword" data-toggle="tab"><i class="fa fa-tags" aria-hidden="true"></i> <?php echo $tab_keyword; ?></a></li>
				<li><a href="#tab-replace" data-toggle="tab"><i class="fa fa-retweet" aria-hidden="true"></i> <?php echo $tab_replace; ?></a></li>
				<li><a href="#tab-templates" data-toggle="tab"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <?php echo $tab_templates; ?></a></li>
				<li><a href="#tab-tools" data-toggle="tab"><i class="fa fa-wrench" aria-hidden="true"></i> <?php echo $tab_tools; ?></a></li>
	          </ul>
			  <!--Tabs UL Ends-->
			  <div class="tab-content">
			  	<!--Broken Links-->
			  	<div class="tab-pane active" id="tab-broken">
					
					<div class="well">
						<div class="form-group">
							<div class="col-sm-3">
								<label class="control-label">Broken link added by</label>
								<select class="form-control" name="hb_brokenlinks_sauthor">
								<option value="0" <?php echo ($hb_brokenlinks_sauthor == "0")? 'selected':''; ?>>All Broken Links</option>
								<option value="3" <?php echo ($hb_brokenlinks_sauthor == "3")? 'selected':''; ?>>Only show links added by system</option>
								<option value="2" <?php echo ($hb_brokenlinks_sauthor == "2")? 'selected':''; ?>>Only show links added by admin</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label">Redirect URL Value</label>
								<select class="form-control" name="hb_brokenlinks_sredirect">
								<option value="0" <?php echo ($hb_brokenlinks_sredirect == "0")? 'selected':''; ?>>Show All</option>
								<option value="1" <?php echo ($hb_brokenlinks_sredirect == "1")? 'selected':''; ?>>Blank Redirect URL</option>
								<option value="2" <?php echo ($hb_brokenlinks_sredirect == "2")? 'selected':''; ?>>Filled Redirect URL</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label">Sorting factor</label>
								<select class="form-control" name="hb_brokenlinks_ssort">
								<option value="date_modified" <?php echo ($hb_brokenlinks_ssort == "date_modified")? 'selected':''; ?>>Last modified date</option>
								<option value="date_added" <?php echo ($hb_brokenlinks_ssort == "date_added")? 'selected':''; ?>>Date Added</option>
								<option value="hits" <?php echo ($hb_brokenlinks_ssort == "hits")? 'selected':''; ?>>Hits</option>
								<option value="redirect_hits" <?php echo ($hb_brokenlinks_ssort == "redirect_hits")? 'selected':''; ?>>Redirected Hits</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label">Order by</label>
								<select class="form-control" name="hb_brokenlinks_sorder">
								<option value="DESC" <?php echo ($hb_brokenlinks_ssort == "DESC")? 'selected':''; ?>>Descending</option>
								<option value="ASC" <?php echo ($hb_brokenlinks_ssort == "ASC")? 'selected':''; ?>>Ascending</option>
								</select>
							</div>	
					   </div>
					</div>
					<h3>List of detected Broken Links (Page not found / 404 Errors)</h3>
					
					<div class="input-group" style="margin-bottom:10px;">
						<input type="text" id="search-brokenlink-value" onkeyup="searchBrokenLink();" class="form-control" placeholder="Search Link">
						<span class="input-group-addon btn" id="search-brokenlink-button" onclick="searchBrokenLink();"><i class="fa fa-search"></i></span>
					</div>
					<div id="broken-links-block"></div>
				</div>
				
				<!--REDIRECTS LINKS-->
				<div class="tab-pane" id="tab-redirect">
					<h3>Page Redirect Links (200 <i class="fa fa-long-arrow-right" aria-hidden="true"></i> 301 / 302 / 307)</h3>
					
					<div class="input-group" style="margin-bottom:10px;">
						<input type="text" id="search-commonlink-value" onkeyup="searchCommonLink();" class="form-control" placeholder="Search Link">
						<span class="input-group-addon btn" id="search-commonlink-button" onclick="searchCommonLink();"><i class="fa fa-search"></i></span>
					</div>
					<div id="redirect-links-block"></div>
				</div>
				
				<!--SETUP-->
				<div class="tab-pane" id="tab-setup">
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $column_smart_url; ?></label>
						<div class="col-sm-9">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_brokenlinks_smarturl" class="form-control" value="1" <?php echo ($hb_brokenlinks_smarturl == '1')? 'checked':''; ?> />
						</div>
				   </div>
				   
				   <div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $column_keyword_url; ?></label>
						<div class="col-sm-9">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_brokenlinks_keywordurl" class="form-control" value="1" <?php echo ($hb_brokenlinks_keywordurl == '1')? 'checked':''; ?> />
						</div>
				   </div>
				   
				   <div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $column_replacer; ?></label>
						<div class="col-sm-9">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_brokenlinks_replacer" class="form-control" value="1" <?php echo ($hb_brokenlinks_replacer == '1')? 'checked':''; ?> />
						</div>
				   </div>
				   
				   <div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $column_default_url; ?></label>
						<div class="col-sm-9">
							<input type="text" name="hb_brokenlinks_defaulturl" class="form-control" value="<?php echo $hb_brokenlinks_defaulturl; ?>"  />
						</div>
				   </div>
				   
				   <div class="form-group">
		                <label class="col-sm-3 control-label"><?php echo $column_redirect_type; ?></label>
		                <div class="col-sm-9">
							<select class="form-control" name="hb_brokenlinks_rtype">
                            <option value="301" <?php echo ($hb_brokenlinks_rtype == "301")? 'selected':''; ?>>Moved Permanently  [Recommended]</option>
                            <option value="302" <?php echo ($hb_brokenlinks_rtype == "302")? 'selected':''; ?>>Found</option>
                            <option value="307" <?php echo ($hb_brokenlinks_rtype == "307")? 'selected':''; ?>>Moved Temporarily</option>
                            </select>	                
                         </div>
		         	</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $column_auto_delete; ?></label>						
						<div class="col-sm-9">
							<div class="input-group">
							  <div class="input-group-addon">DELETE BROKEN LINK RECORDS OLDER THAN </div>
							  <input type="number" name="hb_brokenlinks_adel_days" class="form-control text-right col-sm-2" value="<?php echo $hb_brokenlinks_adel_days; ?>"  />
							  <div class="input-group-addon"> DAYS AND HIT COUNT HAS VALUE LESSER THAN </div>
							  <input type="number" name="hb_brokenlinks_adel_count" class="form-control text-right" value="<?php echo $hb_brokenlinks_adel_count; ?>"  />
							</div>
						</div>
				   </div>
					
					<div class="form-group">
		                <label class="col-sm-3 control-label"><?php echo $column_query_exclude; ?></label>
		                <div class="col-sm-9">
							<input type="text" name="hb_brokenlinks_excludequery" value="<?php echo $hb_brokenlinks_excludequery; ?>" class="form-control" /><br>
		                </div>
					</div>
					
					<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $column_error_exclude; ?></label>
							<div class="col-sm-9">
							  <textarea class="form-control" rows="5" cols="60" name="hb_brokenlinks_excludeterms"><?php echo $hb_brokenlinks_excludeterms; ?></textarea><br>
							</div>
					 </div>
				
					 <div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $column_ignore_ip; ?></label>
							<div class="col-sm-9">
							  <textarea class="form-control" rows="5" cols="60" name="hb_brokenlinks_ignoreip"><?php echo $hb_brokenlinks_ignoreip; ?></textarea><br>
							</div>
					 </div>
					
					 <div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $column_ignore_agent; ?></label>
							<div class="col-sm-9">
							  <textarea class="form-control" rows="5" cols="60" name="hb_brokenlinks_ignoreagents"><?php echo $hb_brokenlinks_ignoreagents; ?></textarea><br>
							</div>
					 </div>
				   
				</div>
				
				<!--KEYWORD-->
				<div class="tab-pane" id="tab-keyword">
					<h3>Set Redirect Path automatically for broken links based on selective keywords</h3>
						<div class="form-group">
					 		<div class="col-sm-10">
								<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#keyword-modal"><i class="fa fa-plus" aria-hidden="true"></i> Add Keyword</button>
							</div>
						</div>
					 <div id="keywords-block"></div>
				</div>
				
				<!--KEYWORD-->
				<div class="tab-pane" id="tab-replace">
					<h3>Replaces a particular matched string in URL with the given string and then redirects</h3>
						<div class="form-group">
					 		<div class="col-sm-10">
								<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#replace-modal"><i class="fa fa-plus" aria-hidden="true"></i> Add String</button>
							</div>
						</div>
					 <div id="replace-block"></div>
				</div>
				
				<!--PAGE DESIGN-->  
				<div class="tab-pane" id="tab-templates">
					<div class="form-group">
						<span class="col-sm-2 control-label"><?php echo $column_enable_page; ?></span>
						<div class="col-sm-10">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_brokenlinks_enablepage" class="form-control" value="1" <?php echo ($hb_brokenlinks_enablepage == 1)? 'checked':''; ?> />
						</div>
					</div>
						
					<div class="form-group page-designer">
						<span class="col-sm-2 control-label"><?php echo $column_page_designer; ?></span> 
						<div class="col-sm-10">
							<ul class="nav nav-tabs" id="languages">
							<?php foreach ($languages as $language) { ?>
							<li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php echo $language['name']; ?></a></li>
							<?php } ?>
							</ul>
						
							<div class="tab-content"> <!-- language tab content -->
							<?php foreach ($languages as $language) { ?>
							<div class="tab-pane" id="language<?php echo $language['language_id']; ?>">	
							<div class="form-group">
									<div class="col-sm-12">
										<textarea name="hb_brokenlinks_page<?php echo $language['language_id']; ?>" id="page<?php echo $language['language_id']; ?>"><?php echo $hb_brokenlinks_page[$language['language_id']]; ?></textarea>
									</div>
							</div>
							</div>
							<?php } ?>
							</div> <!-- language tab content end-->	
						</div>
					</div>
							
				</div>
				
				<!--TOOLS-->
				<div class="tab-pane" id="tab-tools">
					<div class="table-responsive">
                        <table class="table table-bordered table-hover">
                        	<tr>
                            	<td><?php echo $tool_redirect_update; ?></td>
                                <td align="center">
								<input type="text" class="form-control" id="old_url" placeholder="Existing Redirect URL"><i class="fa fa-angle-double-down"></i><br>
								<input type="text" class="form-control" placeholder="New Redirect URL" id="new_url"></td>
                                <td><button type="button" onClick="bulk_update_redirect();"  class="btn btn-primary">UPDATE</button></td>
                            </tr>
                            <tr>
                            	<td><?php echo $tool_type_update; ?></td>
                                <td align="center">
                                <select name="type" class="form-control" id="old_type">
                                        <option value="301" >301 Moved Permanently</option>
                                        <option value="302" >302 Found</option>
                                        <option value="307" >307 Moved Temporarily</option>
                                 </select>
                                
                                <i class="fa fa-angle-double-down"></i><br>
                                <select name="type" class="form-control" id="new_type">
                                        <option value="301" >301 Moved Permanently</option>
                                        <option value="302" >302 Found</option>
                                        <option value="307" >307 Moved Temporarily</option>
                                 </select>
                                </td>
                                <td><button type="button" onClick="update_type();"  class="btn btn-primary">UPDATE</button></td>
                            </tr>
                            <tr>
                            	<td><?php echo $tool_assign_default; ?></td>
                                <td><?php echo $hb_brokenlinks_defaulturl; ?></td>
                                <td><button type="button" onClick="update_default();"  class="btn btn-primary" <?php if(strlen($hb_brokenlinks_defaulturl)< 4) { echo 'disabled';} ?>>UPDATE</button></td>
                            </tr>
                            <tr>
                            	<td colspan="2"><?php echo $tool_reset; ?></td>
                                <td><button type="button" onClick="reset_all();" class="btn btn-danger">DELETE</button></td>
                            </tr>
                        </table>
                    </div> 
				</div>
				
			  </div><!--tab-content block end-->
        </form>
		<!--ADD NEW START-->
		<div class="modal fade" id="add-links" tabindex="-1" role="dialog" aria-labelledby="add-linksLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Redirect Path</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label class="control-label"><?php echo $text_error_url; ?><span data-toggle="tooltip" title="<?php echo $text_error_url_help; ?>"></span></label>
					<textarea class="form-control" id="error_url"></textarea>
				  </div>
				  <div class="form-group">
					<label class="control-label"><?php echo $text_redirect_url; ?></label>
					<input type="text" class="form-control" id="redirect_url">
				  </div>
				  <div class="form-group">
					<label class="control-label"><?php echo $text_redirect_type; ?></label>
					<select id="redirect_type" class="form-control" id="redirect_type">
                        <option value="301" >301 Moved Permanently</option>
                        <option value="302" >302 Found</option>
                        <option value="307" >307 Moved Temporarily</option>
                 	</select>
				  </div>
				  <div class="form-group">
					<label class="control-label"><?php echo $text_redirect_author; ?></label>
					<select id="redirect_author" class="form-control" id="redirect_author">
					<option value="0">--PLEASE SELECT--</option>
                    <option value="1">General Redirect (200 Redirect)</option>
                    <option value="2">Broken Link Redirect (404 Redirect)</option>
                 	</select>
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add-links-entry">ADD ENTRY</button>
			  </div>
			  <div id="addentry-result" style="text-align:center;"></div>
			</div>
		  </div>
		</div>
		<!--ADD NEW END-->
		<!--KEYWORD BASED URL START-->
		<div class="modal fade" id="keyword-modal" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Redirect Path based on Keyword in the Broken Link</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label class="control-label">Keyword</label>
					<input type="text" class="form-control" id="keyword-term">
				  </div>
				  <div class="form-group">
					<label class="control-label">Redirect URL</label>
					<input type="text" class="form-control" id="keyword-redirect-url">
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add-keyword-entry">ADD</button>
			  </div>
			  <div id="addkeyword-result" style="text-align:center;"></div>
			</div>
		  </div>
		</div>
		<!--KEYWORD BASED URL END-->
		<!--URL REPLACER START-->
		<div class="modal fade" id="replace-modal" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add matching string and replace string</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label class="control-label">Match String</label>
					<input type="text" class="form-control" id="match-string">
				  </div>
				  <div class="form-group">
					<label class="control-label">Replace String</label>
					<input type="text" class="form-control" id="replace-string">
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add-replacer-entry">ADD</button>
			  </div>
			  <div id="replacer-result" style="text-align:center;"></div>
			</div>
		  </div>
		</div>
		<!--URL REPLACER END-->
		<!--REFERRER MODAL START-->
		<div class="modal fade" id="referrer-modal" tabindex="-1" role="dialog">
		  <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">REFERRERS</h4>
			  </div>
			  <div class="modal-body">
			  		<div id="referrer-block"></div>
			  </div>
			</div>
		  </div>
		</div>
		<!--REFERRER MODAL END-->
		
      </div>
    </div>
    <!--Panel Content End-->
    <!--Huntbee copyrights-->
    <div class="container-fluid">
      <center>
        <span class="help"><?php echo $heading_title; ?> - <?php echo $extension_version;?> &copy; <a href="https://www.huntbee.com/">WWW.HUNTBEE.COM</a> | <a href="https://www.huntbee.com/get-support">SUPPORT</a> | <a href="https://www.huntbee.com/documentation/docs/seo-broken-link-manager/" target="_blank">DOCUMENTATION</a></span>
      </center>
    </div>
    <!--Huntbee copyrights end-->
  </div>
  <!--Container 1 start -->
</div>
<!--Main Content block end-->
<style type="text/css">
.pr_error,.pr_info,.pr_infos,.pr_success,.pr_warning{margin:10px 0;padding:12px}.pr_info{color:#00529B;background-color:#BDE5F8}.pr_success{color:#4F8A10;background-color:#DFF2BF}.pr_warning{color:#9F6000;background-color:#FEEFB3}.pr_error{color:#D8000C;background-color:#FFBABA}.pr_error i,.pr_info i,.pr_success i,.pr_warning i{margin:10px 0;vertical-align:middle}
</style>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>

<script type="text/javascript"><!--
	$('#languages a:first').tab('show');
//--></script>
<script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
CKEDITOR.replace("page<?php echo $language['language_id']; ?>", {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
<?php } ?>
//--></script>
<script type="text/javascript">
$(document).ready(function() {
	loadreports();
	if ($('input[name="hb_brokenlinks_enablepage"]').is(':checked')){
		$('.page-designer').show();
	}else{
		$('.page-designer').hide();
	}
});
</script>
<script type="text/javascript">
function loadreports(){
	$('#broken-links-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#broken-links-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/brokenlinks&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
	$('#redirect-links-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#redirect-links-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/pageredirects&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
	$('#keywords-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#keywords-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/keywords&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
	$('#replace-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#replace-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/url_replacer&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
}

$('#broken-links-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#broken-links-block').load(this.href);
});

$('#redirect-links-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#redirect-links-block').load(this.href);
});



$('input[name="hb_brokenlinks_enablepage"]').on('change', function() {
	 if(this.checked) {
        $('.page-designer').show();
    }else{
		$('.page-designer').hide();
	}
});
</script>

<script type="text/javascript">
$('#add-links-entry').on('click', function() {
	$('#addentry-result').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/addlinks&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {links : $('#error_url').val(), redirect : $('#redirect_url').val(), response : $('#redirect_type').val(), type : $('#redirect_author').val()},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#addentry-result').html('<div class="pr_success">'+json['success']+'</div>');
					  loadreports();
				}
				if (json['warning']) {
					  $('#addentry-result').html('<div class="pr_error">'+json['warning']+'</div>');
				}
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });	
});

$('#add-keyword-entry').on('click', function() {
	$('#addkeyword-result').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/addkeyword&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {keyword : $('#keyword-term').val(), redirect : $('#keyword-redirect-url').val()},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#addkeyword-result').html('<div class="pr_success">'+json['success']+'</div>');
					  $('#keywords-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/keywords&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
				}
				if (json['warning']) {
					  $('#addkeyword-result').html('<div class="pr_error">'+json['warning']+'</div>');
				}
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });	
});

$('#add-replacer-entry').on('click', function() {
	$('#replacer-result').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/addreplacer&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {matchsting : $('#match-string').val(), replacestring : $('#replace-string').val()},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#replacer-result').html('<div class="pr_success">'+json['success']+'</div>');
					  $('#replace-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/url_replacer&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
				}
				if (json['warning']) {
					  $('#replacer-result').html('<div class="pr_error">'+json['warning']+'</div>');
				}
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });	
});

$('#delete').on('click', function() {
	$('#msgoutput').html('');
	var arraydata404 = $('input[name="selected404[]"]:checked').map(function(){
        return this.value;
    }).get()
	var arraydata200 = $('input[name="selected200[]"]:checked').map(function(){
        return this.value;
    }).get()
	
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/delete&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {selected404 : arraydata404,selected200 : arraydata200},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="pr_success">'+json['success']+'</div>');
					  loadreports();
				}
				if (json['warning']) {
					  $('#msgoutput').html('<div class="pr_error">'+json['warning']+'</div>');
				}
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });	
});

function updateredirect(redirect, id){
	$('#msg'+id).html('<center><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></center>');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/updateredirect&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {redirect: redirect, id: id},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#msg'+id).html('<div class="pr_success">'+json['success']+'</div>');
				}
				if (json['warning']) {
					  $('#msg'+id).html('<div class="pr_error">'+json['warning']+'</div>');
				}
				if (json['sameurl']) {
					  $('#msg'+id).html('');
				}
		  }
	 });
 }
 
function deleteKeyword(id){
	$('#msgoutput').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/deletekeyword&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {id : id},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="pr_success">'+json['success']+'</div>');
					  loadreports();
				}
		  },			
		  error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

function deleteReplacer(id){
	$('#msgoutput').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/deletereplacer&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {id : id},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="pr_success">'+json['success']+'</div>');
					  loadreports();
				}
		  },			
		  error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}
 
function showreferrer(id){
	$('#referrer-block').html('<i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>');
	$('#referrer-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/referrers&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&id='+id);
	$('#referrer-modal').modal('show');
}

function bulk_update_redirect(){
	 var oldurl = $('#old_url').val();
	 var newurl = $('#new_url').val();
	 location = 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/tool_bulkredirectupdate&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&old='+oldurl+'&new='+newurl;
}
function update_type(){
	 var oldtype = $('#old_type').val();
	 var newtype = $('#new_type').val();
	 location = 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/tool_bulktype&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&old='+oldtype+'&new='+newtype;
}
function update_default(){
	 location = 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/tool_bulkdefault&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>';
}
function reset_all(){
	 location = 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/tool_resetall&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>';
}

function searchBrokenLink() {
	var searchvalue = $('#search-brokenlink-value').val();
	$('#broken-links-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/brokenlinks&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&search='+searchvalue);
};

function searchCommonLink() {
	var searchvalue = $('#search-commonlink-value').val();
	$('#redirect-links-block').load('index.php?route=<?php echo $base_route; ?>/hb_brokenlinks/pageredirects&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&search='+searchvalue);
};
</script>

<script type="text/javascript">
$('#store').on('change', function() {
	window.location.href = 'index.php?route=<?php echo $base_route; ?>/hb_brokenlinks&token=<?php echo $token; ?>&store_id='+$('#store').val();
});
</script>
<?php echo $footer; ?>