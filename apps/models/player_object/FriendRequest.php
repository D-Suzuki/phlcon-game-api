<?php

namespace PlayerObject;

use \Beans\Db\FriendRequestBean;
use \Db\PlayerDb\FriendRequestTbl;

Class FriendRequest extends PlayerObject
{

    const REQUEST_STATUS_WAITING = 1;

    const REQUEST_TYPE_SEND = 1;

    const REQUEST_TYPE_RECV = 2;

    /**
     * フレンド申請Beanリスト
     * @var array
     */
    private $friend_request_bean_list = null;

    /**
     * クライアント用フレンド申請リスト取得
     * @return array
     */
    public function getFriendRequestListForClient()
    {
        $friend_request_list_for_client = [];
        $friend_request_bean_list       = $this->getFriendRequestBeanList();
        if (count($friend_request_bean_list) > 0) {
            foreach ($friend_request_bean_list as $FriendRequestBean) {
                $friend_request_list_for_client[] = [
                    'request_type'           => $FriendRequestBean->getRequestType(),
                    'request_player_seq_num' => $FriendRequestBean->getRequestPlayerSeqNum(),
                ];
            }
        }
        var_dump($friend_request_list_for_client);exit;
    }

    /**
     * 申請送信存在判定
     * @param int $request_player_seq_num
     * @return bool
     */
    public function hasSendRequest($request_player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['request_player_seq_num' => $request_player_seq_num]);
        $FriendRequestBean = $this->getFriendRequestBean(self::REQUEST_TYPE_SEND, $request_player_seq_num);
        if (is_null($FriendRequestBean) === true) {
            $has_send_request = false;
        } else {
            $has_send_request = true;
        }
        \AppLogger::endFunc(__METHOD__, $has_send_request);
        return $has_send_request;
    }

    /**
     * 申請受信存在判定
     * @param int $request_player_seq_num
     * @return bool
     */
    public function hasRecvRequest($request_player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['request_player_seq_num' => $request_player_seq_num]);
        $FriendRequestBean = $this->getFriendRequestBean(self::REQUEST_TYPE_RECV, $request_player_seq_num);
        if (is_null($FriendRequestBean) === true) {
            $has_recv_request = false;
        } else {
            $has_recv_request = true;
        }
        \AppLogger::endFunc(__METHOD__, $has_recv_request);
        return $has_recv_request;
    }

    /**
     * 申請追加
     * @param int $request_type
     * @param int $request_player_seq_num
     * @throws Exception
     */
    public function addRequest(int $request_type, int $request_player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->getFriendRequestBean($request_type, $request_player_seq_num)) === false) {
            throw new \Exception();
        }
        // Bean追加
        $FriendRequestBean = new FriendRequestBean([
            'player_seq_num'         => $this->player_seq_num,
            'request_type'           => $request_type,
            'request_player_seq_num' => $request_player_seq_num,
            'status'                 => self::REQUEST_STATUS_WAITING,
            'created_at'             => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            'updated_at'             => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
        ]);
        $FriendRequestBean->setUpdateFlg(true);
        $this->friend_request_bean_list[$request_player_seq_num] = $FriendRequestBean;
        // DB更新
        $FriendRequestTbl = \Db\Factory::getInstance(FriendRequestTbl::class, $this->player_seq_num);
        $FriendRequestTbl->insertOrUpdate([$FriendRequestBean->toRecord()]);
        \AppLogger::endFunc(__METHOD__);
	}

    /**
     * 申請更新
     * @param int $request_player_seq_num
     * @param int $status
     * @throws Exception
     */
    public function updateRequest(int $request_type, int $request_player_seq_num, int $status)
    {
        \AppLogger::startFunc(__METHOD__, [
            'request_type'           => $request_type,
            'request_player_seq_num' => $request_player_seq_num,
            'status'                 => $status
        ]);
        $FriendRequestBean = $this->getFriendRequestBean($request_type, $request_player_seq_num);
        if (is_null($FriendRequestBean) === false) {
            throw new \Exception();
        }
        // Bean更新
        $FriendRequestBean->setStatus($status);
        $FriendRequestBean->updateFlg(true);
        // DB更新
        $FriendRequestTbl = \Db\Factory::getInstance(FriendRequestTbl);
        $FriendRequestTbl->insertOrUpdate([$FriendRequestBean->toRecord()]);
        \AppLogger::endFunc(__METHOD__);
	}

    /**
     * フレンド申請Beanリスト取得
     * @return array
     */
    public function getFriendRequestBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->friend_request_bean_list) === true) {
            $this->setFriendRequestBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->friend_request_bean_list;
    }

    /**
     * 指定フレンド申請Bean取得
     * @param int $request_type
     * @param int $request_player_seq_num
     * @return FriendRequestBean
     */
    public function getFriendRequestBean(int $request_type, int $request_player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendRequestBean        = null;
        $friend_request_bean_list = $this->getFriendRequestBeanList();
        if (count($friend_request_bean_list) > 0) {
            foreach ($friend_request_bean_list as $Bean) {
                if ($Bean->getRequestType() === $request_type && $Bean->getReqestPlayerSeqNum() === $request_player_seq_num) {
                    $FriendRequestBean = $Bean;
                    break;
                }
            }
        }
        \AppLogger::endFunc(__METHOD__);
        return $FriendRequestBean;
    }

    /**
     * フレンド申請Beanリストセット
     */
    private function setFriendRequestBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendRequestTbl = \Db\Factory::getInstance(FriendRequestTbl::class, $this->player_seq_num);
        $record_list      = $FriendRequestTbl->findByPk($this->player_seq_num);
        if ($record_list !== false) {
            foreach ($record_list as $record) {
                $this->friend_request_bean_list[$record['request_player_seq_num']] = new FriendRequestBean($record);
    		}
        } else {
            $this->friend_request_bean_list = [];
        }
        \AppLogger::endFunc(__METHOD__);
    }

}