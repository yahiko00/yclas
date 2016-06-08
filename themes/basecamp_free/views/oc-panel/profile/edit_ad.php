<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="col-xs-12">
			<div class="page-header">
				<h3><?=__('Edit Advertisement')?> <small><?=$ad->title?></small></h3>	
			</div>

			<?=Form::errors()?>

			<?$str=NULL;switch ($ad->status) {
				case Model_Ad::STATUS_NOPUBLISHED:
					$str = __('NOPUBLISHED');
					break;
				case Model_Ad::STATUS_PUBLISHED:
					$str = __('PUBLISHED');
					break;
				case Model_Ad::STATUS_UNCONFIRMED:
					$str = __('UNCONFIRMED');
					break;
				case Model_Ad::STATUS_SPAM:
					$str = __('SPAM');
					break;
				case Model_Ad::STATUS_UNAVAILABLE:
					$str = __('UNAVAILABLE');
					break;
				default:
					break;
			}?>

			<div class="text-right pad_10">
				<a class="btn btn-success" target="_blank" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
					<?=__('View Advertisement')?>
				</a>
				<? if( $ad->status == Model_Ad::STATUS_UNAVAILABLE AND !in_array(core::config('general.moderation'), Model_Ad::$moderation_status)):?>
					<a href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'activate','id'=>$ad->id_ad))?>" 
						class="btn btn-success" 
						title="<?=__('Activate?')?>" 
						data-toggle="confirmation" 
						data-btnOkLabel="<?=__('Yes, definitely!')?>" 
						data-btnCancelLabel="<?=__('No way!')?>">
						<i class="glyphicon glyphicon-ok"></i> <?=__('Activate')?>
					</a>
				<?endif?>
				<button class="btn btn-warning" type="button" data-toggle="collapse" data-target="#adInfo" aria-expanded="false" aria-controls="adInfo">
					<i class="fa fa-info-circle"></i>
				</button>
			</div>

			<div class="collapse" id="adInfo">
				<div class="pad_10">
					<?if(Auth::instance()->get_user()->id_role == Model_Role::ROLE_ADMIN):?>
						<div class="panel panel-default table-responsive">
						<? $owner = new Model_User($ad->id_user)?>
						<table class="table table-bordered admin-table-user">
							<thead>
								<tr>
									<th><?=__('Profile')?></th>
									<th><?=__('Email')?></th>
									<th><?=__('Status')?></th>
								</tr>
							</thead>	
							<tbody>
								<tr>
									<td><p><?= $owner->name?></p></td>
									<td><p><?= $owner->email?></p></td>
									<td><p><?=$str?></p></td>
								</tr>
							</tbody>
						</table>
						</div>
					<?endif?>
					<?if (count($orders) > 0) :?>
						<div class="text-center">
							<?foreach ($orders as $order):?>
							<a class="btn btn-success mt-3" href="<?=Route::url('default', array('controller'=> 'ad','action'=>'checkout' , 'id' => $order->id_order))?>">
								<i class="glyphicon glyphicon-shopping-cart"></i> <?=$order->description?>  
							</a>
							<?endforeach?>
						</div>
					<?endif?>
				</div>
				<hr>
			</div>

			<!-- Boost Ad -->
			<?if((core::config('payment.pay_to_go_on_top') > 0 AND core::config('payment.to_top') != FALSE )
				OR (core::config('payment.to_featured') != FALSE AND $ad->featured < Date::unix2mysql() )):?>
					<div class="alert alert-success text-center alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<?if(core::config('payment.pay_to_go_on_top') > 0 AND core::config('payment.to_top') != FALSE):?>
							<p class="text-info"><?=__('Your Advertisement can go on top again! For only ').i18n::format_currency(core::config('payment.pay_to_go_on_top'),core::config('payment.paypal_currency'));?></p>
							<a class="btn btn-xs btn-primary" type="button" href="<?=Route::url('default', array('action'=>'to_top','controller'=>'ad','id'=>$ad->id_ad))?>"><?=__('Go Top!')?></a>
						<?endif?>
						<br>
						<br>
						<?if(core::config('payment.to_featured') != FALSE AND $ad->featured < Date::unix2mysql()):?>
							<p class="text-info"><?=__('Your Advertisement can go to featured! For only ').i18n::format_currency(Model_Order::get_featured_price(),core::config('payment.paypal_currency'));?></p>
							<a class="btn btn-xs btn-primary" type="button" href="<?=Route::url('default', array('action'=>'to_featured','controller'=>'ad','id'=>$ad->id_ad))?>"><?=__('Go Featured!')?></a>
						<?endif?>
					</div>
			<?endif?>

			<div class="col-md-12">
			<?= FORM::open(Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad)), array('class'=>'form-horizontal edit_ad_form', 'enctype'=>'multipart/form-data'))?>
				<fieldset class="pad_10 ">
					
					<!-- START TITLE -->
					<div class="form-group">
						<div class="col-xs-12">
							<?= FORM::label('title', __('Title'), array('class'=>'', 'for'=>'title'))?>
							<?= FORM::input('title', $ad->title, array('placeholder' => __('Title'), 'class' => 'form-control', 'id' => 'title', 'required'))?>
						</div>
					</div>
					<!-- END TITLE -->
					
					<!-- START CATEGORY -->
					<div class="form-group">
						<div class="col-xs-12">
							<?= FORM::label('category', __('Category'), array('for'=>'category'))?>
							<div id="category-chained" class="hidden"
								data-apiurl="<?=Route::url('api', array('version'=>'v1', 'format'=>'json', 'controller'=>'categories'))?>" 
								data-price0="<?=i18n::money_format(0)?>" 
								<?=(core::config('advertisement.parent_category')) ? 'data-isparent' : NULL?>
							>
								<div id="select-category-template" class="col-sm-6 row hidden">
									<select class="disable-select2 select-category" placeholder="<?=__('Pick a category...')?>"></select>
								</div>
								<div id="paid-category" class="hidden">
									<span class="help-block" data-title="<?=__('Category %s is a paid category: %d')?>"><span class="text-warning"></span></span>
								</div>
							</div>
			
							<div id="category-edit">
								<div class="col-sm-6 row">
									<div class="input-group">
										<input class="form-control" type="text" placeholder="<?=$ad->category->name?>" disabled>
										<span class="input-group-btn">
											<button class="btn btn-default" type="button"><?=__('Edit category')?></button>
										</span>
									</div>
								</div>
							</div>
							<input id="category-selected" name="category" value="<?=$ad->id_category?>" class="form-control invisible" style="height: 0; padding:0; width:1px; border:0;" required></input>
						</div>
					</div>
					<!-- END CATEGORY -->
					
					<!-- START LOCATION -->
					<div class="form-group">
						<div class="col-xs-12">
							<?= FORM::label('locations', __('Location'), array('for'=>'location'))?>
								<div id="location-chained" class="hidden" data-apiurl="<?=Route::url('api', array('version'=>'v1', 'format'=>'json', 'controller'=>'locations'))?>">
									<div id="select-location-template" class="col-sm-6 row hidden">
										<select class="disable-select2 select-location" placeholder="<?=__('Pick a location...')?>"></select>
									</div>
								</div>
								<div id="location-edit">
									<div class="col-sm-6 row">
										<div class="input-group">
											<input class="form-control" type="text" placeholder="<?=$ad->location->name?>" disabled>
											<span class="input-group-btn">
												<button class="btn btn-default" type="button"><?=__('Edit location')?></button>
											</span>
										</div>
									</div>
								</div>
								<input id="location-selected" name="location" value="<?=$ad->id_location?>" class="form-control invisible" style="height: 0; padding:0; width:1px; border:0;" required></input>
						</div>
					</div>
					<!-- END LOCATION -->
					
					<!-- START PRICE AND STOCK -->
					<?if((core::config('payment.stock')) OR (core::config('advertisement.price') != FALSE)):?>
						<div class="form-group">
							<?if(core::config('payment.stock')):?>
								<div class="col-xs-6">
									<?= FORM::label('stock', __('In Stock'), array('class'=>'', 'for'=>'stock'))?>
									<div class="input-prepend">
										<?= FORM::input('stock', $ad->stock, array('placeholder' => '10', 'class' => 'form-control fc-small', 'id' => 'stock', 'type'=>'text'))?>
									</div>
								</div>
							<?endif?>
							<?if(core::config('advertisement.price') != FALSE):?>
								<div class="col-xs-6">
									<?= FORM::label('price', __('Price'), array('class'=>'', 'for'=>'price'))?>
									<div class="input-prepend">
										<?= FORM::input('price', $ad->price, array('placeholder'=>html_entity_decode(i18n::money_format(1)),'class'=>'form-control fc-small', 'id' => 'price', 'data-error' => __('Please enter only numbers.')))?>
									</div>
								</div>
							<?endif?>
						</div>
					<?endif?>
					<!-- END PRICE AND STOCK -->
					
					<!-- START DESCRIPTION -->
					<?if(core::config('advertisement.description') != FALSE):?>
						<div class="form-group">
							<div class="col-xs-12">
								<?= FORM::label('description', __('Description'), array('class'=>'', 'for'=>'description', 'spellcheck'=>TRUE))?>
								<?= FORM::textarea('description', $ad->description, array('class'=>'form-control col-md-9 col-sm-9 col-xs-12'.((Core::config("advertisement.description_bbcode"))?NULL:' disable-bbcode'), 'name'=>'description', 'id'=>'description', 'rows'=>8, 'required'))?>
							</div>
						</div>
					<?endif?>
					<!-- END DESCRIPTION -->
					
					<!-- START PHONE -->
					<?if((core::config('advertisement.phone') != FALSE) OR (core::config('advertisement.address') != FALSE)):?>
						<div class="form-group">
							<div class="col-xs-12">
								<?= FORM::label('phone', __('Phone'), array('class'=>'', 'for'=>'phone'))?>
								<?= FORM::input('phone', $ad->phone, array('class'=>'form-control', 'id'=>'phone', 'placeholder'=>__('Phone')))?>
							</div>
						</div>
					<?endif?>
					<!-- END PHONE -->
					
					<!-- START LOCATION -->
					<?if(core::config('advertisement.address') != FALSE):?>
						<div class="form-group">
							<div class="col-xs-12">
								<?= FORM::label('address', __('Address'), array('class'=>'', 'for'=>'address'))?>
								<?if(core::config('advertisement.map_pub_new')):?>
									<div class="input-group">
										<?= FORM::input('address', $ad->address, array('class'=>'form-control', 'id'=>'address', 'placeholder'=>__('Address')))?>
										<span class="input-group-btn">
											<button class="btn btn-default locateme" type="button"><?=__('Locate me')?></button>
										</span>
									</div>
								<?else:?>
									<?= FORM::input('address', $ad->address, array('class'=>'form-control', 'id'=>'address', 'placeholder'=>__('Address')))?>
								<?endif?>
							</div>
						</div>
						<?if(core::config('advertisement.map_pub_new')):?>
							<div class="popin-map-container">
								<div class="map-inner" id="map" 
									data-lat="<?=($ad->latitude)? $ad->latitude:core::config('advertisement.center_lat')?>" 
									data-lon="<?=($ad->longitude)? $ad->longitude:core::config('advertisement.center_lon')?>"
									data-zoom="<?=core::config('advertisement.map_zoom')?>" 
									style="height:200px;max-width:400px;margin-bottom:5px;">
								</div>
							</div>
							<input type="hidden" name="latitude" id="publish-latitude" value="<?=$ad->latitude?>" <?=is_null($ad->latitude) ? 'disabled': NULL?>>
							<input type="hidden" name="longitude" id="publish-longitude" value="<?=$ad->longitude?>" <?=is_null($ad->longitude) ? 'disabled': NULL?>>
						<?endif?>
					<?endif?>
					<!-- END LOCATION -->
					
					<!-- START WEBSITE -->
					<?if(core::config('advertisement.website') != FALSE):?>
						<div class="form-group">
							<div class="col-sm-8">
								<?= FORM::label('website', __('Website'), array('class'=>'', 'for'=>'website'))?>
								<?= FORM::input('website', $ad->website, array('class'=>'form-control', 'id'=>'website', 'placeholder'=>__('Website')))?>
							</div>
						</div>
					<?endif?>
					<!-- END WEBSITE -->

							
							
							
							
							
							<div class="form-group images" 
								data-max-image-size="<?=core::config('image.max_image_size')?>" 
								data-image-width="<?=core::config('image.width')?>" 
								data-image-height="<?=core::config('image.height') ? core::config('image.height') : 0?>" 
								data-image-quality="<?=core::config('image.quality')?>" 
								data-swaltext="<?=sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('image.max_image_size'))?>">
								<div class="col-md-12">
									<div class="row">
										<?$images = $ad->get_images()?>
										<?if($images):?>
											<?foreach ($images as $key => $value):?>
												<?if(isset($value['thumb'])): // only formated images (not originals)?>
													<div id="img<?=$key?>" class="edit-image text-center display-inline-block m-5">
														<a><img style="width: 150px;" src="<?=$value['thumb']?>" class="img-rounded thumbnail"></a>
														<button class="btn btn-danger index-delete img-delete"
																data-title="<?=__('Are you sure you want to delete?')?>" 
																data-btnOkLabel="<?=__('Yes, definitely!')?>" 
																data-btnCancelLabel="<?=__('No way!')?>"
																type="submit" 
																name="img_delete"
																value="<?=$key?>" 
																href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>" 
																title="<?=__('Delete image')?>">
																<i class="fa fa-trash"></i>
														</button>
														<?if ($key > 1) :?>
															<button class="btn btn-success img-primary"
																type="submit" 
																name="primary_image"
																value="<?=$key?>" 
																title="<?=__('Set image as primary')?>"
																href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>" 
																action="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>"
															>
															<i class="fa fa-check"></i>
															</button>
														<?endif?>
													</div>
												<?endif?>
											<?endforeach?>
										<?endif?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<?if (core::config('advertisement.num_images') > count($images)):?> <!-- permition to add more images-->
									<div class="col-xs-12">
										<hr>
										<br>
										<?= FORM::label('images', __('Add image'), array('class'=>'', 'for'=>'images0'))?>
										<br>
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
												<div>
													<span class="btn btn-default btn-file">
														<span class="fileinput-new"><?=__('Select')?></span>
														<span class="fileinput-exists"><?=__('Edit')?></span>
														<input type="file" name="image0" id="fileInput0" accept="<?='image/'.str_replace(',', ', image/', rtrim(core::config('image.allowed_formats'),','))?>">
													</span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?=__('Delete')?></a>
												</div>
										</div>
									</div>
								<?endif?>
							</div>

							
							 <div class="form-group text-center">
								<br>
									<hr>
								<br>	
									<?= FORM::button('submit_btn', (in_array(core::config('general.moderation'), Model_Ad::$moderation_status))?__('Publish'):__('Update'), array('type'=>'submit', 'class'=>'btn btn-success', 'action'=>Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))))?>
								<br>
							</div>
				</fieldset>
            <?= FORM::close()?>
        </div>

		<div class="clearfix"></div>

		</div>
	</div>
</div>


<div class="modal modal-statc fade" id="processing-modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-body">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?=__('Processing...')?></h4>
				</div>
				<div class="modal-body">
					<div class="progress progress-striped active">
						<div class="progress-bar" style="width: 100%"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>