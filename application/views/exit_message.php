<?php
$dir_path = dirname(__DIR__);
$schedule = empty($schedule) ? '' : $schedule;
$result = empty($result) ? '' : $result;
$first_enter = date("H:i:s",strtotime($schedule[0]->first_enter));
$last_exit = date("H:i:s",strtotime($schedule[0]->last_exit));
$color = empty($result['color']) ? '' : $result['color'];
$status_day = empty($result['status_day']) ? '' : $result['status_day'];
if($status_day == 2) {
	$pop_up = "<span class='badge-new badge-new-".$color."' data-status_id='".$status_day."'>".lang("taken_day")."</span>";
} else if($status_day == 3){
	$pop_up = "<span class='badge-new badge-new-".$color."' data-status_id='".$status_day."'>".lang("not_taken_day")."</span>";
} else {
	$pop_up = '';
}
?>
<link rel="stylesheet" href="<?=base_url('assets/css')?>/badge-new.css"/>
<?php include_once 'blocks/head.php';?>
</head>
<body class="theme-blue">
<div class="mx-auto" style="max-width: 26rem;">
	<div class="card bg-light text-center mt-3" style="max-width: 24rem;">
		<div class="card-header"><h5>Авторизация</h5></div>
		<div class="card-body">
			<div class="card-title">
				<div>Ваш сеанс работы успешно завершен</div>
				<br>
				<div class="card-text"><h3>Вход&nbsp;&nbsp;&nbsp;&nbsp;: <?= $first_enter; ?> <br> Выход&nbsp;: <?= $last_exit; ?></h3></div>
				<p><?= $pop_up; ?></p>
				<div>Хорошего настроения!</div>
				<hr>
				<a href="<?=base_url();?>" class="btn btn-success">Возобновить</a>
			</div>
		</div>
	</div>
</body>
</html>

