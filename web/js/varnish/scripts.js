const validate = () => {
    let ipAddress = document.getElementById('ip');

    if (ipAddress.value === "") {
        ipAddress.classList.add('invalid');
        addAlert('danger', 'IP address cannot be empty.');
        return false;
    }

    if (!validateIpAddress(ipAddress.value)) {
        ipAddress.classList.add('invalid');
        addAlert('danger', 'IP address is invalid.');
        return false;
    }

    return true;
};

const validateIpAddress = ipAddress => {
    return /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipAddress);
};

const toggleVarnishLink = (varnishId, websiteId, elem) => {
    if (elem.disabled) {
        return;
    }

    elem.disabled = true;
    let req = new XMLHttpRequest();
    req.open('POST', '/varnish/link');
    req.setRequestHeader('Content-Type', 'application/json');
    req.onreadystatechange = e => {
        if (req.readyState === 4) {
            const response = JSON.parse(req.responseText);
            elem.disabled = false;
            if (req.status === 200) {
                addAlert('success', response.message);
            } else {
                elem.checked = !elem.checked;
                addAlert('danger', response.message);
            }
        }
    };
    req.send(JSON.stringify({varnishId: varnishId, websiteId: websiteId, isLinked: !elem.checked}));
};