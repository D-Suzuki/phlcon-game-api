<?php

namespace Beans\Db;

Class FriendRequestBean extends BaseDbBean
{

    protected static function getColumnList()
    {
        return \Db\PlayerDb\FriendRequestTbl::$column_list;
    }

    use \Traits\BeanParts\PlayerSeqNum;
    use \Traits\BeanParts\RequestType;
    use \Traits\BeanParts\RequestPlayerSeqNum;
    use \Traits\BeanParts\Status;

}