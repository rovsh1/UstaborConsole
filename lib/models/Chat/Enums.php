<?php
abstract class CHAT_USER_ROLE extends Enum{
	const BLOCKED = 0;
	const ADMINISTRATOR = 1;
	const HELPER = 2;
	const OBSERVER = 3;
}
abstract class CHAT_MESSAGE_TYPE extends Enum{
	const TEXT = 0;
	const BUTTONS = 1;
	const RADIO = 2;
}