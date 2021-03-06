<?php

namespace Logics;

use PlayerObject\PlayerObject;
use PlayerObject\Player;
use PlayerObject\Chara;
use GameObject\Result\Chara\AddCharasResult;

Class CharaLogic
{

    /**
     * クライアント用キャラリスト取得
     * @param int $chara_seq_num
	 * @param DataTime $LastAccessTime
	 * @return array
     */
    public static function getCharaListForClient(int $player_seq_num, DateTime $LastAccessTime = null, int $chara_seq_num = null) : array
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$last_access_time' => $LastAccessTime, '$chara_seq_num' => $chara_seq_num]);

        /* @var $Chara \PlayerObject\Chara */
        $Chara = PlayerObject::getInstance($player_seq_num, Chara::class);

        $chara_list_for_client = [];
        if (is_null($Chara->getCharaBeanList()) === false) {
            foreach ($Chara->getCharaBeanList() as $CharaBean) {
                // 差分取得
                if (is_null($LastAccessTime) === false && $CharaBean->getUpdatedAt() < $LastAccessTime) {
                    continue;
                }
                // キャラ指定
                if (is_null($chara_seq_num) === false && $CharaBean->getCharaSeqNum() != $chara_seq_num) {
                    continue;
                }
                $chara_list_for_client[] = [
                    'chara_seq_num' => (int) $CharaBean->getCharaSeqNum(),
                    'chara_id'      => (int) $CharaBean->getCharaId(),
                    'exp'           => (int) $CharaBean->getExp(),
                    'level'         => (int) $CharaBean->getLevel(),
                    'status'        => (int) $CharaBean->getStatus(),
                ];
            }
        }

        \AppLogger::endFunc(__METHOD__);
        return $chara_list_for_client;
    }

    /**
     * キャラ追加
     * @param int $player_seq_num
     * @param array $chara_id_list
     * @param int $scene_id
     */
    public static function addCharas(int $player_seq_num, array $chara_id_list, int $scene_id) : AddCharasResult
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$chara_id_list' => $chara_id_list, '$scene_id' => $scene_id]);

        $AddCharasResult = new AddCharasResult($player_seq_num, $scene_id);

        // 直接追加
        $Player = PLayerObject::getInstance($player_seq_num, Player::class);
        $Chara  = PLayerObject::getInstance($player_seq_num, Chara::class);
        foreach ($chara_id_list as $chara_id) {
            if ($Chara->count() < $Player->getPlayerBean()->getCharaLimit()) {
                $Chara->addChara($chara_id);
                $AddCharasResult->addAddedCharaId($chara_id);
            } else {
                $AddCharasResult->addOverflowCharaId($chara_id);
            }
        }
        $Chara->syncdb();

        $AddCharasResult->setResultCode(BaseResult::COMPLETE);
        $AddCharasResult->createHistory();
        \AppLogger::endFunc(__METHOD__);
        return $AddCharasResult;
    }

}
