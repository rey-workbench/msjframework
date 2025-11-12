#!/bin/bash

# Script untuk setup Git repository dan push ke GitHub
# Usage: ./setup-git.sh

set -e

# Get script directory and change to it
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR" || exit 1

echo "🚀 Setup Git Repository untuk MSJ Framework"
echo "============================================"
echo ""
echo "Working directory: $(pwd)"
echo ""

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Validasi bahwa composer.json ada
if [ ! -f "composer.json" ]; then
    echo -e "${RED}✗${NC} composer.json tidak ditemukan!"
    echo -e "${RED}✗${NC} Pastikan script dijalankan dari direktori package yang berisi composer.json"
    echo -e "${RED}✗${NC} Current directory: $(pwd)"
    exit 1
fi

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
    echo ""
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
    
    # Fetch tags dari remote
    echo -e "${GREEN}✓${NC} Fetching tags dari remote..."
    git fetch --tags origin 2>/dev/null || true
    
    # Cek tag terakhir
    LAST_TAG=$(git tag --sort=-v:refname | head -n 1)
    
    if [ -z "$LAST_TAG" ]; then
        # Tidak ada tag, buat tag pertama
        NEW_TAG="v1.0.0"
        echo -e "${GREEN}✓${NC} Tidak ada tag sebelumnya, akan membuat tag pertama: $NEW_TAG"
        read -p "Apakah Anda ingin membuat tag $NEW_TAG? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git tag -a "$NEW_TAG" -m "Release version $NEW_TAG"
            git push origin "$NEW_TAG"
            echo -e "${GREEN}✓${NC} Tag $NEW_TAG berhasil dibuat dan di-push"
        fi
    else
        # Ada tag sebelumnya, increment version
        echo -e "${GREEN}✓${NC} Tag terakhir ditemukan: $LAST_TAG"
        
        # Extract version number (remove 'v' prefix)
        VERSION=${LAST_TAG#v}
        
        # Parse version parts (MAJOR.MINOR.PATCH)
        IFS='.' read -ra VERSION_PARTS <<< "$VERSION"
        MAJOR=${VERSION_PARTS[0]:-0}
        MINOR=${VERSION_PARTS[1]:-0}
        PATCH=${VERSION_PARTS[2]:-0}
        
        # Tanya user ingin increment apa
        echo ""
        echo "Pilih jenis release:"
        echo "1) Patch (v$MAJOR.$MINOR.$((PATCH + 1))) - Bug fixes"
        echo "2) Minor (v$MAJOR.$((MINOR + 1)).0) - New features"
        echo "3) Major (v$((MAJOR + 1)).0.0) - Breaking changes"
        echo "4) Custom version"
        echo "5) Skip (tidak buat tag)"
        read -p "Pilihan [1-5] (default: 1): " VERSION_CHOICE
        VERSION_CHOICE=${VERSION_CHOICE:-1}
        
        case $VERSION_CHOICE in
            1)
                NEW_PATCH=$((PATCH + 1))
                NEW_TAG="v$MAJOR.$MINOR.$NEW_PATCH"
                RELEASE_TYPE="Patch"
                ;;
            2)
                NEW_MINOR=$((MINOR + 1))
                NEW_TAG="v$MAJOR.$NEW_MINOR.0"
                RELEASE_TYPE="Minor"
                ;;
            3)
                NEW_MAJOR=$((MAJOR + 1))
                NEW_TAG="v$NEW_MAJOR.0.0"
                RELEASE_TYPE="Major"
                ;;
            4)
                read -p "Masukkan versi baru (format: v1.2.3): " NEW_TAG
                # Validasi format
                if [[ ! $NEW_TAG =~ ^v[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
                    echo -e "${RED}✗${NC} Format versi tidak valid! Harus dalam format v1.2.3"
                    NEW_TAG=""
                fi
                RELEASE_TYPE="Custom"
                ;;
            5)
                NEW_TAG=""
                echo -e "${YELLOW}⚠️  Tag creation di-skip${NC}"
                ;;
            *)
                NEW_PATCH=$((PATCH + 1))
                NEW_TAG="v$MAJOR.$MINOR.$NEW_PATCH"
                RELEASE_TYPE="Patch"
                ;;
        esac
        
        if [ -n "$NEW_TAG" ]; then
            # Cek jika tag sudah ada
            if git tag -l | grep -q "^$NEW_TAG$"; then
                echo -e "${RED}✗${NC} Tag $NEW_TAG sudah ada!"
                echo -e "${YELLOW}⚠️  Tag tidak bisa dibuat karena sudah ada di repository${NC}"
                echo -e "${YELLOW}⚠️  Silakan pilih versi lain atau skip${NC}"
                NEW_TAG=""
            fi
            
            if [ -n "$NEW_TAG" ]; then
                # Buat tag baru
                DEFAULT_MESSAGE="Release $RELEASE_TYPE version $NEW_TAG"
                read -p "Masukkan release message (default: '$DEFAULT_MESSAGE'): " TAG_MESSAGE
                if [ -z "$TAG_MESSAGE" ]; then
                    TAG_MESSAGE="$DEFAULT_MESSAGE"
                fi
                
                echo -e "${GREEN}✓${NC} Membuat tag $NEW_TAG..."
                git tag -a "$NEW_TAG" -m "$TAG_MESSAGE"
                
                echo -e "${GREEN}✓${NC} Pushing tag $NEW_TAG ke remote..."
                git push origin "$NEW_TAG"
                
                echo -e "${GREEN}✓${NC} Tag $NEW_TAG berhasil dibuat dan di-push"
                echo -e "${GREEN}✓${NC} Release message: $TAG_MESSAGE"
            fi
        fi
    fi
else
    echo -e "${YELLOW}⚠️  Push di-skip. Anda bisa push manual dengan:${NC}"
    echo "  git push -u origin main"
fi

echo ""
echo -e "${GREEN}✅ Setup selesai!${NC}"
echo ""

# Tampilkan informasi repository
if git remote | grep -q "^origin$"; then
    REPO_URL=$(git remote get-url origin)
    echo "Repository URL: $REPO_URL"
    echo ""
    echo "Langkah selanjutnya:"
    echo "1. Submit package ke Packagist: https://packagist.org/packages/submit"
    echo "2. Masukkan URL repository: $REPO_URL"
    echo "3. Setup webhook untuk auto-update (lihat PUSH_TO_PACKAGIST.md)"
else
    echo "⚠️  Remote 'origin' belum di-set"
    echo "Langkah selanjutnya:"
    echo "1. Tambahkan remote: git remote add origin <URL>"
    echo "2. Push ke GitHub: git push -u origin main"
    echo "3. Submit package ke Packagist: https://packagist.org/packages/submit"
fi

echo ""
echo "Lihat PUSH_TO_PACKAGIST.md untuk panduan lengkap."

