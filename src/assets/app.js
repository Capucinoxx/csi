const modalEmployees = document.getElementById('modal-employees')
// modalEmployee.classList.add('display-none')
modalEmployees && modalEmployees.querySelectorAll('button[aria-label="close"]')
  .forEach((element) => element.addEventListener('click', () => {
  modalEmployees.classList.add('display-none')
}))

/* searchList
 * ------------------------------------------
 * Permet à l'utilisateur de faire une recherche
 * rapide en rentrant le nom de ce qu'il cherche
 * dans la liste ou une partie du nom / mot
 * 
 * la liste se filtre à chaque fois que l'utilisateur
 * actualise la valeur de la boîte de recherche
*/
const searchList = (listContainer) => {
  const lists_container = document.querySelectorAll(listContainer)
  lists_container.forEach(list_container => {
    const search_bar = list_container.querySelector('.searchbox')
    const childrens = Array.from(list_container.querySelector('.list-container').children)
    
    
    // ajout de l'évennement lorsque l'utilisateur change la valeur dans la boîte
    search_bar.addEventListener('keyup', (e) => {
      const value = e.target.value.toLowerCase()

      // pour chaque enfant de la liste, regarde si le contenu correspond à
      // à la valeur de la barre de recherche
      childrens.forEach(children => {
        let label = children.innerText.toLowerCase()
        
        // on met en display none si l'enfant n'est pas concerné par la recherche
        children.style.display = label.indexOf(value) !== -1 ? 'block' : 'none'
      })
    })
  })
}

/* bindOpenModal
 * ------------------------------------------
 * Permet à l'utilisateur d'ouvrir les différentes
 * fenêtres modales en cliquant sur l'élément associé.
 * Cet élément doit avoit la propriété [data-modal] 
 * comportant l'id de la fenêtre modal souhaitant être
 * ouverte
 */
const bindOpenModal = () => {
  [...document.querySelectorAll('[data-modal]')].forEach(el => {
    const modalId = el.getAttribute('data-modal')

    modalId && document.getElementById(modalId).addEventListener('click',
      (modal) => {
        modal.classList.add('visible')
      }
    )
  })
}

/* bindModalActions
 * ------------------------------------------
 *  Permet lorsque l'on clique sur les boutons
 *  d'effectuer l'action voulue soit pour 
 *  revenir à la liste, pour éditer un
 *  élément ou pour fermer la fenêtre modale.
*/
const bindModalActions = () => {
  document.querySelectorAll('.modal').forEach(modal => {
    // element parent des deux section de la modale
    const modalWrapper = modal.querySelector('.modal-dialog')

    // pour chaque fenêtre modale, ajoutner un évennement pour chaque
    // item envoyant à la liste d'édition
    modal.querySelectorAll('.list-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault()
        modalWrapper.classList.add('selected')
      })
    })

    // ajoutes un évennement lorsque quelqu'un clique sur le bouton
    // pour revenir à la liste
    modal.querySelector('.gotoList').addEventListener('click', (e) => {
      e.preventDefault()
      modalWrapper.classList.remove('selected')
    }) 
  
    // ajoutes un évennement lorsque quelqu'un clique sur le bouton
    // pour quitter la fenêtre modale
    modal.querySelectorAll('[aria-label="close"]').forEach(close => {
      close.addEventListener('click', e => {
        e.preventDefault()
        modal.classList.remove('visible')
      })
    })
  })
}


/*------------------------------ MAIN ----------------------------------*/

// pour chaque liste de recherche, on greffe la logique permettant de faire un tri de liste à
// l'aide d'une boîte de recherche
searchList('.searchlist')

// bindModal()

bindModalActions()