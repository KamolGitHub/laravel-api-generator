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

class RequestGenerator implements GeneratorProvider
{
	/** @var  CommandData */
	private $commandData;

	private $path;

	private $namespace;

	function __construct($commandData)
	{
		$this->commandData = $commandData;
		$this->path = Config::get('generator.path_request', app_path('Http/Requests/'));
		$this->namespace = Config::get('generator.namespace_request', 'App\Http\Requests');
	}

	function generate()
	{
		$templateData = $this->commandData->templatesHelper->getTemplate("Request", "Scaffold");

		$templateData = $this->fillTemplate($templateData);
        $templateData = str_replace('$RULES$', implode(",\n\t\t", $this->generateRules()), $templateData);

		$fileName = $this->commandData->modelName . "Request.php";

		$path = $this->path . $fileName;

		$this->commandData->fileHelper->writeFile($path, $templateData);
		$this->commandData->commandObj->comment("\nRequest created: ");
		$this->commandData->commandObj->info($fileName);
	}

    private function generateRules()
    {
        $rules = [];

        foreach ($this->commandData->inputFields as $field) {
            if (!empty($field['validations'])) {
                $rule = '"'.$field['fieldName'].'" => "'.$field['validations'].'"';
                $rules[] = $rule;
            }
        }

        return $rules;
    }

	private function fillTemplate($templateData)
	{
		$templateData = str_replace('$NAMESPACE$', $this->namespace, $templateData);
		$templateData = str_replace('$MODEL_NAMESPACE$', $this->commandData->modelNamespace, $templateData);

		$templateData = str_replace('$MODEL_NAME$', $this->commandData->modelName, $templateData);
        $templateData = str_replace('$MODEL_NAME_PLURAL_CAMEL$', $this->commandData->modelNamePluralCamel, $templateData);

		return $templateData;
	}
}