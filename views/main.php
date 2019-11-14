<?php if (!defined('ABSPATH')) exit; ?>

<?php 
	
?>
<style type="text/css">
	.iframe-load
	{
		-webkit-animation-name: example; /* Safari 4.0 - 8.0 */
	    -webkit-animation-duration: 4s; /* Safari 4.0 - 8.0 */
	    -webkit-animation-iteration-count: infinite; /* Safari 4.0 - 8.0 */
	}
	@-webkit-keyframes example {
	    0%   {rotate:72;}
	    25%  {rotate:144;}
	    50%  {rotate:216;}
	    75%  {rotate:288;}
	    100% {rotate:0;}
	}

	.scrape_load
	{
		background-color: red;
		cursor:crosshair;
	}


</style>
<div class="bootstrap" name="form" style="margin-left: 50px" ng-app="nsscraps" ng-controller="controller" ng-form novalidate ng-cloak ng-init="
model.post_title='<?php echo isset($post_data['post_title'][0])?esc_js($post_data['post_title'][0]):'';?>';
model.scrape_url = '<?php echo isset($post_data['scrape_post_url'][0])?esc_js($post_data['scrape_post_url'][0]):'';?>'; 
model.scrape_value = '<?php echo isset($post_data['scrape_post_value'][0])?esc_js($post_data['scrape_post_value'][0]):'';?>';
model.scrape_title = '<?php echo isset($post_data['scrape_post_title'][0])?esc_js($post_data['scrape_post_title'][0]):'';?>';
model.post_categorytype = '<?php echo isset($post_data['scrape_post_type'][0])?esc_js($post_data['scrape_post_type'][0]):'';?>';
model.scrape_next = '<?php echo isset($post_data['scrape_post_next'][0])?esc_js($post_data['scrape_post_next'][0]):''?>';
model.post_type = '<?php echo isset($post_data['scrape_task_type'][0])?esc_js($post_data['scrape_task_type'][0]):''?>';
model.create_type = '<?php echo isset($_GET['post'])?$_GET['post']:''?>';
model.scrape_category = '<?php echo isset($post_data['scrape_post_categoryxpath_tax'][0])?esc_js($post_data['scrape_post_categoryxpath_tax'][0]):'';?>';
model.category_edit_value = '<?php echo isset($post_data['scrape_post_categoryxpath'][0])?esc_js($post_data['scrape_post_categoryxpath'][0]):'';?>';
model.scrape_category_value = '<?php echo isset($post_data['scrape_post_category'])?esc_js($post_data['scrape_post_category']):'';?>';
model.scrape_content = '<?php echo isset($post_data['scrape_post_content'][0])?esc_js($post_data['scrape_post_content'][0]):'';?>';
model.scrape_allowhtml = <?php if(isset($post_data['scrape_allowhtml'][0])){?>true<?php }else {?>false<?php }?>;
model.scrape_download_images = <?php if(isset($post_data['scrape_download_images'][0])){?>true<?php }else {?>false<?php }?>;
model.scrape_tags = '<?php echo isset($post_data['scrape_post_tags'][0])?esc_js($post_data['scrape_post_tags'][0]):'';?>';
model.scrape_featured = '<?php echo isset($post_data['scrape_post_featured'][0])?esc_js($post_data['scrape_post_featured'][0]):'';?>';
model.scrape_post_author = '<?php echo isset($post_data['scrape_post_author'][0])?esc_js($post_data['scrape_post_author'][0]):''; ?>';
model.scrape_status = '<?php echo isset($post_data['scrape_post_status'][0])?esc_js($post_data['scrape_post_status'][0]):''; ?>';
model.scrape_cron_type =	 '<?php echo isset($post_data['scrape_cron_type'][0])?esc_js($post_data['scrape_cron_type'][0]):''?>';
model.scrape_run_limit = '<?php echo isset($post_data['scrape_run_limit'][0])?esc_js($post_data['scrape_run_limit'][0]):'';?>';
model.scrape_run_unlimited =<?php if(isset($post_data['scrape_run_unlimited'][0])){?>true<?php }else {?>false<?php }?>;
model.scrape_timeout ='<?php echo isset($post_data['scrape_timeout'][0])?esc_js($post_data['scrape_timeout'][0]):''?>';
model.scrape_comment =<?php if(isset($post_data['scrape_comment'][0])){?>true<?php }else {?>false<?php }?>;
model.scrape_unique_title = <?php if(isset($post_data['scrape_unique_title'][0])){?>true<?php }else {?>false<?php }?>;
model.scrape_unique_content = <?php if(isset($post_data['scrape_unique_content'][0])){?>true<?php }else {?>false<?php }?>;
model.scrape_unique_sourceurl = <?php if(isset($post_data['scrape_unique_url'][0])){?>true<?php }else {?>false<?php }?>;
model.scrape_date_type = '<?php echo isset($post_data['scrape_date_type'][0])?esc_js($post_data['scrape_date_type'][0]):''?>';
model.scrape_customfield = '<?php echo isset($post_data['scrape_post_custom_field'])?esc_js($post_data['scrape_post_custom_field']):''?>';
model.scrape_title_template = '<?php echo isset($post_data['scrape_post_title_template'][0])?esc_js($post_data['scrape_post_title_template'][0]):''?>';
model.scrape_post_content_template = '<?php echo isset($post_data['scrape_post_content_template'][0])?esc_js($post_data['scrape_post_content_template'][0]):''?>';
model.scrape_excerpt = '<?php echo isset($post_data['scrape_excerpt'][0])?esc_js($post_data['scrape_excerpt'][0]):''?>';
model.scrape_date = '<?php echo isset($post_data['scrape_date'])?esc_js($post_data['scrape_date']):''?>';
model.scrape_status = '<?php echo isset($post_data['scrape_post_status'][0])?esc_js($post_data['scrape_post_status'][0]):''?>';
urlcomponent = '<?php echo isset($post_data['scrape_real_url'][0])?esc_js($post_data['scrape_real_url'][0]):''?>';
model.scrape_post_title_from_feed = '<?php echo isset($post_data['scrape_title_from_feed'][0])?esc_js($post_data['scrape_title_from_feed'][0]):''?>';
model.scrape_post_title_from_feed = '<?php echo isset($post_data['scrape_title_from_feed'][0])?esc_js($post_data['scrape_title_from_feed'][0]):''?>';
model.scrape_content_type = '<?php echo isset($post_data['scrape_content_type'][0])?esc_js($post_data['scrape_content_type'][0]):''?>';
model.scrape_featured_type = '<?php echo isset($post_data['scrape_featured_type'][0])?esc_js($post_data['scrape_featured_type'][0]):''?>';
init();
">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="form-horizontal">
					<div class="panel panel-info">
					<div class="panel-heading" style="background-image: linear-gradient(gray, white);">
						<div class="col-sm-1"></div>
						<h2 style="margin-top: 10px;font-size:30px;">ADD NEW</h2>
					</div>
					<div class="form-group">
						<div class="rows form-group has-success has-feedback" style="margin-top: 30px">
							<div class="col-sm-1"></div>
							<label class="col-sm-2 control-label" for="inputSuccess" style="font-size: 25px;">
								    Name: </label>
							<div class="col-sm-8">
								<div class="form-group field">
									<div class="rows">
										<div class="col-sm-12">
											<input name="post_title" class="form-control" type="text" ng-model="model.post_title" ng-required="true">
										</div>
										<div class="col-sm-1"></div>
										<p class="help-block" ng-show ="form.post_name.$invalid">Please enter a valid value.</p>
									</div>
								</div>
							</div>
						</div>
					</div>

						<div class="form-group has-success has-feedback field" style="">
							<div class="col-sm-1"></div>
							<label class="col-sm-3 control-label" for="inputSuccess"
							style="font-size: 25px;padding-top:0px;">Task Type: </label>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="col-sm-12">
										<div class="row">
											<div class="col-xs-4">
												<label>
													<input name="scrape_task_type" value="single" type="radio" ng-model="model.post_type" style="float: left;">
													<span style="font-size: 20px;float: left;margin-left:5px;">Single</span>
												</label>
											</div>

											<div class="col-xs-4">
												<label>
													<input name="scrape_task_type" value="list" type="radio" ng-model="model.post_type" style="float: left;">
													<span style="font-size: 20px;float:left;margin-left:5px;">Serial</span>
												</label>
											</div>

											<div class="col-xs-4">
												<label>
													<input name="scrape_task_type" value="feed" type="radio" ng-model="model.post_type"
													style="float:left;">

													<span style="font-size: 20px;float:left;margin-left:5px;">Feed</span>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					
					</div>

					<div class="panel-group" ng-show="model.post_type">
						<div class="panel" style="background-image: linear-gradient(white, gray);">
							<div class="panel-heading">
								<label style="margin-top:20px;font-size: 28px"><a href="#collapse-0" data-toggle="collapse" aria-expanded="true" class="" style="margin-left: 65px;"><i class="icon ion-link"></i><span>Link Options</span></a></label>
							</div>

							<div id="collapse-0" class="panel-collapse collapse in" aria-expanded="true" style="">
								<div class="panel-body" style="margin-top: 10px">
									<!-- ngIf: model.scrape_type -->
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="font-size: 23px;">
											<div class="col-sm-10">
												Cookies
											</div>
											<div class="col-sm-2">
											 </div></label>
											
										<div class="col-sm-7">
											
											<div class="form-group field">
												<div class="col-sm-12">
													
													<button type="button" class="btn btn-link " style="font-size: 23px;" ng-click="addcustomfield($event,'cookie')"><i class="icon ion-plus-circled"></i> Add new cookie</button>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->

									<!-- ngIf: model.scrape_type -->
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="font-size: 23px;">
											<div class="col-sm-10">
											Source URL</div>
											<div class="col-sm-2">
											 	</div></label>
										
										<div class="col-sm-7">

											<div class="form-group field">
												<div class="col-sm-12">
													<div class="input-group" style="">
														<div class="input-group-addon" style="font-size: 16px;">URL</div>
														<input name="scrape_post_url" class="form-control"  type="text" ng-model="model.scrape_url" ng-pattern="/^(http|https):///" ng-required="true">
													</div>
													<p class="help-block" style="" ng-show="form.scrape_url.$invalid">Please enter a valid value.</p>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group "  ng-show="model.post_type == 'list'">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="font-size: 23px;"><div class="col-sm-10"> Post Item </div>
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7">

											<div class="form-group field">
												<div class="col-sm-12">
													<div class="input-group" style="">
														<div class="input-group-addon" style="font-size: 16px;">Value</div>
														<input name="scrape_post_value" class="form-control"  type="text" ng-model="model.scrape_value" ng-pattern="/^///" ng-required="true">
														<span class="input-group-btn" ng-click="iframe_view($event,'serial','url')"><button type="button" class="btn btn-primary btn-block" style=""><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" style="" ng-show="form.scrape_value.$invalid" >Please enter a valid value.</p>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group "  ng-show="model.post_type == 'list'">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="font-size: 23px;">
											<div class="col-sm-10">
												Next Page 
											</div>
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7">

											<div class="form-group field">
												<div class="col-sm-12">
													<div class="input-group" style="">
														<div class="input-group-addon" style="font-size: 16px;">Value</div>
														<input name="scrape_post_next" class="form-control"  type="text" ng-model="model.scrape_next" ng-pattern="/^///" ng-required="true">
														<span class="input-group-btn" ng-click="iframe_view($event,'serial','next_page')"><button type="button" class="btn btn-primary btn-block" style="margin-left: 0px"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" style="" ng-show="form.scrape_next.$invalid">Please enter a valid value.</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel" style="background-image: linear-gradient(white, gray);">
							<div class="panel-heading">
								<div class="col-sm-1"></div>
								<label style="margin-top:30px;font-size: 28px"><a href="#collapse-1" data-toggle="collapse" aria-expanded="true" class=""><i class="icon ion-folder" style=""></i>Category Options</a></label>
							</div>

							<div id="collapse-0" class="panel-collapse collapse in" aria-expanded="true" style="">
								<div class="panel-body" style="margin-top: 10px">
									<div class="form-group">
										<div class="form-group" style=" margin-top: 25px">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="font-size: 24px; ">
											<div class="col-sm-10">
												Post type 
											</div>
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7" style="">
											<div class="form-group field">
												<div class="col-sm-12">
													<div class="select">
														<select name="scrape_post_type" class="form-control ng-pristine ng-valid ng-not-empty ng-touched"
														ng-model="model.post_categorytype" ><option value="{{item}}" ng-repeat="item in post_type">{{item}}</option></select></div>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->

									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-left: 0px;font-size: 24px; ">
											<div class="col-sm-10">
												<p style="font-size: 24px;">Create</p> 
												<p style="font-size: 20px;">categories</p>
											</div> 
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7" style="">
											<div class="form-group field">
												<div class="col-sm-12">
													<div class="select">
														<select name="scrape_post_categoryxpath_tax" class="form-control" ng-model="model.scrape_category" value="{{model.scrape_category}}">
															<option value="">Please select a taxonomy</option><option value="{{item}}" ng-repeat="item in categories">{{item}}</option></select>
													</div>
												</div>
											</div>

											<!-- ngIf: model.scrape_categoryxpath_tax -->
											<div class="form-group field" ng-show="model.scrape_category && model.post_categorytype">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 16px;">Value</div>
														<input name="scrape_post_categoryxpath"  class="form-control"  ng-required="true" value="" type="text" ng-model="model.category_edit_value">
														<span class="input-group-btn" ng-click="iframe_view($event,'serial','data_page')"><button type="button" class="btn btn-primary btn-block" ><i class="ion-plus-circled"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_categoryxpath.$invalid">Please enter a valid value.</p>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 16px;">Separator</div>
														<input name="scrape_categoryxpath_separator" placeholder="" class="form-control" type="text" ng-model="model.category_edit_value['seperator']">
														<span class="input-group-btn" ng-click="createcategory()"><button type="button" class="btn btn-primary btn-block" ><i class="fa fa-save"></i></button></span>
													</div>
												</div>
												
											</div><!-- end ngIf: model.scrape_categoryxpath_tax -->
											<div class="form-group">
												
											</div>
											
											<div class="form-group" ng-show="model.scrape_category_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_newrule($event,'category_regex_status')"><i class="icon ion-plus-circled"></i> Add new find and replace rule</button>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label style="margin-left: 0px; font-size: 20px"><input name="scrape_category_regex_status" type="checkbox" ng-model="model.scrape_category_regex_status"> Enable find and replace rules</label></div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group category" >
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="font-size: 24px">
											<div class="col-sm-10">Categories</div> 
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7">
											<div class="form-group" style="">
												<div class="col-sm-12">
													<div class="overflow">	
														<div  class="checkbox" ng-repeat="item in category">
														<label>
															<input type="checkbox" value="{{item.id}}" name="scrape_post_categoryxpath" ng-checked="getangularclass(item.id)"/>
															{{item.name}}
														</label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
						</div></div>
						<div class="panel" style="background-image: linear-gradient(white, gray);">
							<div class="panel-heading">
								<div class="col-sm-1"></div>
								<label style="margin-top:30px;font-size: 28px"><a href="#collapse-2" data-toggle="collapse" aria-expanded="true" class=""><i class="icon ion-document-text"></i>Post Options</a></label>
							</div>

							<div id="collapse-2" class="panel-collapse collapse in" aria-expanded="true" style="">
								<div class="panel-body">
									<!-- ngIf: model.scrape_type -->
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px">
											<div class="col-sm-10">Title</div> 
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7" style="margin-top:20px;">
											<div class="form-group field" ng-show="model.scrape_post_title_from_feed != 'title_from_feed'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 17px;">Value</div>
														<input name="scrape_post_title" type="text"
														style="height: 43px" ng-model="model.scrape_title" class="form-control">
														<span class="input-group-btn" ><button type="button" class="btn btn-primary btn-block" style="margin-left: 0px" ng-click="iframe_view($event,'serial',
														'data_page')"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_title.$invalid">Please enter a valid value.</p>
												</div>
											</div>
											<div class="form-group field" ng-show="model.scrape_title_template_status">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 17px;">Template</div>
														<input name="scrape_post_title_template" type="text"
														style="height: 43px" ng-model="model.scrape_title_template" class="form-control">
														
													</div>
													<div class="input-tags">
															<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_title]' >title</button>
															
															<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_tags]' >tags</button>
															<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]' >date</button>
															
															<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]' >custom field</button>
															<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]' >source url</button>
													</div>
												</div>
											</div>
											<div class="form-group" ng-show="model.scrape_title_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_newrule($event,'title_regex_status')"><i class="icon ion-plus-circled"></i> Add new find and replace rule</button>
												</div>
											</div>

											<div class="form-group field">
												<div class="col-sm-12">
													<div class="checkbox">
														<label style="font-size: 22px;">
															<input name="scrape_title_template_status" type="checkbox" ng-model="model.scrape_title_template_status"> Enable template</label></div>
													<div class="checkbox"><label style="font-size: 22px;"><input name="scrape_title_regex_status" type="checkbox" ng-model="model.scrape_title_regex_status"> Enable find and replace rules</label></div>
												</div>
											</div>

											<div class="form-group field" ng-show="model.post_type == 'feed'">
												<div class="col-sm-12">
													<!-- ngIf: model.scrape_type && model.scrape_type == 'feed' -->
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_title_from_feed" value="title_from_feed" checked="checked" type="radio" ng-model="model.scrape_post_title_from_feed"> Detect From Feed</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_title_from_feed" value="xpath" type="radio" ng-model="model.scrape_post_title_from_feed"> Select from source</label></div>
												</div>
											</div>


										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px; font-size: 24px">
											<div class="col-sm-10">
												Content 
											</div>
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7" style="margin-top:20px;">
											<div class="form-group field" ng-if="model.scrape_content_type == 'xpath'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon">
															Value
														</div>
														<input type="text" name="scrape_content" placeholder="" class="form-control" ng-model="model.scrape_content" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="iframe_view($event,'serial','data_page')"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_content.$invalid && (form.scrape_content.$dirty || submitted)">Please enter a vaild value </p>
												</div>
											</div>
											<div class="form-group field" ng-show="model.scrape_content_template_status">
												<div class="col-sm-12">

													<?php

													wp_editor(get_post_meta($post_object->ID, 'ns_scrape_template', true), 'ns_scrapetemplate', array('textarea_name' => 'ns_scrape_template', 'editor_height' => 200)); ?>
													<div class="input-tags">
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_content]' >Content</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_title]' >title</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_categories]' >Categories</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_tags]' >tags</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]' >date</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_thumbnail]' >featured image</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]' >custom field</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]' >source url</button>
													</div>
												</div>
											</div>
											<div class="form-group" ng-show="model.scrape_content_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_newrule($event,'content_regex_status')"><i class="icon ion-plus-circled"></i> Add new find and replace rule</button>
												</div>
											</div>
											<div class="form-group field" style="margin-top:10px;">
												<div class="col-sm-12" style="font-size: 22px;margin-top: -36px">
													<div class="checkbox"><label style="font-size: 22px;"><input name="scrape_template_status" type="checkbox" ng-model="model.scrape_content_template_status"> Enable template</label></div>
													<div class="checkbox"><label style="font-size: 22px;"><input name="scrape_content_regex_status" type="checkbox" ng-model="model.scrape_content_regex_status"> Enable find and replace rules</label></div>
												</div>
											</div>

											<div class="form-group field">
												<div class="col-sm-12">
													<!-- ngIf: model.scrape_type && model.scrape_type == 'feed' -->
													<div class="radio" ng-show="model.post_type=='feed'"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_content_type" value="fromfeed" checked="checked" type="radio" ng-model="model.scrape_content_type"> Detect From Feed</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_content_type" value="auto" checked="checked" type="radio" ng-model="model.scrape_content_type"> Detect automatically</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_content_type" value="xpath" type="radio" ng-model="model.scrape_content_type"> Select from source</label></div>
												</div>
											</div>

											<div class="form-group  field">
												<div class="col-sm-12">
													<div class="checkbox"><label style="font-size: 22px;">
														<input name="scrape_allowhtml" value='on' type="checkbox" ng-model="model.scrape_allowhtml"> Allow HTML tags</label></div>
													<div class="checkbox"><label style="font-size: 22px;"><input name="scrape_download_images" type="checkbox" ng-model="model.scrape_download_images"> Download images to media library</label></div>
												</div>
											</div>
										</div>
									</div>

									<!-- ngIf: model.scrape_type -->
									<div class="form-group" >
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px; font-size: 24px">
											<div class="col-sm-10">
												Excerpt
											</div>
											<div class="col-sm-2">
											
											</div>
										</label>
										<div class="col-sm-7" style="margin-top:20px;">
											<div class="form-group field" ng-show="model.scrape_excert_type=='xpath'">
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 17px;">Value</div>
														<input name="scrape_excerpt" placeholder="" class="form-control" type="text" style="height: 43px" ng-model="model.scrape_excerpt">
														<span class="input-group-btn" ng-click="iframe_view($event,'serial','data_page')"><button type="button" class="btn btn-primary btn-block" style="margin-left: 0px"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block" ng-show="form.scrape_excerpt.$invalid">Please enter a valid value.</p>
												</div>

												<!-- ngIf: model.scrape_excerpt_template_status -->
											</div><!-- end ngIf: model.scrape_excerpt_type == 'xpath' -->
											<div class="form-group field" ng-show="model.scrape_excert_type=='xpath' && model.scrape_excert_template_status">
												<div class="col-sm-12">
													<!-- <div class="input-group">
														<div class="input-group-addon" style="font-size: 17px;margin-left: 60px">Template</div>
														<input name="scrape_post_excert_template" placeholder="" class="form-control" type="text" style="width: 530px; height: 43px" ng-model="model.scrape_excert_template">
														<span class="input-group-btn" ng-click="iframe_view($event,'serial','data_page')"><button type="button" class="btn btn-primary btn-block" style="margin-left: 0px"><i class="icon ion-android-locate"></i></button></span>
													</div> -->
													<!-- <div class="input-tags">
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_value]'>Value</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_date]'>date</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_meta name="name"]'>custom field</button>
														<button type="button" class="btn btn-primary btn-xs" data-value='[scrape_url]'>source url</button>
													</div> -->
												</div>


												<!-- ngIf: model.scrape_excerpt_template_status -->
											</div><!-- end ngIf: model.scrape_excerpt_type == 'xpath' -->
											
											<div class="form-group" ng-show="model.scrape_excert_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_new_field($event,'excert_regex_status')" ><i class="icon ion-plus-circled"></i> Add new find and replace rule</button>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_excerpt_type" value="auto" class="" type="radio" ng-model="model.scrape_excert_type"> Generate from content</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_excerpt_type" value="xpath" class="" checked="checked" type="radio" ng-model="model.scrape_excert_type"> Select from source</label></div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px">
											<div class="col-sm-10">
												Tags
											 </div>
											 <div class="col-sm-2">
											 	
											 </div>
										</label>
										<div class="col-sm-7" style="margin-top:20px;">
											<!-- ngIf: model.scrape_tags_type == 'xpath' --><div class="form-group field" >
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 17px;">Value</div>
														<input name="scrape_post_tags" class="form-control" type="text" style=" height: 43px" ng-model="model.scrape_tags">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" style="margin-left: 0px" ng-click="iframe_view($event,'serial','data_page')"><i class="icon ion-android-locate"></i></button></span>
													</div>
													<p class="help-block"></p>
												</div>
												<div class="col-sm-12">
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 17px;">Separator</div>
														<input name="scrape_tags_separator" style="height: 43px" class="form-control"  type="text" ng-model="model.scrape_tags_separator">
													</div>
													<p class="help-block" ng-show="form.scrape_tags_separator.$invalid"	>Please enter a valid value.</p>
												</div>
											</div><!-- end ngIf: model.scrape_tags_type == 'xpath' -->

											<!-- ngIf: model.scrape_tags_type == 'custom' -->

											
											<div class="form-group" ng-show="model.scrape_tags_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_newrule($event,'tags_regex_status')"><i class="icon ion-plus-circled"></i> Add new find and replace rule</button>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div class="checkbox"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_tags_regex_status" class="" type="checkbox" ng-model="model.scrape_tags_regex_status"> Enable find and replace rules</label></div>
												</div>
											</div>

											
										</div>
									</div><!-- end ngIf: model.scrape_type -->

									<!-- ngIf: model.scrape_type -->
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px">
											<div class="col-sm-10">
												<p style=" font-size: 24px">Featured</p><p style=" font-size: 24px; " >image</p> 
											</div>
											<div class="col-sm-2">
												
											</div>
										</label>
										<div class="col-sm-7" style="margin-top: 57px;">
											<!-- ngIf: model.scrape_featured_type == 'xpath' --><div class="form-group field" ng-show="model.scrape_featured_type != 'feed'">
												<div class="col-sm-12" >
													<!-- <p class="help-block success" ><i class="icon ion-checkmark-circled"></i> Featured image is found automatically.</p> -->
													<div class="input-group">
														<div class="input-group-addon" style="font-size: 17px;">Value</div>
														<input name="scrape_post_featured" class="form-control" type="text" style="height: 43px" ng-model="model.scrape_featured">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" style="margin-left: 0px" ng-click="get_featured_image($event)"><i class="icon ion-android-locate" ng-if="model.scrape_featured_type == 'xpath'"></i><i class="icon ion-image" ng-if="model.scrape_featured_type == 'gallery'"></i></button></span>
													</div>
													<p class="help-block">Please enter a valid value.</p>
												</div>
											</div>

											<div class="form-group field">
												<div class="col-sm-12">
													<!-- ngIf: model.scrape_type && model.scrape_type == 'feed' -->
													<div class="radio"  ng-show="model.post_type == 'feed'"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_featured_type" value="feed" checked="checked" type="radio" ng-model="model.scrape_featured_type"> Detect from Feed</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_featured_type" value="xpath" checked="checked" type="radio" ng-model="model.scrape_featured_type"> Select from source</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 3px"><input name="scrape_featured_type" value="gallery" type="radio" ng-model="model.scrape_featured_type"> Select from media library</label></div>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->

									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px; font-size: 24px">Custom fields </label>
										<div class="col-sm-7 custom-field" style="margin-top:20px;">
											
											<div class="form-group">
												<div class="col-sm-8">
													<button type="button" class="btn btn-link addcustomfield" style="font-size: 24px;" ng-click="addcustomfield($event,'custom')"><i class="icon ion-plus-circled" style="font-size: 24px;margin-left: 0px"></i> Add new custom field</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel" style="background-image: linear-gradient(white, gray);">
							<div class="panel-heading">
								<div class="col-sm-1"></div>
								<label style="margin-top:20px;font-size: 28px"><a href="#collapse-2" data-toggle="collapse"><i class="icon ion-upload"></i>Publish Options</a></h4>
							</div>

							<div id="collapse-2" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px">
											<div class="col-sm-10">Author </div>
											<div class="col-sm-2">

											</div>
										</label>
										<div class="col-sm-7" style="margin-top:20px;">
											<div class="form-group field" >
												<div class="col-sm-12">
													<div class="select">
														<select name="scrape_post_author" class="form-control" ><option value="1" selected="selected"><?php echo get_currentuserinfo()->display_name;?></select>
												</div>
													<p class="help-block hide"></p>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->

								
									<div class="form-group" >
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px; font-size: 24px">
											<div class="col-sm-10">Status</div> 
											<div class="col-sm-2">
											
											</div></label>
										<div class="col-sm-7" style="margin-top:20px;">
											<div class="form-group field">
												<div class="col-sm-12">
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_post_status" value="publish" class="" checked="checked" type="radio" ng-model="model.scrape_status"> Published</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_post_status" value="draft" type="radio" ng-model="model.scrape_status"> Draft</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_post_status" value="pending" class="" type="radio" ng-model="model.scrape_status"> Pending review</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_post_status" value="private"  class="" type="radio" ng-model="model.scrape_status"> Private</label></div>
													<p class="help-block" ></p>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->

									<!-- ngIf: model.scrape_type -->
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px; font-size: 24px">
											<div class="col-sm-10">Date</div> 
											<div class="col-sm-2">
											
											</div></label>
										<div class="col-sm-7" style="margin-top: 0px;">
                                            <div class="form-group field">
                                            	<div class="input-group" ng-show="model.scrape_date_type=='xpath'">
														<div class="input-group-addon">value</div>
														<input type="text" name="scrape_date" placeholder="" class="form-control" ng-model="model.scrape_date" ng-pattern="/^///">
														<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="iframe_view($event,'serial','data_page')"><i class="icon ion-android-locate"></i></button></span>
												</div>
												<div class="input-group" ng-show="model.scrape_date_type=='custom'">
														<div class="input-group-addon">value</div>
														<input type="text" name="scrape_date" placeholder="" class="form-control" ng-model="model.scrape_date" ng-pattern="/^///">
												</div>
												<p class="help-block" ng-show="form.scrape_date.$invalid && model.scrape_date_type == 'xpath'">Please enter a valid value</p>
											</div>

											<div class="form-group" ng-show="model.scrape_date_regex_status">
												<div class="col-sm-12">
													<button type="button" class="btn btn-link" ng-click="add_new_field($event,'date_regex_status')"><i class="icon ion-plus-circled"></i> Add new find and replace rule</button>
												</div>
											</div>

											<div class="form-group" style="margin-top: 0px;">
												<div class="col-sm-12">
													<div class="checkbox"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_date_regex_status" class="" type="checkbox" ng-model="model.scrape_date_regex_status"> Enable find and replace rules</label></div>
												</div>
											</div>

											<!-- ngIf: model.scrape_date_type == 'custom' -->

											<div class="form-group field">
												<div class="col-sm-12" style="margin-top: 30px;">
													<div class="radio" ng-show="model.post_type == 'feed'"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_date_type" value="feed"  class="" checked="checked" type="radio" ng-model="model.scrape_date_type"> Detect From Feed</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_date_type" value="runtime"  class="" checked="checked" type="radio" ng-model="model.scrape_date_type"> Process time</label></div>
													<!-- ngIf: model.scrape_type && model.scrape_type == 'feed' -->
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"> <input name="scrape_date_type" value="xpath" class="" type="radio" ng-model="model.scrape_date_type"> Select from source</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_date_type" value="custom" class="" type="radio" ng-model="model.scrape_date_type"> Enter custom</label></div>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->

									<!-- ngIf: model.scrape_type -->
									<div class="form-group" >
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px; font-size: 24px">
											<div class="col-sm-10">Discussion</div>
											<div class="col-sm-2">
											
											</div>
											</label>
										<div class="col-sm-7" style="margin-top:20px;">
											<div class="form-group field">
												<div class="col-sm-12">
													<div class="checkbox"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_comment" class="" checked="checked" type="checkbox" ng-model="model.scrape_comment"> Allow comments</label></div>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->
								</div>
							</div>
						</div>

						<div class="panel" style="background-image: linear-gradient(white, gray);">
							<div class="panel-heading">
								<div class="col-sm-1"></div>
								<label style="margin-top:20px; font-size: 28px"><a href="#collapse-5" data-toggle="collapse"><i class="icon ion-funnel"></i>Uniqueness Options</a></h4>
							</div>

							<div id="collapse-5" class="panel-collapse collapse in">
								<div class="panel-body">
									<!-- ngIf: model.scrape_type -->
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px">
											<div class="col-sm-10">
												<p style="font-size: 24px">Unique post</p>
												<p style="font-size: 24px;"> check</p> 
											</div>
											<div class="col-sm-2">
												

											</div>
										</label>

										<div class="col-sm-7" style="margin-top:20px;margin-left: 0px;">
											<div class="form-group field">
												<div class="col-sm-12" style="margin-top: 7px">
													<div class="checkbox"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_unique_title" class="" type="checkbox" value="title" ng-model="model.scrape_unique_title"> From title</label></div>
													<div class="checkbox"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_unique_content"  class="" type="checkbox" ng-model="model.scrape_unique_content" value="content"> From content</label></div>
													<div class="checkbox"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_unique_url"  class="" type="checkbox" ng-model="model.scrape_unique_sourceurl" value="unique"> From source url</label></div>
												</div>
											</div>
										</div>
									</div><div class="form-group" >
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px">
											<div class="col-sm-10">
												<p style="font-size: 24px">On existing</p>
												<p style="font-size: 24px;"> POST</p>
											</div>
											<div class="col-sm-2">
												

											</div>
										</label>
										<div class="col-sm-7"  style="margin-top:20px;margin-left: 0px;">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="radio ng-scope" ><label style="font-size: 22px;margin-left: 0px"><input name="scrape_on_unique" value="skip"  class="" checked="checked" type="radio" tyle="font-size: 22px;margin-left: 0px"> skip to next process</label></div><!-- end ngIf: model.scrape_type && model.scrape_type == 'single' -->
													<div class="radio"><label style="font-size: 22px; margin-top:10px"><input name="scrape_on_unique" value="skip"  class="" checked="checked" type="radio" style="font-size: 22px;"> Update post</label></div>
													
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel" style="background-image: linear-gradient(white, gray);">
							<div class="panel-heading">
								<div class="col-sm-1"></div>
								<label style="margin-top:20px; font-size: 28px"><a href="#collapse-6" data-toggle="collapse"><i class="icon ion-calendar"></i>Schedule Options</a></label>
							</div>

							<div id="collapse-6" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group" >
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px; font-size: 24px">
											<div class="col-sm-10">
											Cron type </div>
											<div class="col-sm-2">
											
											</div></label>
										<div class="col-sm-7" style="margin-top:20px;margin-left: 0px;">
											<div class="form-group field">
												<div class="col-sm-12">
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_cron_type" value="wordpress"  checked="checked" type="radio" ng-model="model.scrape_cron_type"> WordPress</label></div>
													<div class="radio"><label style="font-size: 22px;margin-left: 0px"><input name="scrape_cron_type" value="system" class="" type="radio" ng-model="model.scrape_cron_type"> System</label></div>
													
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px"><div class="col-sm-10">Total runs </div>
											<div class="col-sm-2">
											
											</div></label>
										<div class="col-sm-7" style="margin-top:20px;margin-left: 0px;">
											<div class="form-group field">
												<div class="col-sm-12">
													<div class="input-group">
														<input type="text" name="scrape_run_limit" ng-model="model.scrape_run_limit" class="form-control" ng-pattern="/^[1-9][0-9]*$/">
														<div class="input-group-addon">
															times
														</div>
													</div>
												</div>
												<p class="help-block" ng-show="form.scrape_run_limit.$invalid">
													please enter valid value.
												</p>
												<div class="col-sm-12">
													<div class="checkbox">
														<label style="font-size: 22px;margin-left: 0px">
															<div class="col-sm-1">
																<input name="scrape_run_unlimited" class="" checked="checked" type="checkbox" ng-model="model.scrape_run_unlimited"> 
															</div>
															<div class="col-sm-11">
																Unlimited
															</div>
														</label>
													</div>
												</div>
											</div>
										</div>
									</div><!-- end ngIf: model.scrape_type -->
								</div>
							</div>
						</div>

						<div class="panel" style="background-image: linear-gradient(white, gray);">
							<div class="panel-heading">
								<div class="col-sm-1"></div>
								<label style="margin-top:20px;font-size: 28px"><a href="#collapse-7" data-toggle="collapse"><i class="icon ion-gear-a"></i>Other Options</a></label>
							</div>

							<div id="collapse-7" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="form-group"  >
										<div class="col-sm-1"></div>
										<label class="col-sm-4 control-label" style="margin-top:20px;font-size: 24px">
											<div class="col-sm-10">
											<p style="font-size: 24px">Time Process</p>
											</div> 
											<div class="col-sm-2">
												

											</div>
										</label>
										<div class="col-sm-7"  style="margin-top: 65px;margin-left: 0px;">
											<div class="form-group field">
												<div class="col-sm-12">
													<div class="input-group">
														<input name="scrape_time_schedule" placeholder="e.g. 100" class="form-control" required="required" value="24" type="text">
														<div class="input-group-addon" style="font-size: 20px">Seconds</div>
													</div>
													<p class="help-block" style="margin-top: 20px">Please enter a valid value.</p>
												</div>
												<div class="col-sm-3 pull-right">
													<button class="btn btn-primary" type="button" style="padding:10px 20px;" ng-click="submit()" ng-if="model.create_type">Update</button>
													<button class="btn btn-primary" type="button" style="padding:10px 20px;" ng-click="submit()" ng-if="!model.create_type">Create</button>
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>

		
		</div>
	</div>

	<div id="loading" class="modal">
		<div class="modal-dialog">
			<div class="rotate">
				<i class="icon ion-gear-a"></i>
			</div>
		</div>
	</div>

	<div id="error" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<i class="icon ion-android-close" data-dismiss="modal"></i>
				</div>

				<div class="modal-body">
					<i class="icon ion-alert-circled"></i>
					<p class=""></p>
				</div>
			</div>
		</div>
	</div>

	<div id="iframe" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					
					<i class="icon ion-android-close" data-dismiss="modal"></i>
				</div>

				<div class="modal-body">
					<iframe id="iframe_serial" frameborder="0"></iframe>
					<iframe id="iframe_single" frameborder="0"></iframe>
				</div>
			</div>
		</div>
	</div>

	<div id="iframe_load" class="modal fade">
		<div style="width:1000px;height: 700px;">
			<i class="icon ion-ios-settings" class="load" style="margin-left:50%;margin-top:50%;"></i>
		</div>
	</div>
