const validate = () => {
    let file = document.getElementById('sitemap-file');

    if (file.value === "") {
        file.classList.add('invalid');
        addAlert('danger', 'File cannot be empty.');
        return false;
    }

    if (!/(\.xml)$/i.test(file.value)) {
        file.classList.add('invalid');
        addAlert('danger', 'Not valid xml file.');
        return false;
    }

    return true;
};