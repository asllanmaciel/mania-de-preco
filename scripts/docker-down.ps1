docker compose down

if ($LASTEXITCODE -ne 0) {
    exit $LASTEXITCODE
}

Write-Host "Containers finalizados com sucesso."
