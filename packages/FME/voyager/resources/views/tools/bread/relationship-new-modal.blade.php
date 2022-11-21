<div class="modal modal-relationships fade" tabindex="-1" role="dialog" id="new_relationship_modal" data-backdrop="static">
	<div class="modal-dialog modal-lg relationship-panel" role="document">
		<div class="modal-content">
			<div class="modal-header bg-pink text-white border-bottom-0">
				<h4 class="modal-title">
					{{ \Illuminate\Support\Str::singular(ucfirst($table)) }} {{ __('voyager::database.relationship.relationships') }}
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body bg-white">
				<form role="form" action="{{ route('voyager.bread.relationship') }}" method="POST" class="position-relative">
					<div class="row">
						@if(isset($dataType->id))
				            <div class="col-md-12 relationship_details">
				                <p class="relationship_table_select">{{ \Illuminate\Support\Str::singular(ucfirst($table)) }}</p>
				                <select class="relationship_type select2" name="relationship_type">
				                    <option value="hasOne" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasOne') selected="selected" @endif>{{ __('voyager::database.relationship.has_one') }}</option>
				                    <option value="hasMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasMany') selected="selected" @endif>{{ __('voyager::database.relationship.has_many') }}</option>
				                    <option value="belongsTo" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsTo') selected="selected" @endif>{{ __('voyager::database.relationship.belongs_to') }}</option>
				                    <option value="belongsToMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsToMany') selected="selected" @endif>{{ __('voyager::database.relationship.belongs_to_many') }}</option>
				                </select>
				                <select class="relationship_table select2" name="relationship_table">
				                    @foreach($tables as $tbl)
				                        <option value="{{ $tbl }}" @if(isset($relationshipDetails->table) && $relationshipDetails->table == $tbl) selected="selected" @endif>{{ \Illuminate\Support\Str::singular(ucfirst($tbl)) }}</option>
				                    @endforeach
				                </select>
				                <span><input type="text" class="form-control" name="relationship_model" placeholder="{{ __('voyager::database.relationship.namespace') }}" value="{{ $relationshipDetails->model ?? ''}}"></span>
				            </div>
				            <div class="col-md-12 relationship_details relationshipField">
				            	<div class="belongsTo">
				            		<label>{{ __('voyager::database.relationship.which_column_from') }} <span>{{ \Illuminate\Support\Str::singular(ucfirst($table)) }}</span> {{ __('voyager::database.relationship.is_used_to_reference') }} <span class="label_table_name"></span>?</label>
				            		<select name="relationship_column_belongs_to" class="new_relationship_field select2">
				                    	@foreach($fieldOptions as $data)
				                        	<option value="{{ $data['field'] }}">{{ $data['field'] }}</option>
				                    	@endforeach
				               		</select>
				               	</div>
				               	<div class="hasOneMany flexed">
				            		<label>{{ __('voyager::database.relationship.which_column_from') }} <span class="label_table_name"></span> {{ __('voyager::database.relationship.is_used_to_reference') }} <span>{{ \Illuminate\Support\Str::singular(ucfirst($table)) }}</span>?</label>
					                <select name="relationship_column" class="new_relationship_field select2 rowDrop" data-table="{{ $tables[0] }}" data-selected="">
					                </select>
					            </div>
				            </div>
				            <div class="col-md-12 relationship_details relationshipPivot">
				            	<label>{{ __('voyager::database.relationship.pivot_table') }}:</label>
				            	<select name="relationship_pivot" class="select2">
		                        	@foreach($tables as $tbl)
				                        <option value="{{ $tbl }}" @if(isset($relationshipDetails->table) && $relationshipDetails->table == $tbl) selected="selected" @endif>{{ \Illuminate\Support\Str::singular(ucfirst($tbl)) }}</option>
				                    @endforeach
		                        </select>
				            </div>
				            <div class="col-md-12 relationship_details_more">
				                <div class="well px-3">
				                    <label>{{ __('voyager::database.relationship.selection_details') }}</label>
				                    <p><strong>{{ __('voyager::database.relationship.display_the') }} <span class="label_table_name"></span>: </strong>
				                        <select name="relationship_label" class="rowDrop select2" data-table="{{ $tables[0] }}" data-selected="" style="width: 100%">
				                        </select>
				                    </p>
				                    <p class="relationship_key"><strong>{{ __('voyager::database.relationship.store_the') }} <span class="label_table_name"></span>: </strong>
				                        <select name="relationship_key" class="rowDrop select2" data-table="{{ $tables[0] }}" data-selected="" style="width: 100%">
				                        </select>
									</p>

									<p class="relationship_taggable"><strong>{{ __('voyager::database.relationship.allow_tagging') }}:</strong> <br>
										<input type="checkbox" name="relationship_taggable" class="toggleswitch" data-on="{{ __('voyager::generic.yes') }}" data-off="{{ __('voyager::generic.no') }}">
				                    </p>
				                </div>
							</div>
				        @else
				        	<div class="col-md-12">
				        		<p class="relationship-warn h5 mb-0 font-weight-light">
				        			You need to create the BREAD first. Then return back to this page 
				        			and you will be able to add relationships.
				        		</p>
				        	</div>
				        @endif
					</div>
					<input type="hidden" value="{{ $dataType->id ?? '' }}" name="data_type_id">
					@csrf
					<div class="bg-white border-0 py-4 text-right px-3">
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::database.relationship.cancel') }}</button>

							@if(isset($dataType->id))
								<button class="btn btn-pink btn-relationship">
									{{ __('voyager::database.relationship.add_new') }}
								</button>
							@endif

						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>