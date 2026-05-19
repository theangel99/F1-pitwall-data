#!/bin/bash

# Sync all data for 2026 season races that have been completed

echo "=========================================="
echo "Syncing 2026 F1 Season Data"
echo "=========================================="
echo ""

# Array of all session keys for completed races
sessions=(
    # Australian GP
    11227 11228 11229 11230 11234
    # Chinese GP (Sprint)
    11235 11236 11240 11241 11245
    # Japanese GP
    11246 11247 11248 11249 11253
    # Bahrain GP
    11254 11255 11256 11257 11261
    # Saudi Arabian GP
    11262 11263 11264 11265 11269
    # Miami GP (Sprint)
    11270 11271 11275 11276 11280
)

total=${#sessions[@]}
current=0

for session in "${sessions[@]}"; do
    current=$((current + 1))
    echo ""
    echo "[$current/$total] Processing Session $session..."
    echo "----------------------------------------"

    # Sync drivers (creates driver records if not exist)
    echo "  → Syncing drivers..."
    php artisan openf1:sync drivers --session=$session

    # Sync laps
    echo "  → Syncing laps..."
    php artisan openf1:sync laps --session=$session

    echo "  ✓ Session $session completed"
done

echo ""
echo "=========================================="
echo "Processing queued jobs..."
echo "=========================================="
php artisan queue:work --stop-when-empty

echo ""
echo "=========================================="
echo "✓ All data synced successfully!"
echo "=========================================="
echo ""
echo "Summary:"
php artisan tinker --execute="
echo 'Drivers: ' . DB::table('drivers')->count() . PHP_EOL;
echo 'Laps: ' . DB::table('laps')->count() . PHP_EOL;
echo 'Constructors: ' . DB::table('constructors')->count() . PHP_EOL;
echo 'Sessions synced: ' . DB::table('f1_sessions')->whereHas('laps')->count() . PHP_EOL;
"
