<?php if(!$filter_vue){ ?>
	<script>
		var alm_filters = '';
		var alm_filter_id = '';
	</script>
<?php } else { // Pass current filter via JS variable ?>
	<script>
		var alm_filters = <?php echo json_encode($filter_vue) ?>;
		var alm_filter_id = <?php echo '"'.$filter_id.'"'; ?>;
	</script>      		
<?php } ?>
		
<!-- Start app -->
<div class="ajax-load-more-inner-wrapper" id="app">
   
   <!-- MAIN COLUMN -->
   
	<div class="cnkt-main"> 
		<div class="alm-filters">   			  
		   <?php include(ALM_FILTERS_PATH .'admin/views/includes/navigation.php'); ?>
		   <div class="repeater-listing">
		   
			   <header class="alm-filter--intro">
	   		<?php if(!$editing){ ?>
	   			<h2><?php _e('Create New Filter', 'ajax-load-more-filters'); ?></h2>
	   			<p><?php _e('Build a new Ajax Load More filter by adjusting the options below', 'ajax-load-more-filters'); ?>.</p>	     
	   		<?php } else { ?>
	   			<a href="<?php echo ALM_FILTERS_BASE_URL; ?>&action=new" class="button">Create New</a>
	   		   <h2><?php _e('Edit Filter', 'ajax-load-more-filters'); ?>: <em v-cloak>{{ data[0].id }}</em></h2>
	   			<p><?php _e('Adjust the options below to edit this filter', 'ajax-load-more-filters'); ?> - <?php _e('any changes made to the filter must be saved before they take effect', 'ajax-load-more-filters'); ?>.</p>		   		
	   		<?php } ?>	
	   		</header>  
	   		
	   		<header class="alm-filter--header">
	      		<h3>
		      		<?php _e('Options', 'ajax-load-more-filters'); ?> 
		      		<a title="<?php _e('Set up the filter options by entering a filter ID and selecting the interaction style.', 'ajax-load-more-filters'); ?>" href="javascript:void(0)" class="fa fa-question-circle tooltip" tabindex="-1" tabindex="-1"></a>
		      	</h3>
		      	<i class="fa fa-cog"></i>
	   		</header>
	         
	   		<section class="alm-filter--options"> 
	      		                 
	            <div class="alm-filter--row" id="row-id" v-bind:class="{ done: data[0].id !=='' }">
		      		<label data-id="id">
	   	      		<div class="label">
		   	      		<?php _e('ID', 'ajax-load-more-filters'); ?>
		   	      		<a 
			   	      		title="<?php _e('The unique ID for this filter instance.', 'ajax-load-more-filters'); ?>"
			   	      		href="javascript:void(0)" 
				   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
					   	   ></a>
		   	      	</div>
		      			<div class="item"> 
			      			<input 
			      				type="text" 
			      				id="filter-id" 
			      				class="filter-element" 
			      				:value="data[0].id" 
			      				data-id="id" 
			      				v-on:keyup="optionsChange($event)"
			      				v-on:keypress="restrictIDChars($event)"
			      				style="text-transform: lowercase;"
			      				<?php if($editing){ echo ' readonly="readonly"'; }?>
			      				> 
		      			</div>
		      		</label>
	            </div>
	            
	            <div class="alm-filter--row" id="row-style" v-bind:class="{ done: data[0].style !=='' }">
		      		<label data-id="style">
		      			<div class="label">
	   	      			<?php _e('Style', 'ajax-load-more-filters'); ?>
	   	      			<a title="<?php _e('Select the user interaction functionality for this filter.', 'ajax-load-more-filters'); ?>" href="javascript:void(0)" class="fa fa-question-circle tooltip" tabindex="-1"></a>
	   	      		</div>
		      			<div class="item">
	   	      			<div class="select-wrapper">
		   	      			<select 
			   	      			id="filter-style" 
				   	      		class="alm-filter-select" 
					   	      	data-id="style" 
										v-on:change="optionsChange($event)"
									>
								   	   
		   		      			<option value=""<?php echo (!isset($filter['style'])) ? $selected : ''; ?>>-- <?php _e('Select Style', 'ajax-load-more-filters'); ?> --</option>
		   		      			<option value="change"<?php echo (isset($filter['style']) && $filter['style'] === 'change') ? $selected : ''; ?>><?php _e('Change', 'ajax-load-more-filters'); ?></option>
		   		      			<option value="button"<?php echo (isset($filter['style']) && $filter['style'] === 'button') ? $selected : ''; ?>><?php _e('Button', 'ajax-load-more-filters'); ?></option> 
		   	      			</select>
	   	      			</div>
		      			</div>
		      		</label>
	            </div>
	      		<div class="alm-filter--row not-required" id="row-button-text" data-id="button-text" v-if="data[0].style === 'button'">
		      		<label data-id="button_text">
		      			<div class="label">
	   	      			<?php _e('Button Label', 'ajax-load-more-filters'); ?>
	   	      			<a title="<?php _e('Enter a label for the filter submit button.', 'ajax-load-more-filters'); ?>" href="javascript:void(0)" class="fa fa-question-circle tooltip" tabindex="-1"></a>
	   	      		</div>
		      			<div class="item">
		      				<input type="text" id="filter-button-text" data-id="button_text" :value="data[0].button_text" v-on:change="optionsChange($event)" placeholder="<?php echo apply_filters( 'alm_filters_button_text', __('Submit', 'ajax-load-more-filters') ); ?>">
		      			</div>
		      		</label>
	      		</div>
	      		
	   		</section>
	   		
	   		<header class="alm-filter--header">
	   			<h3> 
	      			<?php _e('Filters', 'ajax-load-more-filters'); ?>
	      			<a title="<?php _e('Build a custom filter group by adding and removing filter blocks.', 'ajax-load-more-filters'); ?>" href="javascript:void(0)" class="fa fa-question-circle tooltip" tabindex="-1"></a>
	   			</h3>
	   			<div class="toggle-controls" v-show="filters.length > 1">
	      			<a v-on:click="collapseFilters($event)" href="javascript:void(0);" title="<?php _e('Collapse All Filter Groups', 'ajax-load-more-filters'); ?>">
		      			&ndash;<span class="offscreen"><?php _e('Collapse All Filters', 'ajax-load-more-filters'); ?></span>
		      		</a> 
	      			<a class="last" v-on:click="expandFilters($event)" href="javascript:void(0);" title="<?php _e('Expand All Filter Groups', 'ajax-load-more-filters'); ?>">
		      			&#43;<span class="offscreen"><?php _e('Expand All Filters', 'ajax-load-more-filters'); ?></span>
		      		</a>
	   			</div>
	   			<i class="fa fa-filter"></i>
	   		</header>	      	
	      	
	      	<section class="alm-filter--filters">	  
	         	<draggable v-model="filters" @end="onEnd" :options="{animation:250, handle:'.alm-drag'}"> 	      	
	   			   <filter-template v-for="(filter, index) in filters" :key="filter.uniqueid" :data-filter="index" :keys="keys" :field_types="field_types" :taxonomy_operators="taxonomy_operators" :meta_types="meta_types" :meta_operators="meta_operators" :filters="filters" :filter="filter" :index="index"></filter-template>
	         	</draggable>	      			
	      	</section>
	      	
	   		<section class="alm-filter--add">
	   			<button type="button" v-on:click="addFilter($event)" class="button add-filter"><?php _e('Add Filter', 'ajax-load-more-filters'); ?></button>	      
	   		</section>		
	   		
	   		<section class="alm-filter--actions">
	      		<button type="button" v-on:click="saveFilter($event)" data-baseurl="<?php echo ALM_FILTERS_BASE_URL; ?>" class="buttom button-large button-primary save-filter" :disabled="isEmpty()"><?php if(!$editing){ ?>{{ create_btn_text }}<?php } else { ?>{{ update_btn_text }}<?php } ?></button>
	      		<div class="saving-filter"></div>
	      		<?php if($editing){ ?>
	      			<a v-on:click="deleteFilter($event)" data-id="<?php echo str_replace('alm_filter_', '', $filter_id); ?>" href="javascript:void(0);" class="alm-filter-delete"><?php _e('Delete', 'ajax-load-more-filters'); ?></a>
	      		<?php } ?>		      		
	   		</section>   		
   		   
         	<?php 
         	if( isset($filter['date_created']) || isset($filter['date_created']) ){ 
         		echo '<section class="alm-filter--meta">';
         		if(isset($filter['date_created'])){
            		echo '<div class="col">';
            		   echo __('Published', 'ajax-load-more-filters') .': <span>'.date('Y/m/d h:i:s a', $filter['date_created']) .'</span>';
            		echo '</div>';
         		} 
         		if(isset($filter['date_created'])){
            		echo '<div class="col">';
            		   echo __('Last Modified', 'ajax-load-more-filters') .': <span>'.date('Y/m/d h:i:s a', $filter['date_modified']) .'</span>';
            		echo '</div>';
         		} 
         		echo '</section>';
         	}      		
            ?>
	   		
		   </div>

	   </div>   	   
	   
	</div>   		
	<!-- END MAIN COLUMN -->
	
	<!-- SIDEBAR -->
   <?php include(ALM_FILTERS_PATH .'admin/views/includes/sidebar.php'); ?>
   <!-- END SIDEBAR -->
   
   <div class="clear"></div>
   
   <?php if($editing){ 
		include(ALM_FILTERS_PATH .'admin/views/includes/output-php.php');   
	}
	?>

