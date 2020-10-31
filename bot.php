<?php
date_default_timezone_set('Asia/Baghdad');
if(!file_exists('config.json')){
	$token = readline('Enter Token: ');
	$id = readline('Enter Id: ');
	file_put_contents('config.json', json_encode(['id'=>$id,'token'=>$token]));
	
} else {
		  $config = json_decode(file_get_contents('config.json'),1);
	$token = $config['token'];
	$id = $config['id'];
}

if(!file_exists('accounts.json')){
    file_put_contents('accounts.json',json_encode([]));
}
include 'index.php';
try {
	$callback = function ($update, $bot) {
		global $id;
		if($update != null){
		  $config = json_decode(file_get_contents('config.json'),1);
		  $config['filter'] = $config['filter'] != null ? $config['filter'] : 1;
      $accounts = json_decode(file_get_contents('accounts.json'),1);
			if(isset($update->message)){
				$message = $update->message;
				$chatId = $message->chat->id;
				$text = $message->text;
				if($chatId == $id){
					if($text == '/start'){
              $bot->sendMessage([
                  'chat_id'=>$chatId,
                  'text'=>"Hi BROK in Your Tool\nThis Tool To Check Available Accounts in INSTAGRAM \n \n BY @x_BRK",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'Add Accounts','callback_data'=>'login']],
                          [['text'=>'Grab List','callback_data'=>'grabber']],
                          [['text'=>'Start Check','callback_data'=>'run'],['text'=>'Stop Check','callback_data'=>'stop']],
                          
                          [['text'=>'Status Tool','callback_data'=>'status']],
                          [['text'=>'BRoK','url'=>'T.me/x_BRK']],
                      ]
                  ])
              ]);   
          }
