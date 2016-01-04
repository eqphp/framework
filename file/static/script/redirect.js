(function () {
    var wait = document.getElementById('wait');
    var href = document.getElementById('href').href;
    var interval = setInterval(function () {
        var time = --wait.innerHTML;
        if (time <= 1) {
            location.href = href;
            clearInterval(interval);
        }
    }, 1000);
})();