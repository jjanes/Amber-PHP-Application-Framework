@echo off
if exist %AMBER_PATH% (
	php -f %AMBER_PATH%bin\command.awf.php -- %*
) else (
	@echo on 	
	echo You need to set your AMBERPATH.
)