elseif($text != null){
          	if($config['mode'] != null){
          		$mode = $config['mode'];
          		if($mode == 'addL'){
          			$ig = new ig(['file'=>'','account'=>['useragent'=>'Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)']]);
          			list($user,$pass) = explode(':',$text);
          			list($headers,$body) = $ig->login($user,$pass);
          			// echo $body;
          			$body = json_decode($body);
          			if(isset($body->message)){
          				if($body->message == 'challenge_required'){
          					$bot->sendMessage([
          							'chat_id'=>$chatId,
          							'text'=>"This Account Closed BY instagram."
          					]);
          				} else {
          					$bot->sendMessage([
          							'chat_id'=>$chatId,
          							'text'=>"Username or Pass is Error"
          					]);
          				}
          			} elseif(isset($body->logged_in_user)) {
          				$body = $body->logged_in_user;
          				preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
								  $CookieStr = "";
								  foreach($matches[1] as $item) {
								      $CookieStr .= $item."; ";
								  }
          				$account = ['cookies'=>$CookieStr,'useragent'=>'Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)'];
    
          				$accounts[$text] = $account;
          				file_put_contents('accounts.json', json_encode($accounts));
          				$mid = $config['mid'];
          				$bot->sendMessage([
          							'chat_id'=>$chatId,
          							'text'=>"Login Done BY This Account
User - {$user} ",
												'reply_to_message_id'=>$mid		
          					]);
          				$keyboard = ['inline_keyboard'=>[
										[['text'=>"Add New Account",'callback_data'=>'addL']]
									]];
		              foreach ($accounts as $account => $v) {
		                  $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'ddd'],['text'=>"Log Out",'callback_data'=>'del&'.$account]];
		              }
		              $keyboard['inline_keyboard'][] = [['text'=>'Back','callback_data'=>'back']];
		              $bot->editMessageText([
		                  'chat_id'=>$chatId,
		                  'message_id'=>$mid,
		                  'text'=>"Mange The Accounts.",
		                  'reply_markup'=>json_encode($keyboard)
		              ]);
		              $config['mode'] = null;
		              $config['mid'] = null;
		              file_put_contents('config.json', json_encode($config));
          			}
          		}  elseif($mode == 'selectFollowers'){
          		  if(is_numeric($text)){
          		    bot('sendMessage',[
          		        'chat_id'=>$chatId,
          		        'text'=>"Done",
          		        'reply_to_message_id'=>$config['mid']
          		    ]);
          		    $config['filter'] = $text;
          		    $bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                      'text'=>"Hi BROK in Your Tool\n This Tool To Grab & Check Availble accounts in insta \n it Check All Domains \n BY @x_BRK",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'Add Accounts','callback_data'=>'login']],
                          [['text'=>'Grab List','callback_data'=>'grabber']],
                          [['text'=>'Start Check','callback_data'=>'run'],['text'=>'Stop Check','callback_data'=>'stop']],
                          
                          [['text'=>'Status Tool','callback_data'=>'status']],
                      ]
                      ])
                  ]);
          		    $config['mode'] = null;
		              $config['mid'] = null;
		              file_put_contents('config.json', json_encode($config));
          		  } else {
          		    bot('sendMessage',[
          		        'chat_id'=>$chatId,
          		        'text'=>'Please Send Me only Number'
          		    ]);
          		  }
          		} else {
          		  switch($config['mode']){
          		    case 'search': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php search.php');
          		      break;
          		      case 'followers': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php followers.php');
          		      break;
          		      case 'following': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php following.php');
          		      break;
          		      case 'hashtag': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php hashtag.php');
          		      break;
          		  }
          		}
          	}
          }
				} else {
					$bot->sendMessage([
							'chat_id'=>$chatId,
							'text'=>"Hi in BROK Tool To Grab Available Accounts instagram",
							'reply_markup'=>json_encode([
                  'inline_keyboard'=>[
                      [['text'=>'To BuY The Tool','url'=>'t.me/x_BRK']]
                  ]
							])
					]);
				}
			} elseif(isset($update->callback_query)) {
          $chatId = $update->callback_query->message->chat->id;
          $mid = $update->callback_query->message->message_id;
          $data = $update->callback_query->data;
          echo $data;
          if($data == 'login'){
              
        	$keyboard = ['inline_keyboard'=>[
							[['text'=>"Add New Account",'callback_data'=>'addL']]
						]];
            foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'ddd'],['text'=>"Log Out",'callback_data'=>'del&'.$account]];
            }
            $keyboard['inline_keyboard'][] = [['text'=>'Back','callback_data'=>'back']];
            $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Mange The Accounts",
                'reply_markup'=>json_encode($keyboard)
            ]);
          } elseif($data == 'addL'){
          	
          	$config['mode'] = 'addL';
          	$config['mid'] = $mid;
          	file_put_contents('config.json', json_encode($config));
          	$bot->sendMessage([
          			'chat_id'=>$chatId,
          			'text'=>"Send Me The Accoutn Like This \n User:Pass",
          			'parse_mode'=>'markdown'
          	]);
          } elseif($data == 'grabber'){
            
            $for = $config['for'] != null ? $config['for'] : 'Select Account';
            $count = count(explode("\n", file_get_contents($for)));
            foreach ($accounts as $account => $v) {
            $brok = explode(':',$account)[0];
           } 
            $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Grab List \n Users Count - $count \n Grab - $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'Grab From Search','callback_data'=>'search']],
                        [['text'=>'Grab From Hashtag (#) ','callback_data'=>'hashtag']],
                        [['text'=>'Account Followers','callback_data'=>'followers'],['text'=>"Account Following",'callback_data'=>'following']],
                        
                        [['text'=>"Select Account - $brok" ,'callback_data'=>'for']],
                        [['text'=>'New Grab','callback_data'=>'newList'],['text'=>'On Old Grab','callback_data'=>'append']],
                        [['text'=>'Back','callback_data'=>'back']],
                    ]
                ])
            ]);
          } elseif($data == 'search'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"Send Me one Word or Many Words"
            ]);
            $config['mode'] = 'search';
            file_put_contents('config.json', json_encode($config));
          } 
