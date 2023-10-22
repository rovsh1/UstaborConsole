<?php

use Api\Model\Report\Form as ReportForm;
use Http\Content\Factory as ContentFactory;
use Mail\Sender as MailSender;

class ReportController extends InitController {

	public function dashboardAction() {
		$request = $this->getRequest();
		if (($action = $request->getQuery('action'))) {
			$portal = Api::factory('Portal');
			if (!$portal->findById($request->getQuery('portal_id')))
				$this->redirect(404);
			switch ($action) {
				case 'cronlog':
					$log = $portal->getApi('Status')->cronlog();
					jsonResponse(['log' => $log]);
					break;
			}
		}
		$this->portals = Api::factory('Portal')->select();
		$this->page->setTitle('Dashboard');
	}

	public function indexAction() {
		$request = $this->getRequest();
		if (($action = $request->getQuery('action'))) {
			$portal = Api::factory('Portal');
			if (!$portal->findById($request->getQuery('portal_id')))
				$this->redirect(404);

		}
		$this->portals = Api::factory('Portal')->select();
		$this->page->setTitle('Dashboard');
	}

	public function master_contacts_balanceAction() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			//->addElement('country_id')
			->addElement('category_id')
			->addElement('promotion', 'checkbox', ['label' => 'Только платные']);
		$this->reportFormSubmit($form, 'masterContactsBalance');

