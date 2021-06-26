// weekly calendar

/**
 * Ajout des évennements appropiés sur le calendrier hebdomadaire
 */
const handleWeeklyEvents = () => {
  // pour chaque jour de la semaine
  document.querySelectorAll('.event-list').forEach(
    (list) => {
      // ajout d'un évennement au click permettant d'ajouter un évennement 
      // où la personne à cliqué
      list.addEventListener('click', (e) => {
        const { top, height } = e.target.getBoundingClientRect()
        const { left, right } = list.getBoundingClientRect()

        const y = e.clientY - top

        drawFormManageEvent(
          // calcul la hauteur par rapport au parent du début de l'évennement
          Math.round(y / (height - 54) * 36) / 2 / 18 * 100,
          left,
          right,
          list.getAttribute('data-date')
        )
      })

      // pour chaque évennement de la journée, lorsque l'on clique dessus,
      // on doit pouvoir éditer l'évennement en cause
      list.querySelectorAll('.event-card').forEach(
        (card) => {
          card.addEventListener('click', (e) => {
            e.stopPropagation()
          })
        }
      )
    }
  )
}

/**
 * Place dans le DOM à la bonne place le formulaire de gestion des évennements
 * @param {number} top Hauteur par rapport au parent du début de l'évennement
 * @param {number} left Distance entre le début et la droite du parent
 * @param {number} right Distance entre le début et la gauche du parent
 * @param {string} date 
 */
const drawFormManageEvent = (top, left, right, date) => {
  const [y,m,d] = date.split('-')
  console.log(y,m,d)
  const now = new Date(y,m - 1,d)

  let form = document.getElementById('ajout-timesheet')

  if (form) {
    const { width: widthForm } = form.getBoundingClientRect();
    form.classList.add('visible');

    // insertion des information de date et heures
    form.querySelector('[name="date"]').value = `${date}`;
    form.querySelector('[name="start"]').value = `${( '0' + (Math.ceil(top * 18 / 100) + 6 - 1)).slice(-2)}:00`
    form.querySelector('[name="end"]').value = `${( '0' + (Math.ceil(top * 18 / 100) + 6)).slice(-2)}:00`

    console.log('calc', Math.ceil(top * 18 / 100));

    // gestion de la position sur l'axe des x
    (right > window.innerWidth / 2)
     ? form.style.right = `${window.innerWidth - left}px`
     : form.style.right = `${window.innerWidth - right - widthForm}px`

    //  gestion de la position sur l'axe des y
    form.style.top = `${top}%`

    console.log(form);
  }
}

// monthly calendar

/**
 * Ajout des évennements appropriés sur le calendrier mensuel 
 */
const handleMonthlyEvents = () => {
  // pour chaque jour du mois, ajouter un évennement de clique 
  // qui retourne à la bonne semaine
  
  document.querySelectorAll('.calendar__day').forEach(
    (day) => {
      day.addEventListener('click', () => {

        window.location.replace(`/index.php?week=${day.getAttribute('data-week')}&year=${day.getAttribute('data-year')}`)
      })
    }
  )
}

/**
 * gestion du curseur montrant on se trouve à qu'elle heure actuellement
 */
const drawTimeCursor = () => {
  
  const cursor = document.querySelector('.cursor')
  const { height } = cursor.parentElement.getBoundingClientRect()


  let interval = 0

  let compute = () => {
    let time = new Date();
    let { hours, minutes, day, seconds } = {
      hours: time.getHours(),
      minutes: time.getMinutes(),
      day: time.getDay(),
      seconds: time.getSeconds()
    }

    if (hours >= 6) {
      cursor.classList.remove('invisible')

      console.log((hours - 6 + minutes / 60) / 18)
      let top = ((hours - 6 + minutes / 60) / 18)

      cursor.style.top = `${(height * top) + 54 - 7}px`
      cursor.style.left = `${(day % 7)/7 * 100}%`
      
      interval = 5 * 1000
      setTimeout(compute, interval)
    } else {
      cursor.classList.add('invisible')
    }
  }

  setTimeout(compute, interval)


  


}

/**
 * gestion des évennements relatifs 
 */
