<footer class="fixed bottom-0 left-0 z-20 w-full text-center bg-white border-t border-gray-200 shadow md:p-6 dark:bg-gray-800 dark:border-gray-600">
    <span class="text-xs text-gray-500 text-center dark:text-gray-400">© 2024
        <a href="https://www.linkedin.com/in/lucas-henrique-souza-4395441b7/" class="hover:underline">Luc Tech Solutions™</a> All Rights Reserved.
    </span>
</footer>

<script>
    (function() {

        if(typeof handleNotificationSoundInterval != 'undefined') {
            clearInterval(handleNotificationSoundInterval);
        }

        handleNotificationSoundInterval = setInterval(handleNotificationSound, 3000);

        async function handleNotificationSound() {

        try {
            let unreadNotificationsCountComponent = document.querySelector('span.truncate')

            if(!unreadNotificationsCountComponent) {
                return;
            }

            if(unreadNotificationsCountComponent.innerText == 0){
                return;
            }

            let audio = new Audio('{{url('/sound/notification.mp3')}}');
                audio.play();

        } catch (error) {
            console.error(error);
        }

        }
    })();
</script>
