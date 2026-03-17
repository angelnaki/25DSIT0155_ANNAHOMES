<?php
session_start();
require 'connect.php';

// Get property details from URL
$property = isset($_GET['property']) ? $_GET['property'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : 0;
$location = isset($_GET['location']) ? $_GET['location'] : '';

// If no property specified, redirect to home
if(empty($property)) {
    header("Location: index.php");
    exit();
}

// Format property name for display
$property_name = str_replace('-', ' ', $property);
$property_name = ucwords($property_name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo $property_name; ?> · ANNA HOMES</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        :root {
            --deep-navy: #1A2B3C;
            --soft-white: #F9F7F4;
            --champagne: #F7E6D0;
            --burgundy: #8B3A3A;
            --pure-white: #FFFFFF;
            --shadow: 0 25px 50px -12px rgba(26,43,60,0.2);
            --border-radius-card: 20px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--soft-white);
            color: var(--deep-navy);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navbar */
        .navbar {
            background: rgba(26, 43, 60, 0.85);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            color: var(--soft-white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(247,230,208,0.2);
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 1.8rem;
            color: var(--champagne);
            font-weight: 700;
            font-style: italic;
        }

        .nav-links a {
            color: var(--soft-white);
            text-decoration: none;
            margin-left: 20px;
            transition: var(--transition);
        }

        .nav-links a:hover {
            color: var(--champagne);
        }

        /* Booking Form */
        .booking-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 50px 0;
        }

        .property-summary {
            background: var(--pure-white);
            border-radius: var(--border-radius-card);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--champagne);
        }

        .property-summary h2 {
            color: var(--deep-navy);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--champagne);
            padding-bottom: 10px;
        }

        .property-detail {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 15px 0;
            font-size: 1.1rem;
        }

        .property-detail i {
            color: var(--burgundy);
            width: 25px;
            font-size: 1.2rem;
        }

        .price-big {
            font-size: 2rem;
            color: var(--burgundy);
            font-weight: 600;
            margin: 20px 0;
        }

        .booking-form {
            background: var(--pure-white);
            border-radius: var(--border-radius-card);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--champagne);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--deep-navy);
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--champagne);
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--soft-white);
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--burgundy);
        }

        .date-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Payment Methods */
        .payment-methods {
            margin: 30px 0;
        }

        .payment-option {
            border: 2px solid var(--champagne);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: var(--transition);
        }

        .payment-option:hover {
            border-color: var(--burgundy);
            background: rgba(139,58,58,0.05);
        }

        .payment-option.selected {
            border-color: var(--burgundy);
            background: rgba(139,58,58,0.1);
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-option label {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            margin: 0;
        }

        .payment-icon {
            font-size: 2rem;
            color: var(--burgundy);
            width: 50px;
            text-align: center;
        }

        .payment-details {
            flex: 1;
        }

        .payment-details h4 {
            color: var(--deep-navy);
            margin-bottom: 5px;
        }

        .payment-details p {
            color: var(--deep-navy);
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .mobile-money-details, .bank-details {
            margin-top: 15px;
            padding: 15px;
            background: var(--soft-white);
            border-radius: 10px;
            display: none;
        }

        .mobile-money-details.active, .bank-details.active {
            display: block;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--champagne);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--deep-navy);
        }

        .detail-value {
            color: var(--burgundy);
            font-weight: 500;
        }

        .total-section {
            margin: 30px 0;
            padding: 20px;
            background: var(--soft-white);
            border-radius: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .grand-total {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--burgundy);
            border-top: 2px solid var(--champagne);
            padding-top: 15px;
            margin-top: 15px;
        }

        .book-now-btn {
            width: 100%;
            padding: 15px;
            background: var(--burgundy);
            color: white;
            border: none;
            border-radius: 40px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .book-now-btn:hover {
            background: #9f4545;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139,58,58,0.3);
        }

        .secure-badge {
            text-align: center;
            margin-top: 20px;
            color: var(--deep-navy);
            opacity: 0.7;
        }

        .secure-badge i {
            color: var(--burgundy);
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .booking-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1>ANNA HOMES</h1>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="booking-container">
            <!-- Property Summary -->
            <div class="property-summary">
                <h2>Your Stay</h2>
                <div class="property-detail">
                    <i class="fas fa-home"></i>
                    <span><?php echo $property_name; ?></span>
                </div>
                <div class="property-detail">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo $location; ?></span>
                </div>
                <div class="property-detail">
                    <i class="fas fa-star" style="color: #FFD700;"></i>
                    <span>4.9 · Superhost</span>
                </div>
                <div class="price-big">
                    $<?php echo $price; ?> <small style="font-size: 1rem; opacity: 0.7;">/ night</small>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3 style="margin-bottom: 15px;">What this place offers</h3>
                    <div class="property-detail">
                        <i class="fas fa-wifi"></i>
                        <span>Fast WiFi</span>
                    </div>
                    <div class="property-detail">
                        <i class="fas fa-utensils"></i>
                        <span>Fully equipped kitchen</span>
                    </div>
                    <div class="property-detail">
                        <i class="fas fa-shield-alt"></i>
                        <span>24/7 Security</span>
                    </div>
                    <div class="property-detail">
                        <i class="fas fa-parking"></i>
                        <span>Free parking</span>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="booking-form">
                <h2 style="margin-bottom: 20px;">Complete your booking</h2>
                
                <form action="process_booking.php" method="POST" id="bookingForm">
                    <input type="hidden" name="property" value="<?php echo $property; ?>">
                    <input type="hidden" name="property_name" value="<?php echo $property_name; ?>">
                    <input type="hidden" name="price_per_night" value="<?php echo $price; ?>">
                    <input type="hidden" name="location" value="<?php echo $location; ?>">

                    <!-- Guest Information -->
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="guest_name" required placeholder="Enter your full name">
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="your@email.com">
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" required placeholder="+256 XXX XXX XXX">
                    </div>

                    <!-- Dates -->
                    <div class="date-group">
                        <div class="form-group">
                            <label>Check-in Date</label>
                            <input type="date" name="checkin" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Check-out Date</label>
                            <input type="date" name="checkout" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                    </div>

                    <!-- Guests -->
                    <div class="form-group">
                        <label>Number of Guests</label>
                        <select name="guests" required>
                            <option value="1">1 Guest</option>
                            <option value="2">2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5 Guests</option>
                            <option value="6">6+ Guests</option>
                        </select>
                    </div>

                    <!-- Special Requests -->
                    <div class="form-group">
                        <label>Special Requests (Optional)</label>
                        <textarea name="requests" rows="3" placeholder="Any special requirements?"></textarea>
                    </div>

                    <!-- Payment Methods -->
                    <h3 style="margin: 20px 0 15px;">Choose Payment Method</h3>
                    
                    <div class="payment-methods">
                        <!-- Mobile Money Option -->
                        <div class="payment-option" onclick="selectPayment('mobile')">
                            <input type="radio" name="payment_method" id="mobile" value="mobile_money" required>
                            <label for="mobile">
                                <div class="payment-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="payment-details">
                                    <h4>Mobile Money</h4>
                                    <p>Pay with MTN or Airtel Money</p>
                                </div>
                            </label>
                            <div class="mobile-money-details" id="mobileDetails">
                                <div class="detail-row">
                                    <span class="detail-label">MTN Number:</span>
                                    <span class="detail-value">+256 786 749299</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Airtel Number:</span>
                                    <span class="detail-value">+256 786 749299</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Account Name:</span>
                                    <span class="detail-value">ANNA HOMES LTD</span>
                                </div>
                                <p style="margin-top: 10px; font-size: 0.9rem; color: var(--deep-navy);">
                                    <i class="fas fa-info-circle" style="color: var(--burgundy);"></i> 
                                    You'll receive payment instructions after booking
                                </p>
                            </div>
                        </div>

                        <!-- Bank Transfer Option -->
                        <div class="payment-option" onclick="selectPayment('bank')">
                            <input type="radio" name="payment_method" id="bank" value="bank_transfer">
                            <label for="bank">
                                <div class="payment-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-details">
                                    <h4>Bank Transfer</h4>
                                    <p>Direct bank transfer</p>
                                </div>
                            </label>
                            <div class="bank-details" id="bankDetails">
                                <div class="detail-row">
                                    <span class="detail-label">Bank:</span>
                                    <span class="detail-value">Stanbic Bank Uganda</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Account Name:</span>
                                    <span class="detail-value">ANNA HOMES LTD</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Account Number:</span>
                                    <span class="detail-value">9030012345678</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Branch:</span>
                                    <span class="detail-value">Kampala Main</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">SWIFT Code:</span>
                                    <span class="detail-value">SBICUGKX</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Payment Option -->
                        <div class="payment-option" onclick="selectPayment('card')">
                            <input type="radio" name="payment_method" id="card" value="card">
                            <label for="card">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-details">
                                    <h4>Credit / Debit Card</h4>
                                    <p>Visa, Mastercard, American Express</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Total Calculation -->
                    <div class="total-section">
                        <div class="total-row">
                            <span>$<?php echo $price; ?> x 1 night</span>
                            <span>$<?php echo $price; ?></span>
                        </div>
                        <div class="total-row">
                            <span>Service fee</span>
                            <span>$15</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Total (USD)</span>
                            <span>$<?php echo $price + 15; ?></span>
                        </div>
                    </div>

                    <button type="submit" class="book-now-btn">
                        <i class="fas fa-lock" style="margin-right: 10px;"></i>Confirm & Book
                    </button>
                    
                    <div class="secure-badge">
                        <i class="fas fa-shield-alt"></i> Your payment information is secure
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectPayment(method) {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            if(method === 'mobile') {
                document.querySelector('input#mobile').checked = true;
                document.querySelector('.payment-option:has(#mobile)').classList.add('selected');
                document.getElementById('mobileDetails').classList.add('active');
                document.getElementById('bankDetails').classList.remove('active');
            } else if(method === 'bank') {
                document.querySelector('input#bank').checked = true;
                document.querySelector('.payment-option:has(#bank)').classList.add('selected');
                document.getElementById('bankDetails').classList.add('active');
                document.getElementById('mobileDetails').classList.remove('active');
            } else if(method === 'card') {
                document.querySelector('input#card').checked = true;
                document.querySelector('.payment-option:has(#card)').classList.add('selected');
                document.getElementById('mobileDetails').classList.remove('active');
                document.getElementById('bankDetails').classList.remove('active');
            }
        }

        // Calculate nights and update total
        document.querySelector('input[name="checkin"]').addEventListener('change', updateTotal);
        document.querySelector('input[name="checkout"]').addEventListener('change', updateTotal);

        function updateTotal() {
            const checkin = new Date(document.querySelector('input[name="checkin"]').value);
            const checkout = new Date(document.querySelector('input[name="checkout"]').value);
            const pricePerNight = <?php echo $price; ?>;
            
            if(checkin && checkout && checkout > checkin) {
                const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
                const subtotal = nights * pricePerNight;
                const serviceFee = 15;
                const total = subtotal + serviceFee;
                
                // Update display
                document.querySelector('.total-row:first-child span:first-child').textContent = 
                    `$${pricePerNight} x ${nights} night${nights > 1 ? 's' : ''}`;
                document.querySelector('.total-row:first-child span:last-child').textContent = 
                    `$${subtotal}`;
                document.querySelector('.grand-total span:last-child').textContent = 
                    `$${total}`;
            }
        }
    </script>
</body>
</html>