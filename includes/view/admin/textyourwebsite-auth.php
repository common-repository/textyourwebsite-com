<?php
/**
 * textyourwebsite Auth save page
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php if(count($result) > 0){ ?>
			<h1>Your TextYourWebsite.com Settings</h1>
			<?php } else { ?>
			<h1>TextYourWebsite.com Account Activation</h1>
			<?php } ?>
			
			<?php if(count($result) > 0){ ?>
			<div class="shortcode_info_sec <?php echo esc_html( "$shortcode_sec_class" ); ?>" id="shortcode_info_sec">
				<h3>Installation Complete! To change any settings, please login to your account at <a href="https://textyourwebsite.com/account" title="TextYourWebsite.com" target="_blank">TextYourWebsite.com</a><br/><br/>
				[textyourwebsite] is your shortcode if enabled below
				</h3>
			</div>
			<?php } ?>
			
			<div class="adminform_sec">
				
				<?php if(count($result) < 1){ ?>
				<h2>Update your top announcement banner with a text message!</h2>
				<ul>
					<li>1. Visit <a href="https://textyourwebsite.com/pricing" title="Subscribe @ TextYourWebsite.com" target="_blank">TextYourWebsite.com and choose your plan</a></li>
					<li>2. Once you subscribe and login, copy &amp; paste your <strong>Website Code</strong> below:</li>
				</ul>
				<?php } ?>
				<form method="post" action="" id="textyourwebsite_config_form">
					<input type="hidden" name="record_id" id="record_id" class="form-control" value="<?php echo esc_html( "$record_id" ); ?>">
					
					<?php if(count($result) > 0){ ?>
					<div class="form-group">
			        	<label class="form-lables">Use Shortcode (This will disable the default banner):</label>
			            <select name="shortcode" id="shortcode" class="form-control">
							<?php if($shortcode == 0) { ?>
							<option value="0">No</option>
							<option value="1">Yes</option>
							<?php } else { ?>
							<option value="1">Yes</option>
							<option value="0">No</option>
							<?php } ?>
						</select>
    				</div>
					<?php } else { ?>
					<input type="hidden" name="shortcode" id="shortcode" class="form-control" value="0">
					<?php } ?>
					
					<div class="form-group">
			        	<label class="form-lables">Website Code:</label>
			            <input type="text" name="client_api_id" id="client_api_id" class="form-control" value="<?php echo esc_html( "$client_api_id" ); ?>" required="required">
    				</div>

    				<div class="form-group">
						<?php if(count($result) > 0){ ?>
    					<button type="submit" class="btn btn-default admin_frm_btn" id="admin_frm_btn">Update</button>	
						<?php } else { ?>
						<button type="submit" class="btn btn-default admin_frm_btn" id="admin_frm_btn">Save</button>
						<?php } ?>
    				</div>
				</form>

			</div>
			
			<p>Please contact <a href="mailto:help@textyourwebsite.com" title="help@textyourwebsite.com">help@textyourwebsite.com</a> if you have any problems.</p>
			
		</div>
	</div> 
</div>
