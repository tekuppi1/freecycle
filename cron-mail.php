<?php
    require_once('../wp-load.php');

    $to = array("stitow14@gmail.com", "texchange.ag@gmail.com");
    $subject = "【TexChange日報】" . date("n月j日");

    $giveme_yesterday = get_giveme_log([
                            "count"=>true,
                            "period_from"=>date_i18n('Y-m-d 00:00:00', strtotime( "- 1 day")),
                            "period_to"=>date_i18n('Y-m-d 23:59:59')
                            ]);

    $giveme_state = get_trade_log([
                            "count"=>true,
                            "state"=>"giveme",
                            "period_from"=>date_i18n('Y-m-d 00:00:00', strtotime("- 1 month")),
                            "period_to"=>date_i18n('Y-m-d 23:59:59')
                            ]);
    $confirmed_state = get_trade_log([
                            "count"=>true,
                            "state"=>"confirmed",
                            "period_from"=>date_i18n('Y-m-d 00:00:00', strtotime("- 1 month")),
                            "period_to"=>date_i18n('Y-m-d 23:59:59')
                            ]);
    $item_passed_state = get_trade_log([
                            "count"=>true,
                            "state"=>"item_passed",
                            "period_from"=>date_i18n('Y-m-d 00:00:00', strtotime("- 1 month")),
                            "period_to"=>date_i18n('Y-m-d 23:59:59')
                            ]);

    $new_register_yesterday = get_new_register_log([
                                "count"=>true,
                                "period_from"=>date_i18n('Y-m-d 00:00:00', strtotime( "- 1 day")),
                                "period_to"=>date_i18n('Y-m-d 23:59:59')
                                ]);

    $new_post_yesterday = get_posts_log([
                            "count"=>true,
                            "period_from"=>date_i18n('Y-m-d 00:00:00', strtotime( "- 1 day")),
                            "period_to"=>date_i18n('Y-m-d 23:59:59')
                            ]);

    $message =  "【昨日の情報】\n" .
                "・くださいの数 => " .$giveme_yesterday ."件\n" .
                "\n" .
                "・新規登録者数 => " . $new_register_yesterday . "人\n" .
                "\n" .
                "・新規商品出品 => " . $new_post_yesterday . "件\n" .
                "\n\n" .
                "【商品状態(1ヶ月内)】\n" .
                "・ください済 => " . $giveme_state . "件\n" .
                "\n" .
                "・取引相手確定済 =>" . $confirmed_state . "件\n" .
                "\n" .
                "・商品受渡済 =>" . $item_passed_state . "件\n" .
                "\n\n" .
                "http://texchg.com" ;                ;


    wp_mail($to, $subject, $message);
