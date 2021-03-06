<?php namespace Znck\Transformers\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Znck\Transformers\Transformer;

/**
 * @property \Illuminate\Foundation\Application $laravel
 */
class TransformerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:transformer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new transformer class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Transformer';

    protected function getStub()
    {
        return dirname(dirname(__DIR__)).'/resources/stubs/transformer.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\'.$this->getTransformersDirectory();
    }

    protected function parseModel($name)
    {
        $rootNamespace = $this->laravel->getNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseModel(trim($rootNamespace, '\\').'\\'.$name);
    }

    protected function parseName($name)
    {
        $rootNamespace = $this->laravel->getNamespace();

        if (! Str::endsWith($name, 'Transformer')) {
            $name .= 'Transformer';
        }

        if (Str::contains($name, ['Models', 'Eloquent'])) {
            $name = str_replace(['Models', 'Eloquent'], '', $name);
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        $name = str_replace('\\\\', '\\', $name);

        if (Str::startsWith($name, $rootNamespace)) {
            if (! Str::contains($name, 'Transformers')) {
                return str_replace($rootNamespace, $this->getDefaultNamespace($rootNamespace), $name);
            }

            return $name;
        }

        return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name);
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $transformer = config('transformer.base_transformer', Transformer::class);

        $this->replaceTransformer($stub, $transformer);
        $this->replaceModel($stub);

        return $stub;
    }

    protected function replaceTransformer(&$stub, $class)
    {
        if (! hash_equals(Transformer::class, $class)) {
            $class .= ' as Transformer';
        }

        $stub = str_replace('DummyBaseTransformer', $class, $stub);

        return $this;
    }

    protected function replaceModel(&$stub)
    {
        $name = $this->parseModel($this->getNameInput());

        if (! class_exists($name)) {
            $comments = '// FIXME: Add model class name. Detected: '.$name;
        }

        $namespace = "use ${name};";
        $name = class_basename($name);
        $stub = str_replace('DummyModelNamespace', $namespace, $stub);
        $stub = str_replace('DummyModelClass', $name, $stub);
        $stub = str_replace('HelperComments', $comments ?? '', $stub);

        return $this;
    }

    protected function getTransformersDirectory()
    {
        return 'Transformers';
    }
}
