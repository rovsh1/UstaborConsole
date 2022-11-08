<?php
/**
 * Уведомления мастерам
 */
//8741
$master = Api::factory('User\Master');

//Не заполнен аватар
$q = Db::query('SELECT id FROM users'
	. ' WHERE role=' . USER_ROLE::MASTER
	//. ' AND id=7128'
	. ' AND image IS NULL'
	. ' AND NOT EXISTS(SELECT 1 FROM user_notification WHERE user_id=users.id AND type=' . USER_NOTIFICATION::MASTER_IMAGE . ')');
while ($r = $q->fetch()) {
	$master->findById($r['id']);
	$master->notification(USER_NOTIFICATION::MASTER_IMAGE);
}

//Нет ни одного проекта
$q = Db::query('SELECT id,master_id FROM projects'
	. ' WHERE NOT EXISTS(SELECT 1 FROM files WHERE parent_id=projects.id AND type=' . FILE_TYPE::PROJECT_IMAGE . ')'
	//. ' AND master_id=7128'
	. ' AND NOT EXISTS(SELECT 1 FROM user_notification WHERE base_id=projects.id AND type=' . USER_NOTIFICATION::PROJECT_IMAGES . ')');
while ($r = $q->fetch()) {
	$master->findById($r['master_id']);
	$master->notification([
		'type' => USER_NOTIFICATION::PROJECT_IMAGES,
		'base_id' => $r['id']
	]);
}