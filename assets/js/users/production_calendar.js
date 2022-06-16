$(function() {

	var date_request;

	getCalendarData(function(res){
		getHolidayCalendar(function(events){
			$('#calendar').fullCalendar({
				themeSystem: 'bootstrap4',
				firstDay: 1,
				defaultDate: new Date(),
				buttonText: {
					today: "Сегодня"},
				height: 500,
				plugins: [ 'momentTimezone' ],
				locale: 'uk',
				timeFormat: 'H:mm',
				header: {
					right: 'prev,next',
					center: 'title',
					left:''
				},
				defaultView: 'month',
				editable: false,
				eventLimit: true,
				droppable: false, // this allows things to be dropped onto the calendar
				monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
				monthNamesShort: ['Янв.','Фев.','Март','Апр.','Май','Июнь','Июль','Авг.','Сент.','Окт.','Ноя.','Дек.'],
				dayNames: ["Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота"],
				dayNamesShort: ["ВС","ПН","ВТ","СР","ЧТ","ПТ","СБ"],
				selectable: false,
				select: function(start, end, jsEvent, view) {
					start = moment(start).format("YYYY-MM-DD");
					end = moment(end).add(-1,'hour').format("YYYY-MM-DD");
				},
				eventOrder: "start",
				displayEventTime: false,
				viewRender: function (view, element) {
					let [start, end] = [moment().locale('uk').startOf('year'), moment().locale('uk').endOf('year')];
					if (end < view.end) {
						$("#calendar .fc-next-button").attr('disabled',true).attr('class','fc-next-button btn btn-light');
						return false;
					} else {
						$("#calendar .fc-next-button").attr('disabled',false).attr('class','fc-next-button btn btn-light');
					}
					if (view.start < start) {
						$("#calendar .fc-prev-button").attr('disabled',true).attr('class','fc-prev-button btn btn-light');
						return false;
					} else {
						$("#calendar .fc-prev-button").attr('disabled',false).attr('class','fc-prev-button btn btn-light');
					}
				},
				dayRender: function (date, cell) {
					for (let i in res.data) {
						if (date.format("YYYY-MM-DD") == res.data[i].date) {
							cell.css({'background-color': 'rgba(169,238,171,0.56)', 'border':'1px solid silver'});
							if(res.data[i].title) {
								cell.css({'background-color': 'rgba(225,164,181,0.56)', 'font-size': '8pt'});
								cell.attr("title", res.data[i].title);
							}
						}
					}
				},
				displayEventEnd: true,
				events:	events,
				eventClick: function(event) {
					let modal = $('#successModal');
					modal.modal("show");
					modal.find("#date_for_modal").html(event.start._i);

					// при необходимости выводим название праздника
					if (typeof event.title !== 'undefined' && event.title !== null){
						modal.find("#name_for_modal").html(event.title);
						modal.find("#row_name").removeClass("d-none");
					}

					// при необходимости выводим коментарий
					if (typeof event.comment !== 'undefined' && event.comment !== null){
						modal.find("#comment_for_modal").html(event.comment);
						modal.find("#row_comment").removeClass("d-none");
					}

					type_for_modal = `<span class="pop-up ${event.className} ${event.textColor} col-sm">${event.textType}</span>`;
					modal.find("#type_for_modal").html(type_for_modal);

					date_request = event.start._i;

					var newDiv = '';
					if(event.type_day === 2) {
						newDiv += '<div class="form-group">';
						newDiv += '<label for="radioBtnDiv">Выберите тип дня</label>';
						newDiv += '<div id="radioBtnDiv" title="Выберите тип">';
						newDiv += '<input name="btn" type="radio" class="radioClass" value="2" id="btn" title="Выберите тип дня"/>Праздничный день</div>';
						newDiv += '<div id="radioBtnDiv" title="Выберите тип">';
						newDiv += '<input name="btn" type="radio" class="radioClass" value="1" id="btn" title="Выберите тип дня"/>Выходной день</div>';
						newDiv += '<div id="radioBtnDiv">';
						newDiv += '<input name="btn" type="radio" class="radioClass" value="0" id="btn" title="Выберите тип дня"/>Рабочий день</div></div>';
						modal.find("#container").append(newDiv);
					} else if(event.type_day === 1) {
						newDiv += '<div class="form-group">';
						newDiv += '<label for="radioBtnDiv">Выберите тип дня</label>';
						newDiv += '<div id="radioBtnDiv" title="Выберите тип">';
						newDiv += '<input name="btn" type="radio" class="radioClass" value="2" id="btn" title="Выберите тип дня"/>Праздничный день</div>';
						newDiv += '<div id="radioBtnDiv">';
						newDiv += '<input name="btn" type="radio" class="radioClass" value="0" id="btn" title="Выберите тип дня"/>Рабочий день</div></div>';
						modal.find("#container").append(newDiv);
					} else {
						newDiv += '<div class="form-group">';
						newDiv += '<label for="radioBtnDiv">Выберите тип дня</label>';
						newDiv += '<div id="radioBtnDiv" title="Выберите тип">';
						newDiv += '<input name="btn_holiday" type="radio" class="radioClass" value="2" id="btn" title="Выберите тип дня"/>Праздничный день</div>';
						newDiv += '<div id="radioBtnDiv">';
						newDiv += '<input name="btn_holiday" type="radio" class="radioClass" value="1" id="btn" title="Выберите тип дня"/>Выходной день</div></div>';
						modal.find("#container").append(newDiv);
					}
					modal.find('#radioBtnDiv input:radio').click(function() {
						modal.find("#container_second").empty();
						var blocks = '';
						if ($(this).val() === '2') {
							blocks += '<div></div><div class="form-row">';
							blocks += '<div class="form-group col">';
							blocks += '<label for="day_name">Наименование</label>';
							blocks += '<div class="input-group input-group-sm">';
							blocks += '<input type="text" class="form-control" id="day_name" placeholder="Укажите название праздника" title="Укажите название">';
							blocks += '</div></div></div>';
							modal.find("#container_second").append(blocks);
						}
					});


				}
			});
		});
	});


	function getCalendarData(callback) {
		var current = $.fullCalendar.moment();
		$('#month_year').html(moment(current).locale('ru').format("MMMM YYYY"));
		var firstDay = moment(current).locale('uk').startOf('year').format("YYYY-MM-DD");
		var lastDay = moment(current).locale('uk').endOf('year').format("YYYY-MM-DD");
		let ajaxData = {'start': firstDay, 'end': lastDay};
		$.ajax({
			url: '/api/getCalendarData',
			type: 'POST',
			dataType: 'json',
			data: JSON.stringify(ajaxData),
			success: function(data) {
				res = data;
				callback(res);
			}
		});
	};

	function getHolidayCalendar(callback) {
		var current = $.fullCalendar.moment();
		$('#month_year').html( moment(current).locale('ru').format("MMMM YYYY"));
		firstDay = moment(current).locale('uk').startOf('year').format("YYYY-MM-DD");
		lastDay = moment(current).locale('uk').endOf('year').format("YYYY-MM-DD");
		let ajaxData = {'start': firstDay, 'end':lastDay};
		$.ajax({
			url: '/api/getCalendarDataForTable',
			type: 'POST',
			dataType: 'json',
			data: JSON.stringify(ajaxData),
			success: function(data) {
				res_ws = data;
				let events = [];
				for (let i in res_ws.data) {
					if (res_ws.data[i].type_id === 1 && res_ws.data[i].title !== null) { //рабочий день (запланированный, еще нет отметок о факте и т.д)
						var className = `bg-danger`;
						var textColor = 'text-white';
						var textType = 'Праздничный день';
						var type_day = 2;
					} else if (res_ws.data[i].type_id === 1 && res_ws.data[i].title === null) {
						var className = `bg-success`;
						var textColor = 'text-white';
						var textType = 'Выходной день';
						var type_day = 1;
					} else if (res_ws.data[i].type_id !== 1 && res_ws.data[i].title === null) {
						var className = `bg-white border light`;
						var textColor = 'text-black';
						var textType = 'Рабочий день';
						var type_day = 0;
					}

						events.push({
							title: res_ws.data[i].title,
							start: res_ws.data[i].date,
							// тут будет логика изменения типа евента в зависимости от дня(рабочий, больничный, отпуск)
							className: className,
							textColor: textColor,
							textType: textType,
							type_day: type_day,
							comment: res_ws.data[i].comment
						});

				}
				callback(events);
			}
		});
	};

	// очищаем модальное окно (данные дня) после закрытия
	$('#successModal').on('hidden.bs.modal', function () {
		$(this).find('.auto_clear').html('');
		$(this).find('input').val('');
		$(this).find("#row_name").addClass("d-none");
		$(this).find("#row_comment").addClass("d-none");
	});


	$('#card_calendar').on('click', '#schedule_info', function () {
		button_disabled(this);
		$("#modal_schedule_info").find("#schedule_name").addClass("d-none");
		$("#modal_schedule_info").modal('toggle');
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

	$('#successModal').on('click', '#button_save_day_data', function () {
		button_disabled(this);
		var modal = $('#successModal');
		let date = date_request;
		let comment = modal.find('#comment').val().trim();
		let type = Number($("input[type='radio'].radioClass:checked").val());

		let reg_name = /^[\p{L}`\-()\s,]+$/ui;
		let reg_date = /^\d{4}(.|-)\d{2}(.|-)\d{2}$/;
		let validation = true;

		if (type === 2) {
			var day_name = modal.find('#day_name').val().trim();
			type = 1;
			if (reg_name.test(day_name) === false) {
				show_tooltip('#day_name');
				$('#day_name').focus();
				validation = false;
			}
		}

		if(comment !== null){
			if (reg_name.test(day_name) === false) {
				show_tooltip('#comment');
				$('#comment').focus();
				validation = false;
			}
		}

		if (reg_date.test(date) === false) {
			validation = false;
		}
		else if (Number.isInteger(type) === false) {
			show_tooltip('#btn');
			$('#btn').focus();
			validation = false;
		}

		if(validation){
			let request = JSON.stringify({
				"day_name": day_name,
				"date": date_request,
				"type": type,
				"comment": comment
			});
 console.log(request);
			$.post('/api/changeCalendar', request, function (data) {
				if (data.status) {
					swal({
						title: "Успешно!",
						text: `Данные изменены`,
						type: "success",
						timer: 2000,
						showConfirmButton: false
					});
					document.location.reload();
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

});
