import './bootstrap';

import './notification-sound';


function playNotificationSound() {
    const audio = new Audio('/path/to/notification-sound.mp3');
    audio.play();
}
