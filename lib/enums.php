<?php
abstract class USER_STATUS extends Enum {
	const UNCONFIRMED = 0;
	const CONFIRMED = 1;
	const BLOCKED = 2;
}
abstract class FILE_TYPE extends Enum {
	const NONE = 0;
	const USER_IMAGE = 2;
}