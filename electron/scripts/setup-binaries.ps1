# Setup PHP and Redis binaries for Electron packaging (Windows)

$PHP_VERSION = "8.4.1"
$REDIS_VERSION = "5.0.14.1"

$BIN_DIR = ".\electron\resources\bin\win32-x64"

# Create directory
New-Item -ItemType Directory -Force -Path $BIN_DIR | Out-Null

Write-Host "==================================="
Write-Host "Electron Binary Setup (Windows)"
Write-Host "==================================="
Write-Host ""

# Download PHP
function Download-PHP {
    Write-Host "Downloading PHP $PHP_VERSION..."

    if (Test-Path "$BIN_DIR\php.exe") {
        Write-Host "PHP already exists, skipping..."
        return
    }

    $PHP_URL = "https://dl.static-php.dev/static-php-cli/common/php-$PHP_VERSION-cli-windows-x64.zip"

    Invoke-WebRequest -Uri $PHP_URL -OutFile "$env:TEMP\php.zip"
    Expand-Archive -Path "$env:TEMP\php.zip" -DestinationPath $BIN_DIR -Force
    Remove-Item "$env:TEMP\php.zip"

    # Verify PHP
    & "$BIN_DIR\php.exe" --version
    Write-Host "PHP installed successfully!"
}

# Download Redis
function Download-Redis {
    Write-Host "Downloading Redis $REDIS_VERSION..."

    if (Test-Path "$BIN_DIR\redis-server.exe") {
        Write-Host "Redis already exists, skipping..."
        return
    }

    $REDIS_URL = "https://github.com/tporadowski/redis/releases/download/v$REDIS_VERSION/Redis-x64-$REDIS_VERSION.zip"

    Invoke-WebRequest -Uri $REDIS_URL -OutFile "$env:TEMP\redis.zip"
    Expand-Archive -Path "$env:TEMP\redis.zip" -DestinationPath "$env:TEMP\redis-temp" -Force

    # Copy only necessary files
    Copy-Item "$env:TEMP\redis-temp\redis-server.exe" "$BIN_DIR\"
    Copy-Item "$env:TEMP\redis-temp\redis-cli.exe" "$BIN_DIR\"

    Remove-Item "$env:TEMP\redis.zip"
    Remove-Item "$env:TEMP\redis-temp" -Recurse

    Write-Host "Redis installed successfully!"
}

# Write versions file
function Write-Versions {
    $versions = @{
        php = $PHP_VERSION
        redis = $REDIS_VERSION
        platform = "win32-x64"
        timestamp = (Get-Date -Format "yyyy-MM-ddTHH:mm:ssZ")
    }

    $versions | ConvertTo-Json | Out-File "$BIN_DIR\versions.json" -Encoding utf8
    Write-Host "Versions file written."
}

# Main
Download-PHP
Write-Host ""
Download-Redis
Write-Host ""
Write-Versions

Write-Host ""
Write-Host "==================================="
Write-Host "Setup complete!"
Write-Host "Binaries installed to: $BIN_DIR"
Write-Host "==================================="
