class Popup {
    constructor() {
        this.appUrl = 'https://trusty-scorpion-knowing.ngrok-free.app';
        this.cookieName = 'hideModalGeoBoost';
    }

    init() {
        if (this.cookieExists(this.cookieName)) {
            return;
        }

        this.sendRequest();
    }

    sendRequest() {
        console.log('sendRequest');
        const domain = new URL(window.location.href).hostname;

        const data = {
            domain: domain // Отправляем только домен, IP будет получен на сервере
        };

        fetch(`${this.appUrl}/get-products`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Ошибка HTTP: ${response.status}`);
                }

                this.setCookie(this.cookieName, 'true', 30);
                console.log('Запрос успешно выполнен.');
            })
            .catch(error => {
                console.error('Ошибка отправки запроса:', error);
            });
    }

    cookieExists(name) {
        return document.cookie.split(';').some((item) => item.trim().startsWith(`${name}=`));
    }

    setCookie(name, value, minutes) {
        const date = new Date();
        date.setTime(date.getTime() + (minutes * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value}; ${expires}; path=/`;
    }
}

new Popup().init();
