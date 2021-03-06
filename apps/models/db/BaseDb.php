<?php

namespace Db;

Class BaseDb extends \Phalbase\Db\BaseDb
{

    public function findByPk($pk_value)
    {
        \AppLogger::startFunc(__METHOD__, ['pk_value' => $pk_value]);
        // クエリ生成
        $target_table  = self::getTargetTable();
        $query         =
<<< EOF
    SELECT * FROM {$target_table}
    WHERE player_seq_num = ?
EOF;

        // クエリ実行
        parent::addBindParam($pk_value);
        parent::setQuery($query);
        \AppLogger::execQuery('SELECT');
        $record = parent::selectRow();
        \AppLogger::endFunc(__METHOD__);
        return $record;
    }

    public function searchBy(array $search_list)
    {
        \AppLogger::startFunc(__METHOD__, $search_list);
        // クエリ生成
        $target_table  = self::getTargetTable();
        $query         =
<<< EOF
    SELECT * FROM {$target_table}
    WHERE player_seq_num = ?
EOF;

        // クエリ実行
        parent::addBindParam($search_list['player_seq_num']);
        parent::setQuery($query);
        \AppLogger::endFunc(__METHOD__);
        \AppLogger::execQuery('SELECT');
        return parent::select();
    }

	/**
     * SELECT
     */
    public function selectAll()
    {
        \AppLogger::startFunc(__METHOD__);
        // クエリ生成
        $target_table  = self::getTargetTable();
        $query         =
<<< EOF
    SELECT * FROM {$target_table}
EOF;
        // クエリ実行
        parent::setQuery($query);
        \AppLogger::endFunc(__METHOD__);
        return parent::selectRow();;
    }

	/**
     * INSERT
     */
    public function insert(array $insert_record)
    {
        // クエリ生成
        $target_table  = self::getTargetTable();
        $column_phrase = self::makeColumnPhrase($insert_record);
        $values_phrase = self::makeValuesPhrase($insert_record);
        $query         =
<<< EOF
    INSERT INTO {$target_table}
    {$column_phrase}
    VALUES {$values_phrase}
EOF;
        // パラメータバインド
        foreach ($insert_record as $value) {
            parent::addBindParam($value);
        }
        // クエリ実行
        parent::setQuery($query);
        parent::exec();
        return parent::getLastInsertId();
    }

    /**
     * BULK INSERT
     */
    public function bulkInsert(array $insert_record_list)
    {
        // クエリ生成
        $target_table  = self::getTargetTable();
        $column_phrase = self::makeColumnPhrase($insert_record_list[0]);
        $values_phrase = self::makeMultiValuesPhrase($insert_record_list);
        $query         =
<<< EOF
    INSERT INTO {$target_table}
    {$column_phrase}
    VALUES {$values_phrase}
EOF;
        // パラメータバインド
        foreach ($insert_record_list as $insert_record) {
            foreach ($insert_record as $value) {
                parent::addBindParam($value);
            }
		}
        // クエリ実行
        parent::setQuery($query);
        parent::exec();
    }

    /**
     * INSERT or UPDATE
     */
    public function insertOrUpdate(array $record_list)
    {
        \AppLogger::startFunc(__METHOD__);
        // クエリ生成
        $target_table  = self::getTargetTable();
        $column_phrase = self::makeColumnPhrase($record_list[0]);
        $values_phrase = self::makeMultiValuesPhrase($record_list);
        $update_phrase = self::makeOnDuplucateKeyUpdatePhrase($record_list[0]);
        $query         =
<<< EOF
    INSERT INTO {$target_table}
    {$column_phrase}
    VALUES {$values_phrase}
    ON DUPLICATE KEY UPDATE
    {$update_phrase}
EOF;

        // パラメータバインド
        foreach ($record_list as $record) {
            foreach ($record as $value) {
                parent::addBindParam($value);
            }
		}
        // クエリ実行
        parent::setQuery($query);
        parent::exec();
        \AppLogger::endFunc(__METHOD__);
    }

    private static function makeOnDuplucateKeyUpdatePhrase($record)
    {
        $on_duplicate_key_update_phrase = '';
        foreach ($record as $key => $value) {
            $on_duplicate_key_update_phrase .= $key . ' = VALUES( ' . $key . ' ),';
        }
        return rtrim($on_duplicate_key_update_phrase, ', ');
    }

    /**
     * DEFLAG
     */
    public function deflag()
    {

    }

    /**
     * アーカイブ作成
     */
    public function createArchive()
    {

    }

    /**
     * 対象テーブル取得
     * @return string
     */
    protected static function getTargetTable()
    {
        $table_class = static::class;
        $db_class    = get_parent_class(static::class);
        return $db_class::$db_name . '.' . $table_class::$table_name;
    }

    /**
     * COLUMN句生成
     * @param array $insert_record
     * @return string
     */
    protected static function makeColumnPhrase(array $insert_record)
    {
        $column_phrase = '( ';
        foreach (array_keys($insert_record) as $key) {
            $column_phrase .= $key . ', ';
        }
        return rtrim($column_phrase, ', ') . ' )';
    }

    /**
     * VALUES句生成（単一）
     * @param array $insert_record
     * @return string
     */
    protected static function makeValuesPhrase(array $insert_record)
    {
        $values_phrase = '( ';
        for ($i = 0; $i < count($insert_record); $i++) {
             $values_phrase .= '?, ';
        }
        return rtrim($values_phrase, ', ') . ' )';
    }

    /**
     * VALUES句生成（複数）
     * @param array $insert_record_list
     * @return string
     */
    protected static function makeMultiValuesPhrase(array $insert_record_list)
    {
        $multi_values_phrase = '';
        foreach ($insert_record_list as $insert_record) {
            $multi_values_phrase .= self::makeValuesPhrase($insert_record) . ', ';
        }
        return rtrim($multi_values_phrase, ', ');
    }

    /**
     * IN句生成
     */
    public static function makeInPhrase(array $player_seq_num_list)
    {
        $in_phrase = '( ';
        foreach ($player_seq_num_list as $player_seq_num) {
            $in_phrase .= '?, ';
        }
        $in_phrase = rtrim($in_phrase, ', ') . ' )';
        return $in_phrase;
    }

}
