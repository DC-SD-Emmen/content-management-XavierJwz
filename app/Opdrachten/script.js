
let addButton = document.getElementById('add-game');
let form = document.getElementById('game-form');

addButton.addEventListener('click', function () {

    if (form.style.display == 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }

})