console.log('hello');

const td = document.querySelectorAll('td');

td.forEach(e => {
    e.addEventListener('click', () => {
        console.log(e);
        e.classList.toggle('highlight')
    })
})