<?php

namespace Fnoual\Generators\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;

class GenerateRepositoryCommand extends Command
{
    public string $namespace;
    public string $className;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {repositoryName} {associatedModel?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a repository';
    private Filesystem $filesys;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->filesys = $files;
    }

    /**
     * Execute the console command.
     *  TODO : fix recursive file creation error
     *  TODO : fix associatedModel binding
     * @return int
     */
    public function handle()
    {
        $classPathFile = $this->argument('repositoryName');
        $classPathArray = explode('/', $classPathFile);
        $className = $classPathArray[count($classPathArray) - 1];

        array_pop($classPathArray);
        $namespaceExtension = join('\\', $classPathArray);
        if ($namespaceExtension != null) {
            $namespace = 'App\\Repositories\\' . $namespaceExtension;
        } else {
            $namespace = 'App\\Repositories';
        }
        $path = app_path('Repositories/' . join('/', $classPathArray));
        $pathFileName = app_path('Repositories/' . join('/', $classPathArray)) . $className . '.php';
        $this->setVariables($namespace, $className);
        $data = $this->getSourceFile();

        if (!$this->filesys->isDirectory($path)) {
            $this->filesys->makeDirectory($path, 0755, 1);
        }
        if (!$this->filesys->exists($pathFileName)) {
            $this->filesys->put($pathFileName, $data);
            $this->info("File : {$pathFileName} created");
        } else {
            $this->info("File : {$pathFileName} already exits");
        }
        return Command::SUCCESS;
    }

    public function setVariables($namespace, $className)
    {
        $this->namespace = $namespace;
        $this->className = $className;
    }

    public function getStubPath()
    {
        return dirname( dirname(__FILE__) ) . '/Stubs/Repository.stub';
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     *
     */
    public function getStubVariables()
    {
        if($this->argument('associatedModel')) {
            $model ='private ' . $this->argument('associatedModel') . ' $' . strtolower($this->argument('associatedModel')) . ';';
        }
        else {
            $model = null;
        }
        return [
            'NAMESPACE' => $this->namespace,
            'CLASSNAME' => $this->className,
            'MODEL' => $model
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }


    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }

        return $contents;

    }
}
