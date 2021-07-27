window.addEventListener('DOMContentLoaded', () => {
  getIp()
  window.setInterval(() => {
    getIp()
  }, 1000 * 10)
})

const getIp = () => {
  fetch('http://127.0.0.1:8080/ipv4').then(async (e) => {
    const t = await e.text()
    document.getElementById('ip').textContent = 'http://' + t
  })
}