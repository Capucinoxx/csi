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
          right
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
 */
const drawFormManageEvent = (top, left, right) => {
  let form = document.getElementById('edit-event')
  console.log('plop');

  if (form) {
    const { width: widthForm } = form.getBoundingClientRect();
    form.classList.add('visible');

    // gestion de la position sur l'axe des x
    (right > window.innerWidth / 2)
     ? form.style.right = `${window.innerWidth - left}px`
     : form.style.right = `${window.innerWidth - right - widthForm}px`

     console.log(top, form.offsetHeight)
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

// ===== MAIN =====
handleMonthlyEvents()
handleWeeklyEvents()