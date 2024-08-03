const bidInput = document.getElementById('bid')
const lastBid = document.getElementById('lastBid')
const messageList = document.getElementById('messages')

const colors = ['#8db580', '#ffba08', '#3f88c5', '#ff6b6c', '#9d44b5']
lastBid.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)]

const socket = new WebSocket('ws://localhost:16108')

socket.onopen = _ => {
  console.log('Connected to the WebSocket server')
  addMessage('Connected to the WebSocket server')
}

socket.onmessage = event => {
  console.log({ event })
  const message = JSON.parse(event.data)
  if (message.type == 'lastBid') {
    lastBid.textContent = new Intl.NumberFormat('hu-HU', {
      style: 'currency',
      currency: 'HUF',
      maximumFractionDigits: 0,
      useGrouping: true,
    }).format(message.data.bid)
  }
  addMessage(`Bid received: ${message.data.bid}`)
}

socket.onclose = event => {
  console.log({ event })
}

socket.onerror = error => {
  console.error(error)
}

const addMessage = message => {
  const li = document.createElement('li')
  li.textContent = message
  messageList.appendChild(li)
}

const sendBid = () => {
  const bid = bidInput.value
  socket.send(bid)

  console.log(`Message sent ${bid}`)
  addMessage(`Message sent ${bid}`)

  bidInput.value = ''
  lastBid.textContent = new Intl.NumberFormat('hu-HU', {
    style: 'currency',
    currency: 'HUF',
    maximumFractionDigits: 0,
    useGrouping: true,
  }).format(bid)
}