const handleEventEditingModal = () => {
  const btn = document.getElementById('btn-trigger-gestion')

  if (btn) {
    btn.addEventListener('click', () => {
      document.querySelector('.gestion-options').classList.toggle('visible')
    })
  }

  document.querySelectorAll('i[data-modal]').forEach(
    (open) => {
      open.addEventListener('click', () => {
        document.getElementById(open.getAttribute('data-modal')).classList.add('visible-modal')
      })
    }
  )

  document.querySelector('button[data-modal]').addEventListener('click',
    (e) => {
      const modal = document.getElementById(e.target.getAttribute('data-modal'))

      // mise en place de la journée et de l'heure courrante
      const now = new Date()

      

      modal.querySelector('[name="date"]').value = `${now.getFullYear()}-${('0' + (now.getMonth() + 1)).slice(-2)}-${now.getDate()}`

      console.log(getTime(now))
      modal.querySelector('[name="end"]').value = `${getTime(now)}`
      const now_copy = now;
      now_copy.setHours(now_copy.getHours() - 1)
      modal.querySelector('[name="start"]').value = `${getTime(now_copy)}`

      modal.classList.add('visible-modal')
    }
  )


  document.querySelectorAll('.manage__container').forEach(
    (container) => {
      const edit_form = container.querySelector('.edit-form')

      container.querySelectorAll('.choice').forEach(
        (choice) => choice.addEventListener('click', () => {
          container.querySelector('.choices').classList.add('editing-mode')

          
          edit_form.querySelector('.name').innerText = choice.querySelector('span').innerText
          edit_form.classList.add('editing-mode')
        })
      )

      container.querySelector('.close-btn').addEventListener('click', () => {
        console.log('click ')
        container.classList.remove('visible-modal')
        edit_form.querySelector('.name').innerText = ""
        edit_form.classList.remove('editing-mode')
      })
    }
  )
}

const dropdown = () => {
  document.querySelectorAll(".dropdown").forEach(
    (select) => {
      const input = select.querySelector('input')

      let query = ''


      select.parentNode.classList.add('height')

      input.addEventListener('click', (e) => {
        e.stopPropagation()
        e.preventDefault()
        select.classList.add('open')
      })

      select.parentNode.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()
        select.classList.remove('open')
      })

      input.onkeydown = (e) => {
        // e.preventDefault()
        if (/^.$/u.test(e.key)) {
          query += e.key
        } else if (e.key === "Backspace") {
          query = query.slice(0, -1)

          input.parentNode.querySelectorAll('ul li span').forEach(
            (span) => {
              span.textContent.toLowerCase().includes(query.toLowerCase())
               ? span.parentNode.style.display = 'block'
               : span.parentNode.style.display = 'none'
            }
          )
        }
      }

      select.querySelectorAll("ul li span").forEach(
        (el) => {
          console.log(el)
          el.addEventListener('click', (e) => {

            
            select.classList.remove('open')
            input.value = e.target.textContent
          })
        }
      )

    }
  )
}

const ajoutTimesheet = () => {
  const timesheet = document.getElementById('ajout-timesheet')
  timesheet.querySelector('.save-btn').addEventListener('click',
    () => {
      const formData = new FormData();

      timesheet.querySelectorAll('input, textarea').forEach(
        (input) => {
          formData.append(input.getAttribute('name'), input.value)
        }
      )

      fetch(
        window.location, 
        { method: 'POST', body: formData },
        true
      ).then(() => document.location.reload())
    }
  )
}
 
const getTime = (d) => {
  const t = d.getHours() + Math.round(d.getMinutes()/60 * 2) / 2

  const hours = ('0' + Math.ceil(t)).slice(-2)
  const minutes = ('0' + (t - hours) * 60).slice(-2)

  return `${hours}:${minutes}`
}

const logout = () => {
  const formData = new FormData();
  formData.append('context', 'disconnect');

  fetch(
    window.location, 
    { method: 'POST', body: formData },
    true
  ).then(() => document.location.reload())
}

// ===== MAIN =====
handleMonthlyEvents()
handleWeeklyEvents()
drawTimeCursor()
handleEventEditingModal()
dropdown()
ajoutTimesheet()
