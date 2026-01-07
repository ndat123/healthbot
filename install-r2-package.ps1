# PowerShell script to install R2 package using Docker (if PHP version is too old)

Write-Host "Checking PHP version..." -ForegroundColor Yellow
$phpVersion = php -v 2>&1 | Select-String "PHP (\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }

if ($phpVersion -ge "8.2") {
    Write-Host "PHP $phpVersion detected. Installing package directly..." -ForegroundColor Green
    composer require league/flysystem-aws-s3-v3
} else {
    Write-Host "PHP $phpVersion detected (need 8.2+). Using Docker to install package..." -ForegroundColor Yellow
    
    # Check if Docker is available
    $dockerAvailable = $false
    try {
        docker --version | Out-Null
        $dockerAvailable = $true
    } catch {
        Write-Host "Docker not found. Please install Docker Desktop or upgrade PHP to 8.2+" -ForegroundColor Red
        Write-Host ""
        Write-Host "Alternative: Install PHP 8.2+ from https://windows.php.net/download/" -ForegroundColor Yellow
        exit 1
    }
    
    if ($dockerAvailable) {
        Write-Host "Using Docker to install package..." -ForegroundColor Green
        docker run --rm -v ${PWD}:/app -w /app composer require league/flysystem-aws-s3-v3
        
        Write-Host ""
        Write-Host "Package installed! Now you can use R2 storage." -ForegroundColor Green
        Write-Host "Remember to configure R2 credentials in .env file." -ForegroundColor Yellow
    }
}



