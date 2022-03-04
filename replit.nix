{ pkgs }: {
	deps = [
    (pkgs.php.buildEnv {
    	extraConfig = "
      error_reporting=Off
      zend_extension=${pkgs.phpExtensions.xdebug}/lib/php/extensions/xdebug.so
      ";
    })
    pkgs.phpExtensions.curl
    pkgs.phpExtensions.mbstring
    pkgs.phpExtensions.pdo
    pkgs.phpExtensions.opcache
    pkgs.phpExtensions.imagick
    pkgs.phpExtensions.mysqli
    pkgs.phpExtensions.xdebug
    pkgs.phpPackages.phpcs
    pkgs.phpPackages.phpstan
    pkgs.phpPackages.psalm
    pkgs.phpPackages.composer
	];
}
