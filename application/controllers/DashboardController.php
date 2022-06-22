<?php

class DashboardController extends InitController {

	protected $_routes = [
		['/status/:id/', [], ['id' => '\d+']],
		['/robots/:id/', [], ['id' => '\d+']],
		['/errorlog/:id/', [], ['id' => '\d+']],
		['/errorclear/:id/', [], ['id' => '\d+']],
		['/backups/:id/', [], ['id' => '\d+']],
		['/cron/:id/', [], ['id' => '\d+']],
		['/configs/:id/', [], ['id' => '\d+']],
		['/translation/:id/', [], ['id' => '\d+']]
	];

	public function statusAction() {
		$status = $this->getStatusApi()->status();

		if (!$status)
			$status = ['api_status' => false];
		else
			$status = (array)$status;

		$status['monitor_log'] = Db::query('SELECT monitor_url.*'
			. ' FROM monitor_url'
			. ' WHERE portal_id=' . $this->id . ' AND test_flag=0'
			. ' ORDER BY url ASC')->fetchAll();

		return jsonResponse($status);
	}

	public function robotsAction() {
		$status = $this->getStatusApi()->robots();

		return jsonResponse(['content' => $status]);
	}

	public function errorlogAction() {
		$status = $this->getStatusApi()->errorLog();

		return jsonResponse(['content' => $status]);
	}

	public function errorclearAction() {
		$status = $this->getStatusApi()->errorClear();

		return jsonResponse(['status' => 'ok']);
	}

	public function backupsAction() {
		$status = $this->getStatusApi();

		return jsonResponse([
			'log' => $status->backupLog(),
			'list' => $status->backupList()
		]);
	}

	public function cronAction() {
		$status = $this->getStatusApi();

		return jsonResponse([
			'log' => $status->cronLog(),
			'tasks' => $status->cronTasks()
		]);
	}

	public function configsAction() {
		$status = $this->getStatusApi();

		return jsonResponse($status->configs());
	}

	protected function getApi() {
		$portal = Api::factory('Portal');
		if (!$portal->findById($this->id))
			return $this->redirect(404);

		return $portal;
	}

	protected function getStatusApi() {
		$portal = $this->getApi();

		return $portal->getApi('Status');
	}

}