<?php
$dir_path = dirname(__DIR__);
?>
<?php include_once $dir_path.'/blocks/head.php';?>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/bootstrap-datepicker/css/bootstrap-datepicker.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/jquery-datatable/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/daterangepicker-master/daterangepicker.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/bootstrap-datepicker/css/jquery.timepicker.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/bootstrap-datetimepicker/bootstrap-datetimepicker.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/fullcalendar/fullcalendar.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/select2/css/select2.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/css')?>/badge-new.css"/>

<style>
	.fc-event{cursor: pointer;}

	.event {
		border-radius: 5px 5px 5px 5px;
		font-size: 8pt;
		padding: 0px 2px 0px 2px;
	}

	.pop-up {
		border-radius: 3px 3px 3px 3px;
		font-size: 8pt;
		padding: 2px 2px 2px 2px;
	}
	input[type="radio"] {
		margin-right: 0.5rem;
	}

</style

<?php include_once $dir_path.'/blocks/loader.php';?>
<div id="wrapper">
	<?php include_once $dir_path.'/blocks/navbar.php';?>
	<?php include_once $dir_path.'/blocks/sidebar.php';?>
	<div id="main-content">
		<div class="container-fluid">
			<div class="block-header">
				<div class="row">
					<div class="col-lg-6 col-md-8 col-sm-12">
						<h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Производственный календарь</h2>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="/index.php"><i class="icon-home"></i></a></li>
							<li class="breadcrumb-item">Сотрудники</li>
							<li class="breadcrumb-item active">Производственный календарь</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 col-md-12">
					<div class="card" id="card_calendar">
						<div class="d-flex justify-content-end">
							<div class="col col-auto m-1">
								<i id="schedule_info" class="fa fa-lg fa-info-circle cursor-pointer text-success"
								   title="Справка" aria-hidden="true"></i>
							</div>
						</div>
						<div class="body pb-2">
							<div class="calendar" id="calendar"></div>
							<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">Изменить данные</h5>
											<button type="button" class="close" data-dismiss="modal" title="Закрыть">x</button>
										</div>
										<div class="modal-body">
											<div class="form-group row">
												<div class="col">
													<table class="table table-hover table-sm w-100" id="days_details_table">
														<tbody id="days_details_table">
														<tr><td><i class="fas fa-calendar-day fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Дата</td><td id="date_for_modal" class="auto_clear"></td></tr>
														<tr class="d-none" id="row_name"><td><i class="far fa-file-alt fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Наименование</td><td id="name_for_modal" class="auto_clear"></td></tr>
														<tr class="d-none" id="row_comment"><td><i class="fa fa-comment fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Комментарий</td><td id="comment_for_modal" class="auto_clear text-break"></td></tr>
														<tr><td><i class="fa fa-hashtag fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Тип</td><td id="type_for_modal" class="auto_clear text-break"></td></tr>
														</tbody>
													</table>
												</div>
											</div>
											<div id="container" class="auto_clear"></div>
											<div id="container_second" class="auto_clear"></div>
											<div class="form-row">
												<div class="form-group col">
													<label for="comment">Комментарий</label>
													<div class="input-group input-group-sm">
														<input type="text" class="form-control" id="comment" placeholder="Укажите комментарий для дня" title="Укажите комментарий для дня">
													</div>
												</div>
											</div>
											<div class="row float-right">
												<div class="col">
													<button  type="button" id="button_save_day_data" class="btn btn-sm btn-success"><i class="far fa-save mr-2" aria-hidden="true"></i>Сохранить</button>
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
		<?php include_once 'schedule_info.php';?>
</div>
<?php include_once $dir_path.'/blocks/prefooter.php';?>
<script src="<?=base_url('assets/vendor')?>/select2/js/select2.full.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/daterangepicker-master/moment.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/daterangepicker-master/daterangepicker.js"></script>
<script src="<?=base_url('assets/bundles')?>/datatablescripts.bundle.js"></script>
<script src="<?=base_url('assets/vendor')?>/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="<?=base_url('assets/vendor')?>/bootstrap-datepicker/locales/bootstrap-datepicker.ru.min.js"></script>
<script src="<?= base_url('assets/vendor') ?>/fullcalendar/fullcalendar.js"></script>
<script src="<?= base_url('assets/vendor') ?>/fullcalendar/moment.min.js"></script>
<script src="<?=base_url('assets/js')?>/users/production_calendar.js"></script>
<?php include_once $dir_path.'/blocks/footer.php';?>
