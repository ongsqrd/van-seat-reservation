<?php
$from = "";
$to = "";
$dropoff = "";
$date = "";
$boardingTime = "";
$passengerName = "";
$totalPrice = "";
$plateNumber = "";
$seatsBooked = "";
$bookingId = "";
$referenceNo = "";
$bookedAt = "";
$bookedTime = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AU VAN - Ticket</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="ticket-container">

        <h1>Booking Details</h1>

        <div class="booking-grid">

            <div class="detail">
                <label>From</label>
                <p><?= $from ?></p>
            </div>

            <div class="detail">
                <label>To</label>
                <p><?= $to ?></p>
            </div>

            <div class="detail">
                <label>Drop-off</label>
                <p><?= $dropoff ?></p>
            </div>

            <div class="detail">
                <label>Date</label>
                <p><?= $date ?></p>
            </div>

            <div class="detail">
                <label>Boarding</label>
                <p><?= $boardingTime ?></p>
            </div>

            <div class="detail">
                <label>Passenger Name</label>
                <p><?= $passengerName ?></p>
            </div>

            <div class="detail">
                <label>Total Price</label>
                <p>฿<?= $totalPrice ?></p>
            </div>

            <div class="detail">
                <label>Plate Number</label>
                <p><?= $plateNumber ?></p>
            </div>

            <div class="detail">
                <label>Seats Booked</label>
                <p><?= $seatsBooked ?></p>
            </div>

            <div class="detail">
                <label>Booking ID</label>
                <p><?= $bookingId ?></p>
            </div>

            <div class="detail">
                <label>Reference No.</label>
                <p><?= $referenceNo ?></p>
            </div>

            <div class="detail">
                <label>Booked At</label>
                <p><?= $bookedAt ?></p>
            </div>

            <div class="detail">
                <label>Time</label>
                <p><?= $bookedTime ?></p>
            </div>

        </div>

        <div class="button-container">

            <button type="button" class="back-button">
                Back to Home
            </button>

            <button type="button" class="ticket-button">
                Show Ticket
            </button>

        </div>

    </div>

    <div class="ticket-popup">

        <div class="ticket-box">

            <h1>Ticket</h1>

            <p>Scan your QR code when boarding the van</p>

            <p>Booking ID: <?= $bookingId ?></p>

            <p>Reference Number: <?= $referenceNo ?></p>

            <button type="button">Back</button>

        </div>
    </div>
</body>

</html>