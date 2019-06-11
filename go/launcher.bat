@echo off
"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --profile-directory="Default" --app="data:text/html,<html><body><script>window.moveTo(580,240);window.resizeTo(540,600);window.location='http://localhost/Gleesons/go?term=test';</script></body></html>"
REM This can be used either by itself or the above line ^^ can be placed in the target of a shortcut