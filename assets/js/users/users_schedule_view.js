$(function() {

	$("#monthpicker").datepicker({
		format: "mm-yyyy",
		startView: "months",
		minViewMode: "months",
		language: $.i18n().locale,
		todayBtn: true,
		todayHighlight: true,
		showButtonPanel: true,
		autoclose:true
	}).on('change.monthpicker', function () {
	}).datepicker('setDate', 'today');


	load_main_table();
	initFindDepartmentSelect();
	initFindPositionSelect();
	var date_request;
	var id_request;
	var day_type;

	function load_main_table() {
		var month = $('#monthpicker').val();
		var id = $('#find_user_id').val();
		var department_id = $('#find_department').val();
		var position_id = $('#find_position').val();
		let ajaxData = {'month': month, 'id': id, 'department_id': department_id, 'position_id': position_id};

		$.ajax({
			order: [],
			url: "/api/getWorkScheduleForTable",
			type: 'POST',
			dataType: 'json',
			data: JSON.stringify(ajaxData),
			success: function (data) {
				// формируем массив для столбцов
				columns_length = data.columns.length;
				let columns = [{text: "ФИО", title: "ФИО", type_id: "-"}];
				for (i = 0; i < columns_length; i++) {
					columns.push({
							title: data.columns[i].days,
							text: data.columns[i].title,
							type_id: data.columns[i].type_id,
							orderable: false,
							className: 'text-center'
						}
					);
				}

				//формируем данные для таблицы
				user_data = data.data;
				if (user_data.length === 0) {
					user_data.push(['<br>| ']);
					for (i = 0; i < columns_length; i++) {
						user_data[0].push('-');
					}
				}
				additional_data = data.additional_data;


				var my_table = $('#js_main_table').DataTable({
					//fixedHeader: true,
					destroy: true,
					columns: columns,
					data: user_data,
					//aaData: user_data,
					dom: 'Bft',
					rowCallback: function (row, user_data) {
						$(`td:eq(0)`, row).html(`<a href="/users/find/${Number(user_data[0].split('|')[1])}">${initials(user_data[0])}</a>`);
						for (i = 1; i <= columns_length; i++) {
							if (Number(user_data[i].split('|', 1)) === 1) { // Выходной/праздник
								var arr = user_data[i].split('|').slice(1, 3);
								$(`td:eq(${i})`, row).html(``);
							} else if (user_data[i] === "-") {
								$(`td:eq(${i})`, row).html(``);
							} else {
								if (Number(user_data[i].split('|', 1)) === 0) { // для запланированого рабочего дня черный текст и зеленый border
									var text_color = "black border border-success";
								} else {
									var text_color = "white";
								}
								var arr = user_data[i].split('|').slice(1, 3);
								var text = arr.join('<br>');
								var title = user_data[i].split('|', 6)[5];
								var color = user_data[i].split('|', 5)[4];
								$(`td:eq(${i})`, row).html(`<div class="event bg-${color} text-${text_color}" style="text-align: center" title="${title}">${text}</div>`);
							}

						}
					},
					ordering: true,
					paging: false,
					//select: true,
					language: {
						url: `/assets/js/i18n/datatable/${$.i18n().locale}.json`
					},

				})

				my_table.rows().every(function (rowIdx, tableLoop, rowLoop) {
					for (i = 1; i <= columns_length; i++) {
						if (columns[i].type_id === 1) {
							var cell = my_table.cell({row: rowIdx, column: i}).node();
							$(cell).addClass('weekend');
							if (columns[i].text) {
								var cell = my_table.cell({row: rowIdx, column: i}).node();
								$(cell).addClass('holiday');
							}
						}
					}

				});


				if (additional_data.user_roles.indexOf(60) !== -1) {
					$('#js_main_table').on('click', 'tbody td', function () {
						initFindStatusDaySelect();
						let modal = $('#modal_day_data_edit');
						if (my_table.cell(this).data() !== '-') {
							var userData = my_table.row(this).data()[0];
							var dayData = my_table.cell(this).data();
							var date = dayData.split('|')[3];
							var time_start = dayData.split('|')[1];
							var time_end = dayData.split('|')[2];
							var id = Number(userData.split('|')[1]);
							var text = dayData.split('|', 6)[5];
							var color = dayData.split('|', 5)[4];
							var d_type = Number(dayData.split('|', 1));

							date_request = date;
							id_request = id;
							day_type = d_type;

							modal.modal('show').find("#user_id_for_modal").html(id);
							modal.find("#user_name_for_modal").html(userData.split('|', 1));

							let date_for_modal = '';
							if (Number(dayData.split('|', 1)) === 1 || Number(dayData.split('|', 1)) === 0) { // для запланированого рабочего дня черный текст на светлом фоне и зеленый border
								var text_color = "black border border-success";
							} else {
								var text_color = "text-white";
							}

							date_for_modal += `${date} <span class="pop-up bg-${color} ${text_color} col-sm ml-2" style="text-align: center">${text}</span>`;
							modal.find("#user_date_for_modal").html(date_for_modal);


							$(function () {
								var temp = dayData.split('|')[0];
								$("#day_type").val(temp);
							});


							$('#modal_day_data_edit').find('.timepicker_start').timepicker({
								timeFormat: 'H:i',
								scrollDefault: time_start,
								dropdown: true,
								dynamic: true,
								scrollbar: true,
								width: '100%',
							}).val(time_start);
							$('#modal_day_data_edit').find('.timepicker_end').timepicker({
								timeFormat: 'H:i',
								scrollDefault: time_end,
								dropdown: true,
								dynamic: true,
								scrollbar: true,
								width: '100%',
							}).val(time_end);

						}

					});

				}
			}
		})

	}


	$('#refresh_main_table').on('click', function () {
		$('#js_main_table').off('click', 'tbody td');
		$('#js_main_table').DataTable().clear().destroy();
		$('#js_main_table').empty();
		load_main_table();

	});

	function reload_main_table(){
		$('#js_main_table').off('click', 'tbody td');
		$('#js_main_table').DataTable().clear().destroy();
		$('#js_main_table').empty();
		load_main_table();
	}


	$('.body').on('change', '#monthpicker', function () {
		reload_main_table();
	});

	function initFindDepartmentSelect() {
		$('#find_department').select2({
			placeholder: 'Выберите подразделение',
			allowClear: true,
			ajax: {
				url: '/api/getDepartmentForSelect2',
				dataType: 'json',
				type: 'POST',
				data: function (params) {
					return {
						searchTerm: params.term // search term
					};
				},
				processResults: function (response) {
					return {
						results: response
					};
				},
				cache: true
			},
			dropdownAutoWidth: true,
			templateSelection: function (value) {
				return `<i class="fa fa-list mr-2" aria-hidden="true"></i>${value.text}`;
			},
			escapeMarkup: function (markup) { return markup; }
		});
	}


	function initFindPositionSelect(department_id = null) {
		$('#find_position').select2({
			placeholder: 'Выберите должность',
			allowClear: true,
			ajax: {
				url: '/api/getPositionForSelect2',
				dataType: 'json',
				type: 'POST',
				data: function (params) {
					return {
						searchTerm: params.term, // search term
						department_id: department_id
					};
				},
				processResults: function (response) {
					return {
						results: response
					};
				},
				cache: true
			},
			dropdownAutoWidth: true,
			templateSelection: function (value) {
				return `<i class="fa fa-list mr-2" aria-hidden="true"></i>${value.text}`;
			},
			escapeMarkup: function (markup) { return markup; }
		});
	}

	$('.body').on('change', '#find_department', function () {
		$('.body').find("#find_position").val(null).empty().select2({ data: [] });
		let department_id = $(this).val();
		initFindPositionSelect(department_id);
		reload_main_table();
	});

	$('.body').on('change', '#find_position', function () {
		 	reload_main_table();
		 });

	$('#modal_day_data_edit').on('click', '#button_save_day_data_edit', function () {
		button_disabled(this);
		var modal = $('#modal_day_data_edit');
		let date_type = Number(modal.find('#day_type').val().trim());
		let time_start = modal.find('#time_start').val().trim();
		let time_end = modal.find('#time_end').val().trim();

		let reg_date = /^\d{4}(.|-)\d{2}(.|-)\d{2}$/;
		let reg_time = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
		let validation = true;


		if (reg_date.test(date_request) === false) {
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
		else if (Number.isInteger(date_type) === false) {
			show_tooltip('#date_type');
			$('#date_type').focus();
			validation = false;
		}

		if(validation){
			let request = JSON.stringify({
				"user_id": id_request,
				"date": date_request,
				"time_start": time_start,
				"time_end": time_end,
				"date_type": date_type
			});

			$.post('/api/changeDayDetail', request, function (data) {
				if (data.status) {
					reload_main_table();
					modal.modal('hide');
					swal({
						title: "Успешно!",
						text: `Данные изменены`,
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

	// очищаем модальное окно (данные клиента) после закрытия
	$('#modal_day_data_edit').on('hidden.bs.modal', function () {
		$(this).find('.auto_clear').html('');
		$(this).find('input').val('');
	});

	function button_disabled(elem) {
		$(elem).attr("disabled", true);
		setTimeout(function () {
			$(elem).attr("disabled", false);
		}, 3000);
	}

	function show_tooltip(item) {
		$(item).tooltip('show');
		setTimeout(function () {
			$(item).tooltip('dispose');
		}, 3000);
	}

	function initials(str) {
		var text = str.split('|', 1)[0].trim();
		return text.split(/\s+/).map((w,i) => i ? w.substring(0,1).toUpperCase() + '.' : w).join(' ');
	}

	function initFindStatusDaySelect() {
		$('#day_type').select2({
			//allowClear: true,
			placeholder: "Выберите тип дня",
			width: '100%',
			ajax: {
				url: '/api/getStatusDayForSelect2',
				dataType: 'json',
				type: 'POST',
				data: function (params) {
					return {
						searchTerm: params.term, // search term
						day_type_now: day_type
					};
				},
				processResults: function (data) {
					return {
						results: data
					};
				},
				cache: true
			},
			templateResult: formatColor,
			templateSelection: formatColor,
			escapeMarkup: function (markup) { return markup; }
		});
	}

	function formatColor (color) {
		if (!color.color) { return color.text; }
		return `<i class="fa fa-circle text-${color.color} mr-2" aria-hidden="true"></i>${color.text}`;
	}

	$('#schedule_info').on('click', function () {
		button_disabled(this);
		$("#modal_schedule_info").modal('toggle');
	});

	$(function(){
		$('table').wrap('<div class="big-table"></div>');

		function resize_table_box() {
			$('.big-table').each(function(){
				var box_width = $(this).outerWidth();
				var table_width = $(this).children('table').prop('scrollWidth');
				$(this).removeClass('scroll-left');
				if (table_width > box_width) {
					$(this).addClass('scroll-right');
				} else {
					$(this).removeClass('scroll-right');
				}
			});
		}

		resize_table_box();
		$( window ).on('resize', function() {
			resize_table_box();
		});

		$('.big-table table').on('scroll', function() {
			var parent = $(this).parent();
			if ($(this).scrollLeft() + $(this).innerWidth() >= $(this)[0].scrollWidth) {
				if (parent.hasClass('scroll-right') ){
					parent.removeClass('scroll-right');
				}
			} else if ($(this).scrollLeft() === 0){
				if (parent.hasClass('scroll-left')){
					parent.removeClass('scroll-left');
				}
			} else {
				if(!parent.hasClass('scroll-right')){
					parent.addClass('scroll-right');
				}
				if(!parent.hasClass('scroll-left')){
					parent.addClass('scroll-left');
				}
			}
		});
	});

});
