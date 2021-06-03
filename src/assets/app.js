const modalEmployees = document.getElementById('modal-employees')
// modalEmployee.classList.add('display-none')
modalEmployees.querySelectorAll('button[aria-label="close"]')
  .forEach((element) => element.addEventListener('click', () => {
  modalEmployees.classList.add('display-none')
}))