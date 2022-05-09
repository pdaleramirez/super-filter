<?php

namespace pdaleramirez\superfilter\console\controllers;

use craft\console\Controller;
use Craft;
use craft\helpers\Console;
use craft\helpers\FileHelper;
use craft\helpers\Json;
use pdaleramirez\superfilter\elements\SetupSearch;
use pdaleramirez\superfilter\services\SampleData;
use pdaleramirez\superfilter\SuperFilter;
use yii\console\ExitCode;

class GenerateController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'generate';

    public $searchSetupHandle = '';

    /**
     * @var string Name of the folder the templates will copy into
     * @since 3.3
     */
    public $folderName = '';

    /**
     * @var bool Whether to overwrite an existing folder. Must be passed if a folder with that name already exists.
     */
    public $overwrite = false;

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        $options[] = 'folderName';
        $options[] = 'overwrite';
        $options[] = 'searchSetupHandle';

        return $options;
    }

    public function actionTemplates()
    {
        if ($this->searchSetupHandle !== '') {
            $searchSetupHandle = $this->searchSetupHandle;
        } else {
            $this->stdout('Files will be created to your templates directory.' . PHP_EOL);
            $searchSetupHandle = $this->prompt('Enter a search setup handle.', ['required' => true]);
        }

        $searchSetup = SetupSearch::find()->where(['handle' => $searchSetupHandle])->one();

        if ($searchSetup === null) {
            $this->stderr('Invalid search setup handle.');
        } else {
            $configItems = Json::decodeIfJson($searchSetup->items);
            $options = Json::decodeIfJson($searchSetup->options);

            $templatePath = $this->_getTemplatesPath();
            $template = $options['template'];

            $templateDir =  $templatePath  . '/' . $template;

            try {
                FileHelper::createDirectory($templateDir);
            } catch (\Exception $e) {
                return $this->_returnErrors([$e->getMessage()]);
            }

            $containerHandle = $configItems['container'] ?? null;
            $section = Craft::$app->getSections()->getSectionByHandle($containerHandle);

            $entryTypes = $section->getEntryTypes();

            $items = [];
            foreach ($entryTypes as $entryType) {

                foreach ($entryType->getFieldLayout()->getCustomFields() as $fieldLayoutField) {
                    $items[] = $fieldLayoutField;
                }
            }

            $html = Craft::$app->getView()->renderPageTemplate('super-filter/generate/items', [
                'items' => $items
            ]);
            //$this->stdout(Craft::dump($items));

            $itemsPath = $templateDir . '/items.twig';
            FileHelper::writeToFile($itemsPath, $html);

            $this->stdout("File items.twig template created in $itemsPath" . PHP_EOL);
        }

        return true;
    }

    public function actionExample()
    {
        SuperFilter::$app->sampleData->generateSampleData();

        if ($this->folderName !== '') {
            $folderName = $this->folderName;
        } else {
            $this->stdout('A folder will be copied to your templates directory.' . PHP_EOL);
            $folderName = $this->prompt('Choose folder name:', ['required' => true, 'default' => SampleData::DEFAULT_FOLDER]);
        }

        $templatesPath = $this->_getTemplatesPath();
        $errors = [];
        if (FileHelper::isWritable($templatesPath) === false) {
            $errors[] = Craft::t('super-filter', 'Site template must have write permission.');
        }

        // Folder name is required
        if (trim($folderName) === '') {
            $errors[] = 'No destination folder name provided.';
        }

        if (count($errors) > 0) {
            return $this->_returnErrors($errors);
        }

        SuperFilter::$app->sampleData->createSearchSetup($folderName);

        SuperFilter::$app->sampleData->createFiles($templatesPath, $folderName);

        return true;
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

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    private function _getTemplatesPath(): string
    {
        $originalMode = Craft::$app->getView()->getTemplateMode();
        Craft::$app->getView()->setTemplateMode(\craft\web\View::TEMPLATE_MODE_SITE);
        $templatesPath = Craft::$app->getView()->getTemplatesPath();
        Craft::$app->getView()->setTemplateMode($originalMode);
        return $templatesPath;
    }
}