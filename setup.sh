#!/bin/bash

echo "Setting up POS System..."
echo

if [ -f .env ]; then
    echo ".env file already exists!"
    echo "If you want to recreate it, delete the .env file first."
    exit 1
fi

echo "Copying environment configuration..."
cp env-simple.txt .env

echo
echo "Setup complete!"
echo
echo "Next steps:"
echo "1. Edit .env file and update database settings"
echo "2. Visit http://localhost/pos/setup in your browser"
echo "3. Follow the setup wizard"
echo 