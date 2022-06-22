<?php

class PortalController extends InitController{

	protected $_routes = [
		['/data/:id/', [], ['id' => '\d+']],
		['/translation/:id/', [], ['id' => '\d+']]
	];
	
	public function dataAction() {
		$api = $this->get('api');
		jsonResponse(['data' => $api->getApi('Reference')->getData()]);
	}
	
	/*public function sitesAction() {
		jsonResponse(['data' => $this->getApi()->getApi('Reference')->getSites()]);
	}
	
	public function categoriesAction() {
		$data = $this->getRequest()->getQuery();
		jsonResponse(['data' => $this->getApi()->getApi('Reference')->getCategories($data)]);
	}
	
	public function countriesAction() {
		$data = $this->getRequest()->getQuery();
		jsonResponse(['data' => $this->getApi()->getApi('Reference')->getCountries($data)]);
	}*/
	
	public function translationAction() {
		$api = $this->get('api');
		//jsonResponse(['data' => $api->getApi('Reference')->getTranslation()]);
	}
	
	public function getApi() {
		return Api::factory('Portal');
	}

}