#!/bin/bash

# Script to sync public folders from laravel/public to project root
# Usage: ./sync-public-folders.sh

set -e  # Exit on error

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get the script's directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Define source and destination
SOURCE_DIR="$SCRIPT_DIR/laravel/public"
DEST_DIR="$SCRIPT_DIR"

# Folders to sync
FOLDERS=("css" "fonts" "images" "js")

echo -e "${BLUE}Starting sync from laravel/public to root...${NC}"
echo ""

# Check if source directory exists
if [ ! -d "$SOURCE_DIR" ]; then
    echo "Error: Source directory $SOURCE_DIR does not exist!"
    exit 1
fi

# Sync each folder
for folder in "${FOLDERS[@]}"; do
    if [ -d "$SOURCE_DIR/$folder" ]; then
        echo -e "${BLUE}Syncing $folder...${NC}"
        
        # Remove existing folder in root if it exists
        if [ -d "$DEST_DIR/$folder" ]; then
            rm -rf "$DEST_DIR/$folder"
        fi
        
        # Copy folder from public to root
        cp -r "$SOURCE_DIR/$folder" "$DEST_DIR/$folder"
        
        echo -e "${GREEN}✓ $folder synced successfully${NC}"
    else
        echo "Warning: $SOURCE_DIR/$folder does not exist, skipping..."
    fi
done

echo ""
echo -e "${GREEN}All folders synced successfully!${NC}"

