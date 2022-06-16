<?php
$status_day_lib = empty($status_day_lib) ? '' : $status_day_lib;
?>
<style>
	.box {
		height: 1.875rem;
		width: 10%;
		float: left;
		border-radius: 5px;
	}

	.text {
		display: table-cell;
		height: 1.875rem;
		width: 90%;
		vertical-align: middle;
		padding-left: 5px;
	}

	.weekend {
		background-color: rgba(169, 238, 171, 0.56);
		border: 1px solid silver
	}

	.holiday {
		background-color: rgba(225, 164, 181, 0.56);
		border: 1px solid silver
	}

	.workday {
		background-color: white;
		border: 1px solid silver
	}

</style>

<!--  модальное окно справки -->
<div id="modal_schedule_info" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content shadow-lg">
			<div class="modal-header">
				<h5 class="modal-title">Справка</h5>
				<button type="button" class="close" data-dismiss="modal" title="Закрыть">x</button>
			</div>
			<div class="modal-body">
				<div class="form-group row">
					<div class="col">
						<label for="add_last_name">Календарные дни</label>
						<div class="m-b-5">
							<div class="workday box"></div>
							<span class="text"> - Рабочий день</span></div>
						<div class="m-b-5">
							<div class="weekend box"></div>
							<span class="text"> - Выходной день</span></div>
						<div class="m-b-5">
							<div class="holiday box"></div>
							<span class="text"> - Праздничный день</span></div>
					</div>
				</div>
				<div class="form-group row" id="schedule_name">
					<div class="col">
						<label for="add_last_name">Обозначение дней графика</label>
						<?php foreach ($status_day_lib as $days): ?>
							<?php if ($days->id != 1) { ?>
								<div class="m-b-5">
									<div class="box bg-<?= $days->color === 'light' ? 'light border border-success' : $days->color; ?>"></div>
									<span class="text"> - <?= $days->text ?></span></div>
							<?php } ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
