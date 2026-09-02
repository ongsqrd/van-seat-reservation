<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU VAN - Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="booking-wrapper">

        <div class="booking-container">

            <div class="booking-summary">

                <h2>Booking Summary</h2>

                <div class="booking-info">
                    <p>From</p>
                    <p class="booking-value">Assumption University</p>
                </div>

                <div class="booking-info">
                    <p>To</p>
                    <p class="booking-value">Suvarnabhumi Airport</p>
                </div>

                <div class="booking-info">
                    <p>Date</p>
                    <p class="booking-value">10 September 2026</p>
                </div>

                <div class="booking-info">
                    <p>Time</p>
                    <p class="booking-value">10:00 AM</p>
                </div>

                <div class="booking-info">
                    <p>Passenger</p>
                    <p class="booking-value">2 Persons</p>
                </div>

                <div class="booking-info">
                    <p>Van</p>
                    <p class="booking-value">AU Van</p>
                </div>

                <div class="total">
                    <p>Total</p>
                    <p class="total-price">100 Baht</p>
                </div>

            </div>

            <div class="payment">

                <h2>Payment</h2>

                <div class="payment-method">
                    <button type="button" class="payment-button">PromptPay</button>
                    <button type="button" class="payment-button">Credit Card</button>
                </div>

                <div class="promptpay-payment">

                    <h3>PromptPay</h3>

                    <div class="qr-code">
                        QR Code
                    </div>

                    <p>Scan QR Code to pay</p>

                    <div class="upload-slip">
                        <p>Payment Slip</p>
                        <input type="file" id="payment-slip" name="payment-slip">
                    </div>
                </div>

                <div class="card-payment">

                    <h3>Credit Card</h3>

                    <div class="input-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card-number"
                            placeholder="Enter card number">
                    </div>

                    <div class="input-group">
                        <label for="card-name">Card Holder Name</label>
                        <input type="text" id="card-name" name="card-name"
                            placeholder="Enter card holder name">
                    </div>

                    <div class="card-row">

                        <div class="input-group">
                            <label for="expiry-date">Expiry Date</label>
                            <input type="text" id="expiry-date" name="expiry-date" placeholder="MM/YY">
                        </div>

                        <div class="input-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="CVV">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="button-container">
            <button type="button" class="cancel-button">Cancel</button>
            <button type="button" class="confirm-button">Confirm Booking</button>
        </div>
    </div>
</body>
</html>