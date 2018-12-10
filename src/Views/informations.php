<?php
function displayModules(array $modules): string
{
	$html = '';
	foreach ($modules as $module) {
		$html .= '<li>';
		$html .= $module . ' : ';
		$class = in_array('mod_rewrite', apache_get_modules()) ? 'success' : 'error';
		$html .= '<i class="fa fa-circle text-' . $class . '" aria-hidden="true"></i>';
		$html .= '</li>';
	}
	return $html;
}
?>
<div class="card-columns">

	<div class="card">
		<div class="card-header">
		<h2><i class="fa fa-info-circle" aria-hidden="true"></i> CodeIgniter</h2>
		</div>
		<div class="card-body">
		<p class="card-text">CodeIgniter <?=$ciVersion?> (<?=ENVIRONMENT?>)</p>
		</div>
	</div>

	<div class="card">
		<div class="card-header">
		<h2><i class="fa fa-info-circle" aria-hidden="true"></i> Configuration php</h2>
		</div>
		<div class="card-body">
		<p class="card-text">
			php : <?=phpversion()?> -> <?=PHP_VERSION_ID > 50600 ? 'OK' : 'NOK (php > 5.6 requis)'?>
		</p>
		<p class="card-text">Timezone : <?=date_default_timezone_get()?></p>
		<h3>Modules</h3>
		<ul>
			<?php
			if (function_exists('apache_get_modules')) {
				echo displayModules(['mod_rewrite', 'mod_env']);
			} else {
				echo 'apache_get_modules inconnue';
			}
			?>
		</ul>

		<h3>Extensions</h3>
		<p>intl actif : <?= extension_loaded('intl') ? 'OK' : 'NOK' ?> (préconisé)</p>
		<p>xdebug : <?= extension_loaded('xdebug') ? lang('Admin.yes') : lang('Admin.no') ?></p>
		<h3 class="card-title">phpinfo</h3>
		<?=anchor_popup('admin/informations/displayPhpInfo', lang('Admin.display_phpinfo'))?>
		</div>
	</div>

	<div class="card">
		<div class="card-header">
		<h2><i class="fa fa-info-circle" aria-hidden="true"></i> MySQL</h2>
		</div>
		<div class="card-body">
		<p class="card-text">Seveur MySQL : <?=$dbVersion?></p>
		<a href="<?=site_url('admin/informations/exportDatabase')?>" download class="btn btn-primary disabled" role="button">
			<?= lang('Admin.download_database') ?>
		</a>
		</div>
	</div>

	<div class="card">
		<div class="card-header"><h2><?= lang('Admin.email_params') ?></h2></div>
		<div class="card-body">
			<?=form_open('admin/informations/sendEmailForTest', ['id' => 'formSendMail'])?>
				<div class="form-group">
					<label>E-mail</label>
					<input name="email" id="email" class="form-control" value="" required type="email">
				</div>

				<div class="form-group no-border">
					<input name="send" class="btn btn-primary" value="<?= lang('Admin.send_test_email') ?>"
						   type="submit">
				</div>
			<?=form_close()?>
			<div id="logEmailErrors" class="alert alert-danger" role="alert" style="display: none">
			</div>
		</div>
	</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function(event) {
/*
	$('#formSendMail').submit(function(evt) {
		evt.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: $(this).attr('method'),
			data: $(this).serialize(),
			dataType : "json",
		}).done(function (reponse) {
			console.log(reponse);
			if(reponse.success === true) {
				displaySuccessMessage(reponse.msg);
				$('#logEmailErrors').hide();
			} else {
				//displayErrorMessage(reponse.msg);
				alert(reponse.msg);
				$('#logEmailErrors').html(reponse.logs).show();
			}
		}).fail(function() {
			//modalError("Erreur durant la communication avec le serveur.");
			alert("Erreur durant la communication avec le serveur.");
		});
	});
	*/



	$('#formSendMail').submit(function(evt) {
		evt.preventDefault();
		console.log(evt);
		let formData = new FormData(document.getElementById('formSendMail'));
		//formData.append('email', email);
		let params = {
			method: 'POST',
			body: formData
		};
		return fetch('admin/informations/sendEmailForTest', params)
		.then(response => {
			console.log(response);
			if (!response.ok) {
				throw new Error(response.statusText)
			}
			return response.json()
		})
		.catch(error => {
			console.log(error);
			/*
			swal.showValidationMessage(
				`Erreur : ${error}`
			);
	*/
			alert(`Erreur : ${error}`);
		})
	});
});
</script>
