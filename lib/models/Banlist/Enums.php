<?php
abstract class BANLIST_TYPE extends Enum{
	const EMAIL = 1;
	const IP = 2;
}
abstract class BANLIST_REASON extends Enum{
	const SPAM = 1;
	const BOT = 2;
	const XSS = 3;
	const MORON = 4;
}
abstract class BANLIST_LOG extends Enum{
	const FORM_TRAP = 1;
	const REGISTRATION_EMAIL = 2;
}