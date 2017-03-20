<?php
/**
 * User: Mitul
 * Date: 14/02/15
 * Time: 5:35 PM
 */

namespace Mitul\Generator\Generators\Common;

use Config;
use Mitul\Generator\CommandData;
use Mitul\Generator\Generators\GeneratorProvider;

class TransformGenerator implements GeneratorProvider
{
	/** @var  CommandData */
	private $commandData;

	private $path;

	private $namespace;

	function __construct($commandData)
	{
		$this->commandData = $commandData;
		$this->path = Config::get('generator.path_transformer', app_path('Http/Requests/'));
		$this->namespace = Config::get('generator.namespace_transformer', 'App\Http\Requests');
	}

	function generate()
	{
		$templateData = $this->commandData->templatesHelper->getTemplate("Transform", "Scaffold");
        $templateData = $this->fillTemplate($templateData);
        $templateData = str_replace('$RULES$', implode(",\n\t\t\t", $this->generateRules()), $templateData);


		$fileName = $this->commandData->modelName . "Transformer.php";

		$path = $this->path . $fileName;

		$this->commandData->fileHelper->writeFile($path, $templateData);
		$this->commandData->commandObj->comment("\nRequest created: ");
		$this->commandData->commandObj->info($fileName);
	}

    private function generateRules()
    {
        $rules = [];

        foreach ($this->commandData->inputFields as $field) {
            $rule = '"'.$field['fieldName'].'" => $'.$this->commandData->modelNameCamel."->".$field['fieldName'];
            $rules[] = $rule;
        }

        return $rules;
    }

	private function fillTemplate($templateData)
	{
	    //dd($this->commandData->modelNamePlural);
		$templateData = str_replace('$NAMESPACE$', $this->namespace, $templateData);
		$templateData = str_replace('$MODEL_NAMESPACE$', $this->commandData->modelNamespace, $templateData);
        $templateData = str_replace('$MODEL_NAME_CAMEL$', $this->commandData->modelNameCamel, $templateData);
		$templateData = str_replace('$MODEL_NAME$', $this->commandData->modelName, $templateData);
        $templateData = str_replace('$MODEL_NAME_PLURAL$', $this->commandData->modelNamePlural, $templateData);
        $templateData = str_replace('$MODEL_NAME_PLURAL_CAMEL$', $this->commandData->modelNamePluralCamel, $templateData);

		return $templateData;
	}
}