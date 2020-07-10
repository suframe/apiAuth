<?php

use think\migration\Migrator;

class ApiUser extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        //通用配置组表
        $table = $this->table(
            'api_user',
            array(
                'engine' => 'InnoDB',
                'comment' => 'api用户',
            ));
        $table->addColumn('uid', 'int', ['comment' => '用户id', 'length' => 11])
            ->addColumn('token', 'string', ['comment' => 'token', 'length' => 32])
            ->addColumn('refresh_token', 'string', ['comment' => '刷新token', 'length' => 64])
            ->addColumn('refresh_token_time', 'string', ['comment' => '刷新token创建时间', 'length' => 12])
            ->addTimestamps()
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['uid'], ['unique' => true])
            ->addIndex(['refresh_token'], ['unique' => true])
            ->create();
    }
}
