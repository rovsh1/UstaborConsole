<?php
//TODO add paths separation

use Api\Model\Translation;

class TranslationController extends InitController {

	protected $_routes = [
		['/edit/:id/', [], ['id' => '\d+|new']],
		['/delete/:id/', [], ['id' => '\d+']],
		['/sync/:id/', [], ['id' => '\d+']]
	];

	public function indexAction() {
		//$api = $this->getApi();

		/*$columns = Translation::getColumns();
		$q = Db::query('SELECT * FROM translation_items');
		while ($r = $q->fetch()) {
			foreach ($columns as $n) {
				$text = $r[$n];
				$text = str_replace('FixingList', '%siteName%', $text);
				if ($text !== $r[$n]) {
					Db::update('translation_items', [$n => $text], $r['id']);
				}
			}
			
			//Db::query('DELETE FROM translation_items WHERE name="' . $r['name'] . '" AND id<>' . $r['id']);
		}
		$q = Db::query('SELECT id FROM translation_items');
		while ($r = $q->fetch()) {
			$api->findById($r['id']);
			$api->write();
		}*/

		$this->page->setTitle($this->mainmenu->lang('translation'));
		$this->get('menu')
			->add('add', lang('Add item'))//->add('sync', 'Синхронизация', 'sync/')
		;
	}

	public function searchAction() {
		$api = $this->get('api');
		$request = $this->getRequest();
		$search = $request->getQuery('quicksearch');
		$data = [];
		//$data['path'] = $request->getQuery('path');
		if ($search)
			$data['quicksearch'] = $search;
		else
			$data['empty'] = true;
		$data['deletion_mark'] = 0;
		//$data['path'] = ['site', null];
		$this->layout = false;
		$this->items = $api->select($data);
	}

	public function editAction() {
		$api = $this->get('api');
		$data = $this->getRequest()->getPost();
		$data['path'] = null;
		$api->setData($data);
		return jsonResponse([
			'success' => $api->write(),
			'data' => $api->getData()
		]);
	}

	public function deleteAction() {
		$api = $this->get('api');
		$api->write(['deletion_mark' => true]);
		jsonResponse(['success' => 1]);
	}

	public function syncAction() {
		$api = $this->getApi();

		return jsonResponse(['items' => $api->select()]);
	}

	public function syncOldAction() {
		$api = $this->getApi();
		if ($this->id) {
			$portal = Api::factory('Portal');
			$portal->findById($this->id);

			$import = [
				'inserts' => 0,
				'updates' => 0
			];
			/*$columns = $api->getColumns();
			$referenceApi = $portal->getApi('Reference');
			$portalItems = $referenceApi->getTranslation();
			foreach ($portalItems as $item) {
				if (!$item->path)
					$item->path = 'site';
				if ($api->findByName($item->path, $item->name)) {
					$changed = false;
					foreach ($columns as $n) {
						$v = isset($item->$n) ? $item->$n : null;
						if (!$api->$n && $v) {
							$api->$n = $v;
							$changed = true;
						}
					}
					if ($changed) {
						$api->write();
						$import['updates']++;
					}
				} else {
					$api->setId('new');
					unset($item->id, $item->updated);
					foreach ($item as $k => $v)
						$api->$k = $v;
					$api->write();
					//var_dump($item->name);
					$import['inserts']++;
				}
			}*/
			$syncApi = $portal->getApi('Sync');
			$response = $syncApi->syncTranslation(['items' => $api->select()]);
			//var_dump($response);
			jsonResponse([
				'success' => 1,
				'import' => $import,
				'export' => $response->getResult()
			]);
		} else {
			$form = $this->get('form');
			$form->addElement('portal_id', 'select', [
				'emptyItem' => lang('Portal'),
				'items' => Api::factory('Portal')->select()
			]);
			//$data = [];
			//$data['path'] = ['site', null];
			//$this->items = $api->select($data);

			$this->get('mainmenu')->setCurrent('/translation/');
			$this->page->setTitle('Синхронизация');
		}
	}

	public function testAction() {
		$q = Db::query('SELECT * FROM translation_items2 WHERE path IS NULL OR path="site" OR path="new"');
		while ($r = $q->fetch()) {
			$fr = Db::from('translation_items')
				->where('path IS NULL OR path="site"')
				->where('name=?', $r['name'])
				->query()->fetchRow();
			if ($fr)
				continue;
			unset($r['id'], $r['old_name'], $r['path'], $r['updated']);
			Db::insert('translation_items', $r);
		}
		exit;
	}

	public function getApi() {
		return Api::factory('Translation');
	}

	protected function initAuthUser() {
		if ($this->url->action === 'sync')
			return $this->apiAuth();

		return parent::initAuthUser();
	}

	private function apiAuth() {
		return true;
	}

}