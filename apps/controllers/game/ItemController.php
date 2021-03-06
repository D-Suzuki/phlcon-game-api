<?php

namespace Controllers\Game;

class ItemController extends BaseController
{

    /**
     * コントローラ前処理
     */
    public function beforeExecuteRoute()
    {

    }

    /**
     * コントローラ後処理
     */
    public function afterExecuteRoute()
    {

    }

    /**
     * アイテムリスト取得
     */
    public function listAction()
    {
        \AppLogger::startFunc(__METHOD__);
        $last_access_time = $this->request->get('last_access_time');
        $LastAccessTime   = $last_access_time === '' ? null : new DateTime($last_access_time);

        $Item = PlayerObject::getInstance(Item::class); /* @var $Item \PlayerObject\Item */

        $this->setResponseData([
            'item_list' => $Item->getItemListForClient($LastAccessTime),
        ]);
        \AppLogger::endFunc(__METHOD__);
    }

}
