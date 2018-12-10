<p><?php echo lang('Auth.index_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<table class="table table-bordered table-hover dataTable">
	<tr>
		<th><?php echo lang('Auth.index_fname_th');?></th>
		<th><?php echo lang('Auth.index_lname_th');?></th>
		<th><?php echo lang('Auth.index_email_th');?></th>
		<th><?php echo lang('Auth.index_groups_th');?></th>
		<th><?php echo lang('Auth.index_status_th');?></th>
		<th><?php echo lang('Auth.index_action_th');?></th>
	</tr>
	<?php foreach ($users as $user):?>
		<tr>
			<td><?php echo esc($user->first_name);?></td>
			<td><?php echo esc($user->last_name);?></td>
			<td><?php echo esc($user->email);?></td>
			<td>
				<?php foreach ($user->groups as $group):?>
					<?php echo anchor('admin/users/edit_group/' . $group->id, esc($group->name)); ?><br>
				<?php endforeach?>
			</td>
			<td>
				<?php echo ($user->active) ? anchor('admin/users/deactivate/' . $user->id, lang('Auth.index_active_link')) : anchor('admin/users/activate/' . $user->id, lang('Auth.index_inactive_link'));?>
			</td>
			<td><?php echo anchor('admin/users/edit/' . $user->id, 'Edit') ;?></td>
		</tr>
	<?php endforeach;?>
</table>

<p>
	<?php echo anchor('admin/users/create', lang('Auth.index_create_user_link'))?>
	|
	<?php echo anchor('admin/users/create_group', lang('Auth.index_create_group_link'))?>
</p>
