<?php
abstract class MAIL_TYPE extends Enum{
	//const LAYOUT = 0;
	const CUSTOMER_CLICK_LOG = 1;//click_log_user
	const CUSTOMER_CALLBACK = 2;
	const CUSTOMER_REGISTRATION_CONFIRM = 3;
	const CUSTOMER_REGISTRATION_SUCCESS = 4;
	
	const FORM_FEEDBACK = 5;//feedback_user
	const PASSWORD_RECOVERY = 6;
	
	const MASTER_CALLBACK = 7;
	const MASTER_REGISTRATION_SUCCESS = 8;
	
	const ADMIN_CUSTOMER_REGISTRATION = 9;
	const ADMIN_MASTER_CALLBACK = 10;
	const ADMIN_REVIEW = 11;
	const ADMIN_FEEDBACK = 12;//feedback
	const ADMIN_CLICK_LOG = 13;//CLICK_LOG_ADMINISTRATOR
	const ADMIN_PROJECT_COMPLAIN = 14;//PROJECT_COMPLAIN
	const ADMIN_PROJECT_CREATED = 15;//PROJECT_CREATED
	const ADMIN_PROJECT_DELETED = 22;//PROJECT_CREATED
	const ADMIN_PROMOTION_CREATED = 16;//PROMOTION_CREATED
	const ADMIN_REVIEW_CREATED = 17;//REVIEW_CREATED
	const ADMIN_USER_CREATED = 18;//USER_CREATED
	const ADMIN_USER_IMAGE_CHANGED = 19;//USER_IMAGE_CHANGED
	const ADMIN_USER_REGISTRATED = 20;//USER_REGISTRATED
	const ADMIN_ORDER = 21;
	const ADMIN_CRON_REPORT = 23;
}