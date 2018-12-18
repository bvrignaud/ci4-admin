<h1><?php echo lang('Auth.create_user_heading');?></h1>
<p><?php echo lang('Auth.create_user_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open('admin/users/create');?>

	<p>
		<?php echo form_label(lang('Auth.create_user_fname_label'), 'first_name');?> <br />
		<?php echo form_input($firstName);?>
	</p>

	<p>
		<?php echo form_label(lang('Auth.create_user_lname_label'), 'last_name');?> <br />
		<?php echo form_input($lastName);?>
	</p>

	<?php
	if ($identityColumn !== 'email')
	{
		echo '<p>';
		echo form_label(lang('Auth.create_user_identity_label'), 'identity');
		echo '<br />';
		echo form_error('identity');
		echo form_input($identity);
		echo '</p>';
	}
	?>

	<p>
		<?php echo form_label(lang('Auth.create_user_company_label'), 'company');?> <br />
		<?php echo form_input($company);?>
	</p>

	<p>
		<?php echo form_label(lang('Auth.create_user_email_label'), 'email');?> <br />
		<?php echo form_input($email);?>
	</p>

	<p>
		<?php echo form_label(lang('Auth.create_user_phone_label'), 'phone');?> <br />
		<?php echo form_input($phone);?>
	</p>

	<p>
		<?php echo form_label(lang('Auth.create_user_password_label'), 'password');?> <br />
		<?php echo form_input($password);?>
	</p>

	<p>
		<?php echo form_label(lang('Auth.create_user_password_confirm_label'), 'password_confirm');?> <br />
		<?php echo form_input($passwordConfirm);?>
	</p>


	<p><?php echo form_submit('submit', lang('Auth.create_user_submit_btn'));?></p>

<?php echo form_close();?>
