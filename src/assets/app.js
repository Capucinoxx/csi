class ListFilter extends HTMLElement {
  constructor () {
    super()
    this.root = this.attachShadow({ mode: 'open' })
  }

  connectedCallback() {
    const items = JSON.parse(this.getAttribute('items')) || []
    const subject = this.getAttribute('subject') || ''

    let childs = ''
    items.forEach(item => {
      childs += `
        <li class="list-item" data-id="${item['id']}">
          <span>${item['name']}</span>
          <button>Éditer</button>
        </li>
      `
    })

    this.root.innerHTML = `
      <style>
        :host {
          display: block;
        }
        li:hover {
          --edit-opacity: 1;
        }
        @media(hover:none) {
          --edit-opacity: 1;
        }

        .searchlist {
          width: 100%;
        }

        .scroll {
          padding: 20px;
          overflow-y: auto;
          max-height: 100%;
          -ms-overflow-style: none;
          scrollbar-width: none;
          position: relative;
          padding-bottom: 0;
          padding-top: 0;
          box-shadow: 0 1px 2px rgba(0,0,0,0.07), 
                0 2px 4px rgba(0,0,0,0.07), 
                0 4px 8px rgba(0,0,0,0.07), 
                0 8px 16px rgba(0,0,0,0.07),
                0 16px 32px rgba(0,0,0,0.07), 
                0 32px 64px rgba(0,0,0,0.07);
        }
        
        .searchlist {
          margin-bottom: 44px;
        }

        .scroll::-webkit-scrollbar {
          display: none;
        }

        .list-container {
          list-style: none;
          min-height: 200px;
          max-height: 200px;
          padding: 0 16px;
          margin: 0;
          font-size: 14px;
        }

        .form__div {
          position: relative;
          height: 48px;
          flex: 1 1 auto;
        }
        
        .form__input {
          position: absolute;
          top: 0;
          top: 0;
          left: 0;
          width: calc(100% - 2rem);
          border: 1px solid #DADCE0;
          border-radius: .5rem;
          outline: none;
          padding: 1rem;
          background: none;
          z-index: 1;
        }
        
        .form__label {
          position: absolute;
          left: 1rem;
          top: 1.4rem;
          padding: 0 .25rem;
          background-color: #fff;
          font-size: 1.4rem;
          color: #80868B;
          transition: .3s;
        }
        
        .form__input:focus + .form__label{
          top: -.5rem;
          left: .8rem;
          color: #275EFE;
          font-size: 1rem;
          font-weight: 500;
          z-index: 10;
        }
        
        .form__input:not(:placeholder-shown).form__input:not(:focus)+ .form__label{
          top: -.5rem;
          left: .8rem;
          font-size: 1.4rem;
          font-weight: 500;
          z-index: 10;
        }
        
        .form__input:focus{
          border: 1.5px solid #275EFE;
        }

        .list-item {
          padding: 12px 0;
          display: flex;
          align-items: center;
          justify-content: space-between;
        }

        .list-item:not(:last-child) {
          border-bottom: 1px solid #D1D6EE;
        }

        span {
          display: block;
        }

        button {
          -webkit-appearance: none;
          color: #646B8C;
          border: none;
          outline: none;
          cursor: pointer;
          border-radius: 8px;
          padding: 4px 12px;
          margin: 0;
          line-height: 17px;
          font-family: inherit;
          font-size: 12px;
          font-weight: 500;
          background: var(--hover-bg, #ECEFFC);
          opacity: var(--edit-opacity, 0);
        }
        button:hover {
          --hover-bg: #E1E6F9;
        }
      </style>

      <div class="searchlist">
        <div class="form__div">
          <input type="text" class="form__input searchbox" placeholder=" ">
          <label for="" name="" class="form__label">
            Recherche dans la liste des ${subject}
          </label>
        </div>
        <div class="scroll">
          <ul class="list-container">
            ${childs}
          </ul>
        </div>
      </div>
    `

    // attache la logique permettant à l'utilisateur de filtrer la liste
    // à l'aide de la barre de recherche
    const list_childrens = Array.from(this.root.querySelectorAll('.list-item'))
    this.root.querySelector('.searchbox').addEventListener('keyup',
      (e) => {
        const value = e.target.value.toLowerCase()

        // pour chaque enfant de la liste, on regarde si le contenu
        // correspond à la valeur de la barre de recherche
        list_childrens.forEach(children => {
          let label = children.querySelector('span').innerText.toLowerCase()

          // on met en display none si l'enfant n'est pas concerné par la recherche
          children.style.display = label.indexOf(value) !== -1 ? 'flex' : 'none'
        })
      }
    )
  }
}

