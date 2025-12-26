#!/bin/bash
#
# Setup PHP and Redis binaries for Electron packaging
# Supports macOS (arm64/x64)
#

set -e

PHP_VERSION="8.4.1"
REDIS_VERSION="8.0.0"

# Detect platform
OS=$(uname -s | tr '[:upper:]' '[:lower:]')
ARCH=$(uname -m)

case "$OS-$ARCH" in
    darwin-arm64)
        PLATFORM="darwin-arm64"
        PHP_URL="https://dl.static-php.dev/static-php-cli/common/php-${PHP_VERSION}-cli-macos-aarch64.tar.gz"
        ;;
    darwin-x86_64)
        PLATFORM="darwin-x64"
        PHP_URL="https://dl.static-php.dev/static-php-cli/common/php-${PHP_VERSION}-cli-macos-x86_64.tar.gz"
        ;;
    *)
        echo "Unsupported platform: $OS-$ARCH"
        echo "This script supports macOS arm64 and x64"
        exit 1
        ;;
esac

BIN_DIR="./electron/resources/bin/$PLATFORM"
mkdir -p "$BIN_DIR"

echo "Setting up binaries for $PLATFORM..."
echo ""

# Download PHP
download_php() {
    echo "Downloading PHP $PHP_VERSION..."

    if [ -f "$BIN_DIR/php" ]; then
        echo "PHP already exists, skipping..."
        return
    fi

    curl -L -o /tmp/php.tar.gz "$PHP_URL"
    tar -xzf /tmp/php.tar.gz -C "$BIN_DIR"
    chmod +x "$BIN_DIR/php"
    rm /tmp/php.tar.gz

    # Verify PHP
    "$BIN_DIR/php" --version
    echo "PHP installed successfully!"
}

# Setup Redis
setup_redis() {
    echo "Setting up Redis..."

    if [ -f "$BIN_DIR/redis-server" ]; then
        echo "Redis already exists, skipping..."
        return
    fi

    # Try to copy from Homebrew first
    if [ -f "/opt/homebrew/bin/redis-server" ]; then
        echo "Copying Redis from Homebrew..."
        cp /opt/homebrew/bin/redis-server "$BIN_DIR/"
        cp /opt/homebrew/bin/redis-cli "$BIN_DIR/" 2>/dev/null || true
    elif [ -f "/usr/local/bin/redis-server" ]; then
        echo "Copying Redis from /usr/local/bin..."
        cp /usr/local/bin/redis-server "$BIN_DIR/"
        cp /usr/local/bin/redis-cli "$BIN_DIR/" 2>/dev/null || true
    else
        echo "Redis not found in Homebrew. Please install Redis first:"
        echo "  brew install redis"
        echo ""
        echo "Or build from source:"
        echo "  1. Download Redis from https://redis.io/download"
        echo "  2. Extract and run: make"
        echo "  3. Copy redis-server to $BIN_DIR/"
        exit 1
    fi

    chmod +x "$BIN_DIR/redis-server"
    echo "Redis installed successfully!"
}

# Write versions file
write_versions() {
    cat > "$BIN_DIR/versions.json" << EOF
{
    "php": "$PHP_VERSION",
    "redis": "$REDIS_VERSION",
    "platform": "$PLATFORM",
    "timestamp": "$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
}
EOF
    echo "Versions file written."
}

# Main
echo "==================================="
echo "Electron Binary Setup"
echo "==================================="
echo ""

download_php
echo ""
setup_redis
echo ""
write_versions

echo ""
echo "==================================="
echo "Setup complete!"
echo "Binaries installed to: $BIN_DIR"
echo "==================================="
