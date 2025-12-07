# Script untuk Setup Git Repository
# Jalankan dengan: .\setup-git.ps1

Write-Host "=== Setup Git Repository ===" -ForegroundColor Cyan

# Navigate to project directory
Set-Location $PSScriptRoot

# Initialize Git repository
Write-Host "`n1. Initializing Git repository..." -ForegroundColor Yellow
if (Test-Path .git) {
    Write-Host "   Git repository already exists" -ForegroundColor Green
} else {
    git init
    Write-Host "   Git repository initialized" -ForegroundColor Green
}

# Configure Git user
Write-Host "`n2. Configuring Git user..." -ForegroundColor Yellow
git config user.name "zidannadiz"
git config user.email "zidannadiz@users.noreply.github.com"
Write-Host "   User configured: zidannadiz" -ForegroundColor Green

# Add remote origin
Write-Host "`n3. Adding remote origin..." -ForegroundColor Yellow
$remoteUrl = git config --get remote.origin.url
if ($remoteUrl) {
    Write-Host "   Remote already exists: $remoteUrl" -ForegroundColor Green
} else {
    git remote add origin https://github.com/zidannadiz/e-crm.git
    Write-Host "   Remote origin added: https://github.com/zidannadiz/e-crm.git" -ForegroundColor Green
}

# Verify configuration
Write-Host "`n4. Verifying configuration..." -ForegroundColor Yellow
Write-Host "   User Name: $(git config user.name)" -ForegroundColor Cyan
Write-Host "   User Email: $(git config user.email)" -ForegroundColor Cyan
Write-Host "   Remote URL: $(git config --get remote.origin.url)" -ForegroundColor Cyan

Write-Host "`n=== Setup Complete ===" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. git add ." -ForegroundColor White
Write-Host "2. git commit -m 'Initial commit'" -ForegroundColor White
Write-Host "3. git branch -M main" -ForegroundColor White
Write-Host "4. git push -u origin main" -ForegroundColor White
