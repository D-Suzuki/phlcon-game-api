<?php

namespace Logics;

// モバイルペイメントライブラリ
use Payment\PaymentIos     as PaymentIos;
use Payment\PaymentAndroid as PaymentAndroid;

// 結果オブジェクト
use GameObject\Result\Payment\PaymentResult as PaymentResult;

// プレイヤーオブジェクト
use PlayerObject\Product as Product;
use PlayerObject\Asset   as Asset;
use PlayerObject\GitBox  as GiftBox;

Class PaymentLogic
{

    /**
     * ログイン処理
     * @param \PlayerObject\Login $Login
     * @param \PlayerObject\GiftBox $GiftBox
     * @return \GameLogic\Login\Result\LoginResult
     */
    public static function payment(int $player_seq_num, int $product_id, string $receipt, string $signature)
    {
        \AppLogger::startFunc(__METHOD__);
        $PaymentResult  = new PaymentResult($player_seq_num, $product_id, $receipt, $signature);
        $Player         = PlayerObject::getInstance($player_seq_num, Player::class);
        $Jewel          = PlayerObject::getInstance($player_seq_num, Jewel::class);
        $GiftBox        = PlayerObject::getInstance($player_seq_num, GiftBox::class);
        $ProductCounter = PlayerObject::getInstance($player_seq_num, ProductCounter::class);

        try {
            // OSタイプから支払オブジェクト取得
            $Payment = PaymentFactory::getInstance($Player->getPlayerBean()->getOsType());
            $Payment->setTimezone('Asia/Tokyo');
            $Payment->setReceipt($receipt);

            // 検証情報をセット
            if ($os_type == self::OS_TYPE_ANDROID) { // ▼ Androidの場合、下記もセット
                $Payment->setSignature($signature);
                $Payment->setPublicKeyPath($publickeyPath);
            }

            // レシート検証
            $Payment->verify();

            // ▼ 課金成功の場合
            if ($Payment->getPaymentResultCode() == MobilePayment::PAYMENT_RESULT_CODE_COMPLETE) {

                $ProductBean = $Product->getProductBean($product_id);
                $GiftBox->deliver($ProductBean->getReward());

            // ▼ 課金失敗の場合
            } else {



            }
            // 検証履歴追加
            $historySeqNum = $Payment->addVerifyHistory( $userSeqNum );
            ##############################
            # 課金履歴追加               #
            # ▼ 推奨履歴項目            #
            # ・OSタイプ                 #
            # ・ユーザ識別番号           #
            # ・商品ID                   #
            # ・購入商品情報             #
            # ・支払ステータス           #
            # ・検証履歴のシーケンス番号 #
            ##############################
            ##################
            # レスポンス処理 #
            ##################
        } catch( Exception $e ) {
            ################################################
            # エラー通知処理                               #
            # ユーザ識別番号やレシート、署名情報などを通知 #
            ################################################
            // ロールバック処理へ
            throw new Exception( $e );
        }
        \AppLogger::endFunc(__METHOD__);
    }

}
