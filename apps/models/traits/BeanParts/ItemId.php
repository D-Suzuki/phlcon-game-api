<?php

namespace Traits\BeanParts;

Trait ItemId
{

    /**
     * アイテムID
     * @var int
     */
    protected $item_id = null;

    /**
     * アイテムIDセット
     * @param int $item_id
     */
    public function setItemId(int $item_id)
    {
        $this->item_id = $item_id;
    }

    /**
     * アイテムID取得
     * @return int
     */
    public function getItemId()
    {
        return $this->item_id;
    }

}