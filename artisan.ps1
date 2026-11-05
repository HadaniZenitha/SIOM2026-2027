$env:PATH = "$env:USERPROFILE\.config\herd\bin;" + $env:PATH
$tmpDir = "$env:USERPROFILE\.config\herd\tmp"
if (!(Test-Path $tmpDir)) {
    New-Item -ItemType Directory -Path $tmpDir -Force | Out-Null
}
$env:TEMP = $tmpDir
$env:TMP = $tmpDir
& "$env:USERPROFILE\.config\herd\bin\php84\php.exe" -d "upload_tmp_dir=$tmpDir" -d "sys_temp_dir=$tmpDir" artisan @args