		$this->form = $form;
		$this->page->setTitle('Отчет по списаниям за контакт');
		$this->setTemplate('default');
	}

	public function master_contacts_uniqueAction() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id')//->addElement('category_id')
		;
		$this->reportFormSubmit($form, 'masterContactsUnique');

		$this->form = $form;
		$this->page->setTitle('Уникальные переходы на контакты мастера');
		$this->setTemplate('default');
	}

	public function master_without_projectsAction() {
		$form = new ReportForm();
		$form
			//->addElement('site_id')
			//->addElement('country_id')
			//->addElement('category_id')
		;
		$this->reportFormSubmit($form, 'masterWithoutProjects');

		$this->form = $form;
		$this->page->setTitle('Мастера без проектов');
		$this->setTemplate('default');
	}

	public function master_statisticsAction() {
		$form = new ReportForm();
		$form
			->addElement('site_id')
			->addElement('country_id')
			->addElement('date_interval');
		$this->reportFormSubmit($form, 'masterStatistics');

		$this->step = 100;
		$this->form = $form;
		$this->page->setTitle('Статистика по мастерам');
		$this->setTemplate('default');
	}

	public function customer_statisticsAction() {
		$form = new ReportForm();
		$form;
		$this->reportFormSubmit($form, 'customerStatistics');

		$this->step = 500;
		$this->form = $form;
		$this->page->setTitle('Статистика по заказчикам');
		$this->setTemplate('default');
	}

	public function mastersAction() {
		//$api = new PortalApi();
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id')
			->addElement('category_id');
		$this->reportFormSubmit($form, 'masters');

		$this->form = $form;
		$this->page->setTitle('Выгрузка мастеров');
		$this->setTemplate('default');
	}

	public function customersAction() {
		//$api = new PortalApi();
		$form = new ReportForm();
		$form
			//->addElement('country_id')
			->addElement('created');
		$this->reportFormSubmit($form, 'more-than-10-clicks-customers');

		$this->form = $form;
		$this->page->setTitle('Выгрузка заказчиков');
		$this->setTemplate('default');
	}

	public function promotionsAction() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id')
			->addElement('category_id');
		$this->reportFormSubmit($form, 'promotions');

		$this->form = $form;
		$this->page->setTitle('Отчет о продвижении');
		$this->setTemplate('default');
	}

	public function request_1Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			//->addElement('site_id')
			//->addElement('country_id')
			//->addElement('category_id')
		;
		$this->reportFormSubmit($form, 'request1');

		$this->form = $form;
		$this->page->setTitle('Отчет по количеству');
		$this->setTemplate('default');
	}

	public function request_2Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id')
			->addElement('category_id');
		$this->reportFormSubmit($form, 'request2');

		$this->form = $form;
		$this->page->setTitle('Отчет по заявкам');
		$this->setTemplate('default');
	}

	public function request_3Action() {
		$form = new ReportForm();
		$form
			->addElement('site_id')
			->addElement('country_id');
		$this->reportFormSubmit($form, 'request3');

		$this->form = $form;
		$this->page->setTitle('Мастера по категориям ');
		$this->setTemplate('default');
	}

	public function request_4Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id');
		$this->reportFormSubmit($form, 'request4');

		$this->form = $form;
		$this->page->setTitle('Стоимость контактов');
		$this->setTemplate('default');
	}

	public function request_5Action() {
		$form = new ReportForm();
		$form
			->addElement('site_id')
			->addElement('country_id')
			->addElement('category_id');
		$this->reportFormSubmit($form, 'request5');

		$this->form = $form;
		$this->page->setTitle('Отмененные заявки');
		$this->setTemplate('default');
	}

	public function request_6Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id')
			->addElement('category_id')
			->addElement('type', 'select', ['label' => 'Тип отчета', 'items' => ['customer' => 'Заказчики', 'master' => 'Мастера']]);
		$this->reportFormSubmit($form, 'request6');

		$this->form = $form;
		$this->page->setTitle('Процент различия стоимости услуг');
		$this->setTemplate('default');
	}

	public function request_7Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id');
		$this->reportFormSubmit($form, 'request7');

		$this->form = $form;
		$this->page->setTitle('Выгрузка по категориям');
		$this->setTemplate('default');
	}

	public function category_promotionsAction() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')//->addElement('country_id')
		;
		$this->reportFormSubmit($form, 'categoryPromotions');

		$this->form = $form;
		$this->page->setTitle('Статистка продвижений');
		$this->setTemplate('default');
	}

	public function category_mastersAction() {
		$form = new ReportForm();
		$form
			//->addElement('created')
			->addElement('site_id')//->addElement('country_id')
		;
		$this->reportFormSubmit($form, 'categoryMasters');

		$this->form = $form;
		$this->page->setTitle('Статистка мастеров');
		$this->setTemplate('default');
	}

	public function category_requestsAction() {
		$form = new ReportForm();
		$form
			->addElement('created')//->addElement('site_id')//->addElement('country_id')
		;
		$this->reportFormSubmit($form, 'categoryRequests');

		$this->form = $form;
		$this->page->setTitle('Статистка по заявкам');
		$this->setTemplate('default');
	}

	public function category_marketingAction() {
		$form = new ReportForm();
		$form
			->addElement('site_id')
			->addElement('created')//->addElement('country_id')
		;
		$this->reportFormSubmit($form, 'categoryMarketing');

		$this->form = $form;
		$this->page->setTitle('Прибыль и Убытки');
		$this->setTemplate('default');
	}

	public function request_8Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id');
		$this->reportFormSubmit($form, 'request8');

		$this->form = $form;
		$this->page->setTitle('Выгрузка базы мастеров');
		$this->setTemplate('default');
	}

	public function request_9Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id');
		$this->reportFormSubmit($form, 'request9');

		$this->form = $form;
		$this->page->setTitle('Заявки без отклика');
		$this->setTemplate('default');
	}

	public function request_10Action() {
		$form = new ReportForm();
		$form
			->addElement('created')
			->addElement('site_id')
			->addElement('country_id');
		$this->reportFormSubmit($form, 'request9');

		$this->form = $form;
		$this->page->setTitle('Заявки без отклика');
		$this->setTemplate('default');
	}

	public function previewAction() {
		$request = $this->getRequest();
		$filename = TEMP_PATH . DIRECTORY_SEPARATOR . $request->getQuery('tempnam');
		$content = '';
		if (file_exists($filename)) {
			$i = 0;
			$h = fopen($filename, 'r');
			while ($i++ < 10 && $r = fread($h, 1024)) {
				$content .= $r;
			}
			fclose($h);
		} else {

		}
		$this->layout = false;
		$this->content = $content;
	}

	public function mailAction() {
		$request = $this->getRequest();
		$filename = TEMP_PATH . DIRECTORY_SEPARATOR . $request->getQuery('tempnam');

		$sender = new MailSender();
		foreach (AppConfig::get('mail.sender') as $k => $v) {
			$sender->$k = $v;
		}
		$sender->AddAddress($request->getQuery('email'));
		$sender->Subject = 'Отчет';
		$sender->AddAttachment($filename, 'report.csv', 'base64', 'text/csv');
		//$sender->SMTPDebug = 4;
		$sender->MsgHTML('report');
		if ($sender->Send())
			die('ok ');
		die('false');
	}

	public function fileAction() {
		$request = $this->getRequest();
		$filename = TEMP_PATH . DIRECTORY_SEPARATOR . $request->getQuery('tempnam');
		$out = ContentFactory::fromFile($filename);
		$out->setFilename($request->getQuery('filename'));
		$out->setContentType('text/csv');
		$out->out();
	}

	private function reportFormSubmit($form, $action) {
		if ($form->submit()) {
			$data = $form->getData();
			$portal = Api::factory('Portal');
			$portal->findById($data['portal_id']);
			$response = $portal->getApi('Report')->report($action, $data);
			//var_dump($response);
			if ($response->hasException()) {
				$exception = $response->getException();
				return jsonResponse([
					'exception' => $exception->getMessage(),
					'trace' => $exception->getTraceAsString()
				]);
			} else if ($response->filename) {
				//$out = ContentFactory::fromFile($response->tempnam);
				//$out->setFilename($response->filename);
				//$out->setContentType('text/csv');
				//$out->out();
				jsonResponse([
					'filename' => $response->filename,
					'tempnam' => basename($response->tempnam),
					'filesize' => filesize($response->tempnam)
				]);
			} else {
				jsonResponse($response->getResult());
			}
		}
	}

}