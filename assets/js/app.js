var previousInfoLink  = document.querySelector('.technologies.info-link-technologies');;
var showTime = document.querySelector(".showTime");

setInterval((function interval() {
    let curTime = new Date();
    let hour = curTime.getHours();
    let min = curTime.getMinutes();
    let sec = curTime.getSeconds();
    if (hour < 10) { hour = '0' + hour; }
    if (min < 10) { min = '0' + min; }
    if (sec < 10) { sec = '0' + sec; }

    showTime.innerHTML = hour + ":" + min + ":" + sec;

    return interval;
})(), 1000);

POLICY_BTN.onclick = function () {
    document.querySelector('.reveal').style.display = "none";
    window.scrollTo(0, 0);
}

INFO_LINKS.forEach(function (info_link) {
    info_link.onclick = function () {
        removeCurrentInfoLink();

        if (info_link.classList.contains('info-link-technologies')) {
            previousInfoLink = LINK_TECHNOLOGIES;
            previousInfoLink.classList.add('info-link-show');
        } else if (info_link.classList.contains('info-link-policy')) {
            previousInfoLink = LINK_POLICY;
            previousInfoLink.classList.add('info-link-show');
        } else if (info_link.classList.contains('info-link-id_sample')) {
            previousInfoLink = LINK_ID_SAMPLE;
            previousInfoLink.classList.add('info-link-show');
        } else {
            console.log(info_link.className);
            alert("Something is wrong with this link, please report it to a library staff member. Thank you!");
        }
    }
});

function removeCurrentInfoLink() {
    if (previousInfoLink != null) {
        previousInfoLink.classList.toggle('info-link-show');
    }
}

//Remove policy on page load if using uri to navigate
if(document.location.href.indexOf('index') != -1) {
    document.querySelector('.reveal').style.display = "none";
}