const salesChannelIdSelectEl = document.getElementById('salesChannelId');
const apiLoginEl = document.getElementById('apiLogin');
const apiPasswordEl = document.getElementById('apiPassword');
const apiEnvironmentEl = document.getElementById('apiEnvironment');
const officeOriginEl = document.getElementById('officeOrigin');

salesChannelIdSelectEl.addEventListener('change', (e) => {
    const value = e.target.value;

    const searchParams = new URLSearchParams(window.location.search);

    const urlParams = {
        shopId: searchParams.get('shop-id'),
        salesChannelId: value,
        language: searchParams.get('sw-user-language'),
    };

    const fetchOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    };

    const urlSearchParams = new URLSearchParams(urlParams).toString();

    fetch('/app/config?' + urlSearchParams, fetchOptions)
        .then(result => {
            result.json().then(response => {
                apiLoginEl.value = response.apiLogin ?? '';
                apiPasswordEl.value = response.apiPassword ?? '';
                apiEnvironmentEl.value = response.apiEnvironment ?? '';
                officeOriginEl.value = response.officeOrigin ?? '';
            });
        });
});
