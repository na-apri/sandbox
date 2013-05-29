<?php namespace NaApri\ScriptAutoCompilerL4;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

class ScriptAutoCompilerL4ServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
	
	
	protected $finder = null;


	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('na-apri/script-auto-compiler-l4');

		if(Config::get('script-auto-compiler-l4::config.develop')){
			$this->registerDevelopRoute($this->getFinder());
		}
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['script-auto-compiler-l4.finder'] = $this->app->share(function($app)
		{
			return $this->getFinder();
		});

		$this->app['script-auto-compiler-l4.compiler'] = $this->app->share(function($app)
		{		
			return new ScriptCompiler(
				Config::get('script-auto-compiler-l4::config.compile.command'),
				Config::get('script-auto-compiler-l4::config.tmp')
			);
		});

		$this->app['script-auto-compiler-l4.minify'] = $this->app->share(function($app)
		{
			return new ScriptMinify(
				Config::get('script-auto-compiler-l4::config.minify.command'),
				Config::get('script-auto-compiler-l4::config.tmp')
			);
		});

		$this->app['script-auto-compiler-l4'] = $this->app->share(function($app)
		{
			return new ScriptAutoCompilerL4(
				Config::get('script-auto-compiler-l4::config.build.output'),
				Config::get('script-auto-compiler-l4::config.tmp'),
				Config::get('script-auto-compiler-l4::config.build.url'),
				Config::get('script-auto-compiler-l4::config.build.minify'),
				Config::get('script-auto-compiler-l4::config.develop')
			);
		});

		$this->app['script-auto-compiler-l4.command.build'] = $this->app->share(function($app){
			return new ScriptAutoCompilerL4Command();
		});
		$this->commands('script-auto-compiler-l4.command.build');	
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'script-auto-compiler-l4',
			'script-auto-compiler-l4.command.build',
		);
	}
	
	
	protected function getFinder(){
		if($this->finder == null){
			$this->finder = (new \ReflectionClass(
					Config::get('script-auto-compiler-l4::config.finder.finder')))
					->newInstanceArgs(array_values(
						Config::get('script-auto-compiler-l4::config.finder.args'))
					);
		}
		return $this->finder;
	}
	
	
	protected function registerDevelopRoute(ScriptFinder $finder){
		foreach($finder->getScriptUrlTable() as $url => $path){			
			Route::get("{$url}", function() use ($path){
				return Response::make(
					App::make('script-auto-compiler-l4')->compile($path),
					200,
					['Content-Type' => 'text/javascript']
				);;
			});
		}
	}
}
