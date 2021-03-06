<?php

namespace Traits\BeanLogics;

Trait ExpToLevel
{

    use \Traits\BeanParts\Exp;

    /**
     * 経験値マスタクラス
     * @var string
     */
    static private $level_master_class = null;

    /**
     * レベル取得
     * @return int
     */
    public function getLevel()
    {
        if (is_null(self::$level_master_class) === true) {
            throw new \Exception('static level_master_class property is null');
        }
        $level_master_list = self::$level_master_class::getAll();
        foreach ($level_master_list as $index => $level_master) {
            $from_exp = $level_master['required_exp'];
            if (isset($level_master_list[$index + 1]) === true) {
                $to_exp = $level_master_list[$index + 1]['required_exp'];
            } else {
                $level = $level_master['level'];
                break;
            }
            if ($from_exp <= $this->getExp() && $this->getExp() < $to_exp) {
                $level = $level_master['level'];
                break;
            }
        }
        return $level;
    }

}