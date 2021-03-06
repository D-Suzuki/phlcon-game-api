<?php

namespace Traits\BeanParts;

Trait BonusLotteryId
{

    /**
     * おまけ抽選ID
     * @var int
     */
    protected $bonus_lottery_id = null;

    /**
     * おまけ抽選IDセット
     * @param int $bonus_lottery_id
     */
    public function setBonusLotteryId(int $bonus_lottery_id)
    {
        $this->bonus_lottery_id = $bonus_lottery_id;
    }

    /**
     * おまけ抽選ID取得
     * @return int
     */
    public function getBonusLotteryId()
    {
        return $this->bonus_lottery_id;
    }

}