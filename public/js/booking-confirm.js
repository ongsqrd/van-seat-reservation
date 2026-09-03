/**
 * Booking confirmation dialog.
 *
 * Intercepts the Confirm Booking submit and opens the native <dialog>
 * instead of navigating. Loaded with defer, so the DOM is ready.
 *
 * If <dialog> is unsupported, or this file fails to load, the form
 * submits normally to booking-success.php. Nothing breaks.
 */

const form = document.querySelector('.confirm-form');
const dialog = document.getElementById('bookingConfirmed');

if (form && dialog && typeof dialog.showModal === 'function') {

    form.addEventListener('submit', (event) => {
        // the browser has already run required-field validation by now
        event.preventDefault();
        dialog.showModal();
    });

    // the booking is done — Escape should not drop them back on the form
    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
    });
}