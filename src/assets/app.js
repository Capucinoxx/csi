/**
 * Ajout des évennements appropriés sur le calendrier hebdomadaire
 */
 const dailyLists = document.querySelectorAll('.event-list')
 dailyLists.forEach(
   (list) => {
     const [y,m,d] = list.getAttribute('data-date').split('-')
     const date = new Date()
     date.setFullYear(y)
     date.setMonth(m-1)
     date.setDate(d)
 
     
     list.addEventListener('click', (e) => {
       const { top, height } = e.target.getBoundingClientRect()
       const  yaxis = e.clientY - top
       const time = (Math.round(yaxis / (height - 54) * 36) / 2)
       const hours = (Math.ceil(time) + 6)
       const t = date
      //  console.log(hours)
       t.setHours(hours)

       
       openTimesheetModal(t)
     })
 
     // pour chaque évennement de la journée, ajoute listener click
     // pour éditer les informations
     list.querySelectorAll('.event-card').forEach(
       (card) => {
         card.addEventListener('click', (e) => {
           e.stopPropagation()
          //  const date = new Date(y, m-1,d)
           
           const formData = new FormData()
           formData.append('context', 'getTimesheetById')
           formData.append('id', e.target.getAttribute('data-id'))
 
           fetch(
             window.location,
             { method: 'POST', body: formData },
             true
           ).then(async (resp) => await resp.json())
           .then((data) => {
             data = { ...data, id: e.target.getAttribute('data-id') }
            openTimesheetModal(date, data)
           })
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
 const gestionBtn = document.getElementById('btn-trigger-gestion')
 gestionBtn && gestionBtn.addEventListener('click', () => {
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

     // gère la gestion de la sélection de la palette de couleur si existante
     const palletColors = container.querySelector('.colors-choice')
     palletColors && palletColors.querySelectorAll('.color__choices').forEach(
       (color) => {
          color.addEventListener('click', () => {
            console.log(window.getComputedStyle(color))
            container.querySelector('input[type="color"]').value = color.getAttribute('data-color')
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
 
           const input_event = dropdown.parentNode.querySelector('[name="id_event"]')
           console.log(input_event, span)
           input_event && (input_event.value = span.getAttribute('data-id'))
         })
       }
     )
   }
 )
 
 /**
  * Gestion des évennements relier à la fenêtre d'ajout de timesheet
  */
 const addmodal = document.getElementById('ajout-timesheet')
 // gestion btn fermeture
 addmodal.querySelector('.close-btn').addEventListener('click', () => addmodal.classList.remove('visible'))
 
 // gestion btn ajout
 addmodal.querySelector('.save-btn').addEventListener('click', () => {
   const formData = new FormData()
   formData.append('context', 'addTimesheetEvent')
   addmodal.querySelectorAll('input, textarea').forEach(
     (field) => {
       formData.append(field.getAttribute('name'), field.value)
     }
   )
 
   for (var pair of formData.entries()) {
     console.log(pair[0]+ ', ' + pair[1]); 
   }
   fetch(
     window.location,
     { method: 'POST', body: formData },
     true
   ).then(() => window.location = window.location)
 })
 
 /**
  * Gestion de l'ouverture de la fenêtre gérant l'ajout de timesheet
  * @param {Date} date 
  */
  const openTimesheetModal = (date, defaultValue = undefined) => {
   const saveBtn = addmodal.querySelector('.save-btn')
   const hoursInvestedEl = addmodal.querySelector('input[type="number"]')
   const end = addmodal.querySelector('[name="end"]')
   const start = addmodal.querySelector('[name="start"]')
 
   // lorsque qu'il n'y a pas de valeur pas défault, l'utlisateur
   // souhaite ajouter un nouvel évennement, sinon on modifie
   // un évennement déjà existant
   if (defaultValue !== undefined) {
     // on apporte les modification aux valuers par défault pour que
     // celles-ci puissent être ajouter aux formulaires
     defaultValue.project = addmodal.querySelector(`span[data-id="${defaultValue.id_event}"]`).textContent
     defaultValue.date = new Date(+defaultValue.at).toISOString().slice(0, 10)
     defaultValue.start = floatToTime(+defaultValue.start)
     defaultValue.end = floatToTime(+defaultValue.end)
 
     // on ajoute ces valeurs au formulaire
     addmodal.querySelectorAll('input, textarea').forEach(
       (field) => {
         field.value = defaultValue[field.getAttribute('name')]
       }
     )
 
     saveBtn.lastChild.textContent = 'Enregistrer'
   } else {
     // on met les heures par défault
     addmodal.querySelector('[name="date"]').value = date.toISOString().slice(0, 10)
   
     end.value = getTime(date)
     console.log(date)
     date.setHours( date.getHours() - 1 )
     console.log(date)
     start.value = getTime(date)
     
     setTimeElapsed(start, end, hoursInvestedEl)
     
     saveBtn.lastChild.textContent = 'Ajouter'
   }
 
   [start, end].forEach(
     (input) => input.addEventListener('input', () => {
       setTimeElapsed(start, end, hoursInvestedEl)
     })
   )
 
   hoursInvestedEl.addEventListener('input', (e) => {
     start.value = floatToTime(stringToFloat(end.value, ':', 60) - e.target.value)
   })
 
 
   // on ajoute un évennement au click pour sauvegerder les modifications ou ajouter
   // un nouvel évennement
  //  saveBtn.addEventListener('click', async (e) =>  {
  //    e.stopPropagation()
  //    e.preventDefault()

  //    // on crée un faux formulaire

  //    const formData = new FormData()
  //    formData.append('context', defaultValue !== undefined ? 'editTimesheetEvent' : 'addTimesheetEvent')
  //    defaultValue !== undefined && formData.append('id', defaultValue.id)
  //    addmodal.querySelectorAll('input, textarea').forEach(
  //      (field) => {
  //        formData.append(field.getAttribute('name'), field.value)
  //      }
  //    )

  //    formData.submit( window.location, (err, res) => {
  //      if (err) {} else {
  //        console.log(res)
  //        window.location = window.location
  //      }
  //    })
 
  //  })
   
   const pos = (date.getDay()) % 7
   const percent = (pos > 3 ? 7 - pos  : pos + 1) / 7 * 100
   addmodal.style.left = pos <= 3 ? `${percent}%` : ''
   addmodal.style.right = pos <= 3 ? '' : `${percent}%`
   addmodal.classList.add('visible')
 }
 
 const setTimeElapsed = (startEl, endEl, el) => {
   const start = stringToFloat(startEl.value, ':', 60)
   const end = stringToFloat(endEl.value, ':', 60)
 
   el.value = +(Math.round(end - start + "e+2")  + "e-2")
 }
 
 const stringToFloat = (value, separator, step = 60) => {
   let result = 0
   value.split(separator).forEach((v, i) => result += v / ((step ** (i + 1)) / step))
 
   return result
 }
 
 const getTime = (d) => {
   const t = d.getHours() + Math.round(d.getMinutes()/60 * 2) / 2
 
   return floatToTime(t)
 }
 
 const floatToTime = (f) => {
   const hours = ('0' + Math.ceil(f)).slice(-2)
   const minutes = ('0' + (f - hours) * 60).slice(-2)
 
   return `${hours}:${minutes}`
 }
 
 const logout = () => {
   const formData = new FormData()
   formData.append('context', 'disconnect')
 
   fetch(
     window.location, 
     { method: 'POST', body: formData },
     true
   ).then(() => window.location = window.location)
 }
 

// const onSumbintTimesheet = (e, form) => {
//   e.preventDefault()

//   fetch('', {
//     method: 'post',

//   })
// }



// e.preventDefault();
    
// fetch('file.php', {
//   method: 'post',
//   body: JSON.stringify({name: form.formName.value, email: form.formEmail.value})
// }).then(function(response) {
//   return response.json();
// }).then(function(data) {
//   //Success code goes here
//   alert('form submited')
// }).catch(function(err) {
//   //Failure
//   alert('Error')
// });
// }



const submitTimesheet = (e) => {
  e.preventDefault()

  const formData = new FormData()
  formData.append('context', defaultValue !== undefined ? 'editTimesheetEvent' : 'addTimesheetEvent')
  defaultValue !== undefined && formData.append('id', defaultValue.id)
  addmodal.querySelectorAll('input, textarea').forEach(
    (field) => {
      formData.append(field.getAttribute('name'), field.value)
    }
  )

  fetch(
    window.location,
    {
      method: 'post', body: formData
    }
  ).then(() => window.location = window.location)
}


handleSubmit = (event) => {
  event.preventDefault()
  fetch("https://jsonplaceholder.typicode.com/todos/1")
    .then((response) => response.json())
    .then((json) => {
      console.log(json)
      window.location.reload()
    })
}

function sendTimesheetEvent(e,form) {
  const formData = new FormData()
  const defaultValue = undefined
  formData.append('context', defaultValue !== undefined ? 'editTimesheetEvent' : 'addTimesheetEvent')
  defaultValue !== undefined && formData.append('id', defaultValue.id)
  form.querySelectorAll('input, textarea').forEach(
    (field) => {
      formData.append(field.getAttribute('name'), field.value)
    }
  )  

  fetch('localhost:8002/', {method:'post', body: formData}).catch((err) => console.error(err));

  e.preventDefault();
}