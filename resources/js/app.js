import Alpine from 'alpinejs';
import QRCodeStyling from 'qr-code-styling';

window.QRCodeStyling = QRCodeStyling;

// Guard against multiple Alpine initializations (some builds may include Alpine twice)
if (!window.__alpine_initialized) {
	window.Alpine = Alpine;
	Alpine.start();
	window.__alpine_initialized = true;
}
