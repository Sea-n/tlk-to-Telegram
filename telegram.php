<?php

if ($TG->ChatID > 0) {
	$TG->sendMsg([
		'text' => "*401 Unauthorized*\n\nAuthorization Required.", // 我也沒權限 OwO
		'parse_mode' => 'Markdown'
	]);
}

$tlks = [
	-1001112298671 => 8009521, # AIS3
	-1001054375580 => 87446 # test
];
$tlk = $tlks[$TG->ChatID] ?? -1; # 抓取對應的 tlk ID

if ($tlk < 0) # 假如找不到
	exit; # 就離開呀 廢話

if (($TG->data['message']['reply_to_message']['from']['username'] ?? '') == 'AIS3bot') { # Reply to bot
	$txt = $TG->data['message']['text'] ?? '';
	$username = $TG->data['message']['from']['username'];
} else if (strpos($TG->data['message']['text'] ?? '', 'AIS3bot') !== false) { # Reply with @bot
	$txt = $TG->data['message']['reply_to_message']['text'] ?? '';
	$username = $TG->data['message']['reply_to_message']['from']['username'] ?? 'ais3_2018';
} else
	$txt == '';

if ($txt == '')
	exit;

$cookie = "/tmp/tlk-cookie-$username-" . date("m-d"); # 不知道多久會過期，每日更新

if (!file_exists($cookie)) {
	touch($cookie);

	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => 'https://tlk.io/api/participant',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_COOKIEJAR => $cookie,
		CURLOPT_HEADER => true,
		CURLOPT_POSTFIELDS => "nickname=$username (TG)"
	]);
	$result = curl_exec($curl);
	curl_close($curl);
}


$body = explode("\n", $txt, 10); # 每行分開發，最多切 10 行

foreach ($body as $body) {
	if (empty($body)) # 可能是空行什麼的
		continue; # 就給他跳過吧 :D

	$json = [
		'body' => $body
	];

	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => "https://tlk.io/api/chats/$tlk/messages",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_COOKIEFILE => $cookie,
		CURLOPT_POSTFIELDS => json_encode($json),
		CURLOPT_HTTPHEADER => [
			'Content-Type: application/json',
		]
	]);
	curl_exec($curl);
}
