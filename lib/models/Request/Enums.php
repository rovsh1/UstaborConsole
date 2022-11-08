<?php
abstract class REQUEST_STATUS extends Enum{
	const CREATED = 0;
	const PROCESSING = 1;
	const PROCESSED = 2;
	const COMPLETED = 3;
	const ARCHIVE = 4;
	const DELETED = 5;
}
abstract class REQUEST_RATING_TYPE extends Enum{
	const QUALITY = 1;
	const SPEED = 2;
	const DETAIL = 3;
}