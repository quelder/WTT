<?php
class Users_model extends CI_Model{

	protected $user_id;
	protected $lang_id;
	protected $company_id;
	protected $position_id;


	public function getScheduleByUserID($user_id=null){
		if (isset($user_id) && is_numeric($user_id)){
			$this->db->select("u.id, u.last_name, ush.old_id_schedule, usla1.name as title_old_schedule, usl1.active as active_old_schedule,  ush.new_id_schedule, usla2.name as title_new_schedule, usl2.active as active_new_schedule, to_char(ush.date_add, 'YYYY-MM-DD HH24:MI') as date_add, ush.date_start");
			$this->db->from("users_schedule_history as ush");
			$this->db->join("users as u", "u.id = ush.user_id", "left");
			$this->db->join("users_schedule_lib as usl1", "usl1.id = ush.old_id_schedule", "left");
			$this->db->join("users_schedule_lib as usl2", "usl2.id = ush.new_id_schedule", "left");
			$this->db->join("users_schedule_lang as usla1", "usla1.item_id = ush.old_id_schedule", "left");
			$this->db->join("users_schedule_lang as usla2", "usla2.item_id = ush.new_id_schedule", "left");
			$this->db->where("ush.user_id", $user_id);
			$this->db->where("u.company_id", $this->company_id);
			$this->db->where("(usla1.lang_id = $this->lang_id or usla1.lang_id is null)");
			$this->db->where("(usla2.lang_id = $this->lang_id or usla2.lang_id is null)");
			$this->db->where("ush.date_start <= to_char(CURRENT_DATE, 'YYYY-MM-DD')::date");
			$this->db->order_by("ush.id", "DESC");
			//$this->db->limit(1);
			$result = $this->db->get()->result();
			if(count($result) > 0) {
				return array('status' => true, 'response' => $result);
			}
		}
		return array('status' => false, 'response' => $user_id);
	}

	public function getScheduleQueue(){
		$this->db->select("count(id) as count ");
		$this->db->from('users_schedule_lib');
		$this->db->where("(company_id = $this->company_id OR company_id is null)"); // График компании или доступен всем
		return $this->db->count_all_results();
	}

	public function makeQueryScheduleList($search, $f){
		$this->db->select("us.id, us.title, usl.name, usl.desc, us.time_start, us.time_end, us.date_start as schedule_date_start, to_char(us.date_add, 'YYYY-MM-DD HH24:MI') as date_add, to_char(us.date_mod, 'YYYY-MM-DD HH24:MI') as date_mod, us.active, us.user_id, us.company_id");
		$this->db->from('users_schedule_lib as us');
		$this->db->join("users_schedule_lang as usl", "usl.item_id = us.id", "left");
		$this->db->where("(us.company_id = $this->company_id OR us.company_id is null)"); // График компании или доступен всем
		if(!empty($f)){
			if(isset($f->active) && $f->active !== ''){
				$this->db->where('active', boolval($f->active));
			}
			if(isset($f->date_range)){
				$date_from = new DateTime($f->date_range[0]);
				$date_from = $date_from->format('Y-m-d');
				$date_to = new DateTime($f->date_range[1]);
				$date_to = $date_to->format('Y-m-d');
				$this->db->where("date(us.date_add) >= '$date_from'");
				$this->db->where("date(us.date_add) <= '$date_to'");
			}
		}
		$search = trim($search);
		if ($search !== ''){
			if (preg_match('/^\d+$/', $search)) {
				$this->db->where("us.id", $search);
			}
			else{
				$this->db->where("(usl.name ILIKE '%$search%')");
			}
		}
		$this->db->order_by("us.company_id", "DESC");
	}

	public function getScheduleList($limit=25, $offset=0, $order = array(), $search = '', $ext_filter = array()){
		$this->makeQueryScheduleList($search, $ext_filter);
		$count = $this->db->count_all_results();
		$this->makeQueryScheduleList($search, $ext_filter);
		$this->db->limit($limit, $offset);
		if (count($order) > 0){
			$order_column = $order['column'] === 'schedule_name' ? 'id' : $order['column'];
			$white_list = array('id', 'schedule_name', 'date_add');
			if(in_array($order_column, $white_list)) {
				$this->db->order_by($order_column, $order['dir']);
			}
			else{
				$this->db->order_by("date_add", "DESC");
			}
		}else{
			$this->db->order_by("date_add", "DESC");
		}
		$result = $this->db->get()->result();
		foreach ($result as $item){
			if ($item->company_id == $this->company_id){
				$item->author = $this->getShortUserNameById($item->user_id);
			}
			else if ($item->company_id == null){
				$item->author = null;
				$item->active = null;
			}

		}
		return array(
			"counter" => $count,
			"data" => $result
		);
	}

