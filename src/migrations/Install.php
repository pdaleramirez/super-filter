<?php

namespace pdaleramirez\superfilter\migrations;

use Craft;
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    private $setupSearchTable = '{{%superfilter_setup_search}}';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $setupSearchTable = $this->getDb()->tableExists($this->setupSearchTable);

        if ($setupSearchTable == false) {
            $this->createTable($this->setupSearchTable,
                [
                    'id'     => $this->primaryKey(),
                    'options'=> $this->text(),
                    'fields' => $this->text(),
                    'sorts'  => $this->text(),
                    'dateCreated'   => $this->dateTime(),
                    'dateUpdated'   => $this->dateTime(),
                    'uid'           => $this->uid()
                ]
            );

            $this->addForeignKey(null, $this->setupSearchTable,
                ['id'], '{{%elements}}', ['id'], 'CASCADE');
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        // Place uninstallation code here...
    }
}
