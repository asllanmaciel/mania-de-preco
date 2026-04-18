param(
    [switch]$Seed
)

docker compose up -d

if ($LASTEXITCODE -ne 0) {
    exit $LASTEXITCODE
}

if ($Seed) {
    docker compose exec -T laravel.test php artisan migrate --seed

    if ($LASTEXITCODE -ne 0) {
        exit $LASTEXITCODE
    }
}

Write-Host "Containers iniciados com sucesso."