	public function changeStatusSchedule($schedule_id=null, $active=null){
		if (isset($schedule_id, $active) && is_bool($active) && is_numeric($schedule_id)){
			$this->db->set('active', $active);
			$this->db->set('user_id', $this->user_id);
			$this->db->set('date_mod', 'NOW()');
			$this->db->where('id', $schedule_id);
			$this->db->where('company_id', $this->company_id);
			$this->db->update('users_schedule_lib');
			$count = $this->db->affected_rows();
			$answer = $count > 0 ? array('status' => true) : array('status' => false);
		}
		else{
			$answer = array('status' => false);
		}
		return $answer;
	}

	// Создание нового графика
	public function createSchedule($schedule_name = null, $schedule_desc = null, $date_start = null, $time_start = null, $time_end = null, $type_id = null) {
		if (isset($time_start, $time_end)){
			$this->db->insert('users_schedule_lib', array("title" => $schedule_name, "date_start" => $date_start, "time_start" => $time_start, "time_end" => $time_end, "user_id" => $this->user_id, "company_id" => $this->company_id, "date_add" => "NOW()", "active" => "true", "type_id"=>$type_id));
			$id = $this->db->insert_id();
			$this->db->insert('users_schedule_lang', array("item_id" => $id, "lang_id" => $this->lang_id, "name" => $schedule_name, "user_id" => $this->user_id, "desc" => $schedule_desc, "date_add" => "NOW()"));
			return array('status' => true, "response" => $id);
		}
		return array("status" => false);
	}

	public function getScheduleForSelect($word = null){ //
		$this->db->select("us.id || ' - ' || usl.name || ' (' || to_char(us.time_start, 'HH24:MI') || '-' ||  to_char(us.time_end, 'HH24:MI') || ')' as text, us.id");
		//$this->db->select("us.id, us.title, usl.name, usl.desc, us.time_start, us.time_end, us.date_start as schedule_date_start, to_char(us.date_add, 'YYYY-MM-DD HH24:MI') as date_add, to_char(us.date_mod, 'YYYY-MM-DD HH24:MI') as date_mod, us.active, us.user_id, us.company_id");
		$this->db->from('users_schedule_lib as us');
		$this->db->join("users_schedule_lang as usl", "usl.item_id = us.id", "left");
		$this->db->where("(us.company_id = $this->company_id OR us.company_id is null)"); // График компании или доступен всем
		$this->db->where("(usl.lang_id = $this->lang_id OR usl.lang_id is null)");
		if (isset($word)) {
			$this->db->where("(usl.name ILIKE '%$word%')");
		}
		$this->db->order_by("us.id", "ASC");
		$result = $this->db->get()->result();
		return $result;
	}

