<?php

namespace Traits\BeanParts;

Trait RequestType
{

    /**
     * 申請タイプ
     * @var int
     */
    protected $request_type = 0;

    /**
     * 申請タイプセット
     * @param int $request_type
     */
    public function setRequestType(int $request_type)
    {
        $this->request_type = $request_type;
    }

    /**
     * 申請タイプ取得
     * @return int
     */
    public function getRequestType()
    {
        return $this->request_type;
    }

}