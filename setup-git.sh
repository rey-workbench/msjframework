#!/bin/bash

# Script untuk setup Git repository dan push ke GitHub
# Usage: ./setup-git.sh

set -e

echo "🚀 Setup Git Repository untuk MSJ Framework"
echo "============================================"
echo ""

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check jika git sudah diinisialisasi
if [ -d ".git" ]; then
    echo -e "${YELLOW}⚠️  Git repository sudah diinisialisasi${NC}"
    read -p "Apakah Anda ingin melanjutkan? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
else
    echo -e "${GREEN}✓${NC} Menginisialisasi Git repository..."
    git init
fi

# Validasi composer.json
echo -e "${GREEN}✓${NC} Memvalidasi composer.json..."
if ! composer validate > /dev/null 2>&1; then
    echo -e "${RED}✗${NC} composer.json tidak valid!"
    composer validate
    exit 1
fi
echo -e "${GREEN}✓${NC} composer.json valid"

# Check jika remote sudah ada
if git remote | grep -q "^origin$"; then
    echo -e "${YELLOW}⚠️  Remote 'origin' sudah ada${NC}"
    CURRENT_URL=$(git remote get-url origin)
    echo "Current URL: $CURRENT_URL"
    read -p "Apakah Anda ingin mengubah URL? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        read -p "Masukkan URL repository GitHub: " GITHUB_URL
        git remote set-url origin "$GITHUB_URL"
    fi
else
    read -p "Masukkan URL repository GitHub (https://github.com/rey-workbench/msjframework.git): " GITHUB_URL
    if [ -z "$GITHUB_URL" ]; then
        GITHUB_URL="https://github.com/rey-workbench/msjframework.git"
    fi
    git remote add origin "$GITHUB_URL"
    echo -e "${GREEN}✓${NC} Remote 'origin' ditambahkan: $GITHUB_URL"
fi

# Add semua file
echo -e "${GREEN}✓${NC} Menambahkan file ke staging..."
git add .

# Check jika ada perubahan
if git diff --cached --quiet; then
    echo -e "${YELLOW}⚠️  Tidak ada perubahan untuk di-commit${NC}"
else
    # Commit
    read -p "Masukkan commit message (default: 'Initial commit: MSJ Framework v1.0.0'): " COMMIT_MSG
    if [ -z "$COMMIT_MSG" ]; then
        COMMIT_MSG="Initial commit: MSJ Framework v1.0.0"
    fi
    git commit -m "$COMMIT_MSG"
    echo -e "${GREEN}✓${NC} Commit dibuat: $COMMIT_MSG"
fi

# Set branch ke main
echo -e "${GREEN}✓${NC} Mengatur branch ke 'main'..."
git branch -M main

# Push ke GitHub
echo -e "${GREEN}✓${NC} Push ke GitHub..."
read -p "Apakah Anda ingin push ke GitHub sekarang? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    git push -u origin main
    echo -e "${GREEN}✓${NC} Code berhasil di-push ke GitHub"
    
    # Buat tag
    read -p "Apakah Anda ingin membuat tag v1.0.0? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git tag -a v1.0.0 -m "Release version 1.0.0"
        git push origin v1.0.0
        echo -e "${GREEN}✓${NC} Tag v1.0.0 berhasil dibuat dan di-push"
    fi
else
    echo -e "${YELLOW}⚠️  Push di-skip. Anda bisa push manual dengan:${NC}"
    echo "  git push -u origin main"
fi

echo ""
echo -e "${GREEN}✅ Setup selesai!${NC}"
echo ""
echo "Langkah selanjutnya:"
echo "1. Submit package ke Packagist: https://packagist.org/packages/submit"
echo "2. Masukkan URL repository: $(git remote get-url origin)"
echo "3. Setup webhook untuk auto-update (lihat PUSH_TO_PACKAGIST.md)"
echo ""
echo "Lihat PUSH_TO_PACKAGIST.md untuk panduan lengkap."