	private function sendPostRequest($url_request, $body_request){
		$postdata = http_build_query(
			$body_request
		);
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);
		$context  = stream_context_create($opts);
		return file_get_contents($url_request, false, $context);
	}

	// Запись изменения графика
	public function changeSchedule($schedule_id = null, $user_id = null, $date_start = null, $old_schedule_id = null) {
		if (isset($schedule_id, $user_id) && is_numeric($schedule_id)){
			$this->db->insert('users_schedule_history', array("user_id" => $user_id, "old_id_schedule" => $old_schedule_id, "new_id_schedule" => $schedule_id, "author_id" => $this->user_id, "company_id" => $this->company_id, "date_add" => "NOW()", "date_start" => $date_start));
			$id = $this->db->insert_id();
			$company_id = $this->company_id;
			sendSchedule($schedule_id, $user_id, $date_start, $old_schedule_id, $company_id);
			return array('status' => true, 'response' => $id);
		}
		return array("status" => false);
	}

	public function getScheduleTypeForSelect(){
		$this->db->select("ustla.name as text, ustla.desc as title, ustl.id");
		$this->db->from("users_schedule_type_lib as ustl");
		$this->db->join("users_schedule_type_lang as ustla", "ustl.id = ustla.item_id and ustla.lang_id = $this->lang_id", "left");
		$this->db->order_by("ustl.id", "ASC");
		$result = $this->db->get()->result();
		return $result;
	}

	public function getCalendarData($start = null, $end = null){
		$this->db->select("date, type_id, title, company_id, comment, RANK() OVER (PARTITION BY date ORDER BY date_add DESC) date_add_rank");
		$this->db->from("main_calendar");
		$this->db->where("date >= '$start'");
		$this->db->where("date <= '$end'");
		//$this->db->where("type_id = 1");
		$this->db->where("(company_id = $this->company_id OR company_id is null)");
		$this->db->order_by("date", "ASC");
		$result = $this->db->get()->result();

		$data = array();
		foreach ($result as $item){
			if($item->date_add_rank == 1 && $item->type_id ==1){
				array_push($data, $item);
			}
		}
		return array(
			"succes" => true,
			"data" => $data
		);
	}

	public function getCalendarDataForTable($start = null, $end = null){
		$this->db->select("date, type_id, title, company_id, comment, RANK() OVER (PARTITION BY date ORDER BY date_add DESC) date_add_rank ");
		$this->db->from("main_calendar");
		$this->db->where("date >= '$start'");
		$this->db->where("date <= '$end'");
		$this->db->where("(company_id = $this->company_id OR company_id is null)");
		$this->db->order_by("date", "ASC");
		$result = $this->db->get()->result();

		$data = array();
		foreach ($result as $item){
			if($item->date_add_rank == 1){
				array_push($data, $item);
			}
		}

		return array(
			"succes" => true,
			"data" => $data
		);
	}

	public function getWorkSchedule($start = null, $end = null){
		$this->db->select("to_char(usd.date_time_start, 'YYYY-MM-DD HH24:MI:SS') as date_time_start,
							to_char(usd.date_time_end, 'YYYY-MM-DD HH24:MI:SS') as date_time_end,
							usd.user_id, usd.status_day_id, uds.color, udsl.name");
		$this->db->from("users_schedule_daily as usd");
		$this->db->join("users_days_status_lib as uds", "usd.status_day_id = uds.id", "left");
		$this->db->join("users_days_status_lang as udsl", "uds.id = udsl.item_id", "left");
		$this->db->where("to_char(usd.date_time_start, 'YYYY-MM-DD') >= '$start'");
		$this->db->where("to_char(usd.date_time_end, 'YYYY-MM-DD') <= '$end'");
		$this->db->where("usd.user_id", $this->user_id);
		$this->db->order_by("usd.date_time_start", "ASC");
		$result = $this->db->get()->result();
		return array(
			"succes" => true,
			"data" => $result
		);
	}

	public function getWorkScheduleForTable($order = array(), $search = '', $ext_filter = array()){
		$this->db->select("to_char(date, 'DD') as days, type_id, company_id, title, comment, RANK() OVER (PARTITION BY date ORDER BY date_add DESC) date_add_rank ");
		$this->db->from("main_calendar");
		$this->db->where("(company_id = $this->company_id OR company_id is null)");
		$this->db->order_by("date", "ASC");
		if (!empty($ext_filter)) {
			if (isset($ext_filter->month)) {
				$this->db->where("to_char(date, 'MM-YYYY') = '$ext_filter->month'");
			}
		}
		$result = $this->db->get()->result();

		$arr = array();
		foreach ($result as $item){
			if($item->date_add_rank == 1){
				array_push($arr, $item);
			}
		}

		$this->makeQueryUsersSchedule($search, $ext_filter);
		//$this->db->limit($limit, $offset);
		if (count($order) > 0){
			$white_list = array('id', 'fio', 'position_name', 'department_name');
			if(in_array($order['column'], $white_list)) {
				$this->db->order_by($order['column'], $order['dir']);
			}
			else{
				$this->db->order_by("u.id", "ASC");
			}
		}else{
			$this->db->order_by("u.last_name", "ASC");
		}

		$result = $this->db->get()->result();
//		foreach ($result as $item){
//			if($item->date_add_rank > 1){
//				unset($item);
//			}
//		}
		$users = array();
		$data = array();
		$count = count($arr);
		foreach ($result as $item) {
			if (!in_array($item->fio, $users, true)) {
				array_push($users, $item->fio);
			}
		}

		foreach ($users as $item) {
			array_push($data, array($item));
		}
		unset($item);

		foreach ($data as $key => $value) {
			for ($i = 1; $i <= $count; $i++) {
				array_push($data[$key], '-');
			}
		}
		unset($value);

		foreach ($data as $key => $value) {
			foreach ($result as $item) {
				if ($value[0] === $item->fio) {
					foreach ($value as $key_v => $value_v) {
						if ($key_v == (int)$item->days) {
							$data[$key][$key_v] = implode("|", array($item->status_day_id, $item->time_start, $item->time_end, $item->date, $item->color, $item->name_day));
						}
					}
				}
			}
		}

		return array(
			"succes" => true,
			"columns" => $arr,
			"data" => $data
		);
	}

	public function makeQueryUsersSchedule($search, $f)	{
		$this->db->select("to_char(usd.date_time_start, 'DD') as days, to_char(usd.date_time_start, 'YYYY-MM-DD') as date, usd.user_id, usd.id_schedule, usd.date_time_start, usd.date_time_end,
							to_char(usd.date_time_start, 'HH24:MI') as time_start, to_char(usd.date_time_end, 'HH24:MI') as time_end,
							usd.status_day_id, CONCAT(NULLIF(u.last_name, ''), ' ', NULLIF(u.first_name, ''), ' ', NULLIF(u.middle_name, '') || ' | ' || u.id) AS fio,
							u.position_id,pl.name as position_name,dl.name as department_name, uds.color, udsl.name as name_day");
		$this->db->from("users_schedule_daily as usd");
		$this->db->join("users as u", "u.id = usd.user_id", "left");
		$this->db->join('positions_lib as p', 'p.id = u.position_id', 'left');
		$this->db->join("positions_lang as pl", "pl.item_id = u.position_id and pl.lang_id = $this->lang_id", "left");
		$this->db->join('departments_lib as d', 'd.id = p.department_id', 'left');
		$this->db->join("departments_lang as dl", "dl.item_id = d.id and dl.lang_id = $this->lang_id", "left");
		$this->db->join("users_days_status_lib as uds", "usd.status_day_id = uds.id", "left");
		$this->db->join("users_days_status_lang as udsl", "uds.id = udsl.item_id and and udsl.lang_id = $this->lang_id", "left");
		//$this->db->where("to_char(usd.date_time_start, 'MM-YYYY') = '$month'");
		$this->db->where('u.company_id', $this->company_id);
		$this->db->where('uds.active', true);
		if (!empty($f)) {
			if (isset($f->month)) {
				$this->db->where("to_char(usd.date_time_start, 'MM-YYYY') = '$f->month'");
			}
			if (isset($f->department_id)) {
				$this->db->where('p.department_id', $f->department_id);
			}
			if (isset($f->position_id)) {
				$this->db->where('u.position_id', $f->position_id);
			}
			$search = trim($search);
			if ($search !== '') {
				if (preg_match('/^\d+$/', $search)) {
					$this->db->where("u.id", $search);
				} else {
					$this->db->where("(u.last_name ILIKE '%$search%')");
				}
			}
			$this->db->order_by("days", "ASC");

		}
	}

	// Запись изменения данных по дню
	public function changeDayDetail($date_type = null, $time_start = null, $time_end = null, $user_id = null, $date = null) {
		if (isset($date, $user_id) && is_numeric($date_type)){
			$date_time_end = $date . ' ' . $time_end;
			// update
			$this->db->set('status_day_id', $date_type);
			if(!empty($time_start)) {
				$date_time_start = $date . ' ' . $time_start;
				$this->db->set('date_time_start', $date_time_start);
			}
			if(!empty($time_end)) {
				$date_time_end = $date . ' ' . $time_end;
				$this->db->set('date_time_end', $date_time_end);
			}
			$this->db->where("user_id", $user_id);
			$this->db->where("to_char(date_time_start, 'YYYY-MM-DD') = '$date'");
			$this->db->update('users_schedule_daily');
			return array('status' => true);
		}
		return array("status" => false);
	}

	public function getScheduleHistoryByUserId($user_id = null) {
		$this->db->select("ush.id, to_char(ush.date_add, 'YYYY-MM-DD') as date_add, ush.date_start, usla.name as schedule_name, 
                           u.last_name, u.first_name, u.middle_name");
		$this->db->from("users_schedule_history as ush");
		$this->db->join('users_schedule_lib as usl', 'usl.id = ush.new_id_schedule', 'left');
		$this->db->join("users_schedule_lang as usla", "usla.item_id = usl.id and usla.lang_id = $this->lang_id", "left");
		$this->db->join('users as u', 'u.id = ush.author_id', 'left');
		$this->db->where("ush.user_id", $user_id);
		$this->db->where("ush.date_start <= to_char(CURRENT_DATE, 'YYYY-MM-DD')::date");
		$this->db->order_by("ush.date_add", 'desc');
		$result = $this->db->get()->result();
		foreach ($result as $item){
			$e = $result[0];
			$item->author = $e->last_name . ' ' . mb_substr($e->first_name, 0, 1) . '.' . mb_substr($e->middle_name, 0, 1) . '.';
		}
		return $result;
	}

	public function getStatusDayForSelect2($search = null, $day_type_exclude = null){
		$this->db->select("uds.id, udsl.name as text, uds.color");
		$this->db->from('users_days_status_lib as uds');
		$this->db->join("users_days_status_lang as udsl", "udsl.item_id = uds.id and udsl.lang_id = $this->lang_id", "left");
		$this->db->where("(uds.company_id is null OR uds.company_id = $this->company_id)");
		$this->db->where('uds.active', true);
		$this->db->where_not_in('uds.id', $day_type_exclude);
		if (isset($search)) {
			$this->db->where("(uds.name ILIKE '%$search%')");
		}
		$this->db->order_by("uds.id", 'ASC');
		$result = $this->db->get()->result();
		return $result;
	}

	// Получаем массив данных о табеле
	public function getEntryData(){
		$user_id = $this->session->userdata('user_id');
		$this->db->select("to_char(eh.date_add, 'YYYY-MM-DD') as date, 
		to_char(min(case when type = 0 then eh.date_add end), 'YYYY-MM-DD HH24:MI:SS') as first_enter,
		to_char(max(case when type = 1 then eh.date_add end), 'YYYY-MM-DD HH24:MI:SS') as last_exit,
		usd.date_time_start, usd.date_time_end,	usla.name, usd.id, usd.status_day_id, u.status_id, u.id as user_id, u.company_id, udsl.color");
		$this->db->from('entry_history as eh');
		$this->db->join("users_schedule_daily as usd", "eh.user_id = usd.user_id", "left");
		$this->db->join('users_schedule_lib as usl', 'usl.id = usd.id_schedule', 'left');
		$this->db->join("users_schedule_lang as usla", "usla.item_id = usl.id and usla.lang_id = $this->lang_id", "left");
		$this->db->join("users as u", "u.id = eh.user_id", "left");
		$this->db->join("users_days_status_lib as udsl", "udsl.id = usd.status_day_id", "left");
		$this->db->where('eh.user_id', $user_id);
		//$this->db->where('company_id', $this->company_id);
		$this->db->where("(to_char(eh.date_add, 'YYYY-MM-DD') = to_char(usd.date_time_start, 'YYYY-MM-DD'))");
		$this->db->where("(to_char(eh.date_add, 'YYYY-MM-DD') = to_char(CURRENT_DATE, 'YYYY-MM-DD'))");
		$this->db->group_by("usd.date_time_start, to_char(eh.date_add, 'YYYY-MM-DD'), usd.date_time_end, usla.name, usd.id, usd.status_day_id, u.status_id, u.id, u.company_id, udsl.color");
		$result =  $this->db->get()->result();
		return $result;
	}

	public function updateStatusDay($schedule=null){
		if (isset($schedule)){
			$url = 'http://127.0.0.1:1880/users/timesheet';
			$schedule = empty($schedule) ? '' : $schedule;
			return sendPostCurlRequest($url, array('id' => $schedule[0]->id, 'first_enter' =>  $schedule[0]->first_enter, 'last_exit' => $schedule[0]->last_exit, 'date_time_start' => $schedule[0]->date_time_start, 'date_time_end' => $schedule[0]->date_time_end, 'status_id' => $schedule[0]->status_id , 'color'=> $schedule[0]->color));
		}
		return array('status' => false);
	}

	public function changeCalendar($date=null, $type_id=null, $title=null, $comment=null){
		if (isset($date, $type_id) && is_numeric($type_id)){
			$this->db->set('type_id', $type_id);
			$this->db->set('user_id', $this->user_id);
			$this->db->set('title', $title);
			$this->db->set('comment', $comment);
			$this->db->where('date', $date);
			$this->db->where('company_id', $this->company_id);
			$this->db->update('main_calendar');
			$count = $this->db->affected_rows();
			if($count > 0) {
				$answer = array('status' => true);
			} else {
				$this->db->insert('main_calendar', array("date" => $date, "type_id" => $type_id,"user_id" => $this->user_id, "title" => $title, "company_id" => $this->company_id, "comment" => $comment ));
				$id = $this->db->insert_id();
				$answer = array('status' => true, 'response' => $id);
			}

		}
		else{
			$answer = array('status' => false);
		}
		return $answer;
	}

}