elseif($data == 'followers'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"Send Me one User on Many Users"
            ]);
            $config['mode'] = 'followers';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'explore'){
            exec('screen -dmS gr php explore.php');
          } elseif($data == 'following'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"Send Me one User or Many Users"
            ]);
            $config['mode'] = 'following';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'hashtag'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"Send Me one Hashtag only"
            ]);
            $config['mode'] = 'hashtag';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'newList'){
            file_put_contents('a','new');
            $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"Selected Grab To New List",
							'show_alert'=>1
						]);
          } elseif($data == 'append'){ 
            file_put_contents('a', 'ap');
            $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"Selected Grab on old List",
							'show_alert'=>1
						]);
						
          } elseif($data == 'for'){
            if(!empty($accounts)){
            $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'forg&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"Select Account",
                  'reply_markup'=>json_encode($keyboard)
              ]);
            } else {
              $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"You Do Not Add Any Account",
							'show_alert'=>1
						]);
            }
          }  elseif($data == 'selectFollowers'){
            bot('sendMessage',[
                'chat_id'=>$chatId,
                'text'=>'Send Me Following Count'  
            ]);
            $config['mode'] = 'selectFollowers';
          	$config['mid'] = $mid;
          	file_put_contents('config.json', json_encode($config));
          } elseif($data == 'run'){
            if(!empty($accounts)){
            $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'start&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"Select Account",
                  'reply_markup'=>json_encode($keyboard)
              ]);
            } else {
              $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"You Do Not Add Any Account",
							'show_alert'=>1
						]);
            }
          }elseif($data == 'stop'){
            if(!empty($accounts)){
            $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'stop&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"Select Account",
                  'reply_markup'=>json_encode($keyboard)
              ]);
            } else {
              $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"You Do Not Add Any Account",
							'show_alert'=>1
						]);
            }
          } elseif($data == 'runall'){
            foreach ($accounts as $account => $v) {
            file_put_contents('screen', $account);
              exec('screen -dmS '.explode(':',$account)[0].' php start.php');
              $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"Checking Started - ".explode(':',$account)[0].'`',
                'parse_mode'=>'markdown'
              ]);
              sleep(2);
            }
          } elseif($data == 'tran'){
            if(!empty($accounts)){
            $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'tranfrom&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"Form Account - ",
                  'reply_markup'=>json_encode($keyboard)
              ]);
            } else {
              $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"You Do Not Add Any Account",
							'show_alert'=>1
						]);
            }
          }elseif($data == 'stopgr'){
            shell_exec('screen -S gr -X quit');
            $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"Grab Stoped",
							'show_alert'=>1
						]);
						$for = $config['for'] != null ? $config['for'] : 'Select Account';
            $count = count(explode("\n", file_get_contents($for)));
						foreach ($accounts as $account => $v) {
            $brok = explode(':',$account)[0];
           } 
$bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                 'text'=>"Grab List \n Users Count - $count \n Grab - $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'Grab From Search','callback_data'=>'search']],
                        [['text'=>'Grab From Hashtag (#) ','callback_data'=>'hashtag']],
                        [['text'=>'Account Followers','callback_data'=>'followers'],['text'=>"Account Following",'callback_data'=>'following']],
                        
                        [['text'=>"Select Account - $brok",'callback_data'=>'for']],
                        [['text'=>'New Grab','callback_data'=>'newList'],['text'=>'On Old Grab','callback_data'=>'append']],
                        [['text'=>'Back','callback_data'=>'back']],
                    ]
                ])
            ]);
          } elseif($data == 'status'){
					$status = '';
					foreach($accounts as $account => $ac){
						$c = explode(':', $account)[0];
						$x = exec('screen -S '.$c.' -Q select . ; echo $?');
						if($x == '0'){
				        $status .= "User - ".explode(':',$account)[0]." - Running\n" ;
				    } else {
				        $status .= "User -" .explode(':',$account)[0]." - Stoped\n" ;
				    }
					}
					$bot->sendMessage([
							'chat_id'=>$chatId,
							'text'=>"Status All Accounts \n $status",
							'parse_mode'=>'markdown'
						]);
				} elseif($data == 'back'){
          	$bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                      'text'=>"Hi BROK in Your Tool\n This Tool To Grab & Check Availble accounts in insta \n it Check All Domains \n BY @x_BRK",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'Add Accounts','callback_data'=>'login']],
                          [['text'=>'Grab List','callback_data'=>'grabber']],
                          [['text'=>'Start Check','callback_data'=>'run'],['text'=>'Stop Check','callback_data'=>'stop']],
                          
                          [['text'=>'Status Tool','callback_data'=>'status']],
                      ]
                      ])
                  ]);
          } else {
          	$data = explode('&',$data);
          	if($data[0] == 'del'){
          		
          		unset($accounts[$data[1]]);
          		file_put_contents('accounts.json', json_encode($accounts));
              $keyboard = ['inline_keyboard'=>[
							[['text'=>"Add New Account",'callback_data'=>'addL']]
						]];
            foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'ddd'],['text'=>"Log Out",'callback_data'=>'del&'.$account]];
            }
            $keyboard['inline_keyboard'][] = [['text'=>'Back','callback_data'=>'back']];
            $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Mange The Accounts.",
                'reply_markup'=>json_encode($keyboard)
            ]);
          	} elseif($data[0] == 'forg'){
          	  $config['for'] = $data[1];
          	  file_put_contents('config.json',json_encode($config));
              $for = $config['for'] != null ? $config['for'] : 'Select Account';
             foreach ($accounts as $account => $v) {
            $brok = explode(':',$account)[0];
           } 
 $count = count(file_get_contents($for));
              $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
               'text'=>"Grab List \n Users Count - $count \n Grab - $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'Grab From Search','callback_data'=>'search']],
                        [['text'=>'Grab From Hashtag (#) ','callback_data'=>'hashtag']],
                        [['text'=>'Account Followers','callback_data'=>'followers'],['text'=>"Account Following",'callback_data'=>'following']],
                        
                        [['text'=>"Select Account - $brok",'callback_data'=>'for']],
                        [['text'=>'New Grab','callback_data'=>'newList'],['text'=>'On Old Grab','callback_data'=>'append']],
                        [['text'=>'Back','callback_data'=>'back']],
                    ]
                ])
            ]);
          	}  elseif($data[0] == 'tranfrom'){
          	  $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>'User - '.explode(':',$account)[0].' ','callback_data'=>'tranto&'.$account]];
              }
              file_put_contents('tranfrom', $data[1]);
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"To Account - ",
                  'reply_markup'=>json_encode($keyboard)
              ]);
          	} elseif($data[0] == 'tranto'){
          	  $from = file_get_contents('tranfrom');
          	  if(file_exists($from)){
          	    $list = file_get_contents($from);
          	    if(!empty($list)){
          	      file_put_contents($data[1],$list);
          	      bot('sendMessage',[
          	       'chat_id'=>$chatId,
          	       'text'=>"Done Move List $from To - ".$data[1]."*",
          	       'parse_mode'=>'markdown'
          	     ]);
          	     $bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                     'text'=>"Hi BROK in Your Tool\n This Tool To Grab & Check Availble accounts in insta \n it Check All Domains \n BY @x_BRK",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'Add Accounts','callback_data'=>'login']],
                          [['text'=>'Grab List','callback_data'=>'grabber']],
                          [['text'=>'Start Check','callback_data'=>'run'],['text'=>'Stop Check','callback_data'=>'stop']],
                          
                          [['text'=>'Status Tool','callback_data'=>'status']],
                      ]
                      ])
                  ]);
          	    } else {
          	      bot('sendMessage',[
          	       'chat_id'=>$chatId,
          	       'text'=>"This Account $from Does Not has List To Move it",
          	       'parse_mode'=>'markdown'
          	     ]);
          	    }
           	  } else { 
          	    bot('sendMessage',[
          	       'chat_id'=>$chatId,
          	       'text'=>"This Account $from Does Not has List To Move it",
          	       'parse_mode'=>'markdown'
          	     ]);
          	  }
        	  }elseif($data[0] == 'start'){
          	  file_put_contents('screen', $data[1]);
          	  $bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                     'text'=>"Hi BROK in Your Tool\n This Tool To Grab & Check Availble accounts in insta \n it Check All Domains \n BY @x_BRK",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'Add Accounts','callback_data'=>'login']],
                          [['text'=>'Grab List','callback_data'=>'grabber']],
                          [['text'=>'Start Check','callback_data'=>'run'],['text'=>'Stop Check','callback_data'=>'stop']],
                          
                          [['text'=>'Status Tool','callback_data'=>'status']],
                      ]
                      ])
                  ]);
              exec('screen -dmS '.explode(':',$data[1])[0].' php start.php');
              $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"Cheking Started - ".explode(':',$data[1])[0].'`'
              ]);
          	} elseif($data[0] == 'stop'){
          	  $bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                     'text'=>"Hi BROK in Your Tool\n This Tool To Grab & Check Availble accounts in insta \n it Check All Domains \n BY @x_BRK",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'Add Accounts','callback_data'=>'login']],
                          [['text'=>'Grab List','callback_data'=>'grabber']],
                          [['text'=>'Start Check','callback_data'=>'run'],['text'=>'Stop Check','callback_data'=>'stop']],
                          
                          [['text'=>'Status Tool','callback_data'=>'status']],
                      ]
                      ])
                  ]);
              exec('screen -S '.explode(':',$data[1])[0].' -X quit');
          	}
          }
			}
		}
	};
	$bot = new EzTG(array('throw_telegram_errors'=>false,'token' => $token, 'callback' => $callback));
} catch(Exception $e){
	echo $e->getMessage().PHP_EOL;
	sleep(1);
}