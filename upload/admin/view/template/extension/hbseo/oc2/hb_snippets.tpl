<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
<!--Header Start-->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" onClick="document.getElementById('form-language').submit();" form="form-latest" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
		<a href="https://www.huntbee.com/documentation/docs/seo-structured-data/" target="_blank" class="btn btn-default"><i class="fa fa-book"></i> DOCUMENTATION</a>
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
		  <div id="result-console"></div>
          <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language" class="form-horizontal">
		     <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-sd" data-toggle="tab"><i class="fa fa-google"></i> <?php echo $tab_sd; ?></a></li>
				<li><a href="#tab-contact" data-toggle="tab"><i class="fa fa-location-arrow"></i> <?php echo $tab_contact; ?></a></li>
				<li><a href="#tab-og" data-toggle="tab"><i class="fa fa-facebook"></i> <?php echo $tab_og; ?></a></li>
				<li><a href="#tab-tc" data-toggle="tab"><i class="fa fa-twitter"></i> <?php echo $tab_tc; ?></a></li>
	          </ul>
			  <div class="tab-content">
			  
					 <div class="tab-pane active" id="tab-sd">
					 		<div class="well">
								<p><i class="fa fa-info-circle"></i> Including structured data markup in web content helps Google algorithms better index and understand the content. Some data can also be used to create and display Rich Snippets within the search results. Information about a product, including price, availability, and review ratings might appear in search results. <a href="https://www.huntbee.com/documentation/docs/seo-structured-data/" target="_blank">Learn More...</a></p>
								<p><a class="btn btn-sm btn-success" href="https://search.google.com/test/rich-results" target="_blank">Google Validation/Testing Tool</a></p>
							</div>
					 
					 		<div class="form-group">
								<label class="col-sm-3 control-label">Product Structured Data</label>
								<div class="col-sm-9">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_prod_enable" class="form-control" value="1" <?php echo ($hb_snippets_prod_enable == 1)? 'checked':''; ?> />
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Display prices incl Tax</label>
								<div class="col-sm-9">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_incl_tax" class="form-control" value="1" <?php echo ($hb_snippets_incl_tax == 1)? 'checked':''; ?> />
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Show Property priceValidUntil</label>
								<div class="col-sm-3">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_pricevalid" class="form-control" value="1" <?php echo ($hb_snippets_pricevalid == 1)? 'checked':''; ?> />
								</div>
								<label class="col-sm-4 control-label">Default date when no "End Date" is detected in product database</label>
								<div class="col-sm-2">
									<div class="input-group date">
										<input type="text" name="hb_snippets_pricevaliddate" value="<?php echo $hb_snippets_pricevaliddate; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Default Brand</label>
								<div class="col-sm-9">
									<input type="text" name="hb_snippets_brand" value="<?php echo $hb_snippets_brand; ?>" class="form-control" />
								</div>
							</div>
														
							<div class="form-group">
								<label class="col-sm-3 control-label">Choose Product Description</label>
								<div class="col-sm-9">
									<select name="hb_snippets_description" class="form-control">
										<option value="meta_description" <?php echo ($hb_snippets_description == 'meta_description')? 'selected':''; ?>>Meta Description</option>
										<option value="description" <?php echo ($hb_snippets_description == 'description')? 'selected':''; ?>>Description</option>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Assign Availability</label>
								<div class="col-sm-9">
									<?php foreach ($stock_statuses as $stock_status) { ?>
										<div class="input-group">
											<span class="input-group-addon"><?php echo $stock_status['name']; ?></span>
											<select name="hb_snippets_stock[<?php echo $stock_status['stock_status_id']; ?>]" class="form-control">
												<?php foreach ($availability as $avail) { ?>
												<option value="<?php echo $avail; ?>" <?php echo (isset($hb_snippets_stock[$stock_status['stock_status_id']]) and $hb_snippets_stock[$stock_status['stock_status_id']] == $avail)? 'selected':''; ?>><?php echo $avail; ?></option>
												<?php } ?>
											</select>
										</div><br />
									<?php } ?>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Breadcrumbs List</label>
								<div class="col-sm-9">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_bc_enable" class="form-control" value="1" <?php echo ($hb_snippets_bc_enable == 1)? 'checked':''; ?> />
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Category List Items</label>
								<div class="col-sm-9">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_list_enable" class="form-control" value="1" <?php echo ($hb_snippets_list_enable == 1)? 'checked':''; ?> />
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Logo Image <br /><div class="pr_info">The image must be 112x112px, at minimum and the image must be in .jpg, .png, or. gif format</div></label>
								 <div class="col-sm-9">
								 	<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $logo_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
								  	<input type="hidden" name="hb_snippets_logo" value="<?php echo $hb_snippets_logo; ?>" id="input-image" />
									<?php if ($error_logo) { ?>
									  <div class="text-danger"><?php echo $error_logo; ?></div>
									<?php } ?>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Corporate Contacts <br /><div class="pr_info">Add internationalized version of the phone number, starting with the "+" symbol and country code</div></label>
								<div class="col-sm-9">
									<a onclick="addcontact();" class="btn btn-warning">ADD CONTACT NUMBER</a> <br /><br />
								<div id="corp_contact">
									<?php $contact_row = 0; ?>
										<?php if ($hb_snippets_contact){ ?>
										<?php foreach ($hb_snippets_contact as $contact){ ?>							
											<div id="contact-row<?php echo $contact_row; ?>">
												<div class="col-sm-4"><input type="text" placeholder="+1-401-555-1212" name="hb_snippets_contact[<?php echo $contact_row; ?>][n]" class="form-control" value="<?php echo $contact['n']; ?>"></div>
												<div class="col-sm-4"><select name="hb_snippets_contact[<?php echo $contact_row; ?>][t]" class="form-control">
												<option <?php echo ($contact['t'] == 'Customer Service')? 'selected':''; ?> >Customer Service</option>
												<option <?php echo ($contact['t'] == 'Customer Support')? 'selected':''; ?> >Customer Support</option>
												<option <?php echo ($contact['t'] == 'Technical Support')? 'selected':''; ?> >Technical Support</option>
												<option <?php echo ($contact['t'] == 'Billing Support')? 'selected':''; ?> >Billing Support</option>
												<option <?php echo ($contact['t'] == 'Bill Payment')? 'selected':''; ?> >Bill Payment</option>
												<option <?php echo ($contact['t'] == 'Sales')? 'selected':''; ?> >Sales</option>
												<option <?php echo ($contact['t'] == 'Reservations')? 'selected':''; ?> >Reservations</option>
												<option <?php echo ($contact['t'] == 'Credit Card Support')? 'selected':''; ?> >Credit Card Support</option>
												<option <?php echo ($contact['t'] == 'Emergency')? 'selected':''; ?> >Emergency</option>
												<option <?php echo ($contact['t'] == 'Baggage Tracking')? 'selected':''; ?> >Baggage Tracking</option>
												<option <?php echo ($contact['t'] == 'Roadside Assistance')? 'selected':''; ?> >Roadside Assistance</option>
												<option <?php echo ($contact['t'] == 'Package Tracking')? 'selected':''; ?> >Package Tracking</option>
												</select>
												</div>
												<div class="col-sm-4">
												<button type="button" onclick="$('#contact-row<?php echo $contact_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></div>
												</div>
												<br /><br /><br />
											<?php $contact_row++; ?>
										<?php } ?>	
									<?php } ?>	
								</div>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Social Profile</label>
								<div class="col-sm-9">
									<a onclick="addsocial();" class="btn btn-warning">ADD SOCIAL PROFILE LINKS</a> <br /><br />
									<div id="corp_social">
										<?php $social_row = 0; ?>
										<?php if ($hb_snippets_socials){ ?>
										<?php foreach ($hb_snippets_socials as $social){ ?>							
											<div id="social-row<?php echo $social_row; ?>">
											<div class="col-sm-10"><input type="text" placeholder="https://www.facebook.com/your-profile" name="hb_snippets_socials[<?php echo $social_row; ?>]" class="form-control" value="<?php echo $social; ?>"></div>
											<div class="col-sm-2">
											<button type="button" onclick="$('#social-row<?php echo $social_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></div>
											</div><br><br><br>
											<?php $social_row++; ?>
										<?php } ?>	
										<?php } ?>	
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Sitelinks searchbox</label>
								<div class="col-sm-9">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_search_enable" class="form-control" value="1" <?php echo ($hb_snippets_search_enable == 1)? 'checked':''; ?> />
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label">Enable Website Info on Home page</label>
								<div class="col-sm-9">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_kg_enable" class="form-control" value="1" <?php echo ($hb_snippets_kg_enable == 1)? 'checked':''; ?> />
								</div>
							</div>
					
					</div>
					
					<div class="tab-pane" id="tab-contact">
						<div class="well">
							<p><i class="fa fa-info-circle"></i> Please fill all fields with appropriate data</p>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Store Name / Business Name</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_local_name" id="hb_snippets_local_name" value="<?php echo $hb_snippets_local_name; ?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Street Address</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_local_st" id="hb_snippets_local_st" value="<?php echo $hb_snippets_local_st; ?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Locality</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_local_location" id="hb_snippets_local_location" value="<?php echo $hb_snippets_local_location;?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Region/State</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_local_state" id="hb_snippets_local_state" value="<?php echo $hb_snippets_local_state;?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Postal Code</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_local_postal" id="hb_snippets_local_postal" value="<?php echo $hb_snippets_local_postal;?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Country</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_local_country" id="hb_snippets_local_country" value="<?php echo $hb_snippets_local_country;?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Local Store Image</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_store_image" id="hb_snippets_store_image" value="<?php echo $hb_snippets_store_image;?>" placeholder = "Enter your Local Store Picture Link" required/>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Price Range of your Store Products</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="hb_snippets_price_range" id="hb_snippets_price_range" value="<?php echo $hb_snippets_price_range;?>" placeholder="$0 to $20000" required />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
								<a class="btn btn-primary" onclick="generatelocalsnippet()"><?php echo $btn_generate; ?></a>
								<span id="localmu-block"></span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">MARKUP CODE (in JSON-LD format)<br>
						   				<div class="pr_info">Click on above generate button to Generate JSON-LD for your Shop address.<br />
										You can also manually add / customize the markup code in this field <a href="https://developers.google.com/search/docs/data-types/local-businesses" target="_blank">[Reference]</a>
						   				</div></label>
							<div class="col-sm-9">
								<textarea name="hb_snippets_local_snippet" id="hb_snippets_local_snippet" rows="10" cols="60"  class="form-control"><?php echo $hb_snippets_local_snippet;?></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Enable</label>
							<div class="col-sm-9">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_local_enable" class="form-control" value="1" <?php echo ($hb_snippets_local_enable == 1)? 'checked':''; ?> />
							</div>
						</div>

				
				   </div>

				    
				    <div class="tab-pane" id="tab-og">
						<div class="well">
								<p><i class="fa fa-info-circle"></i> The Open Graph Protocol is a set of tags that allows a webmaster to give major social networks (Facebook, Google +, Twitter, LinkedIn...) detailed information about its pages. This information will allow social networks to better display a link on those pages that have been placed by the users.</p>
								<p><a class="btn btn-sm btn-success" href="https://developers.facebook.com/tools/debug/og/object/" target="_blank"><i class="fa fa-facebook-official"></i> Open Graph Debugger Tool</a></p>
						</div>

						<div class="form-group">
								<label class="col-sm-4 control-label">Enable Open Graph Protocol</label>
								<div class="col-sm-8">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_og_enable" class="form-control" value="1" <?php echo ($hb_snippets_og_enable == 1)? 'checked':''; ?> />
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Facebook App ID</label>
								<div class="col-sm-8">
									<input name="hb_snippets_og_id" class="form-control" value="<?php echo $hb_snippets_og_id; ?>">
									<div class="pr_warning"><i class="fa fa-lightbulb-o"></i> Get APP ID from <a class="btn btn-sm btn-warning" href="https://developers.facebook.com/apps/" target="_blank">Facebook Apps</a></div>
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Product Title Pattern (SHORTCODES: {name}, {price}, {brand}, {model})</label>
								<div class="col-sm-8">
									<input name="hb_snippets_ogp" class="form-control" value="<?php echo $hb_snippets_ogp; ?>">
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Category Title Pattern (SHORTCODES: {name})</label>
								<div class="col-sm-8">
									<input name="hb_snippets_ogc" class="form-control" value="<?php echo $hb_snippets_ogc; ?>">
								</div>
						</div>
						<div class="form-group">
						  <label class="col-sm-4 control-label">Default Image or Cover Image</label>
						  <div class="col-sm-8">
							<a href="" id="thumb-ogimg" data-toggle="image" class="img-thumbnail"><img src="<?php echo $ogimg; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
							<input type="hidden" name="hb_snippets_og_img" id="hb_snippets_og_img" value="<?php echo $hb_snippets_og_img; ?>" />
						  </div>
					  	</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Cover Image Size (W x H)</label>
								<div class="col-sm-4">
									<input name="hb_snippets_og_diw" class="form-control" value="<?php echo $hb_snippets_og_diw; ?>">
								</div>
								<div class="col-sm-4">
									<input name="hb_snippets_og_dih" class="form-control" value="<?php echo $hb_snippets_og_dih; ?>">
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Product Image Size (W x H)</label>
								<div class="col-sm-4">
									<input name="hb_snippets_og_piw" class="form-control" value="<?php echo $hb_snippets_og_piw; ?>">
								</div>
								<div class="col-sm-4">
									<input name="hb_snippets_og_pih" class="form-control" value="<?php echo $hb_snippets_og_pih; ?>">
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Category Image Size (W x H)</label>
								<div class="col-sm-4">
									<input name="hb_snippets_og_ciw" class="form-control" value="<?php echo $hb_snippets_og_ciw; ?>">
								</div>
								<div class="col-sm-4">
									<input name="hb_snippets_og_cih" class="form-control" value="<?php echo $hb_snippets_og_cih; ?>">
								</div>
						</div>
						
				   </div>
				   
				   <div class="tab-pane" id="tab-tc">
					 	<div class="well">
							<p>To validate Twitter Card</p>
							<p><a class="btn btn-sm btn-success" href="https://cards-dev.twitter.com/validator" target="_blank"><i class="fa fa-twitter-square"></i> Twitter Card Validator Tool</a></p>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Enable Twitter Card</label>
								<div class="col-sm-8">
									<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_snippets_tc_enable" class="form-control" value="1" <?php echo ($hb_snippets_tc_enable == 1)? 'checked':''; ?> />
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Username (Ex: @flickr)</label>
								<div class="col-sm-8">
									<input name="hb_snippets_tc_username" class="form-control" value="<?php echo $hb_snippets_tc_username; ?>">
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Product Title Pattern (SHORTCODES: {name}, {price}, {brand}, {model})</label>
								<div class="col-sm-8">
									<input name="hb_snippets_tcp" class="form-control" value="<?php echo $hb_snippets_tcp; ?>">
								</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label">Category Title Pattern (SHORTCODES: {name})</label>
								<div class="col-sm-8">
									<input name="hb_snippets_tcc" class="form-control" value="<?php echo $hb_snippets_tcc; ?>">
								</div>
						</div>
				   </div>
				   
				   </div>
          </form>
    	
      </div>
    </div>
  </div>
  <div class="container-fluid"> <!--Huntbee copyrights-->
 <center>
  <span class="help">SEO STRUCTURED DATA - <?php echo $extension_version; ?> &copy; <a href="https://www.huntbee.com/">WWW.HUNTBEE.COM</a> | <a href="https://www.huntbee.com/get-support">SUPPORT</a> | <a href="https://www.huntbee.com/documentation/docs/seo-structured-data/" target="_blank">DOCUMENTATION</a></span></center>
