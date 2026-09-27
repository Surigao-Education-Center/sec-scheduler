[CmdletBinding(SupportsShouldProcess = $true, ConfirmImpact = 'Medium')]
param(
    [Parameter(Mandatory = $true, Position = 0)]
    [string] $TargetProjectPath,

    [string] $SourceModulePath = (Join-Path $PSScriptRoot '..\Modules\RoomScheduling'),

    [string] $TargetModulesPath = 'Modules',

    [switch] $Overwrite,

    [switch] $SkipComposerDump
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$targetRoot = [System.IO.Path]::GetFullPath($TargetProjectPath)
$sourceModule = [System.IO.Path]::GetFullPath($SourceModulePath)
$modulesRoot = [System.IO.Path]::GetFullPath((Join-Path $targetRoot $TargetModulesPath))
$destinationModule = Join-Path $modulesRoot 'RoomScheduling'
$composerPath = Join-Path $targetRoot 'composer.json'
$statusesPath = Join-Path $targetRoot 'modules_statuses.json'

if (-not (Test-Path -LiteralPath (Join-Path $targetRoot 'artisan') -PathType Leaf)) {
    throw "Target path is not a Laravel project (artisan was not found): $targetRoot"
}

if (-not (Test-Path -LiteralPath $composerPath -PathType Leaf)) {
    throw "Target project does not contain composer.json: $targetRoot"
}

if (-not (Test-Path -LiteralPath $sourceModule -PathType Container)) {
    throw "Source module directory was not found: $sourceModule"
}

$manifestPath = Join-Path $sourceModule 'module.json'
if (-not (Test-Path -LiteralPath $manifestPath -PathType Leaf)) {
    throw "Source module does not contain module.json: $sourceModule"
}

$manifest = Get-Content -LiteralPath $manifestPath -Raw | ConvertFrom-Json
if ($manifest.name -ne 'RoomScheduling') {
    throw "Expected the RoomScheduling module, found '$($manifest.name)'."
}

if (-not $SkipComposerDump -and -not $WhatIfPreference -and -not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw 'Composer was not found on PATH. Install Composer or rerun with -SkipComposerDump.'
}

function Backup-ExistingFile {
    param([Parameter(Mandatory = $true)][string] $Path)

    if (Test-Path -LiteralPath $Path -PathType Leaf) {
        $backupPath = "$Path.bak.$(Get-Date -Format 'yyyyMMddHHmmssfff')"
        Copy-Item -LiteralPath $Path -Destination $backupPath
        Write-Host "Backup created: $backupPath"
    }
}

$changed = $false
$conflicts = [System.Collections.Generic.List[string]]::new()
$sourceFiles = Get-ChildItem -LiteralPath $sourceModule -File -Recurse
$sourceRootPrefix = $sourceModule.TrimEnd('\', '/')

foreach ($sourceFile in $sourceFiles) {
    $relativePath = $sourceFile.FullName.Substring($sourceRootPrefix.Length).TrimStart('\', '/')
    $destinationPath = Join-Path $destinationModule $relativePath

    if (Test-Path -LiteralPath $destinationPath -PathType Leaf) {
        $sourceHash = (Get-FileHash -LiteralPath $sourceFile.FullName -Algorithm SHA256).Hash
        $destinationHash = (Get-FileHash -LiteralPath $destinationPath -Algorithm SHA256).Hash
        if ($sourceHash -eq $destinationHash) {
            continue
        }

        if (-not $Overwrite) {
            $conflicts.Add($relativePath)
            Write-Warning "Keeping existing module file with local changes: $destinationPath (use -Overwrite to replace it)"
            continue
        }

        if ($PSCmdlet.ShouldProcess($destinationPath, 'Replace module file')) {
            Backup-ExistingFile -Path $destinationPath
            Copy-Item -LiteralPath $sourceFile.FullName -Destination $destinationPath -Force
            $changed = $true
        }

        continue
    }

    if ($PSCmdlet.ShouldProcess($destinationPath, 'Import module file')) {
        $destinationDirectory = Split-Path -Parent $destinationPath
        New-Item -ItemType Directory -Path $destinationDirectory -Force | Out-Null
        Copy-Item -LiteralPath $sourceFile.FullName -Destination $destinationPath
        $changed = $true
    }
}

$composer = Get-Content -LiteralPath $composerPath -Raw | ConvertFrom-Json
$composerChanged = $false
if ($null -eq $composer.autoload) {
    $composer | Add-Member -NotePropertyName autoload -NotePropertyValue ([pscustomobject]@{})
    $composerChanged = $true
}

$psr4 = $composer.autoload.'psr-4'
if ($null -eq $psr4) {
    $psr4 = [pscustomobject]@{}
    $composer.autoload | Add-Member -NotePropertyName 'psr-4' -NotePropertyValue $psr4
    $composerChanged = $true
}

$autoloadMappings = @{
    'Modules\RoomScheduling\' = 'Modules/RoomScheduling/app/'
    'Modules\RoomScheduling\Database\Seeders\' = 'Modules/RoomScheduling/database/seeders/'
}

foreach ($mapping in $autoloadMappings.GetEnumerator()) {
    $existingMapping = $psr4.PSObject.Properties[$mapping.Key]
    if ($null -eq $existingMapping) {
        $psr4 | Add-Member -NotePropertyName $mapping.Key -NotePropertyValue $mapping.Value
        $composerChanged = $true
    } elseif ($existingMapping.Value -ne $mapping.Value) {
        Write-Warning "Composer already maps '$($mapping.Key)' to '$($existingMapping.Value)'; leaving that mapping unchanged."
    }
}

if ($composerChanged) {
    if ($PSCmdlet.ShouldProcess($composerPath, 'Merge module PSR-4 autoload mappings')) {
        Backup-ExistingFile -Path $composerPath
        $composerJson = ConvertTo-Json -InputObject $composer -Depth 100
        [System.IO.File]::WriteAllText($composerPath, "$composerJson`n", [System.Text.UTF8Encoding]::new($false))
        $changed = $true
    }
}

if (Test-Path -LiteralPath $statusesPath -PathType Leaf) {
    $statuses = Get-Content -LiteralPath $statusesPath -Raw | ConvertFrom-Json
} else {
    $statuses = [pscustomobject]@{}
}

$moduleStatus = $statuses.PSObject.Properties['RoomScheduling']
if ($null -eq $moduleStatus -or -not [bool] $moduleStatus.Value) {
    if ($null -eq $moduleStatus) {
        $statuses | Add-Member -NotePropertyName RoomScheduling -NotePropertyValue $true
    } else {
        $moduleStatus.Value = $true
    }

    if ($PSCmdlet.ShouldProcess($statusesPath, 'Enable RoomScheduling module')) {
        Backup-ExistingFile -Path $statusesPath
        $statusesJson = ConvertTo-Json -InputObject $statuses -Depth 100
        [System.IO.File]::WriteAllText($statusesPath, "$statusesJson`n", [System.Text.UTF8Encoding]::new($false))
        $changed = $true
    }
}

if ($changed -and -not $SkipComposerDump) {
    Push-Location $targetRoot
    try {
        & composer dump-autoload
        if ($LASTEXITCODE -ne 0) {
            throw "composer dump-autoload failed with exit code $LASTEXITCODE."
        }
    } finally {
        Pop-Location
    }
}

if ($conflicts.Count -gt 0) {
    Write-Warning "Import completed with $($conflicts.Count) existing file conflict(s) left unchanged. Review the paths above."
}

Write-Host 'RoomScheduling module import finished. Migrations and seeders were not run.'