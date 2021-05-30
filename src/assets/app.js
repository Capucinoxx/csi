const modalEmployee = document.getElementById('modal-employee')
modalEmployee.classList.add('display-none')
modalEmployee.querySelector('.close').addEventListener('click', () => {
  modalEmployee.classList.add('display-none')
})