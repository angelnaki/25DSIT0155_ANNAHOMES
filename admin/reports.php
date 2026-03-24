<?php
require_once 'includes/header.php';

// Date range filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Summary stats
$summary = $pdo->prepare("
    SELECT 
        COUNT(*) as total_bookings,
        SUM(guests) as total_guests,
        SUM(total) as total_revenue,
        AVG(total) as avg_booking_value,
        COUNT(DISTINCT property) as unique_properties
    FROM bookings 
    WHERE DATE(booking_date) BETWEEN ? AND ?
    AND status IN ('confirmed', 'completed')
");
$summary->execute([$start_date, $end_date]);
$stats = $summary->fetch();

// Daily breakdown
$daily = $pdo->prepare("
    SELECT 
        DATE(booking_date) as date,
        COUNT(*) as bookings,
        SUM(guests) as guests,
        SUM(total) as revenue
    FROM bookings 
    WHERE DATE(booking_date) BETWEEN ? AND ?
    AND status IN ('confirmed', 'completed')
    GROUP BY DATE(booking_date)
    ORDER BY date DESC
");
$daily->execute([$start_date, $end_date]);
$daily_data = $daily->fetchAll();

// Location breakdown
$locations = $pdo->prepare("
    SELECT 
        location,
        COUNT(*) as bookings,
        SUM(guests) as guests,
        SUM(total) as revenue
    FROM bookings 
    WHERE DATE(booking_date) BETWEEN ? AND ?
    AND status IN ('confirmed', 'completed')
    GROUP BY location
    ORDER BY revenue DESC
");
$locations->execute([$start_date, $end_date]);
$location_data = $locations->fetchAll();

// Payment methods breakdown
$payments = $pdo->prepare("
    SELECT 
        payment_method,
        COUNT(*) as count,
        SUM(total) as revenue
    FROM bookings 
    WHERE DATE(booking_date) BETWEEN ? AND ?
    AND status IN ('confirmed', 'completed')
    GROUP BY payment_method
");
$payments->execute([$start_date, $end_date]);
$payment_data = $payments->fetchAll();
?>

<h1 style="color: var(--deep-navy); margin-bottom: 20px;">Reports & Analytics</h1>

<!-- Date Range Filter -->
<div style="background: white; border-radius: 15px; padding: 20px; margin-bottom: 30px; box-shadow: var(--shadow);">
    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
        <div>
            <label style="display: block; margin-bottom: 5px;">Start Date</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" style="padding: 8px; border: 1px solid var(--champagne); border-radius: 8px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 5px;">End Date</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" style="padding: 8px; border: 1px solid var(--champagne); border-radius: 8px;">
        </div>
        <button type="submit" style="padding: 10px 20px; background: var(--burgundy); color: white; border: none; border-radius: 30px; cursor: pointer;">
            <i class="fas fa-filter"></i> Apply Filter
        </button>
        <a href="reports.php?export=1&start=<?php echo $start_date; ?>&end=<?php echo $end_date; ?>" style="padding: 10px 20px; background: var(--deep-navy); color: white; border-radius: 30px; text-decoration: none;">
            <i class="fas fa-download"></i> Export
        </a>
    </form>
</div>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: var(--shadow);">
        <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">Total Bookings</div>
        <div style="font-size: 2rem; font-weight: 600; color: var(--deep-navy);"><?php echo $stats['total_bookings'] ?? 0; ?></div>
    </div>
    
    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: var(--shadow);">
        <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">Total Guests</div>
        <div style="font-size: 2rem; font-weight: 600; color: var(--deep-navy);"><?php echo $stats['total_guests'] ?? 0; ?></div>
    </div>
    
    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: var(--shadow);">
        <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">Total Revenue</div>
        <div style="font-size: 2rem; font-weight: 600; color: var(--burgundy);">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></div>
    </div>
    
    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: var(--shadow);">
        <div style="color: #666; font-size: 0.9rem; margin-bottom: 5px;">Avg. Booking Value</div>
        <div style="font-size: 2rem; font-weight: 600; color: var(--deep-navy);">$<?php echo number_format($stats['avg_booking_value'] ?? 0, 2); ?></div>
    </div>
</div>

<!-- Two Column Layout -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
    <!-- Daily Breakdown -->
    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px;">Daily Breakdown</h2>
        
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Bookings</th>
                    <th>Guests</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($daily_data as $day): ?>
                <tr>
                    <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                    <td><?php echo $day['bookings']; ?></td>
                    <td><?php echo $day['guests']; ?></td>
                    <td><strong>$<?php echo number_format($day['revenue'], 2); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Location Breakdown -->
    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px;">By Location</h2>
        
        <?php foreach($location_data as $loc): ?>
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span><i class="fas fa-map-marker-alt" style="color: var(--burgundy);"></i> <?php echo $loc['location']; ?></span>
                <span><strong><?php echo $loc['bookings']; ?></strong> bookings</span>
            </div>
            <div style="height: 8px; background: #eee; border-radius: 4px;">
                <div style="width: <?php echo ($loc['bookings'] / $location_data[0]['bookings']) * 100; ?>%; height: 100%; background: var(--burgundy); border-radius: 4px;"></div>
            </div>
            <div style="font-size: 0.9rem; margin-top: 5px;">
                Revenue: $<?php echo number_format($loc['revenue'], 2); ?> | Guests: <?php echo $loc['guests']; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Payment Methods -->
<div style="background: white; border-radius: 15px; padding: 20px; box-shadow: var(--shadow);">
    <h2 style="margin-bottom: 20px;">Payment Methods</h2>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <?php foreach($payment_data as $pm): ?>
        <div style="text-align: center; padding: 20px; background: #f5f5f5; border-radius: 15px;">
            <i class="fas <?php 
                echo $pm['payment_method'] == 'mobile_money' ? 'fa-mobile-alt' : 
                    ($pm['payment_method'] == 'bank_transfer' ? 'fa-university' : 'fa-credit-card'); 
            ?>" style="font-size: 2rem; color: var(--burgundy); margin-bottom: 10px;"></i>
            <h3><?php echo str_replace('_', ' ', $pm['payment_method']); ?></h3>
            <p style="font-size: 1.2rem; font-weight: 600;"><?php echo $pm['count']; ?> bookings</p>
            <p style="color: var(--burgundy); font-weight: 600;">$<?php echo number_format($pm['revenue'], 2); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>