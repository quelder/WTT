<?php
$dir_path = dirname(__DIR__);
?>
<?php include_once $dir_path.'/blocks/head.php';?>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/bootstrap-datepicker/css/bootstrap-datepicker.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/jquery-datatable/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/daterangepicker-master/daterangepicker.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/bootstrap-datepicker/css/jquery.timepicker.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/bootstrap-datetimepicker/bootstrap-datetimepicker.css"/>
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/select2/css/select2.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/css')?>/badge-new.css"/>

<style>

	.weekend {
		background-color: rgba(169,238,171,0.56) !important;
	}

	.holiday {
		background-color: rgba(225,164,181,0.56) !important;
	}

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

	.form-control-input {
		height: calc(1.5em + 0.5rem);
		padding: 0.25rem 0.5rem;
		font-size: .875rem;
		line-height: 1.5;
		border-radius: 0.2rem;
	}

	.input-group-text {
		padding: .3rem .75rem !important;

	}

	.select2-container .select2-selection--single{
		height: auto;
	}

	.select2-container--default .select2-selection--single{
		border: 1px solid #ced4da;
		border-radius: 4px;
	}
	input::placeholder, textarea::placeholder {
		color: #999 !important;
	}

	.big-table {
		overflow: auto;
		position: relative;
	}
	.big-table table {
		vertical-align: top;
		width: 100%;
		overflow-x: auto;
		white-space: nowrap;
		-webkit-overflow-scrolling: touch;
	}
	.scroll-right:after {
		content: '';
		display: block;
		width: 15px;
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		z-index: 500;
		background: radial-gradient(ellipse at right, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 75%) 100% center;
		background-repeat: no-repeat;
		background-attachment: scroll;
		background-size: 15px 100%;
		background-position: 100% 0%;
	}
	.scroll-left:before {
		content: '';
		display: block;
		width: 15px;
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		z-index: 500;
		background: radial-gradient(ellipse at left, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 75%) 0 center;
		background-repeat: no-repeat;
		background-attachment: scroll;
		background-size: 15px 100%;
	}


</style>


<?php include_once $dir_path.'/blocks/loader.php';?>
<div id="wrapper">
	<?php include_once $dir_path.'/blocks/navbar.php';?>
		<?php include_once $dir_path.'/blocks/sidebar.php';?>
	<div id="main-content">
		<div class="container-fluid">
			<div class="block-header">
				<div class="row">
					<div class="col-lg-6 col-md-8 col-sm-12">
						<h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Просмотр графиков</h2>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="/index.php"><i class="icon-home"></i></a></li>
							<li class="breadcrumb-item">Сотрудники</li>
							<li class="breadcrumb-item active">Просмотр графиков</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row clearfix">
				<div class="col-lg-12 col-md-12">
					<div class="card planned_task">
						<div class="d-flex">
						<div class="header mr-auto p-2">
							<h2>Просмотр графиков <i id="refresh_main_table" class="fa fa-sync-alt cursor-pointer ml-2" title="Обновить данные по графикам"></i></h2>
						</div>
						<div class="p-2">
							<div class="col col-auto">
								<i id="schedule_info" class="fa fa-lg fa-info-circle cursor-pointer text-success" title="Справка" aria-hidden="true"></i>
							</div>
							<?php include_once $dir_path.'/users/schedule_info.php';?>
						</div>
						</div>
						<div class="body mb-3">
							<div class="card text-dark bg-light mb-3">
								<div class="card-body">
									<div class="form-row">
										<div class="col-2">
											<label for="monthpicker">Месяц:</label>
											<div class="input-group ">
												<div class="input-group-prepend input-group-sm">
												<div class="input-group-text"><i class="far fa-calendar-alt text-secondary" aria-hidden="true"></i></div>
												</div>
												<input type="text" class="form-control form-control-input" name="monthpicker" id="monthpicker"/>
											</div>
										</div>
										<div class="col-3">
											<label for="find_department">Подразделение:</label>
											<div class="input-group input-group-sm">
												<select id="find_department" class="select2 form-control"></select>
											</div>
										</div>
										<div class="col-3">
											<label for="find_position">Должность:</label>
											<div class="input-group input-group-sm">
												<select id="find_position" class="select2 form-control"></select>
											</div>
										</div>
									</div>
								</div>
							</div
							<div class="table-responsive">
								<table class="table table-sm table-bordered table-striped table-hover dataTable" id="js_main_table">
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Модальное окно редактирования данных по дню -->
	<div id="modal_day_data_edit" class="modal fade">
		<div class="modal-dialog shadow-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Изменить данные</h5>
					<button type="button" class="close" data-dismiss="modal" title="Закрыть">x</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<div class="col">
							<table class="table table-hover table-sm w-100" id="users_details_table">
								<tbody id="users_details_tbody">
								<tr><td><i class="fa fa-hashtag fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Табельный номер</td><td id="user_id_for_modal" class="auto_clear"></td></tr>
								<tr><td><i class="fas fa-user fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>ФИО сотрудника</td><td id="user_name_for_modal" class="auto_clear"></td></tr>
								<tr><td><i class="fas fa-calendar-day fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Дата</td><td id="user_date_for_modal" class="auto_clear text-break"></td></tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<label for="day_type">Тип дня</label>
							<div class="input-group input-group-sm">
								<select id="day_type" class="show-tick ms select2 form-control"></select></div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<label for="time_start">Время начала рабочего дня</label>
							<div class="input-group timepicker">
								<div class="input-group-prepend">
								<div class="input-group-text"><i class="far fa-clock" aria-hidden="true"></i></div>
								</div>
								<input type="text" id="time_start" class="timepicker form-control form-control-sm timepicker_start" autocomplete="off" placeholder="Выберите время" title="Выберите время">
							</div>
						</div>
						<div class="form-group col">
							<label for="time_end">Время окончания</label>
							<div class="input-group timepicker">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="fas fa-clock" aria-hidden="true"></i></div>
								</div>
								<input type="text" id="time_end"  class="timepicker form-control form-control-sm timepicker_end" autocomplete="off" placeholder="Выберите время" title="Выберите время">
							</div>
						</div>
					</div>
					<div class="row float-right">
						<div class="col">
							<button  type="button" id="button_save_day_data_edit" class="btn btn-sm btn-success"><i class="far fa-save mr-2" aria-hidden="true"></i>Сохранить</button>
						</div>
					</div>
			</div>
		</div>
	</div>

</div>
<?php include_once $dir_path.'/blocks/prefooter.php';?>
<script src="<?=base_url('assets/vendor')?>/select2/js/select2.full.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/daterangepicker-master/moment.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/daterangepicker-master/daterangepicker.js"></script>
<script src="<?=base_url('assets/bundles')?>/datatablescripts.bundle.js"></script>
<script src="<?=base_url('assets/vendor')?>/bootstrap-datepicker/js/jquery.timepicker.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="<?=base_url('assets/vendor')?>/bootstrap-datepicker/locales/bootstrap-datepicker.ru.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/jquery-datatable/dataTables.scroller.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/jquery-datatable/scroller.bootstrap4.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/jquery-datatable/scroller.dataTables.min.js"></script>
<script src="<?=base_url('assets/vendor')?>/jquery-datatable/dataTables.select.js"></script>
<script src="<?=base_url('assets/js')?>/users/users_schedule_view.js"></script>
<?php include_once $dir_path.'/blocks/footer.php';?>
