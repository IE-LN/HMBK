<?php
/**
 * Loads and contains the config variables
 **/
class config
{
	static $data;

	/**
	* Reads the configs into this class.
	* config.override.ini is repo ignored, use this to test/override outside of the repo on dev
	*
	* string $config_path | Path to the configs
	* string $env | prod, stage, dev
	*/
	static function init($config_path, $env = null)
	{
		// Always load prod, as it also contains defaults
		config::load( $config_path.'config.prod.ini' );
		$env = strtolower($env);
		switch( $env )
		{
			case 'stg':
				config::load( $config_path.'config.stage.ini' );
				break;
			case 'dev':
				config::load( $config_path.'config.dev_shared.ini' );
				// use config.override.ini if you don't want to use the shared dev config
				file_exists( $config_path.'config.override.ini' ) ? config::load( $config_path.'config.override.ini' ) : null;
				break;
		}
	}

	static function load($file)
	{
		$data = parse_ini_file($file, true);
		foreach($data as $group => $values)
		{
			foreach($values as $k=>$v)
			{
				self::$data[$group][$k] = $v;
			}
		}
	}
	static function get($setting)
	{
		if(strpos($setting, '.')) // specific setting
		{
			list($group, $setting) = explode('.', $setting);
			
			if(isset(self::$data[$group][$setting]))
				return self::$data[$group][$setting];

			return false;
		
		}
		return self::$data[$setting];
	}
	static function group_load()
	{
		$files = func_get_args();
		foreach($files as $f)
		{
			self::load($f);
		}
	}
	static function all()
	{
		return self::$data;
	}
}

