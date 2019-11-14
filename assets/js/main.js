var jq_scrape = $.noConflict();
angular
	.module('nsscraps', [])
	.controller('controller', function(
		$scope,
	    $timeout,
	    $compile
	)
	{
		$scope.step = 1;
		$ = jq_scrape;
		$scope.model = {};//main data
		$scope.model.post_categorytype = "";
		$scope.ele_data = {};
		$scope.categoryvalue = [];
		$scope.enable = false;
		if(!$scope.url)
		{
			$scope.url = '';	
		}
		$scope.enabled = false;

		$scope.nextstep = function()
		{

		}

		$scope.$watch('model.scrape_url',function(newvalue){
			if($scope.urlcomponent)
			{
				$scope.url = $scope.urlcomponent;
				$scope.urlcomponent = '';
			}
			else
			{
				$scope.url = newvalue;	
			}
			$scope.enable = true;
			
		})

		$scope.input_cookies = function() {
			var cookie_string = '';
			var names = $('input[type="text"][name="scrape_cookie_names[]"]');
			var values = $('input[type="text"][name="scrape_cookie_values[]"]');

			$.each(names, function(index, item) {
				cookie_string += '&cookie_names[]=' + encodeURIComponent($(item).val());
				cookie_string += '&cookie_values[]=' + encodeURIComponent($(values[index]).val());
			});

			return cookie_string;
		};


		$scope.init = function()
		{
			$('.postbox-container').remove();
			$('.notice').remove();
			$('#wpfooter').remove();
			console.log("aaa");
			//console.log($scope.url);
			$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'get_post'
					},
					success: function(response) {
						if(response)
						{
							response = JSON.parse(response);
							$scope.scrape_post_type = response;
						}

					},
					error:function(res)
					{
						console.log("aaa");
						res = JSON.parse(res.responseText.substr(0,res.responseText.length - 1));
						$scope.post_type = res;
						$scope.model.categoryvalue = {};

						$.each($scope.post_type,function(key,value){
							$scope.model.categoryvalue[value] = {};
						})
					}
				});

				$.ajax({
					url:ajaxurl,
					type:'post',
					dataType:'json',
					data:{
						action:'get_category'
					},
					success:function(res)
					{
						$scope.category = res;
						$scope.$apply();
					}
				})

				$scope.initdialog();
				$scope.set_template_tags();
				
		}
		$scope.initdialog = function(){

			if($scope.model.scrape_post_content_template)
			{
				$('#ns_scrapetemplate').text($scope.model.scrape_post_content_template);
				$scope.model.scrape_content_template_status = true;
			}
			else
			{
				$scope.model.scrape_content_template_status = false;	
			}

			if($scope.model.scrape_title_template)
			{
				$scope.model.scrape_title_template_status = true;
			}
			else
			{
				$scope.model.scrape_title_template_status = false;	
			}
			if($scope.model.scrape_category_value)
			{
				$scope.model.scrape_category_value = JSON.parse($scope.model.scrape_category_value);	
			}
			else
			{
				$scope.model.scrape_category_value = [];
			}
			if($scope.model.scrape_customfield)
			{
				$scope.model.scrape_customfield = JSON.parse($scope.model.scrape_customfield);
			}

			if($scope.model.scrape_excerpt)
			{
				$scope.model.scrape_excert_type = 'xpath';
			}
			$.each($scope.model.scrape_customfield,function(key,value){
				$scope.addcustomfield($('.addcustomfield'),'custom',value);
			})

		}

		$scope.set_template_tags = function() {
			$(document).on('click', '.input-tags .btn', function() {
				var pos = 0;
				var range;
				var browser;
				var text = $(this).data('value');
				var target = $(this).parent().prev().find('input[type="text"]');

				if (!target.length) {
					target = $('textarea.wp-editor-area');

					if (target.is(':hidden')) {
						$('#ns_scrapetemplate-html').click();
					}
				}

				target.focus();
				var input = document.activeElement;

				if (input.selectionStart || input.selectionStart == '0') {
					browser = 'ff'
				} else {
					if (document.selection) {
						browser = 'ie'
					} else {
						browser = false;
					}
				}

				if (browser == 'ie') {
					input.focus();
					range = document.selection.createRange();
					range.moveStart ("character", -input.value.length);
					pos = range.text.length;
				} else if (browser == 'ff') {
					pos = input.selectionStart;
				}

				var front = (input.value).substring(0, pos);
				var back = (input.value).substring(pos, input.value.length);

				input.value = front + text + back;
				pos = pos + text.length;

				if (browser == 'ie') {
					input.focus();
					range = document.selection.createRange();
					range.moveStart ('character', -input.value.length);
					range.moveStart ('character', pos);
					range.moveEnd('character', 0);
					range.select();
				}

				else if (browser == "ff") {
					input.selectionStart = pos;
					input.selectionEnd = pos;
					input.focus();
				}
			});
		};


		$scope.getangularclass = function(id)
		{
			id = "" + id;
			if($scope.model.scrape_category_value && $scope.model.scrape_category_value.indexOf(id)>=0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		$scope.$watch('model.post_categorytype',function(newvalue){
			$.ajax({
				url:ajaxurl,
				type:'post',
				dataType:'json',
				data:{
					action:'get_post_category',
					post_name:newvalue
				},
				success:function(res)
				{

				},
				error:function(res){
					if(res != '0')
					{
						var response = JSON.parse(res.responseText.substr(0,res.responseText.length-1));
						$scope.categories = response;
						$.each($scope.post_type, function(key,value){
							$scope.model.categoryvalue[value] = {};
						});
						$scope.$apply();
					}
				}
			})
		});

		

		$scope.addcustomfield  = function($event,type,value = null){
			if(type == 'cookie')
			{
				$($event.target).closest('.form-group').before($compile(
					'<div class="form-group">' +
						'<div class="col-sm-12">' +
							'<div class="input-group">' +
								'<div class="input-group-addon">Name</div>' +
								'<input type="text" name="scrape_cookie_names[]" placeholder="cookie" class="form-control" attr="name">' +
								'<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove($event)"><i class="icon ion-trash-a"></i></button></span>' +
							'</div>' +
						'</div>' +
						'<div class="col-sm-12">' +
							'<div class="input-group">' +
								'<div class="input-group-addon">Value</div>' +
								'<input type="text" name="scrape_cookie_values[]" placeholder="value" class="form-control" attr="value">' +
							'</div>' +
						'</div>' +
					'</div>'
					)($scope));
			}
			else if(type == 'custom')
			{	
				array_value = {Custom_Name:'',Custom_Value:''};
				if(value){
					array_value = value;
				}
				// console.log(iframe_element);
				var new_data_div = '<div class="form-group">' +
						'<div class="col-sm-12">' +
							'<div class="input-group">' +
								'<div class="input-group-addon">Name</div>' +
								'<input type="text" name="Custom_Name" placeholder="Name" class="form-control" attr="name" value="' + array_value.Custom_Name + '">' +
								'<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove($event)"><i class="icon ion-trash-a"></i></button></span>' +
							'</div>' +
						'</div>' +
						'<div class="col-sm-12">' +
							'<div class="input-group">' +
								'<div class="input-group-addon">Value</div>' +
								 '<input type="text" name="Custom_Value" placeholder="Value" class="form-control" attr="value" value="' + array_value.Custom_Value + '">' +
								 '<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="iframe_view($event,\'serial\',\'data_page\')"><i class="icon ion-android-locate"></i></button></span>'+
							'</div>' +
						'</div>' +
					'</div>';
					console.log(new_data_div);
					var element = $($event.target);
					if(!$event.target)
					{
						element = $event;
					}
					console.log(element);
				element.closest('.form-group').before($compile(
					new_data_div
				)($scope));
			}
		}

		$scope.get_featured_image = function($event){
			if($scope.model.scrape_featured_type == 'gallery')
			{
				var media = wp.media({
					multiple:false,
					title:"Scrape Library",
					library:{
						type:'image'
					}
				});

				media.on('select',function(){
					$scope.model.scrape_featured = media.state().get('selection').first().toJSON().url;
					
					$scope.$apply();
				})

				media.open();
			}
			else
			{
				$scope.iframe_view($event,'','data_page');
			}
			//$scope.$apply();
		}
		$scope.add_newrule = function($event,type)
		{
			$($event.target).closest('.form-group').before($compile(
					'<div class="form-group field" ng-show="model.scrape_' + type + '">' +
						'<div class="col-sm-12">' +
							'<div class="input-group">' +
								'<div class="input-group-addon">Find</div>' +
								'<input type="text" name="scrape_' + type +  '_Find" placeholder="" class="form-control" attr="find">' +
								'<span class="input-group-btn"><button type="button" class="btn btn-primary btn-block" ng-click="remove($event)"><i class="icon ion-trash-a"></i></button></span>' +
							'</div>' +
						'</div>' +
						'<div class="col-sm-12">' +
							'<div class="input-group">' +
								'<div class="input-group-addon">Replace</div>' +
								'<input type="text" attr="replace" name="scrape_'+ type + '_Replace" placeholder="" class="form-control">' +
							'</div>' +
						'</div>' +
					'</div>'
					)($scope));
		}

		$scope.createcategory = function()
		{

			if($scope.model.scrape_category)
			{

				var data = {};
				data['taxname'] = $scope.model.scrape_category;
				data['value'] = $('input[name=scrape_categoryxpath]').val();
				if(data['value'])
				{
					$.ajax({
						url:ajaxurl,
						type:'post',
						datatype:'json',
						data:{data:data,action:'createcategory'},
						success:function(res){
							data['id'] = res['id'];
							$scope.categoryvalue.push(data);
							console.log($scope.categoryvalue);
							$scope.$apply();
						}
					});
				}	
			}
			
		}

		$scope.remove = function($event)
		{
			$($event.target).closest('.form-group').remove();
		}

		$scope.iframe_view_load = function($event,type,action)
		{
			var current = $('#iframe_serial');
				
				console.log($('#iframe_serial')[0].srcdoc);
				$('#iframe_serial').contents().find('head').append(
					$('<link/>', {
						rel: 'stylesheet',
						type: 'text/css',
						href: plugin_path + '/Pligin_Scrapes/assets/css/iframe.css',
						id: 'ns_scrape'
				}));

				$('#iframe_serial').contents()
				.on('mouseover', function(event) {
					$(event.target).addClass('ol_scrapes_inspector');
				})
				.on('mouseout', function(event) {
					$(event.target).removeClass('ol_scrapes_inspector');
				})
				.on('click',function(event){
					var element = $(event.target);
					console.log(element);
					url1 = "";
					if(action == 'url')
					{	
						while(element != current)
						{
							var url = element[0].tagName;
							if(!url)
							{
								element = element.parent();
								break;
							}
							url = url.toLowerCase();
							if(url == 'a')
							{
								$scope.url = element.attr('href');
								
							}
	
							var index_path = 0;var index_path_url = 0;
							var element_parent = element.parent();
							element_parent.children().each(function(index){
								if($(this)[0] == element[0])
								{
									index_path_url = index;
								}
							});
							if(index_path_url > 0)
							{
								url += "[" + index_path_url + "]";
							}
							url = '/' + url;
							url1 = url + url1;
							element = element.parent();
						}
						var element = $($event.target);
						while(!element.hasClass('input-group'))
						{
							element = element.parent();	
						}
						element.find('input').val("/" + url1);
						action = '';
						
					}

					else if(action == 'data_page' || action == 'next_page')
					{
						var classname = '';

						while(element != current)
						{
							var url = element[0].tagName;
							if(!url)
							{
								break;
							}
							if(element.hasClass('ol_scrapes_inspector'))
							{
								element.removeClass('ol_scrapes_inspector');
							}

							url = url.toLowerCase();
							
							var element_path = element.parent();
							var index_path = 0; var path_enable;
							element_path.children().each(function(index){
								if($(this)[0].tagName.toLowerCase() == url)
								{
									index_path++;
								}
								if($(this)[0] == element[0])
								{
									path_enable = index_path;
								}
							})
							var element_class = url;
							if(index_path > 1)
							{
								 element_class += '[' + path_enable + ']';	
							}
							
							
							classname = '/' + element_class + classname;
							element = element.parent();
							
						}
						var element = $($event.target);
						while(!element.hasClass('input-group'))
						{
							element = element.parent();	
						}
						element.find('input').val("/" + classname);
						action = '';
					}

					else if(action == 'image')
					{
						action = '';
						console.log(element[0].nodeName);
						var url = "";
						if(element[0].nodeName == 'IMG')
						{
							url = element.attr('src');
						}
						else
						{
							element.find('img').each(function(index){
								url = $(this).attr('src');
							});	
						}
						console.log(url);
						$scope.model.scrape_featured = url;	
					}
					else if(action == 'data')
					{
						action = '';
						var content;
						while(element[0] && element[0].tagName)
						{
							content = element[0].innerHTML;
							element = element.children().eq(0);
						}
						var element = $($event.target);
						while(!element.hasClass('input-group'))
						{
							element = element.parent();	
						}
						element.find('input').val(content);
						
					}
					$scope.$apply();
					$('#iframe').modal('hide');
					return false;
			})		
		}

		

		$scope.iframe_view = function($event,type,action)
		{
			var action_url = '';
			
			if(!$scope.url ||  $scope.enable || action == 'next_page' || action == 'url')
			{
				if(action == 'url' || action == 'next_page')
				{
					action_url = $scope.model.scrape_url;
				}
				else
				{
					action_url = $scope.url;
					$scope.enable = false;
				}
				var url_serial = ajaxurl + "?action=get_url&address="+action_url + $scope.input_cookies();
				
				if($scope.model.post_type == 'feed')
				{
					url_serial += '&feed=true';
					
				}
				$('#iframe_serial').attr('src',url_serial);
				$('#loading').modal('show');
				
				$('#iframe_serial').on('load',function(){
					$('#loading').modal('hide');
					$('#iframe_serial').show();
					$('#iframe').modal('show');
					$scope.iframe_view_load($event,type,action);
					action = '';	
				})
			}
			else
			{
				$('#iframe_serial').show();
				$('#iframe').modal('show');
				$scope.iframe_view_load($event,type,action);
			}

			
		}

	$scope.addcategory = function(){

		var tax = $scope.model.scrape_category;
		var category = $('select[name="scrape_post_category"]').val();
		var enable = true;

		$.each($scope.categoryvalue,function(key,value){
			if(value['id'] == category)
			{

				enable = false;
			}
		})

		if(enable)
		{	
			$.each($scope.category,function(key,value){
				if(value.id == category)
				{
					$scope.categoryvalue.push({taxname:tax,id:category,value:value.name});
					$scope.$apply();
				}
			})
		}


	}

	$scope.submit = function()
	{
		var prepare_data = {};
		$('.bootstrap').find('.field').each(function(index){
			if(!$(this).hasClass('ng-hide'))
			{
				$scope.prepare_data($(this));	
			}
		})
		$scope.ele_data['scrape_post_custom_field'] = [];
		$('.custom-field').find('.ng-scope').each(function(index){
			var enable = true; var data = {};
			$(this).find('input').each(function(index){
				if($(this).val())
				{
					data[$(this).attr('name')] = $(this).val();	
				}
				else
				{
					enable = false;
				}
			});
			if(enable)
			{

				$scope.ele_data['scrape_post_custom_field'].push(data);
			}
		})

		$scope.ele_data['scrape_post_category'] = [];
		$('.category').find('input').each(function(index){
			if($(this).prop('checked'))
			{
				$scope.ele_data['scrape_post_category'].push($(this).val());
			}
		})

		if($scope.model.scrape_content_template_status)
		{
			var content = $('#ns_scrapetemplate').val();
			$scope.ele_data['scrape_post_content_template'] = content;
		}
		if($scope.model.scrape_content_type == 'xpath')
		{
			var content = $('input[name="scrape_content"]').val();
			$scope.ele_data['scrape_post_content'] = content;

		}
		
		$scope.ele_data['scrape_real_url'] = $scope.url;
		$.ajax({
			url:'post-new.php?post_type=ns_scrape&&wp-post-new-reload=true',
			type:'post',
			data:{data:$scope.ele_data,post_type:'ns_scrape'},
			success:function(res){
				res = JSON.parse(res);
				console.log(res);
				document.location.href = res['url'];
				$('#loading').modal('show');
			},
			error:function(res){
				document.location.href = res.url;
			}
		})	
	}

	$scope.prepare_data = function(ele)
	{
		var index = 0;
		ele.find('input').each(function(index){
			if($(this).val() && $(this).attr('type') == 'text' && !$(this).hasClass('ng-hide'))
			{
				$scope.ele_data[$(this).attr('name')]	= $(this).val(); 
			}
			if($(this).val() && $(this).attr('type') == 'checkbox' && $(this)[0].checked)
			{
				$scope.ele_data[$(this).attr('name')] = $(this).val();
			}
			if($(this).val() && $(this).attr('type') == 'radio' && $(this)[0].checked)
			{
				$scope.ele_data[$(this).attr('name')] = $(this).val();
			}
		});

		ele.find('select').each(function(index){
			$scope.ele_data[$(this).attr('name')] = $(this).val();
		});

		$scope.ele_data['scrape_post_category'] = [];

		
		$scope.ele_data['scrape_custom_field'] = [];
		

	}

	$scope.removecategory = function(index){
		
		var new_array1= []; var new_array2 = [];
		
		if(index > 0)
			new_array = $scope.categoryvalue.slice(0,index-1);
		if(index < $scope.categoryvalue.length - 1)
		{
			new_array2 = $scope.categoryvalue.slice(index+1,$scope.categoryvalue.length-1);
		}

		$scope.categoryvalue = new_array1.concat(new_array2);
		$scope.$apply();
	}

	$scope.add_scrape_categoryvalue = function()
	{
		if($scope.form.scrape_categoryxpath.$valid && $scope.form.scrape_categoryxpath_separator.$valid)
		{
			$scope.model.categoryvalue[$scope.model.post_categorytype][$scope.model.scrape_category] = $scope.category_edit_value;
		}
	}
});
jQuery = jQuery_scrapes.noConflict();
$ = jQuery_scrapes.noConflict();