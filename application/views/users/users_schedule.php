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
<link rel="stylesheet" href="<?=base_url('assets/vendor')?>/fullcalendar/fullcalendar.min.css"/>
<link rel="stylesheet" href="<?=base_url('assets/css')?>/badge-new.css"/>

<style>

	#ext_filter {
		margin: 10px 0 10px 0!important;
		padding: 7px;
		background-color: aliceblue;
	}

	#find_date_range{
		border: 1px solid #ced4da;
		border-radius: 4px;
		line-height: 28px;
	}

	#js_main_table_filter{
		/*float: right;*/
		padding-bottom: 7px;
	}

	#js_main_table_length {
		margin-top: 20px;
		margin-bottom: -44px;
	}
	.cursor-pointer {
		cursor: pointer;
	}
	.i-button{
		font-size: 17px;
		padding: 3px;
		border: 1px solid #dee2e6;
		border-radius: .25rem;
	}
	.i-button:hover {
		border-color: #a9afb4;
	}
	.loading {
		background-color: #ffffff;
		background-image: url("/assets/img/loader.gif");
		background-size: 20px 20px;
		background-position: center right calc(.375em + .1875rem);
		background-repeat: no-repeat;
	}

	input::placeholder, textarea::placeholder {
		color: #999 !important;
	}

	select.form-control:not([size]):not([multiple]) {
		height: calc(1.5em + .5rem + 2px)!important;
	}

	.select2-container .select2-selection--single{
		height: auto;
	}

	.select2-container--default .select2-selection--single{
		border: 1px solid #ced4da;
		border-radius: 4px;
	}

	.fa.green {
		color: green;
	}

	.fa.red {
		color: red;
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
						<h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>Управление графиками</h2>
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="/index.php"><i class="icon-home"></i></a></li>
							<li class="breadcrumb-item">Сотрудники</li>
							<li class="breadcrumb-item active">Управление графиками</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row clearfix">
				<div class="col-lg-12 col-md-12">
					<div class="card planned_task">
						<div class="header">
							<h2>Управление графиками <i id="refresh_main_table" class="fa fa-sync-alt cursor-pointer ml-2" title="Обновить данные по графикам"></i></h2>
							<small class="w-100"></small>
						</div>
						<div class="body mb-3">
							<div class="table-responsive">
								<table class="table table-sm table-bordered table-striped table-hover dataTable w-100" id="js_main_table">
									<thead>
									<tr>
										<th style="width: 5%;" title="ID графика">ID</th>
										<th>Название</th>
										<th>Время начала/окончания рабочего дня</th>
										<th>Дата начала действия графика</th>
										<th title="Дата создания">Дата создания</th>
										<th title="Текущий статус графика">Статус</th>
										<th>Автор</th>
									</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Модальное окно для создания нового графика -->
	<div id="modal_create_schedule" class="modal fade">
		<div class="modal-dialog shadow-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Новый график</h5>
					<button type="button" class="close" data-dismiss="modal" title="Закрыть">x</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col">
							<label for="schedule_name">Наименование</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control" id="schedule_name" placeholder="Укажите название графика" title="Укажите название графика">
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<label for="schedule_desc">Описание</label>
							<div class="input-group input-group-sm">
								<textarea class="form-control form-control-sm" id="schedule_desc" rows="2" placeholder="Укажите описание графика" title="Укажите описание графика"></textarea>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<div class="input-group input-group-sm">
								<label for="schedule_type">Тип графика</label>
								<select id="schedule_type" class="form-control form-control-sm" data-title="Выберите тип графика"
										title="Выберите тип графика" style="width: 100%">
								</select>
							</div>
						</div>
					</div>
					<div id="schedule_type_desc" class="alert alert-primary" role="alert">Тип графика проставляется для дальнейшего расчета  продолжительности рабочей недели, количество рабочих часов, запланированные выходные дни.<br><br>
					Например:<br>- Пятидневная рабочая неделя с двумя выходными;<br>- 2 рабочих дня через 2 выходных дня;<br>- 3 рабочих дня через 3 выходных дня.</div>
					<div class="form-row">
							<div class="form-group col">
								<label for="date_start">Дата начала графика</label>
								<div class="input-group input-group-sm">
									<div class="input-group-prepend">
									<div class="input-group-text"><i class="far fa-calendar-alt text-secondary" aria-hidden="true"></i></div>
									</div>
									<input type="text" class="form-control form-control-sm datepicker pl-2" id="date_start" placeholder="Выберите" autocomplete="off"  title="Выберите дату начала графика">
								</div>
							</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<label for="time_start">Время начала рабочего дня</label>
							<div class="input-group input-group-sm">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="far fa-clock" aria-hidden="true"></i></div>
								</div>
								<input type="text" id="time_start" data-time-format="H:i" class="timepicker form-control form-control-sm" autocomplete="off" placeholder="Выберите время" title="Выберите время">
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<label for="time_end">Время окончания рабочего дня</label>
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fas fa-clock" aria-hidden="true"></i></div>
							</div>
							<input type="text" id="time_end" data-time-format="H:i"
								   class="timepicker form-control form-control-sm" autocomplete="off" placeholder="Выберите время" title="Выберите время">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row float-right">
					<div class="col">
						<button type="button" id="button_create_schedule" class="btn btn-sm btn-success"><i class="fa fa-plus-circle mr-2" aria-hidden="true"></i>Создать</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

	<!-- Модальное окно для назначения графика сотруднику -->
	<div id="modal_assign_schedule" class="modal fade">
		<div class="modal-dialog shadow-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Назначить графику сотруднику</h5>
					<button type="button" class="close" data-dismiss="modal" title="Закрыть">x</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col">
							<label for="user">Сотрудник</label>
							<div class="input-group input-group-sm">
								<select id="user" class="form-control form-control-sm" data-title="Выберите сотрудника"
										title="Выберите сотрудника" style="width: 100%">
								</select>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col">
								<table class="table table-hover table-sm w-100" id="users_details_table">
									<tbody id="users_details_tbody">
									<tr><td><i class="fa fa-hashtag fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Табельный номер</td><td id="user_id_for_modal" class="auto_clear"></td></tr>
									<tr><td><i class="fas fa-user fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>ФИО сотрудника</td><td id="user_name_for_modal" class="auto_clear"></td></tr>
									<tr><td><i class="fas fa-university fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Подразделение</td><td id="user_department_for_modal" class="auto_clear text-break"></td></tr>
									<tr><td><i class="fas fa-user-graduate fa-fw mr-2" aria-hidden="true"></i>Должность</td><td id="user_position_for_modal" class="auto_clear text-break" style="white-space: break-spaces;"></td></tr>
									<tr><td><i class="fas fa-address-card fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Контакты</td><td id="user_contact_for_modal" class="auto_clear text-break" style="white-space: break-spaces;"></td></tr>
									<tr><td><i class="fas fa-check fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>Статус</td><td id="user_status_for_modal" class="auto_clear text-break" style="white-space: break-spaces;"></td></tr>
									<tr><td><i class="fas fa-calendar fa-fw mr-2" style="width: 20px;" aria-hidden="true"></i>График</td><td id="user_schedule_for_modal" class="auto_clear text-break" style="white-space: break-spaces;"></td></tr>
									</tbody>
								</table>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<label for="schedule">График</label>
							<div class="input-group input-group-sm">
								<select id="schedule" class="form-control form-control-sm" data-title="Выберите график"
										title="Выберите график" style="width: 100%">
								</select>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col">
							<label for="date_start_user_schedule">Дата начала графика</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="far fa-calendar-alt text-secondary" aria-hidden="true"></i></div>
								</div>
								<input type="text" class="form-control form-control-sm datepicker pl-2" id="date_start_user_schedule" placeholder="Выберите" autocomplete="off" title="Выберите дату начала графика">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row float-right">
						<div class="col">
							<button type="button" id="button_save_user_schedule" class="btn btn-sm btn-success"><i
										class="fa fa-save mr-2" aria-hidden="true"></i>Сохранить
							</button>
						</div>
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
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/dataTables.buttons.min.js"></script>
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/buttons.bootstrap4.min.js"></script>
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/buttons.flash.min.js"></script>
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/jszip.min.js"></script>
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/vfs_fonts.js"></script>
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/buttons.colVis.min.js"></script>
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/buttons.html5.min.js"></script>
	<script src="<?=base_url('assets/vendor')?>/jquery-datatable/buttons/buttons.print.min.js"></script>
	<script src="<?=base_url('assets/js')?>/users/users_schedule.js"></script>
<?php include_once $dir_path.'/blocks/footer.php';?>
