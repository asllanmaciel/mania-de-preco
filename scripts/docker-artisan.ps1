param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Arguments
)

if (-not $Arguments -or $Arguments.Count -eq 0) {
    Write-Error "Informe os argumentos do artisan. Exemplo: .\scripts\docker-artisan.ps1 route:list"
    exit 1
}

docker compose exec -T laravel.test php artisan @Arguments

exit $LASTEXITCODE
