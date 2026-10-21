<?php
    namespace Parvus;

    use Illuminate\Container\Container;
    use Illuminate\Events\Dispatcher;
    use Illuminate\Filesystem\Filesystem;
    use Illuminate\View\Compilers\BladeCompiler;
    use Illuminate\View\Engines\CompilerEngine;
    use Illuminate\View\Engines\EngineResolver;
    use Illuminate\View\Engines\PhpEngine;
    use Illuminate\View\Factory;
    use Illuminate\View\FileViewFinder;

    class View
    {

        private static $factory = NULL;

        /**
         * @param $prView
         * @param array $prArray
         * @return mixed
         */
        public final function render ($prView,$prArray = array())
        {
            return static::factory()->make($prView,$prArray)->render();
        }

        /**
         * Create the Blade factory (once per request)
         * @return Factory
         */
        private static function factory ()
        {

            if (static::$factory != NULL)
            {
                return static::$factory;
            }

            $container  = Container::getInstance();
            $filesystem = new Filesystem();

            $compiler = new BladeCompiler($filesystem, path.'cache');

            /** Keep the Blade 4.2 behavior: {{ }} prints raw and {{{ }}} prints escaped */
            $compiler->setEchoFormat('%s');

            $resolver = new EngineResolver();
            $resolver->register('blade', function () use ($compiler, $filesystem)
            {
                return new CompilerEngine($compiler, $filesystem);
            });
            $resolver->register('php', function () use ($filesystem)
            {
                return new PhpEngine($filesystem);
            });

            $finder = new FileViewFinder($filesystem, array(path.'app/view'));

            static::$factory = new Factory($resolver, $finder, new Dispatcher($container));
            static::$factory->setContainer($container);

            return static::$factory;

        }

    }
