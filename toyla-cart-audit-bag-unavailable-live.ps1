$ErrorActionPreference = 'Stop'

$products = @{
    Classic = 'https://toyla.ge/product/bilibo-classic-mix-%e1%83%91%e1%83%98%e1%83%9a%e1%83%98%e1%83%91%e1%83%9d-%e1%83%99%e1%83%9a%e1%83%90%e1%83%a1%e1%83%98%e1%83%99%e1%83%a3%e1%83%a0%e1%83%98-%e1%83%9b%e1%83%98%e1%83%a5%e1%83%a1/'
    Pastel = 'https://toyla.ge/product/bilibo-pastel-mix-%e1%83%91%e1%83%98%e1%83%9a%e1%83%98%e1%83%91%e1%83%9d-%e1%83%9e%e1%83%90%e1%83%a1%e1%83%a2%e1%83%94%e1%83%9a%e1%83%a3%e1%83%a0%e1%83%98-%e1%83%9b%e1%83%98%e1%83%a5%e1%83%a1/'
    Midi = 'https://toyla.ge/product/bilibo-classic-midi-%e1%83%91%e1%83%98%e1%83%9a%e1%83%98%e1%83%91%e1%83%9d-%e1%83%9b%e1%83%98%e1%83%93%e1%83%98-%e1%83%99%e1%83%9a%e1%83%90%e1%83%a1%e1%83%98%e1%83%99%e1%83%a3%e1%83%a0%e1%83%98/'
}

function Add-Item($Session, $Url, $Body) {
    Invoke-WebRequest -Uri $Url -Method Post -Body $Body -WebSession $Session -UseBasicParsing -TimeoutSec 60 | Out-Null
}

function Get-Total($Session) {
    $response = Invoke-WebRequest -Uri 'https://toyla.ge/cart/' -WebSession $Session -UseBasicParsing -TimeoutSec 60
    $text = [System.Net.WebUtility]::HtmlDecode(($response.Content -replace '<[^>]+>', ' ' -replace '\s+', ' '))
    $at = $text.IndexOf('შეკვეთების ჯამი')
    $totals = if ($at -ge 0) { $text.Substring($at, [Math]::Min(220, $text.Length - $at)) } else { '' }
    $match = [regex]::Match($totals, 'ჯამი\s+([\d\s]+)')
    if (!$match.Success) { return -1 }
    return [int](($match.Groups[1].Value -replace '\s', ''))
}

function Test-Scenario($Name, $Url, $Body, $Expected) {
    $session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    Add-Item $session $Url $Body
    $actual = Get-Total $session
    [PSCustomObject]@{ Scenario=$Name; Expected=$Expected; Actual=$actual; Pass=($actual -eq $Expected) }
}

$tests = @()
$tests += Test-Scenario 'Classic x2, submitted bag ignored' $products.Classic @{
    'add-to-cart'='2757'; product_id='2757'; variation_id='3438'
    attribute_pa_feri='stapilosperi'; toyla_bilibo_pack='single'; quantity='2'
    toyla_bilibo_bag='yes'; toyla_bilibo_bag_qty='3'
} 170
$tests += Test-Scenario 'Classic set x2, submitted bag ignored' $products.Classic @{
    'add-to-cart'='2757'; product_id='2757'; variation_id='3438'
    attribute_pa_feri='stapilosperi'; toyla_bilibo_pack='set6'; quantity='2'
    toyla_bilibo_bag='yes'; toyla_bilibo_bag_qty='3'
} 900
$tests += Test-Scenario 'Pastel x2 in stock' $products.Pastel @{
    'add-to-cart'='2769'; product_id='2769'; variation_id='3444'
    attribute_pa_feri='beige'; toyla_bilibo_pack='single'; quantity='2'
} 170
$tests += Test-Scenario 'MIDI x2 now in stock' $products.Midi @{
    'add-to-cart'='2794'; product_id='2794'; variation_id='3450'
    attribute_pa_feri='stapilosperi'; toyla_bilibo_pack='single'; quantity='2'
} 100
$tests += Test-Scenario 'MIDI set x2 now payable' $products.Midi @{
    'add-to-cart'='2794'; product_id='2794'; variation_id='3450'
    attribute_pa_feri='stapilosperi'; toyla_bilibo_pack='set6'; quantity='2'
    toyla_bilibo_bag='yes'; toyla_bilibo_bag_qty='3'
} 500

$tests | Format-Table -AutoSize
if (($tests | Where-Object { -not $_.Pass }).Count -gt 0) { exit 1 }