</div></div><!-- /post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<div id="side-sortables" class="meta-box-sortables ui-sortable"></div></div>
<div id="postbox-container-2" class="postbox-container">
<div id="normal-sortables" class="meta-box-sortables ui-sortable" style=""></div><div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div></div>
</div>
<br class="clear">
</div>
</form>
</div>
<div class="clear"></div></div>
<div class="clear"></div></div>
<div class="clear"></div></div>	

	<div id="wp-link-backdrop" style="display: none"></div>
		<div id="wp-link-wrap" class="wp-core-ui" style="display: none" role="dialog" aria-labelledby="link-modal-title">
		<form id="wp-link" tabindex="-1" action="post.php" method = "POST">
		<input id="_ajax_linking_nonce" name="_ajax_linking_nonce" value="df23f81cd5" type="hidden">		<h1 id="link-modal-title">Insert/edit link</h1>
		<button type="button" id="wp-link-close"><span class="screen-reader-text">Close</span></button>
		<div id="link-selector">
			<div id="link-options">
				<p class="howto" id="wplink-enter-url">Enter the destination URL</p>
				<div>
					<label><span>URL</span>
					<input id="wp-link-url" aria-describedby="wplink-enter-url" type="text"></label>
				</div>
				<div class="wp-link-text-field">
					<label><span>Link Text</span>
					<input id="wp-link-text" type="text"></label>
				</div>
				<div class="link-target">
					<label><span></span>
					<input id="wp-link-target" type="checkbox"> Open link in a new tab</label>
				</div>
			</div>
			<div id="error" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<i class="icon ion-android-close" data-dismiss="modal"></i>
					</div>

					<div class="modal-body">
						<i class="icon ion-alert-circled"></i>
						<p>{{error}}</p>
					</div>
				</div>
			</div>
		</div>

			<p class="howto" id="wplink-link-existing-content">Or link to existing content</p>
			<div id="search-panel">
				<div class="link-search-wrapper">
					<label>
						<span class="search-label">Search</span>
						<input id="wp-link-search" class="link-search-field" autocomplete="off" aria-describedby="wplink-link-existing-content" type="search">
						<span class="spinner"></span>
					</label>
				</div>
				<div id="search-results" class="query-results" tabindex="0">
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
				<div id="most-recent-results" class="query-results" tabindex="0">
					<div class="query-notice" id="query-notice-message">
						<em class="query-notice-default">No search term specified. Showing recent items.</em>
						<em class="query-notice-hint screen-reader-text">Search or use up and down arrow keys to select an item.</em>
					</div>
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
 				</div>
 			</div>
		</div>
		<div class="submitbox">
			<div id="wp-link-cancel">
				<button type="button" class="button">Cancel</button>
			</div>
			<div id="wp-link-update">
				<input value="Add Link" class="button button-primary" id="wp-link-submit" name="wp-link-submit" type="submit">
			</div>
		</div>
		</form>
		</div>
		
<div class="clear"></div>	

</div>
