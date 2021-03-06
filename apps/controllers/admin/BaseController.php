<?php

namespace Controllers\Admin;

use Logger\AppLogger;

class BaseController extends \Takajo\Controller\BaseController
{

    /**
     * プレイヤーシーケンスNUM
     * @var int
     */
    protected $player_seq_num = null;

    /**
     * メンテナンススルーフラグ
     * @var bool
     */
    protected $mentenance_through_flg = false;

    /**
     * セッションスルーフラグ
     * @var bool
     */
    protected $session_through_flg = false;

    /**
     * メンテナンススルーフラグセット
     * @param bool $mentenance_through_flg
     */
    protected function setMentenanceThroughFlg(bool $mentenance_through_flg)
    {
        $this->mentenance_through_flg = $mentenance_through_flg;
    }

    /**
     * セッションスルーフラグセット
     * @param bool $session_through_flg
     */
    protected function setSessionThroughFlg(bool $session_through_flg)
    {
        $this->session_through_flg = $session_through_flg;
    }

    /**
     * 前処理
     */
    public function beforeExecuteRoute()
    {
        AppLogger::startProcess();
        AppLogger::startFunc(__METHOD__);

        // リクエストメソッドチェック
        if ($this->isValidRequestMethod() === false) {
            throw new Exception('Request Method is not valid [Request Method=' . $this->request->getMethod());
        }
        // セッションチェック
        if ($this->hasSession() === false) {
            throw new Exception('Session is not exists');
        }
        // メンテナンスチェック
        if ($this->isMaintenance() === true) {

        }
        // レスポンスキャッシュチェック
        if ($this->hasResponseCache() === false) {

        }

        $this->player_seq_num = 1;

        AppLogger::endFunc(__METHOD__);
    }

    /**
     * 後処理
     */
    public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $Dispatcher)
    {
        AppLogger::startFunc(__METHOD__);

        $response_data = array_merge($this->getResponseData(), $this->getApiStatusResponseData());
        $this->setResponseData($response_data);
        parent::afterExecuteRoute($Dispatcher);

        AppLogger::endFunc(__METHOD__);
        AppLogger::endProcess();
    }

    /**
     * リクエスト取得
     * @param string $request_key
     * @param bool $required_flg
     * @return mixed
     */
    protected function getRequest(string $request_key, bool $required_flg = false)
    {
        $request_value = $this->request->get($request_key);
        // 必須リクエストチェック
        if ($required_flg === true && strlen($request_value) === 0) {
            throw new Exception('Request Key is empty [request_key=' . $request_key . ']');
        }
        return $request_value;
    }

    private function getApiStatusResponseData()
    {
        AppLogger::startFunc(__METHOD__);

        AppLogger::endFunc(__METHOD__);
        return ['
            api_status' => [
                'app_version'   => 0.01,
                'is_mentenance' => 1,
            ]
        ];
    }

    /**
     * リクエストメソッド検証
     */
    private function isValidRequestMethod()
    {
        AppLogger::startFunc(__METHOD__);
        switch (\AppRegistry::getEnv()) {
            // 本番
            case \AppConst::ENV_PROD:
                $is_valid = $this->request->isPost(); // ポストのみ
                break;
            // 開発、ステージング、審査
            case \AppConst::ENV_DEV:
            case \AppConst::ENV_STG:
            case \AppConst::ENV_REV:
                $is_valid = true; // 何でもよい
                break;
        }
        AppLogger::endFunc(__METHOD__, $is_valid);
        return $is_valid;
    }

    /**
     * セッションチェック
     * @return boolean
     */
    private function hasSession()
    {
        return true;

        $session_key = $this->request->getHeader('session_key');

        $player_seq_num = Memcache::get($session_key);
        if (is_null($player_seq_num) === true) {
            return false;
        } else {
            $this->player_seq_num = $player_seq_num;
            return true;
        }
    }

    /**
     * メンテナンスチェック
     * @return boolean
     */
    private function isMaintenance()
    {
        return false;
    }

    /**
     * レスポンスキャッシュチェック
     * @return boolean
     */
    private function hasResponseCache()
    {
        return false;

        $req_seq_id = self::getRequest('req_seq_id', $required_flg = true);

        // プレイヤー認証
        $response_cache = Memcache::get('response' . $this->player_seq_num . '_' . $req_seq_id);
    }

}