customElements.define('list-filter', ListFilter)

/* ======== FONCTIONS RELATIVES AUX FENÊTRES MODALES ======== */

/* bindModal
 * ------------------------------------------
 * Gestion des interractions avec les différentes
 * fenêtres modales.
 */
const bindModal = () => {
  // pour chaque btn ouvrant une fenêtre modale
  [...document.querySelectorAll('[data-modal]')].forEach(el => {
    // récupère l'id de la modale
    const modalId = el.getAttribute('data-modal')

    // récupère l'action de la modale
    const modalAction = el.getAttribute('data-action')

    if (modalId === undefined || modalId === '') {
      return
    }

    const modal = document.getElementById(modalId)

    el.addEventListener('click',
      () => {
        modal.classList.remove('close-modal')

        // si une action spécifique est enregistré, la disposé sinon effacé le titre existant
        modalAction 
          ? bindActionModal(modal, modalAction) 
          : modal.querySelector('.modal-title').innerText = ''

        // rend visible la fenêtre
        modal.classList.add('visible')
      }
    )

    modal.querySelector('.cmb').addEventListener('click', 
      () => {
        modal.classList.add('close-modal')
        modal.classList.remove('visible')
        
      }
    )
  })
}

const bindActionModal = (modal, action) => {
  // changement du titre de la modale
  modal.querySelector('.modal-title').innerText = action

  switch(action) {
    case 'Ajout':
      break;
    case 'Édition':
      // si l'on édite les sections, l'on doit pouvoir lister les éléments existant
      [['#slide-2', 'employées'], ['#slide-3', 'projets'], ['#slide-4', 'libellées']].forEach(section => {
        const el = modal.querySelector(`${section[0]} form`)

        const searchList = document.createElement('list-filter')
        searchList.setAttribute('items', JSON.stringify([{ id: 1, name: 'toto' }, { id: 2, name: 'tata' }, { id: 1, name: 'toto' }, { id: 2, name: 'tata' }, { id: 1, name: 'toto' }, { id: 2, name: 'tata' }]))
        searchList.setAttribute('subject', section[1])

        el.insertBefore(searchList, el.firstChild)
      })
    
      // retire la section évennement du formulaire
      modal.querySelector('#slide-1').style.display = 'none'
      modal.querySelector('#tabbar-slide-1').style.display = 'none'
      modal.querySelector('label[for="tabbar-slide-1"]').style.display = 'none'

      break;
  }

}

bindModal()

const bindEditEvents = () => {
  const { top: top_wrapper, bottom: bottom_wrapper } = document.getElementById('cw').getBoundingClientRect()

  const form = document.getElementById('edit-modal')
  const { width: width_form } = form.getBoundingClientRect()

  // gestion evennement lorsque l'on clique sur bouton fermeture de la modale
  form.querySelector('.cmb').addEventListener('click', 
    () => {
      form.classList.add('close-modal')
      form.classList.remove('visible')
      
    }
  )

  document.querySelectorAll('.event-card').forEach(
    (card) => {
      console.log(card)
      card.addEventListener('click', () => {
        
        const { bottom, top, right, left, width } = card.getBoundingClientRect()

        form.classList.remove('close-modal')
        form.classList.add('visible')

        // gestion de la position sur l'axe des x
        right > window.innerWidth / 2
          ? form.style.right = `${window.innerWidth - left}px`
          : form.style.right = `${window.innerWidth - right - width_form}px`

        // gestion sur l'axe des y
        let y_axis = ((top - top_wrapper) + (bottom_wrapper - bottom))
        y_axis < window.innerHeight * .3 && (y_axis = window.innerHeight * .3)
        form.style.top = `${y_axis}px`
      })
    }
  )
}

bindEditEvents()
