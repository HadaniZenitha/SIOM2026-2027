@echo off
set "PATH=%USERPROFILE%\.config\herd\bin;%PATH%"
set "TMPDIR=%USERPROFILE%\.config\herd\tmp"
if not exist "%TMPDIR%" mkdir "%TMPDIR%"
set "TEMP=%TMPDIR%"
set "TMP=%TMPDIR%"
"%USERPROFILE%\.config\herd\bin\php84\php.exe" -d upload_tmp_dir="%TMPDIR%" -d sys_temp_dir="%TMPDIR%" artisan serve %*
