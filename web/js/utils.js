const addAlert = (type, msg) => {
    let alert = document.createElement('div');
    alert.classList.add('alert', 'alert-' + type, 'alert-dismissible');
    alert.innerHTML = `<a href="#" class="close" onclick="this.parentElement.remove(); return false;">&times;</a><span>${msg}</span>`;
    document.getElementById('alerts').append(alert);

    setTimeout(function() {
        alert.remove();
    }, 5000);
};