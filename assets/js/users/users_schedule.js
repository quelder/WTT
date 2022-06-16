$(function() {

	load_main_table();
	function load_main_table() {
		$('#js_main_table').DataTable({
			pageLength: 25,
			processing: true,
			serverSide: true,
			order: [],
			ajax: {
				url: "/api/getScheduleList",
				type: "POST",
				data: function (d) {
					//d.author = $('#find_author').val();
					d.active = $('#find_active').val();
					d.date_range = $('#find_date_range').val();
				}
			},
			rowId: 'id',
			aoColumns: [
				{mData: 'id'},
				{
					"mRender": function (data, type, full){
						let block = '';
						block += `<span title="${full.desc}">${full.name}</span>`;
						return block;
					}
				},
				{
					"mRender": function (data, type, full){
						let block = '';
						block += `<span>${full.time_start}</span> - <span>${full.time_end}</span>`;
						return block;
					}
				},
				{mData: 'schedule_date_start'},
				{
					"mRender": function (data, type, full){
						let block = '';
						let date_add = (full.date_add != null) ? full.date_add : '-';
						let date_mod = (full.date_mod != null) ? full.date_mod : '-';
						block += `<span title="Дата последнего изменения: ${date_mod}">${date_add}</span>`;
						return block;
					}
				},
				{
					"mRender": function (data, type, full) {
						let checked = full.active ? 'checked' : '';
						let block = '';
						if (full.active != null){
						block += `<div class="custom-control custom-switch"><input type="checkbox" id="one_schedule_${full.id}" class="custom-control-input change_status" ${checked}>`;
						block += `<label for="one_schedule_${full.id}" class="custom-control-label font-weight-normal"></label></div>`;
						}
						return block;
					}
				},
				{
					"mRender": function (data, type, full) {
						let block = '';
						if (full.author != null) {
							block += `<a href="/users/find/${full.user_id}">${full.author}</a>`;
						}
						return block;
					}
				}
			],
			columnDefs: [
				{
					"targets": [0, 1, 2, 3, 4, 5, 6],
					"className": "text-center"
				}
			],
			dom: 'Blfrtip',
			buttons: [
				{
					"extend": 'copyHtml5',
					'title': "Управление графиками сотрудников",
					"text": 'Copy',
					"className": 'btn btn-sm btn-light',
					'exportOptions': {columns: [0, 1, 2, 3, 4, 5, 6], orthogonal: 'export'}
				},
				{
					"extend": 'csvHtml5',
					'title': "Управление графиками сотрудников",
					"filename": 'users_schedule',
					"text": 'CSV',
					"className": 'btn btn-sm btn-light',
					'exportOptions': {columns: [0, 1, 2, 3, 4, 5, 6], orthogonal: 'export'}
				},
				{
					"extend": 'excelHtml5',
					'title': "Управление графиками сотрудников",
					"filename": 'users_schedule',
					"text": 'Excel',
					"className": 'btn btn-sm btn-light',
					'exportOptions': {columns: [0, 1, 2, 3, 4, 5, 6], orthogonal: 'export'}
				},
				{
					"extend": 'print',
					'title': "Управление графиками сотрудников",
					"text": 'Print',
					"className": 'btn btn-sm btn-light',
					'exportOptions': {columns: [0, 1, 2, 3, 4, 5, 6], orthogonal: 'export'}
				},
			],
			language: {
				url: `/assets/js/i18n/datatable/${$.i18n().locale}.json`
			},
			initComplete: function (settings, json) {
				$('#js_main_table_filter input').addClass('form-control-sm');
				$('#js_main_table_length select').addClass('form-control-sm').css('height', '31px');
				if(json.additional_data.user_roles.indexOf(57) !== -1) { // users_department_create
					$('.dt-buttons').prepend('<button type="button" id="add_new_schedule" class="btn btn-sm btn-light"><i class="fa fa-calendar-plus  mr-2" aria-hidden="true"></i>Создать график</button>');
				}
				if(json.additional_data.user_roles.indexOf(58) !== -1) {
					$('.dt-buttons').append('<button type="button" id="add_schedule_for_users" class="btn btn-sm btn-light"><i class="fa fa-calendar mr-2" aria-hidden="true"></i>Назначить график</button>');
				}
				// Инициализация расширенного фильтра
				init_ext_filter();
			}
		});
	}
	$('#refresh_main_table').on('click', function () {
		reload_main_table();
	});

	function reload_main_table(){
		$('#js_main_table').DataTable().ajax.reload();
	}

	/////////////////////////////// EXTRA FILTER ///////////////////////////////

	function init_ext_filter(){
		let ext_filter = '<div id="ext_filter" class="border">';
		ext_filter += '<div class="row ml-0 mr-0">';
		ext_filter += '<div class="col col-md-4"><label for="find_active">Статус:</label><select id="find_active" class="show-tick ms select2 form-control"><option></option></select></div>';
		ext_filter += '<div class="col col-md-4"><label for="find_date_range">Период создания:</label><input type="text" name="find_date_range" id="find_date_range" class="form-control form-control-sm" autocomplete="off" placeholder="Выберите период создания графика"></div>';
		ext_filter += '</div></div>';
		$('#js_main_table_filter input').after('<button id="ext_filter_toggle" type="button" class="btn btn-sm btn-light position-absolute" style="right: 20px;" title="Расширенный фильтр" ><i class="fa fa-caret-down" aria-hidden="true"></i></button>');
		$('#js_main_table_filter').after(ext_filter);
		init_date_range();
		initFindStatusSelect();
		$('#ext_filter').attr('hidden', true);
	}

	$('.body').on('click', '#ext_filter_toggle', function () {
		if($('#ext_filter').attr('hidden')){
			$('#ext_filter').attr('hidden', false);
		}
		else{
			$('#ext_filter').attr('hidden', true);
		}
	});

	$('.body').on('change', '#ext_filter select, #ext_filter input', function () {
		reload_main_table();
	});

	function init_date_range() {
		$( "#find_date_range" ).daterangepicker({
			startDate: false,
			minYear: 2021,
			locale: {
				"format": 'DD.MM.YYYY',
				"separator": " - ",
				"applyLabel":  $.i18n('main_ok'),
				"cancelLabel": $.i18n('main_cancel'),
				"fromLabel": "From",
				"toLabel": "To",
				"customRangeLabel": "Custom",
				"daysOfWeek": [
					$.i18n('days_su'),
					$.i18n('days_mo'),
					$.i18n('days_tu'),
					$.i18n('days_we'),
					$.i18n('days_th'),
					$.i18n('days_fr'),
					$.i18n('days_sa')
				],
				"monthNames": [
					$.i18n('month_january'),
					$.i18n('month_february'),
					$.i18n('month_march'),
					$.i18n('month_april'),
					$.i18n('month_may'),
					$.i18n('month_june'),
					$.i18n('month_july'),
					$.i18n('month_august'),
					$.i18n('month_september'),
					$.i18n('month_october'),
					$.i18n('month_november'),
					$.i18n('month_december')
				],
				"firstDay": 1
			}
		});
		$( "#find_date_range" ).val('');
	}


	function initFindStatusSelect() {
		let dataChange =  [
			{id: 0, text: 'Неактивный'},
			{id: 1, text: 'Активный'},
		];
		$('#find_active').select2({
			placeholder: $.i18n('main_select'),
			allowClear: true,
			data: dataChange
		});
	}

	$('#js_main_table').on('change', '.change_status', function () {
		var active = $(this).prop('checked');
		var schedule_id = $(this).parents('tr').attr('id');
		swal({
			title: "Вы уверены?",
			text: "что хотите изменить статус",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#dc3545",
			confirmButtonText: "Да, изменить!",
			cancelButtonText: "Нет",
			showLoaderOnConfirm: true,
			closeOnConfirm: false
		}, function (isConfirm) {
			if (isConfirm) {
				let request = JSON.stringify({'permission_id': schedule_id, 'active': active });
				$.post('/api/changeStatusSchedule', request, function (data) {

					if (data.status) {
						swal({
							title: "Успешно!",
							text: "Статус изменен.",
							timer: 1000,
							type: "success"
						});
					} else {
						swal({
							title: "Не удалось!",
							text: "Опс! Что-то пошло не так...",
							type: "error"
						});
					}
				});
				reload_main_table()
			}
		});
	});

	function button_disabled(elem){
		$(elem).attr("disabled", true);
		setTimeout(function () {
			$(elem).attr("disabled", false);
		}, 1000);
	}

	function show_tooltip(item) {
		$(item).tooltip('show');
		setTimeout(function () {
			$(item).tooltip('dispose');
		}, 3000);
	}

	let default_text;
	// Кнопка Создать График
	$('.body').on('click', '#add_new_schedule', function () {
		initScheduleTypeSelect()
		let modal =  $('#modal_create_schedule');
		modal.find('#date_start').datepicker({
			language: $.i18n().locale,
			format: 'yyyy-mm-dd',
			todayBtn: true,
			todayHighlight: true,
			widgetParent: true
		}).datepicker('setDate', 'today');
		modal.find('.timepicker').timepicker({
			timeFormat: 'H:i',
			defaultTime: false
		});
		 default_text = modal.find("#schedule_type_desc").html();
		modal.modal('toggle');
	});

	function initScheduleTypeSelect() {
		$('#modal_create_schedule').find('#schedule_type').select2({
			placeholder: 'Выберите тип графика',
			//allowClear: true,
			width: '100%',
			ajax: {
				url: '/api/getScheduleTypeForSelect',
				dataType: 'json',
				type: 'POST',
				data: function (params) {
					return JSON.stringify({
						searchTerm: params.term // search term
					});
				},
				processResults: function (response) {
					return {
						results: response
					};
				},
					cache: true,
				},
			templateSelection: function (data, container) {
				let schedule_type_desc = $('#modal_create_schedule').find("#schedule_type_desc");
				// Add attributes to the <option> tag for the selected option
				if(data.title) {
					schedule_type_desc.html(data.title);
				}
				return data.text;
			}
		});
	}

	$('#modal_create_schedule').on('click', '#button_create_schedule', function () {
		button_disabled(this);
		var modal = $('#modal_create_schedule');
		let schedule_name = modal.find('#schedule_name').val().trim();
		let schedule_desc = modal.find('#schedule_desc').val().trim();
		let date_start = modal.find('#date_start').val().trim();
		let time_start = modal.find('#time_start').val().trim();
		let time_end = modal.find('#time_end').val().trim();
		let schedule_type = Number(modal.find('#schedule_type').val().trim());

		let reg_date = /^\d{4}(.|-)\d{2}(.|-)\d{2}$/;
		let reg_time = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
		let validation = true;


		if (reg_date.test(date_start) === false) {
			show_tooltip('#date_start');
			$('#date_start').focus();
			validation = false;
		}
		else if (reg_time.test(time_start) === false) {
			show_tooltip('#time_start');
			$('#time_start').focus();
			validation = false;
		}
		else if (reg_time.test(time_end) === false) {
			show_tooltip('#time_end');
			$('#time_end').focus();
			validation = false;
		}
		else if (Number.isInteger(schedule_type) === false) {
			show_tooltip('#schedule_type');
			$('#schedule_type').focus();
			validation = false;
		}
 		console.log(validation);
		if(validation){
			let request = JSON.stringify({
				"schedule_name": schedule_name,
				"schedule_desc": schedule_desc,
				"date_start": date_start,
				"time_start": time_start,
				"time_end": time_end,
				"schedule_type": schedule_type
			});
			console.log(request);
			$.post('/api/createSchedule', request, function (data) {
				if (data.status) {
					reload_main_table();
					modal.modal('hide');
					swal({
						title: "Успешно!",
						text: `Номер графика ${data.response}`,
						type: "success",
						timer: 2000,
						showConfirmButton: false
					});
				} else if(data.response === 'not_valid_params') {
					swal({
						title: "Не удалось!",
						text: "Проверьте корректность заполнения всех полей",
						type: "error"
					});
				} else {
					swal({
						title: "Не удалось!",
						text: "Опс! Что-то пошло не так",
						type: "error"
					});
				}
			});
		}
	});


	// очищаем модальное окно после закрытия
	$('#modal_create_schedule').on('hidden.bs.modal', function () {
		$(this).find('input').val('');
		$(this).find('select:not(#date_start)').val(null).empty().select2({ data: [] });
		$(this).find("#schedule_type_desc").html(default_text);
	});

	// Кнопка Назначить график сотруднику
	$('.body').on('click', '#add_schedule_for_users', function () {
		initUsersSelect();
		initScheduleSelect();
		let modal =  $('#modal_assign_schedule');
		modal.find('#date_start_user_schedule').datepicker({
			startDate: 'tomorrow',
			language: $.i18n().locale,
			format: 'yyyy-mm-dd'
		}).datepicker('setDate', 'tomorrow');
		modal.modal('toggle');
	});

	function initUsersSelect() {
		$('#modal_assign_schedule').find('#user').select2({
			placeholder: 'Выберите сотрудника',
			//allowClear: true,
			width: '100%',
			ajax: {
				url: '/api/getUserForSelect2',
				dataType: 'json',
				type: 'POST',
				data: function (params) {
					return JSON.stringify({
						searchTerm: params.term // search term
					});
				},
				processResults: function (response) {
					return {
						results: response
					};
				},
				cache: true
			}
		});
	}

	var old_id_schedule;
	$('#modal_assign_schedule').on('change', '#user', function () {
		let id = $(this).val();
		var data = JSON.stringify({"id": id});
		let modal = $('#modal_assign_schedule');
		$.post('/api/getUserInfo', data, function (data) {
			console.log(data);
			var string = JSON.stringify(data);
			var response = JSON.parse(string);
			var full = response.data[0];
			if (full.user_schedule.status){
				var schedule_name = full.user_schedule.response[0].title_new_schedule;
				var schedule_date_add = full.user_schedule.response[0].date_add;
				var schedule_date_start = full.user_schedule.response[0].date_start;
				var checked = full.user_schedule.response[0].active_new_schedule;
				old_id_schedule = full.user_schedule.response[0].old_id_schedule;
			}else {
				var schedule_name = '-';
				var schedule_date_add = '-';
				old_id_schedule = null;
			}
			modal.find("#user_id_for_modal").html(full.id);
			modal.find("#user_name_for_modal").html(full.fio);
			modal.find("#user_department_for_modal").html(full.department_name);
			modal.find("#user_position_for_modal").html(full.position_name);
			modal.find("#user_schedule_for_modal").html(full.id);

			let badge_color = full.color ? full.color : 'light';
			let block = `<span class="badge-new badge-new-${badge_color} user_status" data-status_id="${full.status_id}">${full.status}</span>`;

			if (full.contacts.status) {
				let contacts = '';
				let phones = full.contacts.response.filter(item => item.type_id === 1);
				console.log(phones);
				if(phones.length > 0) {
					contacts += `Телефон: ${phones[0].value}<br>`;
				}
				let email = full.contacts.response.find(item => item.type_id === 2);
				if(email) {
					contacts += `Email: ${email.value}<br>`;
				}
				let sip = full.contacts.response.find(item => item.type_id === 5);
				if(sip) {
					contacts += `SIP: ${sip.value}</li>`;
				}
				modal.find("#user_contact_for_modal").html(contacts);
			}
			modal.find("#user_status_for_modal").html(block);

			let schedule_for_modal = `${schedule_name}`;
			if (full.user_schedule.status) {
				if (checked) {
					schedule_for_modal += ` <i class="fa fa-check green" aria-hidden="true" title="График активен"></i>`;
				} else {
					schedule_for_modal += ` <i class="fa fa-times red" aria-hidden="true" title="График не активен"></i>`;
				}
				schedule_for_modal += `<br>Установлен: ${schedule_date_add}<br>Действует с: ${schedule_date_start}`;
			}
			modal.find("#user_schedule_for_modal").html(schedule_for_modal);

		});
	});

	function initScheduleSelect() {
		$('#modal_assign_schedule').find('#schedule').select2({
			placeholder: 'Выберите график',
			//allowClear: true,
			width: '100%',
			ajax: {
				url: '/api/getScheduleForSelect',
				dataType: 'json',
				type: 'POST',
				data: function (params) {
					return JSON.stringify({
						searchTerm: params.term // search term
					});
				},
				processResults: function (response) {
					return {
						results: response
					};
				},
				cache: true
			}
		});
	}

	$('#modal_assign_schedule').on('click', '#button_save_user_schedule', function () {
		button_disabled(this);
		var modal = $('#modal_assign_schedule');
		let user_id = modal.find('#user option:selected').val();
		let schedule_id = modal.find('#schedule option:selected').val();
		let date_start_user_schedule = modal.find('#date_start_user_schedule').val();

		let reg_date = /^\d{4}(.|-)\d{2}(.|-)\d{2}$/;
		let validation = true;

		//проверяем дату выбранную в datepicker, дата не раньше чем завтра
		var date_from_form = new Date(date_start_user_schedule).getTime();
		var date_temp = new Date().toISOString().slice(0, 10);
		var date_today = new Date(date_temp).getTime();
		var daysLag = Math.ceil(Math.abs(date_today - date_from_form) / (1000 * 3600 * 24));

		if (reg_date.test(date_start_user_schedule) === false && daysLag <= 0) {
			show_tooltip('#date_start_user_schedule');
			$('#date_start_user_schedule').focus();
			validation = false;
		}
		else if (user_id === null) {
			show_tooltip('#user');
			$('#user').focus();
			validation = false;
		}
		else if (schedule_id === null) {
			show_tooltip('#schedule');
			$('#schedule').focus();
			validation = false;
		}

		if (validation && is_all_selected()) {
			let request = JSON.stringify({
				"user_id": user_id,
				"schedule_id": schedule_id,
				"date_start_user_schedule": date_start_user_schedule,
				"old_schedule_id": old_id_schedule
			});

			$.post('/api/changeSchedule', request, function (data) {
				if (data.status) {
					reload_main_table();
					modal.modal('hide');
					swal({
						title: "Успешно!",
						text: `График изменен`,
						type: "success",
						timer: 2000,
						showConfirmButton: false
					});
				} else if(data.response === 'not_valid_params') {
					swal({
						title: "Не удалось!",
						text: "Проверьте корректность заполнения всех полей",
						type: "error"
					});
				} else {
					swal({
						title: "Не удалось!",
						text: "Опс! Что-то пошло не так",
						type: "error"
					});
				}
			});
		}
	});

	function is_all_selected(){
		let result = true;
		$('#modal_assign_schedule select').each(function (i, item) {
			let val = $(this).val();
			if (val === null || val === '') {
				result = false;
				show_tooltip(item);
			}
		});
		return result;
	}

	// очищаем модальное окно после закрытия
	$('#modal_assign_schedule').on('hidden.bs.modal', function () {
		$(this).find('.auto_clear').html("");
		$(this).find('input').val('');
		$(this).find('select:not(#date_start_user_schedule)').val(null).empty().select2({ data: [] });
	});


});
