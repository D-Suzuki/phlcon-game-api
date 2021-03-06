<?php

namespace Controllers\Game;

use GameLogics\ProfileLogic;
use GameObject\Result\Player\RenameResult;

Class ProfileController extends BaseController
{

    /**
     * ニックネーム変更
     */
    public function renameAction()
    {
        \AppLogger::startFunct(__METHOD__);
        $nickname = $this->request->get('nickname');

        /* @var $RenameResult \GameObject\Result\Player\RenameResult */
        $RenameResult = ProfileLogic::rename($this->player_seq_num, $nickname);

        $this->setResponseData([
            'reneme_result' => $RenameResult->getResultForClient(),
            'profile_data'  => ProfileLogic::getProfileDataForClient($this->player_seq_num),
        ]);

        \AppLogger::endFunc(__METHOD__);
    }

}