</div>
<!-- End #app -->  

<script type="text/x-template" id="filterTemplate">  
      		
	<div class="alm-filter--wrapper" tabindex="-1">
		<div class="alm-counter alm-drag" :title="'<?php _e('Filter block', 'ajax-load-more-filters'); ?> #' + (index+1)" v-on:dblclick="toggleFilterGroup($event)">
   		<div class="count">{{ index + 1 }}</div>
   		<input type="text" 
   		   class="filter-order"
            v-bind:value="index + 1" 		             
            :data-index="index"		             
            :data-oldindex="filter.order - 1" 
            data-id="order"
            readonly="readonly"
   		>
   		<div class="drag"><span></span><span></span><span></span></div>
		</div>	
		
		<!-- Key -->
		<div class="alm-filter--row" id="row-key" :class="{ done: filter.key !== '' }">
			<label>
   			<div class="label">
      			<?php _e('Key', 'ajax-load-more-filters'); ?>
      			<a 
   	      		title="<?php _e('Select a query parameter (required).', 'ajax-load-more-filters'); ?>"
   	      		href="javascript:void(0)" 
	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
		   	   ></a>
            </div>
   			<div class="item">
      			<div class="select-wrapper">
	      			
	      			<select id="filter-key" 
   	      			class="alm-filter-select" 
	   	      		:data-index="index" 
		   	      	data-id="key" 
			   	      v-on:change="filterChange($event)"
				   	>									   	
		      			<option value="">-- <?php _e('Select Query Parameter', 'ajax-load-more-filters'); ?> --</option>
		      			<option v-for="key in keys" :value="key.value" :selected="key.value === filter.key ? 'selected' : ''" :disabled="key.value === '#'">{{ key.text }}</option>
	      			</select>
      			</div>
   			</div>
   		</label>
		</div>	 
		
		<div class="collapsible-controls">
			<button class="open" v-on:click="toggleFilterGroup($event)" title="<?php _e('Toggle filter parameters', 'ajax-load-more-filters'); ?>">
				<span><?php _e('Toggle View', 'ajax-load-more-filters'); ?></span>				
				<i class="control minus">&ndash;</i>
				<i class="control plus">&#43;</i>
			</button>
		</div>
		
		<div class="collapsible">
			
			<!-- Related Filters -->
			<div class="related-filters" v-bind:class="filter.key">     				
				
				<?php include(ALM_FILTERS_PATH .'admin/views/includes/instructions.php'); ?>			
			
				<?php
				// Taxonomies
				$tax_args = array(
					'public'   => true,
					'_builtin' => false
				);
				$tax_output = 'objects';
				$taxonomies = get_taxonomies( $tax_args, $tax_output );
				
				if ( $taxonomies ) { ?>
				<div class="related-filters--wrap" v-show="filter.key === 'taxonomy'" id="taxonomies">
	   			
	   			<div class="alm-filter--row" id="row-taxonomy" v-bind:class="{ done: filter.taxonomy !== '' }">
	      			<label>
		      			<div class="label"><?php _e('Taxonomy', 'ajax-load-more-filters'); ?></div>
		      			<div class="item">
	   	      			<div class="select-wrapper">				   	      			
									<select 
				   	      			id="filter-taxonomy" 
					   	      		class="alm-filter-select" 
						   	      	:data-index="index" 
							   	      data-id="taxonomy" 
								   	   v-on:change="filterChange($event)"
									   >
										<option value="" :selected="filter.taxonomy === '' ? 'selected' : ''">-- <?php _e('Select Taxonomy', 'ajax-load-more-filters'); ?>--</option>
										<?php foreach( $taxonomies as $taxonomy ){ ?>
											<option value="<?php echo $taxonomy->query_var;?>" :selected="filter.taxonomy === '<?php echo $taxonomy->query_var;?>' ? 'selected' : ''"><?php echo $taxonomy->label; ?></option>
										<?php } ?>
									</select>
	   	      			</div>
		      			</div>
		      		</label>
	   			</div>
	   			<div class="alm-filter--row done" id="row-taxonomy-operator">
	      			<label>
		      			<div class="label"><?php _e('Operator', 'ajax-load-more-filters'); ?></div>
		      			<div class="item">
	   	      			<div class="select-wrapper">				   	      			
									<select 
				   	      			id="filter-taxonomy-operator" 
					   	      		class="alm-filter-select" 
						   	      	:data-index="index" 
							   	      data-id="taxonomy_operator" 
								   	   v-on:change="filterChange($event)"
									   >
										   <option v-for="tax_operator in taxonomy_operators" :value="tax_operator.value" :selected="isActive(tax_operator.value, filter.taxonomy_operator, 'sss')">{{ tax_operator.text }}</option>
									</select>
	   	      			</div>
		      			</div>
		      		</label>
	   			</div>
	   			
				</div>
				<?php } ?>
				
				<div class="related-filters--wrap" v-show="filter.key === 'meta'" id="meta">
					
					<div class="alm-filter--row" id="row-meta-key" v-bind:class="{ done: filter.meta_key !== '' }">
	      			<label>
		      			<div class="label"><?php _e('Meta Key', 'ajax-load-more-filters'); ?></div>
		      			<div class="item">
	   	      			<input type="text" id="filter-meta-key" placeholder="<?php _e('Enter custom field name', 'ajax-load-more-filters'); ?>" data-id="meta_key" :data-index="index" :value="filter.meta_key" v-on:change="filterChange($event)">
		      			</div>
		      		</label>
	   			</div>
	   			
					<div class="alm-filter--row" id="row-meta-operator" v-bind:class="{ done: filter.meta_operator !== '' }">
	      			<label>
		      			<div class="label"><?php _e('Compare', 'ajax-load-more-filters'); ?></div>
		      			<div class="item">
	   	      			<div class="select-wrapper">
									<select 
				   	      			id="filter-meta-operator" 
					   	      		class="alm-filter-select" 
						   	      	:data-index="index" 
							   	      data-id="meta_operator" 
								   	   v-on:change="filterChange($event)"
									   >
										<option value="">-- <?php _e('Select a Meta Operator', 'ajax-load-more-filters'); ?> --</option>
										<option v-for="meta_operator in meta_operators" :value="meta_operator.value" :selected="isActive(meta_operator.value, filter.meta_operator, 'IN')">{{ meta_operator.text }}</option>
									</select>
	   	      			</div>
		      			</div>
		      		</label>
	   			</div>
	   			
					<div class="alm-filter--row" id="row-meta-type" v-bind:class="{ done: filter.meta_type !== '' }">
	      			<label>
		      			<div class="label"><?php _e('Type', 'ajax-load-more-filters'); ?></div>
		      			<div class="item">
	   	      			<div class="select-wrapper">
									<select 
				   	      			id="filter-meta-type" 
					   	      		class="alm-filter-select" 
						   	      	:data-index="index" 
							   	      data-id="meta_type" 
								   	   v-on:change="filterChange($event)"
									   >
										<option value="">-- <?php _e('Select a Meta Type', 'ajax-load-more-filters'); ?> --</option>
										<option v-for="meta_type in meta_types" :value="meta_type.value" :selected="isActive(meta_type.value, filter.meta_type, 'CHAR')">{{ meta_type.text }}</option>
									</select>
	   	      			</div>
		      			</div>
		      		</label>
	   			</div>
				</div>
				<!-- End Taxonomies -->
				
				<!-- Role -->
				<div class="related-filters--wrap" v-show="filter.key === 'author'" id="role">	      			
					<div class="alm-filter--row not-required" id="row-author-role">
	      			<label>
		      			<div class="label"><?php _e('Role', 'ajax-load-more-filters'); ?></div>
		      			<div class="item">
	   	      			<div class="select-wrapper">
		   	      			<?php 
			   	      		global $wp_roles;
			   	      		if($wp_roles){ 
				   	      		$roles = $wp_roles->get_names();
				   	      		$author_role = '';
				   	      		$decode_f = json_decode($filter_vue);
				   	      		if( isset($decode_f->filters) && isset($decode_f->filters[0]->author_role) ){		   	      		
					   	      		$author_role = $decode_f->filters[0]->author_role;
					   	      	}					   	      		
			   	      		?>
		   	      			<select 
			   	      			id="filter-author-role" 
				   	      		class="alm-filter-select" 
					   	      	:data-index="index" 
						   	      data-id="author_role" 
							   	   v-on:change="filterChange($event)"
								   >
									   <option value="">-- <?php _e('Select an Author Role', 'ajax-load-more-filters'); ?> --</option>
									<?php				   	      			
		   	      			foreach($roles as $role){?>
		   	      				<option value="<?php echo $role; ?>"<?php if($author_role === $role){ echo ' selected="selected"';} ?>><?php echo $role; ?></option>  	      			
		   	      			<?php } ?>
		   	      			</select>
		   	      			<?php } ?>
	   	      			</div>
		      			</div>
		      		</label>
	   			</div>
				</div>
				<!-- End Role -->
				
				<!-- Exclude -->
				<div class="related-filters--wrap shift" v-show="checkExclude(filter.key, index)">
					<div class="alm-filter--row not-required" id="row-exclude">
	      			<label>
		      			<div class="label">
	   	      			<?php _e('Exclude', 'ajax-load-more-filters'); ?>
	   	      			<a 
	         	      		title="<?php _e('Comma separated list of IDs to exclude from the filter.', 'ajax-load-more-filters'); ?>"
	         	      		href="javascript:void(0)" 
	      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
	      		   	   ></a>
	   	      		</div>
		      			<div class="item">
	   	      			<input type="text" placeholder="99, 199, 213" id="filter-exclude" data-id="exclude" :data-index="index" :value="filter.exclude" v-on:keyup="filterChange($event)">
		      			</div>
		      		</label>
	   			</div>
				</div>	
				<!-- End Exclude -->				
				
			</div>
			<!-- End Related Filters -->
			
			<!-- Field Type -->
			<div class="alm-filter--row" id="row-field-type" v-bind:class="{ done: filter.field_type !== '' }">
				<label>
	   			<div class="label">
		   			<?php _e('Field Type', 'ajax-load-more-filters'); ?>
		   			<a 
	   	      		title="<?php _e('Select a form element style for this filter (required).', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
					</div>
	   			<div class="item">
	      			<div class="select-wrapper">
		      			<select 
	   	      			id="filter-field-type" 
		   	      		class="alm-filter-select" 
			   	      	:data-index="index" 
				   	      data-id="field_type" 
					   	   v-on:change="filterChange($event)"
						   >
			      			<option value="">-- <?php _e('Select Field Type', 'ajax-load-more-filters'); ?> --</option>
			      			<option v-for="field_type in field_types" :value="field_type.value" :selected="field_type.value === filter.field_type ? 'selected' : ''" :disabled="field_type.value === '#'">{{ field_type.text }}</option>
		      			</select>
	      			</div>
	   			</div>
	   		</label>
			</div>
			
			<!-- Toggle All -->
			<div class="related-filters--wrap" v-show="filter.field_type === 'checkbox'" id="select-all">
				
				<div class="alm-filters-inline-desc no-margin">
				   <h3><?php _e('Toggle Checkbox', 'ajax-load-more-filters'); ?></h3>				   
				   <p><?php _e('Add a <strong><em>Select All</em></strong> checkbox option that allows users to <strong>select</strong>/<strong>unselect</strong> all filters with a single click.', 'ajax-load-more-filters'); ?></p>
				</div>
   			
				<!-- Toggle Location -->
				<div class="alm-filter--row not-required" id="row-checkbox_toggle">
      			<label>
	      			<div class="label">
   	      			<?php _e('Position', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Select the placement of the toggle checkbox.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">	  
		      			<div class="select-wrapper">    			
	   	      			<select 
		   	      			id="filter-checkbox_toggle" 
			   	      		class="alm-filter-select" 
					   	      data-id="checkbox_toggle" 
				   	      	:data-index="index" 
						   	   v-on:change="filterChange($event)"
							   >
			      				<option value="" :selected="filter.checkbox_toggle === '' ? 'selected' : ''"><?php _e('None (Do not display)', 'ajax-load-more-filters'); ?></option>
				      			<option value="before" :selected="filter.checkbox_toggle === 'before' ? 'selected' : ''"><?php _e('Before (Displayed before filters)', 'ajax-load-more-filters'); ?></option>	
				      			<option value="after" :selected="filter.checkbox_toggle === 'after' ? 'selected' : ''"><?php _e('After (Displayed after filters)', 'ajax-load-more-filters'); ?></option>				      			
			      			</select>
		      			</div>
	      			</div>
	      		</label>
   			</div>
				<!-- End Toggle Location -->
				<!-- Toggle Text -->
				<div class="alm-filter--row not-required" id="row-checkbox_toggle_label">
      			<label>
	      			<div class="label">
   	      			<?php _e('Label', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('The text to be rendered as the toggle checkbox label.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="text" id="filter-checkbox_toggle_label" data-id="checkbox_toggle_label" :data-index="index" :value="filter.checkbox_toggle_label" v-on:keyup="filterChange($event)" placeholder="<?php echo apply_filters('alm_filters_toggle_label', __('Select All', 'ajax-load-more-filters')); ?>">
	      			</div>
	      		</label>
   			</div>
				<!-- End Toggle Text -->
				
			</div>
			<!-- End Toggle All -->
			
			<!-- Datepicker -->
			<div class="related-filters--wrap" v-show="filter.field_type === 'date_picker'" id="datepicker">
				<div class="alm-filter--row_instructions">
   				<div class="alm-instructions alm-instructions--intro">
   				   <p><?php _e('The <strong>Date Picker</strong> field type uses the <a href="https://flatpickr.js.org/" target="_blank">Flatpickr JS</a> library to display a calendar select input field.', 'ajax-load-more-filters'); ?></p>
   				</div>
				</div>
				
				<div class="alm-filters-inline-desc">
				   <h3><?php _e('Date Picker Configurations', 'ajax-load-more-filters'); ?></h3>				   
				   <p><a href="https://flatpickr.js.org/formatting/" target="_blank"><?php _e('Date Formatting Options', 'ajax-load-more-filters'); ?></a> | <a href="https://github.com/flatpickr/flatpickr/tree/master/src/l10n" target="_blank"><?php _e('Available Locales', 'ajax-load-more-filters'); ?></a><a href="javascript:void(0)" class="fa fa-question-circle tooltip" title="<?php _e('Select the 2 digit locale from the list', 'ajax-load-more-filters'); ?>"></a></p>
				</div>
				
				<!-- Mode -->
				<div class="alm-filter--row not-required" id="row-datemode">
      			<label>
	      			<div class="label">
   	      			<?php _e('Mode', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Select the type of date picker. Choose between single, multiple or a date range.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
		      			<div class="select-wrapper">
   		      			<select 
	   	      			id="filter-datepicker-mode" 
		   	      		class="alm-filter-select" 
				   	      data-id="datepicker_mode" 
			   	      	:data-index="index" 
					   	   v-on:change="filterChange($event)"
						   >
			      			<option value="">-- <?php _e('Select Mode', 'ajax-load-more-filters'); ?> --</option>
			      			<option value="single" :selected="filter.datepicker_mode === 'single' ? 'selected' : ''"><?php _e('Single Date', 'ajax-load-more-filters'); ?></option>	
			      			<option value="multiple" :selected="filter.datepicker_mode === 'multiple' ? 'selected' : ''"><?php _e('Multiple Dates', 'ajax-load-more-filters'); ?></option>				      			
			      			<option value="range" :selected="filter.datepicker_mode === 'range' ? 'selected' : ''"><?php _e('Range', 'ajax-load-more-filters'); ?></option>			      			
		      			</select>
		      			</div>
	      			</div>
	      		</label>
   			</div>
				<!-- Date Display Format -->
				<div class="alm-filter--row not-required" id="row-dateformat">
      			<label>
	      			<div class="label">
   	      			<?php _e('Date Format', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('The date format displayed to the user when a date is selected. Default = Y-m-d.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="text" placeholder="Y-m-d" id="filter-datepicker-format" data-id="datepicker_format" :data-index="index" :value="filter.datepicker_format" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
				<!-- End Date Display Format -->
				
				<!-- Locale -->
				<div class="alm-filter--row not-required" id="row-locale">
      			<label>
	      			<div class="label">
   	      			<?php _e('Localization', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Choose a locale (language) for the date picker. Default = en', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="text" placeholder="en" id="filter-datepicker-locale" data-id="datepicker_locale" :data-index="index" :value="filter.datepicker_locale" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
				<!-- End Locale -->
			</div>
			<!-- End Datepicker -->	
			
			<!-- Range Slider -->
			<div class="related-filters--wrap" v-show="filter.field_type === 'range_slider'" id="rangeslider">
				<div class="alm-filter--row_instructions">
   				<div class="alm-instructions alm-instructions--intro">
   				   <p><?php _e('The <strong>Range Slider</strong> field type uses the <a href="https://refreshless.com/nouislider/" target="_blank">noUiSlider</a> library to display a draggable range selector.', 'ajax-load-more-filters'); ?></p>
   				   <p><?php _e('Range Sliders are most commonly used when querying for custom field values such as product price (WooCommerce) or width/height measurements.', 'ajax-load-more-filters'); ?></p>
   				</div>
				</div>
				
				<div class="alm-filters-inline-desc">
				   <h3><?php _e('Range Slider Config', 'ajax-load-more-filters'); ?></h3>				   
				   <p><?php _e('Set the min/max value and other noUiSlider configuration options.', 'ajax-load-more-filters'); ?></p>
				</div>
				
				<!-- Range Min -->
				<div class="alm-filter--row not-required" id="row-rangeslider-start">
      			<label>
	      			<div class="label">
   	      			<?php _e('Min Value', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Set the minimum value of the slider.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="number" placeholder="0" id="filter-rangeslider-min" data-id="rangeslider_min" :data-index="index" :value="filter.rangeslider_min" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
   			<!-- Range Max -->
				<div class="alm-filter--row not-required" id="row-rangeslider-end">
      			<label>
	      			<div class="label">
   	      			<?php _e('Max Value', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Set the maximum value of the slider.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="number" placeholder="100" id="filter-rangeslider-max" data-id="rangeslider_max" :data-index="index" :value="filter.rangeslider_max" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
   			<!-- Range Steps -->
				<div class="alm-filter--row not-required" id="row-rangeslider-end">
      			<label>
	      			<div class="label">
   	      			<?php _e('Steps', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Make the slide handles jump between intervals on drag - default is 1.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="number" placeholder="1" id="filter-rangeslider-steps" data-id="rangeslider_steps" :data-index="index" :value="filter.rangeslider_steps" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
   			<hr/>
   			<!-- Range Label -->
				<div class="alm-filter--row not-required" id="row-rangeslider-label">
      			<label>
	      			<div class="label">
   	      			<?php _e('Display Label', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('The label renders the current start & end values of the slider to the user. The {start} template parameter displays the low value, and {end} displays the high value.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="text" placeholder="${start} - ${end}" id="filter-rangeslider-label" data-id="rangeslider_label" :data-index="index" :value="filter.rangeslider_label" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
   			<!-- Range Orientation -->
				<div class="alm-filter--row not-required" id="row-rangeslider-orientation">
      			<label>
	      			<div class="label">
   	      			<?php _e('Orientation', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Vertical sliders default to 200px in height. You can adjust this using custom CSS.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<div class="select-wrapper">
	   	      			<select 
		   	      			id="filter-rangeslider-orientation" 
			   	      		class="alm-filter-select" 
					   	      data-id="rangeslider_orientation" 
				   	      	:data-index="index" 
						   	   v-on:change="filterChange($event)"
									>						 	   
	   		      			<option value="horizontal" :selected="isActive('horizontal', filter.rangeslider_orientation, 'horizontal')"><?php _e('Horizontal', 'ajax-load-more-filters'); ?> &nbsp; ↔</option>
	   		      			<option value="vertical" :selected="isActive('vertical', filter.rangeslider_orientation, 'horizontal')"><?php _e('Vertical', 'ajax-load-more-filters'); ?> &nbsp; ↕</option>	
	   	      			</select>
   	      			</div>
	      			</div>
	      		</label>
   			</div>
   			<!-- Range Decimal -->
				<div class="alm-filter--row not-required" id="row-rangeslider-orientation">
      			<label>
	      			<div class="label">
   	      			<?php _e('Show Decimals', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('Render decimals in the range slider display.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<div class="select-wrapper">
	   	      			<select 
		   	      			id="filter-rangeslider-decimals" 
			   	      		class="alm-filter-select" 
					   	      data-id="rangeslider_decimals" 
				   	      	:data-index="index" 
						   	   v-on:change="filterChange($event)"
									>						 	   
	   		      			<option value="true" :selected="isActive('true', filter.rangeslider_decimals, 'true')"><?php _e('True', 'ajax-load-more-filters'); ?></option>
	   		      			<option value="false" :selected="isActive('false', filter.rangeslider_decimals, 'false')"><?php _e('False', 'ajax-load-more-filters'); ?></option>	
	   	      			</select>
   	      			</div>
	      			</div>
	      		</label>
   			</div>
   			<div class="alm-filters-inline-desc">
				   <h3><?php _e('Range Slider Defaults', 'ajax-load-more-filters'); ?></h3>				   
				   <p><?php _e('Set the default start and end position of the drag handles.', 'ajax-load-more-filters'); ?></p>
				</div>
   			<!-- Range Start -->
				<div class="alm-filter--row not-required" id="row-rangeslider-start">
      			<label>
	      			<div class="label">
   	      			<?php _e('Start Value', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('If no value is set, handle will start at the Min value.', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="number" placeholder="10" id="filter-rangeslider-start" data-id="rangeslider_start" :data-index="index" :value="filter.rangeslider_start" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
   			<!-- Range End -->
				<div class="alm-filter--row not-required" id="row-rangeslider-end">
      			<label>
	      			<div class="label">
   	      			<?php _e('End Value', 'ajax-load-more-filters'); ?>
   	      			<a 
         	      		title="<?php _e('If no value is set, handle will start at the Max value', 'ajax-load-more-filters'); ?>"
         	      		href="javascript:void(0)" 
      	   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
      		   	   ></a>
   	      		</div>
	      			<div class="item">
   	      			<input type="number" placeholder="175" id="filter-rangeslider-end" data-id="rangeslider_end" :data-index="index" :value="filter.rangeslider_end" v-on:keyup="filterChange($event)">
	      			</div>
	      		</label>
   			</div>
			</div>
			<!-- End Range Slider -->			
			
			<!-- Custom Values -->
			<div class="alm-filter--row not-required" id="row-values" v-show="filter.key !== 'search' && filter.field_type !== 'text' && filter.field_type !== 'range_slider' && filter.field_type !== 'date_picker'">
				<div class="fake-label">
	   			<div class="label">
		   			<?php _e('Custom Values', 'ajax-load-more-filters'); ?>
		   			<a 
	   	      		title="<?php _e('Create customized filters by adding custom labels and values.', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
		   		</div>
	   			<div class="item">      			
	      			<div class="value-fields-wrap" :data-index="index" data-id="values" :data-key="filter.key">         			
		      			<div class="value-fields-wrap--field" v-for="(cv, index) in filter.values" :key="index">
	   	      			<input 
	   	      			   type="text" 
	   	      			   class="values-label"
	   	      			   placeholder="<?php _e('Label', 'ajax-load-more-filters'); ?>" 
	   	      			   v-on:change="customValueChange($event)" 
	   	      			   :value="cv.label"
	                     >
	                     
	   	      			<input 
	   	      			   type="text" 
	   	      			   class="values-value" 
	   	      			   placeholder="<?php _e('Value', 'ajax-load-more-filters'); ?>" 
	   	      			   v-on:change="customValueChange($event)" 
	   	      			   :value="cv.value"
	   	      			   v-show="filter.key !== 'sort'"
	                     > 
	                     
	   	      			<input 
	   	      			   type="text" 
	   	      			   class="values-sortvalue" 
	   	      			   placeholder="<?php _e('order:orderby', 'ajax-load-more-filters'); ?>" 
	   	      			   v-on:change="customValueChange($event)" 
	   	      			   :value="cv.value"
	   	      			   v-show="filter.key === 'sort'"
	                     > 
	                      
	                     <div class="custom-value-controls">   	                     
   	                     <button 
   	                        :class="{ disabled: index == filter.values.length-1}"
   	                        class="alm-cv-down"
   	            			   v-on:click="customValueMove($event)"
   	            			   data-direction="down"
   	            			   data-id="values"
   	            			   :data-index="index"
   	            			   title="<?php _e('Move Down', 'ajax-load-more-filters'); ?>"
   	                     ><i class="fa fa-angle-down"></i><span><?php _e('Move Down', 'ajax-load-more-filters'); ?></span></button>
   	                     <button 
   	                        :class="{ disabled: index == 0 }"
   	                        class="alm-cv-up"
   	            			   v-on:click="customValueMove($event)"
   	            			   data-direction="up"
   	            			   data-id="values"
   	            			   :data-index="index"
   	            			   title="<?php _e('Move Up', 'ajax-load-more-filters'); ?>"
   	                     ><i class="fa fa-angle-up"></i><span><?php _e('Move Up', 'ajax-load-more-filters'); ?></span></button>
   	                     	
   	                     <button 
   	                        v-show="filter.values.length > 0"
   	                        class="alm-remove-btn"
   	            			   v-on:click="removeCustomValue($event)"
   	            			   data-id="values"
   	            			   :data-index="index"
   	            			   title="<?php _e('Remove', 'ajax-load-more-filters'); ?>"
   	                     >&times;<span><?php _e('Remove', 'ajax-load-more-filters'); ?></span></button>	      
	                     </div>	
	                     	                     		
		      			</div>	      				      			
	      			</div>
	      			<div class="value-fields-wrap--controls">	
	         			<button 
	         			   class="button add-value" 
	         			   v-on:click="addCustomValue($event)"
	         			   data-id="values"
	         			   :data-index="index"
	         			><?php _e('Add Value', 'ajax-load-more-filters'); ?></button>                  	 
	      			</div>  	      			
	   			</div>
				</div>
			</div>
			
			
			<!-- Pre-Selected Value --> 
			<div class="alm-filter--row not-required" id="row-selected-value" v-show="filter.field_type === 'select' || filter.field_type === 'radio' || filter.field_type === 'checkbox'">
				<label>
					<div class="label">
		   			<?php _e('Preselected Value', 'ajax-load-more-filters'); ?>
		   			<a 
			      		title="<?php _e('Set a preselected state (slug) for this filter block. The value set here will be the element selected on initial page load.', 'ajax-load-more-filters'); ?>"
			      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
					</div>   			
					<div class="item">
		   			<input type="text" id="filter-selected-value" data-id="selected_value" :data-index="index" :value="filter.selected_value" v-on:change="filterChange($event)">
					</div>
				</label>
			</div>
			
			
			<!-- Default Value --> 
			<div class="alm-filter--row not-required" id="row-default-value" v-show="filter.field_type !== 'range_slider'">
				<label>
					<div class="label">
		   			<?php _e('Default Value', 'ajax-load-more-filters'); ?>
		   			<a 
			      		title="<?php _e('Set a default fallback value for this filter block. Default values allow you match parameters set in the core Ajax Load More shortcode.', 'ajax-load-more-filters'); ?>"
			      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
					</div>   			
					<div class="item">
		   			<input type="text" id="filter-default-value" data-id="default_value" :data-index="index" :value="filter.default_value" v-on:change="filterChange($event)">
					</div>
				</label>
			</div>
			
			
			<!-- Placeholder -->
			<div class="alm-filter--row not-required" id="row-placeholder" v-show="filter.field_type === 'text' || filter.field_type === 'date_picker'">
				<label>
	   			<div class="label">
	      			<?php _e('Placeholder Text', 'ajax-load-more-filters'); ?>      			
		      		<a 
	   	      		title="<?php _e('Placeholder text displayed on the <input> element.', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
	            </div>
	   			<div class="item">
	      			<input type="text" id="filter-placeholder" data-id="placeholder" :data-index="index" :value="filter.placeholder" v-on:change="filterChange($event)">
	   			</div>
	   		</label>
			</div>
			
			
			<!-- Label -->
			<div class="alm-filter--row not-required" id="row-label" v-show="checkLabel(filter.field_type, index)">
				<label>
	   			<div class="label" v-if="filter.field_type !== 'select'">
	      			<?php _e('Field Label', 'ajax-load-more-filters'); ?>
	      			<a 
	   	      		title="<?php _e('The label for the <input> element.', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
	            </div>
	   			<div class="label" v-else>
	      			<?php _e('Default Label', 'ajax-load-more-filters'); ?>
	      			<a 
	   	      		title="<?php _e('The default label of the <select> element.', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
	            </div>
	   			<div class="item" v-if="filter.field_type !== 'select'">
	      			<input type="text" id="filter-label" data-id="label" :data-index="index" :value="filter.label" v-on:change="filterChange($event)">
	   			</div>
	   			<div class="item" v-else>
	      			<input type="text" id="filter-label" placeholder="<?php _e('-- Select --', 'ajax-load-more-filters'); ?>" data-id="label" :data-index="index" :value="filter.label" v-on:change="filterChange($event)">
	   			</div>
	   		</label>
			</div>
			
			
			<!-- Button Label -->
			<div class="alm-filter--row not-required" id="row-button-label" v-show="filter.field_type === 'text' || filter.field_type === 'date_picker'">
				<label>
	   			<div class="label">
	      			<?php _e('Button Label', 'ajax-load-more-filters'); ?>      			
		      		<a 
	   	      		title="<?php _e('The text display value of the submit button', 'ajax-load-more-filters'); ?> - <?php _e('Button will only be rendered if a value is present.', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
	            </div>
	   			<div class="item">
	      			<input type="text" id="filter-button-label" data-id="button_label" :data-index="index" :value="filter.button_label" v-on:change="filterChange($event)" placeholder="<?php _e('Submit', 'ajax-load-more-filters'); ?>">
	   			</div>
	   		</label>
			</div>
			
			
			<!-- Title -->
			<div class="alm-filter--row not-required" id="row-title">
				<label>
	   			<div class="label">
	      			<?php _e('Title', 'ajax-load-more-filters'); ?>
		      		<a 
	   	      		title="<?php _e('Add an optional title for this filter block. Titles are displayed as <h3> elements above the form element.', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
	            </div>
	   			<div class="item">
	      			<input type="text" id="filter-title" data-id="title" :data-index="index" :value="filter.title" v-on:change="filterChange($event)">
	   			</div>
	   		</label>
			</div>
			
			
			<!-- Classes -->
			<div class="alm-filter--row not-required" id="row-classes">
				<label>
	   			<div class="label">
	      			<?php _e('Classes', 'ajax-load-more-filters'); ?>
		      		<a 
	   	      		title="<?php _e('Add custom classnames to the filter block.', 'ajax-load-more-filters'); ?>"
	   	      		href="javascript:void(0)" 
		   	      	class="fa fa-question-circle tooltip" tabindex="-1" 
			   	   ></a>
	            </div>
	   			<div class="item">
	      			<input type="text" id="filter-classes" data-id="classes" :data-index="index" :value="filter.classes" placeholder="row container" v-on:change="filterChange($event)">
	   			</div>
	   		</label>
			</div>
			
		</div>
		
		
		<div class="alm-filter--controls">
		   <button class="button alm-remove-filter" v-on:click="removeFilter($event)" :data-index="index" :class="{ disabled: filters.length == 1 }"><?php _e('Remove Filter', 'ajax-load-more-filters'); ?></button>
		</div>
		
	</div>
	
</script>