<?php
/**
 * Обновление индексв мастеров для сортировки выдачи проектов
 */
$i = 1;
$q = Db::from('users', 'id')
	->where('role=' . USER_ROLE::MASTER)
	->order('RAND()')
	->query();
while ($r = $q->fetch()) {
	Db::query('UPDATE users SET `index`=' . $i++ . ' WHERE id=' . $r['id']);
}