$(document).ready(function () {
    document.cookie = "name=oeschger; SameSite=None; Secure; expires=Thu, 18 Dec 2025 12:00:00 UTC";
    document.cookie = "favorite_food=tripe; SameSite=None; Secure; expires=Thu, 18 Dec 2025 12:00:00 UTC";
});

function showCookies() {
    const output = document.getElementById('cookies');
    output.textContent = `> ${document.cookie}`;
}

function clearOutputCookies() {
    const output = document.getElementById('cookies');
    output.textContent = '';
    document.cookie = "name=; expires=Thu, 18 Dec 1970 12:00:00 UTC";
    document.cookie = "favorite_food=; expires=Thu, 18 Dec 1970 12:00:00 UTC";
}