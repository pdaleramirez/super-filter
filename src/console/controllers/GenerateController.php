<?php

namespace pdaleramirez\superfilter\console\controllers;

use craft\console\Controller;
use Craft;
use craft\helpers\Console;
use yii\console\ExitCode;

class GenerateController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'generate';

    public string $section = 'superFilterShows';
    public string $folderName = '';

    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'folderName';
        $options[] = 'section';

        return $options;
    }

    public function actionGenerate()
    {
        $section = Craft::$app->getSections()->getSectionByHandle('superFilterShows');

        $entryTypes = $section->getEntryTypes();

        foreach ($entryTypes as $entryType) {
            foreach ($entryType->getFieldLayout()->getFields() as $field) {
                $handle = $field->handle;
            }
           // Craft::dd($entryType->getFieldLayout()->getFields());
        }
        if ($this->folderName !== '') {
            $folderName = $this->folderName;
        } else {
            $this->stdout('A folder will be copied to your templates directory.' . PHP_EOL);
            $folderName = $this->prompt('Choose folder name:', ['required' => true, 'default' => 'search']);
        }

        // Folder name is required
        if (!$folderName) {
            $errors[] = 'No destination folder name provided.';
            return $this->_returnErrors($errors);
        }



        $this->stdout("folderName: " . $folderName . PHP_EOL);
        $this->stdout("section: " . $this->section . PHP_EOL);
    }

    /**
     * @param array $errors
     * @return int
     */
    private function _returnErrors(array $errors): int
    {
        $this->stderr('Error(s):' . PHP_EOL . '    - ' . implode(PHP_EOL . '    - ', $errors) . PHP_EOL, Console::FG_RED);
        return ExitCode::USAGE;
    }
}