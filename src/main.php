<?php

require_once ('functions/botTelegram.php');
require_once ('functions/escapeMarkdownV2.php');
require_once('./tiktok/Class.php');
require_once('./functions/membersManager.php');

$update = file_get_contents('php://input');

$updateDecode = json_decode($update, true);

if($update)
{

    if( $updateDecode['message'] )
    {

        $update_id = $updateDecode['update_id'];

        $message = $updateDecode['message'];
        $messageId = $message['message_id'];

        $from = $message['from'];
        $userId = $from['id'];
        $userName = escapeMarkdownV2($from['first_name']);
        $userUsername = $from['username'];

        $chat = $message['chat'];
        $chatId = $chat['id'];
        $chatName = escapeMarkdownV2($chat['first_name']);
        $chatUsername = $chat['username'];
        $chatType = $chat['type'];
        
        $date = $message['date'];
        $text = $message['text'];

    }

    if( $updateDecode['callback_query'] )
    {

        $update_id = $updateDecode['update_id'];

        $callbackQuery = $updateDecode['callback_query'];
        $callbackQueryId = $callbackQuery['id'];

        $message = $callbackQuery['message'];
        $messageId = $message['message_id'];

        $from = $callbackQuery['from'];
        $userId = $from['id'];
        $userName = escapeMarkdownV2($from['first_name']);
        $userUsername = $from['username'];

        $chat = $message['chat'];
        $chatId = $chat['id'];
        $chatName = escapeMarkdownV2($chat['first_name']);
        $chatUsername = $chat['username'];
        $chatType = $chat['type'];
        
        $date = $message['date'];
        $data = $callbackQuery['data'];  
        
    }

}


if($text == '/start') 
{
    bot('sendMessage',[
        'chat_id' => $chatId,
        'parse_mode' => 'markdownv2',
        'relpy_to_message_id' => $messageId,
        'text' => "*Hello [$userName](tg://user?id=$userId),*\n\nThis bot for *CLAIM TIKTOK USERS* \n\n `you can claim any user for free \.`",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Get new user',
                        'callback_data' => 'claim'
                    ]
                    ],
                    [
                        [
                            'text' => 'Get sessionId',
                            'callback_data' => 'sessionId'
                        ]
                    ]
            ]
        ])
    ]);

}

if($data == 'back') 
{
    bot('editMessageText',[
        'chat_id' => $chatId,
        'parse_mode' => 'markdownv2',
        'message_id' => $messageId,
        'text' => "*Hello [$userName](tg://user?id=$userId),*\n\nThis bot for *CLAIM TIKTOK USERS* \n\n `you can claim any user for free \.`",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Get new user',
                        'callback_data' => 'claim'
                    ]
                    ],
                    [
                        [
                            'text' => 'Get sessionId',
                            'callback_data' => 'sessionId'
                        ]
                    ]
            ]
        ])
    ]);

}

if($data == 'claim')
{
    bot('editMessageText',[
        'chat_id' => $chatId,
        'message_id' => $messageId,
        'parse_mode' => 'markdownv2',
        'text' => "*Hello [$userName](tg://user?id=$userId),*\n\n`Pls send you sessionId now \.`",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Back',
                        'callback_data' => 'back'
                    ]
                ]
            ]
        ]),
    ]);

    file_put_contents($userId.'_claim.json',json_encode([
        'step' => 1
    ]));
}

if($text && $text != '/start' && json_decode(file_get_contents($userId.'_claim.json'), true)['step'] == 1)
{
    $msgId = bot('sendMessage',[
        'chat_id' => $chatId,
        'reply_to_message_id' => $messageId,
        'text' => "⌛️",
    ])->result->message_id;

    $device_id = strval(mt_rand(777777788, 999999999999));
    $iid = strval(mt_rand(777777788, 999999999999));

    $user = get_profile($text, $device_id, $iid);

    bot('deleteMessage',[
        'chat_id' => $chatId,
        'message_id' => $msgId
    ]);

    if ($user !== null) {

        bot('sendMessage',[
            'chat_id' => $chatId,
            'parse_mode' => 'markdownv2',
            'reply_to_message_id' => $messageId,
            'text' => "Your current TikTok username is: `$user`\n\n*Enter the new username you wish to set*",
        ]);

        file_put_contents($userId.'_claim.json',json_encode([
            'step' => 2,
            'sessionId' => $text,
            'device_id' => $device_id,
            'iid' => $iid,
            'user' => $user
        ]));

    } else {

        bot('sendMessage',[
            'chat_id' => $chatId,
            'parse_mode' => 'markdownv2',
            'reply_to_message_id' => $messageId,
            'text' => "*This sessionId not correct ⚠️*\n\n`pls try again \.`",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Back',
                            'callback_data' => 'back'
                        ]
                    ]
                ]
            ])
        ]);

    }

    exit;

}

if($text && $text != '/start' && json_decode(file_get_contents($userId.'_claim.json'), true)['step'] == 2)
{

    $msgId = bot('sendMessage',[
        'chat_id' => $chatId,
        'reply_to_message_id' => $messageId,
        'text' => "⌛️",
    ])->result->message_id;

    $json = json_decode(file_get_contents($userId.'_claim.json'), true);

    $result = change_username($json['sessionId'], $json['device_id'], $json['iid'], $json['user'], $text);

    bot('deleteMessage',[
        'chat_id' => $chatId,
        'message_id' => $msgId
    ]);

    if($result == 'Username change successful.'){
        bot('sendMessage',[
            'chat_id' => $chatId,
            'parse_mode' => 'markdownv2',
            'reply_to_message_id' => $messageId,
            'text' => "This user `$text` Been Claim ✅\n\n> Go To Tiktok And see It\.",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Get new user',
                            'callback_data' => 'claim'
                        ]
                    ],
                    [
                        [
                            'text' => 'Back',
                            'callback_data' => 'back'
                        ]
                    ]
                ]
            ])
        ]);
    } else {
        $error = escapeMarkdownV2(str_replace('Failed to change username: ', '', $result));

        bot('sendMessage',[
            'chat_id' => $chatId,
            'parse_mode' => 'markdownv2',
            'text' => "*error: *```json $error ```"
        ]);
    }
}

if($text && !isMember($userId))
{
    $users = json_decode(file_get_contents('./DB/users.json'), true);
    $countUsers = count($users);

    bot('sendMessage',[
        'chat_id' => 1842794304,
        'parse_mode' => 'markdownv2',
        'text' => "*New User Login To Bot\.*\n\nName: [$userName](tg://user?id=$userId)\niD: `$userId`\nUserName: `$userUserName`\n\nTotal Users: $countUsers",
    ]);

    addNewUser($userId);
}

if($data == 'sessionId')
{
    bot('editMessageText',[
        'chat_id' => $chatId,
        'parse_mode' => 'markdownv2',
        'message_id' => $messageId,
        'text' => "`This option is under *maintenance* at the moment, `\n\n>*try again later\.*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Back',
                        'callback_data' => 'back'
                    ]
                ]
            ]
        ])
    ]);
}