</div><!--Huntbee copyrights end-->
</div>

<style type="text/css">
.pr_error,.pr_info,.pr_infos,.pr_success,.pr_warning{margin:10px 0;padding:12px}.pr_info{color:#00529B;background-color:#BDE5F8}.pr_success{color:#4F8A10;background-color:#DFF2BF}.pr_warning{color:#9F6000;background-color:#FEEFB3}.pr_error{color:#D8000C;background-color:#FFBABA}.pr_error i,.pr_info i,.pr_success i,.pr_warning i{margin:10px 0;vertical-align:middle}
</style>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script type="text/javascript">
$('.date').datetimepicker({
	pickTime: false
});

function generatelocalsnippet(){
	$('#localmu-block').html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_snippets/generatelocalsnippet&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {	name: $('#hb_snippets_local_name').val(),
		  			street: $('#hb_snippets_local_st').val(),
					location: $('#hb_snippets_local_location').val(),
					postal:$('#hb_snippets_local_postal').val(),
		   			state:$('#hb_snippets_local_state').val(),
					country:$('#hb_snippets_local_country').val(),
					store_image:$('#hb_snippets_store_image').val(),
					price_range:$('#hb_snippets_price_range').val() 
				},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
						var ss = json['success'];
					  	$('#hb_snippets_local_snippet').val(ss);
						$('#localmu-block').html('');
				}
		  },			
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	 });
}
</script>
<script type="text/javascript">
var contact_row = <?php echo $contact_row; ?>;
function addcontact(){
html  = '<div id="contact-row' + contact_row + '">';
html += '<div class="col-sm-4"><input type="text" placeholder="+1-401-555-1212" name="hb_snippets_contact[' + contact_row + '][n]" class="form-control"></div>';
html += '<div class="col-sm-4"><select name="hb_snippets_contact[' + contact_row + '][t]" class="form-control">';
html += '<option>Customer Service</option>';
html += '<option>Customer Support</option>';
html += '<option>Technical Support</option>';
html += '<option>Billing Support</option>';
html += '<option>Bill Payment</option>';
html += '<option>Sales</option>';
html += '<option>Reservations</option>';
html += '<option>Credit Card Support</option>';
html += '<option>Emergency</option>';
html += '<option>Baggage Tracking</option>';
html += '<option>Roadside Assistance</option>';
html += '<option>Package Tracking</option>';
html += '</select></div>';
html += '<div class="col-sm-4"><button type="button" onclick="$(\'#contact-row' + contact_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></div>';
html += '</div><br><br><br>';
$('#corp_contact').append(html);
contact_row++;
}

var social_row = <?php echo $social_row; ?>;
function addsocial(){
html  = '<div id="social-row' + social_row + '">';
html += '<div class="col-sm-10"><input type="text" placeholder="https://www.facebook.com/your-profile" name="hb_snippets_socials[' + social_row + ']" class="form-control"></div>';
html += '<div class="col-sm-2"><button type="button" onclick="$(\'#social-row' + social_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_remove; ?></button></div>';
html += '</div><br><br><br>';
$('#corp_social').append(html);
social_row++;
}
</script>

<script type="text/javascript">
$('#store').on('change', function() {
	window.location.href = 'index.php?route=<?php echo $base_route; ?>/hb_snippets&token=<?php echo $token; ?>&store_id='+$('#store').val();
});
</script>
<?php echo $footer; ?>