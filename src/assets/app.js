/**
 * Ajout des évennements appropriés sur le calendrier hebdomadaire
 */
const dailyLists = document.querySelectorAll('.event-list')
dailyLists.forEach(
  (list) => {
    list.addEventListener('click', (e) => {
      console.log(e.target)
      const [y,m,d] = list.getAttribute('data-date').split('-')
      const date = new Date(y, m-1,d)
      console.log(date.getDay())

      openTimesheetModal(date)
    })

    // pour chaque évennement de la journée, ajoute listener click
    // pour éditer les informations
    list.querySelectorAll('.event-card').forEach(
      (card) => {
        card.addEventListener('click', (e) => {
          e.stopPropagation()
        })
      }
    )
  }
)

/**
 * Ajout des évennements appropriés sur le calendrier mensuel 
 */
document.querySelectorAll('.calendar__day').forEach(
  (day) => day.addEventListener('click', () => {
    window.location.replace(`/index.php?week=${day.getAttribute('data-week')}&year=${day.getAttribute('data-year')}`)
  })
)



/**
 * Ajout des évennements relatifs à la modération
 */
const admin_panel = document.querySelector('#btn-trigger-gestion + .gestion-options')
admin_panel && admin_panel.querySelectorAll('li i').forEach(
  (panel) => {
    console.log(panel)
    panel.addEventListener('click', () => {
      // on cherche l'id de la fenetre modale et on l'ouvre
      const ref = panel.getAttribute('data-modal')

      const modal = document.getElementById(ref)
      modal && modal.classList.add('visible-modal')

      // on referme la sélection des choix
      admin_panel.classList.remove('visible')
    })
  }
)

/**
 * Ajout évennements relatifs boutons d'options
 */
document.getElementById('btn-trigger-gestion').addEventListener('click', () => {
  admin_panel.classList.toggle('visible')
})

/**
 * Ajout évennements relatifs panneaux édition administratif
 */
document.querySelectorAll('.manage__container').forEach(
  (container) => {
    let form = container.querySelector('.edit-form')

    container.querySelectorAll('.choice').forEach(
      (choice) => choice.addEventListener('click', () => {
        container.querySelector('.choices').classList.add('editing-mode')

        form.querySelector('.name').innerText = choice.querySelector('span').innerText
        form.classList.add('editing-mode')
        form.style.maxHeight = ""
      })
    )

    const titles = container.querySelectorAll('.manage__title')
    titles.forEach(
      (title) => {
        title.addEventListener('click', () => {
          titles.forEach((e) => e.classList.toggle('is-active'))

          const wrapper = container.querySelector('.manage__wrapper')
          
          let isAddTitle = (title.textContent.indexOf("Ajouter") !== -1)

          form = container.querySelector('.edit-form')
          form.style.maxHeight = isAddTitle ? "1000px" : "0"



          wrapper.querySelector('.choices').style.maxHeight = isAddTitle
            ? "0px"
            : "300px"

          wrapper.querySelector('.form__div.block').style.display = isAddTitle
            ? "none"
            : "block"       

          const titleSection = wrapper.querySelector('.title-section')
          titleSection.querySelector('.underline').textContent = isAddTitle
            ? "Ajout"
            : "Édition de"
          form.classList.remove('editing-mode')
        })
      }
    )
  
    container.querySelector('.close-btn').addEventListener('click', () => {
      container.classList.remove('visible-modal')

      form && (form.querySelector('.name').innerText = "")
      form && form.classList.remove('editing-mode')
    })
  }
)

/**
 * Ajout des évennements relatifs au bouton d'ajout d'évennement
 */
document.getElementById('btn-trigger-timesheet').addEventListener('click', (e) => {
  openTimesheetModal(new Date())
})



/**
 * Ajout des évennements relatif aux différents dropdown
 */
document.querySelectorAll('.dropdown').forEach(
  (dropdown) => {
    const input = dropdown.querySelector('input')
    let query = ''

    dropdown.parentNode.classList.add('height')

    input.addEventListener('click', (e) => {
      e.stopPropagation()
      e.preventDefault()
      dropdown.classList.add('open')
    })

    dropdown.parentNode.addEventListener('click', (e) => {
      dropdown.classList.remove('open')
    })

    input.onkeydown = (e) => {
      if (/^.$/u.test(e.key)) {
        query += e.key
      } else if (e.key === "Backspace") {
        query = query.slice(0, -1)
      }

      input.parentNode.querySelectorAll('ul li span').forEach(
        (span) => {
          span.textContent.toLowerCase().includes(query.toLowerCase())
           ? span.parentNode.style.display = 'block'
           : span.parentNode.style.display = 'none';
        }
      )
    }

    dropdown.querySelectorAll('ul li span').forEach(
      (span) => {
        span.addEventListener('click', (e) => {
          dropdown.classList.remove('open')
          input.value = e.target.textContent
        })
      }
    )
  }
)

const openTimesheetModal = (date) => {
  const modal = document.getElementById('ajout-timesheet')

  modal.querySelector('[name="date"]').value = date.toISOString().slice(0, 10)
  
  modal.querySelector('[name="end"]').value = getTime(date)
  date.setHours( date.getHours() - 1 )
  modal.querySelector('[name="start"]').value = getTime(date)

  const percent = (date.getDay() + 1) % 7 / 7 * 100
  percent < 30 
    ? (modal.style.left = `${percent}%`) 
    : (modal.style.right = `${100 - percent}%`)
  modal.classList.add('visible')
}

const getTime = (d) => {
  const t = d.getHours() + Math.round(d.getMinutes()/60 * 2) / 2

  const hours = ('0' + Math.ceil(t)).slice(-2)
  const minutes = ('0' + (t - hours) * 60).slice(-2)

  return `${hours}:${minutes}`
}