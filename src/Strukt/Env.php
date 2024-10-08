<?php

namespace Strukt;

use Strukt\Core\Registry;
use Strukt\Raise;
use Strukt\Type\Str;

class Env{

	public static function withFile(string $path=".env"){

		// $phar_path = \Phar::running();
		// if(!empty($phar_path))
			// $path = sprintf("%s/%s", rtrim($phar_path, "/"), trim($path, "/"));

		$lines = file(phar($path)->adapt(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		foreach($lines as $line){

			$currLine = Str::create(trim($line));
			if($currLine->startsWith("#") || $currLine->startsWith("//"))
				continue;

			list($key, $val) = explode("=", $line);

			$val = trim($val);
			$states = ["true"=>true,"false"=>false];
			if(array_key_exists($val, $states))
				$val = $states[$val];

			static::set(trim($key), $val);
		}
	}

	public static function has(string $key){

		$key = sprintf("env.%s", $key);

		$registry = Registry::getSingleton();

		return $registry->exists($key);
	}

	public static function get(string $key){

		$key = sprintf("env.%s", $key);

		$registry = Registry::getSingleton();

		if(!$registry->exists($key))
			new Raise(sprintf("Couldn't get [%s], may not be set by %s!", $key, __CLASS__));

		return $registry->get($key);
	}

	public static function set(string $key, string|int|bool $val){
			
		Registry::getSingleton()->set(sprintf("env.%s", $key), $val);
	}
}