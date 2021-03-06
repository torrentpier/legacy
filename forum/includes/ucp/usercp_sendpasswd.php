<?php

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

if ($bb_cfg['emailer_disabled'])
{
	bb_die('Извините, эта функция временно не работает');
}

if ( isset($_POST['submit']) )
{
	$username = ( !empty($_POST['username']) ) ? phpbb_clean_username($_POST['username']) : '';
	$email = ( !empty($_POST['email']) ) ? trim(strip_tags(htmlspecialchars($_POST['email']))) : '';

	$sql = "SELECT *
		FROM " . USERS_TABLE . "
		WHERE user_email = '" . str_replace("\'", "''", $email) . "'
			AND username = '" . str_replace("\'", "''", $username) . "'";
	if ( $result = $db->sql_query($sql) )
	{
		if ( $row = $db->sql_fetchrow($result) )
		{
			if (!$row['user_active'])
			{
				bb_die($lang['NO_SEND_ACCOUNT_INACTIVE']);
			}
			if (in_array($row['user_level'], array(MOD, ADMIN)))
			{
				bb_die($lang['NO_SEND_ACCOUNT']);
			}

			$username = $row['username'];
			$user_id = $row['user_id'];

			$user_actkey = make_rand_str(12);
			$user_password = make_rand_str(8);

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_newpasswd = '" . md5($user_password) . "', user_actkey = '$user_actkey'
				WHERE user_id = " . $row['user_id'];
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update new password information', '', __LINE__, __FILE__, $sql);
			}

			include(INC_DIR . 'emailer.class.php');
			$emailer = new emailer($board_config['smtp_delivery']);

			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);

			$emailer->use_template('user_activate_passwd', $row['user_lang']);
			$emailer->email_address($row['user_email']);
			$emailer->set_subject($lang['NEW_PASSWORD_ACTIVATION']);

			$emailer->assign_vars(array(
				'SITENAME' => $board_config['sitename'],
				'USERNAME' => $username,
				'PASSWORD' => $user_password,
				'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '',

				'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
			);
			$emailer->send();
			$emailer->reset();

			$message = $lang['PASSWORD_UPDATED'] . '<br /><br />' . sprintf($lang['CLICK_RETURN_INDEX'],  '<a href="' . append_sid("index.php") . '">', '</a>');

			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			message_die(GENERAL_MESSAGE, $lang['NO_EMAIL_MATCH']);
		}
	}
	else
	{
		message_die(GENERAL_ERROR, 'Could not obtain user information for sendpassword', '', __LINE__, __FILE__, $sql);
	}
}
else
{
	$username = '';
	$email = '';
}

$template->assign_vars(array(
	'USERNAME' => $username,
	'EMAIL' => $email,

	'S_HIDDEN_FIELDS' => '',
	'S_PROFILE_ACTION' => append_sid("profile.php?mode=sendpassword"))
);

print_page('usercp_sendpasswd.tpl');
