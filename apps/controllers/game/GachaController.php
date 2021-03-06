<?php

namespace Controllers\Game;

class GachaController extends BaseController
{

    /**
     * ガチャリスト取得
     */
    public function listAction()
    {
        \AppLogger::startFunc(__METHOD__);
        \AppRegistry::setDbType(\AppConst::DB_TYPE_READ);

        $this->setResponseData([
            'gacha_list' => GachaLogic::getGachaListForClient($this->player_seq_num),
        ]);
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * ガチャ実行
     */
    public function playAction()
    {
        \AppLogger::startFunc(__METHOD__);
        $gacha_id   = parent::getRequest('gacha_id', $requierd_flg = true);
        $draw_count = parent::getRequest('draw_count', $requierd_flg = true);

        /* @var $GachaPlayResult \GameObject\Result\Gacha\PlayResult */
        $PlayResult = GachaLogic::play($this->player_seq_num, $gacha_id, $draw_count);

        $this->setResponseData([
            'result_data' => $PlayResult->getResultForClient(),
            'jewel_data'  => JewelLogic::getJewelDataForClient($this->player_seq_num),
            'coin_data'   => CoinLogic::getCoinDataForClient($this->player_seq_num),
            'chara_list'  => CharaLogic::getCharaListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
			'item_list'   => ItemLogic::getItemListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
        ]);
        \AppLogger::endFunc(__METHOD__);
    }

}
