<?php

namespace PlayerObject;

use Beans\Db\PlayerBean;
use Db\PlayerDb\PlayerTbl;

Class Player extends PlayerObject
{

    /**
     * プレイヤーBean
     */
    private $PlayerBean = null;

    /**
     * プレイヤーBean取得
     * @return PlayerBean
     */
    public function getPlayerBean()
    {
        if (is_null($this->PlayerBean) === true) {
            $this->setPlayerBean();
        }
        return $this->PlayerBean;
    }

    /**
     * DB同期
     */
    public function syncdb()
    {
        return false;
    }

    /**
     * プレイヤーBeanセット
     */
    private function setPlayerBean()
    {
        \AppLogger::startFunc(__METHOD__);
        $PlayerTbl = \Db\Factory::getInstance(PlayerTbl::class, $this->player_seq_num);
        $record    = $PlayerTbl->findByPk($this->player_seq_num);
        if ($record !== false) {
            $this->PlayerBean = new PlayerBean($record);
        } else {
            throw new \Exception('player data is empty [player_seq_num:' . $this->player_seq_num . ']');
        }
        \AppLogger::endFunc(__METHOD__);
    }

}
