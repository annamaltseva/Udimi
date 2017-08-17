<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cells`.
 */
class m170815_104240_create_cells_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cells', [
            'id'    => $this->primaryKey(),
            'cid'   => $this->integer()->unique()->notNull()->comment('Cell position id'),
            'value' => $this->integer(5)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cells');
    }
}
